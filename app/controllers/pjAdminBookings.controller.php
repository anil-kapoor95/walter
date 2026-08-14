<?php
if (!defined("ROOT_PATH"))
{
	header("HTTP/1.1 403 Forbidden");
	exit;
}
class pjAdminBookings extends pjAdmin
{
	public function pjActionCheckDaysOff()
	{
		$this->setAjax(true);
	
		if ($this->isXHR())
		{
			if (!isset($_GET['start_date']) || empty($_GET['start_date']))
			{
				echo 'true';
				exit;
			}
			
			$start_date = pjUtil::formatDate($_GET['start_date'], $this->option_arr['o_date_format']);
			$is_date_off = pjAppController::checkDateOff($start_date, $this->getForeignId());
			echo $is_date_off == true ? 'false' : 'true';
		}
		exit;
	}
	public function pjActionCheckRangeOff()
	{
		$this->setAjax(true);
	
		if ($this->isXHR())
		{
			if (!isset($_GET['start_date']) || empty($_GET['start_date']) || !isset($_GET['end_date']) || empty($_GET['end_date']) || $_GET['book_by'] != 'multiday')
			{
				echo 'true';
				exit;
			}
			$start_date = pjUtil::formatDate($_GET['start_date'], $this->option_arr['o_date_format']);
			$end_date = pjUtil::formatDate($_GET['end_date'], $this->option_arr['o_date_format']);
			
			$has_date_off = pjAppController::checkDateOffInRange($start_date, $end_date, $this->getForeignId());
			echo $has_date_off == true ? 'false' : 'true';
		}
		exit;
	}
	public function pjActionCheckDateRange()
	{
		$this->setAjax(true);
	
		if ($this->isXHR())
		{
			if (!isset($_GET['start_date']) || empty($_GET['start_date']) || !isset($_GET['end_date']) || empty($_GET['end_date']) || !isset($_GET['room_id']) || empty($_GET['room_id']) || $_GET['book_by'] != 'multiday')
			{
				echo 'true';
				exit;
			}
			$start_date = pjUtil::formatDate($_GET['start_date'], $this->option_arr['o_date_format']);
			$end_date = pjUtil::formatDate($_GET['end_date'], $this->option_arr['o_date_format']);
			
			$has_bookings = pjAppController::checkBookingsInRange($_GET['room_id'], $start_date, $end_date, $_GET['id']);
			echo $has_bookings == true ? 'false' : 'true';
		}
		exit;
	}
	public function pjActionIndex()
	{
		$this->checkLogin();
		
		if ($this->isAdmin() || $this->isEditor())
		{
			$room_arr = pjRoomModel::factory()
				->select('t1.*, t2.content as title')
				->join('pjMultiLang', "t2.model='pjRoom' AND t2.foreign_id=t1.id AND t2.field='title' AND t2.locale='".$this->getLocaleId()."'", 'left outer')
				->where('status', 'T')
				->orderBy("id ASC")
				->findAll()
				->getData();
			
			$this->set('room_arr', $room_arr);
			
			$this->appendJs('jquery.datagrid.js', PJ_FRAMEWORK_LIBS_PATH . 'pj/js/');
			$this->appendJs('pjAdminBookings.js');
		} else {
			$this->set('status', 2);
		}
	}
	
