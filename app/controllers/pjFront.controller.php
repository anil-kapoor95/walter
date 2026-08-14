<?php
if (!defined("ROOT_PATH"))
{
	header("HTTP/1.1 403 Forbidden");
	exit;
}
class pjFront extends pjAppController
{	
	public $defaultCaptcha = 'pjMRBS_Captcha';
	
	public $defaultLocale = 'pjMRBS_LocaleId';
	
	public $defaultFrontUser = 'pjMRBS_User';
	
	public $defaultLangMenu = 'pjMRBS_LangMenu';
	
	public $defaultStore = 'pjMRBS_Store';
	
	public $defaultForm = 'pjMRBS_Form';
	
	public function __construct()
	{
		$this->setLayout('pjActionFront');
		
		self::allowCORS();
	}
	
	public function _get($key)
	{
		if ($this->_is($key))
		{
			return $_SESSION[$this->defaultStore][$key];
		}
		return false;
	}
	
	public function _is($key)
	{
		return isset($_SESSION[$this->defaultStore]) && isset($_SESSION[$this->defaultStore][$key]);
	}
	
	public function _set($key, $value)
	{
		$_SESSION[$this->defaultStore][$key] = $value;
		return $this;
	}
	
	public function _unset($key)
	{
		if ($this->_is($key))
		{
			unset($_SESSION[$this->defaultStore][$key]);
		}
	}
	
	public function afterFilter()
	{		
		if (!isset($_GET['hide']) || (isset($_GET['hide']) && (int) $_GET['hide'] !== 1) &&
			in_array($_GET['action'], array('pjActionCheckout', 'pjActionPreview')))
		{
			$locale_arr = pjLocaleModel::factory()->select('t1.*, t2.file, t2.title')
				->join('pjLocaleLanguage', 't2.iso=t1.language_iso', 'left')
				->where('t2.file IS NOT NULL')
				->orderBy('t1.sort ASC')->findAll()->getData();
			
			$this->set('locale_arr', $locale_arr);
		}
		
		$_terms_conditions = pjMultiLangModel::factory()->select('t1.*')
			->where('t1.model','pjOption')
			->where('t1.locale', $this->getLocaleId())
			->where('t1.field', 'o_terms')
			->limit(0, 1)
			->findAll()->getData();
		$terms_conditions = '';
		if(!empty($_terms_conditions))
		{
			$terms_conditions = $_terms_conditions[0]['content'];
		}
		$this->set('terms_conditions', $terms_conditions);
	}
	
