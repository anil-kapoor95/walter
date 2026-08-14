<?php
include_once dirname(__FILE__) . '/elements/header.php'; 

$STORE = $_SESSION[$controller->defaultStore];
$FORM = @$_SESSION[$controller->defaultForm];
$index = pjObject::escapeString($_GET['index']);
?>

<div class="pjMrBBody">
	<div class="panel panel-default pjMrBCheckout">
		<?php include_once dirname(__FILE__) . '/elements/booking_details.php';?>
		
		<div class="panel-body">
			<div class="pjMrBForm pjMrBFormCheckout">
				<form id="pjMrbCheckoutForm_<?php echo $index;?>" action="#" method="post" data-pj-toggle="validator" role="form">
					<input type="hidden" name="mrbs_checkout" value="1" />
					
					<section class="pjMrBFormSection">
						<p class="pjMrBCheckoutTitle"><?php __('front_personal_details');?></p><!-- /.pjMrBCheckoutTitle -->

						<div class="row">
							<?php
							if (in_array((int) $tpl['option_arr']['o_bf_include_title'], array(2,3)))
							{ 
								?>
								<div class="col-lg-4 col-md-4 col-sm-6 col-xs-6">
									<div class="form-group">
										<label for=""><?php __('front_title'); ?><?php echo (int) $tpl['option_arr']['o_bf_include_title'] === 3 ? ' *' : null;?></label>
										<select id="c_title" name="c_title" class="form-control<?php echo (int) $tpl['option_arr']['o_bf_include_title'] === 3 ? ' required' : null;?>" data-msg-required="<?php __('pj_field_required'); ?>">
											<option value=""></option>
											<?php
											foreach(__('personal_titles', true) as $k => $v) 
											{
												?><option value="<?php echo $k;?>"<?php echo isset($FORM['c_title']) ? ($FORM['c_title'] == $k ? ' selected="selected"' : null) : null;?>><?php  echo $v;?></option><?php
											}
											?>
										</select>
										<div class="help-block with-errors"><ul class="list-unstyled"></ul></div>
									</div><!-- /.form-group -->
								</div><!-- /.col-lg-4 col-md-4 col-sm-6 col-xs-6 -->
								<?php
							}
							if (in_array((int) $tpl['option_arr']['o_bf_include_name'], array(2,3)))
							{ 
								?>
								<div class="col-lg-4 col-md-4 col-sm-6 col-xs-6">
									<div class="form-group">
										<label for=""><?php __('front_name'); ?><?php echo (int) $tpl['option_arr']['o_bf_include_name'] === 3 ? ' *' : NULL; ?></label>
										<input type="text" id="c_name" name="c_name" class="form-control<?php echo (int) $tpl['option_arr']['o_bf_include_name'] === 3 ? ' required' : NULL; ?>" value="<?php echo pjSanitize::html(@$FORM['c_name']); ?>" data-msg-required="<?php __('pj_field_required'); ?>">
						    			<div class="help-block with-errors"><ul class="list-unstyled"></ul></div>
									</div><!-- /.form-group -->
								</div><!-- /.col-lg-4 col-md-4 col-sm-6 col-xs-6 -->
								<?php
							}
							if (in_array((int) $tpl['option_arr']['o_bf_include_address'], array(2,3)))
							{ 
								?>
								<div class="col-lg-4 col-md-4 col-sm-6 col-xs-6">
									<div class="form-group">
										<label for=""><?php __('front_address'); ?><?php echo (int) $tpl['option_arr']['o_bf_include_address'] === 3 ? ' *' : NULL; ?></label>
										<input type="text" id="c_address" name="c_address" class="form-control<?php echo (int) $tpl['option_arr']['o_bf_include_address'] === 3 ? ' required' : NULL; ?>" value="<?php echo pjSanitize::html(@$FORM['c_address']); ?>" data-msg-required="<?php __('pj_field_required'); ?>">
						    			<div class="help-block with-errors"><ul class="list-unstyled"></ul></div>
									</div><!-- /.form-group -->
								</div><!-- /.col-lg-4 col-md-4 col-sm-6 col-xs-6 -->
								<?php
							}
							if (in_array((int) $tpl['option_arr']['o_bf_include_zip'], array(2,3)))
							{ 
								?>
								<div class="col-lg-4 col-md-4 col-sm-6 col-xs-6">
									<div class="form-group">
										<label for=""><?php __('front_zip'); ?><?php echo (int) $tpl['option_arr']['o_bf_include_zip'] === 3 ? ' *' : NULL; ?></label>
										<input type="text" id="c_zip" name="c_zip" class="form-control<?php echo (int) $tpl['option_arr']['o_bf_include_zip'] === 3 ? ' required' : NULL; ?>" value="<?php echo pjSanitize::html(@$FORM['c_zip']); ?>" data-msg-required="<?php __('pj_field_required'); ?>">
						    			<div class="help-block with-errors"><ul class="list-unstyled"></ul></div>
									</div><!-- /.form-group -->
								</div><!-- /.col-lg-4 col-md-4 col-sm-6 col-xs-6 -->
								<?php
							}
							if (in_array((int) $tpl['option_arr']['o_bf_include_city'], array(2,3)))
							{ 
								?>
								<div class="col-lg-4 col-md-4 col-sm-6 col-xs-6">
									<div class="form-group">
										<label for=""><?php __('front_city'); ?><?php echo (int) $tpl['option_arr']['o_bf_include_city'] === 3 ? ' *' : NULL; ?></label>
										<input type="text" id="c_city" name="c_city" class="form-control<?php echo (int) $tpl['option_arr']['o_bf_include_city'] === 3 ? ' required' : NULL; ?>" value="<?php echo pjSanitize::html(@$FORM['c_city']); ?>" data-msg-required="<?php __('pj_field_required'); ?>">
						    			<div class="help-block with-errors"><ul class="list-unstyled"></ul></div>
									</div><!-- /.form-group -->
								</div><!-- /.col-lg-4 col-md-4 col-sm-6 col-xs-6 -->
								<?php
							}
							if (in_array((int) $tpl['option_arr']['o_bf_include_state'], array(2,3)))
							{ 
								?>
								<div class="col-lg-4 col-md-4 col-sm-6 col-xs-6">
									<div class="form-group">
										<label for=""><?php __('front_state'); ?><?php echo (int) $tpl['option_arr']['o_bf_include_state'] === 3 ? ' *' : NULL; ?></label>
										<input type="text" id="c_state" name="c_state" class="form-control<?php echo (int) $tpl['option_arr']['o_bf_include_state'] === 3 ? ' required' : NULL; ?>" value="<?php echo pjSanitize::html(@$FORM['c_state']); ?>" data-msg-required="<?php __('pj_field_required'); ?>">
						    			<div class="help-block with-errors"><ul class="list-unstyled"></ul></div>
									</div><!-- /.form-group -->
								</div><!-- /.col-lg-4 col-md-4 col-sm-6 col-xs-6 -->
								<?php
							}
							if (in_array((int) $tpl['option_arr']['o_bf_include_country'], array(2,3)))
							{ 
								?>
								<div class="col-lg-4 col-md-4 col-sm-6 col-xs-6">
									<div class="form-group">
										<label for=""><?php __('front_country'); ?><?php echo (int) $tpl['option_arr']['o_bf_include_country'] === 3 ? ' *' : null;?></label>
										<select id="c_country" name="c_country" class="form-control<?php echo (int) $tpl['option_arr']['o_bf_include_country'] === 3 ? ' required' : null;?>" data-msg-required="<?php __('pj_field_required'); ?>">
											<option value="">-- <?php __('front_choose');?> --</option>
											<?php
											foreach($tpl['country_arr'] as $k => $v) 
											{
												?><option value="<?php echo $v['id'];?>"<?php echo isset($FORM['c_country']) ? ($FORM['c_country'] == $v['id'] ? ' selected="selected"' : null) : null;?>><?php  echo $v['country_title'];?></option><?php
											}
											?>
										</select>
										<div class="help-block with-errors"><ul class="list-unstyled"></ul></div>
									</div><!-- /.form-group -->
								</div><!-- /.col-lg-4 col-md-4 col-sm-6 col-xs-6 -->
								<?php
							}
							if (in_array((int) $tpl['option_arr']['o_bf_include_email'], array(2,3)))
							{ 
								?>
								<div class="col-lg-4 col-md-4 col-sm-6 col-xs-6">
									<div class="form-group">
										<label for=""><?php __('front_email'); ?><?php echo (int) $tpl['option_arr']['o_bf_include_email'] === 3 ? ' *' : NULL; ?></label>
										<input type="text" id="c_email" name="c_email" class="form-control email<?php echo (int) $tpl['option_arr']['o_bf_include_email'] === 3 ? ' required' : NULL; ?>" value="<?php echo pjSanitize::html(@$FORM['c_email']); ?>" data-msg-required="<?php __('pj_field_required'); ?>" data-msg-email="<?php __('pj_email_validation'); ?>">
						    			<div class="help-block with-errors"><ul class="list-unstyled"></ul></div>
									</div><!-- /.form-group -->
								</div><!-- /.col-lg-4 col-md-4 col-sm-6 col-xs-6 -->
								<?php
							}
							if (in_array((int) $tpl['option_arr']['o_bf_include_phone'], array(2,3)))
							{ 
								?>
								<div class="col-lg-4 col-md-4 col-sm-6 col-xs-6">
									<div class="form-group">
										<label for=""><?php __('front_phone'); ?><?php echo (int) $tpl['option_arr']['o_bf_include_phone'] === 3 ? ' *' : NULL; ?></label>
										<input type="text" id="c_phone" name="c_phone" class="form-control<?php echo (int) $tpl['option_arr']['o_bf_include_phone'] === 3 ? ' required' : NULL; ?>" value="<?php echo pjSanitize::html(@$FORM['c_phone']); ?>" data-msg-required="<?php __('pj_field_required'); ?>">
						    			<div class="help-block with-errors"><ul class="list-unstyled"></ul></div>
									</div><!-- /.form-group -->
								</div><!-- /.col-lg-4 col-md-4 col-sm-6 col-xs-6 -->
								<?php
							}
							if (in_array((int) $tpl['option_arr']['o_bf_include_notes'], array(2,3)))
							{ 
								?>
								<div class="col-lg-8 col-md-8 col-sm-12 col-xs-12">
									<div class="form-group">
										<label for=""><?php __('front_notes'); ?><?php echo (int) $tpl['option_arr']['o_bf_include_notes'] === 3 ? ' *' : NULL; ?></label>
										<textarea id="c_notes" name="c_notes" cols="30" rows="10" class="form-control<?php echo (int) $tpl['option_arr']['o_bf_include_notes'] === 3 ? ' required' : NULL; ?>" style="height: 150px;" data-msg-required="<?php __('pj_field_required'); ?>"><?php echo pjSanitize::html(@$FORM['c_notes']); ?></textarea>
						    			<div class="help-block with-errors"><ul class="list-unstyled"></ul></div>
									</div><!-- /.form-group -->
								</div><!-- /.col-lg-8 col-md-8 col-sm-12 col-xs-12 -->
								<?php
							} 
							?>
						</div><!-- /.row -->
					</section><!-- /.pjMrBFormSection -->

					<section class="pjMrBFormSection">
						<p class="pjMrBCheckoutTitle"><?php __('front_billing_address')?></p><!-- /.pjMrBCheckoutTitle -->

						<div class="row">
							<?php
							if (in_array((int) $tpl['option_arr']['o_bf_include_company'], array(2,3)))
							{
								?>
								<div class="col-lg-4 col-md-4 col-sm-6 col-xs-6">
									<div class="form-group">
										<label for=""><?php __('front_company'); ?><?php echo (int) $tpl['option_arr']['o_bf_include_company'] === 3 ? ' *' : NULL; ?></label>
										<input type="text" id="c_company" name="c_company" class="form-control<?php echo (int) $tpl['option_arr']['o_bf_include_company'] === 3 ? ' required' : NULL; ?>" value="<?php echo pjSanitize::html(@$FORM['c_company']); ?>" data-msg-required="<?php __('pj_field_required'); ?>">
						    			<div class="help-block with-errors"><ul class="list-unstyled"></ul></div>
									</div><!-- /.form-group -->
								</div><!-- /.col-lg-4 col-md-4 col-sm-6 col-xs-6 -->
								<?php
							}
							?>
						</div><!-- /.row -->
					</section><!-- /.pjMrBFormSection -->
					<?php
					if ($tpl['option_arr']['o_payment_disable'] == 'No')
					{ 
						?>
						<section class="pjMrBFormSection">
							<p class="pjMrBCheckoutTitle"><?php __('front_payment_method')?></p><!-- /.pjMrBCheckoutTitle -->
	
							<div class="row">
								<div class="col-lg-4 col-md-4 col-sm-6 col-xs-6">
									<div class="form-group">
										<label for=""><?php __('front_payment_method')?></label>
										<select name="payment_method" class="form-control required" data-msg-required="<?php __('pj_field_required'); ?>">
											<option value="">-- <?php __('front_choose');?> --</option>
											<?php
											foreach (__('payment_methods', true) as $k => $v)
											{
												if ($tpl['option_arr']['o_allow_' . $k] === "Yes")
												{
													?><option value="<?php echo $k; ?>"<?php echo @$FORM['payment_method'] != $k ? NULL : ' selected="selected"'; ?>><?php echo $v; ?></option><?php
												}
											}
											?>
										</select>
										<div class="help-block with-errors"><ul class="list-unstyled"></ul></div>
									</div><!-- /.form-group -->
								</div><!-- /.col-lg-4 col-md-4 col-sm-6 col-xs-6 -->
							</div><!-- /.row -->
							<div class="row pjMrbBankWrap" style="display: <?php echo @$FORM['payment_method'] != 'bank' ? 'none' : NULL; ?>">
								<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
									<div class="form-group">
										<label class="text-muted"><strong><?php echo nl2br(pjSanitize::html($tpl['option_arr']['o_bank_account'])); ?></strong></label>
									</div><!-- /.form-group -->
								</div><!-- /.col-lg-6 col-md-6 col-sm-6 col-xs-6 -->
							</div>
							<div class="row pjMrbCcWrap" style="display: <?php echo @$FORM['payment_method'] != 'creditcard' ? 'none' : NULL; ?>">
								<div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
									<div class="form-group">
										<label for=""><?php __('front_cc_type')?></label>
										<select name="cc_type" class="form-control required" data-msg-required="<?php __('pj_field_required'); ?>">
								    		<option value="">---</option>
								    		<?php
											foreach (__('cc_types', true) as $k => $v)
											{
												?><option value="<?php echo $k; ?>"<?php echo @$FORM['cc_type'] != $k ? NULL : ' selected="selected"'; ?>><?php echo $v; ?></option><?php
											}
											?>
								    	</select>
								    	<div class="help-block with-errors"><ul class="list-unstyled"></ul></div>
									</div><!-- /.form-group -->
								</div><!-- /.col-lg-6 col-md-6 col-sm-6 col-xs-6 -->
	
								<div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
									<div class="form-group">
										<label for=""><?php __('front_cc_num')?></label>
										<input type="text" name="cc_num" class="form-control required" value="<?php echo pjSanitize::html(@$FORM['cc_num']); ?>"  autocomplete="off" data-msg-required="<?php __('pj_field_required'); ?>"/>
						    			<div class="help-block with-errors"><ul class="list-unstyled"></ul></div>
									</div><!-- /.form-group -->
								</div><!-- /.col-lg-6 col-md-6 col-sm-6 col-xs-6 -->
	
								<div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
									<div class="form-group">
										<label for=""><?php __('front_cc_code')?></label>
										<input type="text" name="cc_code" class="form-control required" value="<?php echo pjSanitize::html(@$FORM['cc_code']); ?>"  autocomplete="off" data-msg-required="<?php __('pj_field_required'); ?>"/>
						    			<div class="help-block with-errors"><ul class="list-unstyled"></ul></div>
									</div><!-- /.form-group -->
								</div><!-- /.col-lg-6 col-md-6 col-sm-6 col-xs-6 -->
	
								<div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
									<div class="form-group">
										<label for=""><?php __('front_cc_exp')?></label>
										
										<div class="row">
											<div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
												<?php
												$rand = rand(1, 99999);
												$time = pjTime::factory()
													->attr('name', 'cc_exp_month')
													->attr('id', 'cc_exp_month_' . $rand)
													->attr('class', 'form-control required')
													->prop('format', 'F');
												if (isset($FORM['cc_exp_month']) && !is_null($FORM['cc_exp_month']))
												{
													$time->prop('selected', $FORM['cc_exp_month']);
												}
												echo $time->month();
												?>
											</div><!-- /.col-lg-6 col-md-6 col-sm-6 col-xs-6 -->
	
											<div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
												<?php
												$time = pjTime::factory()
													->attr('name', 'cc_exp_year')
													->attr('id', 'cc_exp_year_' . $rand)
													->attr('class', 'form-control required')
													->prop('left', 0)
													->prop('right', 10);
												if (isset($FORM['cc_exp_year']) && !is_null($FORM['cc_exp_year']))
												{
													$time->prop('selected', $FORM['cc_exp_year']);
												}
												echo $time->year();
												?>
											</div><!-- /.col-lg-6 col-md-6 col-sm-6 col-xs-6 -->
										</div><!-- /.row -->
									</div><!-- /.form-group -->
								</div><!-- /.col-lg-6 col-md-6 col-sm-6 col-xs-6 -->
							</div><!-- /.row -->
						</section><!-- /.pjMrBFormSection -->
						<?php
					}
					if (in_array((int) $tpl['option_arr']['o_bf_include_captcha'], array(3)))
					{ 
						?>
						<section class="pjMrBFormSection">
							<p class="pjMrBCheckoutTitle"><?php __('front_human_verification');?></p><!-- /.pjMrBCheckoutTitle -->
	
							<div class="form-group pjMrBFormCaptcha">
								<div>
									<label for=""><?php __('front_captcha'); ?> *</label>
																		
									<input type="text" id="pjMrCaptchaField_<?php echo $index;?>" name="captcha" class="form-control<?php echo (int) $tpl['option_arr']['o_bf_include_captcha'] === 3 ? ' required' : NULL; ?>" maxlength="6" autocomplete="off" data-msg-required="<?php __('pj_field_required'); ?>" data-msg-remote="<?php __('front_incorrect_captcha');?>">
									<img id="pjMrCaptchaImage_<?php echo $index;?>" src="<?php echo PJ_INSTALL_URL; ?>index.php?controller=pjFront&amp;action=pjActionCaptcha&amp;rand=<?php echo rand(1, 99999); ?><?php echo isset($_GET['session_id']) ? '&session_id=' . pjObject::escapeString($_GET['session_id']) : NULL;?>" alt="Captcha" style="vertical-align: middle; cursor:pointer;" />
								</div>
								<div class="help-block with-errors"><ul class="list-unstyled"></ul></div>
							</div><!-- /.form-group pjMrBFormCaptcha -->
						</section><!-- /.pjMrBFormSection -->
						<?php
					} 
					$terms = __('front_agree_with_terms', true);
					if(!empty($tpl['terms_conditions']))
					{
						$terms = str_replace("{STAG}", '<a href="#" class="pjTbModalTrigger" data-pj-toggle="modal" data-pj-target="#pjNcbTermModal" data-title="'.__('front_terms_title', true).'">', $terms);
						$terms = str_replace("{ETAG}", '</a>', $terms);
					}else{
						$terms = str_replace("{STAG}", '', $terms);
						$terms = str_replace("{ETAG}", '', $terms);
					}
					?>

					<section class="pjMrBFormSection">
						<p class="pjMrBCheckoutTitle"><?php __('front_terms_and_conditions');?></p><!-- /.pjMrBCheckoutTitle -->

						<div class="form-group">
							<div class="pjMrBCheckbox pjMrBCustomCheckbox">
								<input type="checkbox" id="pjMrBCCheckboxTerms" name="terms" value="1" class="required" data-msg-required="<?php __('pj_field_required'); ?>">
																
								<label for="pjMrBCCheckboxTerms">
									<span class="pjMrBCustomCheckboxFake">
										<span class="glyphicon glyphicon-ok" aria-hidden="true"></span>
									</span>
																
									<?php echo $terms;?>
								</label>
							</div><!-- /.pjMrBCheckbox pjMrBCustomCheckbox -->
																
							<div class="help-block with-errors"><ul class="list-unstyled"></ul></div>
						</div><!-- /.form-group -->
					</section><!-- /.pjMrBFormSection -->
					<?php
					$data_load = 'loadFoodDrinks';
					if($tpl['find_menu']['hide_both'] == true)
						{
							$data_load = 'loadBook';
						}elseif($tpl['find_menu']['hide_both'] == false && $tpl['find_menu']['hide_equipment'] == true && $tpl['find_menu']['hide_food_drinks'] == false){
							$data_load = 'loadFoodDrinks';
						}elseif($tpl['find_menu']['hide_both'] == false && $tpl['find_menu']['hide_food_drinks'] == true && $tpl['find_menu']['hide_equipment'] == false){
							$data_load = 'loadEquipment';
						}
					?>
					<div class="clearfix pjMrBFormActions">
						<a href="#" data-load="<?php echo $data_load;?>" class="btn btn-default pull-left pjMrbMenuItem"><?php __('front_btn_back');?></a>
						<button type="submit" class="btn btn-primary pull-right"><?php __('front_btn_preview_booking');?></button>
					</div><!-- /.clearfix pjMrBFormActions -->
				</form>
				
			</div><!-- /.pjMrBForm pjMrBFormCheckout -->
		</div><!-- /.panel-body -->
	</div><!-- /.panel panel-default pjMrBCheckout -->
</div><!-- /.pjMrBBody -->