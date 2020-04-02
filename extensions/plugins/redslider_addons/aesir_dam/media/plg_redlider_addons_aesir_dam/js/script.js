/**
 * Copyright (c) 2011-2014 Felix Gnass
 * Licensed under the MIT license
 * http://spin.js.org/
 *
 * Example:
    var opts = {
      lines: 12             // The number of lines to draw
    , length: 7             // The length of each line
    , width: 5              // The line thickness
    , radius: 10            // The radius of the inner circle
    , scale: 1.0            // Scales overall size of the spinner
    , corners: 1            // Roundness (0..1)
    , color: '#000'         // #rgb or #rrggbb
    , opacity: 1/4          // Opacity of the lines
    , rotate: 0             // Rotation offset
    , direction: 1          // 1: clockwise, -1: counterclockwise
    , speed: 1              // Rounds per second
    , trail: 100            // Afterglow percentage
    , fps: 20               // Frames per second when using setTimeout()
    , zIndex: 2e9           // Use a high z-index by default
    , className: 'spinner'  // CSS class to assign to the element
    , top: '50%'            // center vertically
    , left: '50%'           // center horizontally
    , shadow: false         // Whether to render a shadow
    , hwaccel: false        // Whether to use hardware acceleration (might be buggy)
    , position: 'absolute'  // Element positioning
    }
    var target = document.getElementById('foo')
    var spinner = new Spinner(opts).spin(target)
 */
