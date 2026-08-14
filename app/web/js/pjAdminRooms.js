var jQuery_1_8_2 = jQuery_1_8_2 || $.noConflict();
(function ($, undefined) {
	$(function () {
		"use strict";
		var $frmCreateRoom = $("#frmCreateRoom"),
			$frmUpdateRoom = $("#frmUpdateRoom"),
			$frmCustomPrice = $("#frmCustomPrice"),
			$dialogDelete = $("#dialogDeleteImage"),
			datepicker = ($.fn.datepicker !== undefined),
			tabs = ($.fn.tabs !== undefined),
			dialog = ($.fn.dialog !== undefined),
			multiselect = ($.fn.multiselect !== undefined),
			validate = ($.fn.validate !== undefined),
			datagrid = ($.fn.datagrid !== undefined),
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
			min: 0
		});
		if (multiselect) {
			$("#layout_id").multiselect({noneSelectedText: myLabel.choose});
		}
		if ($frmCreateRoom.length > 0 && validate) {
			$frmCreateRoom.validate({
				errorPlacement: function (error, element) {
					if(element.attr('name') == 'capacity')
					{
						error.insertAfter(element.parent().parent());
					}else{
						error.insertAfter(element.parent());
					}
				},
				onkeyup: false,
				errorClass: "err",
				wrapper: "em",
				ignore: "",
				invalidHandler: function (event, validator) {
					var localeId = $(validator.errorList[0].element, this).attr('lang');
					if(localeId != undefined)
					{
						$(".pj-multilang-wrap").each(function( index ) {
							if($(this).attr('data-index') == localeId)
							{
								$(this).css('display','block');
							}else{
								$(this).css('display','none');
							}
						});
						$(".pj-form-langbar-item").each(function( index ) {
							if($(this).attr('data-index') == localeId)
							{
								$(this).addClass('pj-form-langbar-item-active');
							}else{
								$(this).removeClass('pj-form-langbar-item-active');
							}
						});
					}
				}
			});
		}
		if ($frmUpdateRoom.length > 0 && validate) {
			$frmUpdateRoom.validate({
				errorPlacement: function (error, element) {
					if(element.attr('name') == 'capacity')
					{
						error.insertAfter(element.parent().parent());
					}else{
						error.insertAfter(element.parent());
					}
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
					var localeId = $(validator.errorList[0].element, this).attr('lang');
					if(localeId != undefined)
					{
						$(".pj-multilang-wrap").each(function( index ) {
							if($(this).attr('data-index') == localeId)
							{
								$(this).css('display','block');
							}else{
								$(this).css('display','none');
							}
						});
						$(".pj-form-langbar-item").each(function( index ) {
							if($(this).attr('data-index') == localeId)
							{
								$(this).addClass('pj-form-langbar-item-active');
							}else{
								$(this).removeClass('pj-form-langbar-item-active');
							}
						});
					}
				}
			});
		}
		if ($frmCreateRoom.length > 0 || $frmUpdateRoom.length > 0) 
		{
			if(myLabel.locale_array.length > 0)
			{
				var locale_array = myLabel.locale_array;
				for(var i = 0; i < locale_array.length; i++)
				{
					var name = $("#i18n_title_" + locale_array[i]),
						description = $("#i18n_description_" + locale_array[i]);
					name.rules('add', {
						messages: {
					    	required: myLabel.field_required
					    }
					});
					description.rules('add', {
						messages: {
					    	required: myLabel.field_required
					    }
					});
				}
			}
			priceSelection();
		}
		if ($dialogDelete.length > 0 && dialog) 
		{
			$dialogDelete.dialog({
				modal: true,
				autoOpen: false,
				resizable: false,
				draggable: false,
				width: 320,
				buttons: (function () {
					var buttons = {};
					buttons[mrbsApp.locale.button.delete] = function () {
						$.ajax({
							type: "GET",
							dataType: "json",
							url: $dialogDelete.data('href'),
							success: function (res) {
								if(res.code == 200){
									$('#image_container').remove();
									$dialogDelete.dialog('close');
								}
							}
						});
					};
					buttons[mrbsApp.locale.button.cancel] = function () {
						$dialogDelete.dialog("close");
					};
					
					return buttons;
				})()
			});
		}
		function priceSelection()
		{
			var book_by_valid = false;
			$('.priceTitle').hide();
			if($('#book_by_multiday').is(':checked'))
			{
				$('#multidayPrice').show();
				$('#price_per_day').addClass('required');
				book_by_valid = true;
			}else{
				$('#multidayPrice').hide();
				$('#price_per_day').removeClass('required');
			}
			if($('#book_by_halfday').is(':checked'))
			{
				$('#halfdayPrice').show();
				$('#price_half_day').addClass('required');
				book_by_valid = true;
			}else{
				$('#halfdayPrice').hide();
				$('#price_half_day').removeClass('required');
			}
			if($('#book_by_hour').is(':checked'))
			{
				$('#hourPrice').show();
				$('#price_per_hour').addClass('required');
				book_by_valid = true;
			}else{
				$('#hourPrice').hide();
				$('#price_per_hour').removeClass('required');
			}
			
			if(book_by_valid == true)
			{
				$('#book_by').val(1).valid();
			}else{
				$('#book_by').val("");
			}
		}
		function formatImage(val, obj) {
			var src = val ? val : 'app/web/img/backend/225x150.png';
			return ['<a href="index.php?controller=pjAdminRooms&action=pjActionUpdate&id=', obj.id ,'"><img src="', src, '" style="width: 84px" /></a>'].join("");
		}
		function formatBookings(val, obj) {
			if(parseInt(val,10) > 0)
			{
				return '<a href="index.php?controller=pjAdminBookings&action=pjActionIndex&room_id='+obj.id+'">'+val+'</a>';
			}else{
				return 0;
			}
		}
		if ($("#grid").length > 0 && datagrid) {
			var $grid = $("#grid").datagrid({
				buttons: [{type: "edit", url: "index.php?controller=pjAdminRooms&action=pjActionUpdate&id={:id}"},
				          {type: "delete", url: "index.php?controller=pjAdminRooms&action=pjActionDeleteRoom&id={:id}"}
				          ],
				columns: [{text: myLabel.image, type: "text", sortable: false, editable: false, renderer: formatImage, width: 90, align: "center"},
				          {text: myLabel.room, type: "text", sortable: true, editable: true, width: 220, editableWidth: 150},
				          {text: myLabel.capacity, type: "text", sortable: true, editable: false, width: 90, align: 'center'},
				          {text: myLabel.bookings, type: "text", sortable: true, editable: false, width: 80, align: 'center', renderer: formatBookings},
				          {text: myLabel.status, type: "select", sortable: true, editable: true, width: 100, editableWidth: 80, options: [
				                                                                                     {label: myLabel.active, value: "T"}, 
				                                                                                     {label: myLabel.inactive, value: "F"}
			                                                                                     ], applyClass: "pj-status"}],
				dataUrl: "index.php?controller=pjAdminRooms&action=pjActionGetRoom" + pjGrid.queryString,
				dataType: "json",
				fields: ['image_thumb', 'title', 'capacity', 'cnt_bookings', 'status'],
				paginator: {
					actions: [
				          {text: myLabel.delete_selected, url: "index.php?controller=pjAdminRooms&action=pjActionDeleteRoomBulk", render: true, confirmation: myLabel.delete_confirmation}
					],
					gotoPage: true,
					paginate: true,
					total: true,
					rowCount: true
				},
				saveUrl: "index.php?controller=pjAdminRooms&action=pjActionSaveRoom&id={:id}",
				select: {
					field: "id",
					name: "record[]"
				}
			});
		}
		
		$(document).on("click", ".btn-all", function (e) {
			if (e && e.preventDefault) {
				e.preventDefault();
			}
			$(this).addClass("pj-button-active").siblings(".pj-button").removeClass("pj-button-active");
			var content = $grid.datagrid("option", "content"),
				cache = $grid.datagrid("option", "cache");
			$.extend(cache, {
				status: "",
				q: ""
			});
			$grid.datagrid("option", "cache", cache);
			$grid.datagrid("load", "index.php?controller=pjAdminRooms&action=pjActionGetRoom", "id", "ASC", content.page, content.rowCount);
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
			$grid.datagrid("load", "index.php?controller=pjAdminRooms&action=pjActionGetRoom", "id", "ASC", content.page, content.rowCount);
			return false;
		}).on("submit", ".frm-filter", function (e) {
			if (e && e.preventDefault) {
				e.preventDefault();
			}
			var $this = $(this),
				content = $grid.datagrid("option", "content"),
				cache = $grid.datagrid("option", "cache");
			$.extend(cache, {
				q: $this.find("input[name='q']").val()
			});
			$grid.datagrid("option", "cache", cache);
			$grid.datagrid("load", "index.php?controller=pjAdminRooms&action=pjActionGetRoom", "id", "ASC", content.page, content.rowCount);
			return false;
		}).on("click", ".pj-delete-image", function (e) {
			if (e && e.preventDefault) {
				e.preventDefault();
			}
			$dialogDelete.data('href', $(this).data('href')).dialog("open");
		}).on("focusin", ".datepicker", function (e) {
			var $this = $(this);
			$this.datepicker({
				firstDay: $this.attr("rel"),
				dateFormat: $this.attr("rev")
			});
		}).on("click", ".pj-form-field-icon-date", function (e) {
			var $dp = $(this).parent().siblings("input[type='text']");
			if ($dp.hasClass("hasDatepicker")) {
				$dp.datepicker("show");
			} else {
				$dp.trigger("focusin").datepicker("show");
			}
			
		}).on("change", '#book_by_multiday, #book_by_halfday, #book_by_hour', function (e) {
			priceSelection();
		});
	});
})(jQuery_1_8_2);