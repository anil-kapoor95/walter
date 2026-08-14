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
	
	pjUtil::printNotice(@$titles['AT04'], @$bodies['AT04']);
	
	$week_start = isset($tpl['option_arr']['o_week_start']) && in_array((int) $tpl['option_arr']['o_week_start'], range(0,6)) ? (int) $tpl['option_arr']['o_week_start'] : 0;
	$jqDateFormat = pjUtil::jqDateFormat($tpl['option_arr']['o_date_format']);
	$business = $tpl['arr']['is_dayoff'] == 'T' ? 'none' : NULL;
	$show_period = 'false';
	if((strpos($tpl['option_arr']['o_time_format'], 'a') > -1 || strpos($tpl['option_arr']['o_time_format'], 'A') > -1))
	{
		$show_period = 'true';
	}
	?>
	<form action="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminTime&amp;action=pjActionUpdateCustom" method="post" class="form" id="frmTimeCustom">
		<input type="hidden" name="custom_time" value="1" />
		<input type="hidden" name="csrf_token" value="<?php echo pjAppController::getCsrfToken(); ?>" />
		<input type="hidden" name="id" value="<?php echo @$tpl['arr']['id']; ?>" />
		<?php
		if (isset($_GET['foreign_id']) && (int) $_GET['foreign_id'] > 0)
		{
			?><input type="hidden" name="foreign_id" value="<?php echo (int) $tpl['arr']['foreign_id']; ?>" /><?php
		}
		?>
		<fieldset class="fieldset white">
			<legend><?php __('time_custom'); ?></legend>
			<div class="float_left w350">
				<p>
					<label class="title"><?php __('time_date'); ?></label>
					<span class="pj-form-field-custom pj-form-field-custom-after">
						<input type="text" name="date" id="date" class="pj-form-field w80 datepick pointer required" readonly="readonly" rel="<?php echo $week_start; ?>" rev="<?php echo $jqDateFormat; ?>" value="<?php echo pjUtil::formatDate($tpl['arr']['date'], 'Y-m-d', $tpl['option_arr']['o_date_format']); ?>" />
						<span class="pj-form-field-after"><abbr class="pj-form-field-icon-date"></abbr></span>
					</span>
				</p>
				<p class="business" style="display: <?php echo $business; ?>">
					<label class="title"><?php __('time_from'); ?></label>
					<?php
					$start_time = date($tpl['option_arr']['o_time_format'], strtotime($tpl['arr']['date'] . ' ' . $tpl['arr']['start_time']));
					?>
					<span class="inline-block">
						<input name="start" value="<?php echo $start_time;?>" class="pj-timepicker pj-form-field w80 required"/>
					</span>
				</p>
				<p class="business" style="display: <?php echo $business; ?>">
					<label class="title"><?php __('time_to'); ?></label>
					<?php
					$end_time = date($tpl['option_arr']['o_time_format'], strtotime($tpl['arr']['date'] . ' ' . $tpl['arr']['end_time']));
					?>
					<span class="inline-block">
						<input name="end" value="<?php echo $end_time;?>" class="pj-timepicker pj-form-field w80 required"/>
					</span>
				</p>
				<p>
					<label class="title">&nbsp;</label>
					<input type="submit" value="<?php __('btnSave', false, true); ?>" class="pj-button"  />
					<input type="button" value="<?php __('btnCancel'); ?>" class="pj-button" onclick="window.location.href='<?php echo PJ_INSTALL_URL; ?>index.php?controller=pjAdminTime&action=pjActionCustom';" />
				</p>
			</div>
			<div class="float_right w350">
				<p>
					<label class="title"><?php __('time_is'); ?></label>
					<span class="block float_left t5 b10"><input type="checkbox" name="is_dayoff" id="is_dayoff" value="T"<?php echo $tpl['arr']['is_dayoff'] == 'T' ? ' checked="checked"' : NULL; ?> /></span>
				</p>
				<p class="business" style="display: <?php echo $business; ?>">
					<label class="title"><?php __('time_morning_from'); ?></label>
					<?php
					$start_morning = date($tpl['option_arr']['o_time_format'], strtotime($tpl['arr']['date'] . ' ' . $tpl['arr']['start_morning']));
					?>
					<span class="inline-block">
						<input name="start_morning" value="<?php echo $start_morning;?>" class="pj-timepicker pj-form-field w80 required"/>
					</span>
				</p>
				<p class="business" style="display: <?php echo $business; ?>">
					<label class="title"><?php __('time_morning_to'); ?></label>
					<?php
					$end_morning = date($tpl['option_arr']['o_time_format'], strtotime($tpl['arr']['date'] . ' ' . $tpl['arr']['end_morning']));
					?>
					<span class="inline-block">
						<input name="end_morning" value="<?php echo $end_morning;?>" class="pj-timepicker pj-form-field w80 required"/>
					</span>
				</p>
				<p class="business" style="display: <?php echo $business; ?>">
					<label class="title"><?php __('time_afternoon_from'); ?></label>
					<?php
					$start_afternoon = date($tpl['option_arr']['o_time_format'], strtotime($tpl['arr']['date'] . ' ' . $tpl['arr']['start_afternoon']));
					?>
					<span class="inline-block">
						<input name="start_afternoon" value="<?php echo $start_afternoon;?>" class="pj-timepicker pj-form-field w80 required"/>
					</span>
				</p>
				<p class="business" style="display: <?php echo $business; ?>">
					<label class="title"><?php __('time_afternoon_to'); ?></label>
					<?php
					$end_afternoon = date($tpl['option_arr']['o_time_format'], strtotime($tpl['arr']['date'] . ' ' . $tpl['arr']['end_afternoon']));
					?>
					<span class="inline-block">
						<input name="end_afternoon" value="<?php echo $end_afternoon;?>" class="pj-timepicker pj-form-field w80 required"/>
					</span>
				</p>
			</div>
			<br class="clear_both" />
		</fieldset>
	</form>
	
	<?php
	$day_names = __('day_names', true);
	$months = __('months', true);
	ksort($day_names);
	ksort($months);
	?>
	<script type="text/javascript">
	var myLabel = myLabel || {};
	myLabel.monthNames = ["<?php echo join('","', $months); ?>"];
	myLabel.dayNamesMin = ["<?php echo join('","', $day_names); ?>"];
	myLabel.showperiod = <?php echo $show_period; ?>;
	</script>
	<?php
}
?>