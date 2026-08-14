var jQuery_1_8_2 = jQuery_1_8_2 || $.noConflict();
(function ($, undefined) {
	$(function () {
		"use strict";
		var validator,
			$frmCreateBooking = $('#frmCreateBooking'),
			$frmUpdateBooking = $('#frmUpdateBooking'),
			$dialogConfirmation = $("#dialogConfirmation"),
			dialog = ($.fn.dialog !== undefined),
			datepicker = ($.fn.datepicker !== undefined),
			datagrid = ($.fn.datagrid !== undefined),
			validate = ($.fn.validate !== undefined),
			multiselect = ($.fn.multiselect !== undefined),
			chosen = ($.fn.chosen !== undefined),
			spinner = ($.fn.spinner !== undefined),
			tabs = ($.fn.tabs !== undefined),
			$tabs = $("#tabs"),
			tOpt = {
				activate: function (event, ui) {
					$(":input[name='tab_id']").val($(ui.newPanel).prop('id'));
				}
			};
	
		if ($tabs.length > 0 && tabs) {
			$tabs.tabs(tOpt);
		}
		$(".field-int").spinner({
			min: 1,
			stop:function(e,ui){
				if($(this).attr('name') == 'attendees')
				{
					calculatePrice();
				}else{
					var id = $(this).attr('data-id');
					var type = $(this).attr('data-type');
					if(type == 'equipment')
					{
						if($('#checkunit_' + id).is(':checked'))
						{
							calculatePrice();
						}
					}else{
						if($('#checkfooddrinks_' + id).is(':checked'))
						{
							calculatePrice();
						}
					}
				}
		    }
		});
		if (chosen) {
			$("#c_country").chosen();
		}
		if (multiselect) {
			$("#slots").multiselect({
				noneSelectedText: myLabel.choose,
				close: function(){
					calculatePrice();
				}
			});
		}
		
		if ($frmCreateBooking.length > 0 || $frmUpdateBooking.length > 0) 
		{
			$frmCreateBooking.validate({
				rules: {
					"validate_start_date": {
						remote: {
							url: "index.php?controller=pjAdminBookings&action=pjActionCheckDaysOff",
							data:{
								start_date: function(){
									return $frmCreateBooking.find('input[name="start_date"]').val();
								}
							}
						}
					},
					"validate_end_date": {
						remote: {
							url: "index.php?controller=pjAdminBookings&action=pjActionCheckRangeOff",
							data:{
								start_date: function(){
									return $frmCreateBooking.find('input[name="start_date"]').val();
								},
								end_date: function(){
									return $frmCreateBooking.find('input[name="end_date"]').val();
								},
								book_by: function(){
									return $frmCreateBooking.find('select[name="book_by"]').val();
								}
							}
						}
					},
					"validate_date_range": {
						remote: {
							url: "index.php?controller=pjAdminBookings&action=pjActionCheckDateRange",
							data:{
								start_date: function(){
									return $frmCreateBooking.find('input[name="start_date"]').val();
								},
								end_date: function(){
									return $frmCreateBooking.find('input[name="end_date"]').val();
								},
								book_by: function(){
									return $frmCreateBooking.find('select[name="book_by"]').val();
								},
								room_id: function(){
									return $frmCreateBooking.find('select[name="room_id"]').val();
								}
							}
						}
					},
					"cc_type":{
						required: function(){
							if($('#payment_method').val() == 'creditcard')
							{
								return true;
							}else{
								return false;
							}
						}
					},
					"cc_num":{
						required: function(){
							if($('#payment_method').val() == 'creditcard')
							{
								return true;
							}else{
								return false;
							}
						}
					},
					"cc_code":{
						required: function(){
							if($('#payment_method').val() == 'creditcard')
							{
								return true;
							}else{
								return false;
							}
						}
					}
				},
				errorPlacement: function (error, element) {
					error.insertAfter(element.parent());
				},
				errorClass: "err",
				wrapper: "em",
				ignore: "",
				invalidHandler: function (event, validator) {
				    if (validator.numberOfInvalids()) {
				    	var index = $(validator.errorList[0].element, this).closest("div[id^='tabs-']").index();
				    	if ($tabs.length > 0 && tabs && index !== -1) {
				    		$tabs.tabs(tOpt).tabs("option", "active", index-1);
				    	}
				    };
				}
			});
			$frmUpdateBooking.validate({
				rules: {
					"attendees": {
						max: myLabel.maxCapacity
					},
					"validate_start_date": {
						remote: {
							url: "index.php?controller=pjAdminBookings&action=pjActionCheckDaysOff",
							data:{
								start_date: function(){
									return $frmUpdateBooking.find('input[name="start_date"]').val();
								}
							}
						}
					},
					"validate_end_date": {
						remote: {
							url: "index.php?controller=pjAdminBookings&action=pjActionCheckRangeOff",
							data:{
								start_date: function(){
									return $frmUpdateBooking.find('input[name="start_date"]').val();
								},
								end_date: function(){
									return $frmUpdateBooking.find('input[name="end_date"]').val();
								},
								book_by: function(){
									return $frmUpdateBooking.find('select[name="book_by"]').val();
								}
							}
						}
					},
					"validate_date_range": {
						remote: {
							url: "index.php?controller=pjAdminBookings&action=pjActionCheckDateRange",
							data:{
								start_date: function(){
									return $frmUpdateBooking.find('input[name="start_date"]').val();
								},
								end_date: function(){
									return $frmUpdateBooking.find('input[name="end_date"]').val();
								},
								book_by: function(){
									return $frmUpdateBooking.find('select[name="book_by"]').val();
								},
								room_id: function(){
									return $frmUpdateBooking.find('select[name="room_id"]').val();
								},
								id: function(){
									return $frmUpdateBooking.find('input[name="id"]').val();
								}
							}
						}
					},
					"cc_type":{
						required: function(){
							if($('#payment_method').val() == 'creditcard')
							{
								return true;
							}else{
								return false;
							}
						}
					},
					"cc_num":{
						required: function(){
							if($('#payment_method').val() == 'creditcard')
							{
								return true;
							}else{
								return false;
							}
						}
					},
					"cc_code":{
						required: function(){
							if($('#payment_method').val() == 'creditcard')
							{
								return true;
							}else{
								return false;
							}
						}
					}
				},
				errorPlacement: function (error, element) {
					console.log(element.attr('name'));
					error.insertAfter(element.parent());
				},
				onkeyup: false,
				errorClass: "err",
				wrapper: "em",
				ignore: "",
				invalidHandler: function (event, validator) {
				    if (validator.numberOfInvalids()) {
				    	var index = $(validator.errorList[0].element, this).closest("div[id^='tabs-']").index();
				    	if ($tabs.length > 0 && tabs && index !== -1) {
				    		$tabs.tabs(tOpt).tabs("option", "active", index-1);
				    	}
				    };
				}
			});
			
		}
		if ($("#grid").length > 0 && datagrid) {
			var $grid = $("#grid").datagrid({
				buttons: [{type: "edit", url: "index.php?controller=pjAdminBookings&action=pjActionUpdate&id={:id}"},
				          {type: "delete", url: "index.php?controller=pjAdminBookings&action=pjActionDeleteBooking&id={:id}"}
						 ],
				columns: [
				          {text: myLabel.room, type: "text", sortable: false, width:150},
				          {text: myLabel.date, type: "text", sortable: true, width:100},
				          {text: myLabel.name, type: "text", sortable: true, editable: false, width:150},
				          {text: myLabel.total, type: "text", sortable: true, editable: false, width:80},
				          {text: myLabel.status, type: "select", sortable: true, editable: true, width: 100, options: [
				                                                                                     {label: myLabel.pending, value: "pending"}, 
				                                                                                     {label: myLabel.confirmed, value: "confirmed"},
				                                                                                     {label: myLabel.cancelled, value: "cancelled"}
				                                                                                     ], applyClass: "pj-status"}],
				dataUrl: "index.php?controller=pjAdminBookings&action=pjActionGetBooking" + pjGrid.queryString,
				dataType: "json",
				fields: ['room', 'start_date', 'c_name', 'total', 'status'],
				paginator: {
					actions: [
					   {text: myLabel.delete_selected, url: "index.php?controller=pjAdminBookings&action=pjActionDeleteBookingBulk", render: true, confirmation: myLabel.delete_confirmation},
					   {text: myLabel.exported, url: "index.php?controller=pjAdminBookings&action=pjActionExportBooking", ajax: false}
					],
					gotoPage: true,
					paginate: true,
					total: true,
					rowCount: true
				},
				saveUrl: "index.php?controller=pjAdminBookings&action=pjActionSaveBooking&id={:id}",
				select: {
					field: "id",
					name: "record[]"
				}
			});
		}
				
		$(document).on("focusin", ".datepicker", function (e) {
			var minDate, maxDate,
				$this = $(this),
				custom = {},
				o = {
					firstDay: $this.attr("rel"),
					dateFormat: $this.attr("rev"),
					onClose:function(dateText){
						var $frm = null;
					    if ($frmCreateBooking.length > 0) 
						{
					    	$frm = $frmCreateBooking;
						}
					    if ($frmUpdateBooking.length > 0) 
						{
					    	$frm = $frmUpdateBooking;
						}
					    if($frm != null)
					    {
					    	if($this.attr("name") == 'start_date')
							{
								$(".datepicker[name='end_date']").val(dateText);
								var $validate_start_date = $frm.find('input[name="validate_start_date"]');
								var $validate_end_date = $frm.find('input[name="validate_end_date"]');
								
								$validate_start_date.val(Math.ceil(Math.random() * 999999)).removeData("previousValue");
								$validate_end_date.val(Math.ceil(Math.random() * 999999)).removeData("previousValue");
								if($validate_start_date.valid() && $validate_end_date.valid())
								{
									getDuration();
									if($('#book_by').val() != 'hour')
									{
										calculatePrice();
									}
								}
							}
							if($this.attr("name") == 'end_date')
							{
								var $validate_date_range = $frm.find('input[name="validate_date_range"]');
								var $validate_end_date = $frm.find('input[name="validate_end_date"]');
								$validate_date_range.val(Math.ceil(Math.random() * 999999)).removeData("previousValue");
								$validate_end_date.val(Math.ceil(Math.random() * 999999)).removeData("previousValue");
								if($validate_date_range.valid() && $validate_end_date.valid())
								{
									calculatePrice();
								}
							}
					    }
					}
			};
			switch ($this.attr("name")) {
			case "start_date":
				if($(".datepicker[name='end_date']").val() != '' && $('#book_by').val() == 'multiday')
				{
					$(".datepicker[name='start_date']").datepicker("destroy").removeAttr("id");
				}
				if ($frmCreateBooking.length > 0) 
				{
					custom.minDate = 0;
				}
				break;
			case "end_date":
				if($(".datepicker[name='start_date']").val() != '')
				{
					minDate = $(".datepicker[name='start_date']").datepicker({
						firstDay: $this.attr("rel"),
						dateFormat: $this.attr("rev"),
					}).datepicker("getDate");
					$(".datepicker[name='start_date']").datepicker("destroy").removeAttr("id");
					if (minDate !== null) {
						custom.minDate = minDate;
					}
				}
				break;
			}
			
			$(this).datepicker($.extend(o, custom));
		}).on("click", ".pj-form-field-icon-date", function (e) {
			var $dp = $(this).parent().siblings("input[type='text']");
			if ($dp.hasClass("hasDatepicker")) {
				$dp.datepicker("show");
			} else {
				$dp.trigger("focusin").datepicker("show");
			}
			
		}).on("click", ".btn-all", function (e) {
			if (e && e.preventDefault) {
				e.preventDefault();
			}
			$(this).addClass("pj-button-active").siblings(".pj-button").removeClass("pj-button-active");
			var content = $grid.datagrid("option", "content"),
				cache = $grid.datagrid("option", "cache");
			$.extend(cache, {
				status: "",
				q: "",
				client: "",
				room_id: "",
				start_date: "",
				end_date: "",
			});
			$grid.datagrid("option", "cache", cache);
			$grid.datagrid("load", "index.php?controller=pjAdminBookings&action=pjActionGetBooking", "created", "DESC", content.page, content.rowCount);
			return false;
		}).on("click", ".btn-filter", function (e) {
			if (e && e.preventDefault) {
				e.preventDefault();
			}
			var $this = $(this),
				content = $grid.datagrid("option", "content"),
				cache = $grid.datagrid("option", "cache"),
				obj = {};
			$this.addClass("pj-button-active").siblings(".pj-button").removeClass("pj-button-active");
			obj.status = "";
			obj[$this.data("column")] = $this.data("value");
			$.extend(cache, obj);
			$grid.datagrid("option", "cache", cache);
			$grid.datagrid("load", "index.php?controller=pjAdminBookings&action=pjActionGetBooking", "created", "DESC", content.page, content.rowCount);
			return false;
		}).on("submit", ".frm-filter", function (e) {
			if (e && e.preventDefault) {
				e.preventDefault();
			}
			var $this = $(this),
				content = $grid.datagrid("option", "content"),
				cache = $grid.datagrid("option", "cache");
			$.extend(cache, {
				q: $this.find("input[name='q']").val(),
				client: "",
				room_id: "",
				start_date: "",
				end_date: "",
			});
			$grid.datagrid("option", "cache", cache);
			$grid.datagrid("load", "index.php?controller=pjAdminBookings&action=pjActionGetBooking", "created", "DESC", content.page, content.rowCount);
			return false;
		}).on("click", ".pj-button-detailed, .pj-button-detailed-arrow", function (e) {
			e.stopPropagation();
			$(".pj-form-filter-advanced").toggle();
		}).on("submit", ".frm-filter-advanced", function (e) {
			if (e && e.preventDefault) {
				e.preventDefault();
			}
			var obj = {},
				$this = $(this),
				arr = $this.serializeArray(),
				content = $grid.datagrid("option", "content"),
				cache = $grid.datagrid("option", "cache");
			for (var i = 0, iCnt = arr.length; i < iCnt; i++) {
				obj[arr[i].name] = arr[i].value;
			}
			$.extend(cache, obj);
			$grid.datagrid("option", "cache", cache);
			$grid.datagrid("load", "index.php?controller=pjAdminBookings&action=pjActionGetBooking", "created", "DESC", content.page, content.rowCount);
			return false;
		}).on("reset", ".frm-filter-advanced", function (e) {
			$(".pj-button-detailed").trigger("click");
			$("#client").val('');
			$("#room_id").val('');
			$("#status").val('');
			$("#start_date").val('');
			$("#end_date").val('');
		}).on("change", "#payment_method", function (e) {
			switch ($("option:selected", this).val()) {
				case 'creditcard':
					$(".boxCC").show();
					break;
				default:
					$(".boxCC").hide();
			}
		}).on("change", "#room_id", function (e) {
			getDuration();
			var capacity = 1;
			var attendees = parseInt($('#attendees').val(), 10);
			if($(this).val() != '')
			{
				capacity = parseInt($('option:selected', this).attr('data-capacity'), 10);
			}
			if(attendees > capacity)
			{
				$('#attendees').val(capacity);
			}
			$('#attendees').spinner('option', 'max', capacity);
			$("#attendees").rules("remove", "max");
			$("#attendees").rules( "add", {
				max: capacity
			});
		}).on("change", "#book_by", function (e) {
			checkEndDate();
			checkFromTo();
			if($(this).val() == 'multiday')
			{
				var $frm = null;
			    if ($frmCreateBooking.length > 0) 
				{
			    	$frm = $frmCreateBooking;
				}
			    if ($frmUpdateBooking.length > 0) 
				{
			    	$frm = $frmUpdateBooking;
				}
			    if($frm.find('input[name="validate_date_range"]').val(Math.ceil(Math.random() * 999999)).removeData("previousValue").valid()){
			    	calculatePrice();
			    }
			}else{
				calculatePrice();
			}
		}).on("change", ".pjMrbEquipmentCheck, .pjMrbFoodCheck", function (e) {
			calculatePrice();
		}).on("click", ".pjMrbSendConfirm", function (e) {
			if (e && e.preventDefault) {
				e.preventDefault();
			}
			$dialogConfirmation.data('id', $(this).attr('data-id')).dialog('open');
		}).on("keydown", "#attendees", function (e) {
			if (e.shiftKey == true) {
                e.preventDefault();
            }
			if ((e.keyCode >= 48 && e.keyCode <= 57) || (e.keyCode >= 96 && e.keyCode <= 105) || e.keyCode == 8 || e.keyCode == 9 || e.keyCode == 37 || e.keyCode == 39 || e.keyCode == 46 || e.keyCode == 190) {
				
            } else {
            	e.preventDefault();
            } 
			if($(this).val().indexOf('.') !== -1 && e.keyCode == 190)
			{
				e.preventDefault();
			}
		});
		
		if ($dialogConfirmation.length > 0 && dialog) {
			$dialogConfirmation.dialog({
				autoOpen: false,
				draggable: false,
				resizable: false,
				modal: true,
				width: 645,
				open: function () {
					$dialogConfirmation.html("");
					$.get("index.php?controller=pjAdminBookings&action=pjActionConfirmation", {
						"booking_id": $dialogConfirmation.data('id')
					}).done(function (data) {
						$dialogConfirmation.html(data);
						validator = $dialogConfirmation.find("form").validate({
							
						});
						$dialogConfirmation.dialog("option", "position", "center");
						attachTinyMce.call(null);
					});
				},
				buttons: (function () {
					var buttons = {};
					buttons[mrbsApp.locale.button.send] = function () {
						if (validator.form()) {
							if (window.tinymce !== undefined) 
							{
								tinymce.triggerSave()
							}
							$("#dialogConfirmation").next(".ui-dialog-buttonpane").find(".ui-button").attr("disabled", true).addClass("ui-state-disabled");
							$.post("index.php?controller=pjAdminBookings&action=pjActionConfirmation", $dialogConfirmation.find("form").serialize()).done(function (data) {
								$("#dialogConfirmation").next(".ui-dialog-buttonpane").find(".ui-button").removeAttr("disabled").removeClass("ui-state-disabled");
								$dialogConfirmation.dialog("close");
							});
						}
					};
					buttons[mrbsApp.locale.button.cancel] = function () {
						$dialogConfirmation.dialog("close");
					};
					
					return buttons;
				})()
			});
		}
		function attachTinyMce(options) {
			if (window.tinymce !== undefined) {
				tinymce.EditorManager.editors = [];
				var defaults = {
					selector: "textarea.mceEditor",
					theme: "modern",
					width: 610,
					height: 330,
					plugins: [
				         "advlist autolink link image lists charmap print preview hr anchor pagebreak",
				         "searchreplace visualblocks visualchars code fullscreen insertdatetime media nonbreaking",
				         "save table contextmenu directionality emoticons template paste textcolor"
				    ],
				    toolbar: "insertfile undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image | print preview media fullpage | forecolor backcolor emoticons"
				};
				
				var settings = $.extend({}, defaults, options);
				
				tinymce.init(settings);
			}
		}
		function getDuration()
		{
			var $frm = null
			if ($frmCreateBooking.length > 0) 
			{
				$frm = $frmCreateBooking;
			}
			if ($frmUpdateBooking.length > 0) 
			{
				$frm = $frmUpdateBooking;
			}
			var room_id = $('#room_id').val();
			$('.pj-loader').show();
			$.post("index.php?controller=pjAdminBookings&action=pjActionGetDuration", $frm.serialize()).done(function (data) {
				$('#pjMrbLayout').html(data.layout);
				$('#pjMrbDuration').html(data.duration);
				$('#pjMrbFromTo').html(data.from_to);
				$('.pj-loader').hide();
				
				if (multiselect) {
					$("#slots").multiselect({
						noneSelectedText: myLabel.choose,
						close: function(){
							calculatePrice();
						}
					});
				}
				checkEndDate();
				checkFromTo();
				calculatePrice();
			}).fail(function () {
				
			});
		}
		function calculatePrice()
		{
			var $frm = null;
			var calculate = true;
		    if ($frmCreateBooking.length > 0) 
			{
		    	$frm = $frmCreateBooking;
			}
		    if ($frmUpdateBooking.length > 0) 
			{
		    	$frm = $frmUpdateBooking;
			}
		    if($('#start_date').val() == '')
		    {
		    	calculate = false;
		    }
		    if($('#room_id').val() == '')
		    {
		    	calculate = false;
		    }
		    if($('#book_by').val() == '')
		    {
		    	calculate = false;
		    }else{
		    	if($('#book_by').val() == 'hour' && $('#slots').val() == null)
		    	{
		    		calculate = false;
		    	}
		    }
		    if(calculate == true)
		    {
		    	$('.pj-loader').show();
		    	$.post("index.php?controller=pjAdminBookings&action=pjActionGetPrices", $frm.serialize()).done(function (data) {
		    		$('.pj-loader').hide();
		    		$('#room_price').val(data.room_price.toFixed(2));
		    		$('#equipment_price').val(data.equipment_price.toFixed(2));
		    		$('#food_drink_price').val(data.food_drink_price.toFixed(2));
		    		$('#subtotal').val(data.subtotal.toFixed(2));
		    		$('#tax').val(data.tax.toFixed(2));
		    		$('#total').val(data.total.toFixed(2));
		    		$('#deposit').val(data.deposit.toFixed(2));
		    		$('#hours').val(data.hours);
				}).fail(function () {
					
				});
		    }else{
		    	$('#room_price').val('');
	    		$('#equipment_price').val('');
	    		$('#food_drink_price').val('');
	    		$('#subtotal').val('');
	    		$('#tax').val('');
	    		$('#total').val('');
	    		$('#deposit').val('');
	    		$('#hours').val('');
		    }
		}
		function checkEndDate()
		{
			if($('#room_id').val() == '')
			{
				$('#pjMrbEndDate').hide();
				$('input[name="validate_end_date"]').removeClass('required').valid();
				return;
			}else{
				if($('#book_by').val() == 'multiday')
				{
					$('#pjMrbEndDate').show();
					$('input[name="validate_end_date"]').addClass('required');
				}else{
					$('#pjMrbEndDate').hide();
					$('input[name="validate_end_date"]').removeClass('required').valid();
				}
			}
		}
		function checkFromTo()
		{
			if($('#book_by').val() == 'hour')
			{
				$('#pjMrbFromTo').show();
				$("#slots").addClass('required');
			}else{
				$('#pjMrbFromTo').hide();
				$("#slots").removeClass('required');
			}
		}
		function formatCurrency(price, currency, separator)
		{
			var format = '---';
			switch (currency)
			{
				case 'USD':
					format = "$" + separator + price.toFixed(2);
					break;
				case 'GBP':
					format = "&pound;" + separator  + price.toFixed(2);
					break;
				case 'EUR':
					format = "&euro;" + separator  + price.toFixed(2);
					break;
				case 'JPY':
					format = "&yen;" + separator  + price.toFixed(2);
					break;
				case 'AUD':
				case 'CAD':
				case 'NZD':
				case 'CHF':
				case 'HKD':
				case 'SGD':
				case 'SEK':
				case 'DKK':
				case 'PLN':
					format = price.toFixed(2) + separator  + currency;
					break;
				case 'NOK':
				case 'HUF':
				case 'CZK':
				case 'ILS':
				case 'MXN':
					format = currency + separator  + price.toFixed(2);
					break;
				default:
					format = price.toFixed(2) + separator  + currency;
					break;
			}
			return format;
		}
	});
})(jQuery_1_8_2);