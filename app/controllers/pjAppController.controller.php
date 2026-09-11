<?php
if (!defined("ROOT_PATH"))
{
	header("HTTP/1.1 403 Forbidden");
	exit;
}
class pjAppController extends pjController
{
	public $models = array();

	/**
	 * Generate (or return existing) CSRF token for the current session.
	 */
	public static function getCsrfToken()
	{
		if (empty($_SESSION['csrf_token']))
		{
			$_SESSION['csrf_token'] = bin2hex(random_bytes(32));
		}
		return $_SESSION['csrf_token'];
	}

	/**
	 * Validate CSRF token (admin requests).
	 */
	public static function validateCsrfToken()
	{
		$token = '';
		if (!empty($_POST['csrf_token'])) {
			$token = $_POST['csrf_token'];
		} elseif (!empty($_SERVER['HTTP_X_CSRF_TOKEN'])) {
			$token = $_SERVER['HTTP_X_CSRF_TOKEN'];
		}
		if (empty($_SESSION['csrf_token']) || !hash_equals($_SESSION['csrf_token'], (string) $token))
		{
			header('HTTP/1.1 403 Forbidden');
			exit('Invalid CSRF token.');
		}
	}
	
	public $option_arr = array();
	
	public $defaultLocale = 'admin_locale_id';
  
	public $defaultFields = 'fields';
	
	public $defaultFieldsIndex = 'fields_index';
  
	protected function loadSetFields($force=FALSE, $locale_id=NULL, $fields=NULL)
	{
		if (is_null($locale_id))
		{
			$locale_id = $this->getLocaleId();
		}
		
		if (is_null($fields))
		{
			$fields = $this->defaultFields;
		}
		
		$registry = pjRegistry::getInstance();
		if ($force
				|| !isset($_SESSION[$this->defaultFieldsIndex])
				|| $_SESSION[$this->defaultFieldsIndex] != $this->option_arr['o_fields_index']
				|| !isset($_SESSION[$fields])
				|| empty($_SESSION[$fields]))
		{
			pjAppController::setFields($locale_id);
	
			# Update session
			if ($registry->is('fields'))
			{
				$_SESSION[$fields] = $registry->get('fields');
			}
			$_SESSION[$this->defaultFieldsIndex] = $this->option_arr['o_fields_index'];
		}
	
		if (isset($_SESSION[$fields]) && !empty($_SESSION[$fields]))
		{
			# Load fields from session
			$registry->set('fields', $_SESSION[$fields]);
		}
		
		return TRUE;
	}
	
	public function isCountryReady()
    {
    	return $this->isAdmin();
    }
    
	public function isOneAdminReady()
    {
    	return $this->isAdmin();
    }
	
	public static function setTimezone($timezone="UTC")
    {
    	if (in_array(version_compare(phpversion(), '5.1.0'), array(0,1)))
		{
			date_default_timezone_set($timezone);
		} else {
			$safe_mode = ini_get('safe_mode');
			if ($safe_mode)
			{
				putenv("TZ=".$timezone);
			}
		}
    }

	public static function setMySQLServerTime($offset="-0:00")
    {
		pjAppModel::factory()->prepare("SET SESSION time_zone = :offset;")->exec(compact('offset'));
    }
    
	public function setTime()
	{
		if (isset($this->option_arr['o_timezone']))
		{
			$offset = $this->option_arr['o_timezone'] / 3600;
			if ($offset > 0)
			{
				$offset = "-".$offset;
			} elseif ($offset < 0) {
				$offset = "+".abs($offset);
			} elseif ($offset === 0) {
				$offset = "+0";
			}
	
			pjAppController::setTimezone('Etc/GMT' . $offset);
			if (strpos($offset, '-') !== false)
			{
				$offset = str_replace('-', '+', $offset);
			} elseif (strpos($offset, '+') !== false) {
				$offset = str_replace('+', '-', $offset);
			}
			pjAppController::setMySQLServerTime($offset . ":00");
		}
	}
    
    public function beforeFilter()
    {
		if (strtoupper(@$_SERVER['REQUEST_METHOD']) === 'POST')
		{
			$_csrf_ctrl = isset($_GET['controller']) ? $_GET['controller'] : '';
			$_csrf_act = isset($_GET['action']) ? $_GET['action'] : '';
			if (strpos($_csrf_ctrl, 'pjAdmin') === 0
				&& !in_array($_csrf_act, array('pjActionLogin', 'pjActionForgot', 'pjActionLogout', 'pjActionPreview'), true)) {
				pjAppController::validateCsrfToken();
			}
		}

    	$this->appendJs('jquery.min.js', PJ_THIRD_PARTY_PATH . 'jquery/');
		$baseDir = defined("PJ_INSTALL_PATH") ? PJ_INSTALL_PATH : null;
		$dm = new pjDependencyManager($baseDir, PJ_THIRD_PARTY_PATH);
		$dm->load(PJ_CONFIG_PATH . 'dependencies.php')->resolve();
		$this->appendJs('jquery-migrate.min.js', $dm->getPath('jquery_migrate'), FALSE, FALSE);
		$this->appendJs('pjAdminCore.js');
		$this->appendCss('reset.css');
		 
		$this->appendJs('js/jquery-ui.custom.min.js', PJ_THIRD_PARTY_PATH . 'jquery_ui/');
		$this->appendCss('css/smoothness/jquery-ui.min.css', PJ_THIRD_PARTY_PATH . 'jquery_ui/');
				
		$this->appendCss('pj-all.css', PJ_FRAMEWORK_LIBS_PATH . 'pj/css/');
		$this->appendCss('admin.css');
		
    	if ($_GET['controller'] != 'pjInstaller')
		{
			$this->models['Option'] = pjOptionModel::factory();
			$this->option_arr = $this->models['Option']->getPairs($this->getForeignId());
			$this->set('option_arr', $this->option_arr);
			$this->setTime();
			
			if (!isset($_SESSION[$this->defaultLocale]))
			{
				$locale_arr = pjLocaleModel::factory()->where('is_default', 1)->limit(1)->findAll()->getData();
				if (count($locale_arr) === 1)
				{
					$this->setLocaleId($locale_arr[0]['id']);
				}
			}
			$this->loadSetFields();
		}
    }
    
