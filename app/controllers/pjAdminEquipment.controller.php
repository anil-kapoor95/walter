<?php
if (!defined("ROOT_PATH"))
{
	header("HTTP/1.1 403 Forbidden");
	exit;
}
class pjAdminEquipment extends pjAdmin
{
	public function pjActionCreate()
	{
		$this->checkLogin();
		
		if ($this->isAdmin())
		{
			if (isset($_POST['equipment_create']))
			{
				$pjEquipmentModel = pjEquipmentModel::factory();
				
				$data = array();
				if(isset($_POST['multi_units']))
				{
					$data['multi_units'] = 'T';
				}else{
					$data['multi_units'] = 'F';
				}
				$id = $pjEquipmentModel->setAttributes(array_merge($_POST, $data))->insert()->getInsertId();
				
				if ($id !== false && (int) $id > 0)
				{
					$err = 'AEQ03';
					
					$pjMultiLangModel = pjMultiLangModel::factory();
					$pjMultiLangModel->saveMultiLang($_POST['i18n'], $id, 'pjEquipment', 'data');
					
				} else {
					$err = 'AEQ04';
				}
				pjUtil::redirect($_SERVER['PHP_SELF'] . "?controller=pjAdminEquipment&action=pjActionIndex&err=$err");
			} else {
				$locale_arr = pjLocaleModel::factory()->select('t1.*, t2.file')
					->join('pjLocaleLanguage', 't2.iso=t1.language_iso', 'left')
					->where('t2.file IS NOT NULL')
					->orderBy('t1.sort ASC')->findAll()->getData();
						
				$lp_arr = array();
				foreach ($locale_arr as $item)
				{
					$lp_arr[$item['id']."_"] = $item['file']; 
				}
				$this->set('lp_arr', $locale_arr);
				$this->set('locale_str', pjAppController::jsonEncode($lp_arr));
		
				$this->appendJs('jquery.validate.min.js', PJ_THIRD_PARTY_PATH . 'validate/');
				$this->appendJs('jquery.multilang.js', PJ_FRAMEWORK_LIBS_PATH . 'pj/js/');
				$this->appendJs('jquery.tipsy.js', PJ_THIRD_PARTY_PATH . 'tipsy/');
				$this->appendCss('jquery.tipsy.css', PJ_THIRD_PARTY_PATH . 'tipsy/');
				$this->appendJs('pjAdminEquipment.js');
			}
		} else {
			$this->set('status', 2);
		}
	}
		
	public function pjActionDeleteEquipment()
	{
		$this->setAjax(true);
	
		if ($this->isXHR())
		{
			$response = array();
			$pjEquipmentModel = pjEquipmentModel::factory();
			
			if ($pjEquipmentModel->setAttributes(array('id' => $_GET['id']))->erase()->getAffectedRows() == 1)
			{
				pjMultiLangModel::factory()->where('model', 'pjEquipment')->where('foreign_id', $_GET['id'])->eraseAll();
								
				$response['code'] = 200;
			} else {
				$response['code'] = 100;
			}
			pjAppController::jsonResponse($response);
		}
		exit;
	}
	
	public function pjActionDeleteEquipmentBulk()
	{
		$this->setAjax(true);
	
		if ($this->isXHR())
		{
			if (isset($_POST['record']) && count($_POST['record']) > 0)
			{
				$pjEquipmentModel = pjEquipmentModel::factory();
				
				$pjEquipmentModel->reset()->whereIn('id', $_POST['record'])->eraseAll();
				pjMultiLangModel::factory()->where('model', 'pjEquipment')->whereIn('foreign_id', $_POST['record'])->eraseAll();
			}
		}
		exit;
	}
	
	public function pjActionGetEquipment()
	{
		$this->setAjax(true);
	
		if ($this->isXHR())
		{
			$pjEquipmentModel = pjEquipmentModel::factory()
				->join('pjMultiLang', "t2.foreign_id = t1.id AND t2.model = 'pjEquipment' AND t2.locale = '".$this->getLocaleId()."' AND t2.field = 'title'", 'left');
			
			if (isset($_GET['q']) && !empty($_GET['q']))
			{
				$q = pjObject::escapeString($_GET['q']);
				$pjEquipmentModel->where('t2.content LIKE', "%$q%");
			}
			if (isset($_GET['status']) && !empty($_GET['status']) && in_array($_GET['status'], array('T', 'F')))
			{
				$pjEquipmentModel->where('t1.status', $_GET['status']);
			}

			$column = 'id';
			$direction = 'ASC';
			$allowed_columns = array('title', 'multi_units', 'price');
			if (isset($_GET['direction']) && isset($_GET['column']) && in_array($_GET['column'], $allowed_columns) && in_array(strtoupper($_GET['direction']), array('ASC', 'DESC')))
			{
				$column = $_GET['column'];
				$direction = strtoupper($_GET['direction']);
			}

			$total = $pjEquipmentModel->findCount()->getData();
			$rowCount = isset($_GET['rowCount']) && (int) $_GET['rowCount'] > 0 ? (int) $_GET['rowCount'] : 100;
			$pages = ceil($total / $rowCount);
			$page = isset($_GET['page']) && (int) $_GET['page'] > 0 ? intval($_GET['page']) : 1;
			$offset = ((int) $page - 1) * $rowCount;
			if ($page > $pages)
			{
				$page = $pages;
			}
			
			$data = $pjEquipmentModel
				->select('t1.*, t2.content AS title')
				->orderBy("$column $direction")
				->limit($rowCount, $offset)
				->findAll()
				->getData();

			$price_per = array();
			$price_per['booking'] = __('lblPricePerBooking', true);
			$price_per['hour'] = __('lblPricePerHour', true);
			$_yesno = __('_yesno', true);
			foreach($data as $k => $v)
			{
				$v['price'] = pjUtil::formatCurrencySign($v['price'], $this->option_arr['o_currency']) . ' ' . $price_per[$v['price_per']];
				$v['multi_units'] = $_yesno[$v['multi_units']];
				$data[$k] = $v;
			}
			
			pjAppController::jsonResponse(compact('data', 'total', 'pages', 'page', 'rowCount', 'column', 'direction'));
		}
		exit;
	}
		
