<header class="panel-heading">
	<p class="pjMrBCheckoutTitle"><?php __('front_your_booking');?></p><!-- /.pjMrBCheckoutTitle -->

	<section class="pjMrBCheckoutSection">
		<div class="row">
			<div class="col-lg-5 col-md-5 col-sm-5 col-xs-12">
				<p><strong><?php echo pjSanitize::html($tpl['arr']['title']);?></strong></p>
			</div><!-- /.col-lg-5 col-md-5 col-sm-5 col-xs-12 -->

			<div class="col-lg-3 col-md-3 col-sm-3 col-xs-4">
				<p><strong><?php echo $tpl['arr']['capacity'];?> <?php $tpl['arr']['capacity'] != 1 ? __('front_people') : __('front_person');?></strong></p>
			</div><!-- /.col-lg-3 col-md-3 col-sm-3 col-xs-4 -->

			<?php
			$price = '';
			if($STORE['book_by'] == 'multiday')
			{
				$price = pjUtil::formatCurrencySign($tpl['arr']['price_per_day'], $tpl['option_arr']['o_currency']) . ' ' . __('front_per_day', true);
			}elseif($STORE['book_by'] == 'morning' || $STORE['book_by'] == 'afternoon'){
				$price = pjUtil::formatCurrencySign($tpl['arr']['price_half_day'], $tpl['option_arr']['o_currency']) . ' ' . __('front_half_day', true);
			}else{
				$price = pjUtil::formatCurrencySign($tpl['arr']['price_per_hour'], $tpl['option_arr']['o_currency']) . ' ' . __('front_per_hour', true);
			}
			?>
			<div class="col-lg-3 col-md-3 col-sm-3 col-xs-5">
				<p><?php echo $price;?></p>
			</div><!-- /.col-lg-3 col-md-3 col-sm-3 col-xs-5 -->

			<div class="col-lg-1 col-md-1 col-sm-1 col-xs-3">
				<p><strong><?php echo pjUtil::formatCurrencySign(number_format($tpl['price_arr']['room_price'], 2), $tpl['option_arr']['o_currency']);?></strong></p>
			</div><!-- /.col-lg-1 col-md-1 col-sm-1 col-xs-3 -->
		</div><!-- /.row -->

		<div class="row">
			<div class="col-lg-5 col-md-5 col-sm-5 col-xs-6">
				<p><?php $STORE['book_by'] == 'multiday' ? __('front_start_date') : __('front_date');?>: </p>
			</div><!-- /.col-lg-5 col-md-5 col-sm-5 col-xs-6 -->

			<div class="col-lg-7 col-md-7 col-sm-7 col-xs-6">
				<p><strong><?php echo date($tpl['option_arr']['o_date_format'], strtotime($STORE['start_date']));?></strong></p>
			</div><!-- /.col-lg-7 col-md-7 col-sm-7 col-xs-6 -->
		</div><!-- /.row -->
		<?php
		if($STORE['book_by'] == 'multiday')
		{
			?>
			<div class="row">
				<div class="col-lg-5 col-md-5 col-sm-5 col-xs-6">
					<p><?php __('front_end_date');?>: </p>
				</div><!-- /.col-lg-5 col-md-5 col-sm-5 col-xs-6 -->

				<div class="col-lg-7 col-md-7 col-sm-7 col-xs-6">
					<p><strong><?php echo date($tpl['option_arr']['o_date_format'], strtotime($STORE['end_date']));?></strong></p>
				</div><!-- /.col-lg-7 col-md-7 col-sm-7 col-xs-6 -->
			</div><!-- /.row -->
			<?php
		} 
		if($STORE['book_by'] == 'hour')
		{
			$selected_slots = isset($STORE['slots']) ? $STORE['slots'] : array();
			?>
			<div class="row">
				<div class="col-lg-5 col-md-5 col-sm-5 col-xs-6">
					<p><?php __('front_from_to');?>: </p>
				</div><!-- /.col-lg-5 col-md-5 col-sm-5 col-xs-6 -->

				<div class="col-lg-7 col-md-7 col-sm-7 col-xs-6">
					<?php
					foreach($selected_slots as $k => $pair)
					{ 
						list($start_ts, $end_ts) = explode("|", $pair);
						?>
						<p><strong><?php echo date($tpl['option_arr']['o_time_format'],$start_ts);?> - <?php echo date($tpl['option_arr']['o_time_format'],$end_ts);?></strong></p>
						<?php
					} 
					?>
				</div><!-- /.col-lg-7 col-md-7 col-sm-7 col-xs-6 -->
			</div><!-- /.row -->
			<?php
		} 
		?>

		<div class="row">
			<div class="col-lg-5 col-md-5 col-sm-5 col-xs-6">
				<p><?php __('front_attendees');?></p>
			</div><!-- /.col-lg-5 col-md-5 col-sm-5 col-xs-6 -->

			<div class="col-lg-7 col-md-7 col-sm-7 col-xs-6">
				<p><strong><?php echo $STORE['attendees'];?></strong></p>
			</div><!-- /.col-lg-7 col-md-7 col-sm-7 col-xs-6 -->
		</div><!-- /.row -->
	</section><!-- /.pjMrBCheckoutSection -->

	<section class="pjMrBCheckoutSection">
		<?php
		if(isset($STORE['layout_id']))
		{ 
			?>
			<div class="row">
				<div class="col-lg-5 col-md-5 col-sm-5 col-xs-6">
					<p><?php __('front_room_layout');?>: </p>
				</div><!-- /.col-lg-5 col-md-5 col-sm-5 col-xs-6 -->
	
				<div class="col-lg-7 col-md-7 col-sm-7 col-xs-6">
					<p><strong><?php echo pjSanitize::html($tpl['layout']['title']);?></strong></p>
				</div><!-- /.col-lg-7 col-md-7 col-sm-7 col-xs-6 -->
			</div><!-- /.row -->
			<?php
		}
		$selected_equipment_id_arr = isset($STORE['equipment_id']) ? $STORE['equipment_id'] : array();
		$units = isset($STORE['units']) ? $STORE['units'] : array();
		if(!empty($selected_equipment_id_arr))
		{
			$price_per = array();
			$price_per['booking'] = __('lblPricePerBooking', true);
			$price_per['hour'] = __('lblPricePerHour', true);
			foreach($tpl['equipment_arr'] as $k => $v)
			{
				list($price, $per) = explode("|", $selected_equipment_id_arr[$v['id']]);
				if($per == 'booking')
				{
					$equipment_price = $price * (isset($units[$v['id']]) ? (int) $units[$v['id']] : 0);
				}else{
					$equipment_price = $price * $tpl['price_arr']['hours'] * (isset($units[$v['id']]) ? (int) $units[$v['id']] : 0);
				}
				?>
				<div class="row">
					<div class="col-lg-5 col-md-5 col-sm-5 col-xs-6">
						<p><?php echo $k == 0 ? __('front_equipment', true) . ':' : '&nbsp;';?> </p>
					</div><!-- /.col-lg-5 col-md-5 col-sm-5 col-xs-6 -->

					<div class="col-lg-3 col-md-3 col-sm-3 col-xs-6">
						<p><strong><?php echo pjSanitize::html($v['title']);?> x <?php echo $units[$v['id']];?></strong></p>
					</div><!-- /.col-lg-3 col-md-3 col-sm-3 col-xs-6 -->

					<div class="col-lg-3 col-md-3 col-sm-3 col-xs-6">
						<p><?php echo pjUtil::formatCurrencySign($v['price'], $tpl['option_arr']['o_currency']) . ' ' . $price_per[$v['price_per']];?></p>
					</div><!-- /.col-lg-3 col-md-3 col-sm-3 col-xs-6 -->

					<div class="col-lg-1 col-md-1 col-sm-1 col-xs-6">
						<p><strong><?php echo  pjUtil::formatCurrencySign(number_format($equipment_price, 2), $tpl['option_arr']['o_currency']);?></strong></p>
					</div><!-- /.col-lg-1 col-md-1 col-sm-1 col-xs-6 -->
				</div><!-- /.row -->
				<?php
			}
		} 
		
		$selected_food_drink_id_arr = isset($STORE['food_drink_id']) ? $STORE['food_drink_id'] : array();
		$people = isset($STORE['people']) ? $STORE['people'] : array();
		if(!empty($selected_food_drink_id_arr))
		{
			foreach($tpl['food_drink_arr'] as $k => $v)
			{
				$food_drink_price = $v['price'] * (isset($people[$v['id']]) ? (int) $people[$v['id']] : 0);
				?>
				<div class="row">
					<div class="col-lg-5 col-md-5 col-sm-5 col-xs-12">
						<p><?php echo $k == 0 ? __('front_food_drinks', true) . ':' : '&nbsp;';?> </p>
					</div><!-- /.col-lg-5 col-md-5 col-sm-5 col-xs-12 -->

					<div class="col-lg-3 col-md-3 col-sm-3 col-xs-5">
						<p><strong><?php echo pjSanitize::html($v['title']);?> x <?php echo $people[$v['id']];?></strong></p>
					</div><!-- /.col-lg-3 col-md-3 col-sm-3 col-xs-5 -->

					<div class="col-lg-3 col-md-3 col-sm-3 col-xs-4">
						<p><?php echo pjUtil::formatCurrencySign($v['price'], $tpl['option_arr']['o_currency']) . ' ' . __('front_per_person', true);?></p>
					</div><!-- /.col-lg-3 col-md-3 col-sm-3 col-xs-4 -->

					<div class="col-lg-1 col-md-1 col-sm-1 col-xs-3">
						<p><strong><?php echo pjUtil::formatCurrencySign(number_format($food_drink_price, 2), $tpl['option_arr']['o_currency']);?></strong></p>
					</div><!-- /.col-lg-1 col-md-1 col-sm-1 col-xs-3 -->
				</div><!-- /.row -->
				<?php
			}
		} 
		?>
	</section><!-- /.pjMrBCheckoutSection -->

	<section class="pjMrBCheckoutSection">
		<div class="row">
			<div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 text-left">
				<p><?php __('front_subtotal');?>: </p>
			</div><!-- /.col-lg-6 col-md-6 col-sm-6 col-xs-6 text-left -->

			<div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 text-right">
				<p class="pjMrBPrice"><strong><?php echo pjUtil::formatCurrencySign(number_format($tpl['price_arr']['subtotal'], 2), $tpl['option_arr']['o_currency']);?></strong></p><!-- /.pjMrBPrice -->
			</div><!-- /.col-lg-6 col-md-6 col-sm-6 col-xs-6 text-right -->
		</div><!-- /.row -->

		<div class="row">
			<div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 text-left">
				<p><?php __('front_tax');?>: </p>
			</div><!-- /.col-lg-6 col-md-6 col-sm-6 col-xs-6 text-left -->

			<div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 text-right">
				<p class="pjMrBPrice"><strong><?php echo pjUtil::formatCurrencySign(number_format($tpl['price_arr']['tax'], 2), $tpl['option_arr']['o_currency']);?></strong></p><!-- /.pjMrBPrice -->
			</div><!-- /.col-lg-6 col-md-6 col-sm-6 col-xs-6 text-right -->
		</div><!-- /.row -->
		<div class="row">
			<div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 text-left">
				<p><?php __('front_total');?>: </p>
			</div><!-- /.col-lg-6 col-md-6 col-sm-6 col-xs-6 text-left -->

			<div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 text-right">
				<p class="pjMrBPrice"><strong><?php echo pjUtil::formatCurrencySign(number_format($tpl['price_arr']['total'], 2), $tpl['option_arr']['o_currency']);?></strong></p><!-- /.pjMrBPrice -->
			</div><!-- /.col-lg-6 col-md-6 col-sm-6 col-xs-6 text-right -->
		</div><!-- /.row -->
		<div class="row">
			<div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 text-left">
				<p><?php __('front_deposit');?>: </p>
			</div><!-- /.col-lg-6 col-md-6 col-sm-6 col-xs-6 text-left -->

			<div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 text-right">
				<p class="pjMrBPrice"><strong><?php echo pjUtil::formatCurrencySign(number_format($tpl['price_arr']['deposit'], 2), $tpl['option_arr']['o_currency']);?></strong></p><!-- /.pjMrBPrice -->
			</div><!-- /.col-lg-6 col-md-6 col-sm-6 col-xs-6 text-right -->
		</div><!-- /.row -->
	</section><!-- /.pjMrBCheckoutSection -->
</header><!-- /.panel-heading -->