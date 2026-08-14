<?php
if (isset($tpl['arr']) && !empty($tpl['arr']))
{
	?>
	<form action="" method="post" class="form pj-form">
		<input type="hidden" name="send_confirm" value="1" />
		<input type="hidden" name="csrf_token" value="<?php echo pjAppController::getCsrfToken(); ?>" />
		<input type="hidden" name="to" value="<?php echo $tpl['arr']['to']; ?>" />
		<input type="hidden" name="from" value="<?php echo $tpl['arr']['from']; ?>" />
		<p>
			<span class="bold inline_block b5"><?php __('booking_subject'); ?></span>
			<input type="text" name="subject" id="confirm_subject" class="pj-form-field w600 required" value="<?php echo pjSanitize::html($tpl['arr']['subject']); ?>" />
		</p>
		<p>
			<span class="bold inline_block b5"><?php __('booking_message'); ?></span>
			<textarea name="message" id="confirm_message" class="pj-form-field required mceEditor"><?php echo !empty($tpl['arr']['message']) ? stripslashes($tpl['arr']['message']) : ''; ?></textarea>
		</p>
	</form>
	<?php
}
?>