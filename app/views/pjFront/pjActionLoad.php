<?php
mt_srand();
$index = mt_rand(1, 9999);
$theme = isset($_GET['theme']) ? $_GET['theme'] : $tpl['option_arr']['o_theme'];
?>
<div id="pjWrapperMeetingRoomBooking_<?php echo $theme;?>">
	<div id="pjMrbContainer_<?php echo $index; ?>" class="container-fluid pjMrBContainer"></div>
	
	<div class="modal fade pjTbModal" id="pjNcbTermModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	  	<div class="modal-dialog">
	    	<div class="modal-content">
	      		<div class="modal-header">
	        		<button type="button" class="close" data-pj-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only"><?php __('front_btn_close');?></span></button>
	        		<h4 class="modal-title pjTbModalTitle" id="myModalLabel"><?php __('front_terms_title', false, true); ?></h4>
	      		</div>
			    <div class="modal-body">
			    	<?php echo nl2br(pjSanitize::clean($tpl['terms_conditions'])); ?>
			    </div>
		      	<div class="modal-footer">
		        	<button type="button" class="btn btn-default" data-pj-dismiss="modal"><?php __('front_btn_close');?></button>
		      	</div>
	    	</div>
	  	</div>
	</div>
	
	<div class="modal fade pjTbModal" id="pjNcbWrongCaptchaModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	  	<div class="modal-dialog">
	    	<div class="modal-content">
			    <div class="modal-body">
			    	<?php __('front_incorrect_captcha');?>
			    </div>
		      	<div class="modal-footer">
		        	<button type="button" class="btn btn-default" data-pj-dismiss="modal"><?php __('front_btn_close');?></button>
		      	</div>
	    	</div>
	  	</div>
	</div>
</div>

<script type="text/javascript">
var pjQ = pjQ || {},
	MeetingRoomBooking_<?php echo $index; ?>;
(function () {
	"use strict";
	
	var isSafari = /Safari/.test(navigator.userAgent) && /Apple Computer/.test(navigator.vendor),
		
	loadCssHack = function(url, callback){
		var link = document.createElement('link');
		link.type = 'text/css';
		link.rel = 'stylesheet';
		link.href = url;

		document.getElementsByTagName('head')[0].appendChild(link);

		var img = document.createElement('img');
		img.onerror = function(){
			if (callback && typeof callback === "function") {
				callback();
			}
		};
		img.src = url;
	},
	loadRemote = function(url, type, callback) {
		if (type === "css" && isSafari) {
			loadCssHack(url, callback);
			return;
		}
		var _element, _type, _attr, scr, s, element;
		
		switch (type) {
		case 'css':
			_element = "link";
			_type = "text/css";
			_attr = "href";
			break;
		case 'js':
			_element = "script";
			_type = "text/javascript";
			_attr = "src";
			break;
		}
		
		scr = document.getElementsByTagName(_element);
		s = scr[scr.length - 1];
		element = document.createElement(_element);
		element.type = _type;
		if (type == "css") {
			element.rel = "stylesheet";
		}
		if (element.readyState) {
			element.onreadystatechange = function () {
				if (element.readyState == "loaded" || element.readyState == "complete") {
					element.onreadystatechange = null;
					if (callback && typeof callback === "function") {
						callback();
					}
				}
			};
		} else {
			element.onload = function () {
				if (callback && typeof callback === "function") {
					callback();
				}
			};
		}
		element[_attr] = url;
		s.parentNode.insertBefore(element, s.nextSibling);
	},
	loadScript = function (url, callback) {
		loadRemote(url, "js", callback);
	},
	loadCss = function (url, callback) {
		loadRemote(url, "css", callback);
	},
	getSessionId = function () {
		return sessionStorage.getItem("session_id") == null ? "" : sessionStorage.getItem("session_id");
	},
	createSessionId = function () {
		if(getSessionId()=="") {
			sessionStorage.setItem("session_id", "<?php echo session_id(); ?>");
		}
	},
	options = {
		server: "<?php echo PJ_INSTALL_URL; ?>",
		folder: "<?php echo PJ_INSTALL_URL; ?>",
		theme: "<?php echo $theme;?>",
		index: <?php echo $index; ?>,
		locale: <?php echo isset($_GET['locale']) && (int) $_GET['locale'] > 0 ? (int) $_GET['locale'] : $controller->pjActionGetLocale(); ?>,
		week_start: <?php echo (int) $tpl['option_arr']['o_week_start']; ?>,
		momentDateFormat: "<?php echo pjUtil::toMomemtJS($tpl['option_arr']['o_date_format']); ?>"
	};
	<?php
	$dm = new pjDependencyManager(PJ_INSTALL_PATH, PJ_THIRD_PARTY_PATH);
	$dm->load(PJ_CONFIG_PATH . 'dependencies.php')->resolve();
	?>
	loadScript("<?php echo PJ_INSTALL_URL . $dm->getPath('storage_polyfill'); ?>storagePolyfill.min.js", function () {
		if (isSafari) {
			createSessionId();
			options.session_id = getSessionId();
		}else{
			options.session_id = "";
		}
		loadScript("<?php echo PJ_INSTALL_URL . $dm->getPath('pj_jquery'); ?>pjQuery.min.js", function () {
			loadScript("<?php echo PJ_INSTALL_URL . $dm->getPath('pj_validate'); ?>pjQuery.validate.min.js", function () {
				loadScript("<?php echo PJ_INSTALL_URL . $dm->getPath('pj_bootstrap'); ?>pjQuery.bootstrap.min.js", function () {
					loadScript("<?php echo PJ_INSTALL_URL . $dm->getPath('pj_bootstrap_datetimepicker'); ?>moment-with-locales.min.js", function () {
						loadScript("<?php echo PJ_INSTALL_URL . $dm->getPath('pj_bootstrap_datetimepicker'); ?>pjQuery.bootstrap-datetimepicker.min.js", function () {
							loadScript("<?php echo PJ_INSTALL_URL . PJ_JS_PATH ?>pjFront.js", function () {
								MeetingRoomBooking = MeetingRoomBooking(options);
							});
						});
					});
				});
			});
		});
	});
})();
</script>