;(function (root, factory) {

  /* CommonJS */
  if (typeof module == 'object' && module.exports) module.exports = factory()

  /* AMD module */
  else if (typeof define == 'function' && define.amd) define(factory)

  /* Browser global */
  else root.Spinner = factory()
}(this, function () {
  "use strict"

  var prefixes = ['webkit', 'Moz', 'ms', 'O'] /* Vendor prefixes */
    , animations = {} /* Animation rules keyed by their name */
    , useCssAnimations /* Whether to use CSS animations or setTimeout */
    , sheet /* A stylesheet to hold the @keyframe or VML rules. */

  /**
   * Utility function to create elements. If no tag name is given,
   * a DIV is created. Optionally properties can be passed.
   */
  function createEl (tag, prop) {
    var el = document.createElement(tag || 'div')
      , n

    for (n in prop) el[n] = prop[n]
    return el
  }

  /**
   * Appends children and returns the parent.
   */
  function ins (parent /* child1, child2, ...*/) {
    for (var i = 1, n = arguments.length; i < n; i++) {
      parent.appendChild(arguments[i])
    }

    return parent
  }

  /**
   * Creates an opacity keyframe animation rule and returns its name.
   * Since most mobile Webkits have timing issues with animation-delay,
   * we create separate rules for each line/segment.
   */
  function addAnimation (alpha, trail, i, lines) {
    var name = ['opacity', trail, ~~(alpha * 100), i, lines].join('-')
      , start = 0.01 + i/lines * 100
      , z = Math.max(1 - (1-alpha) / trail * (100-start), alpha)
      , prefix = useCssAnimations.substring(0, useCssAnimations.indexOf('Animation')).toLowerCase()
      , pre = prefix && '-' + prefix + '-' || ''

    if (!animations[name]) {
      sheet.insertRule(
        '@' + pre + 'keyframes ' + name + '{' +
        '0%{opacity:' + z + '}' +
        start + '%{opacity:' + alpha + '}' +
        (start+0.01) + '%{opacity:1}' +
        (start+trail) % 100 + '%{opacity:' + alpha + '}' +
        '100%{opacity:' + z + '}' +
        '}', sheet.cssRules.length)

      animations[name] = 1
    }

    return name
  }

  /**
   * Tries various vendor prefixes and returns the first supported property.
   */
  function vendor (el, prop) {
    var s = el.style
      , pp
      , i

    prop = prop.charAt(0).toUpperCase() + prop.slice(1)
    if (s[prop] !== undefined) return prop
    for (i = 0; i < prefixes.length; i++) {
      pp = prefixes[i]+prop
      if (s[pp] !== undefined) return pp
    }
  }

  /**
   * Sets multiple style properties at once.
   */
  function css (el, prop) {
    for (var n in prop) {
      el.style[vendor(el, n) || n] = prop[n]
    }

    return el
  }

  /**
   * Fills in default values.
   */
  function merge (obj) {
    for (var i = 1; i < arguments.length; i++) {
      var def = arguments[i]
      for (var n in def) {
        if (obj[n] === undefined) obj[n] = def[n]
      }
    }
    return obj
  }

  /**
   * Returns the line color from the given string or array.
   */
  function getColor (color, idx) {
    return typeof color == 'string' ? color : color[idx % color.length]
  }

  // Built-in defaults

  var defaults = {
    lines: 12             // The number of lines to draw
  , length: 7             // The length of each line
  , width: 5              // The line thickness
  , radius: 10            // The radius of the inner circle
  , scale: 1.0            // Scales overall size of the spinner
  , corners: 1            // Roundness (0..1)
  , color: '#000'         // #rgb or #rrggbb
  , opacity: 1/4          // Opacity of the lines
  , rotate: 0             // Rotation offset
  , direction: 1          // 1: clockwise, -1: counterclockwise
  , speed: 1              // Rounds per second
  , trail: 100            // Afterglow percentage
  , fps: 20               // Frames per second when using setTimeout()
  , zIndex: 2e9           // Use a high z-index by default
  , className: 'spinner'  // CSS class to assign to the element
  , top: '50%'            // center vertically
  , left: '50%'           // center horizontally
  , shadow: false         // Whether to render a shadow
  , hwaccel: false        // Whether to use hardware acceleration (might be buggy)
  , position: 'absolute'  // Element positioning
  }

  /** The constructor */
  function Spinner (o) {
    this.opts = merge(o || {}, Spinner.defaults, defaults)
  }

  // Global defaults that override the built-ins:
  Spinner.defaults = {}

  merge(Spinner.prototype, {
    /**
     * Adds the spinner to the given target element. If this instance is already
     * spinning, it is automatically removed from its previous target b calling
     * stop() internally.
     */
    spin: function (target) {
      this.stop()

      var self = this
        , o = self.opts
        , el = self.el = createEl(null, {className: o.className})

      css(el, {
        position: o.position
      , width: 0
      , zIndex: o.zIndex
      , left: o.left
      , top: o.top
      })

      if (target) {
        target.insertBefore(el, target.firstChild || null)
      }

      el.setAttribute('role', 'progressbar')
      self.lines(el, self.opts)

      if (!useCssAnimations) {
        // No CSS animation support, use setTimeout() instead
        var i = 0
          , start = (o.lines - 1) * (1 - o.direction) / 2
          , alpha
          , fps = o.fps
          , f = fps / o.speed
          , ostep = (1 - o.opacity) / (f * o.trail / 100)
          , astep = f / o.lines

        ;(function anim () {
          i++
          for (var j = 0; j < o.lines; j++) {
            alpha = Math.max(1 - (i + (o.lines - j) * astep) % f * ostep, o.opacity)

            self.opacity(el, j * o.direction + start, alpha, o)
          }
          self.timeout = self.el && setTimeout(anim, ~~(1000 / fps))
        })()
      }
      return self
    }

    /**
     * Stops and removes the Spinner.
     */
  , stop: function () {
      var el = this.el
      if (el) {
        clearTimeout(this.timeout)
        if (el.parentNode) el.parentNode.removeChild(el)
        this.el = undefined
      }
      return this
    }

    /**
     * Internal method that draws the individual lines. Will be overwritten
     * in VML fallback mode below.
     */
  , lines: function (el, o) {
      var i = 0
        , start = (o.lines - 1) * (1 - o.direction) / 2
        , seg

      function fill (color, shadow) {
        return css(createEl(), {
          position: 'absolute'
        , width: o.scale * (o.length + o.width) + 'px'
        , height: o.scale * o.width + 'px'
        , background: color
        , boxShadow: shadow
        , transformOrigin: 'left'
        , transform: 'rotate(' + ~~(360/o.lines*i + o.rotate) + 'deg) translate(' + o.scale*o.radius + 'px' + ',0)'
        , borderRadius: (o.corners * o.scale * o.width >> 1) + 'px'
        })
      }

      for (; i < o.lines; i++) {
        seg = css(createEl(), {
          position: 'absolute'
        , top: 1 + ~(o.scale * o.width / 2) + 'px'
        , transform: o.hwaccel ? 'translate3d(0,0,0)' : ''
        , opacity: o.opacity
        , animation: useCssAnimations && addAnimation(o.opacity, o.trail, start + i * o.direction, o.lines) + ' ' + 1 / o.speed + 's linear infinite'
        })

        if (o.shadow) ins(seg, css(fill('#000', '0 0 4px #000'), {top: '2px'}))
        ins(el, ins(seg, fill(getColor(o.color, i), '0 0 1px rgba(0,0,0,.1)')))
      }
      return el
    }

    /**
     * Internal method that adjusts the opacity of a single line.
     * Will be overwritten in VML fallback mode below.
     */
  , opacity: function (el, i, val) {
      if (i < el.childNodes.length) el.childNodes[i].style.opacity = val
    }

  })


  function initVML () {

    /* Utility function to create a VML tag */
    function vml (tag, attr) {
      return createEl('<' + tag + ' xmlns="urn:schemas-microsoft.com:vml" class="spin-vml">', attr)
    }

    // No CSS transforms but VML support, add a CSS rule for VML elements:
    sheet.addRule('.spin-vml', 'behavior:url(#default#VML)')

    Spinner.prototype.lines = function (el, o) {
      var r = o.scale * (o.length + o.width)
        , s = o.scale * 2 * r

      function grp () {
        return css(
          vml('group', {
            coordsize: s + ' ' + s
          , coordorigin: -r + ' ' + -r
          })
        , { width: s, height: s }
        )
      }

      var margin = -(o.width + o.length) * o.scale * 2 + 'px'
        , g = css(grp(), {position: 'absolute', top: margin, left: margin})
        , i

      function seg (i, dx, filter) {
        ins(
          g
        , ins(
            css(grp(), {rotation: 360 / o.lines * i + 'deg', left: ~~dx})
          , ins(
              css(
                vml('roundrect', {arcsize: o.corners})
              , { width: r
                , height: o.scale * o.width
                , left: o.scale * o.radius
                , top: -o.scale * o.width >> 1
                , filter: filter
                }
              )
            , vml('fill', {color: getColor(o.color, i), opacity: o.opacity})
            , vml('stroke', {opacity: 0}) // transparent stroke to fix color bleeding upon opacity change
            )
          )
        )
      }

      if (o.shadow)
        for (i = 1; i <= o.lines; i++) {
          seg(i, -2, 'progid:DXImageTransform.Microsoft.Blur(pixelradius=2,makeshadow=1,shadowopacity=.3)')
        }

      for (i = 1; i <= o.lines; i++) seg(i)
      return ins(el, g)
    }

    Spinner.prototype.opacity = function (el, i, val, o) {
      var c = el.firstChild
      o = o.shadow && o.lines || 0
      if (c && i + o < c.childNodes.length) {
        c = c.childNodes[i + o]; c = c && c.firstChild; c = c && c.firstChild
        if (c) c.opacity = val
      }
    }
  }

  if (typeof document !== 'undefined') {
    sheet = (function () {
      var el = createEl('style', {type : 'text/css'})
      ins(document.getElementsByTagName('head')[0], el)
      return el.sheet || el.styleSheet
    }())

    var probe = css(createEl('group'), {behavior: 'url(#default#VML)'})

    if (!vendor(probe, 'transform') && probe.adj) initVML()
    else useCssAnimations = vendor(probe, 'animation')
  }

  return Spinner

}));

