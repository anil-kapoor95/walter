var jQuery_1_8_2 = jQuery_1_8_2 || $.noConflict();
(function ($, undefined) {
	$(function () {
		"use strict";
		var tabs = ($.fn.tabs !== undefined),
			$tabs = $("#tabs"),
			tOpt = {
				activate: function (event, ui) {
					$(":input[name='tab_id']").val(ui.newPanel.attr('id'));
				}
			};
		
		if ($tabs.length > 0 && tabs) {
			$tabs.tabs(tOpt);
		}
		$(".field-int").spinner({
			min: 0
		});
		
				
		$("#content").on("focusin", ".textarea_install", function (e) {
			$(this).select();
		}).on("change", "select[name='value-enum-o_send_email']", function (e) {
			switch ($("option:selected", this).val()) {
			case 'mail|smtp::mail':
				$(".boxSmtp").hide();
				break;
			case 'mail|smtp::smtp':
				$(".boxSmtp").show();
				break;
			}
		}).on("change", "select[name='value-enum-o_allow_paypal']", function (e) {
			switch ($("option:selected", this).val()) {
			case 'Yes|No::No':
				$(".boxPaypal").hide();
				break;
			case 'Yes|No::Yes':
				$(".boxPaypal").show();
				break;
			}
		}).on("change", "select[name='value-enum-o_allow_authorize']", function (e) {
			switch ($("option:selected", this).val()) {
			case 'Yes|No::No':
				$(".boxAuthorize").hide();
				break;
			case 'Yes|No::Yes':
				$(".boxAuthorize").show();
				break;
			}
		}).on("change", "select[name='value-enum-o_allow_bank']", function (e) {
			switch ($("option:selected", this).val()) {
			case 'Yes|No::No':
				$(".boxBankAccount").hide();
				break;
			case 'Yes|No::Yes':
				$(".boxBankAccount").show();
				break;
			}
		}).on("click", ".pj-use-theme", function (e) {
			var theme = $(this).attr('data-theme'),
				href = $('#pj_preview_install').attr('href');
			$('.pj-loader').css('display', 'block');
			$.ajax({
				type: "GET",
				async: false,
				url: 'index.php?controller=pjAdminOptions&action=pjActionUpdateTheme&theme=' + theme,
				success: function (data) {
					$('.theme-holder').html(data);
					$('.pj-loader').css('display', 'none');
				}
			});
		}).on("change", "#client_email_notify", function (e) {
			var value = $(this).val();
			$('.boxClient').hide();
			$('.boxClient' + value).show();
		}).on("change", "#client_sms_notify", function (e) {
			var value = $(this).val();
			$('.boxClientSms').hide();
			$('.boxClientSms' + value).show();
		}).on("change", "#admin_email_notify", function (e) {
			var value = $(this).val();
			$('.boxAdmin').hide();
			$('.boxAdmin' + value).show();
		}).on("change", "#admin_sms_notify", function (e) {
			var value = $(this).val();
			$('.boxAdminSms').hide();
			$('.boxAdminSms' + value).show();
		});
		if ($('#frmNotification').length > 0) 
		{
			var value = $('#client_email_notify').val();
			$('.boxClient' + value).show();
			
			var value = $('#client_sms_notify').val();
			$('.boxClientSms' + value).show();
			
			var value = $('#admin_email_notify').val();
			$('.boxAdmin' + value).show();
			
			var value = $('#admin_sms_notify').val();
			$('.boxAdminSms' + value).show();
			
			tinymce.init({
			    selector: "textarea.mceEditor",
			    theme: "modern",
			    width: 500,
			    plugins: [
			         "advlist autolink link image lists charmap print preview hr anchor pagebreak",
			         "searchreplace wordcount visualblocks visualchars code fullscreen insertdatetime media nonbreaking",
			         "save table contextmenu directionality emoticons template paste textcolor"
			   ],
			   toolbar: "insertfile undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | l      ink image | print preview media fullpage | forecolor backcolor emoticons"
			 });
		}
	});
})(jQuery_1_8_2);