<?php
if (!defined("ROOT_PATH"))
{
	header("HTTP/1.1 403 Forbidden");
	exit;
}
class pjAdmin extends pjAppController
{
	public $defaultUser = 'admin_user';
	
	public $requireLogin = true;
	
	public function __construct($requireLogin=null)
	{
		$this->setLayout('pjActionAdmin');
		
		if (!is_null($requireLogin) && is_bool($requireLogin))
		{
			$this->requireLogin = $requireLogin;
		}
		
		if ($this->requireLogin)
		{
			if (!$this->isLoged() && !in_array(@$_GET['action'], array('pjActionLogin', 'pjActionForgot', 'pjActionPreview')))
			{
				pjUtil::redirect($_SERVER['PHP_SELF'] . "?controller=pjAdmin&action=pjActionLogin");
			}
		}
	}
	
	public function afterFilter()
	{
		parent::afterFilter();
		if ($this->isLoged() && !in_array(@$_GET['action'], array('pjActionLogin')))
		{
			$this->appendJs('index.php?controller=pjAdmin&action=pjActionMessages', PJ_INSTALL_URL, true);
		}
	}
	
	public function beforeRender()
	{
		
	}
		
	public function pjActionIndex()
	{
		$this->checkLogin();
		
		if ($this->isAdmin() || $this->isEditor())
		{
			$pjBookingModel = pjBookingModel::factory();
			
			$total_bookings = $pjBookingModel->findCount()->getData();
			$cnt_bookings_made = $pjBookingModel->reset()->where("DATE(t1.created)=CURDATE()")->findCount()->getData();
			$cnt_bookings_today = $pjBookingModel->reset()->where("( (t1.book_by='multiday' AND CURDATE() BETWEEN t1.start_date AND t1.end_date) OR ( (t1.book_by='hour' OR t1.book_by='afternoon' OR t1.book_by='morning') AND t1.start_date=CURDATE() ) )")->findCount()->getData();
			
			$latest_bookings = $pjBookingModel
				->reset()
				->select('t1.*, t2.content as room')
				->join('pjMultiLang', "t2.model='pjRoom' AND t2.foreign_id=t1.room_id AND t2.field='title' AND t2.locale='".$this->getLocaleId()."'", 'left outer')
				->orderBy("t1.created DESC")
				->limit(3)
				->findAll()
				->getData();
				
			$date = date('Y-m-d');

			$booking_arr = $this->getBookings($date, $this->getLocaleId());
			
			$this->set('total_bookings', $total_bookings);
			$this->set('cnt_bookings_made', $cnt_bookings_made);
			$this->set('cnt_bookings_today', $cnt_bookings_today);
			$this->set('latest_bookings', $latest_bookings);
			$this->set('booking_arr', $booking_arr);
			
			$this->set('date', $date);
			
			$this->appendJs('pjAdmin.js');
		} else {
			$this->set('status', 2);
		}
	}
	
	public function pjActionForgot()
	{
		$this->setLayout('pjActionAdminLogin');
		
		if (isset($_POST['forgot_user']))
		{
			if (!isset($_POST['forgot_email']) || !pjValidation::pjActionNotEmpty($_POST['forgot_email']) || !pjValidation::pjActionEmail($_POST['forgot_email']))
			{
				pjUtil::redirect($_SERVER['PHP_SELF'] . "?controller=pjAdmin&action=pjActionForgot&err=AA10");
			}
			$pjUserModel = pjUserModel::factory();
			$user = $pjUserModel
				->where('t1.email', $_POST['forgot_email'])
				->limit(1)
				->findAll()
				->getData();
				
			if (count($user) != 1)
			{
				pjUtil::redirect($_SERVER['PHP_SELF'] . "?controller=pjAdmin&action=pjActionForgot&err=AA10");
			} else {
				$user = $user[0];
				
				$Email = new pjEmail();
				$Email
					->setTo($user['email'])
					->setFrom($user['email'])
					->setSubject(__('emailForgotSubject', true));
				
				if ($this->option_arr['o_send_email'] == 'smtp')
				{
					$Email
						->setTransport('smtp')
						->setSmtpHost($this->option_arr['o_smtp_host'])
						->setSmtpPort($this->option_arr['o_smtp_port'])
						->setSmtpUser($this->option_arr['o_smtp_user'])
						->setSmtpPass($this->option_arr['o_smtp_pass'])
						->setSender($this->option_arr['o_smtp_user'])
					;
				}
				
				$body = str_replace(
					array('{Name}', '{Password}'),
					array($user['name'], $user['password']),
					__('emailForgotBody', true)
				);

				if ($Email->send($body))
				{
					$err = "AA11";
				} else {
					$err = "AA12";
				}
				pjUtil::redirect($_SERVER['PHP_SELF'] . "?controller=pjAdmin&action=pjActionForgot&err=$err");
			}
		} else {
			$this->appendJs('jquery.validate.min.js', PJ_THIRD_PARTY_PATH . 'validate/');
			$this->appendJs('pjAdmin.js');
		}
	}
	
