<?php
if(count($tpl['booking_arr']) > 0)
{
	foreach($tpl['booking_arr'] as $v)
	{
		$book_by = '';
		if($v['book_by'] == 'multiday')
		{
			$book_by = __('dash_full_day', true);
		}else if($v['book_by'] == 'morning'){
			$book_by = __('time_morning', true);
		}else if($v['book_by'] == 'afternoon'){
			$book_by = __('time_afternoon', true);
		}else if($v['book_by'] == 'evening'){
			$book_by = __('time_evening', true);
		}else{
			if(!empty($v['min_slot']) && !empty($v['max_slot']))
			{
				$book_by = date($tpl['option_arr']['o_time_format'], strtotime($v['min_slot'])) . ' - ' . date($tpl['option_arr']['o_time_format'], strtotime($v['max_slot']));
			}
		}	
		?>
		<div class="dashboard_row">
			<label><?php echo pjSanitize::html($v['room']);?></label>
			<label><a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminBookings&amp;action=pjActionUpdate&amp;id=<?php echo $v['id'];?>"><?php echo pjSanitize::html($v['c_name']);?></a></label>
			<label><?php echo $book_by;?></label>
		</div>
		<?php
	}
}else{
	?>
	<div class="dashboard_row"><label><?php __('dash_no_bookings_found');?></label></div>
	<?php
} 
?>