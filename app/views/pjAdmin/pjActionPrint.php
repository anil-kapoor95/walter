<div style="margin: 0 auto; width: 100%;">
	<table class="table" cellspacing="2" cellpadding="5" style="width: 100%">
		<thead>
			<tr>
				<th><?php __('lblDate')?></th>
				<th><?php __('lblDuration')?></th>
				<th><?php __('lblAttendees')?></th>
				<th><?php __('lblRoom')?></th>
				<th><?php __('lblLayout')?></th>
				<th><?php __('lblEquipment')?></th>
				<th><?php __('lblFoodDrinks')?></th>
				<th><?php __('lblResvName')?></th>
				<th><?php __('lblResvEmail')?></th>
				<th><?php __('lblResvPhone')?></th>
			</tr>
		</thead>
		<tbody>
			<?php
			if(!empty($tpl['booking_arr'])) 
			{
				$name_titles = __('personal_titles', true, false);
				foreach($tpl['booking_arr'] as $k => $v)
				{
					?>
					<tr>
						<td style="vertical-align: top;"><?php echo date($tpl['option_arr']['o_date_format'], strtotime($v['start_date']));?></td>
						<td style="vertical-align: top;">
							<?php
							if($v['book_by'] == 'multiday')
							{
								echo date($tpl['option_arr']['o_date_format'], strtotime($v['end_date']));
							}else if($v['book_by'] == 'morning' || $v['book_by'] == 'afternoon' || $v['book_by'] == 'evening'){
								if($v['book_by'] == 'morning')
								{
									__('time_morning');
								}else if($v['book_by'] == 'afternoon'){
									__('time_afternoon');
								}else{
									__('time_evening');
								}
							}else{
								if(isset($tpl['booking_slot_arr'][$v['id']]) && count($tpl['booking_slot_arr'][$v['id']]) > 0)
								{
									echo join("<br/>", $tpl['booking_slot_arr'][$v['id']]);
								}else{
									echo '&nbsp;';
								}
							} 
							?>
						</td>
						<td style="vertical-align: top;"><?php echo pjSanitize::html($v['attendees']);?></td>
						<td style="vertical-align: top;"><?php echo pjSanitize::html($v['room']);?></td>
						<td style="vertical-align: top;"><?php echo pjSanitize::html($v['layout']);?></td>
						<td style="vertical-align: top;">
							<?php
							if(isset($tpl['equipment_arr'][$v['id']]) && count($tpl['equipment_arr'][$v['id']]) > 0)
							{
								echo join("<br/>", $tpl['equipment_arr'][$v['id']]);
							}else{
								echo '&nbsp;';
							} 
							?>
						</td>
						<td style="vertical-align: top;">
							<?php
							if(isset($tpl['food_drink_arr'][$v['id']]) && count($tpl['food_drink_arr'][$v['id']]) > 0)
							{
								echo join("<br/>", $tpl['food_drink_arr'][$v['id']]);
							}else{
								echo '&nbsp;';
							} 
							?>
						</td>
						<td style="vertical-align: top;"><?php echo !empty($v['c_title']) ? $name_titles[$v['c_title']] : '&nbsp;'; ?> <?php echo pjSanitize::html($v['c_name']);?></td>
						<td style="vertical-align: top;"><?php echo !empty($v['c_email']) ? pjSanitize::html($v['c_email']) : '&nbsp;'; ?></td>
						<td style="vertical-align: top;"><?php echo !empty($v['c_phone']) ? pjSanitize::html($v['c_phone']) : '&nbsp;'; ?></td>
					</tr>
					<?php
				}
			}else{
				?><tr><td colspan="10"><?php __('dash_no_bookings_found');?></td></tr><?php
			}
			?>
		</tbody>
	</table>
</div>