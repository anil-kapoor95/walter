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
}else{
	
	$week_start = isset($tpl['option_arr']['o_week_start']) && in_array((int) $tpl['option_arr']['o_week_start'], range(0,6)) ? (int) $tpl['option_arr']['o_week_start'] : 0;
	$jqDateFormat = pjUtil::jqDateFormat($tpl['option_arr']['o_date_format']);
	
	?>
	<div class="dashboard_header">
		<div class="item">
			<div class="stat bookings">
				<div class="info">
					<abbr><?php echo $tpl['cnt_bookings_made'];?></abbr>
					<label><?php echo $tpl['cnt_bookings_made'] != 1 ? __('dash_bookings_made_today') : __('dash_booking_made_today');?></label>
				</div>
			</div>
		</div>
		<div class="item">
			<div class="stat bookings">
				<div class="info">
					<abbr><?php echo $tpl['cnt_bookings_today'];?></abbr>
					<label><?php echo $tpl['cnt_bookings_today'] != 1 ? __('dash_bookings_today') : __('dash_booking_today');?></label>
				</div>
			</div>
		</div>
		<div class="item">
			<div class="stat bookings">
				<div class="info">
					<abbr><?php echo $tpl['total_bookings'];?></abbr>
					<label><?php __('dash_total_bookings');?></label>
				</div>
			</div>
		</div>
	</div>
	
	<div class="dashboard_box">
		<div class="dashboard_top">
			<div class="dashboard_column_top"><?php __('dash_latest_bookings');?></div>
			<div class="dashboard_column_top"><?php __('dash_reservations');?></div>
			<div class="dashboard_column_top"><?php __('dash_quick_links');?></div>
		</div>
		<div class="dashboard_middle">
			<div class="dashboard_column">
				<div class="dashboard_list dashboard_latest_list">
					<?php
					if(count($tpl['latest_bookings']) > 0)
					{
						foreach($tpl['latest_bookings'] as $v)
						{
							?>
							<div class="dashboard_row">
								<label><?php echo pjSanitize::html($v['room']);?></label>
								<label><?php __('dash_date');?>: <?php echo date($tpl['option_arr']['o_date_format'], strtotime($v['start_date']));?></label>
								<label><a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminBookings&amp;action=pjActionUpdate&amp;id=<?php echo $v['id'];?>"><?php echo pjSanitize::html($v['c_name']);?></a></label>
							</div>
							<?php
						}
					}else{
						?>
						<div class="dashboard_row"><label><?php __('dash_no_bookings_found');?></label></div>
						<?php
					} 
					?>
				</div>
			</div>
			
			<div class="dashboard_column">
				<div class="dashboard_list dashboard_latest_list">
					<div class="dashboard_row">
						<p>
							<label class="block float_left t7 r3"><?php __('dash_date');?>:</label>
							<span class="block float_left r10">
								<span class="pj-form-field-custom pj-form-field-custom-after">
									<input type="text" id="date" name="date" class="pj-form-field pointer w90 datepicker" value="<?php echo date($tpl['option_arr']['o_date_format'], strtotime($tpl['date']));?>" readonly="readonly" rel="<?php echo $week_start; ?>" rev="<?php echo $jqDateFormat; ?>"/>
									<span class="pj-form-field-after"><abbr class="pj-form-field-icon-date"></abbr></span>
								</span>
							</span>
							<a id="pjMrbPrintBookings" target="_blank" class="block float_left t7" href="#" data-href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdmin&amp;action=pjActionPrint&amp;date={DATE}"><?php __('dash_print');?></a>
						</p>
					</div>
				</div>
				<div id="pjMrbBookingList" class="dashboard_list dashboard_latest_list">
					<?php include_once dirname(__FILE__) . '/pjActionGetBookings.php';?>
				</div>
			</div>
			<div class="dashboard_column">
				<div class="dashboard_list dashboard_latest_list">
					<div class="dashboard_row">
						<a class="block no-decor fs14 b10" href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminBookings&amp;action=pjActionCreate"><?php __('dash_add_booking');?></a>
						<?php
						if($controller->isAdmin())
						{ 
							?>
							<a class="block no-decor fs14 b20" href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminRooms&amp;action=pjActionCreate"><?php __('dash_add_room');?></a>
							<?php
						} 
						?>
						
						<a class="block no-decor fs14 b10" href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminBookings&amp;action=pjActionIndex"><?php __('dash_view_bookings');?></a>
						<?php
						if($controller->isAdmin())
						{ 
							?>
							<a class="block no-decor fs14 b10" href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminRooms&amp;action=pjActionIndex"><?php __('dash_view_rooms');?></a>
							<a class="block no-decor fs14 b10" href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminEquipment&amp;action=pjActionIndex"><?php __('dash_manage_equipment');?></a>
							<a class="block no-decor fs14 b10" href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminOptions&amp;action=pjActionBookingForm"><?php __('dash_configure_booking_form');?></a>
							<a class="block no-decor fs14 b10" href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjLocale&amp;action=pjActionIndex"><?php __('dash_setup_languages');?></a>
							<a class="block no-decor fs14 b10" href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjBackup&amp;action=pjActionIndex"><?php __('dash_make_backup');?></a>
							<?php
						} 
						?>
					</div>
				</div>
			</div>
		</div>
		<div class="dashboard_bottom"></div>
	</div>
	
	<div class="clear_left t20 overflow">
		<div class="float_left black t30 t20"><span class="gray"><?php echo ucfirst(__('lblDashLastLogin', true)); ?>:</span> <?php echo pjUtil::formatDate(date('Y-m-d', strtotime($_SESSION[$controller->defaultUser]['last_login'])), 'Y-m-d', $tpl['option_arr']['o_date_format']) . ', ' . pjUtil::formatTime(date('H:i:s', strtotime($_SESSION[$controller->defaultUser]['last_login'])), 'H:i:s', $tpl['option_arr']['o_time_format']); ?></div>
		<div class="float_right overflow">
		<?php
		list($hour, $day, $other) = explode("_", date("H:i_l_F d, Y"));
		$days = __('days', true, false);
		?>
			<div class="dashboard_date">
				<abbr><?php echo $days[date('w')]; ?></abbr>
				<?php echo pjUtil::formatDate(date('Y-m-d'), 'Y-m-d', $tpl['option_arr']['o_date_format']); ?>
			</div>
			<div class="dashboard_hour"><?php echo date($tpl['option_arr']['o_time_format'], time()); ?></div>
		</div>
	</div>
	<?php
}
?>