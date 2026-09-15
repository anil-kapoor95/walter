<?php
include_once dirname(__FILE__) . '/elements/header.php'; 

if(!empty($tpl['arr']))
{
	?>
	<div class="pjMrBBody">
		
		<ul class="list-group pjMrBRooms">
			<?php
			foreach($tpl['arr'] as $k => $v)
			{ 
				$image_url = PJ_INSTALL_URL . PJ_IMG_PATH . 'frontend/350x230.png';
				if(!empty($v['image_source']) && file_exists(PJ_INSTALL_PATH . $v['image_source']))
				{
					$image_url = PJ_INSTALL_URL . $v['image_source'];
				}
				$price_arr = array();
				if(!empty($v['price_per_hour']))
				{
					$price_arr[] = pjUtil::formatCurrencySign($v['price_per_hour'], $tpl['option_arr']['o_currency']) . ' ' . __('front_per_hour', true);
				}
				if(!empty($v['price_half_day']))
				{
					$price_arr[] = pjUtil::formatCurrencySign($v['price_half_day'], $tpl['option_arr']['o_currency']) . ' ' . __('front_half_day', true);
				}
				if($v['book_by_morningafternoon'] == 'T' && !empty($v['price_morning_afternoon']))
				{
					$price_arr[] = pjUtil::formatCurrencySign($v['price_morning_afternoon'], $tpl['option_arr']['o_currency']) . ' ' . __('front_morningafternoon', true);
				}
				if($v['book_by_afternoonevening'] == 'T' && !empty($v['price_afternoon_evening']))
				{
					$price_arr[] = pjUtil::formatCurrencySign($v['price_afternoon_evening'], $tpl['option_arr']['o_currency']) . ' ' . __('front_afternoonevening', true);
				}
				if(!empty($v['price_per_day']))
				{
					$price_arr[] = pjUtil::formatCurrencySign($v['price_per_day'], $tpl['option_arr']['o_currency']) . ' ' . __('front_per_day', true);
				}
				?>
				<li class="list-group-item pjMrBRoom">
					<div class="row">
						<aside class="col-lg-5 col-md-5 col-sm-5 col-xs-12 pjMrBRoomAside">
							<div class="pjMrBRoomImage">
								<img src="<?php echo $image_url;?>" alt="" />
							</div><!-- /.pjMrBRoomImage -->
		
							<div class="pjMrBRoomMeta">
								<dl>
									<dt><?php __('front_capacity');?>: </dt>
									<dd><?php echo $v['capacity'];?> <?php $v['capacity'] != 1 ? __('front_people') : __('front_person');?></dd>
								</dl>
		
								<dl>
									<dt><?php __('front_price');?>: </dt>
									<dd><?php echo join("<br/>", $price_arr);?></dd>
								</dl>
							</div><!-- /.pjMrBRoomMeta -->
						</aside><!-- /.col-lg-5 col-md-5 col-sm-5 col-xs-12 pjMrBRoomAside -->
		
						<div class="col-lg-7 col-md-7 col-sm-7 col-xs-12 pjMrBRoomContent">
							<p class="pjMrBRoomTitle"><?php echo pjSanitize::html($v['title']);?></p><!-- /.pjMrBRoomTitle -->
							<p><?php echo nl2br(stripslashes($v['description']));?></p>
		
							<div class="pjMrBRoomActions">
								<a href="#" class="btn btn-primary pjMrbBookRoom" data-id="<?php echo $v['id'];?>"><?php echo __('front_btn_book_this_room');?></a>
							</div><!-- /.pjMrBRoomActions -->
						</div><!-- /.col-lg-7 col-md-7 col-sm-7 col-xs-12 pjMrBRoomContent -->
					</div><!-- /.row -->
				</li><!-- /.list-group-item pjMrBRoom -->
				<?php
			} 
			?>
	
		</ul><!-- /.list-group pjMrBRooms -->
	</div><!-- /.pjMrBBody -->
	<?php
	include_once dirname(__FILE__) . '/elements/pagination.php';
}else{
	?><div class="pjMrBBody"><?php __('front_no_rooms_found');?></div><?php
} 
?>