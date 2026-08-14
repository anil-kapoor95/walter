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
				<form id="pjMrbPreviewForm_<?php echo $index;?>" action="#" method="post" data-pj-toggle="validator" role="form">
					<input type="hidden" name="mrbs_preview" value="1" />
					
					<section class="pjMrBFormSection">
						<p class="pjMrBCheckoutTitle"><?php __('front_personal_details');?></p><!-- /.pjMrBCheckoutTitle -->

						<div class="row">
							<?php
							if (in_array((int) $tpl['option_arr']['o_bf_include_title'], array(2,3)))
							{ 
								$personal_titles = __('personal_titles', true);
								?>
								<div class="col-lg-4 col-md-4 col-sm-6 col-xs-6">
									<dl>
										<dt><?php __('front_title'); ?></dt>
										<dd><?php echo $personal_titles[$FORM['c_title']];?></dd>
									</dl>
								</div><!-- /.col-lg-4 col-md-4 col-sm-6 col-xs-6 -->
								<?php
							}
							if (in_array((int) $tpl['option_arr']['o_bf_include_name'], array(2,3)))
							{ 
								?>
								<div class="col-lg-4 col-md-4 col-sm-6 col-xs-6">
									<dl>
										<dt><?php __('front_name'); ?></dt>
										<dd><?php echo pjSanitize::html(@$FORM['c_name']); ?></dd>
									</dl>
								</div><!-- /.col-lg-4 col-md-4 col-sm-6 col-xs-6 -->
								<?php
							}
							if (in_array((int) $tpl['option_arr']['o_bf_include_address'], array(2,3)))
							{ 
								?>
								<div class="col-lg-4 col-md-4 col-sm-6 col-xs-6">
									<dl>
										<dt><?php __('front_address'); ?></dt>
										<dd><?php echo pjSanitize::html(@$FORM['c_address']); ?></dd>
									</dl>
								</div><!-- /.col-lg-4 col-md-4 col-sm-6 col-xs-6 -->
								<?php
							}
							if (in_array((int) $tpl['option_arr']['o_bf_include_zip'], array(2,3)))
							{ 
								?>
								<div class="col-lg-4 col-md-4 col-sm-6 col-xs-6">
									<dl>
										<dt><?php __('front_zip'); ?></dt>
										<dd><?php echo pjSanitize::html(@$FORM['c_zip']); ?></dd>
									</dl>
								</div><!-- /.col-lg-4 col-md-4 col-sm-6 col-xs-6 -->
								<?php
							}
							if (in_array((int) $tpl['option_arr']['o_bf_include_city'], array(2,3)))
							{ 
								?>
								<div class="col-lg-4 col-md-4 col-sm-6 col-xs-6">
									<dl>
										<dt><?php __('front_city'); ?></dt>
										<dd><?php echo pjSanitize::html(@$FORM['c_city']); ?></dd>
									</dl>
								</div><!-- /.col-lg-4 col-md-4 col-sm-6 col-xs-6 -->
								<?php
							}
							
							if (in_array((int) $tpl['option_arr']['o_bf_include_state'], array(2,3)))
							{ 
								?>
								<div class="col-lg-4 col-md-4 col-sm-6 col-xs-6">
									<dl>
										<dt><?php __('front_state'); ?></dt>
										<dd><?php echo pjSanitize::html(@$FORM['c_state']); ?></dd>
									</dl>
								</div><!-- /.col-lg-4 col-md-4 col-sm-6 col-xs-6 -->
								<?php
							}
							if (in_array((int) $tpl['option_arr']['o_bf_include_country'], array(2,3)))
							{ 
								?>
								<div class="col-lg-4 col-md-4 col-sm-6 col-xs-6">
									<dl>
										<dt><?php __('front_country'); ?></dt>
										<?php
										if(isset($tpl['country_arr']['country_title']))
										{ 
											?><dd><?php echo pjSanitize::html($tpl['country_arr']['country_title']); ?></dd><?php
										}else{
											?><dd>&nbsp;</dd><?php
										} 
										?>
									</dl>
								</div><!-- /.col-lg-4 col-md-4 col-sm-6 col-xs-6 -->
								<?php
							}
							if (in_array((int) $tpl['option_arr']['o_bf_include_email'], array(2,3)))
							{ 
								?>
								<div class="col-lg-4 col-md-4 col-sm-6 col-xs-6">
									<dl>
										<dt><?php __('front_email'); ?></dt>
										<dd><?php echo pjSanitize::html(@$FORM['c_email']); ?></dd>
									</dl>
								</div><!-- /.col-lg-4 col-md-4 col-sm-6 col-xs-6 -->
								<?php
							}
							if (in_array((int) $tpl['option_arr']['o_bf_include_phone'], array(2,3)))
							{ 
								?>
								<div class="col-lg-4 col-md-4 col-sm-6 col-xs-6">
									<dl>
										<dt><?php __('front_phone'); ?></dt>
										<dd><?php echo pjSanitize::html(@$FORM['c_phone']); ?></dd>
									</dl>
								</div><!-- /.col-lg-4 col-md-4 col-sm-6 col-xs-6 -->
								<?php
							}
							if (in_array((int) $tpl['option_arr']['o_bf_include_notes'], array(2,3)))
							{ 
								?>
								<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
									<dl>
										<dt><?php __('front_notes'); ?></dt>
										<dd><?php echo nl2br(pjSanitize::html(@$FORM['c_notes'])); ?></dd>
									</dl>
								</div><!-- /.col-lg-12 col-md-12 col-sm-12 col-xs-12 -->
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
									<dl>
										<dt><?php __('front_company'); ?></dt>
										<dd><?php echo pjSanitize::html(@$FORM['c_company']); ?></dd>
									</dl>
								</div><!-- /.col-lg-4 col-md-4 col-sm-6 col-xs-6 -->
								<?php
							}
							?>
						</div><!-- /.row -->
					</section><!-- /.pjMrBFormSection -->
					<?php
					if ($tpl['option_arr']['o_payment_disable'] == 'No')
					{ 
						$payment_methods = __('payment_methods', true);
						$cc_types = __('cc_types', true);
						?>
						<section class="pjMrBFormSection">
							<p class="pjMrBCheckoutTitle"><?php __('front_payment_method')?></p><!-- /.pjMrBCheckoutTitle -->
	
							<div class="row">
								<div class="col-lg-4 col-md-4 col-sm-6 col-xs-6">
									<dl>
										<dt><?php __('front_payment_method')?></dt>
										<dd><?php echo $payment_methods[$FORM['payment_method']]; ?></dd>
									</dl>
								</div><!-- /.col-lg-4 col-md-4 col-sm-6 col-xs-6 -->
							</div><!-- /.row -->
							<div class="row pjMrbBankWrap" style="display: <?php echo @$FORM['payment_method'] != 'bank' ? 'none' : NULL; ?>">
								<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
									<dl>
										<dd><?php echo nl2br(pjSanitize::html($tpl['option_arr']['o_bank_account'])); ?></dd>
									</dl>
								</div><!-- /.col-lg-6 col-md-6 col-sm-6 col-xs-6 -->
							</div>
							<div class="row pjMrbCcWrap" style="display: <?php echo @$FORM['payment_method'] != 'creditcard' ? 'none' : NULL; ?>">
								<div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
									<dl>
										<dt><?php __('front_payment_method')?></dt>
										<dd><?php echo $payment_methods[$FORM['payment_method']]; ?></dd>
									</dl>
								</div><!-- /.col-lg-6 col-md-6 col-sm-6 col-xs-6 -->
	
								<div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
									<dl>
										<dt><?php __('front_cc_num')?></dt>
										<dd><?php echo pjSanitize::html(@$FORM['cc_num']); ?></dd>
									</dl>
								</div><!-- /.col-lg-6 col-md-6 col-sm-6 col-xs-6 -->
	
								<div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
									<dl>
										<dt><?php __('front_cc_code')?></dt>
										<dd><?php echo pjSanitize::html(@$FORM['cc_code']); ?></dd>
									</dl>
								</div><!-- /.col-lg-6 col-md-6 col-sm-6 col-xs-6 -->
	
								<div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
									<dl>
										<dt><?php __('front_cc_exp')?></dt>
										<dd><?php echo pjSanitize::html(@$FORM['cc_exp_month']); ?>/<?php echo pjSanitize::html(@$FORM['cc_exp_year']); ?></dd>
									</dl>
								</div><!-- /.col-lg-6 col-md-6 col-sm-6 col-xs-6 -->
							</div><!-- /.row -->
						</section><!-- /.pjMrBFormSection -->
						<?php
					}
					?>
					<div class="clearfix pjMrBFormActions">
						<a href="#" data-load="loadCheckout" class="btn btn-default pull-left pjMrbMenuItem"><?php __('front_btn_back');?></a>
						<button type="submit" class="btn btn-primary pull-right"><?php __('front_btn_confirm');?></button>
					</div><!-- /.clearfix pjMrBFormActions -->
				</form>
				
			</div><!-- /.pjMrBForm pjMrBFormCheckout -->
		</div><!-- /.panel-body -->
	</div><!-- /.panel panel-default pjMrBCheckout -->
</div><!-- /.pjMrBBody -->