/**
 * Copyright (c) 2011-2014 Felix Gnass
 * Licensed under the MIT license
 * http://spin.js.org/
 */

/*

Basic Usage:
============

$('#el').spin() // Creates a default Spinner using the text color of #el.
$('#el').spin({ ... }) // Creates a Spinner using the provided options.

$('#el').spin(false) // Stops and removes the spinner.

Using Presets:
==============

$('#el').spin('small') // Creates a 'small' Spinner using the text color of #el.
$('#el').spin('large', '#fff') // Creates a 'large' white Spinner.

Adding a custom preset:
=======================

$.fn.spin.presets.flower = {
  lines:   9
, length: 10
, width:  20
, radius:  0
}

$('#el').spin('flower', 'red')

*/

;(function(factory) {

  if (typeof exports == 'object') {
    // CommonJS
    factory(require('jquery'), require('spin.js'))
  } else if (typeof define == 'function' && define.amd) {
    // AMD, register as anonymous module
    define(['jquery', 'spin'], factory)
  } else {
    // Browser globals
    if (!window.Spinner) throw new Error('Spin.js not present')
    factory(window.jQuery, window.Spinner)
  }

}(function($, Spinner) {

  $.fn.spin = function(opts, color) {

    return this.each(function() {
      var $this = $(this)
        , data = $this.data()

      if (data.spinner) {
        data.spinner.stop()
        delete data.spinner
      }
      if (opts !== false) {
        opts = $.extend(
          { color: color || $this.css('color') }
        , $.fn.spin.presets[opts] || opts
        )
        data.spinner = new Spinner(opts).spin(this)
      }
    })
  }

  $.fn.spin.presets = {
    tiny:  { lines:  8, length: 2, width: 2, radius: 3 }
  , small: { lines:  8, length: 4, width: 3, radius: 5 }
  , large: { lines: 10, length: 8, width: 4, radius: 8 }
  }

}));