	public function pjActionMessages()
	{
		$this->setAjax(true);
		header("Content-Type: text/javascript; charset=utf-8");
	}
	
	public function pjActionLogin()
	{
		$this->setLayout('pjActionAdminLogin');
		
		if (isset($_POST['login_user']))
		{
			if (!isset($_POST['login_email']) || !isset($_POST['login_password']) ||
				!pjValidation::pjActionNotEmpty($_POST['login_email']) ||
				!pjValidation::pjActionNotEmpty($_POST['login_password']) ||
				!pjValidation::pjActionEmail($_POST['login_email']))
			{				
				pjUtil::redirect($_SERVER['PHP_SELF'] . "?controller=pjAdmin&action=pjActionLogin&err=4");
			}
			$pjUserModel = pjUserModel::factory();

			$user = $pjUserModel
				->where('t1.email', $_POST['login_email'])
				->where(sprintf("t1.password = AES_ENCRYPT('%s', '%s')", pjObject::escapeString($_POST['login_password']), PJ_SALT))
				->limit(1)
				->findAll()
				->getData();

			if (count($user) != 1)
			{
				pjUtil::redirect($_SERVER['PHP_SELF'] . "?controller=pjAdmin&action=pjActionLogin&err=1");
			} else {
				$user = $user[0];
				unset($user['password']);
															
				if (!in_array($user['role_id'], array(1,2,3)))
				{
					pjUtil::redirect($_SERVER['PHP_SELF'] . "?controller=pjAdmin&action=pjActionLogin&err=2");
				}
				
				if ($user['role_id'] == 3 && $user['is_active'] == 'F')
				{
					pjUtil::redirect($_SERVER['PHP_SELF'] . "?controller=pjAdmin&action=pjActionLogin&err=2");
				}
				
				if ($user['status'] != 'T')
				{
					pjUtil::redirect($_SERVER['PHP_SELF'] . "?controller=pjAdmin&action=pjActionLogin&err=3");
				}
				
				# Login succeed
				$last_login = date("Y-m-d H:i:s");
				if($user['last_login'] == $user['created'])
				{
					$user['last_login'] = date("Y-m-d H:i:s");
				}
    			$_SESSION[$this->defaultUser] = $user;
    			
    			$data = array();
    			$data['last_login'] = $last_login;
    			$pjUserModel->reset()->setAttributes(array('id' => $user['id']))->modify($data);

    			if ($this->isAdmin() || $this->isEditor())
    			{
	    			pjUtil::redirect($_SERVER['PHP_SELF'] . "?controller=pjAdmin&action=pjActionIndex");
    			}
			}
		} else {
			$this->appendJs('jquery.validate.min.js', PJ_THIRD_PARTY_PATH . 'validate/');
			$this->appendJs('pjAdmin.js');
		}
	}
	
	public function pjActionLogout()
	{
		if ($this->isLoged())
        {
        	unset($_SESSION[$this->defaultUser]);
        }
       	pjUtil::redirect($_SERVER['PHP_SELF'] . "?controller=pjAdmin&action=pjActionLogin");
	}
	