	public function beforeFilter()
	{
		$OptionModel = pjOptionModel::factory();
		$this->option_arr = $OptionModel->getPairs($this->getForeignId());
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
	
	public function beforeRender()
	{
		if (isset($_GET['iframe']))
		{
			$this->setLayout('pjActionIframe');
		}
	}
	
	public function pjActionLocale()
	{
		$this->setAjax(true);
	
		if ($this->isXHR())
		{
			if (isset($_GET['locale_id']))
			{
				$this->pjActionSetLocale($_GET['locale_id']);
				
				$this->loadSetFields(true);
				
				$day_names = __('day_names', true);
				ksort($day_names, SORT_NUMERIC);
				
				$months = __('months', true);
				ksort($months, SORT_NUMERIC);
				
				pjAppController::jsonResponse(array('status' => 'OK', 'code' => 200, 'text' => 'Locale have been changed.', 'opts' => array(
					'day_names' => array_values($day_names),
					'month_names' => array_values($months)
				)));
			}
		}
		exit;
	}
	private function pjActionSetLocale($locale)
	{
		if ((int) $locale > 0)
		{
			$_SESSION[$this->defaultLocale] = (int) $locale;
		}
		return $this;
	}
	
	public function pjActionGetLocale()
	{
		return isset($_SESSION[$this->defaultLocale]) && (int) $_SESSION[$this->defaultLocale] > 0 ? (int) $_SESSION[$this->defaultLocale] : FALSE;
	}
	
	public function pjActionCaptcha()
	{
		$this->setAjax(true);
		$Captcha = new pjCaptcha(PJ_INSTALL_PATH . 'app/web/obj/Anorexia.ttf', $this->defaultCaptcha, 6);
		$Captcha->setImage(PJ_INSTALL_PATH . 'app/web/img/button.png')->init(isset($_GET['rand']) ? $_GET['rand'] : null);
	}

	public function pjActionCheckCaptcha()
	{
		$this->setAjax(true);
		if (!isset($_GET['captcha']) || empty($_GET['captcha']) || strtoupper($_GET['captcha']) != $_SESSION[$this->defaultCaptcha]){
			echo 'false';
		}else{
			echo 'true';
		}
		exit;
	}
	
	public function pjActionLoadCss()
	{
		$dm = new pjDependencyManager(PJ_INSTALL_PATH, PJ_THIRD_PARTY_PATH);
		$dm->load(PJ_CONFIG_PATH . 'dependencies.php')->resolve();
	
		$theme = $this->option_arr['o_theme'];
		$fonts = $this->option_arr['o_theme'];
		if(isset($_GET['theme']) && in_array($_GET['theme'], array('theme1', 'theme2', 'theme3', 'theme4', 'theme5', 'theme6', 'theme7', 'theme8', 'theme9', 'theme10')))
		{
			$theme = $_GET['theme'];
			$fonts = $_GET['theme'];
		}
		$arr = array(
				array('file' => 'bootstrap-datetimepicker.min.css', 'path' => $dm->getPath('pj_bootstrap_datetimepicker')),
				array('file' => 'style.css', 'path' => PJ_CSS_PATH),
				array('file' => "$fonts.css", 'path' => PJ_CSS_PATH . "fonts/"),
				array('file' => "$theme.css", 'path' => PJ_CSS_PATH . "themes/"),
				array('file' => 'transitions.css', 'path' => PJ_CSS_PATH)
		);
		header("Content-Type: text/css; charset=utf-8");
		foreach ($arr as $item)
		{
			ob_start();
			@readfile($item['path'] . $item['file']);
			$string = ob_get_contents();
			ob_end_clean();
				
			if ($string !== FALSE)
			{
				echo str_replace(
						array('../fonts/glyphicons', "pjWrapper"),
						array(
								PJ_INSTALL_URL . PJ_FRAMEWORK_LIBS_PATH . 'pj/fonts/glyphicons',
								"pjWrapperMeetingRoomBooking_" . $theme
						),
						$string
				) . "\n";
			}
		}
		exit;
	}
	
	public function pjActionLoad()
	{
		ob_start();
		header("Content-Type: text/javascript; charset=utf-8");
	}
	public function pjActionSetRoom()
	{
		$this->setAjax(true);
	
		if($this->isXHR())
		{
			if (isset($_GET['room_id']) && (int) $_GET['room_id'] > 0)
			{
				$this->_set('room_id', (int) $_GET['room_id']);
				pjAppController::jsonResponse(array('status' => 'OK', 'code' => 200, 'text' => ''));
			}else{
				pjAppController::jsonResponse(array('status' => 'ERR', 'code' => 100, 'text' => ''));
			}
			exit;
		}
	}
	public function pjActionSetDate()
	{
		$this->setAjax(true);
	
		if($this->isXHR())
		{
			if (isset($_GET['date']) && !empty($_GET['date']))
			{
				$this->_set('start_date', pjUtil::formatDate($_GET['date'], $this->option_arr['o_date_format']));
				$this->_set('end_date', date('Y-m-d', strtotime($this->_get('start_date')) + 86400));
				pjAppController::jsonResponse(array('status' => 'OK', 'code' => 200, 'text' => ''));
			}else{
				pjAppController::jsonResponse(array('status' => 'ERR', 'code' => 100, 'text' => ''));
			}
			exit;
		}
	}
	public function pjActionSetBookBy()
	{
		$this->setAjax(true);
	
		if($this->isXHR())
		{
			if (isset($_GET['book_by']) && in_array($_GET['book_by'], array('multiday', 'hour', 'morning', 'afternoon') ))
			{
				$this->_set('book_by', $_GET['book_by']);
				pjAppController::jsonResponse(array('status' => 'OK', 'code' => 200, 'text' => ''));
			}else{
				pjAppController::jsonResponse(array('status' => 'ERR', 'code' => 100, 'text' => ''));
			}
			exit;
		}
	}
	public function pjActionSetEndDate()
	{
		$this->setAjax(true);
	
		if($this->isXHR())
		{
			if (isset($_GET['end_date']) && !empty($_GET['end_date']))
			{
				$this->_set('end_date', pjUtil::formatDate($_GET['end_date'], $this->option_arr['o_date_format']));
				
				$has_date_off = pjAppController::checkDateOffInRange($this->_get('start_date'), $this->_get('end_date'), $this->getForeignId());
				if($has_date_off == true)
				{
					pjAppController::jsonResponse(array('status' => 'ERR', 'code' => 101, 'text' => __('front_date_off_in_range', true)));
				}else{
					$has_bookings = pjAppController::checkBookingsInRange($this->_get('room_id'), $this->_get('start_date'), $this->_get('end_date'), null);
					if($has_bookings == true)
					{
						pjAppController::jsonResponse(array('status' => 'ERR', 'code' => 102, 'text' => __('front_date_in_range_booked', true)));
					}
				}
				pjAppController::jsonResponse(array('status' => 'OK', 'code' => 200, 'text' => ''));
			}else{
				pjAppController::jsonResponse(array('status' => 'ERR', 'code' => 100, 'text' => __('front_params_missing', true)));
			}
			exit;
		}
	}
	public function pjActionCheckAvail()
	{
		$this->setAjax(true);
	
		if($this->isXHR())
		{
			if($this->_get('book_by') == 'multiday')
			{
				$has_date_off = pjAppController::checkDateOffInRange($this->_get('start_date'), $this->_get('end_date'), $this->getForeignId());
				if($has_date_off == true)
				{
					pjAppController::jsonResponse(array('status' => 'ERR', 'code' => 101, 'text' => __('front_date_off_in_range', true)));
				}else{
					$has_bookings = pjAppController::checkBookingsInRange($this->_get('room_id'), $this->_get('start_date'), $this->_get('end_date'), null);
					if($has_bookings == true)
					{
						pjAppController::jsonResponse(array('status' => 'ERR', 'code' => 102, 'text' => __('front_date_in_range_booked', true)));
					}
				}
			}
			pjAppController::jsonResponse(array('status' => 'OK', 'code' => 200, 'text' => ''));
			exit;
		}
	}
	public function pjActionSaveBookForm()
	{
		$this->setAjax(true);
	
		if($this->isXHR())
		{
			$this->_set('attendees', $_POST['attendees']);
			if($this->_is('slots'))
			{
				$this->_unset('slots');
			}
			if($this->_get('book_by') == 'hour')
			{
				$this->_set('slots', $_POST['slots']);
			}
			pjAppController::jsonResponse(array('status' => 'OK', 'code' => 200, 'text' => ''));
			exit;
		}
	}
	public function pjActionSaveLayout()
	{
		$this->setAjax(true);
	
		if($this->isXHR())
		{
			if($this->_is('layout_id'))
			{
				$this->_unset('layout_id');
			}
			$this->_set('layout_id', $_POST['layout_id']);
			
			if($this->_is('equipment_id'))
			{
				$this->_unset('equipment_id');
			}
			if($this->_is('units'))
			{
				$this->_unset('units');
			}
			if(isset($_POST['equipment_id']) && is_array($_POST['equipment_id']) && count($_POST['equipment_id']))
			{
				$this->_set('equipment_id', $_POST['equipment_id']);
				$this->_set('units', $_POST['units']);
			}			
			pjAppController::jsonResponse(array('status' => 'OK', 'code' => 200, 'text' => ''));
			exit;
		}
	}
	public function pjActionSaveFoodDrinks()
	{
		$this->setAjax(true);
	
		if($this->isXHR())
		{
			if($this->_is('food_drink_id'))
			{
				$this->_unset('food_drink_id');
			}
				
			if($this->_is('people'))
			{
				$this->_unset('people');
			}
			if(isset($_POST['food_drink_id']) && is_array($_POST['food_drink_id']) && count($_POST['food_drink_id']))
			{
				$this->_set('food_drink_id', $_POST['food_drink_id']);
				$this->_set('people', $_POST['people']);
			}
			pjAppController::jsonResponse(array('status' => 'OK', 'code' => 200, 'text' => ''));
			exit;
		}
	}
	
	public function pjActionRooms()
	{
		$this->setAjax(true);
	
		if($this->isXHR())
		{
			$pjRoomModel = pjRoomModel::factory()
				->join('pjMultiLang', "t2.foreign_id = t1.id AND t2.model = 'pjRoom' AND t2.locale = '".$this->getLocaleId()."' AND t2.field = 'title'", 'left')
				->join('pjMultiLang', "t3.foreign_id = t1.id AND t3.model = 'pjRoom' AND t3.locale = '".$this->getLocaleId()."' AND t3.field = 'description'", 'left');
			$pjRoomModel->where('status', 'T');
			$column = 'capacity';
			$direction = 'ASC';
			if (isset($_GET['direction']) && isset($_GET['column']) && in_array(strtoupper($_GET['direction']), array('ASC', 'DESC')))
			{
				$column = $_GET['column'];
				if($column == 'price_per_day' || $column == 'price_per_hour')
				{
					$column = "ISNULL($column), $column";
				}
				$direction = strtoupper($_GET['direction']);
			}
			
			$total = $pjRoomModel->findCount()->getData();
			$rowCount = isset($_GET['rowCount']) && (int) $_GET['rowCount'] > 0 ? (int) $_GET['rowCount'] : $this->option_arr['o_items_per_page'];
			$pages = ceil($total / $rowCount);
			$page = isset($_GET['page']) && (int) $_GET['page'] > 0 ? intval($_GET['page']) : 1;
			$offset = ((int) $page - 1) * $rowCount;
			if ($page > $pages)
			{
				$page = $pages;
			}
			
			$arr = $pjRoomModel
				->select("t1.*, t2.content as title, t3.content as description")
				->orderBy("$column $direction")
				->limit($rowCount, $offset)
				->findAll()
				->getData();
			
			unset($_SESSION[$this->defaultStore]);
			
			$this->set('arr', $arr);
			$this->set('paginator', array('pages' => $pages, 'total' => $total));
		}
	}
	public function pjActionBook()
	{
		$this->setAjax(true);
	
		if($this->isXHR())
		{
			if (isset($_SESSION[$this->defaultStore]) &&
					count($_SESSION[$this->defaultStore]) > 0 &&
					isset($_SESSION[$this->defaultStore]['room_id']))
			{
				$room_id = $_SESSION[$this->defaultStore]['room_id'];
				if(!$this->_is('start_date'))
				{
					$this->_set('start_date', date('Y-m-d', time() + (($this->option_arr['o_days_earlier'] + 1) * 86400)) );
				}
				if(!$this->_is('end_date'))
				{
					$this->_set('end_date', date('Y-m-d', strtotime($this->_get('start_date')) + 86400));
				}
				
				$is_date_off = pjAppController::checkDateOff($this->_get('start_date'), $this->getForeignId());
				$is_both_half = pjAppController::checkBothHalfDay($this->_get('room_id'), $this->_get('start_date'));
				
				if($is_date_off == false && $is_both_half == false)
				{
					$duration_info_arr = pjAppController::getDuration($room_id, $this->_get('start_date'), null, $this->getLocaleId(), $this->getForeignId(), $this->option_arr);
					$find_menu = $this->findMenu($duration_info_arr['layout_arr']);
					
					$this->set('arr', $duration_info_arr['arr']);
					$this->set('layout_arr', $duration_info_arr['layout_arr']);
					$this->set('from_to_arr', $duration_info_arr['from_to_arr']);
					$this->set('full_day_booked', $duration_info_arr['full_day_booked']);
					$this->set('multi_day_booked', $duration_info_arr['multi_day_booked']);
					$this->set('halfday_morning', $duration_info_arr['halfday_morning']);
					$this->set('halfday_afternoon', $duration_info_arr['halfday_afternoon']);
					$this->set('hourly_morning', $duration_info_arr['hourly_morning']);
					$this->set('hourly_afternoon', $duration_info_arr['hourly_afternoon']);
					$this->set('morning_arr', $duration_info_arr['morning_arr']);
					$this->set('afternoon_arr', $duration_info_arr['afternoon_arr']);
					$this->set('find_menu', $find_menu);
				}else{
					$arr = pjRoomModel::factory()
						->select('t1.*, t2.content as title')
						->join('pjMultiLang', "t2.model='pjRoom' AND t2.foreign_id=t1.id AND t2.field='title' AND t2.locale='".$this->getLocaleId()."'", 'left outer')
						->find($room_id)->getData();
					$this->set('arr', $arr);
				}
				
				$range_arr = array('status' => 'OK');
				$has_date_off = pjAppController::checkDateOffInRange($this->_get('start_date'), $this->_get('end_date'), $this->getForeignId());
				if($has_date_off == true)
				{
					$range_arr = array('status' => 'ERR', 'code' => 101, 'text' => __('front_date_off_in_range', true));
				}else{
					$has_bookings = pjAppController::checkBookingsInRange($this->_get('room_id'), $this->_get('start_date'), $this->_get('end_date'), null);
					if($has_bookings == true)
					{
						$range_arr = array('status' => 'ERR', 'code' => 102, 'text' => __('front_date_in_range_booked', true));
					}
				}
				
				$this->set('range_arr', $range_arr);
				$this->set('is_date_off', $is_date_off);
				$this->set('is_both_half', $is_both_half);
				
			}else{
				pjAppController::jsonResponse(array('status' => 'ERR', 'code' => 100, 'text' => ''));
				exit;
			}
		}
	}
	public function pjActionEquipment()
	{
		$this->setAjax(true);
	
		if($this->isXHR())
		{
			if (isset($_SESSION[$this->defaultStore]) &&
					count($_SESSION[$this->defaultStore]) > 0 &&
					isset($_SESSION[$this->defaultStore]['room_id']) &&
					isset($_SESSION[$this->defaultStore]['book_by']))
			{
				$duration_info_arr = pjAppController::getDuration($this->_get('room_id'), $this->_get('start_date'), null, $this->getLocaleId(), $this->getForeignId(), $this->option_arr);
				
				$equipment_arr = pjEquipmentModel::factory()
					->select('t1.*, t2.content as title')
					->join('pjMultiLang', "t2.model='pjEquipment' AND t2.foreign_id=t1.id AND t2.field='title' AND t2.locale='".$this->getLocaleId()."'", 'left outer')
					->orderBy("id ASC")
					->findAll()
					->getData();
				$layout_arr = $duration_info_arr['layout_arr'];
				
				if(!empty($equipment_arr) || !empty($layout_arr))
				{
					$this->set('arr', $duration_info_arr['arr']);
					$this->set('layout_arr', $layout_arr);
					$this->set('equipment_arr', $equipment_arr);
					
					$this->set('find_menu', $this->findMenu($duration_info_arr['layout_arr']));
				}else{
					pjAppController::jsonResponse(array('status' => 'ERR', 'code' => 101, 'text' => 'Next step'));
				}
			}else{
				pjAppController::jsonResponse(array('status' => 'ERR', 'code' => 100, 'text' => ''));
				exit;
			}
		}
	}
	public function pjActionFoodDrinks()
	{
		$this->setAjax(true);
	
		if($this->isXHR())
		{
			if (isset($_SESSION[$this->defaultStore]) &&
					count($_SESSION[$this->defaultStore]) > 0 &&
					isset($_SESSION[$this->defaultStore]['room_id']) &&
					isset($_SESSION[$this->defaultStore]['book_by']))
			{
				$food_drink_arr = pjFoodDrinkModel::factory()
					->select('t1.*, t2.content as title')
					->join('pjMultiLang', "t2.model='pjFoodDrink' AND t2.foreign_id=t1.id AND t2.field='title' AND t2.locale='".$this->getLocaleId()."'", 'left outer')
					->orderBy("id ASC")
					->findAll()
					->getData();

				if(!empty($food_drink_arr))
				{
					$duration_info_arr = pjAppController::getDuration($this->_get('room_id'), $this->_get('start_date'), null, $this->getLocaleId(), $this->getForeignId(), $this->option_arr);
		
					$layout = pjLayoutModel::factory()
						->select('t1.*, t2.content as title')
						->join('pjMultiLang', "t2.model='pjLayout' AND t2.foreign_id=t1.id AND t2.field='title' AND t2.locale='".$this->getLocaleId()."'", 'left outer')
						->find($this->_get('layout_id'))
						->getData();
					
					$selected_equipment_id_arr = isset($_SESSION[$this->defaultStore]['equipment_id']) ? $_SESSION[$this->defaultStore]['equipment_id'] : array();
					if(!empty($selected_equipment_id_arr))
					{
						$equipment_arr = pjEquipmentModel::factory()
							->select('t1.*, t2.content as title')
							->join('pjMultiLang', "t2.model='pjEquipment' AND t2.foreign_id=t1.id AND t2.field='title' AND t2.locale='".$this->getLocaleId()."'", 'left outer')
							->whereIn('t1.id', array_keys($selected_equipment_id_arr))
							->orderBy("id ASC")
							->findAll()
							->getData();
						$this->set('equipment_arr', $equipment_arr);
					}
					
					$this->set('arr', $duration_info_arr['arr']);
					$this->set('layout', $layout);
					$this->set('food_drink_arr', $food_drink_arr);
					$this->set('find_menu', $this->findMenu($duration_info_arr['layout_arr']));
				}else{
					pjAppController::jsonResponse(array('status' => 'ERR', 'code' => 101, 'text' => ''));
					exit;
				}
			}else{
				pjAppController::jsonResponse(array('status' => 'ERR', 'code' => 100, 'text' => ''));
				exit;
			}
		}
	}
	public function pjActionCheckout()
	{
		$this->setAjax(true);
	
		if($this->isXHR())
		{
			if (isset($_SESSION[$this->defaultStore]) &&
					count($_SESSION[$this->defaultStore]) > 0 &&
					isset($_SESSION[$this->defaultStore]['room_id']) &&
					isset($_SESSION[$this->defaultStore]['book_by']))
			{
				if(isset($_POST['mrbs_checkout']))
				{
					if ((int) $this->option_arr['o_bf_include_captcha'] === 3 && (!isset($_POST['captcha']) || !pjCaptcha::validate($_POST['captcha'], $_SESSION[$this->defaultCaptcha]) ))
					{
						pjAppController::jsonResponse(array('status' => 'ERR', 'code' => 101, 'text' => ''));
					}
					
					$_SESSION[$this->defaultForm] = $_POST;
					pjAppController::jsonResponse(array('status' => 'OK', 'code' => 200));
				}else{
					$duration_info_arr = pjAppController::getDuration($this->_get('room_id'), $this->_get('start_date'), null, $this->getLocaleId(), $this->getForeignId(), $this->option_arr);
		
					$layout = pjLayoutModel::factory()
						->select('t1.*, t2.content as title')
						->join('pjMultiLang', "t2.model='pjLayout' AND t2.foreign_id=t1.id AND t2.field='title' AND t2.locale='".$this->getLocaleId()."'", 'left outer')
						->find($this->_get('layout_id'))
						->getData();
		
					$selected_equipment_id_arr = isset($_SESSION[$this->defaultStore]['equipment_id']) ? $_SESSION[$this->defaultStore]['equipment_id'] : array();
					if(!empty($selected_equipment_id_arr))
					{
						$equipment_arr = pjEquipmentModel::factory()
							->select('t1.*, t2.content as title')
							->join('pjMultiLang', "t2.model='pjEquipment' AND t2.foreign_id=t1.id AND t2.field='title' AND t2.locale='".$this->getLocaleId()."'", 'left outer')
							->whereIn('t1.id', array_keys($selected_equipment_id_arr))
							->orderBy("id ASC")
							->findAll()
							->getData();
						$this->set('equipment_arr', $equipment_arr);
					}
					$selected_food_drink_id_arr = isset($_SESSION[$this->defaultStore]['food_drink_id']) ? $_SESSION[$this->defaultStore]['food_drink_id'] : array();
					if(!empty($selected_food_drink_id_arr))
					{
						$food_drink_arr = pjFoodDrinkModel::factory()
							->select('t1.*, t2.content as title')
							->join('pjMultiLang', "t2.model='pjFoodDrink' AND t2.foreign_id=t1.id AND t2.field='title' AND t2.locale='".$this->getLocaleId()."'", 'left outer')
							->whereIn('t1.id', array_keys($selected_food_drink_id_arr))
							->orderBy("id ASC")
							->findAll()
							->getData();
						$this->set('food_drink_arr', $food_drink_arr);
					}
					
					$STORE = $_SESSION[$this->defaultStore];
					$start_date = $STORE['start_date'];
					$end_date = $STORE['end_date'];
					$room_id = $STORE['room_id'];
					$book_by = $STORE['book_by'];
					$slots = isset($STORE['slots']) ? $STORE['slots'] : array();
					$equipment_id = isset($STORE['equipment_id']) ? $STORE['equipment_id'] : array();
					$units = isset($STORE['units']) ? $STORE['units'] : array();
					$food_drink_id = isset($STORE['food_drink_id']) ? $STORE['food_drink_id'] : array();
					$people = isset($STORE['people']) ? $STORE['people'] : array();
					
					$price_arr = pjAppController::getPrices($start_date, $end_date, $room_id, $book_by, $slots, $equipment_id, $units, $food_drink_id, $people, $this->getForeignId(), $this->option_arr);
					
					$country_arr = pjCountryModel::factory()
						->select('t1.id, t2.content AS country_title')
						->join('pjMultiLang', "t2.model='pjCountry' AND t2.foreign_id=t1.id AND t2.field='name' AND t2.locale='".$this->getLocaleId()."'", 'left outer')
						->orderBy('`country_title` ASC')
						->findAll()
						->getData();
					
					$this->set('arr', $duration_info_arr['arr']);
					$this->set('layout', $layout);
					$this->set('price_arr', $price_arr);
					$this->set('country_arr', $country_arr);
					$this->set('find_menu', $this->findMenu($duration_info_arr['layout_arr']));
				}
			}else{
				pjAppController::jsonResponse(array('status' => 'ERR', 'code' => 100, 'text' => ''));
				exit;
			}
		}
	}
	public function pjActionPreview()
	{
		$this->setAjax(true);
	
		if($this->isXHR())
		{
			if (isset($_SESSION[$this->defaultStore]) &&
					count($_SESSION[$this->defaultStore]) > 0 &&
					isset($_SESSION[$this->defaultStore]['room_id']) &&
					isset($_SESSION[$this->defaultStore]['book_by']))
			{
				$duration_info_arr = pjAppController::getDuration($this->_get('room_id'), $this->_get('start_date'), null, $this->getLocaleId(), $this->getForeignId(), $this->option_arr);
	
				$layout = pjLayoutModel::factory()
					->select('t1.*, t2.content as title')
					->join('pjMultiLang', "t2.model='pjLayout' AND t2.foreign_id=t1.id AND t2.field='title' AND t2.locale='".$this->getLocaleId()."'", 'left outer')
					->find($this->_get('layout_id'))
					->getData();

				$selected_equipment_id_arr = isset($_SESSION[$this->defaultStore]['equipment_id']) ? $_SESSION[$this->defaultStore]['equipment_id'] : array();
				if(!empty($selected_equipment_id_arr))
				{
					$equipment_arr = pjEquipmentModel::factory()
						->select('t1.*, t2.content as title')
						->join('pjMultiLang', "t2.model='pjEquipment' AND t2.foreign_id=t1.id AND t2.field='title' AND t2.locale='".$this->getLocaleId()."'", 'left outer')
						->whereIn('t1.id', array_keys($selected_equipment_id_arr))
						->orderBy("id ASC")
						->findAll()
						->getData();
					$this->set('equipment_arr', $equipment_arr);
				}
				$selected_food_drink_id_arr = isset($_SESSION[$this->defaultStore]['food_drink_id']) ? $_SESSION[$this->defaultStore]['food_drink_id'] : array();
				if(!empty($selected_food_drink_id_arr))
				{
					$food_drink_arr = pjFoodDrinkModel::factory()
						->select('t1.*, t2.content as title')
						->join('pjMultiLang', "t2.model='pjFoodDrink' AND t2.foreign_id=t1.id AND t2.field='title' AND t2.locale='".$this->getLocaleId()."'", 'left outer')
						->whereIn('t1.id', array_keys($selected_food_drink_id_arr))
						->orderBy("id ASC")
						->findAll()
						->getData();
					$this->set('food_drink_arr', $food_drink_arr);
				}
					
				$STORE = $_SESSION[$this->defaultStore];
				$start_date = $STORE['start_date'];
				$end_date = $STORE['end_date'];
				$room_id = $STORE['room_id'];
				$book_by = $STORE['book_by'];
				$slots = isset($STORE['slots']) ? $STORE['slots'] : array();
				$equipment_id = isset($STORE['equipment_id']) ? $STORE['equipment_id'] : array();
				$units = isset($STORE['units']) ? $STORE['units'] : array();
				$food_drink_id = isset($STORE['food_drink_id']) ? $STORE['food_drink_id'] : array();
				$people = isset($STORE['people']) ? $STORE['people'] : array();
					
				$price_arr = pjAppController::getPrices($start_date, $end_date, $room_id, $book_by, $slots, $equipment_id, $units, $food_drink_id, $people, $this->getForeignId(), $this->option_arr);
					
				if(isset($_SESSION[$this->defaultForm]['c_country']) && (int) $_SESSION[$this->defaultForm]['c_country'] > 0)
				{	
					$country_arr = pjCountryModel::factory()
						->select('t1.id, t2.content AS country_title')
						->join('pjMultiLang', "t2.model='pjCountry' AND t2.foreign_id=t1.id AND t2.field='name' AND t2.locale='".$this->getLocaleId()."'", 'left outer')
						->find($_SESSION[$this->defaultForm]['c_country'])
						->getData();
					$this->set('country_arr', $country_arr);
				}
					
				$this->set('arr', $duration_info_arr['arr']);
				$this->set('layout', $layout);
				$this->set('price_arr', $price_arr);
				$this->set('find_menu', $this->findMenu($duration_info_arr['layout_arr']));
				
			}else{
				pjAppController::jsonResponse(array('status' => 'ERR', 'code' => 100, 'text' => ''));
				exit;
			}
		}
	}
	public function pjActionSaveBooking()
	{
		$this->setAjax(true);
	
		if ($this->isXHR())
		{
			if (!isset($_POST['mrbs_preview']) || !isset($_SESSION[$this->defaultForm]) || empty($_SESSION[$this->defaultForm]) || !isset($_SESSION[$this->defaultStore]) || empty($_SESSION[$this->defaultStore]))
			{
				pjAppController::jsonResponse(array('status' => 'ERR', 'code' => 109));
			}
			if ((int) $this->option_arr['o_bf_include_captcha'] === 3 && (!isset($_SESSION[$this->defaultForm]['captcha']) ||
					!pjCaptcha::validate($_SESSION[$this->defaultForm]['captcha'], $_SESSION[$this->defaultCaptcha]) ))
			{
				pjAppController::jsonResponse(array('status' => 'ERR', 'code' => 110));
			}
			$_SESSION[$this->defaultCaptcha] = NULL;
			unset($_SESSION[$this->defaultCaptcha]);
				
			$STORE = @$_SESSION[$this->defaultStore];
			$FORM = @$_SESSION[$this->defaultForm];
				
			$data = array();
				
			$data['uuid'] = time();
			$data['room_id'] = $STORE['room_id'];
			$data['attendees'] = $STORE['attendees'];
			$data['layout_id'] = isset($STORE['layout_id']) ? $STORE['layout_id'] : ":NULL";
			$data['book_by'] = $STORE['book_by'];
			$data['start_date'] = $STORE['start_date'];
			if($STORE['book_by'] == 'multiday')
			{
				$data['end_date'] = $STORE['end_date'];
			}else{
				$data['end_date'] = ':NULL';
			}
			$data['ip'] = pjUtil::getClientIp();
			$data['status'] = $this->option_arr['o_booking_status'];
			$data['created'] = date('Y-m-d H:i:s');
			$payment = ':NULL';
			if(isset($FORM['payment_method']))
			{
				$payment = $FORM['payment_method'];
				if($payment != 'creditcard')
				{
					$data['cc_exp_month'] = '';
					$data['cc_exp_year'] = '';
				}
			}
					
			$start_date = $STORE['start_date'];
			$end_date = $STORE['end_date'];
			$room_id = $STORE['room_id'];
			$book_by = $STORE['book_by'];
			$slots = isset($STORE['slots']) ? $STORE['slots'] : array();
			$equipment_id = isset($STORE['equipment_id']) ? $STORE['equipment_id'] : array();
			$units = isset($STORE['units']) ? $STORE['units'] : array();
			$food_drink_id = isset($STORE['food_drink_id']) ? $STORE['food_drink_id'] : array();
			$people = isset($STORE['people']) ? $STORE['people'] : array();
			$price_arr = pjAppController::getPrices($start_date, $end_date, $room_id, $book_by, $slots, $equipment_id, $units, $food_drink_id, $people, $this->getForeignId(), $this->option_arr);
			
			$data['room_price'] = $price_arr['room_price'];
			$data['tax'] = $price_arr['tax'];
			$data['subtotal'] = $price_arr['subtotal'];
			$data['total'] = $price_arr['total'];
			$data['deposit'] = $price_arr['deposit'];
			$data['equipment_price'] = $price_arr['equipment_price'];
			$data['food_drink_price'] = $price_arr['food_drink_price'];
			$data['hours'] = $price_arr['hours'];
			
			$pjBookingModel = pjBookingModel::factory();
			$id = $pjBookingModel->setAttributes(array_merge($FORM, $data))->insert()->getInsertId();
			if ($id !== false && (int) $id > 0)
			{
				if($STORE['book_by'] == 'hour')
				{
					if(isset($STORE['slots']) && is_array($STORE['slots']) && count($STORE['slots']) > 0)
					{
						$pjBookingSlotModel = pjBookingSlotModel::factory();
						foreach($STORE['slots'] as $k => $pair)
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
				if(isset($STORE['equipment_id']) && is_array($STORE['equipment_id']) && count($STORE['equipment_id']) > 0)
				{
					$pjBookingEquipmentModel = pjBookingEquipmentModel::factory();
					foreach($STORE['equipment_id'] as $equipment_id => $pair)
					{
						$equipment_data = array();
						list($price, $per) = explode("|", $pair);
						$equipment_data['booking_id'] = $id;
						$equipment_data['equipment_id'] = $equipment_id;
						$equipment_data['units'] = (isset($STORE['units'][$equipment_id]) ? (int) $STORE['units'][$equipment_id] : 1);
				
						$pjBookingEquipmentModel->reset()->setAttributes($equipment_data)->insert();
					}
				}
				if(isset($STORE['food_drink_id']) && is_array($STORE['food_drink_id']) && count($STORE['food_drink_id']) > 0)
				{
					$pjBookingFoodDrinkModel = pjBookingFoodDrinkModel::factory();
					foreach($STORE['food_drink_id'] as $food_drink_id => $price)
					{
						$food_drink_data = array();
						$food_drink_data['booking_id'] = $id;
						$food_drink_data['food_drink_id'] = $food_drink_id;
						$food_drink_data['people'] = (isset($STORE['people'][$food_drink_id]) ? (int) $STORE['people'][$food_drink_id] : 1);
				
						$pjBookingFoodDrinkModel->reset()->setAttributes($food_drink_data)->insert();
					}
				}
				$arr = $pjBookingModel
					->reset()
					->find($id)
					->getData();
	
				$pdata = array();
				$pdata['booking_id'] = $id;
				$pdata['payment_method'] = $payment;
				$pdata['payment_type'] = 'online';
				$pdata['amount'] = $arr['deposit'];
				$pdata['status'] = 'notpaid';
				pjBookingPaymentModel::factory()->setAttributes($pdata)->insert();
	
				pjFront::pjActionConfirmSend($this->option_arr, $id, PJ_SALT, 'confirm');
	
				unset($_SESSION[$this->defaultStore]);
				unset($_SESSION[$this->defaultForm]);
	
				$json = array('code' => 200, 'text' => '', 'booking_id' => $id, 'payment' => $payment);
				pjAppController::jsonResponse($json);
			}else {
				pjAppController::jsonResponse(array('code' => 'ERR', 'code' => 119));
			}
		}
	}
	public function pjActionCancel()
	{
		$this->setLayout('pjActionCancel');
	
		$pjBookingModel = pjBookingModel::factory();
	
		if (isset($_POST['booking_cancel']))
		{
			$booking_arr = $pjBookingModel->find($_POST['id'])->getData();
			if (count($booking_arr) > 0)
			{
				$sql = "UPDATE `".$pjBookingModel->getTable()."` SET status = 'cancelled' WHERE SHA1(CONCAT(`id`, `created`, '".PJ_SALT."')) = '" . $_POST['hash'] . "'";
	
				$pjBookingModel->reset()->execute($sql);
	
				pjFront::pjActionConfirmSend($this->option_arr, $_POST['id'], PJ_SALT, 'cancel');
	
				pjUtil::redirect($_SERVER['PHP_SELF'] . '?controller=pjFront&action=pjActionCancel&err=200');
			}
		}else{
			if (isset($_GET['hash']) && isset($_GET['id']))
			{
				$arr = $pjBookingModel
					->reset()
					->select("t1.*, t2.content as room, t3.content as country_title, t4.content as layout, AES_DECRYPT(t1.cc_type, '".PJ_SALT."') AS `cc_type`,
								AES_DECRYPT(t1.cc_num, '".PJ_SALT."') AS `cc_num`,
								AES_DECRYPT(t1.cc_exp_month, '".PJ_SALT."') AS `cc_exp_month`,
								AES_DECRYPT(t1.cc_exp_year, '".PJ_SALT."') AS `cc_exp_year`,
								AES_DECRYPT(t1.cc_code, '".PJ_SALT."') AS `cc_code`")
					->join('pjMultiLang', "t2.model='pjRoom' AND t2.foreign_id=t1.room_id AND t2.field='title' AND t2.locale='".$this->getLocaleId()."'", 'left outer')
					->join('pjMultiLang', "t3.model='pjCountry' AND t3.foreign_id=t1.c_country AND t3.field='name' AND t3.locale='".$this->getLocaleId()."'", 'left outer')
					->join('pjMultiLang', "t4.model='pjLayout' AND t4.foreign_id=t1.layout_id AND t4.field='title' AND t4.locale='".$this->getLocaleId()."'", 'left outer')
					->find($_GET['id'])
					->getData();
				if (count($arr) == 0)
				{
					$this->set('status', 2);
				}else{
					if ($arr['status'] == 'cancelled')
					{
						$this->set('status', 4);
					}else{
						$hash = sha1($arr['id'] . $arr['created'] . PJ_SALT);
						if ($_GET['hash'] != $hash)
						{
							$this->set('status', 3);
						}else{
	
							$this->set('arr', $arr);
						}
					}
				}
			}else if (!isset($_GET['err'])) {
				$this->set('status', 1);
			}
		}
	}
	
	public function pjActionGetPaymentForm()
	{
		$this->setAjax(true);
	
		if ($this->isXHR())
		{
			$arr = pjBookingModel::factory()
				->select('t1.*, t2.content as room')
				->join('pjMultiLang', "t2.model='pjRoom' AND t2.foreign_id=t1.room_id AND t2.field='title' AND t2.locale='".$this->getLocaleId()."'", 'left outer')
				->find($_GET['booking_id'])
				->getData();
				
			if (!empty($arr))
			{
				switch ($arr['payment_method'])
				{
					case 'paypal':
						$this->set('params', array(
							'name' => 'mrbsPaypal',
							'id' => 'mrbsPaypal',
							'business' => $this->option_arr['o_paypal_address'],
							'client_id' => $this->option_arr['o_paypal_client_id'],
							'client_secret' => $this->option_arr['o_paypal_client_secret'],
							'item_name' => pjSanitize::html($arr['room']),
							'custom' => $arr['id'],
							'amount' => $arr['deposit'],
							'currency_code' => $this->option_arr['o_currency'],
							'return' => $this->option_arr['o_thankyou_page'],
							'failure_url' => $this->option_arr['o_paypal_cancel_url'],
							'notify_url' => PJ_INSTALL_URL . 'index.php?controller=pjFront&action=pjActionConfirmPaypal',
							'target' => '_self',
							'charset' => 'utf-8'
						));
						break;
					case 'authorize':
						$this->set('params', array(
							'name' => 'mrbsAuthorize',
							'id' => 'mrbsAuthorize',
							'target' => '_self',
							'timezone' => $this->option_arr['o_authorize_timezone'],
							'transkey' => $this->option_arr['o_authorize_transkey'],
							'x_login' => $this->option_arr['o_authorize_merchant_id'],
							'x_description' => pjSanitize::html($arr['room']),
							'x_amount' => $arr['deposit'],
							'x_invoice_num' => $arr['id'],
							'x_receipt_link_url' => $this->option_arr['o_thankyou_page'],
							'x_relay_url' => PJ_INSTALL_URL . 'index.php?controller=pjFront&action=pjActionConfirmAuthorize'
						));
						break;
				}
			}
			$this->set('arr', $arr);
			$this->set('get', $_GET);
		}
	}
	
	public function pjActionConfirmAuthorize()
	{
		$this->setAjax(true);
	
		if (pjObject::getPlugin('pjAuthorize') === NULL)
		{
			$this->log('Authorize.NET plugin not installed');
			exit;
		}
		$pjBookingModel = pjBookingModel::factory();
	
		$booking_arr = $pjBookingModel
			->find($_POST['x_invoice_num'])
			->getData();
		if (count($booking_arr) == 0)
		{
			$this->log('No such booking');
			pjUtil::redirect($this->option_arr['o_thankyou_page']);
		}
	
		if (count($booking_arr) > 0)
		{
			$params = array(
					'transkey' => $this->option_arr['o_authorize_transkey'],
					'x_login' => $this->option_arr['o_authorize_merchant_id'],
					'md5_setting' => $this->option_arr['o_authorize_md5_hash'],
					'key' => md5($this->option_arr['private_key'] . PJ_SALT)
			);
				
			$response = $this->requestAction(array('controller' => 'pjAuthorize', 'action' => 'pjActionConfirm', 'params' => $params), array('return'));
			if ($response !== FALSE && $response['status'] === 'OK')
			{
			    $pjBookingModel->reset()->set('id', $response['transaction_id'])
					->modify(array('status' => $this->option_arr['o_payment_status'], 'processed_on' => ':NOW()'));
	
				pjBookingPaymentModel::factory()->where('booking_id', $response['transaction_id'])->where('payment_type', 'online')->limit(1)->modifyAll(array('status' => 'paid'));
					
				pjFront::pjActionConfirmSend($this->option_arr, $booking_arr['id'], PJ_SALT, 'payment');
	
			} elseif (!$response) {
				$this->log('Authorization failed');
			} else {
				$this->log('Booking not confirmed. ' . $response['response_reason_text']);
			}
			?>
			<script type="text/javascript">window.location.href="<?php echo $this->option_arr['o_thankyou_page']; ?>";</script>
			<?php
			return;
		}
	}
	
	public function pjActionConfirmPaypal()
	{
		$this->setAjax(true);
	
		if (pjObject::getPlugin('pjPaypal') === NULL)
		{
			$this->log('Paypal plugin not installed');
			pjAppController::jsonResponse(array('status' => 'ERR', 'text' => 'Paypal plugin not installed'));
		}
		$input = file_get_contents('php://input');
		$post = json_decode($input, true);
		if ($post) {
		    $_REQUEST = array_merge($_REQUEST, $post);
		}
		
		$pjBookingModel = pjBookingModel::factory();
	
		$booking_arr = $pjBookingModel
		->find($_REQUEST['custom'])
		->getData();
		if (count($booking_arr) == 0)
		{
			$this->log('No such booking');
			pjAppController::jsonResponse(array('status' => 'ERR', 'text' => 'No such booking'));
		}
	
		$params = array(
		    'request'		=> $_REQUEST,
		    'cancel_hash'	=> sha1($booking_arr['uuid'].strtotime($booking_arr['created']).PJ_SALT),
		    'txn_id' => @$booking_arr['txn_id'],
		    'paypal_address' => $this->option_arr['o_paypal_address'],
		    'deposit' => @$booking_arr['deposit'],
		    'currency' => $this->option_arr['o_currency'],
		    'key' => md5($this->option_arr['private_key'] . PJ_SALT)
		);
		$response = $this->requestAction(array('controller' => 'pjPaypal', 'action' => 'pjActionConfirm', 'params' => $params), array('return'));	
		if ($response !== FALSE && $response['status'] === 'OK')
		{
			$this->log('Booking confirmed');
			$pjBookingModel->reset()->set('id', $booking_arr['id'])->modify(array(
					'status' => $this->option_arr['o_payment_status'],
					'txn_id' => $response['transaction_id'],
					'processed_on' => ':NOW()'
			));
			pjBookingPaymentModel::factory()->where('booking_id', $booking_arr['id'])->where('payment_type', 'online')->limit(1)->modifyAll(array('status' => 'paid'));
				
			pjFront::pjActionConfirmSend($this->option_arr, $booking_arr['id'], PJ_SALT, 'payment');
			pjAppController::jsonResponse(array('status' => 'OK', 'text' => 'Booking confirmed'));
		} elseif (!$response) {
			$this->log('Authorization failed');
			pjAppController::jsonResponse(array('status' => 'ERR', 'text' => 'Authorization failed'));
		} else {
			$this->log('Booking not confirmed');
			pjAppController::jsonResponse(array('status' => 'ERR', 'text' => 'Booking not confirmed'));
		}
	}
	
	public function pjActionConfirmSend($option_arr, $booking_id, $salt, $opt)
	{
		$Email = new pjEmail();
		if ($option_arr['o_send_email'] == 'smtp')
		{
			$Email
				->setTransport('smtp')
				->setSmtpHost($option_arr['o_smtp_host'])
				->setSmtpPort($option_arr['o_smtp_port'])
				->setSmtpUser($option_arr['o_smtp_user'])
				->setSmtpPass($option_arr['o_smtp_pass'])
				->setSender($option_arr['o_smtp_user'])
			;
		}
		$Email->setContentType('text/html');
	
		$admin_email = $this->getAdminEmail();
		$admin_phone = $this->getAdminPhone();
		$from_email = $admin_email;
	
		$locale_id = $this->getLocaleId();
	
		$booking_arr = pjBookingModel::factory()->find($booking_id)->getData();
		
		$tokens = pjAppController::getTokens($booking_id, $option_arr, PJ_SALT, $locale_id);
	
		$pjMultiLangModel = pjMultiLangModel::factory();
	
		if ($option_arr['o_email_payment'] == 1 && $opt == 'payment')
		{
			$lang_message = $pjMultiLangModel->reset()->select('t1.*')
				->where('t1.model','pjOption')
				->where('t1.locale', $locale_id)
				->where('t1.field', 'o_email_payment_message')
				->limit(0, 1)
				->findAll()->getData();
			$lang_subject = $pjMultiLangModel->reset()->select('t1.*')
				->where('t1.model','pjOption')
				->where('t1.locale', $locale_id)
				->where('t1.field', 'o_email_payment_subject')
				->limit(0, 1)
				->findAll()->getData();
	
			if (count($lang_message) === 1 && count($lang_subject) === 1 && !empty($lang_subject[0]['content']) && !empty($lang_message[0]['content']))
			{
				$message = str_replace($tokens['search'], $tokens['replace'], $lang_message[0]['content']);
	
				$Email
					->setTo($booking_arr['c_email'])
					->setFrom($from_email)
					->setSubject($lang_subject[0]['content'])
					->send($message);
			}
		}
		if ($option_arr['o_admin_email_payment'] == 1 && $opt == 'payment')
		{
			$lang_message = $pjMultiLangModel->reset()->select('t1.*')
				->where('t1.model','pjOption')
				->where('t1.locale', $locale_id)
				->where('t1.field', 'o_admin_email_payment_message')
				->limit(0, 1)
				->findAll()->getData();
			$lang_subject = $pjMultiLangModel->reset()->select('t1.*')
				->where('t1.model','pjOption')
				->where('t1.locale', $locale_id)
				->where('t1.field', 'o_admin_email_payment_subject')
				->limit(0, 1)
				->findAll()->getData();
	
			if (count($lang_message) === 1 && count($lang_subject) === 1 && !empty($lang_subject[0]['content']) && !empty($lang_message[0]['content']))
			{
				$message = str_replace($tokens['search'], $tokens['replace'], $lang_message[0]['content']);
	
				$Email
					->setTo($admin_email)
					->setFrom($from_email)
					->setSubject($lang_subject[0]['content'])
					->send($message);
			}
		}
		if(!empty($admin_phone) && $opt == 'payment')
		{
			$lang_message = $pjMultiLangModel->reset()->select('t1.*')
				->where('t1.model','pjOption')
				->where('t1.locale', $locale_id)
				->where('t1.field', 'o_admin_sms_payment_message')
				->limit(0, 1)
				->findAll()->getData();
			if (count($lang_message) === 1 && !empty($lang_message[0]['content']))
			{
				$message = str_replace($tokens['search'], $tokens['replace'], $lang_message[0]['content']);
				if($message != '')
				{
					$params = array(
							'text' => $message,
							'type' => 'unicode',
							'key' => md5($option_arr['private_key'] . PJ_SALT)
					);
					$params['number'] = $admin_phone;
					$this->requestAction(array('controller' => 'pjSms', 'action' => 'pjActionSend', 'params' => $params), array('return'));
				}
			}
		}
	
		if ($option_arr['o_email_confirmation'] == 1 && $opt == 'confirm')
		{
			$lang_message = $pjMultiLangModel->reset()->select('t1.*')
				->where('t1.model','pjOption')
				->where('t1.locale', $locale_id)
				->where('t1.field', 'o_email_confirmation_message')
				->limit(0, 1)
				->findAll()->getData();
			$lang_subject = $pjMultiLangModel->reset()->select('t1.*')
				->where('t1.model','pjOption')
				->where('t1.locale', $locale_id)
				->where('t1.field', 'o_email_confirmation_subject')
				->limit(0, 1)
				->findAll()->getData();
	
			if (count($lang_message) === 1 && count($lang_subject) === 1 && !empty($lang_subject[0]['content']) && !empty($lang_message[0]['content']))
			{
				$message = str_replace($tokens['search'], $tokens['replace'], $lang_message[0]['content']);
					
				$Email
					->setTo($booking_arr['c_email'])
					->setFrom($from_email)
					->setSubject($lang_subject[0]['content'])
					->send($message);
			}
		}
		if ($option_arr['o_admin_email_confirmation'] == 1 && $opt == 'confirm')
		{
			$lang_message = $pjMultiLangModel->reset()->select('t1.*')
				->where('t1.model','pjOption')
				->where('t1.locale', $locale_id)
				->where('t1.field', 'o_admin_email_confirmation_message')
				->limit(0, 1)
				->findAll()->getData();
			$lang_subject = $pjMultiLangModel->reset()->select('t1.*')
				->where('t1.model','pjOption')
				->where('t1.locale', $locale_id)
				->where('t1.field', 'o_admin_email_confirmation_subject')
				->limit(0, 1)
				->findAll()->getData();
	
			if (count($lang_message) === 1 && count($lang_subject) === 1 && !empty($lang_subject[0]['content']) && !empty($lang_message[0]['content']))
			{
				$message = str_replace($tokens['search'], $tokens['replace'], $lang_message[0]['content']);
				$Email
					->setTo($admin_email)
					->setFrom($from_email)
					->setSubject($lang_subject[0]['content'])
					->send($message);
			}
		}
		if(!empty($booking_arr['c_phone']) && $opt == 'confirm')
		{
			$lang_message = $pjMultiLangModel->reset()->select('t1.*')
				->where('t1.model','pjOption')
				->where('t1.locale', $locale_id)
				->where('t1.field', 'o_sms_confirmation_message')
				->limit(0, 1)
				->findAll()->getData();
			if (count($lang_message) === 1 && !empty($lang_message[0]['content']))
			{
				$message = str_replace($tokens['search'], $tokens['replace'], $lang_message[0]['content']);
				if($message != '')
				{
					$params = array(
							'text' => $message,
							'type' => 'unicode',
							'key' => md5($option_arr['private_key'] . PJ_SALT)
					);
					$params['number'] = $booking_arr['c_phone'];
					$this->requestAction(array('controller' => 'pjSms', 'action' => 'pjActionSend', 'params' => $params), array('return'));
				}
			}
		}
		if(!empty($admin_phone) && $opt == 'confirm')
		{
			$lang_message = $pjMultiLangModel->reset()->select('t1.*')
				->where('t1.model','pjOption')
				->where('t1.locale', $locale_id)
				->where('t1.field', 'o_admin_sms_confirmation_message')
				->limit(0, 1)
				->findAll()->getData();
			if (count($lang_message) === 1 && !empty($lang_message[0]['content']))
			{
				$message = str_replace($tokens['search'], $tokens['replace'], $lang_message[0]['content']);
				if($message != '')
				{
					$params = array(
							'text' => $message,
							'type' => 'unicode',
							'key' => md5($option_arr['private_key'] . PJ_SALT)
					);
					$params['number'] = $admin_phone;
					$this->requestAction(array('controller' => 'pjSms', 'action' => 'pjActionSend', 'params' => $params), array('return'));
				}
			}
		}
	
		if ($option_arr['o_email_cancel'] == 1 && $opt == 'cancel')
		{
			$lang_message = $pjMultiLangModel->reset()->select('t1.*')
				->where('t1.model','pjOption')
				->where('t1.locale', $locale_id)
				->where('t1.field', 'o_email_cancel_message')
				->limit(0, 1)
				->findAll()->getData();
			$lang_subject = $pjMultiLangModel->reset()->select('t1.*')
				->where('t1.model','pjOption')
				->where('t1.locale', $locale_id)
				->where('t1.field', 'o_email_cancel_subject')
				->limit(0, 1)
				->findAll()->getData();
	
			if (count($lang_message) === 1 && count($lang_subject) === 1 && !empty($lang_subject[0]['content']) && !empty($lang_message[0]['content']))
			{
				$message = str_replace($tokens['search'], $tokens['replace'], $lang_message[0]['content']);
	
				$Email
					->setTo($booking_arr['c_email'])
					->setFrom($from_email)
					->setSubject($lang_subject[0]['content'])
					->send($message);
			}
		}
		if ($option_arr['o_admin_email_cancel'] == 1 && $opt == 'cancel')
		{
			$lang_message = $pjMultiLangModel->reset()->select('t1.*')
				->where('t1.model','pjOption')
				->where('t1.locale', $locale_id)
				->where('t1.field', 'o_admin_email_cancel_message')
				->limit(0, 1)
				->findAll()->getData();
			$lang_subject = $pjMultiLangModel->reset()->select('t1.*')
				->where('t1.model','pjOption')
				->where('t1.locale', $locale_id)
				->where('t1.field', 'o_admin_email_cancel_subject')
				->limit(0, 1)
				->findAll()->getData();
	
			if (count($lang_message) === 1 && count($lang_subject) === 1 && !empty($lang_subject[0]['content']) && !empty($lang_message[0]['content']))
			{
				$message = str_replace($tokens['search'], $tokens['replace'], $lang_message[0]['content']);
	
				$Email
					->setTo($admin_email)
					->setFrom($from_email)
					->setSubject($lang_subject[0]['content'])
					->send($message);
			}
		}
	}
	public function findMenu($layout_arr)
	{
		$cnt_equipment = pjEquipmentModel::factory()->findCount()->getData();
		$hide_equipment = true;
		$hide_food_drinks = true;
		$hide_both = true;
		if(!empty($layout_arr) || $cnt_equipment > 0)
		{
			$hide_equipment = false;
			$hide_both = false;
		}
		$cnt_food_drinks = pjFoodDrinkModel::factory()->findCount()->getData();
		if($cnt_food_drinks > 0)
		{
			$hide_food_drinks = false;
			$hide_both = false;
		}
		return compact('hide_equipment','hide_food_drinks','hide_both');
	}
	public function isXHR()
	{
		return parent::isXHR() || isset($_SERVER['HTTP_ORIGIN']);
	}
	
	static protected function allowCORS()
	{
		$origin = isset($_SERVER['HTTP_ORIGIN']) ? $_SERVER['HTTP_ORIGIN'] : '*';
		header('P3P: CP="ALL DSP COR CUR ADM TAI OUR IND COM NAV INT"');
		header("Access-Control-Allow-Origin: $origin");
		header("Access-Control-Allow-Credentials: true");
		header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
		header("Access-Control-Allow-Headers: Origin, X-Requested-With");
	}
}
?>