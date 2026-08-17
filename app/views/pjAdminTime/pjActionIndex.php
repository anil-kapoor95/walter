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
	
	pjUtil::printNotice(__('infoDefaultTimeTitle', true), __('infoDefaultTimeDesc', true));
	
	?>
	<form action="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminTime&amp;action=pjActionIndex" method="post" class="form">
		<input type="hidden" name="working_time" value="1" />
		<input type="hidden" name="csrf_token" value="<?php echo pjAppController::getCsrfToken(); ?>" />
		<?php
		if ($controller->isAdmin())
		{
			?><input type="hidden" name="id" value="<?php echo (int) $tpl['wt_arr']['id']; ?>" /><?php
		}
		if (isset($_GET['foreign_id']) && (int) $_GET['foreign_id'] > 0)
		{
			?><input type="hidden" name="foreign_id" value="<?php echo (int) $tpl['wt_arr']['foreign_id']; ?>" /><?php
		}
		?>
		<div class="pj-table-responsive">
		<table class="pj-table pj-worktime-table" cellpadding="0" cellspacing="0" style="width: 100%;">
			<thead>
				<tr>
					<th><?php __('time_day'); ?></th>
					<th><?php __('time_is'); ?></th>
					<th><?php __('time_from'); ?></th>
					<th><?php __('time_to'); ?></th>
					<th><?php __('time_morning_from'); ?></th>
					<th><?php __('time_morning_to'); ?></th>
					<th><?php __('time_afternoon_from'); ?></th>
					<th><?php __('time_afternoon_to'); ?></th>
					<th><?php __('time_evening_from'); ?></th>
					<th><?php __('time_evening_to'); ?></th>
				</tr>
			</thead>
			<tbody>
			<?php
			$i = 1;
			$days = __('days', true);
			$w_days = array(
				'monday' => $days[1],
				'tuesday' => $days[2],
				'wednesday' => $days[3],
				'thursday' => $days[4],
				'friday' => $days[5],
				'saturday' => $days[6],
				'sunday' => $days[0]
			);
			$show_period = 'false';
			if((strpos($tpl['option_arr']['o_time_format'], 'a') > -1 || strpos($tpl['option_arr']['o_time_format'], 'A') > -1))
			{
				$show_period = 'true';
			}
			
			foreach ($w_days as $k => $day)
			{
				$from = null;
				$to = null;
				$morning_from = null;
				$morning_to = null;
				if (isset($tpl['wt_arr']) && !empty($tpl['wt_arr']))
				{
					$tpl['wt_arr'][$k.'_from'] = empty($tpl['wt_arr'][$k.'_from']) ? '09:00:00' : $tpl['wt_arr'][$k.'_from'];
					$tpl['wt_arr'][$k.'_to'] = empty($tpl['wt_arr'][$k.'_to']) ? '18:00:00' : $tpl['wt_arr'][$k.'_to'];
					$tpl['wt_arr'][$k.'_morning_from'] = empty($tpl['wt_arr'][$k.'_morning_from']) ? '12:30:00' : $tpl['wt_arr'][$k.'_morning_from'];
					$tpl['wt_arr'][$k.'_morning_to'] = empty($tpl['wt_arr'][$k.'_morning_to']) ? '13:30:00' : $tpl['wt_arr'][$k.'_morning_to'];
					$tpl['wt_arr'][$k.'_afternoon_from'] = empty($tpl['wt_arr'][$k.'_afternoon_from']) ? '12:30:00' : $tpl['wt_arr'][$k.'_afternoon_from'];
					$tpl['wt_arr'][$k.'_afternoon_to'] = empty($tpl['wt_arr'][$k.'_afternoon_to']) ? '13:30:00' : $tpl['wt_arr'][$k.'_afternoon_to'];
					$tpl['wt_arr'][$k.'_evening_from'] = empty($tpl['wt_arr'][$k.'_evening_from']) ? '19:00:00' : $tpl['wt_arr'][$k.'_evening_from'];
					$tpl['wt_arr'][$k.'_evening_to'] = empty($tpl['wt_arr'][$k.'_evening_to']) ? '23:00:00' : $tpl['wt_arr'][$k.'_evening_to'];
					
					$from = date('H:i', strtotime($tpl['wt_arr'][$k.'_from']));
					$to = date('H:i', strtotime($tpl['wt_arr'][$k.'_to']));
					$morning_from = date('H:i', strtotime($tpl['wt_arr'][$k.'_morning_from']));
					$morning_to = date('H:i', strtotime($tpl['wt_arr'][$k.'_morning_to']));
					$afternoon_from = date('H:i', strtotime($tpl['wt_arr'][$k.'_afternoon_from']));
					$afternoon_to = date('H:i', strtotime($tpl['wt_arr'][$k.'_afternoon_to']));
					$evening_from = date('H:i', strtotime($tpl['wt_arr'][$k.'_evening_from']));
					$evening_to = date('H:i', strtotime($tpl['wt_arr'][$k.'_evening_to']));
					
					if($show_period == 'true')
					{
						if(strpos($tpl['option_arr']['o_time_format'], 'A') > -1)
						{
							$from = date('h:i A', strtotime($tpl['wt_arr'][$k.'_from']));
							$to = date('h:i A', strtotime($tpl['wt_arr'][$k.'_to']));
							$morning_from = date('h:i A', strtotime($tpl['wt_arr'][$k.'_morning_from']));
							$morning_to = date('h:i A', strtotime($tpl['wt_arr'][$k.'_morning_to']));
							$afternoon_from = date('h:i A', strtotime($tpl['wt_arr'][$k.'_afternoon_from']));
							$afternoon_to = date('h:i A', strtotime($tpl['wt_arr'][$k.'_afternoon_to']));
							$evening_from = date('h:i A', strtotime($tpl['wt_arr'][$k.'_evening_from']));
							$evening_to = date('h:i A', strtotime($tpl['wt_arr'][$k.'_evening_to']));
						}else{
							$from = date('h:i a', strtotime($tpl['wt_arr'][$k.'_from']));
							$to = date('h:i a', strtotime($tpl['wt_arr'][$k.'_to']));
							$morning_from = date('h:i a', strtotime($tpl['wt_arr'][$k.'_morning_from']));
							$morning_to = date('h:i a', strtotime($tpl['wt_arr'][$k.'_morning_to']));
							$afternoon_from = date('h:i a', strtotime($tpl['wt_arr'][$k.'_afternoon_from']));
							$afternoon_to = date('h:i a', strtotime($tpl['wt_arr'][$k.'_afternoon_to']));
							$evening_from = date('h:i a', strtotime($tpl['wt_arr'][$k.'_evening_from']));
							$evening_to = date('h:i a', strtotime($tpl['wt_arr'][$k.'_evening_to']));
						}
					}
					
					$checked = NULL;
					$dayoff_class = NULL;
						
					if ($tpl['wt_arr'][$k.'_dayoff'] == 'T')
					{
						$checked = ' checked="checked"';
						$dayoff_class = ' tsDayOff';
					}
				} else {
					$from = NULL;
					$to = NULL;
					$morning_from = NULL;
					$morning_to = NULL;
					$afternoon_from = NULL;
					$afternoon_to = NULL;
					$evening_from = NULL;
					$evening_to = NULL;
					$checked = NULL;
				}
				?>
				<tr class="<?php echo ($i % 2 !== 0 ? 'odd' : 'even'); ?>" data-day="<?php echo $k;?>">
					<td><?php echo $day; ?></td>
					<td class="align_center"><input type="checkbox" class="working_day" name="<?php echo $k; ?>_dayoff" value="T"<?php echo $checked; ?> /></td>
					<td>
						<p class="pj-worktime-cell tsWorkingDay_<?php echo $k;?><?php echo $dayoff_class;?>">
							<span class="inline-block">
								<input name="<?php echo $k?>_from" value="<?php echo $from;?>" class="pj-timepicker pj-form-field pj-worktime-input"/>
							</span>
						</p>
					</td>
					<td>
						<p class="pj-worktime-cell tsWorkingDay_<?php echo $k;?><?php echo $dayoff_class;?>">
							<span class="inline-block">
								<input name="<?php echo $k?>_to" value="<?php echo $to;?>" class="pj-timepicker pj-form-field pj-worktime-input"/>
							</span>
						</p>
					</td>
					<td>
						<p class="pj-worktime-cell tsWorkingDay_<?php echo $k;?><?php echo $dayoff_class;?>">
							<span class="inline-block">
								<input name="<?php echo $k?>_morning_from" value="<?php echo $morning_from;?>" class="pj-timepicker pj-form-field pj-worktime-input"/>
							</span>
						</p>
					</td>
					<td>
						<p class="pj-worktime-cell tsWorkingDay_<?php echo $k;?><?php echo $dayoff_class;?>">
							<span class="inline-block">
								<input name="<?php echo $k?>_morning_to" value="<?php echo $morning_to;?>" class="pj-timepicker pj-form-field pj-worktime-input"/>
							</span>
						</p>
					</td>
					<td>
						<p class="pj-worktime-cell tsWorkingDay_<?php echo $k;?><?php echo $dayoff_class;?>">
							<span class="inline-block">
								<input name="<?php echo $k?>_afternoon_from" value="<?php echo $afternoon_from;?>" class="pj-timepicker pj-form-field pj-worktime-input"/>
							</span>
						</p>
					</td>
					<td>
						<p class="pj-worktime-cell tsWorkingDay_<?php echo $k;?><?php echo $dayoff_class;?>">
							<span class="inline-block">
								<input name="<?php echo $k?>_afternoon_to" value="<?php echo $afternoon_to;?>" class="pj-timepicker pj-form-field pj-worktime-input"/>
							</span>
						</p>
					</td>
					<td>
						<p class="pj-worktime-cell tsWorkingDay_<?php echo $k;?><?php echo $dayoff_class;?>">
							<span class="inline-block">
								<input name="<?php echo $k?>_evening_from" value="<?php echo $evening_from;?>" class="pj-timepicker pj-form-field pj-worktime-input"/>
							</span>
						</p>
					</td>
					<td>
						<p class="pj-worktime-cell tsWorkingDay_<?php echo $k;?><?php echo $dayoff_class;?>">
							<span class="inline-block">
								<input name="<?php echo $k?>_evening_to" value="<?php echo $evening_to;?>" class="pj-timepicker pj-form-field pj-worktime-input"/>
							</span>
						</p>
					</td>
				</tr>
				<?php
				$i++;
			}
			?>
			</tbody>
			<tfoot>
				<tr>
					<td colspan="10"><input type="submit" value="<?php __('btnSave', false, true); ?>" class="pj-button" /></td>
				</tr>
			</tfoot>
		</table>
		</div><!-- /.pj-table-responsive -->
	</form>
	<script type="text/javascript">
	var myLabel = myLabel || {};
	myLabel.showperiod = <?php echo $show_period; ?>;
	</script>
	<?php
}
?>