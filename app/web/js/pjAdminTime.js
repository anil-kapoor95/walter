var jQuery_1_8_2 = jQuery_1_8_2 || $.noConflict();
(function ($, undefined) {
	$(function () {
		"use strict";
		var datepicker = ($.fn.datepicker !== undefined),
			validate = ($.fn.validate !== undefined),
			datagrid = ($.fn.datagrid !== undefined),
			qs = "",
			$frmTimeCustom = $("#frmTimeCustom");
		
		if ($frmTimeCustom.length > 0 && validate) {
			$frmTimeCustom.validate({
				errorPlacement: function (error, element) {
					error.insertAfter(element.parent());
				},
				onkeyup: false,
				errorClass: "err",
				wrapper: "em"
			});
		}
		
		$('.pj-timepicker ').timepicker({
			showPeriod: myLabel.showperiod,
			defaultTime: ''
		});
		
		$("#content").on("click", ".working_day", function () {
			var checked = $(this).is(":checked"),
				$tr = $(this).closest("tr");
			$tr.find("select").attr("disabled", checked);
		}).on("focusin", ".datepick", function () {
			if (datepicker) {
				var $this = $(this);
				$this.datepicker({
					monthNames: myLabel.monthNames,
					dayNamesMin: myLabel.dayNamesMin,
					firstDay: $this.attr("rel"),
					dateFormat: $this.attr("rev")
				});
			}
		}).on("click", ".working_day", function () {
			var $this = $(this),
				$tr = $this.closest("tr"),
				day = $tr.attr('data-day');
			if ($this.is(":checked")) {
				$('.tsWorkingDay_' + day).hide();
			} else {
				$('.tsWorkingDay_' + day).show();
			}
		}).on("change", "input[name='is_dayoff']", function () {
			var $this = $(this),
				$form = $this.closest("form");
			if ($this.is(":checked")) {
				$form.find(".business").hide();
			} else {
				$form.find(".business").show();
			}
		});
		
		if ($("#grid").length > 0 && datagrid) {
			
			var m = window.location.href.match(/&type=(employee|calendar)&foreign_id=(\d+)/);
			if (m !== null) {
				qs = m[0];
			}
			
			var $grid = $("#grid").datagrid({
				buttons: [{type: "edit", url: "index.php?controller=pjAdminTime&action=pjActionUpdateCustom"+qs+"&id={:id}"},
				          {type: "delete", url: "index.php?controller=pjAdminTime&action=pjActionDeleteDate&id={:id}"}
				          ],
				columns: [{text: myLabel.time_date, type: "text", sortable: true, editable: false, width: 100},
				          {text: myLabel.time_start, type: "text", sortable: true, editable: false, width: 90},
				          {text: myLabel.time_end, type: "text", sortable: true, editable: false, width: 90},
				          {text: myLabel.time_morning, type: "text", sortable: false, editable: false, width: 100},
				          {text: myLabel.time_afternoon, type: "text", sortable: false, editable: false, width: 100},
				          {text: myLabel.time_evening, type: "text", sortable: false, editable: false, width: 100},
				          {text: myLabel.time_dayoff, type: "select", sortable: true, editable: true, options: [
			     				       {label: myLabel.time_yesno.T, value: 'T'}, 
			     				       {label: myLabel.time_yesno.F, value: 'F'}
			     				       ], applyClass: "pj-status"}],
				dataUrl: "index.php?controller=pjAdminTime&action=pjActionGetDate" + qs,
				dataType: "json",
				fields: ['date', 'start_time', 'end_time', 'morning', 'afternoon', 'evening', 'is_dayoff'],
				paginator: {
					actions: [
					   {text: myLabel.delete_selected, url: "index.php?controller=pjAdminTime&action=pjActionDeleteDateBulk", render: true, confirmation: myLabel.delete_confirmation}
					],
					gotoPage: true,
					paginate: true,
					total: true,
					rowCount: true
				},
				saveUrl: "index.php?controller=pjAdminTime&action=pjActionSaveDate&id={:id}",
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
				"is_dayoff": ""
			});
			$grid.datagrid("option", "cache", cache);
			$grid.datagrid("load", "index.php?controller=pjAdminTime&action=pjActionGetDate" + qs, "date", "ASC", content.page, content.rowCount);
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
			obj.is_dayoff = "";
			obj[$this.data("column")] = $this.data("value");
			$.extend(cache, obj);
			$grid.datagrid("option", "cache", cache);
			$grid.datagrid("load", "index.php?controller=pjAdminTime&action=pjActionGetDate" + qs, "date", "ASC", content.page, content.rowCount);
			return false;
		});
	});
})(jQuery_1_8_2);