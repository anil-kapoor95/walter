<?php
ob_start(); 
?>
<p>
	<label class="title"><?php __('lblDuration'); ?></label>
	<span class="inline_block">
		<select name="book_by" id="book_by" class="pj-form-field w150 required" data-msg-required="<?php __('pj_field_required');?>">
			<?php
			if($tpl['full_day_booked'] == 0)
			{ 
				?>
				<option value="">-- <?php __('lblChoose'); ?> --</option>
				<?php
			}else if($tpl['halfday_morning'] == 0 || $tpl['halfday_afternoon'] == 0 || $tpl['halfday_evening'] == 0) {
				?>
				<option value="">-- <?php __('lblChoose'); ?> --</option>
				<?php
			}else{
				?>
				<option value="">-- <?php __('lblRoomBookedAtDate'); ?> --</option>
				<?php
			}
			if($tpl['full_day_booked'] == 0)
			{
				if($tpl['arr']['book_by_hour'] == 'T')
				{
					$book_by = __('book_by', true);
					?><option value="hour"><?php echo $book_by['hour']; ?></option><?php
				}
				if($tpl['arr']['book_by_halfday'] == 'T')
				{
					if($tpl['halfday_morning'] == 0 && $tpl['hourly_morning'] == 0)
					{
						?>
						<option value="morning"><?php echo $book_by['halfday']; ?> - <?php __('time_morning');?></option>
						<?php
					}
					if($tpl['halfday_afternoon'] == 0 && $tpl['hourly_afternoon'] == 0)
					{
						?>
						<option value="afternoon"><?php echo $book_by['halfday']; ?> - <?php __('time_afternoon');?></option>
						<?php
					}
					if($tpl['halfday_evening'] == 0 && $tpl['hourly_evening'] == 0 && !empty($tpl['evening_arr']))
					{
						?>
						<option value="evening"><?php echo $book_by['halfday']; ?> - <?php __('time_evening');?></option>
						<?php
					}
				}
				if($tpl['arr']['book_by_multiday'] == 'T' && $tpl['multi_day_booked'] == 0)
				{
					?><option value="multiday"><?php echo $book_by['multiday']; ?></option><?php
				}
			}else if($tpl['halfday_morning'] == 0 || $tpl['halfday_afternoon'] == 0 || $tpl['halfday_evening'] == 0) {
				if($tpl['arr']['book_by_hour'] == 'T')
				{
					$book_by = __('book_by', true);
					?><option value="hour"><?php echo $book_by['hour']; ?></option><?php
				}
				if($tpl['arr']['book_by_halfday'] == 'T')
				{
					if($tpl['halfday_morning'] == 0 && $tpl['hourly_morning'] == 0)
					{
						?>
						<option value="morning"><?php echo $book_by['halfday']; ?> - <?php __('time_morning');?></option>
						<?php
					}
					if($tpl['halfday_afternoon'] == 0 && $tpl['hourly_afternoon'] == 0)
					{
						?>
						<option value="afternoon"><?php echo $book_by['halfday']; ?> - <?php __('time_afternoon');?></option>
						<?php
					}
					if($tpl['halfday_evening'] == 0 && $tpl['hourly_evening'] == 0 && !empty($tpl['evening_arr']))
					{
						?>
						<option value="evening"><?php echo $book_by['halfday']; ?> - <?php __('time_evening');?></option>
						<?php
					}
				}
			} 
			?>
		</select>
	</span>
</p>
<?php
$duration = ob_get_contents();
ob_end_clean();
ob_start(); 
?>
<p>
	<label class="title"><?php __('lblLayout'); ?></label>
	<span class="inline_block">
		<?php
		if(!empty($tpl['layout_arr']))
		{ 
			?>
			<select name="layout_id" id="layout_id" class="pj-form-field w150 required" data-msg-required="<?php __('pj_field_required');?>">
				<option value="">-- <?php __('lblChoose'); ?> --</option>
				<?php
				foreach($tpl['layout_arr'] as $k => $v)
				{
					?><option value="<?php echo $v['layout_id'];?>"><?php echo pjSanitize::html($v['title']); ?></option><?php
				}
				?>
			</select>
			<?php
		}else{
			?><label class="block t5"><?php __('lblNA');?></label><?php
		} 
		?>
	</span>
</p>
<?php
$layout = ob_get_contents();
ob_end_clean();
ob_start();
?>
<p>
	<label class="title"><?php __('lblFromTo'); ?></label>
	<span class="inline_block">
		<select name="slots[]" id="slots" multiple="multiple" size="5" class="pj-form-field required w100" data-msg-required="<?php __('pj_field_required');?>">
			<?php
			foreach($tpl['from_to_arr'] as $k => $v)
			{
				?><option value="<?php echo $k;?>"><?php echo $v; ?></option><?php
			}
			?>
		</select>
	</span>
</p>
<?php
$from_to = ob_get_contents();
ob_end_clean();

pjAppController::jsonResponse(compact('duration', 'layout', 'from_to'));
?>