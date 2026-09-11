<?php
if (isset($tpl['status']))
{
	$status = __('status', true);
	switch ($tpl['status'])
	{
		case 2:
			pjUtil::printNotice(NULL, $status[2]);
			break;
	}
} else {
	if (isset($_GET['err']))
	{
		$titles = __('error_titles', true);
		$bodies = __('error_bodies', true);
		pjUtil::printNotice(@$titles[$_GET['err']], @$bodies[$_GET['err']]);
	}
	$week_start = isset($tpl['option_arr']['o_week_start']) && in_array((int) $tpl['option_arr']['o_week_start'], range(0,6)) ? (int) $tpl['option_arr']['o_week_start'] : 0;
	$jqDateFormat = pjUtil::jqDateFormat($tpl['option_arr']['o_date_format']);
	
	?>
	<form action="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminBookings&amp;action=pjActionUpdate" method="post" class="form pj-form" id="frmUpdateBooking">
		<input type="hidden" name="booking_update" value="1"/>
		<input type="hidden" name="csrf_token" value="<?php echo pjAppController::getCsrfToken(); ?>" />
		<input type="hidden" name="id" value="<?php echo $tpl['arr']['id'];?>"/>
		<input type="hidden" id="hours" name="hours" value="<?php echo $tpl['arr']['hours'];?>"/>
		
		<div id="tabs">
			<ul>
				<li><a href="#tabs-1"><?php __('tabBookingDetails');?></a></li>
				<li><a href="#tabs-2"><?php __('tabClientDetails');?></a></li>
			</ul>
			<div id="tabs-1">
				<?php pjUtil::printNotice(__('infoBookingDetailsTitle', true, false), __('infoBookingDetailsDesc', true, false)); ?>
				<div class="pj-loader-outer">
					<div class="pj-loader"></div>
					<fieldset class="fieldset white">
						<legend><?php __('legendDateAndRoom'); ?></legend>
						<div class="float_left w50p overflow">
							<p>
								<label class="title"><?php __('lblCreatedOn'); ?></label>
								<span class="inline_block t5"><?php echo date($tpl['option_arr']['o_date_format'], strtotime($tpl['arr']['created'])) . ', ' . date($tpl['option_arr']['o_time_format'], strtotime($tpl['arr']['created']));?></span>
							</p>
							<p>
								<label class="title"><?php __('lblIpAddress'); ?></label>
								<span class="inline_block t5"><?php echo pjSanitize::html($tpl['arr']['ip']);?></span>
							</p>
							
							<p>
								<label class="title"><?php __('lblDate'); ?></label>
								<span class="pj-form-field-custom pj-form-field-custom-after">
									<input type="text" name="start_date" id="start_date" value="<?php echo date($tpl['option_arr']['o_date_format'], strtotime($tpl['arr']['start_date']));?>" class="pj-form-field pointer w100 datepicker" readonly="readonly" rel="<?php echo $week_start; ?>" rev="<?php echo $jqDateFormat; ?>"/>
									<input type="hidden" name="validate_start_date" value="<?php echo mt_rand(0, 999999);?>" class="required" data-msg-required="<?php __('pj_field_required');?>" data-msg-remote="<?php __('lblSelectedDateOff');?>"/>
									<span class="pj-form-field-after"><abbr class="pj-form-field-icon-date"></abbr></span>
								</span>
							</p>
							<p id="pjMrbEndDate" class="<?php echo $tpl['arr']['book_by'] == 'multiday' ? '' : 'none';?>">
								<label class="title"><?php __('lblEndDate'); ?></label>
								<span class="pj-form-field-custom pj-form-field-custom-after">
									<input type="text" name="end_date" id="end_date" value="<?php echo !empty($tpl['arr']['end_date']) ? date($tpl['option_arr']['o_date_format'], strtotime($tpl['arr']['end_date'])) : NULL;?>" class="pj-form-field pointer w100 datepicker" readonly="readonly" rel="<?php echo $week_start; ?>" rev="<?php echo $jqDateFormat; ?>"/>
									<input type="hidden" name="validate_end_date" value="<?php echo mt_rand(0, 999999);?>" class="required" data-msg-required="<?php __('pj_field_required');?>" data-msg-remote="<?php __('lblRangeDaysOff');?>"/>
									<input type="hidden" name="validate_date_range" value="<?php echo mt_rand(0, 999999);?>" data-msg-remote="<?php __('lblInvalidDateRange');?>"/>
									<span class="pj-form-field-after"><abbr class="pj-form-field-icon-date"></abbr></span>
								</span>
							</p>
							<p>
								<label class="title"><?php __('lblAttendees'); ?></label>
								<input type="text" name="attendees" id="attendees" value="<?php echo $tpl['arr']['attendees'];?>" class="pj-form-field field-int w100 digits required" data-msg-required="<?php __('pj_field_required');?>" data-msg-digits="<?php __('pj_digits_validation');?>" data-msg-max="<?php __('pj_attendees_validation')?>"/>
							</p>
							<p>
								<label class="title"><?php __('lblRoom'); ?></label>
								<span class="inline_block">
									<select name="room_id" id="room_id" class="pj-form-field w150 required" data-msg-required="<?php __('pj_field_required');?>">
										<option value="">-- <?php __('lblChoose'); ?> --</option>
										<?php
										$max_capacity = 1;
										foreach($tpl['room_arr'] as $k => $v)
										{
											if($tpl['arr']['room_id'] == $v['id'])
											{
												$max_capacity = (int) $v['capacity'];
											}
											?><option value="<?php echo $v['id'];?>"<?php echo $tpl['arr']['room_id'] == $v['id'] ? ' selected="selected"' : NULL;?> data-capacity="<?php echo (int) $v['capacity'];?>"><?php echo pjSanitize::html($v['title']); ?></option><?php
										} 
										?>
									</select>
								</span>
							</p>
							<div id="pjMrbLayout">
								<p>
									<label class="title"><?php __('lblLayout'); ?></label>
									<span class="inline_block">
										<select name="layout_id" id="layout_id" class="pj-form-field w150" data-msg-required="<?php __('pj_field_required');?>">
											<option value="">-- <?php __('lblChoose'); ?> --</option>
											<?php
											foreach($tpl['layout_arr'] as $k => $v)
											{
												?><option value="<?php echo $v['layout_id'];?>"<?php echo $v['layout_id'] == $tpl['arr']['layout_id'] ? ' selected="selected"' : NULL;?>><?php echo pjSanitize::html($v['title']); ?></option><?php
											}
											?>
										</select>
									</span>
								</p>
							</div>
							<div id="pjMrbDuration">
								<p>
									<label class="title"><?php __('lblDuration'); ?></label>
									<span class="inline_block">
										<select name="book_by" id="book_by" class="pj-form-field w150 required" data-msg-required="<?php __('pj_field_required');?>">
											<option value="">-- <?php __('lblChoose'); ?> --</option>
											<?php
											$book_by = __('book_by', true);
											if($tpl['room']['book_by_hour'] == 'T')
											{
												?><option value="hour"<?php echo $tpl['arr']['book_by'] == 'hour' ? ' selected="selected"' : NULL;?>><?php echo $book_by['hour']; ?></option><?php
											}
											if($tpl['room']['book_by_halfday'] == 'T')
											{
												if($tpl['halfday_morning'] == 0)
												{
													?>
													<option value="morning"<?php echo $tpl['arr']['book_by'] == 'morning' ? ' selected="selected"' : NULL;?>><?php echo $book_by['halfday']; ?> - <?php __('time_morning');?></option>
													<?php
												}
												if($tpl['halfday_afternoon'] == 0)
												{
													?>
													<option value="afternoon"<?php echo $tpl['arr']['book_by'] == 'afternoon' ? ' selected="selected"' : NULL;?>><?php echo $book_by['halfday']; ?> - <?php __('time_afternoon');?></option>
													<?php
												}
												if($tpl['halfday_evening'] == 0)
												{
													?>
													<option value="evening"<?php echo $tpl['arr']['book_by'] == 'evening' ? ' selected="selected"' : NULL;?>><?php echo $book_by['halfday']; ?> - <?php __('time_evening');?></option>
													<?php
												}
												if($tpl['combo_morningafternoon_available'] == 1)
												{
													?>
													<option value="morningafternoon"<?php echo $tpl['arr']['book_by'] == 'morningafternoon' ? ' selected="selected"' : NULL;?>><?php __('time_morningafternoon');?></option>
													<?php
												}
												if($tpl['combo_afternoonevening_available'] == 1)
												{
													?>
													<option value="afternoonevening"<?php echo $tpl['arr']['book_by'] == 'afternoonevening' ? ' selected="selected"' : NULL;?>><?php __('time_afternoonevening');?></option>
													<?php
												}
											}
											if($tpl['room']['book_by_multiday'] == 'T' && $tpl['multi_day_booked'] == 0)
											{
												?><option value="multiday"<?php echo $tpl['arr']['book_by'] == 'multiday' ? ' selected="selected"' : NULL;?>><?php echo $book_by['multiday']; ?></option><?php
											}  
											?>
										</select>
									</span>
								</p>
							</div>
							<div id="pjMrbFromTo" class="<?php echo $tpl['arr']['book_by'] == 'hour' ? '' : 'none';?>">
								<p>
									<label class="title"><?php __('lblFromTo'); ?></label>
									<span class="inline_block">
										<select name="slots[]" id="slots" multiple="multiple" size="5" class="pj-form-field w100<?php echo $tpl['arr']['book_by'] == 'hour' ? ' required' : '';?>" data-msg-required="<?php __('pj_field_required');?>">
											<?php
											foreach($tpl['from_to_arr'] as $k => $v)
											{
												?><option value="<?php echo $k;?>"<?php echo in_array($k, $tpl['booking_slot_arr']) ? ' selected="selected"' : NULL;?>><?php echo $v; ?></option><?php
											}
											?>
										</select>
									</span>
								</p>
							</div>
							<p>
								<label class="title">&nbsp;</label>
								<span class="inline_block">
									<input type="submit" value="<?php __('btnSave'); ?>" class="pj-button" />
									<input type="button" value="<?php __('btnCancel'); ?>" class="pj-button" onclick="window.location.href='<?php echo PJ_INSTALL_URL; ?>index.php?controller=pjAdminBookings&action=pjActionIndex';" />
								</span>
							</p>
						</div>
						<div class="float_left w50p overflow">
							<p>
								<span class="inline_block t5"><a href="#" class="pjMrbSendConfirm" data-id="<?php echo $tpl['arr']['id']?>"><?php __('lblSendEmailConfirmation'); ?></a></span>
							</p>
							<p>
								<label class="title"><?php __('lblStatus'); ?></label>
								<span class="inline_block">
									<select name="status" id="status" class="pj-form-field w150 required" data-msg-required="<?php __('pj_field_required');?>">
										<option value="">-- <?php __('lblChoose'); ?> --</option>
										<?php
										foreach (__('booking_statuses', true) as $k => $v)
										{
											?><option value="<?php echo $k; ?>"<?php echo $k == $tpl['arr']['status'] ? ' selected="selected"' : NULL;?>><?php echo stripslashes($v); ?></option><?php
										}
										?>
									</select>
								</span>
							</p>
							<p>
								<label class="title"><?php __('lblPaymentMethod');?></label>
								<span class="inline-block">
									<select name="payment_method" id="payment_method" class="pj-form-field w150 required" data-msg-required="<?php __('pj_field_required');?>">
										<option value="">-- <?php __('lblChoose'); ?>--</option>
										<?php
										foreach (__('payment_methods', true, false) as $k => $v)
										{
											?><option value="<?php echo $k; ?>"<?php echo $k == $tpl['arr']['payment_method'] ? ' selected="selected"' : NULL;?>><?php echo $v; ?></option><?php
										}
										?>
									</select>
								</span>
							</p>
							<?php $isCC = $tpl['arr']['payment_method'] == 'creditcard'; ?>
							<p class="boxCC" style="display: <?php echo !$isCC ? 'none' : NULL; ?>;">
								<label class="title"><?php __('lblCCType'); ?></label>
								<span class="inline-block">
									<select name="cc_type" class="pj-form-field w150" data-msg-required="<?php __('pj_field_required');?>">
										<option value="">---</option>
										<?php
										foreach (__('cc_types', true, false) as $k => $v)
										{
											?><option value="<?php echo $k; ?>"<?php echo $tpl['arr']['cc_type'] == $k ? ' selected="selected"' : NULL;?>><?php echo $v; ?></option><?php
										}
										?>
									</select>
								</span>
							</p>
							<p class="boxCC" style="display: <?php echo !$isCC ? 'none' : NULL; ?>;">
								<label class="title"><?php __('lblCCNum'); ?></label>
								<span class="inline-block">
									<input type="text" name="cc_num" id="cc_num" class="pj-form-field w136" value="<?php echo htmlspecialchars(stripslashes($tpl['arr']['cc_num'])); ?>" data-msg-required="<?php __('pj_field_required');?>"/>
								</span>
							</p>
							<p class="boxCC" style="display: <?php echo !$isCC ? 'none' : NULL; ?>;">
								<label class="title"><?php __('lblCCExp'); ?></label>
								<span class="inline-block">
									<select name="cc_exp_month" class="pj-form-field" data-msg-required="<?php __('pj_field_required');?>">
										<?php
										$month_arr = __('months', true, false);
										ksort($month_arr);
										foreach ($month_arr as $key => $val)
										{
											?><option value="<?php echo $key;?>"<?php echo $tpl['arr']['cc_exp_month'] == $key ? ' selected="selected"' : NULL;?>><?php echo $val;?></option><?php
										}
										?>
									</select>
									<select name="cc_exp_year" class="pj-form-field" data-msg-required="<?php __('pj_field_required');?>">
										<?php
										$y = (int) date('Y');
										for ($i = $y; $i <= $y + 10; $i++)
										{
											?><option value="<?php echo $i; ?>" <?php echo $tpl['arr']['cc_exp_year'] == $i ? ' selected="selected"' : NULL;?>><?php echo $i; ?></option><?php
										}
										?>
									</select>
								</span>
							</p>
							<p class="boxCC" style="display: <?php echo !$isCC ? 'none' : NULL; ?>">
								<label class="title"><?php __('lblCCCode'); ?></label>
								<span class="inline-block">
									<input type="text" name="cc_code" id="cc_code" class="pj-form-field w100" value="<?php echo htmlspecialchars(stripslashes($tpl['arr']['cc_code'])); ?>" data-msg-required="<?php __('pj_field_required');?>"/>
								</span>
							</p>
							<p>
								<label class="title"><?php __('lblRoomPrice'); ?></label>
								<span class="pj-form-field-custom pj-form-field-custom-before">
									<span class="pj-form-field-before"><abbr class="pj-form-field-icon-text"><?php echo pjUtil::formatCurrencySign(NULL, $tpl['option_arr']['o_currency'], ""); ?></abbr></span>
									<input type="text" id="room_price" name="room_price" value="<?php echo $tpl['arr']['room_price']; ?>" class="pj-form-field number w80" readonly="readonly"/>
								</span>
							</p>
							<p>
								<label class="title"><?php __('lblEquipmentPrice'); ?></label>
								<span class="pj-form-field-custom pj-form-field-custom-before">
									<span class="pj-form-field-before"><abbr class="pj-form-field-icon-text"><?php echo pjUtil::formatCurrencySign(NULL, $tpl['option_arr']['o_currency'], ""); ?></abbr></span>
									<input type="text" id="equipment_price" name="equipment_price" value="<?php echo $tpl['arr']['equipment_price']; ?>" class="pj-form-field number w80" readonly="readonly"/>
								</span>
							</p>
							<p>
								<label class="title"><?php __('lblFoodDrinkPrice'); ?></label>
								<span class="pj-form-field-custom pj-form-field-custom-before">
									<span class="pj-form-field-before"><abbr class="pj-form-field-icon-text"><?php echo pjUtil::formatCurrencySign(NULL, $tpl['option_arr']['o_currency'], ""); ?></abbr></span>
									<input type="text" id="food_drink_price" name="food_drink_price" value="<?php echo $tpl['arr']['food_drink_price']; ?>" class="pj-form-field number w80" readonly="readonly"/>
								</span>
							</p>
							<p>
								<label class="title"><?php __('lblSubTotal'); ?></label>
								<span class="pj-form-field-custom pj-form-field-custom-before">
									<span class="pj-form-field-before"><abbr class="pj-form-field-icon-text"><?php echo pjUtil::formatCurrencySign(NULL, $tpl['option_arr']['o_currency'], ""); ?></abbr></span>
									<input type="text" id="subtotal" name="subtotal" class="pj-form-field number w80" value="<?php echo $tpl['arr']['subtotal']; ?>" readonly="readonly"/>
								</span>
							</p>
							<p>
								<label class="title"><?php __('lblTax'); ?></label>
								<span class="pj-form-field-custom pj-form-field-custom-before">
									<span class="pj-form-field-before"><abbr class="pj-form-field-icon-text"><?php echo pjUtil::formatCurrencySign(NULL, $tpl['option_arr']['o_currency'], ""); ?></abbr></span>
									<input type="text" id="tax" name="tax" class="pj-form-field number w80" value="<?php echo $tpl['arr']['tax']; ?>" data-tax="<?php echo $tpl['option_arr']['o_tax_payment'];?>" readonly="readonly"/>
								</span>
							</p>
							<p>
								<label class="title"><?php __('lblTotal'); ?></label>
								<span class="pj-form-field-custom pj-form-field-custom-before">
									<span class="pj-form-field-before"><abbr class="pj-form-field-icon-text"><?php echo pjUtil::formatCurrencySign(NULL, $tpl['option_arr']['o_currency'], ""); ?></abbr></span>
									<input type="text" id="total" name="total" class="pj-form-field number w80" value="<?php echo $tpl['arr']['total']; ?>" readonly="readonly"/>
								</span>
							</p>
							<p>
								<label class="title"><?php __('lblDeposit'); ?></label>
								<span class="pj-form-field-custom pj-form-field-custom-before">
									<span class="pj-form-field-before"><abbr class="pj-form-field-icon-text"><?php echo pjUtil::formatCurrencySign(NULL, $tpl['option_arr']['o_currency'], ""); ?></abbr></span>
									<input type="text" id="deposit" name="deposit" class="pj-form-field number w80" value="<?php echo $tpl['arr']['deposit']; ?>" data-deposit="<?php echo $tpl['option_arr']['o_deposit_payment'];?>" readonly="readonly"/>
								</span>
							</p>
						</div>
					</fieldset>
					<?php
					if(!empty($tpl['equipment_arr']))
					{
						?>
						<fieldset class="fieldset white">
							<legend><?php __('legendEquipment'); ?></legend>
							<table class="pj-table" style="width: 100%; margin-bottom: 10px;">
								<thead>
									<tr>
										<th><?php __('lblTitle');?></th>
										<th><?php __('lblUnits');?></th>
										<th><?php __('lblPrice');?></th>
										<th>&nbsp;</th>
									</tr>
								</thead>
								<tbody>
									<?php
									$price_per = array();
									$price_per['booking'] = __('lblPricePerBooking', true);
									$price_per['hour'] = __('lblPricePerHour', true);
									foreach($tpl['equipment_arr'] as $k => $v)
									{
										?>
										<tr>
											<td><?php echo pjSanitize::html($v['title']);?></td>
											<td>
												<?php
												if($v['multi_units'] == 'T')
												{
													?><input type="text" name="units[<?php echo $v['id'];?>]" value="<?php echo isset($tpl['booking_equipment_arr'][$v['id']]) ? $tpl['booking_equipment_arr'][$v['id']]['units'] : 1;?>" class="pj-form-field field-int w80 digits" data-type="equipment" data-id="<?php echo $v['id'];?>" data-msg-digits="<?php __('pj_digits_validation');?>"/><?php
												}else{
													?>1<input type="hidden" name="units[<?php echo $v['id'];?>]" value="<?php echo isset($tpl['booking_equipment_arr'][$v['id']]) ? $tpl['booking_equipment_arr'][$v['id']]['units'] : 1;?>"/><?php
												}
												?>
											</td>
											<td><?php echo pjUtil::formatCurrencySign($v['price'], $tpl['option_arr']['o_currency']) . ' ' . $price_per[$v['price_per']];?></td>
											<td><input type="checkbox" id="checkunit_<?php echo $v['id'];?>" name="equipment_id[<?php echo $v['id'];?>]"<?php echo isset($tpl['booking_equipment_arr'][$v['id']]) ? ' checked="checked"' : NULL;?> value="<?php echo $v['price'];?>|<?php echo $v['price_per'];?>" class="pjMrbEquipmentCheck"/></td>
										</tr>
										<?php
									} 
									?>
								</tbody>
							</table>
							
							<div class="overflow">
								<input type="submit" value="<?php __('btnSave'); ?>" class="pj-button" />
							</div>
						</fieldset>
						<?php
					}
					if(!empty($tpl['food_drink_arr']))
					{ 
						?>
						<fieldset class="fieldset white">
							<legend><?php __('legendFoodAndDrinks'); ?></legend>
							
							<table class="pj-table" style="width: 100%; margin-bottom: 10px;">
								<thead>
									<tr>
										<th><?php __('lblTitle');?></th>
										<th><?php __('lblPeople');?></th>
										<th><?php __('lblPrice');?></th>
										<th>&nbsp;</th>
									</tr>
								</thead>
								<tbody>
									<?php
									foreach($tpl['food_drink_arr'] as $k => $v)
									{
										?>
										<tr>
											<td><?php echo pjSanitize::html($v['title']);?></td>
											<td><input type="text" name="people[<?php echo $v['id'];?>]" value="<?php echo isset($tpl['booking_food_drink_arr'][$v['id']]['people']) ? $tpl['booking_food_drink_arr'][$v['id']]['people'] : 1;?>" class="pj-form-field field-int w80 digits" data-type="food_drinks" data-id="<?php echo $v['id'];?>" data-msg-digits="<?php __('pj_digits_validation');?>"/></td>
											<td><?php echo pjUtil::formatCurrencySign($v['price'], $tpl['option_arr']['o_currency']);?> <?php __('lblPerAttendee');?></td>
											<td><input type="checkbox" id="checkfooddrinks_<?php echo $v['id'];?>" name="food_drink_id[<?php echo $v['id'];?>]"<?php echo isset($tpl['booking_food_drink_arr'][$v['id']]) ? ' checked="checked"' : NULL;?> value="<?php echo $v['price'];?>" class="pjMrbFoodCheck"/></td>
										</tr>
										<?php
									} 
									?>
								</tbody>
							</table>
							<div class="overflow">
								<input type="submit" value="<?php __('btnSave'); ?>" class="pj-button" />
							</div>
						</fieldset>
						<?php
					} 
					?>
				</div>
			</div><!-- #tabs-1 -->
			<div id="tabs-2">
				<?php pjUtil::printNotice(__('infoClientDetailsTitle', true, false), __('infoClientDetailsDesc', true, false)); ?>
				
				<?php
				if (in_array((int) $tpl['option_arr']['o_bf_include_title'], array(2,3)))
				{
					?>
					<p>
						<label class="title"><?php __('lblResvTitle'); ?></label>
						<span class="inline-block">
							<select name="c_title" id="c_title" class="pj-form-field w150<?php echo $tpl['option_arr']['o_bf_include_title'] == 3 ? ' required' : NULL; ?>">
								<option value="">-- <?php __('lblChoose'); ?>--</option>
								<?php
								foreach ( __('personal_titles', true, false) as $k => $v)
								{
									?><option value="<?php echo $k; ?>"<?php echo $k == $tpl['arr']['c_title'] ? ' selected="selected"' : NULL;?>><?php echo $v; ?></option><?php
								}
								?>
							</select>
						</span>
					</p>
					<?php
				}
				if (in_array((int) $tpl['option_arr']['o_bf_include_name'], array(2,3)))
				{ 
					?>
					<p>
						<label class="title"><?php __('lblResvName'); ?></label>
						<span class="inline-block">
							<input type="text" name="c_name" id="c_name" value="<?php echo pjSanitize::html($tpl['arr']['c_name']); ?>" class="pj-form-field w400<?php echo $tpl['option_arr']['o_bf_include_name'] == 3 ? ' required' : NULL; ?>" data-msg-required="<?php __('pj_field_required');?>"/>
						</span>
					</p>
					<?php
				}
				if (in_array((int) $tpl['option_arr']['o_bf_include_address'], array(2,3)))
				{ 
					?>
					<p>
						<label class="title"><?php __('lblResvAddress'); ?></label>
						<span class="inline-block">
							<input type="text" name="c_address" id="c_address" value="<?php echo pjSanitize::html($tpl['arr']['c_address']); ?>" class="pj-form-field w400<?php echo $tpl['option_arr']['o_bf_include_address'] == 3 ? ' required' : NULL; ?>" data-msg-required="<?php __('pj_field_required');?>"/>
						</span>
					</p>
					<?php
				}
				if (in_array((int) $tpl['option_arr']['o_bf_include_zip'], array(2,3)))
				{ 
					?>
					<p>
						<label class="title"><?php __('lblResvZip'); ?></label>
						<span class="inline-block">
							<input type="text" name="c_zip" id="c_zip" value="<?php echo pjSanitize::html($tpl['arr']['c_zip']); ?>" class="pj-form-field w400<?php echo $tpl['option_arr']['o_bf_include_zip'] == 3 ? ' required' : NULL; ?>" data-msg-required="<?php __('pj_field_required');?>"/>
						</span>
					</p>
					<?php
				}
				if (in_array((int) $tpl['option_arr']['o_bf_include_city'], array(2,3)))
				{ 
					?>
					<p>
						<label class="title"><?php __('lblResvCity'); ?></label>
						<span class="inline-block">
							<input type="text" name="c_city" id="c_city" value="<?php echo pjSanitize::html($tpl['arr']['c_city']); ?>" class="pj-form-field w400<?php echo $tpl['option_arr']['o_bf_include_city'] == 3 ? ' required' : NULL; ?>" data-msg-required="<?php __('pj_field_required');?>"/>
						</span>
					</p>
					<?php
				}
				if (in_array((int) $tpl['option_arr']['o_bf_include_state'], array(2,3)))
				{ 
					?>
					<p>
						<label class="title"><?php __('lblResvState'); ?></label>
						<span class="inline-block">
							<input type="text" name="c_state" id="c_state" value="<?php echo pjSanitize::html($tpl['arr']['c_state']); ?>" class="pj-form-field w400<?php echo $tpl['option_arr']['o_bf_include_state'] == 3 ? ' required' : NULL; ?>" data-msg-required="<?php __('pj_field_required');?>"/>
						</span>
					</p>
					<?php
				}
				if (in_array((int) $tpl['option_arr']['o_bf_include_country'], array(2,3)))
				{ 
					?>
					<p>
						<label class="title"><?php __('lblResvCountry'); ?></label>
						<span class="inline-block">
							<select name="c_country" id="c_country" class="pj-form-field w400<?php echo $tpl['option_arr']['o_bf_include_country'] == 3 ? ' required' : NULL; ?>" data-msg-required="<?php __('pj_field_required');?>">
								<option value="">-- <?php __('lblChoose'); ?>--</option>
								<?php
								foreach ($tpl['country_arr'] as $v)
								{
								    ?><option value="<?php echo $v['id']; ?>"<?php echo $v['id'] == $tpl['arr']['c_country'] ? ' selected="selected"' : NULL;?>><?php echo pjSanitize::html($v['country_title']); ?></option><?php
								}
								?>
							</select>
						</span>
					</p>
					<?php
				}
				if (in_array((int) $tpl['option_arr']['o_bf_include_email'], array(2,3)))
				{
					?>
					<p>
						<label class="title"><?php __('lblResvEmail'); ?></label>
						<span class="inline-block">
							<input type="text" name="c_email" id="c_email" value="<?php echo pjSanitize::html($tpl['arr']['c_email']); ?>" class="pj-form-field email w400<?php echo $tpl['option_arr']['o_bf_include_email'] == 3 ? ' required' : NULL; ?>" data-msg-required="<?php __('pj_field_required');?>" data-msg-email="<?php __('pj_email_validation');?>"/>
						</span>
					</p>
					<?php
				}
				if (in_array((int) $tpl['option_arr']['o_bf_include_phone'], array(2,3)))
				{ 
					?>
					<p>
						<label class="title"><?php __('lblResvPhone'); ?></label>
						<span class="inline-block">
							<input type="text" name="c_phone" id="c_phone" value="<?php echo pjSanitize::html($tpl['arr']['c_phone']); ?>" class="pj-form-field w400<?php echo $tpl['option_arr']['o_bf_include_phone'] == 3 ? ' required' : NULL; ?>" data-msg-required="<?php __('pj_field_required');?>"/>
						</span>
					</p>
					<?php
				}
				if (in_array((int) $tpl['option_arr']['o_bf_include_notes'], array(2,3)))
				{ 
					?>
					<p>
						<label class="title"><?php __('lblResvNotes'); ?></label>
						<span class="inline-block">
							<textarea name="c_notes" id="c_notes" class="pj-form-field w500 h120<?php echo $tpl['option_arr']['o_bf_include_notes'] == 3 ? ' required' : NULL; ?>" data-msg-required="<?php __('pj_field_required');?>"><?php echo pjSanitize::html($tpl['arr']['c_notes']); ?></textarea>
						</span>
					</p>
					<?php
				}
				if (in_array((int) $tpl['option_arr']['o_bf_include_company'], array(2,3)))
				{ 
					?>
					<p>
						<label class="title"><?php __('lblResvCompany'); ?></label>
						<span class="inline-block">
							<input type="text" name="c_company" id="c_company" value="<?php echo pjSanitize::html($tpl['arr']['c_company']); ?>" class="pj-form-field w400<?php echo $tpl['option_arr']['o_bf_include_company'] == 3 ? ' required' : NULL; ?>" data-msg-required="<?php __('pj_field_required');?>"/>
						</span>
					</p>
					<?php
				}
				?>
				
				<p>
					<label class="title">&nbsp;</label>
					<input type="submit" value="<?php __('btnSave', false, true); ?>" class="pj-button" />
					<input type="button" value="<?php __('btnCancel'); ?>" class="pj-button" onclick="window.location.href='<?php echo PJ_INSTALL_URL; ?>index.php?controller=pjAdminBookings&action=pjActionIndex';" />
				</p>
			</div><!-- #tabs-2 -->
		</div>
		
	</form>
	
	<div id="dialogConfirmation" title="<?php __('booking_confirmation_title'); ?>" style="display: none"></div>
	
	<script type="text/javascript">
	var myLabel = myLabel || {};
	myLabel.currency = "<?php echo $tpl['option_arr']['o_currency'];?>";
	myLabel.choose = "-- <?php __('lblChoose'); ?> --";
	myLabel.maxCapacity = <?php echo $max_capacity;?>;
	</script>
	<?php
}
?>