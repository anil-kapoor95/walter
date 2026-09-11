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
					}elseif($STORE['book_by'] == 'morningafternoon' || $STORE['book_by'] == 'afternoonevening'){
						$price = pjUtil::formatCurrencySign($tpl['arr']['price_half_day'] * 2, $tpl['option_arr']['o_currency']) . ' ' . __('front_half_day', true);
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
					<?php
					if(isset($STORE['layout_id']))
					{ 
						?>
						<dl class="dl-horizontal pjMrBAsideProductMeta">
							<dt><?php __('front_room_layout');?>: </dt>
							<dd><?php echo pjSanitize::html($tpl['layout']['title']);?></dd>
						</dl><!-- /.dl-horizontal pjMrBAsideProductMeta -->
						<?php
					}
					$selected_equipment_id_arr = isset($STORE['equipment_id']) ? $STORE['equipment_id'] : array();
					$units = isset($STORE['units']) ? $STORE['units'] : array();
					if(!empty($selected_equipment_id_arr))
					{
						?>
						<dl class="dl-horizontal pjMrBAsideProductMeta">
							<dt><?php __('front_equipment');?>: </dt>
							<?php
							foreach($tpl['equipment_arr'] as $k => $v)
							{ 
								?>
								<dd><?php echo $v['title'];?> x <?php echo $units[$v['id']];?></dd>
								<?php
							} 
							?>
						</dl><!-- /.dl-horizontal pjMrBAsideProductMeta -->
						<?php
					} 
					?>
				</div><!-- /.col-lg-12 col-md-12 col-sm-12 col-xs-8 pjMrBAsideProductContent -->
			</div><!-- /.row pjMrBAsideProduct -->
		</aside><!-- /.col-lg-4 col-md-4 col-sm-4 col-xs-12 pjMrBAside -->

		<div class="col-lg-8 col-md-8 col-sm-8 col-xs-12 pjMrBContent">
			<p class="pjMrBMainTitle"><?php __('front_food_drinks');?></p><!-- /.pjMrBMainTitle -->

			<div class="pjMrBForm pjMrBFormRoomSetup">
				<form id="pjMrbFoodDrinksForm_<?php echo $index;?>" action="#" method="post">
					<section class="pjMrBFormSection">
						<div class="pjMrBFormSectionContent">
							<ul class="list-unstyled pjMrBCustomCheckboxesExtras">
								<?php
								$selected_food_drink_id_arr = isset($STORE['food_drink_id']) ? $STORE['food_drink_id'] : array();
								$people = isset($STORE['people']) ? $STORE['people'] : array();
								
								foreach($tpl['food_drink_arr'] as $k => $v)
								{ 
									?>
									<li>
										<div class="pjMrBCheckbox pjMrBCustomCheckbox">
											<input type="checkbox" id="pjMrBCheckboxExtra<?php echo $v['id'];?>" name="food_drink_id[<?php echo $v['id'];?>]"<?php echo array_key_exists($v['id'], $selected_food_drink_id_arr) ? ' checked="checked"' : NULL;?> value="<?php echo $v['price'];?>" autocomplete="off" />
	
											<div class="row">
												<div class="col-lg-5 col-md-5 col-sm-12 col-xs-5">
													<label for="pjMrBCheckboxExtra<?php echo $v['id'];?>">
														<span class="pjMrBCustomCheckboxFake">
															<span class="glyphicon glyphicon-ok" aria-hidden="true"></span>
														</span>
	
														<span><?php echo pjSanitize::html($v['title']);?></span>
													</label>
												</div><!-- /.col-lg-5 col-md-5 col-sm-12 col-xs-5 -->
	
												<div class="col-lg-4 col-md-4 col-sm-6 col-xs-3">
													<div class="pjMrBSpinner">
														<input type="text" class="form-control pjMrbPeople" name="people[<?php echo $v['id'];?>]" value="<?php echo isset($people[$v['id']]) ? $people[$v['id']] : (isset($STORE['attendees']) ? $STORE['attendees'] : 1);?>" autocomplete="off"  data-min="1" />
														
														<a href="#" class="pjMrBSpinnerBtn pjMrBSpinnerBtnUp">
															<span class="caret"></span>
														</a>
	
														<a href="#" class="pjMrBSpinnerBtn pjMrBSpinnerBtnDown">
															<span class="caret"></span>
														</a>
													</div><!-- /.pjMrBSpinner -->
	
													<p><?php __('front_people');?></p>
												</div><!-- /.col-lg-4 col-md-4 col-sm-6 col-xs-3 -->
	
												<div class="col-lg-3 col-md-3 col-sm-6 col-xs-3">
													<p><strong><?php echo pjUtil::formatCurrencySign($v['price'], $tpl['option_arr']['o_currency']);?> <?php __('front_per_person');?></strong></p>
												</div><!-- /.col-lg-3 col-md-3 col-sm-6 col-xs-3 -->
											</div><!-- /.row -->
										</div><!-- /.pjMrBCheckbox pjMrBCustomCheckbox -->
									</li>
									<?php
								} 
								?>
								
							</ul><!-- /.list-unstyled pjMrBCustomCheckboxesExtras -->
						</div><!-- /.pjMrBFormSectionContent -->
					</section><!-- /.pjMrBFormSection -->

					<div class="pjMrBFormActions">
						<a href="#" data-load="<?php echo $tpl['find_menu']['hide_equipment'] == true ? 'loadBook' : 'loadEquipment';?>" class="btn btn-default pull-left pjMrbMenuItem"><?php __('front_btn_back');?></a>
						<button type="submit" class="btn btn-primary pull-right"><?php __('front_btn_next');?></button>
					</div><!-- /.pjMrBFormActions -->
				</form>
			</div><!-- /.pjMrBForm pjMrBFormRoomSetup -->
		</div><!-- /.col-lg-8 col-md-8 col-sm-8 col-xs-12 pjMrBContent -->
	</div><!-- /.row pjMrBMain -->
</div><!-- /.pjMrBBody -->