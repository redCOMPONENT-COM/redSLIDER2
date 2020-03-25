/*!
	Autosize 3.0.21
	license: MIT
	http://www.jacklmoore.com/autosize
*/
(function (global, factory) {
	if (typeof define === 'function' && define.amd) {
		define(['exports', 'module'], factory);
	} else if (typeof exports !== 'undefined' && typeof module !== 'undefined') {
		factory(exports, module);
	} else {
		var mod = {
			exports: {}
		};
		factory(mod.exports, mod);
		global.autosize = mod.exports;
	}
})(this, function (exports, module) {
	'use strict';

	var map = typeof Map === "function" ? new Map() : (function () {
		var keys = [];
		var values = [];

		return {
			has: function has(key) {
				return keys.indexOf(key) > -1;
			},
			get: function get(key) {
				return values[keys.indexOf(key)];
			},
			set: function set(key, value) {
				if (keys.indexOf(key) === -1) {
					keys.push(key);
					values.push(value);
				}
			},
			'delete': function _delete(key) {
				var index = keys.indexOf(key);
				if (index > -1) {
					keys.splice(index, 1);
					values.splice(index, 1);
				}
			}
		};
	})();

	var createEvent = function createEvent(name) {
		return new Event(name, { bubbles: true });
	};
	try {
		new Event('test');
	} catch (e) {
		// IE does not support `new Event()`
		createEvent = function (name) {
			var evt = document.createEvent('Event');
			evt.initEvent(name, true, false);
			return evt;
		};
	}

	function assign(ta) {
		if (!ta || !ta.nodeName || ta.nodeName !== 'TEXTAREA' || map.has(ta)) return;

		var heightOffset = null;
		var clientWidth = ta.clientWidth;
		var cachedHeight = null;

		function init() {
			var style = window.getComputedStyle(ta, null);

			if (style.resize === 'vertical') {
				ta.style.resize = 'none';
			} else if (style.resize === 'both') {
				ta.style.resize = 'horizontal';
			}

			if (style.boxSizing === 'content-box') {
				heightOffset = -(parseFloat(style.paddingTop) + parseFloat(style.paddingBottom));
			} else {
				heightOffset = parseFloat(style.borderTopWidth) + parseFloat(style.borderBottomWidth);
			}
			// Fix when a textarea is not on document body and heightOffset is Not a Number
			if (isNaN(heightOffset)) {
				heightOffset = 0;
			}

			update();
		}

		function changeOverflow(value) {
			{
				// Chrome/Safari-specific fix:
				// When the textarea y-overflow is hidden, Chrome/Safari do not reflow the text to account for the space
				// made available by removing the scrollbar. The following forces the necessary text reflow.
				var width = ta.style.width;
				ta.style.width = '0px';
				// Force reflow:
				/* jshint ignore:start */
				ta.offsetWidth;
				/* jshint ignore:end */
				ta.style.width = width;
			}

			ta.style.overflowY = value;
		}

		function getParentOverflows(el) {
			var arr = [];

			while (el && el.parentNode && el.parentNode instanceof Element) {
				if (el.parentNode.scrollTop) {
					arr.push({
						node: el.parentNode,
						scrollTop: el.parentNode.scrollTop
					});
				}
				el = el.parentNode;
			}

			return arr;
		}

		function resize() {
			var originalHeight = ta.style.height;
			var overflows = getParentOverflows(ta);
			var docTop = document.documentElement && document.documentElement.scrollTop; // Needed for Mobile IE (ticket #240)

			ta.style.height = 'auto';

			var endHeight = ta.scrollHeight + heightOffset;

			if (ta.scrollHeight === 0) {
				// If the scrollHeight is 0, then the element probably has display:none or is detached from the DOM.
				ta.style.height = originalHeight;
				return;
			}

			ta.style.height = endHeight + 'px';

			// used to check if an update is actually necessary on window.resize
			clientWidth = ta.clientWidth;

			// prevents scroll-position jumping
			overflows.forEach(function (el) {
				el.node.scrollTop = el.scrollTop;
			});

			if (docTop) {
				document.documentElement.scrollTop = docTop;
			}
		}

		function update() {
			resize();

			var styleHeight = Math.round(parseFloat(ta.style.height));
			var computed = window.getComputedStyle(ta, null);

			// Using offsetHeight as a replacement for computed.height in IE, because IE does not account use of border-box
			var actualHeight = computed.boxSizing === 'content-box' ? Math.round(parseFloat(computed.height)) : ta.offsetHeight;

			// The actual height not matching the style height (set via the resize method) indicates that
			// the max-height has been exceeded, in which case the overflow should be allowed.
			if (actualHeight !== styleHeight) {
				if (computed.overflowY === 'hidden') {
					changeOverflow('scroll');
					resize();
					actualHeight = computed.boxSizing === 'content-box' ? Math.round(parseFloat(window.getComputedStyle(ta, null).height)) : ta.offsetHeight;
				}
			} else {
				// Normally keep overflow set to hidden, to avoid flash of scrollbar as the textarea expands.
				if (computed.overflowY !== 'hidden') {
					changeOverflow('hidden');
					resize();
					actualHeight = computed.boxSizing === 'content-box' ? Math.round(parseFloat(window.getComputedStyle(ta, null).height)) : ta.offsetHeight;
				}
			}

			if (cachedHeight !== actualHeight) {
				cachedHeight = actualHeight;
				var evt = createEvent('autosize:resized');
				try {
					ta.dispatchEvent(evt);
				} catch (err) {
					// Firefox will throw an error on dispatchEvent for a detached element
					// https://bugzilla.mozilla.org/show_bug.cgi?id=889376
				}
			}
		}

		var pageResize = function pageResize() {
			if (ta.clientWidth !== clientWidth) {
				update();
			}
		};

		var destroy = (function (style) {
			window.removeEventListener('resize', pageResize, false);
			ta.removeEventListener('input', update, false);
			ta.removeEventListener('keyup', update, false);
			ta.removeEventListener('autosize:destroy', destroy, false);
			ta.removeEventListener('autosize:update', update, false);

			Object.keys(style).forEach(function (key) {
				ta.style[key] = style[key];
			});

			map['delete'](ta);
		}).bind(ta, {
			height: ta.style.height,
			resize: ta.style.resize,
			overflowY: ta.style.overflowY,
			overflowX: ta.style.overflowX,
			wordWrap: ta.style.wordWrap
		});

		ta.addEventListener('autosize:destroy', destroy, false);

		// IE9 does not fire onpropertychange or oninput for deletions,
		// so binding to onkeyup to catch most of those events.
		// There is no way that I know of to detect something like 'cut' in IE9.
		if ('onpropertychange' in ta && 'oninput' in ta) {
			ta.addEventListener('keyup', update, false);
		}

		window.addEventListener('resize', pageResize, false);
		ta.addEventListener('input', update, false);
		ta.addEventListener('autosize:update', update, false);
		ta.style.overflowX = 'hidden';
		ta.style.wordWrap = 'break-word';

		map.set(ta, {
			destroy: destroy,
			update: update
		});

		init();
	}

	function destroy(ta) {
		var methods = map.get(ta);
		if (methods) {
			methods.destroy();
		}
	}

	function update(ta) {
		var methods = map.get(ta);
		if (methods) {
			methods.update();
		}
	}

	var autosize = null;

	// Do nothing in Node.js environment and IE8 (or lower)
	if (typeof window === 'undefined' || typeof window.getComputedStyle !== 'function') {
		autosize = function (el) {
			return el;
		};
		autosize.destroy = function (el) {
			return el;
		};
		autosize.update = function (el) {
			return el;
		};
	} else {
		autosize = function (el, options) {
			if (el) {
				Array.prototype.forEach.call(el.length ? el : [el], function (x) {
					return assign(x, options);
				});
			}
			return el;
		};
		autosize.destroy = function (el) {
			if (el) {
				Array.prototype.forEach.call(el.length ? el : [el], destroy);
			}
			return el;
		};
		autosize.update = function (el) {
			if (el) {
				Array.prototype.forEach.call(el.length ? el : [el], update);
			}
			return el;
		};
	}

	module.exports = autosize;
});
/**
 * Creating dummy SqueezeBox since we are using BS3 in backend
 * instead of BS2 (which Joomla does). Conflicts happens because
 * of Joomla mootools usage for creating modals.
 * Check AES-1093 for more info.
 */