	public function pjActionIndex()
	{
		$this->checkLogin();
		
		if ($this->isAdmin())
		{
			$this->appendJs('jquery.datagrid.js', PJ_FRAMEWORK_LIBS_PATH . 'pj/js/');
			$this->appendJs('pjAdminEquipment.js');
		} else {
			$this->set('status', 2);
		}
	}
	
	public function pjActionSaveEquipment()
	{
		$this->setAjax(true);
	
		if ($this->isXHR())
		{
			$pjEquipmentModel = pjEquipmentModel::factory();
			if (!in_array($_POST['column'], $pjEquipmentModel->getI18n()))
			{
				$pjEquipmentModel->where('id', $_GET['id'])->limit(1)->modifyAll(array($_POST['column'] => $_POST['value']));
			} else {
				pjMultiLangModel::factory()->updateMultiLang(array($this->getLocaleId() => array($_POST['column'] => $_POST['value'])), $_GET['id'], 'pjEquipment', 'data');
			}
		}
		exit;
	}
	
	public function pjActionUpdate()
	{
		$this->checkLogin();

		if ($this->isAdmin())
		{
			if (isset($_POST['equipment_update']))
			{
				$pjEquipmentModel = pjEquipmentModel::factory();
				
				$arr = $pjEquipmentModel->find($_POST['id'])->getData();
				if (empty($arr))
				{
					pjUtil::redirect($_SERVER['PHP_SELF'] . "?controller=pjAdminEquipment&action=pjActionIndex&err=AEQ08");
				}
				
				$data = array();
				if(isset($_POST['multi_units']))
				{
					$data['multi_units'] = 'T';
				}else{
					$data['multi_units'] = 'F';
				}
				$pjEquipmentModel->reset()->where('id', $_POST['id'])->limit(1)->modifyAll(array_merge($_POST, $data));
				if (isset($_POST['i18n']))
				{
					pjMultiLangModel::factory()->updateMultiLang($_POST['i18n'], $_POST['id'], 'pjEquipment', 'data');
				}
				
				pjUtil::redirect(PJ_INSTALL_URL . "index.php?controller=pjAdminEquipment&action=pjActionIndex&err=AEQ01");
			} else {
				$arr = pjEquipmentModel::factory()->find($_GET['id'])->getData();
				if (count($arr) === 0)
				{
					pjUtil::redirect(PJ_INSTALL_URL. "index.php?controller=pjAdminEquipment&action=pjActionIndex&err=AEQ08");
				}
				$arr['i18n'] = pjMultiLangModel::factory()->getMultiLang($arr['id'], 'pjEquipment');
				$this->set('arr', $arr);
				
				$locale_arr = pjLocaleModel::factory()->select('t1.*, t2.file')
					->join('pjLocaleLanguage', 't2.iso=t1.language_iso', 'left')
					->where('t2.file IS NOT NULL')
					->orderBy('t1.sort ASC')->findAll()->getData();
				
				$lp_arr = array();
				foreach ($locale_arr as $item)
				{
					$lp_arr[$item['id']."_"] = $item['file']; 
				}
				$this->set('lp_arr', $locale_arr);
				$this->set('locale_str', pjAppController::jsonEncode($lp_arr));
				
				$this->appendJs('jquery.validate.min.js', PJ_THIRD_PARTY_PATH . 'validate/');
				$this->appendJs('jquery.multilang.js', PJ_FRAMEWORK_LIBS_PATH . 'pj/js/');
				$this->appendJs('jquery.tipsy.js', PJ_THIRD_PARTY_PATH . 'tipsy/');
				$this->appendCss('jquery.tipsy.css', PJ_THIRD_PARTY_PATH . 'tipsy/');
				$this->appendJs('jquery.datagrid.js', PJ_FRAMEWORK_LIBS_PATH . 'pj/js/');
				$this->appendJs('pjAdminEquipment.js');
			}
		} else {
			$this->set('status', 2);
		}
	}
}
?>