	public function pjActionProfile()
	{
		$this->checkLogin();
		
		if (!$this->isAdmin())
		{
			if (isset($_POST['profile_update']))
			{
				$pjUserModel = pjUserModel::factory();
				$arr = $pjUserModel->find($this->getUserId())->getData();
				$data = array();
				$data['role_id'] = $arr['role_id'];
				$data['status'] = $arr['status'];
				$post = array_merge($_POST, $data);
				if (!$pjUserModel->validates($post))
				{
					pjUtil::redirect($_SERVER['PHP_SELF'] . "?controller=pjAdmin&action=pjActionProfile&err=AA14");
				}
				$pjUserModel->set('id', $this->getUserId())->modify($post);
				
				pjUtil::redirect($_SERVER['PHP_SELF'] . "?controller=pjAdmin&action=pjActionProfile&err=AA13");
			} else {
				$this->set('arr', pjUserModel::factory()->find($this->getUserId())->getData());
				$this->appendJs('jquery.validate.min.js', PJ_THIRD_PARTY_PATH . 'validate/');
				$this->appendJs('pjAdmin.js');
			}
		} else {
			$this->set('status', 2);
		}
	}
	public function pjActionPrint()
	{
		$this->checkLogin();
	
		$this->setLayout('pjActionPrint'); 
		
		if ($this->isAdmin() || $this->isEditor())
		{
			$date = pjUtil::formatDate($_GET['date'], $this->option_arr['o_date_format']);

			$booking_arr = $this->getBookings($date, $this->getLocaleId());
			
			$booking_id_arr = array();
			$hour_booking_id_arr = array();
			$booking_slot_arr = array();
			$equipment_arr = array();
			$food_drink_arr = array();
			foreach($booking_arr as $k => $v)
			{
				if($v['book_by'] == 'hour')
				{
					$hour_booking_id_arr[] = $v['id'];
				}
				$booking_id_arr[] = $v['id'];
			}
			if(!empty($hour_booking_id_arr))
			{
				$temp_booking_slot_arr = pjBookingSlotModel::factory()->whereIn('t1.booking_id', $hour_booking_id_arr)->findAll()->getData();
				foreach($temp_booking_slot_arr as $k => $v)
				{
					$booking_slot_arr[$v['booking_id']][] = date($this->option_arr['o_time_format'], $v['start_ts']) . ' - ' . date($this->option_arr['o_time_format'], $v['end_ts']);
				}
			}
			if(!empty($booking_id_arr))
			{
				$temp_equipment_arr = pjBookingEquipmentModel::factory()
					->select("t1.*, t2.content as title")
					->join('pjMultiLang', "t2.model='pjEquipment' AND t2.foreign_id=t1.equipment_id AND t2.field='title' AND t2.locale='".$this->getLocaleId()."'", 'left outer')
					->whereIn('t1.booking_id', $booking_id_arr)
					->findAll()
					->getData();
				foreach($temp_equipment_arr as $k => $v)
				{
					$equipment_arr[$v['booking_id']][] = $v['title'] . ' x ' . $v['units'];
				}
				
				$temp_food_drink_arr = pjBookingFoodDrinkModel::factory()
					->select("t1.*, t2.content as title")
					->join('pjMultiLang', "t2.model='pjFoodDrink' AND t2.foreign_id=t1.food_drink_id AND t2.field='title' AND t2.locale='".$this->getLocaleId()."'", 'left outer')
					->whereIn('t1.booking_id', $booking_id_arr)
					->findAll()
					->getData();
				foreach($temp_food_drink_arr as $k => $v)
				{
					$food_drink_arr[$v['booking_id']][] = $v['title'] . ' x ' . $v['people'];
				}
			}
			
			$this->set('booking_arr', $booking_arr);
			$this->set('booking_slot_arr', $booking_slot_arr);
			$this->set('equipment_arr', $equipment_arr);
			$this->set('food_drink_arr', $food_drink_arr);
		} else {
			$this->set('status', 2);
		}
	}
	public function pjActionGetBookings()
	{
		$this->setAjax(true);
	
		if ($this->isXHR())
		{
			$date = pjUtil::formatDate($_GET['date'], $this->option_arr['o_date_format']);
			
			$this->set('booking_arr', $this->getBookings($date, $this->getLocaleId()));
		}
	}
	public function getBookings($date, $locale_id)
	{
		$bookign_slots_table = pjBookingSlotModel::factory()->getTable();
			
		$booking_arr = pjBookingModel::factory()
			->select("t1.*, t2.content as room, t3.content as layout, 
						(SELECT MIN(TBS1.start_iso) FROM `".$bookign_slots_table."` AS `TBS1` WHERE `TBS1`.booking_id=t1.id) AS min_slot,
						(SELECT MAX(TBS2.end_iso) FROM `".$bookign_slots_table."` AS `TBS2` WHERE `TBS2`.booking_id=t1.id) AS max_slot ")
			->join('pjMultiLang', "t2.model='pjRoom' AND t2.foreign_id=t1.room_id AND t2.field='title' AND t2.locale='".$locale_id."'", 'left outer')
			->join('pjMultiLang', "t3.model='pjLayout' AND t3.foreign_id=t1.layout_id AND t3.field='title' AND t3.locale='".$locale_id."'", 'left outer')
			->where("( (t1.book_by='multiday' AND '$date' BETWEEN t1.start_date AND t1.end_date) OR ( (t1.book_by='hour' OR t1.book_by='afternoon' OR t1.book_by='morning') AND t1.start_date='$date' ) )")
			->findAll()
			->getData();
		
		return $booking_arr;
	}
}
?>