	public function pjActionGetBooking()
	{
		$this->setAjax(true);
	
		if ($this->isXHR())
		{
			$pjBookingModel = pjBookingModel::factory()
				->join('pjMultiLang', "t2.model='pjRoom' AND t2.foreign_id=t1.room_id AND t2.field='title' AND t2.locale='".$this->getLocaleId()."'", 'left outer');
				
			if (isset($_GET['q']) && !empty($_GET['q']))
			{
				$q = pjObject::escapeString($_GET['q']);
				$pjBookingModel->where("(t1.id = '$q' OR t1.uuid = '$q' OR t1.c_name LIKE '%$q%' OR t2.content LIKE '%$q%')");
			}
			if (isset($_GET['client']) && !empty($_GET['client']))
			{
				$client = pjObject::escapeString($_GET['client']);
				$pjBookingModel->where("(t1.c_name LIKE '%$client%' OR t1.c_email LIKE '%$client%' OR t1.c_phone LIKE '%$client%')");
			}
			if (isset($_GET['status']) && !empty($_GET['status']) && in_array($_GET['status'], array('confirmed','cancelled','pending')))
			{
				$pjBookingModel->where('t1.status', $_GET['status']);
			}
			if (isset($_GET['room_id']) && (int) $_GET['room_id'] > 0)
			{
				$pjBookingModel->where('t1.room_id', $_GET['room_id']);
			}
			if (isset($_GET['start_date']) && !empty($_GET['start_date']) && isset($_GET['end_date']) && !empty($_GET['end_date']))
			{
				$start_date = pjUtil::formatDate($_GET['start_date'], $this->option_arr['o_date_format']);
				$end_date = pjUtil::formatDate($_GET['end_date'], $this->option_arr['o_date_format']);
				$pjBookingModel->where("(t1.start_date BETWEEN '$start_date' AND '$end_date')");
			}elseif(isset($_GET['start_date']) && !empty($_GET['start_date']) && isset($_GET['end_date']) && empty($_GET['end_date'])){
				$start_date = pjUtil::formatDate($_GET['start_date'], $this->option_arr['o_date_format']);
				$pjBookingModel->where("(`start_date` >= '$start_date')");
			}elseif(isset($_GET['start_date']) && empty($_GET['start_date']) && isset($_GET['end_date']) && !empty($_GET['end_date'])){
				$end_date = pjUtil::formatDate($_GET['end_date'], $this->option_arr['o_date_format']);
				$pjBookingModel->where("(`start_date` <= '$end_date')");
			}
			
			$column = 'created';
			$direction = 'DESC';
			$allowed_columns = array('room', 'start_date', 'c_name', 'total', 'status');
			if (isset($_GET['direction']) && isset($_GET['column']) && in_array($_GET['column'], $allowed_columns) && in_array(strtoupper($_GET['direction']), array('ASC', 'DESC')))
			{
				$column = $_GET['column'];
				$direction = strtoupper($_GET['direction']);
			}

			$total = $pjBookingModel->findCount()->getData();
			$rowCount = isset($_GET['rowCount']) && (int) $_GET['rowCount'] > 0 ? (int) $_GET['rowCount'] : 100;
			$pages = ceil($total / $rowCount);
			$page = isset($_GET['page']) && (int) $_GET['page'] > 0 ? intval($_GET['page']) : 1;
			$offset = ((int) $page - 1) * $rowCount;
			if ($page > $pages)
			{
				$page = $pages;
			}

			$data = array();
			
			$data = $pjBookingModel
				->select("t1.*, t2.content as room, 
								AES_DECRYPT(t1.cc_type, '".PJ_SALT."') AS `cc_type`,
								AES_DECRYPT(t1.cc_num, '".PJ_SALT."') AS `cc_num`,
								AES_DECRYPT(t1.cc_exp_month, '".PJ_SALT."') AS `cc_exp_month`,
								AES_DECRYPT(t1.cc_exp_year, '".PJ_SALT."') AS `cc_exp_year`,
								AES_DECRYPT(t1.cc_code, '".PJ_SALT."') AS `cc_code`")
				->orderBy("$column $direction")
				->limit($rowCount, $offset)
				->findAll()
				->getData();
			
			foreach($data as $k => $v)
			{
				$v['c_name'] = pjSanitize::clean($v['c_name']);
				$v['start_date'] = date($this->option_arr['o_date_format'], strtotime($v['start_date']));
				$v['total'] = pjUtil::formatCurrencySign($v['total'], $this->option_arr['o_currency']);
				$data[$k] = $v;
			}
			
			pjAppController::jsonResponse(compact('data', 'total', 'pages', 'page', 'rowCount', 'column', 'direction'));
		}
		exit;
	}
	
	public function pjActionSaveBooking()
	{
		$this->setAjax(true);
	
		if ($this->isXHR())
		{
			pjBookingModel::factory()->where('id', $_GET['id'])->limit(1)->modifyAll(array($_POST['column'] => $_POST['value']));
		}
		exit;
	}
	
	public function pjActionExportBooking()
	{
		$this->checkLogin();
		
		if (isset($_POST['record']) && is_array($_POST['record']))
		{
			$arr = pjBookingModel::factory()->whereIn('id', $_POST['record'])->findAll()->getData();
			$csv = new pjCSV();
			$csv
				->setHeader(true)
				->setName("Bookings-".time().".csv")
				->process($arr)
				->download();
		}
		exit;
	}
	
	public function pjActionDeleteBooking()
	{
		$this->setAjax(true);
	
		if ($this->isXHR())
		{
			$response = array();
			if (pjBookingModel::factory()->setAttributes(array('id' => $_GET['id']))->erase()->getAffectedRows() == 1)
			{
				pjBookingSlotModel::factory()->where('booking_id', $_GET['id'])->eraseAll();
				pjBookingEquipmentModel::factory()->where('booking_id', $_GET['id'])->eraseAll();
				pjBookingFoodDrinkModel::factory()->where('booking_id', $_GET['id'])->eraseAll();
				pjBookingPaymentModel::factory()->where('booking_id', $_GET['id'])->eraseAll();
				$response['code'] = 200;
			} else {
				$response['code'] = 100;
			}
			pjAppController::jsonResponse($response);
		}
		exit;
	}
	
	public function pjActionDeleteBookingBulk()
	{
		$this->setAjax(true);
	
		if ($this->isXHR())
		{
			if (isset($_POST['record']) && count($_POST['record']) > 0)
			{
				pjBookingModel::factory()->whereIn('id', $_POST['record'])->eraseAll();
				pjBookingSlotModel::factory()->whereIn('booking_id', $_POST['record'])->eraseAll();
				pjBookingEquipmentModel::factory()->whereIn('booking_id', $_POST['record'])->eraseAll();
				pjBookingFoodDrinkModel::factory()->whereIn('booking_id', $_POST['record'])->eraseAll();
				pjBookingPaymentModel::factory()->whereIn('booking_id', $_POST['record'])->eraseAll();
			}
		}
		exit;
	}
	
	public function pjActionCreate()
	{
		$this->checkLogin();
		
		if ($this->isAdmin())
		{
			if (isset($_POST['booking_create']))
			{
				$data = array();
				
				$data['uuid'] = time();
				$data['ip'] = pjUtil::getClientIp();
				$data['start_date'] = pjUtil::formatDate($_POST['start_date'], $this->option_arr['o_date_format']);
				if($_POST['book_by'] == 'multiday')
				{
					$data['end_date'] = pjUtil::formatDate($_POST['end_date'], $this->option_arr['o_date_format']);
				}else{
					$data['end_date'] = ":NULL";
				}
				
				$id = pjBookingModel::factory(array_merge($_POST, $data))->insert()->getInsertId();
				if ($id !== false && (int) $id > 0)
				{
					if($_POST['book_by'] == 'hour')
					{
						if(isset($_POST['slots']) && is_array($_POST['slots']) && count($_POST['slots']) > 0)
						{
							$pjBookingSlotModel = pjBookingSlotModel::factory();
							foreach($_POST['slots'] as $k => $pair)
							{
								$slot_data = array();
								list($start_ts, $end_ts) = explode("|", $pair);
								$slot_data['booking_id'] = $id;
								$slot_data['start_ts'] = $start_ts;
								$slot_data['end_ts'] = $end_ts;
								$slot_data['start_iso'] = date('Y-m-d H:i:s', $start_ts);
								$slot_data['end_iso'] = date('Y-m-d H:i:s', $end_ts);
								
								$pjBookingSlotModel->reset()->setAttributes($slot_data)->insert();
							}
						}
					}
					if(isset($_POST['equipment_id']) && is_array($_POST['equipment_id']) && count($_POST['equipment_id']) > 0)
					{
						$pjBookingEquipmentModel = pjBookingEquipmentModel::factory();
						foreach($_POST['equipment_id'] as $equipment_id => $pair)
						{
							$equipment_data = array();
							list($price, $per) = explode("|", $pair);
							$equipment_data['booking_id'] = $id;
							$equipment_data['equipment_id'] = $equipment_id;
							$equipment_data['units'] = (isset($_POST['units'][$equipment_id]) ? (int) $_POST['units'][$equipment_id] : 1);
								
							$pjBookingEquipmentModel->reset()->setAttributes($equipment_data)->insert();
						}
					}
					if(isset($_POST['food_drink_id']) && is_array($_POST['food_drink_id']) && count($_POST['food_drink_id']) > 0)
					{
						$pjBookingFoodDrinkModel = pjBookingFoodDrinkModel::factory();
						foreach($_POST['food_drink_id'] as $food_drink_id => $price)
						{
							$food_drink_data = array();
							$food_drink_data['booking_id'] = $id;
							$food_drink_data['food_drink_id'] = $food_drink_id;
							$food_drink_data['people'] = (isset($_POST['people'][$food_drink_id]) ? (int) $_POST['people'][$food_drink_id] : 1);
								
							$pjBookingFoodDrinkModel->reset()->setAttributes($food_drink_data)->insert();
						}
					}
					$err = 'AR03';
				}else{
					$err = 'AR04';
				}
				
				pjUtil::redirect(PJ_INSTALL_URL. "index.php?controller=pjAdminBookings&action=pjActionIndex&err=$err");
			}else{
				
				$room_arr = pjRoomModel::factory()
					->select('t1.*, t2.content as title')
					->join('pjMultiLang', "t2.model='pjRoom' AND t2.foreign_id=t1.id AND t2.field='title' AND t2.locale='".$this->getLocaleId()."'", 'left outer')
					->where('status', 'T')
					->orderBy("id ASC")
					->findAll()
					->getData();
					
				$country_arr = pjCountryModel::factory()
					->select('t1.id, t2.content AS country_title')
					->join('pjMultiLang', "t2.model='pjCountry' AND t2.foreign_id=t1.id AND t2.field='name' AND t2.locale='".$this->getLocaleId()."'", 'left outer')
					->orderBy('`country_title` ASC')
					->findAll()
					->getData();
				
				$equipment_arr = pjEquipmentModel::factory()
					->select('t1.*, t2.content as title')
					->join('pjMultiLang', "t2.model='pjEquipment' AND t2.foreign_id=t1.id AND t2.field='title' AND t2.locale='".$this->getLocaleId()."'", 'left outer')
					->orderBy("id ASC")
					->findAll()
					->getData();
				
				$food_drink_arr = pjFoodDrinkModel::factory()
					->select('t1.*, t2.content as title')
					->join('pjMultiLang', "t2.model='pjFoodDrink' AND t2.foreign_id=t1.id AND t2.field='title' AND t2.locale='".$this->getLocaleId()."'", 'left outer')
					->orderBy("id ASC")
					->findAll()
					->getData();
				
				$this->set('room_arr', $room_arr);
				$this->set('country_arr', $country_arr);
				$this->set('equipment_arr', $equipment_arr);
				$this->set('food_drink_arr', $food_drink_arr);
				
				$this->appendJs('jquery.multiselect.min.js', PJ_THIRD_PARTY_PATH . 'multiselect/');
				$this->appendCss('jquery.multiselect.css', PJ_THIRD_PARTY_PATH . 'multiselect/');
				$this->appendJs('chosen.jquery.js', PJ_THIRD_PARTY_PATH . 'chosen/');
				$this->appendCss('chosen.css', PJ_THIRD_PARTY_PATH . 'chosen/');
				$this->appendJs('jquery.validate.min.js', PJ_THIRD_PARTY_PATH . 'validate/');
				$this->appendJs('pjAdminBookings.js');
			}
		} else {
			
			$this->set('status', 2);
		}
	}
	
	public function pjActionUpdate()
	{
		$this->checkLogin();
		
		if ($this->isAdmin() || $this->isEditor())
		{
			if (isset($_POST['booking_update']))
			{
				$pjBookingModel = pjBookingModel::factory();
				
				$arr = pjBookingModel::factory()->find($_POST['id'])->getData();
				if (empty($arr))
				{
					pjUtil::redirect($_SERVER['PHP_SELF'] . "?controller=pjAdminOrders&action=pjActionIndex&err=AR08");
				}
				
				
				$data = array();
				
				$data['ip'] = pjUtil::getClientIp();
				$data['start_date'] = pjUtil::formatDate($_POST['start_date'], $this->option_arr['o_date_format']);
				if($_POST['book_by'] == 'multiday')
				{
					$data['end_date'] = pjUtil::formatDate($_POST['end_date'], $this->option_arr['o_date_format']);
				}else{
					$data['end_date'] = ":NULL";
				}
				
				$pjBookingModel->reset()->where('id', $_POST['id'])->limit(1)->modifyAll(array_merge($_POST, $data));
				
				$pjBookingSlotModel = pjBookingSlotModel::factory();
				$pjBookingSlotModel->where('booking_id', $_POST['id'])->eraseAll();
				if($_POST['book_by'] == 'hour')
				{
					if(isset($_POST['slots']) && is_array($_POST['slots']) && count($_POST['slots']) > 0)
					{
						foreach($_POST['slots'] as $k => $pair)
						{
							$slot_data = array();
							list($start_ts, $end_ts) = explode("|", $pair);
							$slot_data['booking_id'] = $_POST['id'];
							$slot_data['start_ts'] = $start_ts;
							$slot_data['end_ts'] = $end_ts;
							$slot_data['start_iso'] = date('Y-m-d H:i:s', $start_ts);
							$slot_data['end_iso'] = date('Y-m-d H:i:s', $end_ts);
				
							$pjBookingSlotModel->reset()->setAttributes($slot_data)->insert();
						}
					}
				}
				$pjBookingEquipmentModel = pjBookingEquipmentModel::factory();
				$pjBookingEquipmentModel->where('booking_id', $_POST['id'])->eraseAll();
				if(isset($_POST['equipment_id']) && is_array($_POST['equipment_id']) && count($_POST['equipment_id']) > 0)
				{
					foreach($_POST['equipment_id'] as $equipment_id => $pair)
					{
						$equipment_data = array();
						list($price, $per) = explode("|", $pair);
						$equipment_data['booking_id'] = $_POST['id'];
						$equipment_data['equipment_id'] = $equipment_id;
						$equipment_data['units'] = (isset($_POST['units'][$equipment_id]) ? (int) $_POST['units'][$equipment_id] : 1);
				
						$pjBookingEquipmentModel->reset()->setAttributes($equipment_data)->insert();
					}
				}
				$pjBookingFoodDrinkModel = pjBookingFoodDrinkModel::factory();
				$pjBookingFoodDrinkModel->where('booking_id', $_POST['id'])->eraseAll();
				if(isset($_POST['food_drink_id']) && is_array($_POST['food_drink_id']) && count($_POST['food_drink_id']) > 0)
				{
					foreach($_POST['food_drink_id'] as $food_drink_id => $price)
					{
						$food_drink_data = array();
						$food_drink_data['booking_id'] = $_POST['id'];
						$food_drink_data['food_drink_id'] = $food_drink_id;
						$food_drink_data['people'] = (isset($_POST['people'][$food_drink_id]) ? (int) $_POST['people'][$food_drink_id] : 1);
				
						$pjBookingFoodDrinkModel->reset()->setAttributes($food_drink_data)->insert();
					}
				}
				
				$err = 'AR01';
				pjUtil::redirect(PJ_INSTALL_URL. "index.php?controller=pjAdminBookings&action=pjActionIndex&err=$err");
			}else{
				
				$arr = pjBookingModel::factory()->find($_GET['id'])->getData();
				if(count($arr) <= 0)
				{
					pjUtil::redirect(PJ_INSTALL_URL. "index.php?controller=pjAdminBookings&action=pjActionIndex&err=AR08");
				}
				$this->set('arr', $arr);
				
				$room_arr = pjRoomModel::factory()
					->select('t1.*, t2.content as title')
					->join('pjMultiLang', "t2.model='pjRoom' AND t2.foreign_id=t1.id AND t2.field='title' AND t2.locale='".$this->getLocaleId()."'", 'left outer')
					->where('status', 'T')
					->orderBy("id ASC")
					->findAll()
					->getData();
					
				$country_arr = pjCountryModel::factory()
					->select('t1.id, t2.content AS country_title')
					->join('pjMultiLang', "t2.model='pjCountry' AND t2.foreign_id=t1.id AND t2.field='name' AND t2.locale='".$this->getLocaleId()."'", 'left outer')
					->orderBy('`country_title` ASC')
					->findAll()
					->getData();
				
				$equipment_arr = pjEquipmentModel::factory()
					->select('t1.*, t2.content as title')
					->join('pjMultiLang', "t2.model='pjEquipment' AND t2.foreign_id=t1.id AND t2.field='title' AND t2.locale='".$this->getLocaleId()."'", 'left outer')
					->orderBy("id ASC")
					->findAll()
					->getData();
				
				$food_drink_arr = pjFoodDrinkModel::factory()
					->select('t1.*, t2.content as title')
					->join('pjMultiLang', "t2.model='pjFoodDrink' AND t2.foreign_id=t1.id AND t2.field='title' AND t2.locale='".$this->getLocaleId()."'", 'left outer')
					->orderBy("id ASC")
					->findAll()
					->getData();
				
				$duration_info_arr = pjAppController::getDuration($arr['room_id'], $arr['start_date'], $arr['id'], $this->getLocaleId(), $this->getForeignId(), $this->option_arr);

				$booking_equipment_arr = array();
				$temp_booking_equipment_arr = pjBookingEquipmentModel::factory()->where('t1.booking_id', $arr['id'])->findAll()->getData();
				foreach($temp_booking_equipment_arr as $k => $v)
				{
					$booking_equipment_arr[$v['equipment_id']] = $v;
				}
				
				$booking_food_drink_arr = array();
				$temp_booking_food_drink_arr = pjBookingFoodDrinkModel::factory()->where('t1.booking_id', $arr['id'])->findAll()->getData();
				foreach($temp_booking_food_drink_arr as $k => $v)
				{
					$booking_food_drink_arr[$v['food_drink_id']] = $v;
				}
				
				$booking_slot_arr = array();
				$temp_booking_slot_arr = pjBookingSlotModel::factory()->where('t1.booking_id', $arr['id'])->findAll()->getData();
				foreach($temp_booking_slot_arr as $k => $v)
				{
					$booking_slot_arr[] = $v['start_ts'] . '|' . $v['end_ts'];
				}
				
				$this->set('room_arr', $room_arr);
				$this->set('room', $duration_info_arr['arr']);
				$this->set('layout_arr', $duration_info_arr['layout_arr']);
				$this->set('from_to_arr', $duration_info_arr['from_to_arr']);
				$this->set('full_day_booked', $duration_info_arr['full_day_booked']);
				$this->set('multi_day_booked', $duration_info_arr['multi_day_booked']);
				$this->set('halfday_morning', $duration_info_arr['halfday_morning']);
				$this->set('halfday_afternoon', $duration_info_arr['halfday_afternoon']);
				$this->set('hourly_morning', $duration_info_arr['hourly_morning']);
				$this->set('hourly_afternoon', $duration_info_arr['hourly_afternoon']);
				$this->set('country_arr', $country_arr);
				$this->set('equipment_arr', $equipment_arr);
				$this->set('food_drink_arr', $food_drink_arr);
				$this->set('booking_food_drink_arr', $booking_food_drink_arr);
				$this->set('booking_equipment_arr', $booking_equipment_arr);
				$this->set('booking_slot_arr', $booking_slot_arr);
				
				$this->appendJs('tinymce.min.js', PJ_THIRD_PARTY_PATH . 'tinymce/');
				$this->appendJs('jquery.multiselect.min.js', PJ_THIRD_PARTY_PATH . 'multiselect/');
				$this->appendCss('jquery.multiselect.css', PJ_THIRD_PARTY_PATH . 'multiselect/');
				$this->appendJs('chosen.jquery.js', PJ_THIRD_PARTY_PATH . 'chosen/');
				$this->appendCss('chosen.css', PJ_THIRD_PARTY_PATH . 'chosen/');
				$this->appendJs('jquery.validate.min.js', PJ_THIRD_PARTY_PATH . 'validate/');
				$this->appendJs('pjAdminBookings.js');
			}
		} else {
			$this->set('status', 2);
		}
	}
	public function pjActionConfirmation()
	{
		$this->setAjax(true);
	
		if ($this->isXHR())
		{
			if (isset($_POST['send_confirm']) && !empty($_POST['to']) && !empty($_POST['from']) &&
					!empty($_POST['subject']) && !empty($_POST['message']))
			{
				$Email = new pjEmail();
				$Email->setContentType('text/html');
				if ($this->option_arr['o_send_email'] == 'smtp')
				{
					$Email
						->setTransport('smtp')
						->setSmtpHost($this->option_arr['o_smtp_host'])
						->setSmtpPort($this->option_arr['o_smtp_port'])
						->setSmtpUser($this->option_arr['o_smtp_user'])
						->setSmtpPass($this->option_arr['o_smtp_pass'])
						->setSender($this->option_arr['o_smtp_user']);
				}
	
				$subject = $_POST['subject'];
				$message = $_POST['message'];
				
				if (get_magic_quotes_gpc())
				{
					$subject = stripslashes($_POST['subject']);
					$message = stripslashes($_POST['message']);
				}
	
				$r = $Email
					->setTo($_POST['to'])
					->setFrom($_POST['from'])
					->setSubject($subject)
					->send($message);
					
				if ($r)
				{
					pjAppController::jsonResponse(array('status' => 'OK', 'code' => 200, 'text' => 'Email has been sent.'));
				}
				pjAppController::jsonResponse(array('status' => 'ERR', 'code' => 100, 'text' => 'Email failed to send.'));
			}
	
			if (isset($_GET['booking_id']) && (int) $_GET['booking_id'] > 0)
			{
				$pjMultiLangModel = pjMultiLangModel::factory();
				$lang_message = $pjMultiLangModel->reset()->select('t1.*')
					->where('t1.model','pjOption')
					->where('t1.locale', $this->getLocaleId())
					->where('t1.field', 'o_email_confirmation_message')
					->limit(0, 1)
					->findAll()->getData();
				$lang_subject = $pjMultiLangModel->reset()->select('t1.*')
					->where('t1.model','pjOption')
					->where('t1.locale', $this->getLocaleId())
					->where('t1.field', 'o_email_confirmation_subject')
					->limit(0, 1)
					->findAll()->getData();
	
				if (count($lang_message) === 1 && count($lang_subject) === 1)
				{
					$booking_arr = pjBookingModel::factory()->find($_GET['booking_id'])->getData();
					$tokens = pjAppController::getTokens($_GET['booking_id'], $this->option_arr, PJ_SALT, $this->getLocaleId());
					
					$subject_client = str_replace($tokens['search'], $tokens['replace'], $lang_subject[0]['content']);
					$message_client = str_replace($tokens['search'], $tokens['replace'], $lang_message[0]['content']);
	
					$this->set('arr', array(
							'to' => $booking_arr['c_email'],
							'from' => $this->getAdminEmail(),
							'message' => $message_client,
							'subject' => $subject_client
					));
				}
			} else {
				exit;
			}
		}
	}
	public function pjActionGetDuration()
	{
		$this->setAjax(true);
	
		if ($this->isXHR())
		{
			if (isset($_POST['room_id']) && (int) $_POST['room_id'] > 0)
			{
				$start_date = pjUtil::formatDate( $_POST['start_date'], $this->option_arr['o_date_format']);
				$duration_info_arr = pjAppController::getDuration($_POST['room_id'], $start_date, isset($_POST['id']) ? $_POST['id'] : null, $this->getLocaleId(), $this->getForeignId(), $this->option_arr);
				
				$this->set('arr', $duration_info_arr['arr']);
				$this->set('full_day_booked', $duration_info_arr['full_day_booked']);
				$this->set('multi_day_booked', $duration_info_arr['multi_day_booked']);
				$this->set('layout_arr', $duration_info_arr['layout_arr']);
				$this->set('from_to_arr', $duration_info_arr['from_to_arr']);
				$this->set('halfday_morning', $duration_info_arr['halfday_morning']);
				$this->set('halfday_afternoon', $duration_info_arr['halfday_afternoon']);
				$this->set('hourly_morning', $duration_info_arr['hourly_morning']);
				$this->set('hourly_afternoon', $duration_info_arr['hourly_afternoon']);
			}
		}
	}
	public function pjActionGetPrices()
	{
		$this->setAjax(true);
	
		if ($this->isXHR())
		{
			$start_date = pjUtil::formatDate($_POST['start_date'], $this->option_arr['o_date_format']);
			$end_date = pjUtil::formatDate($_POST['end_date'], $this->option_arr['o_date_format']);
			$attendees = $_POST['attendees'];
			$room_id = $_POST['room_id'];
			$book_by = $_POST['book_by'];
			$slots = isset($_POST['slots']) ? $_POST['slots'] : array();
			$equipment_id = isset($_POST['equipment_id']) ? $_POST['equipment_id'] : array();
			$units = isset($_POST['units']) ? $_POST['units'] : array();
			$food_drink_id = isset($_POST['food_drink_id']) ? $_POST['food_drink_id'] : array();
			$people = isset($_POST['people']) ? $_POST['people'] : array();
			
			$price_arr = pjAppController::getPrices($start_date, $end_date, $room_id, $book_by, $slots, $equipment_id, $units, $food_drink_id, $people, $this->getForeignId(), $this->option_arr);
			
			pjAppController::jsonResponse($price_arr);
		}
	}
}
?>