    public function isEditor()
    {
    	return $this->getRoleId() == 2;
    }
    
    public function getForeignId()
    {
    	return 1;
    }
    
    public static function setFields($locale)
    {
    if(isset($_SESSION['lang_show_id']) && (int) $_SESSION['lang_show_id'] == 1)
		{
			$fields = pjMultiLangModel::factory()
				->select('CONCAT(t1.content, CONCAT(":", t2.id, ":")) AS content, t2.key')
				->join('pjField', "t2.id=t1.foreign_id", 'inner')
				->where('t1.locale', $locale)
				->where('t1.model', 'pjField')
				->where('t1.field', 'title')
				->findAll()
				->getDataPair('key', 'content');
		}else{
			$fields = pjMultiLangModel::factory()
				->select('t1.content, t2.key')
				->join('pjField', "t2.id=t1.foreign_id", 'inner')
				->where('t1.locale', $locale)
				->where('t1.model', 'pjField')
				->where('t1.field', 'title')
				->findAll()
				->getDataPair('key', 'content');
		}
		$registry = pjRegistry::getInstance();
		$tmp = array();
		if ($registry->is('fields'))
		{
			$tmp = $registry->get('fields');
		}
		$arrays = array();
		foreach ($fields as $key => $value)
		{
			if (strpos($key, '_ARRAY_') !== false)
			{
				list($prefix, $suffix) = explode("_ARRAY_", $key);
				if (!isset($arrays[$prefix]))
				{
					$arrays[$prefix] = array();
				}
				$arrays[$prefix][$suffix] = $value;
			}
		}
		require PJ_CONFIG_PATH . 'settings.inc.php';
		$fields = array_merge($tmp, $fields, $settings, $arrays);
		$registry->set('fields', $fields);
    }

    public static function jsonDecode($str)
	{
		$Services_JSON = new pjServices_JSON();
		return $Services_JSON->decode($str);
	}
	
	public static function jsonEncode($arr)
	{
		$Services_JSON = new pjServices_JSON();
		return $Services_JSON->encode($arr);
	}
	
	public static function jsonResponse($arr)
	{
		header("Content-Type: application/json; charset=utf-8");
		echo pjAppController::jsonEncode($arr);
		exit;
	}

	public function getLocaleId()
	{
		return isset($_SESSION[$this->defaultLocale]) && (int) $_SESSION[$this->defaultLocale] > 0 ? (int) $_SESSION[$this->defaultLocale] : false;
	}
	
	public function setLocaleId($locale_id)
	{
		$_SESSION[$this->defaultLocale] = (int) $locale_id;
	}
	
	public function pjActionCheckInstall()
	{
		$this->setLayout('pjActionEmpty');
		
		$result = array('status' => 'OK', 'code' => 200, 'text' => 'Operation succeeded', 'info' => array());
		$folders = array(
							'app/web/upload',
							'app/web/upload/rooms',
							'app/web/upload/rooms/layouts'
						);
		foreach ($folders as $dir)
		{
			if (!is_writable($dir))
			{
				$result['status'] = 'ERR';
				$result['code'] = 101;
				$result['text'] = 'Permission requirement';
				$result['info'][] = sprintf('Folder \'<span class="bold">%1$s</span>\' is not writable. You need to set write permissions (chmod 777) to directory located at \'<span class="bold">%1$s</span>\'', $dir);
			}
		}
		
		return $result;
	}
	
	public function friendlyURL($str, $divider='-')
	{
		$str = mb_strtolower($str, mb_detect_encoding($str));
		$str = trim($str);
		$str = preg_replace('/[_|\s]+/', $divider, $str);
		$str = preg_replace('/\x{00C5}/u', 'AA', $str);
		$str = preg_replace('/\x{00C6}/u', 'AE', $str);
		$str = preg_replace('/\x{00D8}/u', 'OE', $str);
		$str = preg_replace('/\x{00E5}/u', 'aa', $str);
		$str = preg_replace('/\x{00E6}/u', 'ae', $str);
		$str = preg_replace('/\x{00F8}/u', 'oe', $str);
		$str = preg_replace('/[^a-z\x{0400}-\x{04FF}0-9-]+/u', '', $str);
		$str = preg_replace('/[-]+/', $divider, $str);
		$str = preg_replace('/^-+|-+$/', '', $str);
		return $str;
	}
	
	public function getAdminEmail()
	{
		$arr = pjUserModel::factory()
			->findAll()
			->orderBy("t1.id ASC")
			->limit(1)
			->getData();
		return !empty($arr) ? $arr[0]['email'] : null;	
	}
	
	public function getAdminPhone()
	{
		$arr = pjUserModel::factory()
			->findAll()
			->orderBy("t1.id ASC")
			->limit(1)
			->getData();
		return !empty($arr) ? (!empty($arr[0]['phone']) ? $arr[0]['phone'] : null) : null;	
	}
	
