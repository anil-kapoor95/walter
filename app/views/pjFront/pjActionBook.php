<?php
include_once dirname(__FILE__) . '/elements/header.php'; 

$image_url = PJ_INSTALL_URL . PJ_IMG_PATH . 'frontend/190x126.png';
if(!empty($tpl['arr']['image_source']) && file_exists(PJ_INSTALL_PATH . $tpl['arr']['image_source']))
{
	$image_url = PJ_INSTALL_URL . $tpl['arr']['image_source'];
}

$months = __('months', true);
$short_months = __('short_months', true);
ksort($months);
ksort($short_months);
$days = __('days', true);
$short_days = __('short_days', true);

$STORE = $_SESSION[$controller->defaultStore];

$min_from = date('Y-m-d', time() + (($tpl['option_arr']['o_days_earlier'] + 1) * 86400));
$min_to = $STORE['start_date'];

$start_date = date($tpl['option_arr']['o_date_format'], strtotime($STORE['start_date']));
$end_date = isset($STORE['end_date']) ? date($tpl['option_arr']['o_date_format'], strtotime($STORE['end_date'])) : date($tpl['option_arr']['o_date_format'], strtotime($STORE['start_date']) + 86400);

$index = pjObject::escapeString($_GET['index']);
?>
<div class="pjMrBBody">
	<div class="thumbnail pjMrBRoomInner">
		<div class="row">
			<div class="col-lg-3 col-md-3 col-sm-4 col-xs-5 pjMrBRoomInnerImage">
				<img src="<?php echo $image_url;?>" alt="" />
				<br/><br/>
				<dl>
					<dt><?php __('front_price');?>: </dt>
					<dd>
						<?php
						$price_arr = array();
						if($tpl['arr']['book_by_hour'] == 'T')
						{
							$price_arr[] = pjUtil::formatCurrencySign($tpl['arr']['price_per_hour'], $tpl['option_arr']['o_currency']) . ' ' . __('front_per_hour', true);
						}
						if($tpl['arr']['book_by_halfday'] == 'T')
						{
							$price_arr[] = pjUtil::formatCurrencySign($tpl['arr']['price_half_day'], $tpl['option_arr']['o_currency']) . ' ' . __('front_half_day', true);
						}
						if($tpl['arr']['book_by_multiday'] == 'T')
						{
							$price_arr[] = pjUtil::formatCurrencySign($tpl['arr']['price_per_day'], $tpl['option_arr']['o_currency']) . ' ' . __('front_per_day', true);
						}
						echo join("<br/>", $price_arr);
						?>
					</dd>
				</dl>
			</div><!-- /.col-lg-3 col-md-3 col-sm-4 col-xs-5 pjMrBRoomInnerImage -->

			<div class="col-lg-9 col-md-9 col-sm-8 col-xs-7 pjMrBRoomInnerContent">
				<div class="pjMrBForm pjMrBFormProduct">
					<form id="pjMrbBookForm_<?php echo $index;?>" action="#" method="post" class="form-horizontal">
						<header class="pjMrBFormHead">
							<p class="pjMrBRoomInnerTitle"><?php echo pjSanitize::html($tpl['arr']['title']);?></p><!-- /.pjMrBRoomInnerTitle -->
							<dl>
								<dt><?php __('front_capacity');?>: </dt>
								<dd><?php echo $tpl['arr']['capacity'];?> <?php $tpl['arr']['capacity'] != 1 ? __('front_people') : __('front_person');?></dd>
							</dl>	
											
							
						</header><!-- /.pjMrBFormHead -->

						<div id="pjMrbCalendarLocale" style="display: none;" data-months="<?php echo implode("_", $months);?>" data-days="<?php echo implode("_", $short_days);?>"></div>
						
						<div class="pjMrBFormBody">
							<div class="form-group">
								<label for="" class="col-lg-3 col-md-3 col-sm-12 col-xs-12 control-label"><?php __('front_date')?>: </label>

								<div class="col-lg-9 col-md-9 col-sm-12 col-xs-12">
									<div class="input-group pjMrBDatePicker pjMrbDatePickerFrom" data-min="<?php echo $min_from;?>">
										<input type="text" id="pjMrbStartDate_<?php echo $index;?>" name="start_date" value="<?php echo $start_date;?>" class="form-control" readonly="readonly" />
									
										<span class="input-group-addon">
											<span class="glyphicon glyphicon-calendar" aria-hidden="true"></span>
										</span>
									</div><!-- /.input-group pjMrBDatePicker -->
								</div><!-- /.col-lg-9 col-md-9 col-sm-12 col-xs-12 -->
							</div><!-- /.form-group -->
							<?php
							if($tpl['is_date_off'] == false && $tpl['is_both_half'] == false)
							{
								$book_by = __('book_by', true);
								?>
								<div class="form-group">
									<label for="" class="col-lg-3 col-md-3 col-sm-12 col-xs-12 control-label"><?php __('front_duration');?>: </label>
	
									<div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
										<select id="pjMrbBookBy_<?php echo $index;?>" name="book_by" class="form-control">
											<?php
											if($tpl['full_day_booked'] == 0)
											{
												if($tpl['arr']['book_by_hour'] == 'T')
												{
													?><option value="hour"<?php echo isset($STORE['book_by']) ? ($STORE['book_by'] == 'hour' ? ' selected="selected"' : NULL) : NULL;?>><?php echo $book_by['hour']; ?></option><?php
												}
												if($tpl['arr']['book_by_halfday'] == 'T')
												{
													if($tpl['halfday_morning'] == 0 && $tpl['hourly_morning'] == 0)
													{
														?>
														<option value="morning"<?php echo isset($STORE['book_by']) ? ($STORE['book_by'] == 'morning' ? ' selected="selected"' : NULL) : NULL;?>><?php echo $book_by['halfday']; ?> - <?php __('front_morning');?> (<?php echo date($tpl['option_arr']['o_time_format'], $tpl['morning_arr']['start_ts']);?> - <?php echo date($tpl['option_arr']['o_time_format'], $tpl['morning_arr']['end_ts']);?>)</option>
														<?php
													}
													if($tpl['halfday_afternoon'] == 0 && $tpl['hourly_afternoon'] == 0)
													{
														?>
														<option value="afternoon"<?php echo isset($STORE['book_by']) ? ($STORE['book_by'] == 'morning' ? ' selected="selected"' : NULL) : NULL;?>><?php echo $book_by['halfday']; ?> - <?php __('front_afternoon');?> (<?php echo date($tpl['option_arr']['o_time_format'], $tpl['afternoon_arr']['start_ts']);?> - <?php echo date($tpl['option_arr']['o_time_format'], $tpl['afternoon_arr']['end_ts']);?>)</option>
														<?php
													}
												}
												if($tpl['arr']['book_by_multiday'] == 'T' && $tpl['multi_day_booked'] == 0)
												{
													?><option value="multiday"<?php echo isset($STORE['book_by']) ? ($STORE['book_by'] == 'multiday' ? ' selected="selected"' : NULL) : NULL;?>><?php echo $book_by['multiday']; ?></option><?php
												}
											}else if($tpl['halfday_morning'] == 0 || $tpl['halfday_afternoon'] == 0) {
												if($tpl['arr']['book_by_hour'] == 'T')
												{
													?><option value="hour"<?php echo isset($STORE['book_by']) ? ($STORE['book_by'] == 'hour' ? ' selected="selected"' : NULL) : NULL;?>><?php echo $book_by['hour']; ?></option><?php
												}
												if($tpl['arr']['book_by_halfday'] == 'T')
												{
													if($tpl['halfday_morning'] == 0 && $tpl['hourly_morning'] == 0)
													{
														?>
														<option value="morning"<?php echo isset($STORE['book_by']) ? ($STORE['book_by'] == 'morning' ? ' selected="selected"' : NULL) : NULL;?>><?php echo $book_by['halfday']; ?> - <?php __('front_morning');?> (<?php echo date($tpl['option_arr']['o_time_format'], $tpl['morning_arr']['start_ts']);?> - <?php echo date($tpl['option_arr']['o_time_format'], $tpl['morning_arr']['end_ts']);?>)</option>
														<?php
													}
													if($tpl['halfday_afternoon'] == 0 && $tpl['hourly_afternoon'] == 0)
													{
														?>
														<option value="afternoon"<?php echo isset($STORE['book_by']) ? ($STORE['book_by'] == 'morning' ? ' selected="selected"' : NULL) : NULL;?>><?php echo $book_by['halfday']; ?> - <?php __('front_afternoon');?> (<?php echo date($tpl['option_arr']['o_time_format'], $tpl['afternoon_arr']['start_ts']);?> - <?php echo date($tpl['option_arr']['o_time_format'], $tpl['afternoon_arr']['end_ts']);?>)</option>
														<?php
													}
												}
											}else{
												?>
												<option value="">-- <?php __('lblRoomBookedAtDate'); ?> --</option>
												<?php
											} 
											?>
											
										</select>
									</div><!-- /.col-lg-6 col-md-6 col-sm-12 col-xs-12 -->
								</div><!-- /.form-group -->
	
								<div id="pjMrbEndDateWrapper_<?php echo $index;?>" class="form-group" style="display: none;">
									<label for="" class="col-lg-3 col-md-3 col-sm-12 col-xs-12 control-label"><?php __('front_end_date')?>: </label>
	
									<div class="col-lg-9 col-md-9 col-sm-12 col-xs-12">
										<div class="input-group pjMrBDatePicker pjMrbDatePickerTo" data-min="<?php echo $min_to;?>">
											<input type="text" id="pjMrbEndDate_<?php echo $index;?>" name="end_date" value="<?php echo $end_date;?>" class="form-control" readonly="readonly" />
										
											<span class="input-group-addon">
												<span class="glyphicon glyphicon-calendar" aria-hidden="true"></span>
											</span>
										</div><!-- /.input-group pjMrBDatePicker -->
									</div><!-- /.col-lg-9 col-md-9 col-sm-12 col-xs-12 -->
								</div><!-- /.form-group -->
								<div class="form-group" style="display: <?php echo $tpl['range_arr']['status'] == 'OK' ? "none" : "block;"?>;">
									<div class="col-lg-9 col-lg-offset-3 col-md-9 col-md-offset-3 col-sm-12 col-xs-12">
										<div id="pjMrbEndDateMessage" class="text-danger"><?php echo $tpl['range_arr']['status'] == 'ERR' ? $tpl['range_arr']['text'] : '';?></div>
									</div><!-- /.col-lg-9 col-lg-offset-3 col-md-9 col-md-offset-3 col-sm-12 col-xs-12 pjMrBActions -->
								</div><!-- /.form-group -->				
								<div id="pjMrbFromTo_<?php echo $index;?>" class="form-group">
									<label for="" class="col-lg-3 col-md-3 col-sm-12 col-xs-12 control-label"><?php __('front_from_to')?>: </label>
	
									<div class="col-lg-9 col-md-9 col-sm-12 col-xs-12">
										<ul class="list-inline pjMrBCustomCheckboxesDuration">
											<?php
											$selected_slots = isset($STORE['slots']) ? $STORE['slots'] : array();
											$i = 1;
											foreach($tpl['from_to_arr'] as $k => $v)
											{ 
												?>
												<li>
													<div class="pjMrBCheckbox pjMrBCustomCheckbox">
														<input type="checkbox" name="slots[]"<?php echo in_array($k, $selected_slots) ? ' checked="checked"' : NULL;?> value="<?php echo $k;?>" class="pjMrbSlots" id="pjMrBCheckboxDuration<?php echo $i;?>" autocomplete="off" />
		
														<label for="pjMrBCheckboxDuration<?php echo $i;?>"><?php echo $v;?></label>
													</div><!-- /.pjMrBCheckbox pjMrBCustomCheckbox -->
												</li>
												<?php
												$i++;
											} 
											?>
										</ul><!-- /.list-inline pjMrBCustomCheckboxesDuration -->
									</div><!-- /.col-lg-9 col-md-9 col-sm-12 col-xs-12 -->
								</div><!-- /.form-group -->
								<div class="form-group" style="display: none;">
									<div class="col-lg-9 col-lg-offset-3 col-md-9 col-md-offset-3 col-sm-12 col-xs-12">
										<div id="pjMrbSlotsMessage" class="text-danger"><?php __('front_select_slot_message');?></div>
									</div><!-- /.col-lg-9 col-lg-offset-3 col-md-9 col-md-offset-3 col-sm-12 col-xs-12 pjMrBActions -->
								</div><!-- /.form-group -->	
								<div class="form-group">
									<label for="" class="col-lg-3 col-md-3 col-sm-12 col-xs-12 control-label"><?php __('front_attendees');?>: </label>
	
									<div class="col-lg-9 col-md-9 col-sm-12 col-xs-12">
										<div class="pjMrBSpinner">
											<input type="text" name="attendees" class="form-control pjMrbPeople digits" autocomplete="off" value="<?php echo isset($STORE['attendees']) ? $STORE['attendees'] : 1;?>" data-min="1" data-max="<?php echo $tpl['arr']['capacity'];?>" data-msg-max="<?php __('front_max_attendees_valid');?>"/>
											
											<a href="#" class="pjMrBSpinnerBtn pjMrBSpinnerBtnUp">
												<span class="caret"></span>
											</a>
	
											<a href="#" class="pjMrBSpinnerBtn pjMrBSpinnerBtnDown">
												<span class="caret"></span>
											</a>
										</div><!-- /.pjMrBSpinner -->
										<div class="help-block with-errors"><ul class="list-unstyled"></ul></div>
									</div><!-- /.col-lg-9 col-md-9 col-sm-12 col-xs-12 -->
								</div><!-- /.form-group -->
	
								<div class="form-group">
									<div class="col-lg-3 col-md-3 col-sm-12 col-xs-12">
										<a href="#" data-load="loadRooms" class="btn btn-default pull-left pjMrbMenuItem"><?php __('front_btn_back');?></a>
									</div>
									<div class="col-lg-9 col-md-9 col-sm-12 col-xs-12 pjMrBActions">
										<button type="submit" class="btn btn-primary pjMrbNextButton"<?php echo $tpl['range_arr']['status'] == 'ERR' ? ' disabled="disabled"' : "";?>><?php __('front_btn_next');?></button>
									</div><!-- /.col-lg-9 col-lg-offset-3 col-md-9 col-md-offset-3 col-sm-12 col-xs-12 pjMrBActions -->
								</div><!-- /.form-group -->
								<?php
							}else{
								?>
								<div class="form-group">
									<div class="col-lg-9 col-lg-offset-3 col-md-9 col-md-offset-3 col-sm-12 col-xs-12 pjMrBActions">
										<div class="text-danger"><?php $tpl['is_date_off'] == true ? __('front_date_off_msg') : __('front_date_booked_msg');?></div>
									</div><!-- /.col-lg-9 col-lg-offset-3 col-md-9 col-md-offset-3 col-sm-12 col-xs-12 pjMrBActions -->
								</div><!-- /.form-group -->
								<?php
							} 
							?>
						</div><!-- /.pjMrBFormBody -->
					</form><!-- /.form-horizontal -->
				</div><!-- /.pjMrBForm pjMrBFormProduct -->
			</div><!-- /.col-lg-9 col-md-9 col-sm-8 col-xs-7 pjMrBRoomInnerContent -->
		</div><!-- /.row -->
	</div><!-- /.thumbnail pjMrBRoomInner -->
</div><!-- /.pjMrBBody -->