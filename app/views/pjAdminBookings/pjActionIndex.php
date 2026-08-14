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
	$statuses = __('booking_statuses', true);
	
	$week_start = isset($tpl['option_arr']['o_week_start']) && in_array((int) $tpl['option_arr']['o_week_start'], range(0,6)) ? (int) $tpl['option_arr']['o_week_start'] : 0;
	$jqDateFormat = pjUtil::jqDateFormat($tpl['option_arr']['o_date_format']);
	?>
	
	<?php pjUtil::printNotice(__('infoBookingsListTitle', true, false), __('infoBookingsListDesc', true, false)); ?>
	
	<div class="b10">
		<?php
		if($controller->isAdmin())
		{ 
			?>
			<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="get" class="float_left pj-form r10">
				<input type="hidden" name="controller" value="pjAdminBookings" />
				<input type="hidden" name="action" value="pjActionCreate" />
				<input type="submit" class="pj-button" value="<?php __('btnAddBooking'); ?>" />
			</form>
			<?php
		} 
		?>
		<form action="" method="get" class="float_left pj-form frm-filter">
			<input type="text" name="q" class="pj-form-field pj-form-field-search w150" placeholder="<?php __('btnSearch'); ?>" />
			<button type="button" class="pj-button pj-button-detailed"><span class="pj-button-detailed-arrow"></span></button>
		</form>
		<div class="float_right t5">
			<a href="#" class="pj-button btn-all"><?php __('lblAll');?></a>
			<a href="#" class="pj-button btn-filter btn-status" data-column="status" data-value="pending"><?php echo $statuses['pending']; ?></a>
			<a href="#" class="pj-button btn-filter btn-status" data-column="status" data-value="confirmed"><?php echo $statuses['confirmed']; ?></a>
			<a href="#" class="pj-button btn-filter btn-status" data-column="status" data-value="cancelled"><?php echo $statuses['cancelled']; ?></a>
			
		</div>
		<br class="clear_both" />
	</div>
	
	<div class="pj-form-filter-advanced" style="display: none;">
		<span class="pj-menu-list-arrow"></span>
		<form action="" method="get" class="form pj-form pj-form-search frm-filter-advanced">
			<div class="overflow float_left w340">
				<p>
					<label class="title100"><?php __('lblClient'); ?></label>
					<input type="text" name="client" id="client" class="pj-form-field w150" />
				</p>
				<p>
					<label class="title100"><?php __('lblRoom'); ?></label>
					
					<span class="inline_block">
						<select name="room_id" id="room_id" class="pj-form-field w200">
							<option value="">-- <?php __('lblChoose'); ?> --</option>
							<?php
							if (isset($tpl['room_arr']) && count($tpl['room_arr']) > 0)
							{
								foreach ($tpl['room_arr'] as $v)
								{
									?><option value="<?php echo $v['id']; ?>"><?php echo pjSanitize::html($v['title']); ?></option><?php
									
								}
							}
							?>
						</select>
					</span>
				</p>
				<p>
					<label class="title100">&nbsp;</label>
					<input type="submit" value="<?php __('btnSearch'); ?>" class="pj-button" />
					<input type="reset" value="<?php __('btnCancel'); ?>" class="pj-button" />
				</p>
			</div>
			<div class="overflow float_left">
				<p>
					<label class="title100"><?php __('lblDate'); ?></label>
					<span class="block float_left">
						<span class="pj-form-field-custom pj-form-field-custom-after">
							<input type="text" name="start_date" id="start_date" class="pj-form-field pointer w80 datepicker" readonly="readonly" rel="<?php echo $week_start; ?>" rev="<?php echo $jqDateFormat; ?>"/>
							<span class="pj-form-field-after"><abbr class="pj-form-field-icon-date"></abbr></span>
						</span>
					</span>
					<span class="block float_left t6 r5 l5"><?php __('lblTo');?></span>
					<span class="block float_left">
						<span class="pj-form-field-custom pj-form-field-custom-after">
							<input type="text" name="end_date" id="end_date" class="pj-form-field pointer w80 datepicker" readonly="readonly" rel="<?php echo $week_start; ?>" rev="<?php echo $jqDateFormat; ?>"/>
							<span class="pj-form-field-after"><abbr class="pj-form-field-icon-date"></abbr></span>
						</span>
					</span>
				</p>
				<p>
					<label class="title100"><?php __('lblStatus'); ?></label>
					
					<span class="inline_block">
						<select name="status" id="status" class="pj-form-field w150">
							<option value="">-- <?php __('lblChoose'); ?> --</option>
							<option value="pending"><?php echo $statuses['pending']; ?></option>
							<option value="confirmed"><?php echo $statuses['confirmed']; ?></option>
							<option value="cancelled"><?php echo $statuses['cancelled']; ?></option>
						</select>
					</span>
				</p>
			</div>
			<br class="clear_both" />
		</form>
	</div>

	<div id="grid"></div>
	<script type="text/javascript">
	var pjGrid = pjGrid || {};
	pjGrid.queryString = "";
	<?php
	if (isset($_GET['room_id']) && (int) $_GET['room_id'] > 0)
	{
		?>pjGrid.queryString += "&room_id=<?php echo (int) $_GET['room_id']; ?>";<?php
	}
	
	?>
	var myLabel = myLabel || {};
	myLabel.room = "<?php __('lblRoom'); ?>";
	myLabel.date = "<?php __('lblDate'); ?>";
	myLabel.name = "<?php __('lblName'); ?>";
	myLabel.total = "<?php __('lblTotal'); ?>";
	myLabel.status = "<?php __('lblStatus'); ?>";
	myLabel.delete_selected = "<?php __('delete_selected'); ?>";
	myLabel.delete_confirmation = "<?php __('delete_confirmation'); ?>";
	myLabel.pending = "<?php echo $statuses['pending']; ?>";
	myLabel.confirmed = "<?php echo $statuses['confirmed']; ?>";
	myLabel.cancelled = "<?php echo $statuses['cancelled']; ?>";	
	</script>
	<?php
}
?>