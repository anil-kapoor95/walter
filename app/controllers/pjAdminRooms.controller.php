<?php
if (!defined("ROOT_PATH"))
{
	header("HTTP/1.1 403 Forbidden");
	exit;
}
class pjAdminRooms extends pjAdmin
{
	public function pjActionCreate()
	{
		$this->checkLogin();
		
		if ($this->isAdmin())
		{
			$post_max_size = pjUtil::getPostMaxSize();
			if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_SERVER['CONTENT_LENGTH']) && (int) $_SERVER['CONTENT_LENGTH'] > $post_max_size)
			{
				pjUtil::redirect(PJ_INSTALL_URL . "index.php?controller=pjAdminRooms&action=pjActionIndex&err=ARM05");
			}
			if (isset($_POST['room_create']))
			{
				$pjRoomModel = pjRoomModel::factory();
				
				$data = array();
				if(isset($_POST['book_by_multiday']))
				{
					$data['book_by_multiday'] = 'T';
					$data['price_per_day'] = $_POST['price_per_day'];
				}else{
					$data['book_by_multiday'] = 'F';
					$data['price_per_day'] = ':NULL';
				}
				if(isset($_POST['book_by_halfday']))
				{
					$data['book_by_halfday'] = 'T';
					$data['price_half_day'] = $_POST['price_half_day'];
					$data['price_morning_afternoon'] = $_POST['price_morning_afternoon'];
					$data['price_afternoon_evening'] = $_POST['price_afternoon_evening'];
				}else{
					$data['book_by_halfday'] = 'F';
					$data['price_half_day'] = ':NULL';
					$data['price_morning_afternoon'] = ':NULL';
					$data['price_afternoon_evening'] = ':NULL';
				}
				if(isset($_POST['book_by_hour']))
				{
					$data['book_by_hour'] = 'T';
					$data['price_per_hour'] = $_POST['price_per_hour'];
				}else{
					$data['book_by_hour'] = 'F';
					$data['price_per_hour'] = ':NULL';
				}

				$id = $pjRoomModel->setAttributes(array_merge($_POST, $data))->insert()->getInsertId();
				
				if ($id !== false && (int) $id > 0)
				{
					$err = 'ARM03';
					
					$pjMultiLangModel = pjMultiLangModel::factory();
					$pjMultiLangModel->saveMultiLang($_POST['i18n'], $id, 'pjRoom', 'data');
					
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
											$image_path = PJ_UPLOAD_PATH . 'rooms/' . $id . '_' . $hash . '.' . $Image->getExtension();
											$thumb_path = PJ_UPLOAD_PATH . 'rooms/thumbs/' . $id . '_' . $hash . '.' . $Image->getExtension();
											
											$data = array();
											
											$data['image_source'] = $image_path;
											$data['image_thumb'] = $thumb_path;
											
											$Image->loadImage($_FILES['image']["tmp_name"]);
											$Image->saveImage($image_path);
												
											$Image->loadImage($_FILES['image']["tmp_name"]);
											$Image->resizeSmart(225, 150);
											$Image->saveImage($thumb_path);
												
											$pjRoomModel->reset()->where('id', $id)->limit(1)->modifyAll($data);
										}
									}
								}
							}else{
								$err = 'ARM09';
							}
						}else if($_FILES['image']['error'] != 4){
							$err = 'ARM09';
						}
					}
					$pjRoomLayoutModel = pjRoomLayoutModel::factory();
					if (isset($_POST['layout_id']) && is_array($_POST['layout_id']) && count($_POST['layout_id']) > 0)
					{
						$pjRoomLayoutModel->begin();
						foreach ($_POST['layout_id'] as $layout_id)
						{
							$pjRoomLayoutModel
							->reset()
							->set('room_id', $id)
							->set('layout_id', $layout_id)
							->insert();
						}
						$pjRoomLayoutModel->commit();
					}
					if($err != 'ARM03')
					{
						pjUtil::redirect($_SERVER['PHP_SELF'] . "?controller=pjAdminRooms&action=pjActionIndex&err=ARM09");
					}
					pjUtil::redirect($_SERVER['PHP_SELF'] . "?controller=pjAdminRooms&action=pjActionIndex&err=$err");
				} else {
					$err = 'ARM04';
					pjUtil::redirect($_SERVER['PHP_SELF'] . "?controller=pjAdminRooms&action=pjActionIndex&err=$err");
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
		
				$layout_arr = pjLayoutModel::factory()
					->select('t1.*, t2.content as title')
					->join('pjMultiLang', "t2.foreign_id = t1.id AND t2.model = 'pjLayout' AND t2.locale = '".$this->getLocaleId()."' AND t2.field = 'title'", 'left')
					->findAll()->getData();
				$this->set('layout_arr', $layout_arr);
				
				$this->appendJs('jquery.validate.min.js', PJ_THIRD_PARTY_PATH . 'validate/');
				$this->appendJs('jquery.multilang.js', PJ_FRAMEWORK_LIBS_PATH . 'pj/js/');
				$this->appendJs('jquery.tipsy.js', PJ_THIRD_PARTY_PATH . 'tipsy/');
				$this->appendCss('jquery.tipsy.css', PJ_THIRD_PARTY_PATH . 'tipsy/');
				$this->appendJs('jquery.multiselect.min.js', PJ_THIRD_PARTY_PATH . 'multiselect/');
				$this->appendCss('jquery.multiselect.css', PJ_THIRD_PARTY_PATH . 'multiselect/');
				$this->appendJs('pjAdminRooms.js');
			}
		} else {
			$this->set('status', 2);
		}
	}
		
	public function pjActionDeleteRoom()
	{
		$this->setAjax(true);
	
		if ($this->isXHR())
		{
			$response = array();
			$pjRoomModel = pjRoomModel::factory();
			$arr = $pjRoomModel->find($_GET['id'])->getData();
			
			if ($pjRoomModel->setAttributes(array('id' => $_GET['id']))->erase()->getAffectedRows() == 1)
			{
				if (file_exists(PJ_INSTALL_PATH . $arr['image_source'])) 
				{
					@unlink(PJ_INSTALL_PATH . $arr['image_source']);
				}
				if (file_exists(PJ_INSTALL_PATH . $arr['image_thumb']))
				{
					@unlink(PJ_INSTALL_PATH . $arr['image_thumb']);
				}
				
				pjMultiLangModel::factory()->where('model', 'pjRoom')->where('foreign_id', $_GET['id'])->eraseAll();
				pjRoomLayoutModel::factory()->where('room_id', $_GET['id'])->eraseAll();
								
				$response['code'] = 200;
			} else {
				$response['code'] = 100;
			}
			pjAppController::jsonResponse($response);
		}
		exit;
	}
	
	public function pjActionDeleteRoomBulk()
	{
		$this->setAjax(true);
	
		if ($this->isXHR())
		{
			if (isset($_POST['record']) && count($_POST['record']) > 0)
			{
				$pjRoomModel = pjRoomModel::factory();
				$arr = $pjRoomModel->whereIn('id', $_POST['record'])->findAll()->getData();
				foreach($arr as $v)
				{
					if (file_exists(PJ_INSTALL_PATH . $v['image_source'])) {
						@unlink(PJ_INSTALL_PATH . $v['image_source']);
					}
					if (file_exists(PJ_INSTALL_PATH . $v['image_thumb'])) {
						@unlink(PJ_INSTALL_PATH . $v['image_thumb']);
					}
				}
				
				$pjRoomModel->reset()->whereIn('id', $_POST['record'])->eraseAll();
				pjMultiLangModel::factory()->where('model', 'pjRoom')->whereIn('foreign_id', $_POST['record'])->eraseAll();
				
				pjRoomLayoutModel::factory()->whereIn('room_id', $_POST['record'])->eraseAll();
			}
		}
		exit;
	}
	
	public function pjActionGetRoom()
	{
		$this->setAjax(true);
	
		if ($this->isXHR())
		{
			$pjRoomModel = pjRoomModel::factory()
				->join('pjMultiLang', "t2.foreign_id = t1.id AND t2.model = 'pjRoom' AND t2.locale = '".$this->getLocaleId()."' AND t2.field = 'title'", 'left');
			
			if (isset($_GET['q']) && !empty($_GET['q']))
			{
				$q = pjObject::escapeString($_GET['q']);
				$pjRoomModel->where('t2.content LIKE', "%$q%");
			}
			if (isset($_GET['status']) && !empty($_GET['status']) && in_array($_GET['status'], array('T', 'F')))
			{
				$pjRoomModel->where('t1.status', $_GET['status']);
			}

			$column = 'id';
			$direction = 'ASC';
			$allowed_columns = array('image_thumb', 'title', 'capacity', 'cnt_bookings', 'status');
			if (isset($_GET['direction']) && isset($_GET['column']) && in_array($_GET['column'], $allowed_columns) && in_array(strtoupper($_GET['direction']), array('ASC', 'DESC')))
			{
				$column = $_GET['column'];
				$direction = strtoupper($_GET['direction']);
			}

			$total = $pjRoomModel->findCount()->getData();
			$rowCount = isset($_GET['rowCount']) && (int) $_GET['rowCount'] > 0 ? (int) $_GET['rowCount'] : 100;
			$pages = ceil($total / $rowCount);
			$page = isset($_GET['page']) && (int) $_GET['page'] > 0 ? intval($_GET['page']) : 1;
			$offset = ((int) $page - 1) * $rowCount;
			if ($page > $pages)
			{
				$page = $pages;
			}
			
			$data = $pjRoomModel
				->select(sprintf("t1.*, t2.content AS title, (SELECT COUNT(`TB`.id) FROM `%1\$s` AS `TB` WHERE `TB`.room_id=t1.id) AS cnt_bookings", pjBookingModel::factory()->getTable()))
				->orderBy("$column $direction")
				->limit($rowCount, $offset)
				->findAll()
				->getData();
				
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
			$this->appendJs('pjAdminRooms.js');
		} else {
			$this->set('status', 2);
		}
	}
	
	public function pjActionSaveRoom()
	{
		$this->setAjax(true);
	
		if ($this->isXHR())
		{
			$pjRoomModel = pjRoomModel::factory();
			if (!in_array($_POST['column'], $pjRoomModel->getI18n()))
			{
				$pjRoomModel->where('id', $_GET['id'])->limit(1)->modifyAll(array($_POST['column'] => $_POST['value']));
			} else {
				pjMultiLangModel::factory()->updateMultiLang(array($this->getLocaleId() => array($_POST['column'] => $_POST['value'])), $_GET['id'], 'pjRoom', 'data');
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
				pjUtil::redirect(PJ_INSTALL_URL . "index.php?controller=pjAdminRooms&action=pjActionIndex&err=ARM06");
			}
			if (isset($_POST['room_update']))
			{
				$pjRoomModel = pjRoomModel::factory();
				
				$err = 'ARM01';
				
				$arr = $pjRoomModel->find($_POST['id'])->getData();
				if (empty($arr))
				{
					pjUtil::redirect($_SERVER['PHP_SELF'] . "?controller=pjAdminRooms&action=pjActionIndex&err=ARM08");
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
										if(!empty($arr['image_source']))
										{
											@unlink(PJ_INSTALL_PATH . $arr['image_source']);
										}
										if(!empty($arr['image_thumb']))
										{
											@unlink(PJ_INSTALL_PATH . $arr['image_thumb']);
										}
										
										$hash = md5(uniqid(rand(), true));
										$image_path = PJ_UPLOAD_PATH . 'rooms/' . $_POST['id'] . '_' . $hash . '.' . $Image->getExtension();
										$thumb_path = PJ_UPLOAD_PATH . 'rooms/thumbs/' . $_POST['id'] . '_' . $hash . '.' . $Image->getExtension();
										
										$Image->loadImage($_FILES['image']["tmp_name"]);
										$Image->saveImage($image_path);
										
										$Image->loadImage($_FILES['image']["tmp_name"]);
										$Image->resizeSmart(257, 170);
										$Image->saveImage($thumb_path);
										
										$data['image_source'] = $image_path;
										$data['image_thumb'] = $thumb_path;
									}
								}
							}
						}else{
							$err = 'ARM10';
						}
					}else if($_FILES['image']['error'] != 4){
						$err = 'ARM10';
					}
				}
				
				if(isset($_POST['book_by_multiday']))
				{
					$data['book_by_multiday'] = 'T';
					$data['price_per_day'] = $_POST['price_per_day'];
				}else{
					$data['book_by_multiday'] = 'F';
					$data['price_per_day'] = ':NULL';
				}
				if(isset($_POST['book_by_halfday']))
				{
					$data['book_by_halfday'] = 'T';
					$data['price_half_day'] = $_POST['price_half_day'];
					$data['price_morning_afternoon'] = $_POST['price_morning_afternoon'];
					$data['price_afternoon_evening'] = $_POST['price_afternoon_evening'];
				}else{
					$data['book_by_halfday'] = 'F';
					$data['price_half_day'] = ':NULL';
					$data['price_morning_afternoon'] = ':NULL';
					$data['price_afternoon_evening'] = ':NULL';
				}
				if(isset($_POST['book_by_hour']))
				{
					$data['book_by_hour'] = 'T';
					$data['price_per_hour'] = $_POST['price_per_hour'];
				}else{
					$data['book_by_hour'] = 'F';
					$data['price_per_hour'] = ':NULL';
				}
				$pjRoomModel->reset()->where('id', $_POST['id'])->limit(1)->modifyAll(array_merge($_POST, $data));
				if (isset($_POST['i18n']))
				{
					pjMultiLangModel::factory()->updateMultiLang($_POST['i18n'], $_POST['id'], 'pjRoom', 'data');
				}
				
				$pjRoomLayoutModel = pjRoomLayoutModel::factory();
				$pjRoomLayoutModel->where('room_id', $_POST['id'])->eraseAll();
				if (isset($_POST['layout_id']) && is_array($_POST['layout_id']) && count($_POST['layout_id']) > 0)
				{
					$pjRoomLayoutModel->begin();
					foreach ($_POST['layout_id'] as $layout_id)
					{
						$pjRoomLayoutModel
							->reset()
							->set('room_id', $_POST['id'])
							->set('layout_id', $layout_id)
							->insert();
					}
					$pjRoomLayoutModel->commit();
				}
				
				if($err == 'ARM01')
				{
					pjUtil::redirect(PJ_INSTALL_URL . "index.php?controller=pjAdminRooms&action=pjActionIndex&err=ARM01");
				}else{
					pjUtil::redirect(PJ_INSTALL_URL . "index.php?controller=pjAdminRooms&action=pjActionIndex&err=ARM10");
				}
			} else {
				$arr = pjRoomModel::factory()->find($_GET['id'])->getData();
				if (count($arr) === 0)
				{
					pjUtil::redirect(PJ_INSTALL_URL. "index.php?controller=pjAdminRooms&action=pjActionIndex&err=ARM08");
				}
				$arr['i18n'] = pjMultiLangModel::factory()->getMultiLang($arr['id'], 'pjRoom');
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
				
				$layout_arr = pjLayoutModel::factory()
					->select('t1.*, t2.content as title')
					->join('pjMultiLang', "t2.foreign_id = t1.id AND t2.model = 'pjLayout' AND t2.locale = '".$this->getLocaleId()."' AND t2.field = 'title'", 'left')
					->findAll()->getData();
				$this->set('layout_arr', $layout_arr);
				
				$this->set('layout_id_arr', pjRoomLayoutModel::factory()
					->where("room_id", $_GET['id'])
					->findAll()
					->getDataPair(null, "layout_id"));
				
				$this->appendJs('jquery.validate.min.js', PJ_THIRD_PARTY_PATH . 'validate/');
				$this->appendJs('jquery.multilang.js', PJ_FRAMEWORK_LIBS_PATH . 'pj/js/');
				$this->appendJs('jquery.tipsy.js', PJ_THIRD_PARTY_PATH . 'tipsy/');
				$this->appendCss('jquery.tipsy.css', PJ_THIRD_PARTY_PATH . 'tipsy/');
				$this->appendJs('jquery.datagrid.js', PJ_FRAMEWORK_LIBS_PATH . 'pj/js/');
				$this->appendJs('jquery.multiselect.min.js', PJ_THIRD_PARTY_PATH . 'multiselect/');
				$this->appendCss('jquery.multiselect.css', PJ_THIRD_PARTY_PATH . 'multiselect/');
				$this->appendJs('pjAdminRooms.js');
			}
		} else {
			$this->set('status', 2);
		}
	}
	
	public function pjActionDeleteImage()
	{
		$this->setAjax(true);
	
		if ($this->isXHR())
		{
			$response = array();
				
			$pjRoomModel = pjRoomModel::factory();
			$arr = $pjRoomModel->find($_GET['id'])->getData();
				
			if(!empty($arr))
			{
				if(!empty($arr['image_source']))
				{
					@unlink(PJ_INSTALL_PATH . $arr['image_source']);
				}
				if(!empty($arr['image_thumb']))
				{
					@unlink(PJ_INSTALL_PATH . $arr['image_thumb']);
				}
	
				$data = array();
				$data['image_source'] = ':NULL';
				$data['image_thumb'] = ':NULL';
				$pjRoomModel->reset()->where(array('id' => $_GET['id']))->limit(1)->modifyAll($data);
	
				$response['code'] = 200;
			}else{
				$response['code'] = 100;
			}
				
			pjAppController::jsonResponse($response);
		}
	}
}
?>