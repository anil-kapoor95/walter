(function (window, undefined){
	"use strict";
	pjQ.$.ajaxSetup({
		xhrFields: {
			withCredentials: true
		}
	});
	var document = window.document,
		validate = (pjQ.$.fn.validate !== undefined),
		routes = [
		          	{pattern: /^#!\/loadRooms$/, eventName: "loadRooms"},
		          	{pattern: /^#!\/loadBook$/, eventName: "loadBook"},
		          	{pattern: /^#!\/loadEquipment$/, eventName: "loadEquipment"},
		          	{pattern: /^#!\/loadFoodDrinks$/, eventName: "loadFoodDrinks"},
		          	{pattern: /^#!\/loadCheckout$/, eventName: "loadCheckout"},
		          	{pattern: /^#!\/loadPreview$/, eventName: "loadPreview"}
		         ];
	
	function log() {
		if (window.console && window.console.log) {
			for (var x in arguments) {
				if (arguments.hasOwnProperty(x)) {
					window.console.log(arguments[x]);
				}
			}
		}
	}
	
	function assert() {
		if (window && window.console && window.console.assert) {
			window.console.assert.apply(window.console, arguments);
		}
	}
	
	function hashBang(value) {
		if (value !== undefined && value.match(/^#!\//) !== null) {
			if (window.location.hash == value) {
				return false;
			}
			window.location.hash = value;
			return true;
		}
		
		return false;
	}
	
	function onHashChange() {
		var i, iCnt, m;
		for (i = 0, iCnt = routes.length; i < iCnt; i++) {
			m = window.location.hash.match(routes[i].pattern);
			if (m !== null) {
				pjQ.$(window).trigger(routes[i].eventName, m.slice(1));
				break;
			}
		}
		if (m === null) {
			pjQ.$(window).trigger("loadRooms");
		}
	}
	pjQ.$(window).on("hashchange", function (e) {
    	onHashChange.call(null);
    });
	
	function MeetingRoomBooking(opts) {
		if (!(this instanceof MeetingRoomBooking)) {
			return new MeetingRoomBooking(opts);
		}
				
		this.reset.call(this);
		this.init.call(this, opts);
		
		return this;
	}
	
	MeetingRoomBooking.inObject = function (val, obj) {
		var key;
		for (key in obj) {
			if (obj.hasOwnProperty(key)) {
				if (obj[key] == val) {
					return true;
				}
			}
		}
		return false;
	};
	
	MeetingRoomBooking.size = function(obj) {
		var key,
			size = 0;
		for (key in obj) {
			if (obj.hasOwnProperty(key)) {
				size += 1;
			}
		}
		return size;
	};
	
	MeetingRoomBooking.prototype = {
		reset: function () {
			this.$container = null;			
			this.container = null;
			this.opts = {};
			this.column = 'capacity';
			this.direction = 'ASC';
			this.page = 1;
			return this;
		},
		
		disableButtons: function () {
			this.$container.find(".btn").each(function (i, el) {
				pjQ.$(el).attr("disabled", "disabled");
			});
		},
		enableButtons: function () {
			this.$container.find(".btn").removeAttr("disabled");
		},
		
		init: function (opts) {
			var self = this;
			this.opts = opts;
			this.container = document.getElementById("pjMrbContainer_" + self.opts.index);
						
			self.$container = pjQ.$(self.container);
			
			this.$container.on("click.mrb", ".pjMrbMenuItem", function (e) {
				if (e && e.preventDefault) {
					e.preventDefault();
				}
				if(!pjQ.$(this).parent().hasClass('disabled'))
				{
					var load = pjQ.$(this).attr('data-load');
					if (!hashBang("#!/" + load)) 
					{
						pjQ.$(window).trigger(load);
					}
				}
				return false;
			}).on("change.mrb", ".pjMrbOrderBySelector", function (e) {
				if (e && e.preventDefault) {
					e.preventDefault();
				}
				self.column = pjQ.$('option:selected', this).attr('data-column');
				self.direction = pjQ.$('option:selected', this).attr('data-direction');
				self.page = 1;
				self.loadRooms.call(self);
				return false;
			}).on("click.mrb", ".pjMrbPaging", function (e) {
				if (e && e.preventDefault) {
					e.preventDefault();
				}
				self.page = pjQ.$(this).attr('data-page');
				self.loadRooms.call(self);
				return false;
			}).on("click.mrb", ".pjMrbBookRoom", function (e) {
				if (e && e.preventDefault) {
					e.preventDefault();
				}
				var params = {};
				if(self.opts.session_id != '')
				{
					params.session_id = self.opts.session_id;
				}
				params.room_id = pjQ.$(this).attr('data-id');
				self.disableButtons();
				pjQ.$.get([self.opts.folder, "index.php?controller=pjFront&action=pjActionSetRoom"].join(""), params).done(function (data) {
					if(data.status == 'OK')
					{
						if (!hashBang("#!/loadBook")) 
						{
							self.loadBook.call(self);
						}
					}else{
						self.enableButtons();
					}
				}).fail(function () {
					
				});
				return false;
			}).on("change.mrb", "#pjMrbBookBy_" + self.opts.index, function (e) {
				if (e && e.preventDefault) {
					e.preventDefault();
				}
				self.changeBookBy.call(self,'change');
				
				return false;
			}).on("change.mrb", "select[name='payment_method']", function () {
				self.$container.find(".pjMrbCcWrap").hide();
				self.$container.find(".pjMrbBankWrap").hide();
				switch (pjQ.$("option:selected", this).val()) {
				case 'creditcard':
					self.$container.find(".pjMrbCcWrap").show();
					break;
				case 'bank':
					self.$container.find(".pjMrbBankWrap").show();
					break;
				}
			}).on("click.mrb", ".pjMrbBtnStartOver", function (e) {
				if (e && e.preventDefault) {
					e.preventDefault();
				}
				if (!hashBang("#!/loadRooms")) 
				{
					self.loadRooms.call(self);
				}
				return false;
			}).on("keydown.mrb", ".pjMrbPeople, .pjMrbUnits", function (e) {
				if (e.shiftKey == true) {
	                e.preventDefault();
	            }
				if ((e.keyCode >= 48 && e.keyCode <= 57) || (e.keyCode >= 96 && e.keyCode <= 105) || e.keyCode == 8 || e.keyCode == 9 || e.keyCode == 37 || e.keyCode == 39 || e.keyCode == 46) {
					
	            } else {
	            	e.preventDefault();
	            } 
			}).on("focusin.mrb", ".pjMrbPeople, .pjMrbUnits", function (e) {
				this.selectionStart = this.selectionEnd = this.value.length;
			}).on("click.mrb", "#pjMrCaptchaImage_" + self.opts.index, function (e) {
				if (e && e.preventDefault) {
					e.preventDefault();
				}
				pjQ.$('#pjMrCaptchaField_' + self.opts.index).val("").removeData("previousValue");
				pjQ.$(this).attr("src", pjQ.$(this).attr("src").replace(/(&rand=)\d+/g, '\$1' + Math.ceil(Math.random() * 99999)));
				return false;
			});
			
			pjQ.$(window).on("loadRooms", this.$container, function (e) {
				self.loadRooms.call(self);
			}).on("loadBook", this.$container, function (e) {
				self.loadBook.call(self);
			}).on("loadEquipment", this.$container, function (e) {
				self.loadEquipment.call(self);
			}).on("loadFoodDrinks", this.$container, function (e) {
				self.loadFoodDrinks.call(self);
			}).on("loadCheckout", this.$container, function (e) {
				self.loadCheckout.call(self);
			}).on("loadPreview", this.$container, function (e) {
				self.loadPreview.call(self);
			});
			
			if (window.location.hash.length === 0) {
				this.loadRooms.call(this);
			} else {
				onHashChange.call(null);
			}
			
			pjQ.$(document).on("click.mrb", 'button[data-dismiss="modal"]', function (e) {
				if (e && e.preventDefault) {
					e.preventDefault();
				}
				var $modal = pjQ.$(this).closest('.modal');
				if ($modal !== undefined && $modal.length > 0) {
					$modal.modal('hide');
					pjQ.$('body').removeClass('modal-open');
				}
				return false;
			});
		},
		changeBookBy: function(action){
			var self = this,
				index = this.opts.index,
				params = {};
			var book_by = pjQ.$('#pjMrbBookBy_' + self.opts.index).val();
			
			if(book_by == 'multiday')
			{
				pjQ.$('#pjMrbEndDateWrapper_' + self.opts.index).show();
				pjQ.$('#pjMrbFromTo_' + self.opts.index).hide();
			}else if(book_by == 'morning' || book_by == 'afternoon' || book_by == 'evening'){
				pjQ.$('#pjMrbEndDateWrapper_' + self.opts.index).hide();
				pjQ.$('#pjMrbFromTo_' + self.opts.index).hide();
			}else{
				pjQ.$('#pjMrbEndDateWrapper_' + self.opts.index).hide();
				pjQ.$('#pjMrbFromTo_' + self.opts.index).show();
			}
			if(self.opts.session_id != '')
			{
				params.session_id = self.opts.session_id;
			}
			params.book_by = book_by;
			if(action == 'change')
			{
				self.disableButtons();
			}
			pjQ.$.get([self.opts.folder, "index.php?controller=pjFront&action=pjActionSetBookBy"].join(""), params).done(function (data) {
				if(action == 'change')
				{
					self.enableButtons();
				}
				var options = pjQ.$('#pjMrbBookBy_' + self.opts.index).find('option').length;
				if(options > 1 || (options == 1 && book_by != ''))
				{
					self.enableButtons();
				}
				pjQ.$('#pjMrbEndDateMessage').html("").parent().parent().hide();
			}).fail(function () {
				
			});
		},
		checkSlots: function(){
			var self = this,
				index = this.opts.index,
				valid = false;
			var book_by = pjQ.$('#pjMrbBookBy_' + self.opts.index).val();
			if(book_by == 'hour')
			{
				pjQ.$('.pjMrbSlots').each(function(){
					if(pjQ.$(this).is(':checked'))
					{
						valid = true;
					}
				});
			}else{
				valid = true;
			}
			if(valid == true)
			{
				pjQ.$('#pjMrbSlotsMessage').parent().parent().hide();
			}else{
				pjQ.$('#pjMrbSlotsMessage').parent().parent().show();
				self.enableButtons.call(self);
			}
			return valid;
		},
		checkLayout: function(){
			var self = this,
				index = this.opts.index,
				valid = false;
			pjQ.$('.pjMrbLayoutRadio').each(function(){
				if(pjQ.$(this).is(':checked'))
				{
					valid = true;
				}
			});
			if(valid == true)
			{
				pjQ.$('#pjMrbLayoutMessage').parent().parent().hide();
			}else{
				pjQ.$('#pjMrbLayoutMessage').parent().parent().show();
				self.enableButtons.call(self);
			}
			return valid;
		},
		parseDate: function(input){
			var parts = input.match(/(\d+)/g);
			return new Date(parts[0], parts[1]-1, parts[2]);
		},
		loadRooms: function () {
			var self = this,
				index = this.opts.index,
				params = {};
			if(self.opts.session_id != '')
			{
				params.session_id = self.opts.session_id;
			}
			params.locale = self.opts.locale;
			params.index = self.opts.index;
			params.column = self.column;
			params.direction = self.direction;
			params.page = self.page;
			
			pjQ.$.get([this.opts.folder, "index.php?controller=pjFront&action=pjActionRooms"].join(""), params).done(function (data) {
				self.$container.html(data);
			}).fail(function () {
				
			});
		},
		loadBook: function () {
			var self = this,
				index = this.opts.index,
				params = {};
			if(self.opts.session_id != '')
			{
				params.session_id = self.opts.session_id;
			}
			params.locale = self.opts.locale;
			params.index = self.opts.index;
			
			pjQ.$.get([this.opts.folder, "index.php?controller=pjFront&action=pjActionBook"].join(""), params).done(function (data) {
				if (data.code != undefined && data.status == 'ERR') {
					if (!hashBang("#!/loadRooms")) 
					{
						self.loadRooms.call(self);
					}
				}else{
					self.$container.html(data);
					self.bindBook.call(self);
					pjQ.$('html, body').animate({
				        scrollTop: self.$container.offset().top
				    }, 500);
				}
			}).fail(function () {
				
			});
		},
		bindBook: function(){
			var self = this,
				index = this.opts.index,
				params = {};
			
			var $book_by = pjQ.$('#pjMrbBookBy_' + self.opts.index);
			if($book_by.length > 0)
			{
				self.changeBookBy.call(self, 'bind');
			}
			if(pjQ.$('#pjMrbCalendarLocale').length > 0)
			{
				moment.updateLocale('en', {
					months : pjQ.$('#pjMrbCalendarLocale').data('months').split("_"),
			        weekdaysMin : pjQ.$('#pjMrbCalendarLocale').data('days').split("_"),
			        week: { dow: self.opts.week_start }
				});
			}
			if(pjQ.$('.pjMrbDatePickerFrom').length > 0)
			{
				var currentDate = new Date();
				var min_from = pjQ.$('.pjMrbDatePickerFrom').eq(0).attr('data-min');
				var fromDate = self.parseDate(min_from);
				pjQ.$('.pjMrbDatePickerFrom').datetimepicker({
					format: self.opts.momentDateFormat.toUpperCase(),
					locale: moment.locale('en'),
					allowInputToggle: true,
					minDate: new Date(fromDate.getFullYear(), fromDate.getMonth(), fromDate.getDate()),
					ignoreReadonly: true,
					useCurrent: false
				});
				pjQ.$('.pjMrbDatePickerFrom').on('dp.change', function (e) {
					var toDate = new Date(e.date);
					toDate.setDate(toDate.getDate() + 1);
					var momentDate = new moment(toDate);
					if(pjQ.$('.pjMrbDatePickerTo').length > 0)
					{
						pjQ.$('.pjMrbDatePickerTo').datetimepicker().children('input').val(momentDate.format(self.opts.momentDateFormat.toUpperCase()));
						pjQ.$('.pjMrbDatePickerTo').data("DateTimePicker").minDate(e.date);
					}
					
					self.disableButtons();
					if(self.opts.session_id != '')
					{
						params.session_id = self.opts.session_id;
					}
					params.date = pjQ.$('#pjMrbStartDate_' + self.opts.index).val();
					pjQ.$.get([self.opts.folder, "index.php?controller=pjFront&action=pjActionSetDate"].join(""), params).done(function (data) {
						if (!hashBang("#!/loadBook")) 
						{
							self.loadBook.call(self);
						}
					}).fail(function () {
						
					});
				});
			}
			if(pjQ.$('.pjMrbDatePickerTo').length > 0)
			{
				var min_to = pjQ.$('.pjMrbDatePickerTo').eq(0).attr('data-min');
				var fromDate = self.parseDate(min_to);
				pjQ.$('.pjMrbDatePickerTo').datetimepicker({
					format: self.opts.momentDateFormat.toUpperCase(),
					locale: moment.locale('en'),
					allowInputToggle: true,
					ignoreReadonly: true,
					useCurrent: false,
					minDate: new Date(fromDate.getFullYear(), fromDate.getMonth(), fromDate.getDate())
				});
				
				pjQ.$('.pjMrbDatePickerTo').on('dp.change', function (e) {
					
					self.disableButtons();
					if(self.opts.session_id != '')
					{
						params.session_id = self.opts.session_id;
					}
					params.end_date = pjQ.$('#pjMrbEndDate_' + self.opts.index).val();
					pjQ.$.get([self.opts.folder, "index.php?controller=pjFront&action=pjActionSetEndDate"].join(""), params).done(function (data) {
						if(data.status == 'OK')
						{
							pjQ.$('#pjMrbEndDateMessage').html("").parent().parent().hide();
							self.enableButtons();
						}else{
							self.enableButtons();
							pjQ.$('.pjMrbNextButton').attr("disabled", "disabled");
							pjQ.$('#pjMrbEndDateMessage').html(data.text).parent().parent().show();
						}
					}).fail(function () {
						
					});
				});
			}
			
			if (pjQ.$('.pjMrBCustomCheckbox').length) {
				var checkedClass = 'pjMrBCustomInputChecked';
				var disabledClass = 'pjMrBCustomInputDisabled';
				var inputSelector = '.pjMrBCustomCheckbox input, .pjMrBCustomRadio input';

				pjQ.$(inputSelector).each(function() {
					var input = this;
					pjQ.$(input).parent().toggleClass(checkedClass, input.checked);
				}).on('change', function() {
					var input = this;
					pjQ.$(input).parent().toggleClass(checkedClass, input.checked);
					self.checkSlots.call(self);
				}).on('disable', function() {
					var input = this;
					input.disabled = true;
					pjQ.$(input).parent().addClass(disabledClass);
				}).on('enable', function() {
					var input = this;
					input.disabled = false;
					pjQ.$(input).parent().removeClass(disabledClass);
				});
			};
			if (pjQ.$('.pjMrBSpinner').length) {
				pjQ.$('.pjMrBSpinner').on('click', '.pjMrBSpinnerBtn', function(e) {
					var spinnerBtnUpClass = 'pjMrBSpinnerBtnUp';
					var spinnerBtnDownClass = 'pjMrBSpinnerBtnDown';
					var $currentSpinnerBtn = pjQ.$(this);
					var $currentSpinner = $currentSpinnerBtn.parents('.pjMrBSpinner');
					var $currentSpinnerInput = $currentSpinner.find('.form-control');
					var $currentSpinnerInputInitialValue = parseInt($currentSpinnerInput.val(), 10);
					var $currentSpinnerInputMinimum = $currentSpinnerInput.attr('data-min');
					var $currentSpinnerInputMaximum = $currentSpinnerInput.attr('data-max');

					if ($currentSpinnerBtn.hasClass(spinnerBtnDownClass)) {
						$currentSpinnerInput.val($currentSpinnerInputInitialValue --- 1);
					} else if ($currentSpinnerBtn.hasClass(spinnerBtnUpClass)) {
						$currentSpinnerInput.val($currentSpinnerInputInitialValue +++ 1);
					};

					if ((parseInt($currentSpinnerInput.val(), 10)) <= (parseInt($currentSpinnerInputMinimum, 10))) {
						$currentSpinnerInput.val($currentSpinnerInputMinimum)
					};
					if ((parseInt($currentSpinnerInput.val(), 10)) >= (parseInt($currentSpinnerInputMaximum, 10))) {
						$currentSpinnerInput.val($currentSpinnerInputMaximum)
					};
					$currentSpinnerInput.attr('value', $currentSpinnerInput.val());

					e.preventDefault();
				});
			};
			if (validate) 
			{
				var $form = pjQ.$('#pjMrbBookForm_'+ self.opts.index);
				$form.validate({
					rules: {
				        "attendees": {
				            max: parseInt($form.find('input[name="attendees"]').attr('data-max'), 10)
				        }
				    },
					onkeyup: false,
					errorElement: 'li',
					errorPlacement: function (error, element) {
						if(element.attr('name') == 'attendees')
						{
							error.appendTo(element.parent().next().find('ul'));
						}else{
							error.appendTo(element.next().find('ul'));
						}
					},
		            highlight: function(ele, errorClass, validClass) {
		            	var element = pjQ.$(ele);
		            	if(element.attr('name') == 'attendees')
						{
							element.parent().parent().addClass('has-error');
						}else{
							element.parent().addClass('has-error');
						}
		            },
		            unhighlight: function(ele, errorClass, validClass) {
		            	var element = pjQ.$(ele);
		            	if(element.attr('name') == 'attendees')
						{
							element.parent().parent().removeClass('has-error').addClass('has-success');
						}else{
							element.parent().removeClass('has-error').addClass('has-success');
						}
		            },
					submitHandler: function (form) {
						
						self.disableButtons.call(self);
						var book_by =  pjQ.$('#pjMrbBookBy_' + self.opts.index).val();
						if(book_by == 'morning' || book_by == 'afternoon' || book_by == 'evening')
						{
							self.saveBookForm.call(self);
						}else if(book_by == 'hour'){
							var slot_valid = self.checkSlots.call(self);
							if(slot_valid == true)
							{
								self.saveBookForm.call(self);
							}
						}else{
							if(self.opts.session_id != '')
							{
								params.session_id = self.opts.session_id;
							}
							pjQ.$.get([self.opts.folder, "index.php?controller=pjFront&action=pjActionCheckAvail"].join(""), params).done(function (data) {
								if(data.status == 'OK')
								{
									self.saveBookForm.call(self);
								}else{
									pjQ.$('#pjMrbEndDateMessage').html(data.text).parent().parent().show();
								}
							}).fail(function () {
								
							});
						}
						return false;
					}
				});
			}
		},
		saveBookForm: function(){
			var self = this,
				index = this.opts.index,
				params = {};
			if(self.opts.session_id != '')
			{
				params.session_id = self.opts.session_id;
			}
			var $form = pjQ.$('#pjMrbBookForm_'+ self.opts.index);
			self.disableButtons.call(self);
			pjQ.$.post([self.opts.folder, "index.php?controller=pjFront&action=pjActionSaveBookForm"].join(""), $form.serialize()).done(function (data) {
				if (!hashBang("#!/loadEquipment")) 
				{
					self.loadEquipment.call(self);
				}
			}).fail(function () {
				
			});
		},
		loadEquipment: function () {
			var self = this,
				index = this.opts.index,
				params = {};
			if(self.opts.session_id != '')
			{
				params.session_id = self.opts.session_id;
			}
			params.locale = self.opts.locale;
			params.index = self.opts.index;

			pjQ.$.get([this.opts.folder, "index.php?controller=pjFront&action=pjActionEquipment"].join(""), params).done(function (data) {
				if (data.code != undefined && data.status == 'ERR') {
					if(data.code == '100')
					{
						if (!hashBang("#!/loadSearch")) 
						{
							self.loadSearch.call(self);
						}
					}else{
						if (!hashBang("#!/loadFoodDrinks")) 
						{
							self.loadFoodDrinks.call(self);
						}
					}
				}else{
					self.$container.html(data);
					self.bindEquipment.call(self);
					pjQ.$('html, body').animate({
				        scrollTop: self.$container.offset().top
				    }, 500);
				}
			}).fail(function () {
				
			});
		},
		bindEquipment: function(){
			var self = this,
				index = this.opts.index,
				params = {};
			if (pjQ.$('.pjMrBCustomRadio, .pjMrBCustomCheckbox').length) 
			{
				var checkedClass = 'pjMrBCustomInputChecked';
				var disabledClass = 'pjMrBCustomInputDisabled';
				var inputSelector = '.pjMrBCustomCheckbox input, .pjMrBCustomRadio input';

				pjQ.$(inputSelector).each(function() {
					var input = this;

					pjQ.$(input).parent().toggleClass(checkedClass, input.checked);
				}).on('change', function() {
					var input = this;

					if(input.type === 'radio') {
						var name = input.name;
						pjQ.$(input.ownerDocument).find('[name=' + name + ']').each(function() {
							var radioInput = this;
							pjQ.$(radioInput).parent().toggleClass(checkedClass, radioInput.checked);
						});
						self.checkLayout.call(self);
					} else {
						pjQ.$(input).parent().toggleClass(checkedClass, input.checked);
					};
				}).on('disable', function() {
					var input = this;

					input.disabled = true;
					pjQ.$(input).parent().addClass(disabledClass);
				}).on('enable', function() {
					var input = this;

					input.disabled = false;
					pjQ.$(input).parent().removeClass(disabledClass);
				});
			};
			if (pjQ.$('.pjMrBSpinner').length) {
				pjQ.$('.pjMrBSpinner').on('click', '.pjMrBSpinnerBtn', function(e) {
					var spinnerBtnUpClass = 'pjMrBSpinnerBtnUp';
					var spinnerBtnDownClass = 'pjMrBSpinnerBtnDown';
					var $currentSpinnerBtn = pjQ.$(this);
					var $currentSpinner = $currentSpinnerBtn.parents('.pjMrBSpinner');
					var $currentSpinnerInput = $currentSpinner.find('.form-control');
					var $currentSpinnerInputInitialValue = parseInt($currentSpinnerInput.val(), 10);
					var $currentSpinnerInputMinimum = $currentSpinnerInput.attr('data-min');

					if ($currentSpinnerBtn.hasClass(spinnerBtnDownClass)) {
						$currentSpinnerInput.val($currentSpinnerInputInitialValue --- 1);
					} else if ($currentSpinnerBtn.hasClass(spinnerBtnUpClass)) {
						$currentSpinnerInput.val($currentSpinnerInputInitialValue +++ 1);
					};

					if ((parseInt($currentSpinnerInput.val(), 10)) <= (parseInt($currentSpinnerInputMinimum, 10))) {
						$currentSpinnerInput.val($currentSpinnerInputMinimum)
					};

					$currentSpinnerInput.attr('value', $currentSpinnerInput.val());

					e.preventDefault();
				});
			};
			if (validate) 
			{
				var $form = pjQ.$('#pjMrbEquipmentForm_'+ self.opts.index);
				$form.validate({
					onkeyup: false,
					submitHandler: function (form) {
						if(self.checkLayout.call(self) == true)
						{
							self.disableButtons.call(self);
							pjQ.$.post([self.opts.folder, "index.php?controller=pjFront&action=pjActionSaveLayout", '&session_id=', self.opts.session_id].join(""), pjQ.$(form).serialize()).done(function (data) {
								if (!hashBang("#!/loadFoodDrinks")) 
								{
									self.loadFoodDrinks.call(self);
								}
							}).fail(function () {
								
							});
						}
						return false;
					}
				});
			}
		},
		loadFoodDrinks: function () {
			var self = this,
				index = this.opts.index,
				params = {};
			if(self.opts.session_id != '')
			{
				params.session_id = self.opts.session_id;
			}
			params.locale = self.opts.locale;
			params.index = self.opts.index;

			pjQ.$.get([this.opts.folder, "index.php?controller=pjFront&action=pjActionFoodDrinks"].join(""), params).done(function (data) {
				if (data.code != undefined && data.status == 'ERR') {
					if(data.code == '100')
					{
						if (!hashBang("#!/loadSearch")) 
						{
							self.loadSearch.call(self);
						}
					}else{
						if (!hashBang("#!/loadCheckout")) 
						{
							self.loadCheckout.call(self);
						}
					}
				}else{
					self.$container.html(data);
					self.bindFoodDrinks.call(self);
					pjQ.$('html, body').animate({
				        scrollTop: self.$container.offset().top
				    }, 500);
				}
			}).fail(function () {
				
			});
		},
		bindFoodDrinks: function(){
			var self = this,
				index = this.opts.index,
				params = {};
			if (pjQ.$('.pjMrBCustomRadio, .pjMrBCustomCheckbox').length) 
			{
				var checkedClass = 'pjMrBCustomInputChecked';
				var disabledClass = 'pjMrBCustomInputDisabled';
				var inputSelector = '.pjMrBCustomCheckbox input, .pjMrBCustomRadio input';

				pjQ.$(inputSelector).each(function() {
					var input = this;

					pjQ.$(input).parent().toggleClass(checkedClass, input.checked);
				}).on('change', function() {
					var input = this;

					if(input.type === 'radio') {
						var name = input.name;
						pjQ.$(input.ownerDocument).find('[name=' + name + ']').each(function() {
							var radioInput = this;
							pjQ.$(radioInput).parent().toggleClass(checkedClass, radioInput.checked);
						});
						self.checkLayout.call(self);
					} else {
						pjQ.$(input).parent().toggleClass(checkedClass, input.checked);
					};
				}).on('disable', function() {
					var input = this;

					input.disabled = true;
					pjQ.$(input).parent().addClass(disabledClass);
				}).on('enable', function() {
					var input = this;

					input.disabled = false;
					pjQ.$(input).parent().removeClass(disabledClass);
				});
			};
			if (pjQ.$('.pjMrBSpinner').length) {
				pjQ.$('.pjMrBSpinner').on('click', '.pjMrBSpinnerBtn', function(e) {
					var spinnerBtnUpClass = 'pjMrBSpinnerBtnUp';
					var spinnerBtnDownClass = 'pjMrBSpinnerBtnDown';
					var $currentSpinnerBtn = pjQ.$(this);
					var $currentSpinner = $currentSpinnerBtn.parents('.pjMrBSpinner');
					var $currentSpinnerInput = $currentSpinner.find('.form-control');
					var $currentSpinnerInputInitialValue = parseInt($currentSpinnerInput.val(), 10);
					var $currentSpinnerInputMinimum = $currentSpinnerInput.attr('data-min');

					if ($currentSpinnerBtn.hasClass(spinnerBtnDownClass)) {
						$currentSpinnerInput.val($currentSpinnerInputInitialValue --- 1);
					} else if ($currentSpinnerBtn.hasClass(spinnerBtnUpClass)) {
						$currentSpinnerInput.val($currentSpinnerInputInitialValue +++ 1);
					};

					if ((parseInt($currentSpinnerInput.val(), 10)) <= (parseInt($currentSpinnerInputMinimum, 10))) {
						$currentSpinnerInput.val($currentSpinnerInputMinimum)
					};

					$currentSpinnerInput.attr('value', $currentSpinnerInput.val());

					e.preventDefault();
				});
			};
			if (validate) 
			{
				var $form = pjQ.$('#pjMrbFoodDrinksForm_'+ self.opts.index);
				$form.validate({
					onkeyup: false,
					submitHandler: function (form) {
						
						self.disableButtons.call(self);
						pjQ.$.post([self.opts.folder, "index.php?controller=pjFront&action=pjActionSaveFoodDrinks", '&session_id=', self.opts.session_id].join(""), pjQ.$(form).serialize()).done(function (data) {
							if (!hashBang("#!/loadCheckout")) 
							{
								self.loadCheckout.call(self);
							}
						}).fail(function () {
							
						});
						return false;
					}
				});
			}
		},
		loadCheckout: function () {
			var self = this,
				index = this.opts.index,
				params = {};
			if(self.opts.session_id != '')
			{
				params.session_id = self.opts.session_id;
			}
			params.locale = self.opts.locale;
			params.index = self.opts.index;

			pjQ.$.get([this.opts.folder, "index.php?controller=pjFront&action=pjActionCheckout"].join(""), params).done(function (data) {
				if (data.code != undefined && data.status == 'ERR') {
					if (!hashBang("#!/loadSearch")) 
					{
						self.loadSearch.call(self);
					}
				}else{
					self.$container.html(data);
					self.bindCheckout.call(self);
					pjQ.$('html, body').animate({
				        scrollTop: self.$container.offset().top
				    }, 500);
				}
			}).fail(function () {
				
			});
		},
		bindCheckout: function(){
			var self = this,
				index = this.opts.index;
		
			pjQ.$('.modal-dialog').css("z-index", "9999"); 
			
			if (pjQ.$('.pjMrBCustomRadio, .pjMrBCustomCheckbox').length) 
			{
				var checkedClass = 'pjMrBCustomInputChecked';
				var disabledClass = 'pjMrBCustomInputDisabled';
				var inputSelector = '.pjMrBCustomCheckbox input, .pjMrBCustomRadio input';

				pjQ.$(inputSelector).each(function() {
					var input = this;

					pjQ.$(input).parent().toggleClass(checkedClass, input.checked);
				}).on('change', function() {
					var input = this;

					if(input.type === 'radio') {
						var name = input.name;
						pjQ.$(input.ownerDocument).find('[name=' + name + ']').each(function() {
							var radioInput = this;
							pjQ.$(radioInput).parent().toggleClass(checkedClass, radioInput.checked);
						});
						self.checkLayout.call(self);
					} else {
						pjQ.$(input).parent().toggleClass(checkedClass, input.checked);
					};
				}).on('disable', function() {
					var input = this;

					input.disabled = true;
					pjQ.$(input).parent().addClass(disabledClass);
				}).on('enable', function() {
					var input = this;

					input.disabled = false;
					pjQ.$(input).parent().removeClass(disabledClass);
				});
			};
			if (validate) 
			{				
				var $form = pjQ.$('#pjMrbCheckoutForm_'+ self.opts.index);
				var remote_url = self.opts.folder + "index.php?controller=pjFront&action=pjActionCheckCaptcha";
				if(self.opts.session_id != '')
				{
					remote_url += "&session_id=" + self.opts.session_id;
				}
				$form.validate({
					rules: {
						"captcha": {
							remote: remote_url
						}
					},
					onkeyup: false,
					errorElement: 'li',
					errorPlacement: function (error, element) {
						if(element.attr('name') == 'terms' || element.attr('name') == 'captcha')
						{
							error.appendTo(element.parent().next().find('ul'));
						}else{
							error.appendTo(element.next().find('ul'));
						}
					},
		            highlight: function(ele, errorClass, validClass) {
		            	var element = pjQ.$(ele);
		            	if(element.attr('name') == 'terms' || element.attr('name') == 'captcha')
						{
							element.parent().parent().addClass('has-error');
						}else{
							element.parent().addClass('has-error');
						}
		            },
		            unhighlight: function(ele, errorClass, validClass) {
		            	var element = pjQ.$(ele);
		            	if(element.attr('name') == 'terms' || element.attr('name') == 'captcha')
						{
							element.parent().parent().removeClass('has-error').addClass('has-success');
						}else{
							element.parent().removeClass('has-error').addClass('has-success');
						}
		            },
					submitHandler: function (form) {
						self.disableButtons.call(self);
						
						var $form = pjQ.$(form);
						var session_id = '';
						if(self.opts.session_id != '')
						{
							session_id += '&session_id=' + self.opts.session_id;
						}
						pjQ.$.post([self.opts.folder, "index.php?controller=pjFront&action=pjActionCheckout"].join("") + session_id, $form.serialize()).done(function (data) {
							if (data.status == "OK") {
								if (!hashBang("#!/loadPreview")) 
								{
									self.loadPreview.call(self);
								}
							}else if(data.status == 'ERR' && data.code == '101'){
								self.enableButtons.call(self);
								pjQ.$('#pjNcbWrongCaptchaModal').modal('show');
							}
						}).fail(function () {
							self.enableButtons.call(self);
						});
						return false;
					}
				});
			}
		},
		loadPreview: function () {
			var self = this,
				index = this.opts.index,
				params = {};
			if(self.opts.session_id != '')
			{
				params.session_id = self.opts.session_id;
			}
			params.locale = self.opts.locale;
			params.index = self.opts.index;

			pjQ.$.get([this.opts.folder, "index.php?controller=pjFront&action=pjActionPreview"].join(""), params).done(function (data) {
				if (data.code != undefined && data.status == 'ERR') {
					if (!hashBang("#!/loadSearch")) 
					{
						self.loadSearch.call(self);
					}
				}else{
					self.$container.html(data);
					self.bindPreview.call(self);
					pjQ.$('html, body').animate({
				        scrollTop: self.$container.offset().top
				    }, 500);
				}
			}).fail(function () {
				
			});
		},
		bindPreview: function(){
			var self = this,
				index = this.opts.index;
		
			if (validate) 
			{
				var $form = pjQ.$('#pjMrbPreviewForm_'+ self.opts.index);
				$form.validate({
					onkeyup: false,
					submitHandler: function (form) {
						self.disableButtons.call(self);
						var $form = pjQ.$(form);
						var session_id = '';
						if(self.opts.session_id != '')
						{
							session_id += '&session_id=' + self.opts.session_id;
						}
						pjQ.$.post([self.opts.folder, "index.php?controller=pjFront&action=pjActionSaveBooking"].join("") + session_id, $form.serialize()).done(function (data) {
							if (data.code == "200") {
								self.getPaymentForm.call(self, data);
							} else if (data.code == "119") {
								self.enableButtons.call(self);
							}
						}).fail(function () {
							self.enableButtons.call(self);
						});
						return false;
						return false;
					}
				});
			}
		},
		getPaymentForm: function(obj){
			var self = this,
				index = this.opts.index,
				params = {};
			if(self.opts.session_id != '')
			{
				params.session_id = self.opts.session_id;
			}
			params.locale = self.opts.locale;
			params.index = self.opts.index;
			params.booking_id = obj.booking_id;
			params.payment_method = obj.payment;
			
			pjQ.$.get([this.opts.folder, "index.php?controller=pjFront&action=pjActionGetPaymentForm"].join(""), params).done(function (data) {
				self.$container.html(data);
				pjQ.$('html, body').animate({
			        scrollTop: self.$container.offset().top
			    }, 500);
				switch (obj.payment) {
					case 'paypal':
						self.$container.find("form[name='mrbsPaypal']").trigger('submit');
						break;
					case 'authorize':
						self.$container.find("form[name='mrbsAuthorize']").trigger('submit');
						break;
					case 'creditcard':
					case 'bank':
					case 'cash':
						break;
				}
			}).fail(function () {
				log("Deferred is rejected");
			});
		}
	};
	
	window.MeetingRoomBooking = MeetingRoomBooking;	
})(window);