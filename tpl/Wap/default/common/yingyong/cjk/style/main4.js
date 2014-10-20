define(function(require, exports, module) {
	var $ = require("./zepto"),
	$ = require("./maskLayer"),
	app = require("./main1"),
	IScroll = require("./iscroll"),
	$formPages = $(".page-form");

			$formPages.each(function(i, item) {
				function showValidateTip($input, msg) {
					var type = $('[name="name"]').prop("type");
					return "radio" != type && "checkbox" != type && ($input.data("value", $input.val()).val(msg).addClass("z-error"), $input.blur()),
					$btnSubmit.val("请填写完整").prop("disabled", !0),
					!1
				}
				console.log("form init"),
				$page = $(item);
				var $contactFormBox = $page.find(".m-contactForm");
				if ($contactFormBox.length) {
					var $formContact = $contactFormBox.find("#formContact"),
					$btnSubmit = $formContact.find(".btn-submit"),
					$contactFormLayer = $page.find(".m-contactFormLayer").maskLayer(),
					$successTipLayer = $contactFormBox.find(".successTipLayer").maskLayer({
						closeButton: !1
					}),
					contactFormLayer = $contactFormLayer.maskLayer("getPluginObject"),
					successTipLayer = $successTipLayer.maskLayer("getPluginObject");
					contactFormLayer.on("show",
					function() {
						app.disableFlipPage()
					}),
					contactFormLayer.on("hide",
					function() {
						app.enableFlipPage(),
						$formContact[0].reset(),
						$btnSubmit.prop("disabled", !1)
					}),
					$page.delegate(".m-contactUs a", $.isPC ? "click": "tap",
					function() {
						if (contactFormLayer.show(), !window.form_contactFormLayer_iScrollFloag) {
							window.form_contactFormLayer_iScrollFloag = !0;
							var $win = $(window),
							formScroll = new IScroll($contactFormLayer[0]);
							$formContact.delegate("input", "focus",
							function() {
								form_focus_input = this,
								setTimeout(function() {
									formScroll.refresh(),
									formScroll.scrollToElement(form_focus_input)
								},
								100)
							}),
							$win.on("resize",
							function() {
								$contactFormBox.css("margin-top", 120),
								setTimeout(function() {
									$contactFormBox.css("margin-top", 0),
									$contactFormBox.parent().css({
										"margin-top": 120,
										"padding-bottom": 120
									})
								},
								100),
								window.navigator.userAgent.indexOf("iPhone") >= 0 && $contactFormLayer.css("height", window.innerHeight),
								setTimeout(function() {
									formScroll.refresh(),
									formScroll.scrollToElement(form_focus_input)
								},
								100)
							}).resize()
						}
					}),
					$formContact.delegate("input.z-error", "focus",
					function() {
						var $input = $(this);
						$input.val($input.data("value")).removeClass("z-error"),
						$btnSubmit.prop("disabled", !1)
					}).delegate(".btn-submit", $.isPC ? "click": "tap",
					function() {
						$btnSubmit.prop("disabled") || $formContact.submit()
					}),
					$formContact.on("submit",
					function(e) {
						e.preventDefault();
						var $name = $formContact.find('input[name="name"]'),
						$tel = ($formContact.find('input[name="sex"]'), $formContact.find('input[name="tel"]')),
						$company = $formContact.find('input[name="company"]'),
						$post = $formContact.find('input[name="post"]'),
						$email = $formContact.find('input[name="email"]');
						if ($name.length && 0 == $.trim($name.val()).length) return showValidateTip($name, "请输入姓名！");
						if ($tel.length && 0 == $.trim($tel.val()).length) return showValidateTip($tel, "请输入电话！");
						if ($tel.length > 0 && $.trim($tel.val()).length > 0) {
							var reg = /^13[0-9]{9}|15[0-9]{9}|17[0-9]{9}|18[0-9]{9}$/;
							if (!$.trim($tel.val()).match(reg)) return showValidateTip($tel, "电话号码输入不正确！")
						}
						if ($email.length && 0 == $.trim($email.val()).length) return showValidateTip($email, "请输入邮箱！");
						if ($email.length > 0 && $.trim($email.val()).length > 0) {
							var reg = /(^[a-z\d]+(\.[a-z\d]+)*@([\da-z](-[\da-z])?)+(\.{1,2}[a-z]+)+$)/i;
							if (!$.trim($email.val()).match(reg)) return showValidateTip($email, "邮箱格式不正确！")
						}
						return $company.length && 0 == $.trim($company.val()).length ? showValidateTip($company, "请输入公司名称！") : $post.length && 0 == $.trim($post.val()).length ? showValidateTip($post, "请输入职务！") : void $.ajax({
							url: $formContact.attr("action"),
							type: $formContact.attr("method"),
							data: $formContact.serialize(),
							dataType: "json",
							success: function() {
								successTipLayer.show(),
								setTimeout(function() {
									successTipLayer.hide(),
									setTimeout(function() {
										contactFormLayer.hide(),
										$formContact[0].reset()
									},
									800)
								},
								2e3)
							},
							error: function() {
								alert($("input[data-fail-msg]").val())
							}
						})
					})
				}
				$page.on("active",
				function() {
					console.log("form active")
				}).on("current",
				function() {
					console.log("form current")
				})
			})
			/*	module.exports = {
		init: function() {
		}
	}*/
});