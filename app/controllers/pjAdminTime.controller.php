<?php
if (!defined("ROOT_PATH"))
{
	header("HTTP/1.1 403 Forbidden");
	exit;
}
class pjAdminTime extends pjAdmin
{
	public function pjActionIndex()
	{
		$this->checkLogin();

		if ($this->isAdmin() || $this->isEmployee())
		{
			if (isset($_POST['working_time']))
			{
				$data = array();
				$weekDays = array('monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday');
				foreach ($weekDays as $day)
				{
					if (!isset($_POST[$day . '_dayoff']))
					{
						$data[$day . '_from'] = date('H:i', strtotime($_POST[$day . '_from']));
						$data[$day . '_to'] = date('H:i', strtotime($_POST[$day . '_to']));
						$data[$day . '_morning_from'] = date('H:i', strtotime($_POST[$day . '_morning_from']));
						$data[$day . '_morning_to'] = date('H:i', strtotime($_POST[$day . '_morning_to']));
						$data[$day . '_afternoon_from'] = date('H:i', strtotime($_POST[$day . '_afternoon_from']));
						$data[$day . '_afternoon_to'] = date('H:i', strtotime($_POST[$day . '_afternoon_to']));
						$data[$day . '_dayoff'] = "F";
					} else {
						$data[$day . '_from'] = ":NULL";
						$data[$day . '_to'] = ":NULL";
						$data[$day . '_morning_from'] = ":NULL";
						$data[$day . '_morning_to'] = ":NULL";
						$data[$day . '_afternoon_from'] = ":NULL";
						$data[$day . '_afternoon_to'] = ":NULL";
						$data[$day . '_dayoff'] = "T";
					}
				}

				pjWorkingTimeModel::factory()
					->set('id', $_POST['id'])
					->modify($data);

				pjUtil::redirect(sprintf("%sindex.php?controller=pjAdminTime&action=pjActionIndex&err=AT01", PJ_INSTALL_URL));
			}
			
			$foreign_id = $this->getForeignId();
			if (isset($_GET['foreign_id']) && (int) $_GET['foreign_id'] > 0)
			{
				$foreign_id = (int) $_GET['foreign_id'];
			}
			
			$wt_arr = pjWorkingTimeModel::factory()
				->where('t1.foreign_id', $foreign_id)
				->limit(1)
				->findAll()
				->getData();
			
			$this->set('wt_arr', !empty($wt_arr) ? $wt_arr[0] : array());
			$this->appendCss('jquery.ui.timepicker.css', PJ_THIRD_PARTY_PATH . 'timepicker/');
			$this->appendJs('jquery.ui.timepicker.js', PJ_THIRD_PARTY_PATH . 'timepicker/');
			$this->appendJs('pjAdminTime.js');
		} else {
			$this->set('status', 2);
		}
	}
	
	public function pjActionCustom()
	{
		$this->checkLogin();

		if ($this->isAdmin())
		{
			if (isset($_POST['custom_time']))
			{
				$foreign_id = $this->getForeignId();
				if (isset($_POST['foreign_id']) && (int) $_POST['foreign_id'] > 0)
				{
					$foreign_id = (int) $_POST['foreign_id'];
				}
				
				$pjDateModel = pjDateModel::factory();
				$date = pjUtil::formatDate($_POST['date'], $this->option_arr['o_date_format']);
				$pjDateModel
					->where('foreign_id', $foreign_id)
					->where('`date`', $date)
					->limit(1)
					->eraseAll();
				
				$data = array();
				$data['foreign_id'] = $foreign_id;
				$data['start_time'] = !empty($_POST['start']) ? date('H:i', strtotime($_POST['start'])) : '00:00';
				$data['end_time'] = !empty($_POST['end']) ? date('H:i', strtotime($_POST['end'])) : '00:00';
				$data['start_morning'] = !empty($_POST['start_morning']) ? date('H:i', strtotime($_POST['start_morning'])) : '00:00';
				$data['end_morning'] = !empty($_POST['end_morning']) ? date('H:i', strtotime($_POST['end_morning'])) : '00:00';
				$data['start_afternoon'] = !empty($_POST['start_afternoon']) ? date('H:i', strtotime($_POST['start_afternoon'])) : '00:00';
				$data['end_afternoon'] = !empty($_POST['end_afternoon']) ? date('H:i', strtotime($_POST['end_afternoon'])) : '00:00';
				$data['date'] = $date;
				
				$pjDateModel->reset()->setAttributes(array_merge($_POST, $data))->insert();
				
				pjUtil::redirect($_SERVER['PHP_SELF'] . "?controller=pjAdminTime&action=pjActionCustom&err=AT02");
			}

			$this->appendCss('jquery.ui.timepicker.css', PJ_THIRD_PARTY_PATH . 'timepicker/');
			$this->appendJs('jquery.ui.timepicker.js', PJ_THIRD_PARTY_PATH . 'timepicker/');
			$this->appendJs('jquery.validate.min.js', PJ_THIRD_PARTY_PATH . 'validate/');
			$this->appendJs('jquery.datagrid.js', PJ_FRAMEWORK_LIBS_PATH . 'pj/js/');
			$this->appendJs('pjAdminTime.js');
			$this->appendJs('index.php?controller=pjAdmin&action=pjActionMessages', PJ_INSTALL_URL, true);
			
		} else {
			$this->set('status', 2);
		}
	}
	
