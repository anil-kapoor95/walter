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
	$titles = __('error_titles', true);
	$bodies = __('error_bodies', true);
	if (isset($_GET['err']))
	{
		pjUtil::printNotice(@$titles[$_GET['err']], @$bodies[$_GET['err']]);
	}
	include PJ_VIEWS_PATH . 'pjLayouts/elements/optmenu.php';
	include dirname(__FILE__) . '/elements/menu_options.php';
	
	pjUtil::printNotice(@$titles['AT05'], @$bodies['AT05']);
	
	$week_start = isset($tpl['option_arr']['o_week_start']) && in_array((int) $tpl['option_arr']['o_week_start'], range(0,6)) ? (int) $tpl['option_arr']['o_week_start'] : 0;
	$jqDateFormat = pjUtil::jqDateFormat($tpl['option_arr']['o_date_format']);
	$show_period = 'false';
	if((strpos($tpl['option_arr']['o_time_format'], 'a') > -1 || strpos($tpl['option_arr']['o_time_format'], 'A') > -1))
	{
		$show_period = 'true';
	}
	?>
	<form action="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminTime&amp;action=pjActionCustom" method="post" class="form" id="frmTimeCustom">
		<input type="hidden" name="custom_time" value="1" />
		<input type="hidden" name="csrf_token" value="<?php echo pjAppController::getCsrfToken(); ?>" />
		<?php
		if (isset($_GET['foreign_id']) && (int) $_GET['foreign_id'] > 0)
		{
			?><input type="hidden" name="foreign_id" value="<?php echo (int) $_GET['foreign_id']; ?>" /><?php
		}
		?>
		<fieldset class="fieldset white">
			<legend><?php __('time_custom'); ?></legend>
			<div class="float_left w350">
				<p>
					<label class="title"><?php __('time_date'); ?></label>
					<span class="pj-form-field-custom pj-form-field-custom-after">
						<input type="text" name="date" id="date" class="pj-form-field w80 datepick pointer required" readonly="readonly" rel="<?php echo $week_start; ?>" rev="<?php echo $jqDateFormat; ?>" />
						<span class="pj-form-field-after"><abbr class="pj-form-field-icon-date"></abbr></span>
					</span>
				</p>
				<p class="business">
					<label class="title"><?php __('time_from'); ?></label>
					<span class="inline-block">
						<input name="start" class="pj-timepicker pj-form-field w80 required"/>
					</span>
				</p>
				<p class="business">
					<label class="title"><?php __('time_to'); ?></label>
					<span class="inline-block">
						<input name="end" class="pj-timepicker pj-form-field w80 required"/>
					</span>
				</p>
				<p>
					<label class="title">&nbsp;</label>
					<input type="submit" value="<?php __('btnSave', false, true); ?>" class="pj-button"  />
				</p>
			</div>
			<div class="float_right w350">
				<p>
					<label class="title"><?php __('time_is'); ?></label>
					<span class="block float_left t5 b10"><input type="checkbox" name="is_dayoff" id="is_dayoff" value="T" /></span>
				</p>
				<p class="business">
					<label class="title"><?php __('time_morning_from'); ?></label>
					<span class="inline-block">
						<input name="start_morning" class="pj-timepicker pj-form-field w80 required"/>
					</span>
				</p>
				<p class="business">
					<label class="title"><?php __('time_morning_to'); ?></label>
					<span class="inline-block">
						<input name="end_morning" class="pj-timepicker pj-form-field w80 required"/>
					</span>
				</p>
				<p class="business">
					<label class="title"><?php __('time_afternoon_from'); ?></label>
					<span class="inline-block">
						<input name="start_afternoon" class="pj-timepicker pj-form-field w80 required"/>
					</span>
				</p>
				<p class="business">
					<label class="title"><?php __('time_afternoon_to'); ?></label>
					<span class="inline-block">
						<input name="end_afternoon" class="pj-timepicker pj-form-field w80 required"/>
					</span>
				</p>
			</div>
			<br class="clear_both" />
		</fieldset>
	</form>
	
	<div class="b10">
		<?php
		$yesno = __('_yesno', true);
		?>
		<div class="float_right">
			<a href="#" class="pj-button btn-all"><?php __('lblAll'); ?></a>
			<a href="#" class="pj-button btn-filter btn-status" data-column="is_dayoff" data-value="T"><?php echo $yesno['T']; ?></a>
			<a href="#" class="pj-button btn-filter btn-status" data-column="is_dayoff" data-value="F"><?php echo $yesno['F']; ?></a>
		</div>
		<br class="clear_right" />
	</div>
	
	<div id="grid"></div>
	<?php
	$day_names = __('day_names', true);
	$months = __('months', true);
	ksort($day_names);
	ksort($months);
	?>
	<script type="text/javascript">
	var pjGrid = pjGrid || {};
	pjGrid.jsDateFormat = "<?php echo pjUtil::jsDateFormat($tpl['option_arr']['o_date_format']); ?>";
	var myLabel = myLabel || {};
	myLabel.showperiod = <?php echo $show_period; ?>;
	myLabel.time_date = "<?php __('time_date', false, true); ?>";
	myLabel.time_start = "<?php __('time_from', false, true); ?>";
	myLabel.time_end = "<?php __('time_to', false, true); ?>";
	myLabel.time_morning = "<?php __('time_morning', false, true); ?>";
	myLabel.time_afternoon = "<?php __('time_afternoon', false, true); ?>";
	myLabel.time_morning_start = "<?php __('time_morning_from', false, true); ?>";
	myLabel.time_morning_end = "<?php __('time_morning_to', false, true); ?>";
	myLabel.time_afternoon_start = "<?php __('time_afternoon_from', false, true); ?>";
	myLabel.time_afternoon_end = "<?php __('time_afternoon_to', false, true); ?>";
	myLabel.time_dayoff = "<?php __('time_is', false, true); ?>";
	myLabel.time_yesno = <?php echo pjAppController::jsonEncode(__('_yesno', true)); ?>;
	myLabel.delete_selected = "<?php __('delete_selected', false, true); ?>";
	myLabel.delete_confirmation = "<?php __('delete_confirmation', false, true); ?>";

	myLabel.monthNames = ["<?php echo join('","', $months); ?>"];
	myLabel.dayNamesMin = ["<?php echo join('","', $day_names); ?>"];
	</script>
	<?php
}
?>