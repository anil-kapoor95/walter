<?php
if (!defined("ROOT_PATH"))
{
	header("HTTP/1.1 403 Forbidden");
	exit;
}
class pjAdminLayouts extends pjAdmin
{
	public function pjActionCreate()
	{
		$this->checkLogin();
		
		if ($this->isAdmin())
		{
			$post_max_size = pjUtil::getPostMaxSize();
			if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_SERVER['CONTENT_LENGTH']) && (int) $_SERVER['CONTENT_LENGTH'] > $post_max_size)
			{
				pjUtil::redirect(PJ_INSTALL_URL . "index.php?controller=pjAdminLayouts&action=pjActionIndex&err=ALY05");
			}
			if (isset($_POST['layout_create']))
			{
				$pjLayoutModel = pjLayoutModel::factory();
				
				$id = $pjLayoutModel->setAttributes($_POST)->insert()->getInsertId();
				
				if ($id !== false && (int) $id > 0)
				{
					$err = 'ALY03';
					
					$pjMultiLangModel = pjMultiLangModel::factory();
					$pjMultiLangModel->saveMultiLang($_POST['i18n'], $id, 'pjLayout', 'data');
					
					if (isset($_FILES['image']))
					{
						if($_FILES['image']['error'] == 0)
						{
							if(getimagesize($_FILES['image']["tmp_name"]) != false)
							{
								$Image = new pjImage();
								if ($Image->getErrorCode() !== 200)
								{
									$Image->setAllowedTypes(array('image/png', 'image/gif', 'image/jpg', 'image/jpeg', 'image/pjpeg'));
									if ($Image->load($_FILES['image']))
									{
										$resp = $Image->isConvertPossible();
										if ($resp['status'] === true)
										{
											$hash = md5(uniqid(rand(), true));
											$image_path = PJ_UPLOAD_PATH . 'rooms/layouts/' . $id . '_' . $hash . '.' . $Image->getExtension();
											
											$data = array();
											
											$data['image'] = $image_path;
											
											$Image->loadImage($_FILES['image']["tmp_name"]);
											$Image->saveImage($image_path);
												
											$pjLayoutModel->reset()->where('id', $id)->limit(1)->modifyAll($data);
										}
									}
								}
							}else{
								$err = 'ALY09';
							}
						}else if($_FILES['image']['error'] != 4){
							$err = 'ALY09';
						}
					}
					if($err != 'ALY03')
					{
						pjUtil::redirect($_SERVER['PHP_SELF'] . "?controller=pjAdminLayouts&action=pjActionIndex&err=ALY09");
					}
					pjUtil::redirect($_SERVER['PHP_SELF'] . "?controller=pjAdminLayouts&action=pjActionIndex&err=$err");
				} else {
					$err = 'ALY04';
					pjUtil::redirect($_SERVER['PHP_SELF'] . "?controller=pjAdminLayouts&action=pjActionIndex&err=$err");
				}
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
				$this->appendJs('pjAdminLayouts.js');
			}
		} else {
			$this->set('status', 2);
		}
	}
		
	public function pjActionDeleteLayout()
	{
		$this->setAjax(true);
	
		if ($this->isXHR())
		{
			$response = array();
			$pjLayoutModel = pjLayoutModel::factory();
			$arr = $pjLayoutModel->find($_GET['id'])->getData();
			
			if ($pjLayoutModel->setAttributes(array('id' => $_GET['id']))->erase()->getAffectedRows() == 1)
			{
				if (file_exists(PJ_INSTALL_PATH . $arr['image'])) 
				{
					@unlink(PJ_INSTALL_PATH . $arr['image']);
				}
				
				pjMultiLangModel::factory()->where('model', 'pjLayout')->where('foreign_id', $_GET['id'])->eraseAll();
				pjRoomLayoutModel::factory()->where('layout_id', $_GET['id'])->eraseAll();
								
				$response['code'] = 200;
			} else {
				$response['code'] = 100;
			}
			pjAppController::jsonResponse($response);
		}
		exit;
	}
	
	public function pjActionDeleteLayoutBulk()
	{
		$this->setAjax(true);
	
		if ($this->isXHR())
		{
			if (isset($_POST['record']) && count($_POST['record']) > 0)
			{
				$pjLayoutModel = pjLayoutModel::factory();
				$arr = $pjLayoutModel->whereIn('id', $_POST['record'])->findAll()->getData();
				foreach($arr as $v)
				{
					if (file_exists(PJ_INSTALL_PATH . $v['image'])) {
						@unlink(PJ_INSTALL_PATH . $v['image']);
					}
				}
				
				$pjLayoutModel->reset()->whereIn('id', $_POST['record'])->eraseAll();
				pjMultiLangModel::factory()->where('model', 'pjLayout')->whereIn('foreign_id', $_POST['record'])->eraseAll();
				
				pjRoomLayoutModel::factory()->whereIn('layout_id', $_POST['record'])->eraseAll();
			}
		}
		exit;
	}
	
	public function pjActionGetLayout()
	{
		$this->setAjax(true);
	
		if ($this->isXHR())
		{
			$pjLayoutModel = pjLayoutModel::factory()
				->join('pjMultiLang', "t2.foreign_id = t1.id AND t2.model = 'pjLayout' AND t2.locale = '".$this->getLocaleId()."' AND t2.field = 'title'", 'left');
			
			if (isset($_GET['q']) && !empty($_GET['q']))
			{
				$q = pjObject::escapeString($_GET['q']);
				$pjLayoutModel->where('t2.content LIKE', "%$q%");
			}
			if (isset($_GET['status']) && !empty($_GET['status']) && in_array($_GET['status'], array('T', 'F')))
			{
				$pjLayoutModel->where('t1.status', $_GET['status']);
			}

			$column = 'id';
			$direction = 'ASC';
			$allowed_columns = array('image', 'title', 'rooms');
			if (isset($_GET['direction']) && isset($_GET['column']) && in_array($_GET['column'], $allowed_columns) && in_array(strtoupper($_GET['direction']), array('ASC', 'DESC')))
			{
				$column = $_GET['column'];
				$direction = strtoupper($_GET['direction']);
			}

			$total = $pjLayoutModel->findCount()->getData();
			$rowCount = isset($_GET['rowCount']) && (int) $_GET['rowCount'] > 0 ? (int) $_GET['rowCount'] : 100;
			$pages = ceil($total / $rowCount);
			$page = isset($_GET['page']) && (int) $_GET['page'] > 0 ? intval($_GET['page']) : 1;
			$offset = ((int) $page - 1) * $rowCount;
			if ($page > $pages)
			{
				$page = $pages;
			}
			
			$data = $pjLayoutModel
				->select('t1.*, t2.content AS title')
				->orderBy("$column $direction")
				->limit($rowCount, $offset)
				->findAll()
				->getData();

			$layout_id_arr = $pjLayoutModel->limit($rowCount, $offset)->findAll()->getDataPair(null, 'id');
			$room_arr = array();
			if(!empty($layout_id_arr))
			{
				$temp_room_arr = pjRoomLayoutModel::factory()
					->select('t1.*, t2.content as room')
					->join('pjMultiLang', "t2.foreign_id = t1.room_id AND t2.model = 'pjRoom' AND t2.locale = '".$this->getLocaleId()."' AND t2.field = 'title'", 'left')
					->whereIn('t1.layout_id', $layout_id_arr)
					->findAll()
					->getData();
				foreach($temp_room_arr as $k => $v)
				{
					$room_arr[$v['layout_id']][] = $v['room'];
				}
			}
			foreach($data as $k => $v)
			{
				$v['rooms'] = isset($room_arr[$v['id']]) && count($room_arr[$v['id']]) > 0 ? join("<br/>", $room_arr[$v['id']]) : __('lblNA', true); 
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
			$this->appendJs('pjAdminLayouts.js');
		} else {
			$this->set('status', 2);
		}
	}
	
	public function pjActionSaveLayout()
	{
		$this->setAjax(true);
	
		if ($this->isXHR())
		{
			$pjLayoutModel = pjLayoutModel::factory();
			if (!in_array($_POST['column'], $pjLayoutModel->getI18n()))
			{
				$pjLayoutModel->where('id', $_GET['id'])->limit(1)->modifyAll(array($_POST['column'] => $_POST['value']));
			} else {
				pjMultiLangModel::factory()->updateMultiLang(array($this->getLocaleId() => array($_POST['column'] => $_POST['value'])), $_GET['id'], 'pjLayout', 'data');
			}
		}
		exit;
	}
	
	public function pjActionUpdate()
	{
		$this->checkLogin();

		if ($this->isAdmin())
		{
			$post_max_size = pjUtil::getPostMaxSize();
			if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_SERVER['CONTENT_LENGTH']) && (int) $_SERVER['CONTENT_LENGTH'] > $post_max_size)
			{
				pjUtil::redirect(PJ_INSTALL_URL . "index.php?controller=pjAdminLayouts&action=pjActionIndex&err=ALY06");
			}
			if (isset($_POST['layout_update']))
			{
				$pjLayoutModel = pjLayoutModel::factory();
				
				$err = 'ALY01';
				
				$arr = $pjLayoutModel->find($_POST['id'])->getData();
				if (empty($arr))
				{
					pjUtil::redirect($_SERVER['PHP_SELF'] . "?controller=pjAdminLayouts&action=pjActionIndex&err=ALY08");
				}
				
				$data = array();
				
				if (isset($_FILES['image']))
				{
					if($_FILES['image']['error'] == 0)
					{
						if(getimagesize($_FILES['image']["tmp_name"]) != false)
						{
							$Image = new pjImage();
							if ($Image->getErrorCode() !== 200)
							{
								$Image->setAllowedTypes(array('image/png', 'image/gif', 'image/jpg', 'image/jpeg', 'image/pjpeg'));
								if ($Image->load($_FILES['image']))
								{
									$resp = $Image->isConvertPossible();
									if ($resp['status'] === true)
									{
										if(!empty($arr['image']))
										{
											@unlink(PJ_INSTALL_PATH . $arr['image']);
										}
										
										$hash = md5(uniqid(rand(), true));
										$image_path = PJ_UPLOAD_PATH . 'rooms/layouts/' . $_POST['id'] . '_' . $hash . '.' . $Image->getExtension();
										
										$Image->loadImage($_FILES['image']["tmp_name"]);
										$Image->saveImage($image_path);
										
										$data['image'] = $image_path;
									}
								}
							}
						}else{
							$err = 'ALY10';
						}
					}else if($_FILES['image']['error'] != 4){
						$err = 'ALY10';
					}
				}
				
				$pjLayoutModel->reset()->where('id', $_POST['id'])->limit(1)->modifyAll(array_merge($_POST, $data));
				if (isset($_POST['i18n']))
				{
					pjMultiLangModel::factory()->updateMultiLang($_POST['i18n'], $_POST['id'], 'pjLayout', 'data');
				}
				
				if($err == 'ALY01')
				{
					pjUtil::redirect(PJ_INSTALL_URL . "index.php?controller=pjAdminLayouts&action=pjActionIndex&err=ALY01");
				}else{
					pjUtil::redirect(PJ_INSTALL_URL . "index.php?controller=pjAdminLayouts&action=pjActionIndex&err=ALY10");
				}
			} else {
				$arr = pjLayoutModel::factory()->find($_GET['id'])->getData();
				if (count($arr) === 0)
				{
					pjUtil::redirect(PJ_INSTALL_URL. "index.php?controller=pjAdminLayouts&action=pjActionIndex&err=ALY08");
				}
				$arr['i18n'] = pjMultiLangModel::factory()->getMultiLang($arr['id'], 'pjLayout');
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
				$this->appendJs('pjAdminLayouts.js');
			}
		} else {
			$this->set('status', 2);
		}
	}
}
?>