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
	$_yesno = __('_yesno', true);
	?>
	<?php pjUtil::printNotice(__('infoAddRoomTitle', true, false), __('infoAddRoomDesc', true, false)); ?>
	
	<?php if ((int) $tpl['option_arr']['o_multi_lang'] === 1 && count($tpl['lp_arr']) > 1) : ?>
	<div class="multilang"></div>
	<?php endif; ?>
	
	<form action="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminRooms&amp;action=pjActionCreate" method="post" id="frmCreateRoom" class="form pj-form" autocomplete="off" enctype="multipart/form-data">
		<input type="hidden" name="room_create" value="1" />
		<input type="hidden" name="csrf_token" value="<?php echo pjAppController::getCsrfToken(); ?>" />
		<?php
		foreach ($tpl['lp_arr'] as $v)
		{
			?>
			<p class="pj-multilang-wrap" data-index="<?php echo $v['id']; ?>" style="display: <?php echo (int) $v['is_default'] === 0 ? 'none' : NULL; ?>">
				<label class="title"><?php __('lblTitle'); ?></label>
				<span class="inline_block">
					<input type="text" id="i18n_title_<?php echo $v['id'];?>" name="i18n[<?php echo $v['id']; ?>][title]" class="pj-form-field w300<?php echo (int) $v['is_default'] === 0 ? NULL : ' required'; ?>" lang="<?php echo $v['id']; ?>" />
					<?php if ((int) $tpl['option_arr']['o_multi_lang'] === 1 && count($tpl['lp_arr']) > 1) : ?>
					<span class="pj-multilang-input"><img src="<?php echo PJ_INSTALL_URL . PJ_FRAMEWORK_LIBS_PATH . 'pj/img/flags/' . $v['file']; ?>" alt="" /></span>
					<?php endif; ?>
				</span>
			</p>
			<?php
		}
		?>
		<p>
			<label class="title"><?php __('lblImage', false, true); ?></label>
			<span class="inline_block">
				<input type="file" name="image" id="image" class="pj-form-field w400"/>
			</span>
		</p>
		<p>
			<label class="title"><?php __('lblCapacity', false, true); ?></label>
			<span class="inline_block">
				<input type="text" name="capacity" id="capacity" class="pj-form-field field-int required digits w80" data-msg-digits="<?php __('pj_digits_validation');?>"/>
				<span class="inline_block"><?php __('lblPeople');?></span>
			</span>
		</p>
		<?php 
		foreach ($tpl['lp_arr'] as $v)
		{
		?>
			<p class="pj-multilang-wrap" data-index="<?php echo $v['id']; ?>" style="display: <?php echo (int) $v['is_default'] === 0 ? 'none' : NULL; ?>">
				<label class="title"><?php __('lblDescription'); ?></label>
				<span class="inline_block">
					<textarea id="i18n_description_<?php echo $v['id'];?>" name="i18n[<?php echo $v['id']; ?>][description]" class="pj-form-field w500 h150<?php echo (int) $v['is_default'] === 0 ? NULL : ' required'; ?>" lang="<?php echo $v['id']; ?>"></textarea>
					<?php if ((int) $tpl['option_arr']['o_multi_lang'] === 1 && count($tpl['lp_arr']) > 1) : ?>
					<span class="pj-multilang-input"><img src="<?php echo PJ_INSTALL_URL . PJ_FRAMEWORK_LIBS_PATH . 'pj/img/flags/' . $v['file']; ?>" alt="" /></span>
					<?php endif; ?>
				</span>
			</p>
			<?php
		}
		
		$book_by = __('book_by', true);
		$price_per = __('price_per', true);
		?>
		<p>
			<label class="title"><?php __('lblBookBy', false, true); ?></label>
			<span class="inline_block">
				<span class="block t5 float_left r20"><input type="checkbox" id="book_by_multiday" name="book_by_multiday" value="T" class="block float_left r5" checked="checked"/><label class="block float_left" for="book_by_multiday"><?php echo $book_by['multiday'];?></label></span>
				<span class="block t5 float_left r20"><input type="checkbox" id="book_by_halfday" name="book_by_halfday" value="T" class="block float_left r5"/><label class="block float_left" for="book_by_halfday"><?php echo $book_by['halfday'];?></label></span>
				<span class="block t5 float_left r20"><input type="checkbox" id="book_by_hour" name="book_by_hour" value="T" class="block float_left r5"/><label class="block float_left" for="book_by_hour"><?php echo $book_by['hour'];?></label></span>
				<input type="hidden" id="book_by" name="bookby" value="" class="required"/>
			</span>
		</p>
		
		<p id="multidayPrice" class="priceTitle none">
			<label class="title"><?php echo $price_per['day']; ?></label>
			<span class="pj-form-field-custom pj-form-field-custom-before">
				<span class="pj-form-field-before"><abbr class="pj-form-field-icon-text"><?php echo pjUtil::formatCurrencySign(NULL, $tpl['option_arr']['o_currency'], ""); ?></abbr></span>
				<input type="text" id="price_per_day" name="price_per_day" class="pj-form-field number w108" data-msg-number="<?php __('pj_number_validation');?>"/>
			</span>
		</p>
		<p id="halfdayPrice" class="priceTitle none">
			<label class="title"><?php echo $price_per['half']; ?></label>
			<span class="pj-form-field-custom pj-form-field-custom-before">
				<span class="pj-form-field-before"><abbr class="pj-form-field-icon-text"><?php echo pjUtil::formatCurrencySign(NULL, $tpl['option_arr']['o_currency'], ""); ?></abbr></span>
				<input type="text" id="price_half_day" name="price_half_day" class="pj-form-field number w108" data-msg-number="<?php __('pj_number_validation');?>"/>
			</span>
		</p>
		<p id="hourPrice" class="priceTitle none">
			<label class="title"><?php echo $price_per['hour']; ?></label>
			<span class="pj-form-field-custom pj-form-field-custom-before">
				<span class="pj-form-field-before"><abbr class="pj-form-field-icon-text"><?php echo pjUtil::formatCurrencySign(NULL, $tpl['option_arr']['o_currency'], ""); ?></abbr></span>
				<input type="text" id="price_per_hour" name="price_per_hour" class="pj-form-field number w108" data-msg-number="<?php __('pj_number_validation');?>"/>
			</span>
		</p>
		<p id="morningAfternoonPrice" class="priceTitle none">
			<label class="title"><?php echo $price_per['morningafternoon']; ?></label>
			<span class="pj-form-field-custom pj-form-field-custom-before">
				<span class="pj-form-field-before"><abbr class="pj-form-field-icon-text"><?php echo pjUtil::formatCurrencySign(NULL, $tpl['option_arr']['o_currency'], ""); ?></abbr></span>
				<input type="text" id="price_morning_afternoon" name="price_morning_afternoon" class="pj-form-field number w108" data-msg-number="<?php __('pj_number_validation');?>"/>
			</span>
		</p>
		<p id="afternoonEveningPrice" class="priceTitle none">
			<label class="title"><?php echo $price_per['afternoonevening']; ?></label>
			<span class="pj-form-field-custom pj-form-field-custom-before">
				<span class="pj-form-field-before"><abbr class="pj-form-field-icon-text"><?php echo pjUtil::formatCurrencySign(NULL, $tpl['option_arr']['o_currency'], ""); ?></abbr></span>
				<input type="text" id="price_afternoon_evening" name="price_afternoon_evening" class="pj-form-field number w108" data-msg-number="<?php __('pj_number_validation');?>"/>
			</span>
		</p>
		<p>
			<label class="title"><?php __('lblLayouts'); ?></label>
			<span class="inline_block">
				<?php
				if(!empty($tpl['layout_arr']))
				{ 
					?>
					<select name="layout_id[]" id="layout_id" multiple="multiple" size="5" class="pj-form-field w300">
						<?php
						foreach ($tpl['layout_arr'] as $v)
						{
							?><option value="<?php echo $v['id']; ?>"><?php echo stripslashes($v['title']); ?></option><?php
						}
						?>
					</select>
					<?php
				}else{
					$message = __('lblNoLayoutsMessage', true, false);
					$message = str_replace("{STAG}", '<a href="'.$_SERVER['PHP_SELF'].'?controller=pjAdminLayouts&amp;action=pjActionCreate">', $message);
					$message = str_replace("{ETAG}", '</a>', $message);
					?>
					<input type="hidden" name="hidden_layout_id" id="hidden_layout_id"/>
					<label class="content"><?php echo $message;?></label>
					<?php
				} 
				?>
			</span>
		</p>
		<p>
			<label class="title"><?php __('lblStatus'); ?></label>
			<span class="inline_block">
				<select name="status" id="status" class="pj-form-field required" data-msg-required="<?php __('pj_field_required');?>">
					<option value="">-- <?php __('lblChoose'); ?>--</option>
					<?php
					foreach (__('u_statarr', true) as $k => $v)
					{
						?><option value="<?php echo $k; ?>"<?php echo $k == 'T' ? ' selected="selected"' : null;?>><?php echo $v; ?></option><?php
					}
					?>
				</select>
			</span>
		</p>
		<p>
			<label class="title">&nbsp;</label>
			<span class="inline_block">
				<input type="submit" value="<?php __('btnSave'); ?>" class="pj-button" />
				<input type="button" value="<?php __('btnCancel'); ?>" class="pj-button" onclick="window.location.href='<?php echo PJ_INSTALL_URL; ?>index.php?controller=pjAdminRooms&action=pjActionIndex';" />
			</span>
		</p>
	</form>
	
	<script type="text/javascript">
	var myLabel = myLabel || {};
	myLabel.choose = "-- <?php __('lblChoose'); ?> --";
	myLabel.field_required = "<?php __('pj_field_required'); ?>";
	
	var pjLocale = pjLocale || {};
	var locale_array = new Array(); 
	pjLocale.langs = <?php echo $tpl['locale_str']; ?>;
	pjLocale.flagPath = "<?php echo PJ_FRAMEWORK_LIBS_PATH; ?>pj/img/flags/";
	<?php
	foreach ($tpl['lp_arr'] as $v)
	{
		?>locale_array.push(<?php echo $v['id'];?>);<?php
	} 
	?>
	myLabel.locale_array = locale_array;
	(function ($) {
		$(function() {
			$(".multilang").multilang({
				langs: pjLocale.langs,
				flagPath: pjLocale.flagPath,
				select: function (event, ui) {
					
				}
			});
		});
	})(jQuery_1_8_2);
	</script>
	<?php
}
?>