	public static function getTokens($booking_id, $option_arr, $salt, $locale_id)
	{
		$country = NULL;
		$date = NULL;
		$room = NULL;
		$room_layout = NULL;
		$duration = NULL;
		$equipment = NULL;
		$food_drinks = NULL;
		
		$data = pjBookingModel::factory()->find($booking_id)->getData();
		
		$personal_titles = __('personal_titles', true, false);
		$payment_methods = __('payment_methods', true, false);
	
		$title = $personal_titles[$data['c_title']];
		$payment_method = !empty($data['payment_method']) ? $payment_methods[$data['payment_method']]: NULL;
	
		$room_arr = pjRoomModel::factory()
			->join('pjMultiLang', "t2.foreign_id = t1.id AND t2.model = 'pjRoom' AND t2.locale = '".$locale_id."' AND t2.field = 'title'", 'left')
			->select('t1.*, t2.content as title')
			->find($data['room_id'])
			->getData();
		
		$price = '';
		if($data['book_by'] == 'multiday')
		{
			$price = pjUtil::formatCurrencySign($room_arr['price_per_day'], $option_arr['o_currency']) . ' ' . __('front_per_day', true);
			$duration = date($option_arr['o_date_format'], strtotime($data['start_date'])) . ' - ' . date($option_arr['o_date_format'], strtotime($data['end_date']));
		}elseif($data['book_by'] == 'morning' || $data['book_by'] == 'afternoon' || $data['book_by'] == 'evening' || $data['book_by'] == 'morningafternoon' || $data['book_by'] == 'afternoonevening'){
			$is_combo = ($data['book_by'] == 'morningafternoon' || $data['book_by'] == 'afternoonevening');
			$price = pjUtil::formatCurrencySign($is_combo ? $room_arr['price_half_day'] * 2 : $room_arr['price_half_day'], $option_arr['o_currency']) . ' ' . __('front_half_day', true);

			$morning_arr = array();
			$afternoon_arr = array();
			$evening_arr = array();
			
			if($data['start_date'] != '')
			{
				$pjDateModel = pjDateModel::factory();
				$date_arr = $pjDateModel->getDailyWorkingTime(1, $data['start_date']);
				if ($date_arr === false)
				{
					$pjWorkingTimeModel = pjWorkingTimeModel::factory();
					$wt_data = $pjWorkingTimeModel->getWorkingTime(1);
					$wt_arr = $pjWorkingTimeModel->filterDate($wt_data, $data['start_date']);
					if (!empty($wt_arr))
					{
						$morning_arr['start_ts'] = $wt_arr['morning_start_ts'];
						$morning_arr['end_ts'] = $wt_arr['morning_end_ts'];
						$afternoon_arr['start_ts'] = $wt_arr['afternoon_start_ts'];
						$afternoon_arr['end_ts'] = $wt_arr['afternoon_end_ts'];
						if (isset($wt_arr['evening_start_ts'], $wt_arr['evening_end_ts']))
						{
							$evening_arr['start_ts'] = $wt_arr['evening_start_ts'];
							$evening_arr['end_ts'] = $wt_arr['evening_end_ts'];
						}
					}
				} else {
					if (count($date_arr) > 0)
					{
						$morning_arr['start_ts'] = $date_arr['morning_start_ts'];
						$morning_arr['end_ts'] = $date_arr['morning_end_ts'];
						$afternoon_arr['start_ts'] = $date_arr['afternoon_start_ts'];
						$afternoon_arr['end_ts'] = $date_arr['afternoon_end_ts'];
						if (isset($date_arr['evening_start_ts'], $date_arr['evening_end_ts']))
						{
							$evening_arr['start_ts'] = $date_arr['evening_start_ts'];
							$evening_arr['end_ts'] = $date_arr['evening_end_ts'];
						}
					}
				}
			}
			$duration = __('front_' . $data['book_by'], true);
			if($data['book_by'] == 'morning' && !empty($morning_arr))
			{
				$duration .= ' ('.date($option_arr['o_time_format'], $morning_arr['start_ts']) . ' - ' . date($option_arr['o_time_format'], $morning_arr['end_ts']) .')';
			}
			if($data['book_by'] == 'afternoon' && !empty($afternoon_arr))
			{
				$duration .= ' ('.date($option_arr['o_time_format'], $afternoon_arr['start_ts']) . ' - ' . date($option_arr['o_time_format'], $afternoon_arr['end_ts']) .')';
			}
			if($data['book_by'] == 'evening' && !empty($evening_arr))
			{
				$duration .= ' ('.date($option_arr['o_time_format'], $evening_arr['start_ts']) . ' - ' . date($option_arr['o_time_format'], $evening_arr['end_ts']) .')';
			}
			if($data['book_by'] == 'morningafternoon' && !empty($morning_arr) && !empty($afternoon_arr))
			{
				$duration .= ' ('.date($option_arr['o_time_format'], $morning_arr['start_ts']) . ' - ' . date($option_arr['o_time_format'], $afternoon_arr['end_ts']) .')';
			}
			if($data['book_by'] == 'afternoonevening' && !empty($afternoon_arr) && !empty($evening_arr))
			{
				$duration .= ' ('.date($option_arr['o_time_format'], $afternoon_arr['start_ts']) . ' - ' . date($option_arr['o_time_format'], $evening_arr['end_ts']) .')';
			}
		}else{
			$price = pjUtil::formatCurrencySign($room_arr['price_per_hour'], $option_arr['o_currency']) . ' ' . __('front_per_hour', true);
			$temp_slot_arr = pjBookingSlotModel::factory()->where('t1.booking_id', $data['id'])->orderBy('t1.start_ts ASC')->findAll()->getData();
			$slot_arr = array();
			foreach($temp_slot_arr as $k => $v)
			{
				$slot_arr[] = date($option_arr['o_time_format'], $v['start_ts']) . ' - ' . date($option_arr['o_time_format'], $v['end_ts']);
			}
			$duration = join("<br/>", $slot_arr);
		}
		$room = $room_arr['title'] . ' ('.$price.')'; 
		
		$date = date($option_arr['o_date_format'], strtotime($data['start_date']));
		
		$room_layout_arr = pjLayoutModel::factory()
			->join('pjMultiLang', "t2.foreign_id = t1.id AND t2.model = 'pjLayout' AND t2.locale = '".$locale_id."' AND t2.field = 'title'", 'left')
			->select('t1.*, t2.content as title')
			->find($data['layout_id'])
			->getData();
		$room_layout = $room_layout_arr['title'];
		
		if (isset($data['c_country']) && !empty($data['c_country']))
		{
			if(isset($data['c_country']) && (int) $data['c_country'] > 0)
			{
				$country_arr = pjCountryModel::factory()
					->select('t1.id, t2.content AS country_title')
					->join('pjMultiLang', "t2.model='pjCountry' AND t2.foreign_id=t1.id AND t2.field='name' AND t2.locale='".$locale_id."'", 'left outer')
					->find($data['c_country'])
					->getData();
				if (!empty($country_arr))
				{
					$country = $country_arr['country_title'];
				}
			}
		}
		
		$temp_equipment_arr = pjBookingEquipmentModel::factory()
			->select('t1.*, t2.content as title, t3.multi_units, t3.price, t3.price_per')
			->join('pjMultiLang', "t2.model='pjEquipment' AND t2.foreign_id=t1.equipment_id AND t2.field='title' AND t2.locale='".$locale_id."'", 'left outer')
			->join('pjEquipment', 't3.id=t1.equipment_id', 'left outer')
			->where('t1.booking_id', $booking_id)
			->findAll()->getData();
		$equipment_arr = array();
		$price_per = array();
		$price_per['booking'] = __('lblPricePerBooking', true);
		$price_per['hour'] = __('lblPricePerHour', true);
		foreach($temp_equipment_arr as $k => $v)
		{
			if($v['price_per'] == 'booking')
			{
				$equipment_price = $v['price'] * $v['units'];
				$equipment_arr[] = $v['title'] . '('. pjUtil::formatCurrencySign($v['price'], $option_arr['o_currency']) . ' ' . $price_per[$v['price_per']] .') x ' . $v['units'] . ' = ' . pjUtil::formatCurrencySign(number_format($equipment_price, 2), $option_arr['o_currency']);
			}else{
				$equipment_price = $v['price'] * $v['units'] * $data['hours'];
				$equipment_arr[] = $v['title'] . ' ('. pjUtil::formatCurrencySign($v['price'], $option_arr['o_currency']) . ' ' . $price_per[$v['price_per']] .') x ' . $v['units'] . ' x ' . $data['hours'] . ' ' . __('front_hours', true) . ' = ' . pjUtil::formatCurrencySign(number_format($equipment_price, 2), $option_arr['o_currency']);
			}
		}
		$equipment = join("<br/>", $equipment_arr);
		
		$temp_food_drink_arr = pjBookingFoodDrinkModel::factory()
			->select('t1.*, t2.content as title, t3.price')
			->join('pjMultiLang', "t2.model='pjFoodDrink' AND t2.foreign_id=t1.food_drink_id AND t2.field='title' AND t2.locale='".$locale_id."'", 'left outer')
			->join('pjFoodDrink', 't3.id=t1.food_drink_id', 'left outer')
			->where('t1.booking_id', $booking_id)
			->findAll()->getData();
		$food_drink_arr = array();
		foreach($temp_food_drink_arr as $k => $v)
		{
			$food_drink_price = $v['price'] * $v['people'];
			$food_drink_arr[] = $v['title'] . ' ('.pjUtil::formatCurrencySign($v['price'], $option_arr['o_currency']) . ' ' . __('front_per_person', true) .') x ' . $v['people'] . ' = ' . pjUtil::formatCurrencySign(number_format($food_drink_price, 2), $option_arr['o_currency']);
		}
		$food_drinks = join("<br/>", $food_drink_arr);
	
		$subtotal = pjUtil::formatCurrencySign($data['subtotal'], $option_arr['o_currency']);
		$tax = pjUtil::formatCurrencySign($data['tax'], $option_arr['o_currency']);
		$deposit = pjUtil::formatCurrencySign($data['deposit'], $option_arr['o_currency']);
		$total = pjUtil::formatCurrencySign($data['total'], $option_arr['o_currency']);
	
		$cancelURL = PJ_INSTALL_URL . 'index.php?controller=pjFront&action=pjActionCancel&id='.@$data['id'].'&hash='.sha1(@$data['id'].@$data['created'].$salt);
		$cancelURL = '<a href="'.$cancelURL.'">' . $cancelURL . '</a>';
		
		$cc_exp = '';
		if(!empty($data['payment_method']) && $data['payment_method'] == 'creditcard')
		{
			$cc_exp = @$data['cc_exp_month'] . '-' . @$data['cc_exp_year'];
		}
		
		$search = array(
				'{Date}',
				'{Room}',
				'{RoomLayout}',
				'{Duration}',
				'{Attendees}',
				'{Equipment}',
				'{FoodDrinks}',
				'{UniqueID}',
				'{Title}',
				'{Name}',
				'{Address}',
				'{Zip}',
				'{City}',
				'{State}',
				'{Country}',
				'{Email}',
				'{Phone}',
				'{Company}',
				'{SubTotal}',
				'{Total}',
				'{Tax}',
				'{Deposit}',
				'{PaymentMethod}',
				'{CCType}',
				'{CCNum}',
				'{CCExp}',
				'{CCSec}',
				'{CancelURL}'
		);
		$replace = array(
				$date,
				$room,
				$room_layout,
				$duration,
				@$data['attendees'],
				$equipment,
				$food_drinks,
				@$data['uuid'],
				$title,
				@$data['c_name'],
				@$data['c_address'],
				@$data['c_zip'],
				@$data['c_city'],
				@$data['c_state'],
				$country,
				@$data['c_email'],
				@$data['c_phone'],
				@$data['c_company'],
				$subtotal,
				$total,
				$tax,
				$deposit,
				$payment_method,
				@$data['cc_type'],
				@$data['cc_num'],
				$cc_exp,
				@$data['cc_code'],
				$cancelURL
		);
		return compact('search', 'replace');
	}
	public static function checkBothHalfDay($room_id, $date)
	{
		$pjBookingModel = pjBookingModel::factory();
		$cnt_morning = $pjBookingModel
			->where('t1.room_id', $room_id)
			->where("(t1.start_date='$date')")
			->where("(t1.book_by='morning' OR t1.book_by='morningafternoon')")
			->where("t1.status <>", 'cancelled')
			->findCount()
			->getData();
		$cnt_afternoon = $pjBookingModel
			->reset()
			->where('t1.room_id', $room_id)
			->where("(t1.start_date='$date')")
			->where("(t1.book_by='afternoon' OR t1.book_by='morningafternoon' OR t1.book_by='afternoonevening')")
			->where("t1.status <>", 'cancelled')
			->findCount()
			->getData();
		$cnt_evening = $pjBookingModel
			->reset()
			->where('t1.room_id', $room_id)
			->where("(t1.start_date='$date')")
			->where("(t1.book_by='evening' OR t1.book_by='afternoonevening')")
			->where("t1.status <>", 'cancelled')
			->findCount()
			->getData();
		if($cnt_morning > 0 && $cnt_afternoon > 0 && $cnt_evening > 0)
		{
			return true;
		}else{
			return false;
		}
	}
	public static function checkDateOff($date, $foreign_id)
	{
		$pjDateModel = pjDateModel::factory();
		$date_arr = $pjDateModel->getDailyWorkingTime($foreign_id, $date);
		if ($date_arr === false)
		{
			$pjWorkingTimeModel = pjWorkingTimeModel::factory();
			$wt_data = $pjWorkingTimeModel->getWorkingTime($foreign_id);
			$wt_arr = $pjWorkingTimeModel->filterDate($wt_data, $date);
			if (empty($wt_arr))
			{
				return true;
			}
		} else {
			if (count($date_arr) === 0)
			{
				return true;
			}
		}
		return false;
	}
	public static function checkDateOffInRange($start_date, $end_date, $foreign_id)
	{
		$start_date_ts = strtotime($start_date);
		$end_date_ts = strtotime($end_date);
		
		$pjDateModel = pjDateModel::factory();
		$date_off_arr = $pjDateModel->getRangeWorkingTime($foreign_id, $start_date, $end_date);
			
		for($i = $start_date_ts; $i <= $end_date_ts; $i += 86400)
		{
			$date_iso = date('Y-m-d', $i);
			if(isset($date_off_arr[$date_iso]) && isset($date_off_arr[$date_iso]['is_dayoff']) && $date_off_arr[$date_iso]['is_dayoff'] == 'T')
			{
				return true;
			}
		}
		$pjWorkingTimeModel = pjWorkingTimeModel::factory();
		$date_off_arr = $pjWorkingTimeModel->getDaysOff($foreign_id);
		if(!empty($date_off_arr))
		{
			for($i = $start_date_ts; $i <= $end_date_ts; $i += 86400)
			{
				$day_of_week = strtolower(date('l', $i));
				if(array_key_exists($day_of_week, $date_off_arr))
				{
					return true;
				}
			}
		}
		return false;
	}
	public static function checkBookingsInRange($room_id, $start_date, $end_date, $id)
	{
		$where_str = '';
		if(isset($id) && (int) $id > 0)
		{
			$where_str = " AND (t1.id <> '".$id."')";
		}
		$cnt_bookings = pjBookingModel::factory()
			->where('t1.room_id', $room_id)
			->where("(((t1.start_date BETWEEN '$start_date' AND '$end_date') OR (t1.end_date BETWEEN '$start_date' AND '$end_date') OR ('$start_date' BETWEEN t1.start_date AND t1.end_date) OR ('$end_date' BETWEEN t1.start_date AND t1.end_date))$where_str)")
			->where("t1.status <>", 'cancelled')
			->findCount()
			->getData();
		if($cnt_bookings > 0)
		{
			return true;
		}else{
			return false;
		}
	}
	public static function getDuration($room_id, $start_date, $id, $locale_id, $foreign_id, $option_arr)
	{
		$arr = pjRoomModel::factory()
			->select('t1.*, t2.content as title')
			->join('pjMultiLang', "t2.model='pjRoom' AND t2.foreign_id=t1.id AND t2.field='title' AND t2.locale='".$locale_id."'", 'left outer')
			->find($room_id)->getData();
		$layout_arr = pjRoomLayoutModel::factory()
			->select('t1.*, t3.image, t2.content as title')
			->join('pjMultiLang', "t2.model='pjLayout' AND t2.foreign_id=t1.layout_id AND t2.field='title' AND t2.locale='".$locale_id."'", 'left outer')
			->join('pjLayout', "t3.id=t1.layout_id", 'left outer')
			->where('t1.room_id', $room_id)
			->findAll()
			->getData();
		
		$from_to_arr = array();
		$halfday_morning = 0;
		$halfday_afternoon = 0;
		$halfday_evening = 0;
		$hourly_morning = 0;
		$hourly_afternoon = 0;
		$hourly_evening = 0;
		$full_day_booked = 0;
		$multi_day_booked = 0;
		$morning_arr = array();
		$afternoon_arr = array();
		$evening_arr = array();
		$combo_morningafternoon_available = 0;
		$combo_afternoonevening_available = 0;
		
		if($start_date != '')
		{
			$pjDateModel = pjDateModel::factory();
			$date_arr = $pjDateModel->getDailyWorkingTime($foreign_id, $start_date);
			
			if ($date_arr === false)
			{
				$pjWorkingTimeModel = pjWorkingTimeModel::factory();
				$wt_data = $pjWorkingTimeModel->getWorkingTime($foreign_id);
				$wt_arr = $pjWorkingTimeModel->filterDate($wt_data, $start_date);
				
				if (!empty($wt_arr))
				{
					$start_ts = $wt_arr['start_ts'];
					$end_ts = $wt_arr['end_ts'];
					$morning_arr['start_ts'] = $wt_arr['morning_start_ts'];
					$morning_arr['end_ts'] = $wt_arr['morning_end_ts'];
					$afternoon_arr['start_ts'] = $wt_arr['afternoon_start_ts'];
					$afternoon_arr['end_ts'] = $wt_arr['afternoon_end_ts'];
					if (isset($wt_arr['evening_start_ts']) && isset($wt_arr['evening_end_ts']))
					{
						$evening_arr['start_ts'] = $wt_arr['evening_start_ts'];
						$evening_arr['end_ts'] = $wt_arr['evening_end_ts'];
					}
				}
			} else {
				if (count($date_arr) > 0)
				{
					$start_ts = $date_arr['start_ts'];
					$end_ts = $date_arr['end_ts'];
					$morning_arr['start_ts'] = $date_arr['morning_start_ts'];
					$morning_arr['end_ts'] = $date_arr['morning_end_ts'];
					$afternoon_arr['start_ts'] = $date_arr['afternoon_start_ts'];
					$afternoon_arr['end_ts'] = $date_arr['afternoon_end_ts'];
					if (isset($date_arr['evening_start_ts']) && isset($date_arr['evening_end_ts']))
					{
						$evening_arr['start_ts'] = $date_arr['evening_start_ts'];
						$evening_arr['end_ts'] = $date_arr['evening_end_ts'];
					}
				}
			}
			
			$pjBookingSlotModel = pjBookingSlotModel::factory();
			$pjBookingModel = pjBookingModel::factory();
			
			
			if(isset($id) && (int) $id > 0)
			{
				$pjBookingModel->where('t1.id <>', $id);
			}
			$halfday_morning = $pjBookingModel
				->where("(t1.start_date='$start_date')")
				->where('t1.status <>', 'cancelled')
				->where("(t1.book_by='morning' OR t1.book_by='morningafternoon')")
				->where('t1.room_id', $room_id)
				->findCount()->getData();
			
			$pjBookingModel->reset();
			if(isset($id) && (int) $id > 0)
			{
				$pjBookingModel->where('t1.id <>', $id);
			}
			if (isset($morning_arr['start_ts']) && isset($morning_arr['end_ts']))
			{
				$hourly_morning = $pjBookingModel
					->where("(t1.start_date='$start_date')")
					->where('t1.status <>', 'cancelled')
					->where("(t1.book_by='hour' AND t1.id IN(SELECT `TBS`.`booking_id` FROM `".$pjBookingSlotModel->getTable()."` AS `TBS` WHERE `TBS`.start_ts >=".(int) $morning_arr['start_ts']." AND `TBS`.`start_ts`<".(int) $morning_arr['end_ts']." ))")
					->where('t1.room_id', $room_id)
					->findCount()->getData();
			}
			
			$pjBookingModel->reset();
			if(isset($id) && (int) $id > 0)
			{
				$pjBookingModel->where('t1.id <>', $id);
			}
			$halfday_afternoon = $pjBookingModel
				->where("(t1.start_date='$start_date')")
				->where('t1.status <>', 'cancelled')
				->where("(t1.book_by='afternoon' OR t1.book_by='morningafternoon' OR t1.book_by='afternoonevening')")
				->where('t1.room_id', $room_id)
				->findCount()->getData();
			$pjBookingModel->reset();
			if(isset($id) && (int) $id > 0)
			{
				$pjBookingModel->where('t1.id <>', $id);
			}
			if (isset($afternoon_arr['start_ts']) && isset($afternoon_arr['end_ts']))
			{
				$hourly_afternoon = $pjBookingModel
					->where("(t1.start_date='$start_date')")
					->where('t1.status <>', 'cancelled')
					->where("(t1.book_by='hour' AND t1.id IN(SELECT `TBS`.`booking_id` FROM `".$pjBookingSlotModel->getTable()."` AS `TBS` WHERE `TBS`.start_ts >=".(int) $afternoon_arr['start_ts']." AND `TBS`.`start_ts`<".(int) $afternoon_arr['end_ts']." ))")
					->where('t1.room_id', $room_id)
					->findCount()->getData();
			}
			
			$pjBookingModel->reset();
			if(isset($id) && (int) $id > 0)
			{
				$pjBookingModel->where('t1.id <>', $id);
			}
			$halfday_evening = $pjBookingModel
				->where("(t1.start_date='$start_date')")
				->where('t1.status <>', 'cancelled')
				->where("(t1.book_by='evening' OR t1.book_by='afternoonevening')")
				->where('t1.room_id', $room_id)
				->findCount()->getData();

			if (isset($evening_arr['start_ts']) && isset($evening_arr['end_ts']))
			{
				$pjBookingModel->reset();
				if(isset($id) && (int) $id > 0)
				{
					$pjBookingModel->where('t1.id <>', $id);
				}
				$hourly_evening = $pjBookingModel
					->where("(t1.start_date='$start_date')")
					->where('t1.status <>', 'cancelled')
					->where("(t1.book_by='hour' AND t1.id IN(SELECT `TBS`.`booking_id` FROM `".$pjBookingSlotModel->getTable()."` AS `TBS` WHERE `TBS`.start_ts >=".(int) $evening_arr['start_ts']." AND `TBS`.`start_ts`<".(int) $evening_arr['end_ts']." ))")
					->where('t1.room_id', $room_id)
					->findCount()->getData();
			}

			if($halfday_morning > 0 || $halfday_afternoon > 0 || $halfday_evening > 0 || $hourly_morning > 0 || $hourly_afternoon > 0 || $hourly_evening > 0)
			{
				$full_day_booked = 1;
			}
			$pjBookingModel->reset();
			if(isset($id) && (int) $id > 0)
			{
				$pjBookingModel->where('t1.id <>', $id);
			}
			$check_selected_date = $pjBookingModel
				->where("('$start_date' BETWEEN `start_date` AND `end_date`)")
				->where("t1.book_by", 'multiday')
				->where('t1.status <>', 'cancelled')
				->where('t1.room_id', $room_id)
				->findCount()->getData();
			if($check_selected_date > 0)
			{
				$full_day_booked = 1;
				$halfday_morning = 1;
				$halfday_afternoon = 1;
				$halfday_evening = 1;
				$hourly_morning = 1;
				$hourly_afternoon = 1;
				$hourly_evening = 1;
			}
			
			$pjBookingModel->reset();
			if(isset($id) && (int) $id > 0)
			{
				$pjBookingModel->where('t1.id <>', $id);
			}
			$multi_day_booked = $pjBookingModel
				->where("(t1.start_date='$start_date')")
				->where('t1.status <>', 'cancelled')
				->where('t1.room_id', $room_id)
				->findCount()->getData();

			$combo_morningafternoon_available = ($halfday_morning == 0 && $halfday_afternoon == 0 && $hourly_morning == 0 && $hourly_afternoon == 0) ? 1 : 0;
			$combo_afternoonevening_available = ($halfday_afternoon == 0 && $halfday_evening == 0 && $hourly_afternoon == 0 && $hourly_evening == 0) ? 1 : 0;

			if(isset($start_ts) && isset($end_ts) && ($full_day_booked == 0 || 
					($full_day_booked == 1 && ($halfday_morning == 0 || $halfday_afternoon == 0 || $halfday_evening == 0) ) ||
					($full_day_booked == 1 && ($hourly_morning == 0 || $hourly_afternoon == 0 || $hourly_evening == 0) ) ))
			{
				$pjBookingModel->reset();
				$date_iso = date('Y-m-d', $start_ts);
				$where_str = '';
				$where_str .= " AND `TB`.room_id = " . $room_id;
				if(isset($id) && (int) $id > 0)
				{
					$where_str .= " AND `TB`.id <> " . $id;
					$pjBookingModel->where('t1.id <>', $id);
				}
				$bs_arr = pjBookingSlotModel::factory()
					->where("(DATE(t1.start_iso) = '$date_iso')")
					->where("t1.booking_id IN(SELECT `TB`.id FROM `".pjBookingModel::factory()->getTable()."` AS `TB` WHERE `TB`.status <> 'cancelled'$where_str) ")
					->findAll()->getDataPair(null, 'start_ts');
				$temp_bs_arr = array();
				if(!empty($bs_arr))
				{
					foreach($bs_arr as $k => $ts)
					{
						$temp_bs_arr[] = date($option_arr['o_time_format'], $ts);
					}
				}
				
				$halfday_arr = array();
				if($halfday_morning > 0)
				{
					if(isset($morning_arr['start_ts']) && isset($morning_arr['end_ts']))
					{
						for($i = $morning_arr['start_ts']; $i < $morning_arr['end_ts']; $i += 3600)
						{
							$halfday_arr[] = date($option_arr['o_time_format'], $i);
						}
					}
				}
				if($halfday_afternoon > 0)
				{
					if(isset($afternoon_arr['start_ts']) && isset($afternoon_arr['end_ts']))
					{
						for($i = $afternoon_arr['start_ts']; $i < $afternoon_arr['end_ts']; $i += 3600)
						{
							$halfday_arr[] = date($option_arr['o_time_format'], $i);
						}
					}
				}
				if($halfday_evening > 0)
				{
					if(isset($evening_arr['start_ts']) && isset($evening_arr['end_ts']))
					{
						for($i = $evening_arr['start_ts']; $i < $evening_arr['end_ts']; $i += 3600)
						{
							$halfday_arr[] = date($option_arr['o_time_format'], $i);
						}
					}
				}
				
				for($i = $start_ts; $i < $end_ts; $i += 3600)
				{
					$next_ts = $i + 3600;
					$time_iso = date($option_arr['o_time_format'], $i);
					$next_iso = date($option_arr['o_time_format'], $next_ts);
					if(!in_array($time_iso, $halfday_arr))
					{
						if(!empty($temp_bs_arr))
						{
							if(!in_array($time_iso, $temp_bs_arr))
							{
								$from_to_arr[$i . '|' . $next_ts] = $time_iso . ' - ' . $next_iso;
							}
						}else{
							$from_to_arr[$i . '|' . $next_ts] = $time_iso . ' - ' . $next_iso;
						}
					}
				}
			}
		}
		return compact('arr', 'layout_arr', 'from_to_arr', 'halfday_morning', 'halfday_afternoon', 'halfday_evening', 'hourly_morning', 'hourly_afternoon', 'hourly_evening', 'morning_arr', 'afternoon_arr', 'evening_arr', 'full_day_booked', 'multi_day_booked', 'combo_morningafternoon_available', 'combo_afternoonevening_available');
	}
	public static function getPrices($start_date, $end_date, $room_id, $book_by, $slots, $equipment_id, $units, $food_drink_id, $people, $foreign_id, $option_arr)
	{
		$start_date_ts = strtotime($start_date);
		$end_date_ts = strtotime($end_date);
		
		$subtotal = 0;
		$total = 0;
		$tax = 0;
		$deposit = 0;
		$room_price = 0;
		$equipment_price = 0;
		$food_drink_price = 0;
			
		$seconds = abs($end_date_ts - $start_date_ts);
		$days = floor($seconds / 86400) + 1;
		$hours = 0;
			
		$room_arr = pjRoomModel::factory()->find($room_id)->getData();
		if($book_by == 'multiday')
		{
			$room_price = (float) $room_arr['price_per_day'] * $days;
		
			$pjDateModel = pjDateModel::factory();
			$pjWorkingTimeModel = pjWorkingTimeModel::factory();
			$date_arr = $pjDateModel->getRangeWorkingTime($foreign_id, $start_date, $end_date);
			$wt_data = $pjWorkingTimeModel->getWorkingTime($foreign_id);
		
			for($i = $start_date_ts; $i <= $end_date_ts; $i += 86400)
			{
				$date_iso = date('Y-m-d', $i);
				if(isset($date_arr[$date_iso]) && isset($date_arr[$date_iso]['start_time']) && isset($date_arr[$date_iso]['end_time']))
				{
					$hours += floor(abs($date_arr[$date_iso]['end_time'] - $date_arr[$date_iso]['start_time']) / 3600);
				}else{
					$day_of_week = strtolower(date('l', $i));
					$week_day_from_ts = strtotime($date_iso . ' ' . $wt_data[$day_of_week . '_from']);
					$week_day_to_ts = strtotime($date_iso . ' ' . $wt_data[$day_of_week . '_to']);
					$hours += floor(abs($week_day_to_ts - $week_day_from_ts) / 3600);
				}
			}
		}else if($book_by == 'morning'){
			$room_price = (float) $room_arr['price_half_day'];
		
			$pjDateModel = pjDateModel::factory();
			$date_arr = $pjDateModel->getDailyWorkingTime($foreign_id, $start_date);
		
			if ($date_arr === false)
			{
				$pjWorkingTimeModel = pjWorkingTimeModel::factory();
				$wt_data = $pjWorkingTimeModel->getWorkingTime($foreign_id);
				$wt_arr = $pjWorkingTimeModel->filterDate($wt_data, $start_date);
				if (!empty($wt_arr))
				{
					$hours = floor(abs($wt_arr['morning_end_ts'] - $wt_arr['morning_start_ts']) / 3600);
				}
			} else {
				if (count($date_arr) > 0)
				{
					$hours = floor(abs($date_arr['morning_end_ts'] - $date_arr['morning_start_ts']) / 3600);
				}
			}
		}else if($book_by == 'afternoon'){
			$room_price = (float) $room_arr['price_half_day'];
		
			$pjDateModel = pjDateModel::factory();
			$date_arr = $pjDateModel->getDailyWorkingTime($foreign_id, $start_date);
			if ($date_arr === false)
			{
				$pjWorkingTimeModel = pjWorkingTimeModel::factory();
				$wt_data = $pjWorkingTimeModel->getWorkingTime($foreign_id);
				$wt_arr = $pjWorkingTimeModel->filterDate($wt_data, $start_date);
				if (!empty($wt_arr))
				{
					$hours = floor(abs($wt_arr['afternoon_end_ts'] - $wt_arr['afternoon_start_ts']) / 3600);
				}
			} else {
				if (count($date_arr) > 0)
				{
					$hours = floor(abs($date_arr['afternoon_end_ts'] - $date_arr['afternoon_start_ts']) / 3600);
				}
			}
		}else if($book_by == 'evening'){
			$room_price = (float) $room_arr['price_half_day'];

			$pjDateModel = pjDateModel::factory();
			$date_arr = $pjDateModel->getDailyWorkingTime($foreign_id, $start_date);
			if ($date_arr === false)
			{
				$pjWorkingTimeModel = pjWorkingTimeModel::factory();
				$wt_data = $pjWorkingTimeModel->getWorkingTime($foreign_id);
				$wt_arr = $pjWorkingTimeModel->filterDate($wt_data, $start_date);
				if (!empty($wt_arr) && isset($wt_arr['evening_start_ts'], $wt_arr['evening_end_ts']))
				{
					$hours = floor(abs($wt_arr['evening_end_ts'] - $wt_arr['evening_start_ts']) / 3600);
				}
			} else {
				if (count($date_arr) > 0 && isset($date_arr['evening_start_ts'], $date_arr['evening_end_ts']))
				{
					$hours = floor(abs($date_arr['evening_end_ts'] - $date_arr['evening_start_ts']) / 3600);
				}
			}
		}else if($book_by == 'morningafternoon'){
			$room_price = (float) $room_arr['price_half_day'] * 2;

			$pjDateModel = pjDateModel::factory();
			$date_arr = $pjDateModel->getDailyWorkingTime($foreign_id, $start_date);
			if ($date_arr === false)
			{
				$pjWorkingTimeModel = pjWorkingTimeModel::factory();
				$wt_data = $pjWorkingTimeModel->getWorkingTime($foreign_id);
				$wt_arr = $pjWorkingTimeModel->filterDate($wt_data, $start_date);
				if (!empty($wt_arr))
				{
					$hours = floor(abs($wt_arr['morning_end_ts'] - $wt_arr['morning_start_ts']) / 3600)
						+ floor(abs($wt_arr['afternoon_end_ts'] - $wt_arr['afternoon_start_ts']) / 3600);
				}
			} else {
				if (count($date_arr) > 0)
				{
					$hours = floor(abs($date_arr['morning_end_ts'] - $date_arr['morning_start_ts']) / 3600)
						+ floor(abs($date_arr['afternoon_end_ts'] - $date_arr['afternoon_start_ts']) / 3600);
				}
			}
		}else if($book_by == 'afternoonevening'){
			$room_price = (float) $room_arr['price_half_day'] * 2;

			$pjDateModel = pjDateModel::factory();
			$date_arr = $pjDateModel->getDailyWorkingTime($foreign_id, $start_date);
			if ($date_arr === false)
			{
				$pjWorkingTimeModel = pjWorkingTimeModel::factory();
				$wt_data = $pjWorkingTimeModel->getWorkingTime($foreign_id);
				$wt_arr = $pjWorkingTimeModel->filterDate($wt_data, $start_date);
				if (!empty($wt_arr))
				{
					$hours = floor(abs($wt_arr['afternoon_end_ts'] - $wt_arr['afternoon_start_ts']) / 3600);
					if (isset($wt_arr['evening_start_ts'], $wt_arr['evening_end_ts']))
					{
						$hours += floor(abs($wt_arr['evening_end_ts'] - $wt_arr['evening_start_ts']) / 3600);
					}
				}
			} else {
				if (count($date_arr) > 0)
				{
					$hours = floor(abs($date_arr['afternoon_end_ts'] - $date_arr['afternoon_start_ts']) / 3600);
					if (isset($date_arr['evening_start_ts'], $date_arr['evening_end_ts']))
					{
						$hours += floor(abs($date_arr['evening_end_ts'] - $date_arr['evening_start_ts']) / 3600);
					}
				}
			}
		}else{
			if(is_array($slots) && count($slots) > 0)
			{
				$room_price = ((float) $room_arr['price_per_hour']) * count($slots);
			}
			$hours = count($slots);
		}
			
		if($room_price > 0 )
		{
			if(is_array($equipment_id) && count($equipment_id) > 0)
			{
					
				foreach($equipment_id as $id => $pair)
				{
					list($price, $per) = explode("|", $pair);
					if($per == 'booking')
					{
						$equipment_price += $price * (isset($units[$id]) ? (int) $units[$id] : 0);
					}else{
						$equipment_price += $price * $hours * (isset($units[$id]) ? (int) $units[$id] : 0);
					}
				}
			}
			if(is_array($food_drink_id) && count($food_drink_id) > 0)
			{
				foreach($food_drink_id as $id => $price)
				{
					$food_drink_price += $price * (isset($people[$id]) ? (int) $people[$id] : 0);
				}
			}
		}
		$subtotal = $room_price + $equipment_price + $food_drink_price;
		$tax = $subtotal * $option_arr['o_tax_payment'] / 100;
		$total = $subtotal + $tax;
		$deposit = $total * $option_arr['o_deposit_payment'] / 100;
		
		return compact('room_price', 'equipment_price', 'food_drink_price', 'subtotal', 'tax', 'total', 'deposit', 'days', 'hours');
	}
}
?>