	public function pjActionDeleteDate()
	{
		$this->setAjax(true);
	
		if ($this->isXHR() && $this->isLoged())
		{
			if (isset($_GET['id']) && (int) $_GET['id'] > 0 && pjDateModel::factory()->set('id', $_GET['id'])->erase()->getAffectedRows() == 1)
			{
				pjAppController::jsonResponse(array('status' => 'OK', 'code' => 200, 'text' => ''));
			}
			pjAppController::jsonResponse(array('status' => 'ERR', 'code' => 100, 'text' => ''));
		}
		exit;
	}
	
	public function pjActionDeleteDateBulk()
	{
		$this->setAjax(true);
	
		if ($this->isXHR() && $this->isLoged())
		{
			if (isset($_POST['record']) && !empty($_POST['record']))
			{
				pjDateModel::factory()->whereIn('id', $_POST['record'])->eraseAll();
				pjAppController::jsonResponse(array('status' => 'OK', 'code' => 200, 'text' => ''));
			}
			pjAppController::jsonResponse(array('status' => 'ERR', 'code' => 100, 'text' => ''));
		}
		exit;
	}
	
	public function pjActionGetDate()
	{
		$this->setAjax(true);
	
		if ($this->isXHR() && $this->isLoged())
		{
			$pjDateModel = pjDateModel::factory();
				
			$foreign_id = $this->getForeignId();
			if (isset($_GET['foreign_id']) && (int) $_GET['foreign_id'] > 0)
			{
				$foreign_id = (int) $_GET['foreign_id'];
			}
			
			$pjDateModel->where('t1.foreign_id', $foreign_id);
			
			if (isset($_GET['is_dayoff']) && strlen($_GET['is_dayoff']) > 0 && in_array($_GET['is_dayoff'], array('T', 'F')))
			{
				$pjDateModel->where('t1.is_dayoff', $_GET['is_dayoff']);
			}
				
			$column = 'date';
			$direction = 'ASC';
			$allowed_columns = array('date', 'start_time', 'end_time', 'morning', 'afternoon', 'is_dayoff');
			if (isset($_GET['direction']) && isset($_GET['column']) && in_array($_GET['column'], $allowed_columns) && in_array(strtoupper($_GET['direction']), array('ASC', 'DESC')))
			{
				$column = $_GET['column'];
				$direction = strtoupper($_GET['direction']);
			}

			$total = $pjDateModel->findCount()->getData();
			$rowCount = isset($_GET['rowCount']) && (int) $_GET['rowCount'] > 0 ? (int) $_GET['rowCount'] : 100;
			$pages = ceil($total / $rowCount);
			$page = isset($_GET['page']) && (int) $_GET['page'] > 0 ? intval($_GET['page']) : 1;
			$offset = ((int) $page - 1) * $rowCount;
			if ($page > $pages)
			{
				$page = $pages;
			}

			$data = $pjDateModel
				->orderBy("$column $direction")->limit($rowCount, $offset)->findAll()->getData();
			foreach($data as $k => $v)
			{
				if($v['is_dayoff'] == 'T')
				{
					$v['date'] = date($this->option_arr['o_date_format'], strtotime($v['date']));
					$v['start_time'] = '--';
					$v['end_time'] = '--';
					$v['morning'] = '--';
					$v['afternoon'] = '--';
				}else{
					$v['date'] = date($this->option_arr['o_date_format'], strtotime($v['date']));
					$v['start_time'] = date($this->option_arr['o_time_format'], strtotime($v['date'] . ' ' . $v['start_time']));
					$v['end_time'] = date($this->option_arr['o_time_format'], strtotime($v['date'] . ' ' . $v['end_time']));
					$v['morning'] = date($this->option_arr['o_time_format'], strtotime($v['date'] . ' ' . $v['start_morning'])) . '<br/>' . date($this->option_arr['o_time_format'], strtotime($v['date'] . ' ' . $v['end_morning']));
					$v['afternoon'] = date($this->option_arr['o_time_format'], strtotime($v['date'] . ' ' . $v['start_afternoon'])) . '<br/>' . date($this->option_arr['o_time_format'], strtotime($v['date'] . ' ' . $v['end_afternoon']));
				}
				$data[$k] = $v;
			}	
			pjAppController::jsonResponse(compact('data', 'total', 'pages', 'page', 'rowCount', 'column', 'direction'));
		}
		exit;
	}
	
