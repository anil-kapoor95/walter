<?php
include_once dirname(__FILE__) . '/elements/header.php'; 

$STORE = $_SESSION[$controller->defaultStore];

$image_url = PJ_INSTALL_URL . PJ_IMG_PATH . 'frontend/190x126.png';
if(!empty($tpl['arr']['image_source']) && file_exists(PJ_INSTALL_PATH . $tpl['arr']['image_source']))
{
	$image_url = PJ_INSTALL_URL . $tpl['arr']['image_source'];
}
$index = pjObject::escapeString($_GET['index']);
?>
<div class="pjMrBBody">
	<div class="row pjMrBMain">
		<aside class="col-lg-4 col-md-4 col-sm-4 col-xs-12 pjMrBAside">
			<p class="pjMrBMainTitle"><?php __('front_your_booking');?></p><!-- /.pjMrBMainTitle -->

			<div class="row pjMrBAsideProduct">
				<div class="col-lg-12 col-md-12 col-sm-12 col-xs-4 pjMrBAsideProductImage">
					<img src="<?php echo $image_url;?>" alt="" />
				</div><!-- /.col-lg-12 col-md-12 col-sm-12 col-xs-4 pjMrBAsideProductImage -->

				<div class="col-lg-12 col-md-12 col-sm-12 col-xs-8 pjMrBAsideProductContent">
					<p class="pjMrBAsideProductTitle"><?php echo pjSanitize::html($tpl['arr']['title']);?></p><!-- /.pjMrBAsideProductTitle -->

					<dl class="dl-horizontal pjMrBAsideProductMeta">
						<dt><?php __('front_capacity');?>: </dt>
						<dd><?php echo $tpl['arr']['capacity'];?> <?php $tpl['arr']['capacity'] != 1 ? __('front_people') : __('front_person');?></dd>
					</dl>
					<?php
					if($STORE['book_by'] == 'multiday')
					{
						$price = pjUtil::formatCurrencySign($tpl['arr']['price_per_day'], $tpl['option_arr']['o_currency']) . ' ' . __('front_per_day', true);
						?>
						<dl class="dl-horizontal pjMrBAsideProductMeta">
							<dt><?php __('front_price');?>: </dt>
							<dd><?php echo $price;?></dd>
						</dl><!-- /.dl-horizontal pjMrBRoomInnerMeta -->
						<?php
					}elseif($STORE['book_by'] == 'morning' || $STORE['book_by'] == 'afternoon' || $STORE['book_by'] == 'evening'){
						$price = pjUtil::formatCurrencySign($tpl['arr']['price_half_day'], $tpl['option_arr']['o_currency']) . ' ' . __('front_half_day', true);
						?>
						<dl class="dl-horizontal pjMrBAsideProductMeta">
							<dt><?php __('front_price');?>: </dt>
							<dd><?php echo $price;?></dd>
						</dl><!-- /.dl-horizontal pjMrBRoomInnerMeta -->
						<?php
					}elseif($STORE['book_by'] == 'morningafternoon'){
						$price = pjUtil::formatCurrencySign($tpl['arr']['price_morning_afternoon'], $tpl['option_arr']['o_currency']) . ' ' . __('front_half_day', true);
						?>
						<dl class="dl-horizontal pjMrBAsideProductMeta">
							<dt><?php __('front_price');?>: </dt>
							<dd><?php echo $price;?></dd>
						</dl><!-- /.dl-horizontal pjMrBRoomInnerMeta -->
						<?php
					}elseif($STORE['book_by'] == 'afternoonevening'){
						$price = pjUtil::formatCurrencySign($tpl['arr']['price_afternoon_evening'], $tpl['option_arr']['o_currency']) . ' ' . __('front_half_day', true);
						?>
						<dl class="dl-horizontal pjMrBAsideProductMeta">
							<dt><?php __('front_price');?>: </dt>
							<dd><?php echo $price;?></dd>
						</dl><!-- /.dl-horizontal pjMrBRoomInnerMeta -->
						<?php
					}else{
						$price = pjUtil::formatCurrencySign($tpl['arr']['price_per_hour'], $tpl['option_arr']['o_currency']) . ' ' . __('front_per_hour', true);
						?>
						<dl class="dl-horizontal pjMrBAsideProductMeta">
							<dt><?php __('front_price');?>: </dt>
							<dd><?php echo $price;?></dd>
						</dl><!-- /.dl-horizontal pjMrBRoomInnerMeta -->
						<?php
					}
					?>
					<dl class="dl-horizontal pjMrBAsideProductMeta">
						<dt><?php $STORE['book_by'] == 'multiday' ? __('front_start_date') : __('front_date');?>: </dt>
						<dd><?php echo date($tpl['option_arr']['o_date_format'], strtotime($STORE['start_date']));?></dd>
					</dl><!-- /.dl-horizontal pjMrBAsideProductMeta -->
					<?php
					if($STORE['book_by'] == 'multiday')
					{
						?>
						<dl class="dl-horizontal pjMrBAsideProductMeta">
							<dt><?php __('front_end_date');?>: </dt>
							<dd><?php echo date($tpl['option_arr']['o_date_format'], strtotime($STORE['end_date']));?></dd>
						</dl><!-- /.dl-horizontal pjMrBAsideProductMeta -->
						<?php
					}
					if($STORE['book_by'] == 'hour')
					{ 
						$selected_slots = isset($STORE['slots']) ? $STORE['slots'] : array();
						$slot_pairs = array();
						foreach($selected_slots as $pair)
						{
							list($start_ts, $end_ts) = explode("|", $pair);
							$slot_pairs[] = array((int) $start_ts, (int) $end_ts);
						}
						usort($slot_pairs, function($a, $b){ return $a[0] - $b[0]; });
						$slot_ranges = array();
						foreach($slot_pairs as $pair)
						{
							$last = count($slot_ranges) - 1;
							if($last >= 0 && $slot_ranges[$last][1] == $pair[0])
							{
								$slot_ranges[$last][1] = $pair[1];
							}else{
								$slot_ranges[] = $pair;
							}
						}
						?>
						<dl class="dl-horizontal pjMrBAsideProductMeta">
							<dt><?php __('front_from_to');?>: </dt>
							<?php
							$slots_arr = array();
							foreach($slot_ranges as $range)
							{
								$slots_arr[] = date($tpl['option_arr']['o_time_format'],$range[0]) . ' - ' . date($tpl['option_arr']['o_time_format'],$range[1]);
							}
							?>
							<dd><?php echo join('<br/>', $slots_arr);?></dd>
						</dl><!-- /.dl-horizontal pjMrBAsideProductMeta -->
						<?php
					} 
					?>

					<dl class="dl-horizontal pjMrBAsideProductMeta">
						<dt><?php __('front_attendees');?></dt>
						<dd><?php echo $STORE['attendees'];?></dd>
					</dl><!-- /.dl-horizontal pjMrBAsideProductMeta -->
				</div><!-- /.col-lg-12 col-md-12 col-sm-12 col-xs-8 pjMrBAsideProductContent -->
			</div><!-- /.row pjMrBAsideProduct -->
		</aside><!-- /.col-lg-4 col-md-4 col-sm-4 col-xs-12 pjMrBAside -->

		<div class="col-lg-8 col-md-8 col-sm-8 col-xs-12 pjMrBContent">
			<p class="pjMrBMainTitle"><?php __('front_room_setup');?></p><!-- /.pjMrBMainTitle -->

			<div class="pjMrBForm pjMrBFormRoomSetup">
				<form id="pjMrbEquipmentForm_<?php echo $index;?>" action="#" method="post">
					<?php
					if(!empty($tpl['layout_arr']))
					{ 
						?>
						<section class="pjMrBFormSection">
							<p class="pjMrBFormSectionTitle"><?php __('front_room_layout')?> </p><!-- /.pjMrBFormSectionTitle -->
	
							<div class="pjMrBFormSectionContent">
								<div class="row pjMrBCustomRadiosLayout">
									<?php
									foreach($tpl['layout_arr'] as $k => $v)
									{ 
										if(!empty($v['image']) && file_exists(PJ_INSTALL_PATH . $v['image']))
										{
											?>
											<div class="col-lg-4 col-md-4 col-sm-6 col-xs-4">
												<div class="pjMrBRadio pjMrBCustomRadio">
													<input type="radio" id="pjMrBCustomLayout<?php echo $v['layout_id'];?>" name="layout_id" value="<?php echo $v['layout_id'];?>"<?php echo isset($STORE['layout_id']) ? ($STORE['layout_id'] == $v['layout_id'] ? ' checked="checked"' : NULL) : NULL;?> class="pjMrbLayoutRadio" autocomplete="off" />
														
													<label for="pjMrBCustomLayout<?php echo $v['layout_id'];?>">
														<span class="pjMrBCustomRadioImage">
															<img src="<?php echo PJ_INSTALL_URL . $v['image'];?>" alt="" class="pjMrBCustomRadioImageNotChecked" />
														</span>
														
														<span class="pjMrBCustomRadioContent">
															<span class="pjMrBCustomRadioContentInner">
																<strong><?php echo pjSanitize::html($v['title']);?></strong>
															</span>
														</span>
													</label>
												</div><!-- /.pjMrBRadio pjMrBCustomRadio -->
											</div><!-- /.col-lg-4 col-md-4 col-sm-6 col-xs-4 -->
											<?php
										}
									} 
									?>
								</div><!-- /.row pjMrBCustomRadiosLayout -->
								<div class="row pjMrBCustomRadiosLayout" style="display: none;">
									<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
										<div id="pjMrbLayoutMessage" class="text-danger"><?php __('front_layout_error_msg');?></div>
									</div><!-- /.col-lg-9 col-lg-offset-3 col-md-9 col-md-offset-3 col-sm-12 col-xs-12 pjMrBActions -->
								</div><!-- /.form-group -->
							</div><!-- /.pjMrBFormSectionContent -->
						</section><!-- /.pjMrBFormSection -->
						<?php
					}
					if(!empty($tpl['equipment_arr']))
					{ 
						?>
						<section class="pjMrBFormSection">
							<p class="pjMrBFormSectionTitle"><?php __('front_equipment');?> </p><!-- /.pjMrBFormSectionTitle -->
	
							<div class="pjMrBFormSectionContent">
								<ul class="list-unstyled pjMrBCustomCheckboxesExtras">
									<?php
									$price_per = array();
									$price_per['booking'] = __('lblPricePerBooking', true);
									$price_per['hour'] = __('lblPricePerHour', true);
									
									$selected_equipment_id_arr = isset($STORE['equipment_id']) ? $STORE['equipment_id'] : array();
									$units = isset($STORE['units']) ? $STORE['units'] : array();
									
									foreach($tpl['equipment_arr'] as $k => $v)
									{ 
										?>
										<li>
											<div class="pjMrBCheckbox pjMrBCustomCheckbox">
												<input type="checkbox" id="pjMrBCheckboxExtra<?php echo $v['id'];?>"<?php echo array_key_exists($v['id'], $selected_equipment_id_arr) ? ' checked="checked"' : NULL;?> class="pjMrbEquipmentCheckbox" name="equipment_id[<?php echo $v['id'];?>]" value="<?php echo $v['price'];?>|<?php echo $v['price_per'];?>" autocomplete="off" />
		
												<div class="row">
													<div class="col-lg-5 col-md-5 col-sm-12 col-xs-5">
														<label for="pjMrBCheckboxExtra<?php echo $v['id'];?>">
															<span class="pjMrBCustomCheckboxFake">
																<span class="glyphicon glyphicon-ok" aria-hidden="true"></span>
															</span>
		
															<span><?php echo pjSanitize::html($v['title']);?></span>
														</label>
													</div><!-- /.col-lg-5 col-md-5 col-sm-12 col-xs-5 -->
		
													<div class="col-lg-4 col-md-4 col-sm-6 col-xs-4">
														<?php
														if($v['multi_units'] == 'T')
														{ 
															?>
															<div class="pjMrBSpinner">
																<input type="text" class="form-control pjMrbUnits" autocomplete="off" name="units[<?php echo $v['id'];?>]" value="<?php echo isset($units[$v['id']]) ? $units[$v['id']] : 1;?>" data-min="1" />
																
																<a href="#" class="pjMrBSpinnerBtn pjMrBSpinnerBtnUp">
																	<span class="caret"></span>
																</a>
			
																<a href="#" class="pjMrBSpinnerBtn pjMrBSpinnerBtnDown">
																	<span class="caret"></span>
																</a>
															</div><!-- /.pjMrBSpinner -->
															<p><?php __('front_units');?></p>
															<?php
														}else{
															?>
															<p><?php echo isset($units[$v['id']]) ? $units[$v['id']] : 1;?></p>
															<input type="hidden" name="units[<?php echo $v['id'];?>]" value="<?php echo isset($units[$v['id']]) ? $units[$v['id']] : 1;?>"/>
															<?php
														} 
														?>
													</div><!-- /.col-lg-4 col-md-4 col-sm-12 col-xs-3 -->
		
													<div class="col-lg-3 col-md-3 col-sm-6 col-xs-3">
														<p><strong><?php echo pjUtil::formatCurrencySign($v['price'], $tpl['option_arr']['o_currency']) . ' ' . $price_per[$v['price_per']];?></strong></p>
													</div><!-- /.col-lg-3 col-md-3 col-sm-12 col-xs-3 -->
												</div><!-- /.row -->
											</div><!-- /.pjMrBCheckbox pjMrBCustomCheckbox -->
										</li>
										<?php
									} 
									?>
								</ul><!-- /.list-unstyled pjMrBCustomCheckboxesExtras -->
							</div><!-- /.pjMrBFormSectionContent -->
						</section><!-- /.pjMrBFormSection -->
						<?php
					} 
					?>

					<div class="pjMrBFormActions">
						<a href="#" data-load="loadBook" class="btn btn-default pull-left pjMrbMenuItem"><?php __('front_btn_back');?></a>
						<button type="submit" class="btn btn-primary pull-right"><?php __('front_btn_next');?></button>
					</div><!-- /.pjMrBFormActions -->
				</form>
			</div><!-- /.pjMrBForm pjMrBFormRoomSetup -->
		</div><!-- /.col-lg-8 col-md-8 col-sm-8 col-xs-12 pjMrBContent -->
	</div><!-- /.row pjMrBMain -->
</div><!-- /.pjMrBBody -->