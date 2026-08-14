<?php
$action = $_GET['action'];
$menu_arr = array(
		'pjActionRooms' => 1,
		'pjActionBook' => 2,
		'pjActionEquipment' => 3,
		'pjActionFoodDrinks' => 4,
		'pjActionCheckout' => 5,
		'pjActionPreview' => 6
);
$menu_title_arr = array(
		1 => array('text'=> __('front_menu_step_1', true), 'load' => 'loadRooms'),
		2 => array('text'=> __('front_menu_step_2', true), 'load' => 'loadBook'),
		3 => array('text'=> __('front_menu_step_3', true), 'load' => 'loadEquipment'),
		4 => array('text'=> __('front_menu_step_4', true), 'load' => 'loadFoodDrinks'),
		5 => array('text'=> __('front_menu_step_5', true), 'load' => 'loadCheckout'),
		6 => array('text'=> __('front_menu_step_6', true), 'load' => 'loadPreview')
);
if(isset($tpl['find_menu']))
{
	if($tpl['find_menu']['hide_both'] == true)
	{
		$menu_arr = array(
				'pjActionRooms' => 1,
				'pjActionBook' => 2,
				'pjActionCheckout' => 3,
				'pjActionPreview' => 4
		);
		$menu_title_arr = array(
				1 => array('text'=> __('front_menu4_step_1', true), 'load' => 'loadRooms'),
				2 => array('text'=> __('front_menu4_step_2', true), 'load' => 'loadBook'),
				3 => array('text'=> __('front_menu4_step_3', true), 'load' => 'loadCheckout'),
				4 => array('text'=> __('front_menu4_step_4', true), 'load' => 'loadPreview')
		);
	}elseif($tpl['find_menu']['hide_both'] == false && $tpl['find_menu']['hide_equipment'] == true && $tpl['find_menu']['hide_food_drinks'] == false){
		$menu_arr = array(
				'pjActionRooms' => 1,
				'pjActionBook' => 2,
				'pjActionFoodDrinks' => 3,
				'pjActionCheckout' => 4,
				'pjActionPreview' => 5
		);
		$menu_title_arr = array(
				1 => array('text'=> __('front_menu2_step_1', true), 'load' => 'loadRooms'),
				2 => array('text'=> __('front_menu2_step_2', true), 'load' => 'loadBook'),
				3 => array('text'=> __('front_menu2_step_3', true), 'load' => 'loadFoodDrinks'),
				4 => array('text'=> __('front_menu2_step_4', true), 'load' => 'loadCheckout'),
				5 => array('text'=> __('front_menu2_step_5', true), 'load' => 'loadPreview')
		);
	}elseif($tpl['find_menu']['hide_both'] == false && $tpl['find_menu']['hide_food_drinks'] == true && $tpl['find_menu']['hide_equipment'] == false){
		$menu_arr = array(
				'pjActionRooms' => 1,
				'pjActionBook' => 2,
				'pjActionEquipment' => 3,
				'pjActionCheckout' => 4,
				'pjActionPreview' => 5
		);
		$menu_title_arr = array(
				1 => array('text'=> __('front_menu3_step_1', true), 'load' => 'loadRooms'),
				2 => array('text'=> __('front_menu3_step_2', true), 'load' => 'loadBook'),
				3 => array('text'=> __('front_menu3_step_3', true), 'load' => 'loadEquipment'),
				4 => array('text'=> __('front_menu3_step_4', true), 'load' => 'loadCheckout'),
				5 => array('text'=> __('front_menu3_step_5', true), 'load' => 'loadPreview')
		);
	}
}
$current_item = $menu_arr[$action];
?>
<header class="navbar navbar-default pjMrBHeader">
	<a href="#" data-load="loadRooms" class="btn btn-default pjMrBHome pjMrbMenuItem">
		<span class="glyphicon glyphicon-home" aria-hidden="true"></span>
	</a>

	<div class="btn-group pjMrBNav">
		<button class="btn btn-default dropdown-toggle" data-pj-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
			<?php echo $menu_title_arr[$current_item]['text'];?>
			<span class="caret"></span>
		</button>

		<ul class="dropdown-menu">
			<?php
			foreach($menu_title_arr as $k => $v)
			{
				?><li<?php echo $k <= $current_item ? ($k == $current_item ? ' class="active"' : NULL) : ' class="pjMrBDdisabled"'; ?>><a href="#" class="pjMrbMenuItem" data-load="<?php echo $v['load'];?>"><?php echo $v['text'];?></a></li><?php
			} 
			?>
		</ul><!-- /.dropdown-menu -->
	</div><!-- /.btn-group pjMrBNav -->
	<?php
	if($action == 'pjActionRooms')
	{	 
		?>
		<div class="pjMrBForm pjMrBFormFilters pull-right text-right">
			<form action="#" method="post" class="form-inline">
				<div class="form-group">
					<label for=""><?php __('front_order_by');?>: </label>
					<?php
					$order_arr = __('order_arr', true); 
					?>
					<select name="order_by" class="form-control pjMrbOrderBySelector">
						<?php
						foreach($order_arr as $k => $v)
						{
							list($column, $direction) = explode("_SORT_", $k);
							?><option value="<?php echo $k;?>"<?php echo $k == $_GET['column'] . '_SORT_' . $_GET['direction'] ? ' selected="selected"' : NULL;?> data-column="<?php echo $column?>" data-direction="<?php echo $direction?>"><?php echo $v;?></option><?php
						} 
						?>
					</select>
				</div><!-- /.form-group -->
			</form><!-- /.form-inline -->
		</div><!-- /.pjMrBForm pjMrBFormFilters text-right -->
		<?php
	} 
	?>
	
</header><!-- /.navbar navbar-default pjMrBHeader -->