	public function pjActionSaveDate()
	{
		$this->setAjax(true);
	
		if ($this->isXHR() && $this->isLoged())
		{
			$pjDateModel = pjDateModel::factory();
			if (!in_array($_POST['column'], $pjDateModel->getI18n()))
			{
				$pjDateModel->set('id', $_GET['id'])->modify(array($_POST['column'] => $_POST['value']));
			} else {
				pjMultiLangModel::factory()->updateMultiLang(array($this->getLocaleId() => array($_POST['column'] => $_POST['value'])), $_GET['id'], 'pjDate');
			}
		}
		exit;
	}
	
	public function pjActionUpdateCustom()
	{
		$this->checkLogin();

		if ($this->isAdmin() || $this->isEmployee())
		{
			if (isset($_POST['custom_time']))
			{
				$data = array();
				$data['date'] = pjUtil::formatDate($_POST['date'], $this->option_arr['o_date_format']);
				$data['start_time'] = !empty($_POST['start']) ? date('H:i', strtotime($_POST['start'])) : '00:00';
				$data['end_time'] = !empty($_POST['end']) ? date('H:i', strtotime($_POST['end'])) : '00:00';
				$data['start_morning'] = !empty($_POST['start_morning']) ? date('H:i', strtotime($_POST['start_morning'])) : '00:00';
				$data['end_morning'] = !empty($_POST['end_morning']) ? date('H:i', strtotime($_POST['end_morning'])) : '00:00';
				$data['start_afternoon'] = !empty($_POST['start_afternoon']) ? date('H:i', strtotime($_POST['start_afternoon'])) : '00:00';
				$data['end_afternoon'] = !empty($_POST['end_afternoon']) ? date('H:i', strtotime($_POST['end_afternoon'])) : '00:00';
				$data['is_dayoff'] = isset($_POST['is_dayoff']) ? 'T' : 'F';
				
				pjDateModel::factory()->set('id', $_POST['id'])->modify($data);
				
				pjUtil::redirect($_SERVER['PHP_SELF'] . "?controller=pjAdminTime&action=pjActionCustom&err=AT03");
			}
			
			$this->set('arr', pjDateModel::factory()->find($_GET['id'])->getData());
			
			$this->appendCss('jquery.ui.timepicker.css', PJ_THIRD_PARTY_PATH . 'timepicker/');
			$this->appendJs('jquery.ui.timepicker.js', PJ_THIRD_PARTY_PATH . 'timepicker/');
			$this->appendJs('jquery.validate.min.js', PJ_THIRD_PARTY_PATH . 'validate/');
			$this->appendJs('pjAdminTime.js');
		} else {
			$this->set('status', 2);
		}
	}
}
?>