<div class="pjMrBBody">
	<div class="panel panel-default pjMrBCheckout">
		
		<div class="panel-body">
			<div class="pjMrBForm pjMrBFormCheckout">
				<section class="pjMrBFormSection">
					<div class="row">
						<div class="col-sm-12">
							<?php
							if (isset($tpl['get']['payment_method']))
							{
								$status = __('front_booking_statuses', true);
								switch ($tpl['get']['payment_method'])
								{
									case 'paypal':
										?><p class="text-success text-center"><?php echo $status[2]; ?></p><?php
										if (pjObject::getPlugin('pjPaypal') !== NULL)
										{
											$controller->requestAction(array('controller' => 'pjPaypal', 'action' => 'pjActionForm', 'params' => $tpl['params']));
										}
										break;
									case 'authorize':
										?><p class="text-success text-center"><?php echo $status[3]; ?></p><?php
										if (pjObject::getPlugin('pjAuthorize') !== NULL)
										{
											$controller->requestAction(array('controller' => 'pjAuthorize', 'action' => 'pjActionForm', 'params' => $tpl['params']));
										}
										break;
									case 'bank':
										?><p class="text-success text-center"><?php echo $status[1]; ?></p><?php
										break;
									case 'creditcard':
									case 'cash':
									default:
										?><p class="text-success text-center"><?php echo $status[1]; ?></p><?php
								}
							}
							?>
						</div><!-- /.col-sm-12 -->
						<?php
						if($tpl['get']['payment_method'] == 'bank' || $tpl['get']['payment_method'] == 'creditcard' || $tpl['get']['payment_method'] == 'cash' || $tpl['option_arr']['o_payment_disable'] == 'Yes') 
						{
							?>
							<div class="form-group">
								<div class="col-sm-12 text-center">
									<input type="button" class="btn btn-default pjMrbBtnStartOver" value="<?php __('front_btn_start_over')?>" />
								</div>
							</div>
							<?php
						} 
						?>
					</div>
				</section>
			</div>
		</div>
	</div>
</div>