(function ($) {

	Dropzone.options.aesirFieldAssetDropzone = {
		paramName: 'file',
		addRemoveLinks: false,
		parallelUploads: 1,
		init: function () {
			this.on('error', function (file, response) {
				$(file.previewElement).find('.dz-error-message').text(response);
			});
		},
		accept: function (file, done) {
			var duplicatesUrl = $('#aesir-field-asset-dropzone').data('duplicates-url');
			var data = {
				'name': file.name,
				'collection_id': $('#upload_collection_id').val()
			};
			data[$('#sessionToken').attr('name')] = 1;

			$.ajax({
				method: "POST",
				url: duplicatesUrl,
				data: data,
				dataType: "json"
			}).always(function (data, textStatus, jqXHR) {
				if (textStatus != 'success'){
					done('Refused');
					return;
				}

				if (0 == data.count) {
					done();
					return;
				}

				var $overwriteModal = $('.file-exists-modal');

				if (!$overwriteModal.length) {
					done();
					return;
				}

				var $modalTemplate = $('.file-exists-modal').clone();
				$modalTemplate.appendTo('body');

				var $closeButton = $modalTemplate.find('.close-modal');
				var $overwriteButton = $modalTemplate.find('#overwrite-file');
				var $fileName = $modalTemplate.find('.file-exists-name');
				var $fileTxt = $modalTemplate.find('.file-exists-text');

				$modalTemplate.modal('show');
				$fileName.text('(' + file.name + ')');
				$fileTxt.html(data.msg);
				var doneSent = false;

				$closeButton.on('click', function (e) {
					doneSent = true;
					e.preventDefault();
					$modalTemplate.modal('hide');
					done('Refused');
				});

				$overwriteButton.on('click', function (e) {
					doneSent = true;
					e.preventDefault();
					$modalTemplate.modal('hide');
					done();
				});

				$modalTemplate.on('hidden.bs.modal', function () {
					$modalTemplate.remove();
					if (!doneSent){
						done('Refused');
					}
				})
			});
		}
	};

	function DamAssetSelectionModal(options) {
		this.$modal = options.$modal;
		this.$baseAjaxUrl = options.$baseAjaxUrl;
		this.$assetsAjaxUrl = this.$baseAjaxUrl + '&task=assets.filter';
		this.$overrideAjaxUrl = this.$baseAjaxUrl + '&task=assets.overrides';
		this.assetFieldGroup = options.assetFieldGroup;
		this.$assetFieldGroup = $(this.assetFieldGroup);
		this.$selectedAssets = options.$selectedAssets;
		this.selectedAssets = options.selectedAssets;
		this.$selectedSave = options.$selectedSave;
		this.$selectedRemove = options.$selectedRemove;
		this.$uploadButton = options.$uploadButton;
		this.$selectedAssetsCount = options.$selectedAssetsCount;
		this.selectedAssetsCount = options.selectedAssetsCount;
		this.$selectedAssetsControl = options.$selectedAssetsControl;
		this.selectedAssetsControl = options.selectedAssetsControl;
		this.$pagination = this.$modal.find('.thumbnails-pagination');
		this.$modalAssets = this.$modal.find('.thumbnails-wrapper');
		this.$modalOverrides = this.$modal.find('.selected-attribs-fields');
		this.$assetsCount = this.$modal.find('.assets-count');
		this.$thumbnails = this.$modal.find('.thumbnail');
		this.thumbnailSelectedClass = 'selected';
		this.holderJsClass = 'holderjs';

		this.bootpag = null;
		this.maxPaginationVisible = 10;
		this.currentAssetsRequest = null;
		this.currentOverridesRequest = null;
		this.modalLoaded = false;
		this.currentAsset = null;
		this.currentAssetName = null;
		this.formControlPrefix = 'jform_custom_fields_';
		this.defaultFilterValues = null;

		this.setup();
	}

	DamAssetSelectionModal.prototype.setup = function () {
		this.setupAssetsCount();
		this.setupModal();
		this.setupFilters();
	};

	DamAssetSelectionModal.prototype.findSibling = function (currentEl, findEl)
	{
		return this.findFieldGroup(currentEl).find(findEl);
	};

	DamAssetSelectionModal.prototype.findFieldGroup = function (currentEl)
	{
		return $(currentEl).closest(this.assetFieldGroup);
	};

	DamAssetSelectionModal.prototype.setupModal = function () {
		var self = this;
		self.defaultFilterValues = self.collectFilterValues();

		this.$modal.on('show.bs.modal', function (e) {
			self.$selectedAssets = self.findSibling(e.relatedTarget, self.selectedAssets);
			var showSelected = $(e.relatedTarget).data('show-selected');
			var $buttons = $('.asset-modal-buttons, .asset-upload');

			if (self.$selectedAssets.prop('disabled') || self.$selectedAssets.prop('hidden')){
				$buttons.hide();
			}else{
				$buttons.show();
			}

			// Reset all filters to default
            self.$modal.find("[name^='filter']").each(function () {
				var $filter = $(this);
				if ($filter.val() !== self.defaultFilterValues[$filter.attr('name')]){
					$filter.val(self.defaultFilterValues[$filter.attr('name')]);
					if ($filter.is("select")) {
						$filter.trigger("chosen:updated")
							.trigger("liszt:updated");
					}
				}
			});

			var $selectedFilter = self.$modal.find("[name^='filter[selected]']");
			if ($selectedFilter) {
				$selectedFilter.val(showSelected);
				$selectedFilter.trigger("chosen:updated")
					.trigger("liszt:updated");
			}

			var filters = self.collectFilterValues();
			self.updateThumbnails(filters);
			self.updateCurrentAssetFields();
			self.updateExtraFields();
		});

		this.$selectedSave.on('click', function(){
            var selectedAssets = [];
			if (self.$selectedAssets.data('multiple')) {
			    selectedAssets = JSON.parse(self.$selectedAssets.val());
			}

			if (!self.currentAsset) {
				return;
			}

			var assetId = self.currentAsset;

			var currentIndex = self.findAsset(assetId);

			var assetData = {
				id: assetId,
				name: self.currentAssetName
			};

			self.$modalOverrides.find(':input').each(function(){
				var CustomfieldName = this.id.substring(self.formControlPrefix.length);
				assetData[CustomfieldName] = $(this).val();
			});

			if (currentIndex == -1) {
				selectedAssets.push(assetData);
			}
			else {
				selectedAssets[currentIndex] = assetData;
			}

			self.$selectedAssets.val(JSON.stringify(selectedAssets));
			self.$selectedAssets.trigger('change');

			self.$modal.modal('hide');
			self.currentAsset = null;
		});

		this.$selectedRemove.on('click', function(){
			if (!self.currentAsset) {
				return;
			}

			self.removeAsset(self.findFieldGroup(self.$selectedAssets), self.currentAsset);
			self.$modal.modal('hide');
			self.currentAsset = null;
		});

		$('.switchButtonDAM').on('click', function(){
			$('.uploadSwitch').toggleClass('hide');

			if (!$('.selectDAM').hasClass('hide')){
				self.updateThumbnails(self.collectFilterValues());
			}
		});
	};

	DamAssetSelectionModal.prototype.updateExtraFields = function () {
		var self = this;

		self.$modalOverrides.find(':input').each(function(){
			$(this).val('');
		});

		var currentIndex = self.findAsset(self.currentAsset);

		if (currentIndex > -1) {
			var selectedAssets = JSON.parse(self.$selectedAssets.val());
			var assetData = selectedAssets[currentIndex];

			self.$modalOverrides.find(':input').each(function(){
				var customfieldName = this.id.substring(self.formControlPrefix.length);

				if (assetData.hasOwnProperty(customfieldName)) {
					$(this).val(assetData[customfieldName]);
				}
			});
		}
	};

	DamAssetSelectionModal.prototype.updateSelectedList = function ($assetFieldGroup) {
		var self = this;
		var $selectedAssets = $assetFieldGroup.find(this.selectedAssets);
		var disabled = ($selectedAssets.prop('disabled') || $selectedAssets.prop('hidden'));
		var selectedAssets = JSON.parse($selectedAssets.val());
		var assetControlList = $assetFieldGroup.find(this.selectedAssetsControl);

		assetControlList.empty();
		selectedAssets.forEach(function(asset){
			var row = $("<span class='selected-asset-control' data-toggle='popover' data-trigger='hover'></span><br/>");
			var spanSelected = '<span class="edit-selected"'
				+ (!disabled ? ' data-toggle="modal" data-target="#select-assets-modal" data-show-selected="1"' : '')
				+ '><span class="name-selected">' + asset.name + '</span>'
				+ (!disabled ? '<i class="fa fa-edit"></i>' : '')
				+ '</span>';

			var $oneItem = $(spanSelected);
			var options = {
				html: true,
				content: function () {
					var popover = $(this);
					if (popover.data('content')){
						return  popover.data('content');
					}
					popover.data('content', 'Loading...');
					$.ajax({
						type: "POST",
						url: self.$assetsAjaxUrl,
						data: {
							filter : {
								selected : 1,
								id: [asset.id]
							},
							list: {
								overrides: 1
							}
						},
						success: function (data) {
							var content = 'No content loaded!';
							if (data.items[0].thumbnail){
								content = data.items[0].thumbnail;
								if (data.items[0].overrides){
									var $overrides = $(data.items[0].overrides);
									$overrides.find(':input').each(function(){
										var customfieldName = this.id.substring(self.formControlPrefix.length);
										if (asset.hasOwnProperty(customfieldName) && asset[customfieldName] !== '') {
											$(this).attr('value', asset[customfieldName]);
										}else{
											// Do not show empty fields
											$(this).closest('.js-aesir-field').remove();
										}
									});
									$overrides.find('.limit_counter').addClass('hide');
									content = $(content).append($overrides).html();
								}
							}
							popover.data('content', content);
							if (popover.attr('aria-describedby')){
								popover.popover('show')
							}
						},
						dataType: 'json'
					});
					return popover.data('content');
				}
			};
			if (!disabled){
				$oneItem.click(function(){
					self.currentAsset = asset.id;
					self.currentAssetName = asset.name;
				}).appendTo(row);
				$('<span class="remove-selected"><i class="fa fa-trash"></i></span>').click(function(){
					self.removeAsset(self.findFieldGroup(this), asset.id)
				}).appendTo(row);
			}else{
				$oneItem.appendTo(row);
			}
			row.popover(options);
			$(row).on('shown.bs.popover', function () {
				var popover = $(this);
				var holderImage = document.getElementsByClassName(self.holderJsClass);
				if (holderImage.length){
					// Run holder to process the thumbnails placeholders
					Holder.run({
						images: holderImage
					});

					var changed = popover.data('bs.popover');
					var calculatedOffset = changed.getCalculatedOffset(
						changed.options.placement, changed.getPosition(), changed.$tip[0].offsetWidth, changed.$tip[0].offsetHeight
					);
					changed.applyPlacement(calculatedOffset, changed.options.placement);
				}
			});
			row.appendTo(assetControlList);
		});
	};

	DamAssetSelectionModal.prototype.removeAsset = function ($assetFieldGroup, assetId) {
		var self = this;
		var $selectedAssets = $assetFieldGroup.find(self.selectedAssets);
		var selectedAssets = JSON.parse($selectedAssets.val());
		var index = self.findAssetByFieldGroup($assetFieldGroup, assetId);

		if (index > -1) {
			selectedAssets.splice(index, 1);

			$selectedAssets.val(JSON.stringify(selectedAssets));
			$selectedAssets.trigger('change');
		}
	};

	DamAssetSelectionModal.prototype.setupAssetsCount = function () {
		var self = this;
		this.$assetFieldGroup.each(function(){
			var $this = $(this);
			$this.find(self.selectedAssetsCount).html(self.getSelectedAssetsCount($this));
			$this.find(self.selectedAssets).on('change', function () {
				$this.find(self.selectedAssetsCount)
					.html(self.getSelectedAssetsCount($this));
				self.updateSelectedList($this);
			});
			self.updateSelectedList($this);
		});
	};

	DamAssetSelectionModal.prototype.collectFilterValues = function () {
		var filters = {};

        this.$modal.find("[name^='filter']").each(function () {
			var $filter = $(this);

			filters[$filter.attr('name')] = $filter.val();
		});

		// Process special filters
		if (filters.hasOwnProperty('filter[selected]') && 1 == filters['filter[selected]']) {
			filters['filter[id]'] = this.getSelectedAssets().map(function(element) { return element.id; });
		}

		return filters;
	};

	DamAssetSelectionModal.prototype.setupFilters = function () {
		var self = this;

        self.$modal.find("input[name^='filter']").on('input', function () {
			self.updateThumbnails(self.collectFilterValues());
		});

        self.$modal.find("select[name^='filter']").on('change', function () {
			self.updateThumbnails(self.collectFilterValues());
		});
	};

	DamAssetSelectionModal.prototype.updateThumbnails = function (params) {
		var self = this;
		// Hack needed for freezed scrolling
		self.$modalAssets.css('height', self.$modalAssets.height() + 'px');
		self.$modalAssets.html('');
		self.$modalAssets.spin({scale: 0.7, width: 3});

		if (self.currentAssetsRequest) {
			self.currentAssetsRequest.abort();
		}

		self.currentAssetsRequest = $.post(
			self.$assetsAjaxUrl,
			params || {}
		).done(function (data) {
			self.$modalAssets.spin(false);

			data = JSON.parse(data);
			self.applyThumbnailsToDom(data);

			if (!self.bootpag) {
				self.bootpag = self.$pagination.bootpag({
					total: data.pages,
					page: data.page,
					maxVisible: self.maxPaginationVisible,
					leaps: true,
					firstLastUse: true
				}).on('page', function (event, num) {
					var params = self.collectFilterValues();
					params.page = num;

					self.updateThumbnails(params);
				});
			} else {
				self.$pagination.bootpag({
					total: data.pages,
					page: data.page
				});
			}
		}).always(function () {
			self.$modalAssets.css('height', 'auto');
		});
	};

	DamAssetSelectionModal.prototype.applyThumbnailsToDom = function (data) {
		var self = this;

		if (!data.items.length) {
			self.$modalAssets.html('');
			return;
		}

		// Sets the count in the modal header
		self.$assetsCount.html(numberWithCommas(data.count));

		// Append each thumbnail to the assets
		var items = data.items;
		var content = '';

		for (var i = 0; i < items.length; i++) {
			content += items[i].thumbnail;
		}

		self.$modalAssets.html(content);

		// Refresh the thumbnails selector after DOM update
		this.$thumbnails = self.$modal.find('.thumbnail');

		// Wrap all thumbnails to make a grid
		this.$thumbnails.wrap('<div class="col-xs-12 col-md-4 col-lg-3 thumbnail-wrapper"></div>');

		// Automatic rows depending on screen size
        self.$modal.find('.thumbnails-wrapper > .thumbnail-wrapper:nth-child(4)').after('<div class="clearfix visible-lg"></div>');
        self.$modal.find('.thumbnails-wrapper > .thumbnail-wrapper:nth-child(3)').after('<div class="clearfix visible-md"></div>');

		self.setupThumbnails();
	};

	DamAssetSelectionModal.prototype.highlightSelected = function () {
		var self = this;

		self.$thumbnails.removeClass(self.thumbnailSelectedClass);

		// Add the selected class to selected asset
		self.$thumbnails.each(function () {
			var $thumbnail = $(this);

			if (isAssetSelected($thumbnail)) {
				$thumbnail.addClass(self.thumbnailSelectedClass)
			}
		});

		function isAssetSelected($thumbnail) {
			var assetId = $thumbnail.data('id');

			return assetId == self.currentAsset;
		}
	};

	DamAssetSelectionModal.prototype.setupThumbnails = function () {
		var self = this;

		self.highlightSelected();

		// Run holder to process the thumbnails placeholders
		Holder.run({
			images: document.getElementsByClassName(this.holderJsClass)
		});

		// Handle asset selection / deselection
		this.$thumbnails.on('click', function selectAsset() {
			var $thumbnail = $(this);
			var assetId = $thumbnail.data('id');

			self.$thumbnails.removeClass(self.thumbnailSelectedClass);
			$thumbnail.addClass(self.thumbnailSelectedClass);

			self.currentAsset = assetId;
			self.currentAssetName = $thumbnail.data('name');

			self.updateCurrentAssetFields();
		});
	};

	DamAssetSelectionModal.prototype.updateCurrentAssetFields = function () {
		var self = this;

		var assetId = self.currentAsset;

		self.$modalOverrides.html('');
		self.$modalOverrides.spin({scale: 0.7, width: 3});

		if (self.currentOverridesRequest) {
			self.currentOverridesRequest.abort();
		}

		self.currentOverridesRequest = $.post(
			self.$overrideAjaxUrl,
			{id: assetId}
		).done(function (data) {
			data = JSON.parse(data);

			self.$modalOverrides.spin(false);
			self.$modalOverrides.html(data);

			var selectedAssets = JSON.parse(self.$selectedAssets.val());
			var currentIndex = self.findAsset(assetId);

			if (currentIndex > -1) {
				var assetData = selectedAssets[currentIndex];

				self.$modalOverrides.find(':input').each(function(){
					var customfieldName = this.id.substring(self.formControlPrefix.length);

					if (assetData.hasOwnProperty(customfieldName)) {
						$(this).val(assetData[customfieldName]);
					}
				});
			}
		});
	};

	DamAssetSelectionModal.prototype.findAssetByFieldGroup = function ($assetFieldGroup, id) {
		var selectedAssets = JSON.parse($assetFieldGroup.find(this.selectedAssets).val());

		return selectedAssets.reduce(function(ind, asset, currentIndex){
			if (asset.id == id) {
				return currentIndex;
			}

			return ind;
		}, -1);
	};

	DamAssetSelectionModal.prototype.findAsset = function (id) {
		var selectedAssets = JSON.parse(this.$selectedAssets.val());

		return selectedAssets.reduce(function(ind, asset, currentIndex){
			if (asset.id == id) {
				return currentIndex;
			}

			return ind;
		}, -1);
	};

	DamAssetSelectionModal.prototype.getSelectedAssetsCount = function ($currentAssetFieldGroup) {
		var selectedAssets = JSON.parse($currentAssetFieldGroup.find(this.selectedAssets).val());

		return selectedAssets ? selectedAssets.length : 0;
	};

	DamAssetSelectionModal.prototype.getSelectedAssets = function () {
		return JSON.parse(this.$selectedAssets.val());
	};

	function numberWithCommas(x) {
		return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
	}

	$(function(){
		$('[data-toggle="asset-modal"]').click(function(e){
			e.preventDefault();
			var target = $(this).data('target');
			$(target).modal('show');
		});
	});

	window.DamAssetSelectionModal = DamAssetSelectionModal;

})(jQuery);
