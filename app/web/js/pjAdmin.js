var jQuery_1_8_2 = jQuery_1_8_2 || $.noConflict();
(function ($, undefined) {
	$(function () {
		"use strict";
		var $frmLoginAdmin = $("#frmLoginAdmin"),
			$frmForgotAdmin = $("#frmForgotAdmin"),
			$frmUpdateProfile = $("#frmUpdateProfile"),
			validate = ($.fn.validate !== undefined);
		
		if ($frmLoginAdmin.length > 0 && validate) {
			$frmLoginAdmin.validate({
				rules: {
					login_email: {
						required: true,
						email: true
					},
					login_password: "required"
				},
				errorPlacement: function (error, element) {
					error.insertAfter(element.parent());
				},
				onkeyup: false,
				errorClass: "err",
				wrapper: "em"
			});
		}
		
		if ($frmForgotAdmin.length > 0 && validate) {
			$frmForgotAdmin.validate({
				rules: {
					forgot_email: {
						required: true,
						email: true
					}
				},
				errorPlacement: function (error, element) {
					error.insertAfter(element.parent());
				},
				onkeyup: false,
				errorClass: "err",
				wrapper: "em"
			});
		}
		
		if ($frmUpdateProfile.length > 0 && validate) {
			$frmUpdateProfile.validate({
				rules: {
					"email": {
						required: true,
						email: true
					},
					"password": "required",
					"name": "required"
				},
				errorPlacement: function (error, element) {
					error.insertAfter(element.parent());
				},
				onkeyup: false,
				errorClass: "err",
				wrapper: "em"
			});
		}
		function updatePrintURL()
		{
			var $print_url = $('#pjMrbPrintBookings');
			var date = $('#date').val();
			var data_href = $print_url.attr('data-href');
			data_href = data_href.replace("{DATE}", date);
			$print_url.attr('href', data_href);
		}
		if($('#date').length > 0)
		{
			updatePrintURL();
		}
		$(document).on("focusin", ".datepicker", function (e) {
			var $this = $(this);
			$this.datepicker($.extend({
				firstDay: $this.attr("rel"),
				dateFormat: $this.attr("rev"),
				onClose:function(dateText){
					$.get("index.php?controller=pjAdmin&action=pjActionGetBookings&date=" + dateText).done(function (data) {
						updatePrintURL();
						$('#pjMrbBookingList').html(data);
					}).fail(function () {
						
					});
				}
			}));
		}).on("click", ".pj-form-field-icon-date", function (e) {
			var $dp = $(this).parent().siblings("input[type='text']");
			if ($dp.hasClass("hasDatepicker")) {
				$dp.datepicker("show");
			} else {
				$dp.trigger("focusin").datepicker("show");
			}
			
		})
	});
})(jQuery_1_8_2);