if (typeof SqueezeBox == 'undefined') {
	var SqueezeBox = {
		initialize : function () {},
		assign : function () {},
		close : function () {}
	}
}

if (typeof slider_editor == 'undefined') {
	var slider_editor = {};
}

if (typeof slider_editor.editor == 'undefined') {
	(function ($) {
		var editor =
		{
			/**
			 * Default class settings
			 */
			settings: {
				/**
				 * Array of allowed keyCode
				 */
				allowedKeyCodes: [
					8, // backspace
					35, // end
					36, // home
					37, // left arrow
					38, // up arrow
					39, // right arrow
					40, // down arrow
					45, // insert
					46, // delete
					112, // F1
					113, // F2
					116 // F5
				],
			},
			/**
			 * Initialise special behaviors of an editor.
			 *
			 * @param   {integer}   editorId  DOM identifier
			 * @param   {ibject}    options   Options
			 * @param   {Function}  callback  Optional callback to execute when editor is loaded
			 *
			 * @return  {void}
			 */
			init: function (editorId, options, callback) {
				var $this = this;

				// Declare options if param is not provided
				if (typeof options == 'undefined') {
					options = {};
				}

				// Declare default selectors if not provided
				if (typeof options.selectors == 'undefined') {
					options.selectors = {
						currentChars: '#' + editorId + '_current_chars'
					}
				}

				// Merge with default
				options = $.extend(true, {}, $this.settings, options);

				switch (options.editorType) {
					case 'tinymce':
						$('#' + editorId).on('beforemove', function(){
							var editor = tinymce.EditorManager.get(editorId);

							if (editor){
								tinymce.EditorManager.execCommand('mceRemoveEditor',false, editorId);
							}
						}).on('aftermove', function(){
								tinymce.EditorManager.execCommand('mceAddEditor',true, editorId);
								$this._tinymce(editorId, options, callback);
								$this._default(editorId, options, callback);
						});

						$this._tinymce(editorId, options, callback);
						break;
					case 'jce':
						$('#' + editorId).on('beforemove', function(){
							var editor = tinymce.EditorManager.get(editorId);

							if (editor){
								tinymce.EditorManager.execCommand('mceRemoveEditor',false, editorId);

								// Move outside JCE container
								$(this).parent().before(this);

								// Remove JCE container
								$(this).parent().find('.wf-editor-container').remove();
							}
						})
						.on('aftermove', function(){
							tinymce.EditorManager.execCommand('mceAddEditor',true, editorId);
							$this._jce(editorId, options, callback);
						});

						$this._jce(editorId, options, callback);
						break;
					case 'codemirror':
						$this._codemirror(editorId, options, callback);
						break;
					default:
						$this._default(editorId, options, callback);
						break;
				}

				$this.initButtons(editorId);
			},
			/**
			 * Initialise xtd-editor buttons of an editor.
			 *
			 * @param   {string}  editor  DOM identifier
			 *
			 * @return  {void}
			 */
			initButtons : function (fieldId) {
				var editor        = fieldId + '_editor';
				var buttons       = $('#' + editor).find('#editor-xtd-buttons a.btn');
				var buttonWrapper = $('#' + editor).find('#editor-xtd-buttons');
				buttonWrapper.css('margin-left', '0px');

				buttons.each(function () {
					var button = $(this);
					button.addClass('btn-default');

					if (button.hasClass('modal-button'))
					{
						// Remove mootools modals from element by cloning it, and attach to bootstrap modal
						var clonedButton = button.clone();
						var href = button.attr('href');
						clonedButton.removeClass('modal-button').removeAttr('href').removeAttr('rel');
						clonedButton.attr('onclick', 'slider_editor.editor.showButtonModal("' + $.trim(clonedButton.text()) + '", "' + editor + '", "' + href + '")');
						button.replaceWith(clonedButton);
					}
				});
			},
			/**
			 * Shows button modal for given editor and button source.
			 *
			 * @param   {string}  title   Modal title
			 * @param   {string}  editor  DOM identifier
			 * @param   {string}  src     Iframe source
			 *
			 * @return  {boolean}
			 */
			showButtonModal : function (title, editor, src) {
				var iframe = $('<iframe/>', {src : src, style: "width: 100%; height: 500px; border: 0;"});
				var modal  = $('#' + editor).parent().find('#editor-button-modal-' + editor);
				modal.find('.modal-title').html(title);
				modal.find('.modal-body').html(iframe);
				modal.modal('show');

				return false;
			},
			/**
			 * Initialise a tinyMCE editor.
			 *
			 * @param   {integer}   editorId  DOM identifier
			 * @param   {object}    options   Options
			 * @param   {Function}  callback  Optional callback to execute when editor is loaded
			 *
			 * @return  {void}
			 */
			_tinymce: function (editorId, options, callback) {
				var $this = this,
					disabled = false,
					editor = null;

				// We are not sure when the editor will be ready, so use a timeout loop
				(function tryInit() {
					editor = tinyMCE.get(editorId);

					function getLength() {
						return $this.getLength(editor.getContent());
					}

					if (editor) {
						// Hook on keyUp to count characters and disable editor if reached limit
						editor.on('keyUp', function (event) {
							contentLength = getLength();

							if (options.limit > 0) {
								disabled = $this._limitReached(contentLength, options);
							}

							$this.updateCount(options.selectors.currentChars, contentLength);
						});

						editor.on('change', function (event) {
							// Needed for ajax saving
							editor.save();
						});

						// Hook on keyPress and cancel it if reached limit
						editor.on('keyPress', function (event) {
							if (disabled) {
								$this.hookKeyPress(event);
							}
						});

						editor.on('init', function (event) {
							var contentLength = getLength();
							$this.updateCount(options.selectors.currentChars, contentLength);

							if (options.limit > 0 && typeof document.formvalidator != 'undefined') {
								$('#' + editorId).addClass('validate-' + editorId);

								document.formvalidator.setHandler(editorId, function(value){
									editor.save();
									return !$this._limitReached(getLength(), options);
								});
							}

							$('#' + editorId).parents('form').trigger('field_init_done');
						});

						if (typeof callback === 'function') {
							callback(editor, options);
						}
					}
					else {
						setTimeout(tryInit, 300);
					}
				})();
			},
			/** Initialise a JCE editor.
			 *
			 * @param   {integer}   editorId  DOM identifier
			 * @param   {ibject}    options   Options
			 * @param   {Function}  callback  Optional callback to execute when editor is loaded
			 *
			 * @return  {void}
			 */
			_jce: function (editorId, options, callback) {
				var $this = this,
					disabled = false,
					editor = null;

				// We are not sure when the editor will be ready, so use a timeout loop
				(function tryInit() {
					editor = tinyMCE.get(editorId);

					function getLength() {
						return $this.getLength(editor.getContent());
					}

					if (editor) {

						// Hook on keyUp to count characters and disable editor if reached limit
						editor.onKeyUp.add(function (event) {
							contentLength = getLength();

							if (options.limit > 0) {
								disabled = $this._limitReached(contentLength, options);
							}

							$this.updateCount(options.selectors.currentChars, contentLength);
						});

						editor.onChange.add(function (event) {
							editor.save();
							contentLength = getLength();
							$this.updateCount(options.selectors.currentChars, contentLength);
						});

						// Hook on keyPress and cancel it if reached limit
						editor.onKeyPress.add(function (editor, event) {
							if (disabled) {
								$this.hookKeyPress(event);
							}
						});

						editor.onInit.add(function (event) {
							var contentLength = getLength();
							$this.updateCount(options.selectors.currentChars, contentLength);

							if (options.limit > 0 && typeof document.formvalidator != 'undefined') {
								$('#' + editorId).addClass('validate-' + editorId);

								document.formvalidator.setHandler(editorId, function(value){
									editor.save();
									return !$this._limitReached(getLength(), options);
								});
							}

							$('#' + editorId).parents('form').trigger('field_init_done');
						});

						if (typeof callback === 'function') {
							callback(editor, options);
						}
					}
					else {
						setTimeout(tryInit, 300);
					}
				})();
			},
			/** Initialise a CodeMirror editor.
			 *
			 * @param   {integer}   editorId  DOM identifier
			 * @param   {ibject}    options   Options
			 * @param   {Function}  callback  Optional callback to execute when editor is loaded
			 *
			 * @return  {void}
			 */
			_codemirror: function (editorId, options, callback) {
				var $this = this,
					disabled = false,
					editor = null;

				// We are not sure when the editor will be ready, so use a timeout loop
				(function tryInit() {
					editor = Joomla.editors.instances[editorId];

					if (editor) {
						var contentLength = $this.getLength(editor.getValue());

						$this.updateCount(options.selectors.currentChars, contentLength);
						editor.on('beforeChange', function (instance, changeObj) {
							if (options.limit > 0)
							{
								contentLength = $this.getLength(instance.getValue());

								if (changeObj.origin == "+input" && $this._limitReached(contentLength, options)) {
									changeObj.cancel();
								}
							}
						});

						editor.on('change', function (instance, changeObj) {
							contentLength = $this.getLength(instance.getValue());

							if (options.limit > 0)
							{
								disabled = $this._limitReached(contentLength, options);
							}

							$this.updateCount(options.selectors.currentChars, contentLength);
						});

						if (options.limit > 0 && typeof document.formvalidator != 'undefined') {
							$('#' + editorId).addClass('validate-' + editorId);

							document.formvalidator.setHandler(editorId, function(value){
								return !$this._limitReached($this.getLength(editor.val()), options);
							});
						}

						if (typeof callback === 'function') {
							callback(editor, options);
						}
					}
					else {
						setTimeout(tryInit, 300);
					}
				})();
			},
			/** Initialise a textarea editor.
			 *
			 * @param   {integer}   editorId  DOM identifier
			 * @param   {ibject}    options   Options
			 * @param   {Function}  callback  Optional callback to execute when editor is loaded
			 *
			 * @return  {void}
			 * @private
			 */
			_default: function (editorId, options, callback) {
				var $this = this,
					disabled = false,
					editor = $('textarea#' + editorId);

				if (!editor.length) {
					return;
				}

				var contentLength = $this.getLength(editor.val());
				$this.updateCount(options.selectors.currentChars, contentLength);

				try {
					editor.keyup(function (event) {
						contentLength = $this.getLength(editor.val());

						if (options.limit > 0) {
							disabled = $this._limitReached(contentLength, options);
						}

						$this.updateCount(options.selectors.currentChars, contentLength);
					});
				}
				catch (error) {
				}

				try {
					editor.change(function (event) {
						contentLength = $this.getLength(editor.val());
					});
				}
				catch (error) {
				}

				try {
					editor.keypress(function (event) {
						if (disabled) {
							$this.hookKeyPress(event);
						}
					})
				}
				catch (error) {
				}

				// Autosize
				if (options.autoSize == 1) {
					autosize(editor);
				}

				if (options.limit > 0 && typeof document.formvalidator != 'undefined') {
					$('#' + editorId).addClass('validate-' + editorId);

					document.formvalidator.setHandler(editorId, function(value){
						return !$this._limitReached($this.getLength(editor.val()), options);
					});
				}

				if (typeof callback === 'function') {
					callback(editor, options);
				}
			},
			/**
			 * Verify if contentLength is reached limit.
			 *
			 * @param   {integer}  contentLength  Length of the editor content
			 * @param   {object}   options        Editor options
			 *
			 * @return  {boolean}
			 * @private
			 */
			_limitReached: function (contentLength, options) {
				try {
					var limit = parseInt(options.limit);

					if (limit > 0 && contentLength > limit) {
						return true;
					}

					return false;
				}
				catch (error) {

				}
			},
			/**
			 * Update chars count.
			 *
			 * @param   {string}  targetEl  Selector of counter
			 * @param   {string}  count     New chars count
			 *
			 * @return  {void}
			 */
			updateCount: function (targetEl, count) {
				$(targetEl).text(count);
			},
			/**
			 * Get total chars in a string.
			 *
			 * @param   {string}  content  Source string
			 *
			 * @return  {integer}
			 */
			getLength: function (content) {
				var el = document.createElement('div');
				el.innerHTML = content;

				var text = el.textContent || el.innerText || $(el).text();

				return text.length;
			},
			/**
			 * Prevent keyPress if keyCode not in allowedKeyCodes
			 *
			 * @param   {object}  event  Event to check for key code
			 *
			 * @return  {boolean}
			 */
			hookKeyPress: function (event) {
				var $this = this;
				try {
					if ($this.settings.allowedKeyCodes.indexOf(event.keyCode) < 0) {
						event.stopPropagation();
						event.preventDefault();
						return false;
					}

					return true;
				}
				catch (error) {
				}
			}
		};
		slider_editor.editor = editor;
	})(jQuery);
}
