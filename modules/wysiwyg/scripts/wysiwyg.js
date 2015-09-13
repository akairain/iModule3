/*
	Redactor v10.1.1
	Updated: April 28, 2015

	http://imperavi.com/redactor/

	Copyright (c) 2009-2015, Imperavi LLC.
	License: http://imperavi.com/redactor/license/

	Usage: $('#content').redactor();
*/

(function($) {
	'use strict';

	if (!Function.prototype.bind) {
		Function.prototype.bind = function(scope) {
			var fn = this;
			return function() {
				return fn.apply(scope);
			};
		};
	}

	var uuid = 0;

	// Plugin
	$.fn.redactor = function(options) {
		var val = [];
		var args = Array.prototype.slice.call(arguments, 1);

		if (typeof options === 'string') {
			this.each(function() {
				var instance = $.data(this, 'redactor');
				var func;

				if (options.search(/\./) != '-1') {
					func = options.split('.');
					if (typeof instance[func[0]] != 'undefined') {
						func = instance[func[0]][func[1]];
					}
				}
				else {
					func = instance[options];
				}

				if (typeof instance !== 'undefined' && $.isFunction(func)) {
					var methodVal = func.apply(instance, args);
					if (methodVal !== undefined && methodVal !== instance) {
						val.push(methodVal);
					}
				}
				else {
					$.error('No such method "' + options + '" for Redactor');
				}
			});
		}
		else {
			this.each(function() {
				$.data(this, 'redactor', {});
				$.data(this, 'redactor', Redactor(this, options));
			});
		}

		if (val.length === 0) return this;
		else if (val.length === 1) return val[0];
		else return val;

	};

	// Initialization
	function Redactor(el, options) {
		return new Redactor.prototype.init(el, options);
	}

	// Functionality
	$.Redactor = Redactor;
	$.Redactor.VERSION = '10.1.1';
	$.Redactor.modules = ['alignment', 'block', 'buffer', 'build', 'button',
						  'caret', 'clean', 'code', 'core', 'dropdown', 'file', 'focus',
						  'image', 'indent', 'inline', 'insert', 'keydown', 'keyup',
						  'lang', 'line', 'link', 'list', 'observe', 'paragraphize',
						  'paste', 'placeholder', 'selection', 'shortcuts',
						  'tabifier', 'tidy',  'toolbar', 'upload', 'utils', 'linkify', 'fontcolor', 'video', 'insertcode'];

	$.Redactor.opts = {
		// settings
		lang: 'en',
		direction: 'ltr', // ltr or rtl

		plugins: false, // array

		focus: false,
		focusEnd: false,

		placeholder: false,

		visual: true,
		tabindex: false,

		minHeight: false,
		maxHeight: false,

		linebreaks: false,
		replaceDivs: true,
		paragraphize: true,
		cleanStyleOnEnter: false,
		enterKey: true,

		cleanOnPaste: true,
		cleanSpaces: true,
		pastePlainText: true,
		
		linkTooltip: true,
		linkProtocol: 'http',
		linkNofollow: false,
		linkSize: 50,

		imageEditable: true,
		imagePosition: true,
		imageFloatMargin: '10px',
		imageResizable: true,

		dragImageUpload: true,
		dragFileUpload: true,

		convertLinks: true,
		convertUrlLinks: true,
		convertImageLinks: true,
		convertVideoLinks: true,

		preSpaces: 4, // or false
		tabAsSpaces: true, // true or number of spaces
		tabKey: true,

		scrollTarget: false,

		toolbar: true,
		toolbarFixed: true,
		toolbarExternal: false, // ID selector
		toolbarOverflow: false,

		source: true,
		buttons: ['html', 'formatting', 'bold', 'italic', 'underline', 'deleted', 'fontcolor', 'backcolor', 'alignment', 'unorderedlist', 'orderedlist',
				  'outdent', 'indent', 'insertcode', 'video', 'image', 'file', 'link', 'horizontalrule'], // + 'underline'

		buttonsHide: [],
		
		formatting: ['p', 'blockquote', 'pre', 'h1', 'h2', 'h3', 'h4', 'h5', 'h6'],
		formattingAdd: false,/*[{
			tag: 'span',
			title: 'P Attr Title',
			class: 'boxDefault',
			clear: true
		}],*/

		tabifier: true,

		deniedTags: ['script', 'style', 'table', 'button', 'input', 'form'],
		allowedTags: false, // or array

		paragraphizeBlocks: ['div', 'pre', 'form', 'ul', 'ol', 'h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'dl', 'blockquote', 'figcaption',
							'address', 'section', 'header', 'footer', 'aside', 'article', 'object', 'style', 'script', 'iframe', 'select', 'input', 'textarea',
							'button', 'option', 'map', 'area', 'math', 'hr', 'fieldset', 'legend', 'hgroup', 'nav', 'figure', 'details', 'menu', 'summary', 'p'],

		removeComments: true,
		replaceTags: [
			['strike', 'del']
		],
		replaceStyles: [
			['font-weight:\\s?bold', "strong"],
			['font-style:\\s?italic', "em"],
			['text-decoration:\\s?underline', "u"],
			['text-decoration:\\s?line-through', 'del']
		],
		removeDataAttr: false,

		removeAttr: false, // or multi array
		allowedAttr: false, // or multi array

		removeWithoutAttr: ['span'], // or false
		removeEmpty: ['div'], // or false;

		activeButtons: ['deleted', 'italic', 'bold', 'underline', 'unorderedlist', 'orderedlist',
						'alignleft', 'aligncenter', 'alignright', 'alignjustify'],
		activeButtonsStates: {
			b: 'bold',
			strong: 'bold',
			i: 'italic',
			em: 'italic',
			del: 'deleted',
			strike: 'deleted',
			ul: 'unorderedlist',
			ol: 'orderedlist',
			u: 'underline'
		},

		shortcuts: {
			'ctrl+shift+m, meta+shift+m': { func: 'inline.removeFormat' },
			'ctrl+b, meta+b': { func: 'inline.format', params: ['bold'] },
			'ctrl+i, meta+i': { func: 'inline.format', params: ['italic'] },
			'ctrl+h, meta+h': { func: 'inline.format', params: ['superscript'] },
			'ctrl+l, meta+l': { func: 'inline.format', params: ['subscript'] },
			'ctrl+k, meta+k': { func: 'link.show' },
			'ctrl+shift+7':   { func: 'list.toggle', params: ['orderedlist'] },
			'ctrl+shift+8':   { func: 'list.toggle', params: ['unorderedlist'] }
		},
		shortcutsAdd: false,

		// private
		buffer: [],
		rebuffer: [],
		emptyHtml: '<p>&#x200b;</p>',
		invisibleSpace: '&#x200b;',
		imageTypes: ['image/png', 'image/jpeg', 'image/gif'],
		indentValue: 20,
		verifiedTags: 		['a', 'img', 'b', 'strong', 'sub', 'sup', 'i', 'em', 'u', 'small', 'strike', 'del', 'cite', 'ul', 'ol', 'li'], // and for span tag special rule
		inlineTags: 		['strong', 'b', 'u', 'em', 'i', 'code', 'del', 'ins', 'samp', 'kbd', 'sup', 'sub', 'mark', 'var', 'cite', 'small'],
		alignmentTags: 		['P', 'H1', 'H2', 'H3', 'H4', 'H5', 'H6',  'DL', 'DT', 'DD', 'DIV', 'TD', 'BLOCKQUOTE', 'OUTPUT', 'FIGCAPTION', 'ADDRESS', 'SECTION', 'HEADER', 'FOOTER', 'ASIDE', 'ARTICLE'],
		blockLevelElements: ['PRE', 'UL', 'OL', 'LI'],

		focusCallback:function(e) {
			this.$box.parents(".inputBlock, .inputInline").addClass("hasFocus");
		},
		
		blurCallback:function(e) {
			if (this.$textarea.attr("data-required") == "required" && this.$textarea.val().length == 0) {
				this.$box.parents(".inputBlock, .inputInline").addClass("hasError");
			} else {
				this.$box.parents(".inputBlock, .inputInline").removeClass("hasError");
			}
			this.$box.parents(".inputBlock, .inputInline").removeClass("hasFocus");
		},

		// lang
		langs: {
			en: {
				html: 'HTML',
				video: 'Insert Video',
				image: 'Insert Image',
				link: 'Link',
				link_insert: 'Insert link',
				link_edit: 'Edit link',
				unlink: 'Unlink',
				formatting: 'Formatting',
				paragraph: 'Normal text',
				quote: 'Quote',
				code: 'Code',
				insert_code: 'Insert Code',
				/*header1: 'Header 1',
				header2: 'Header 2',
				header3: 'Header 3',
				header4: 'Header 4',*/
				header5: 'Title',
				bold: 'Bold',
				italic: 'Italic',
				fontcolor: 'Font Color',
				backcolor: 'Back Color',
				unorderedlist: 'Unordered List',
				orderedlist: 'Ordered List',
				outdent: 'Outdent',
				indent: 'Indent',
				cancel: 'Cancel',
				insert: 'Insert',
				save: 'Save',
				_delete: 'Delete',
				title: 'Title',
				image_position: 'Position',
				none: 'None',
				left: 'Left',
				right: 'Right',
				center: 'Center',
				image_web_link: 'Image Web Link',
				text: 'Text',
				mailto: 'Email',
				web: 'URL',
				video_html_code: 'Video Embed Code or Youtube/Vimeo Link',
				insert_code_text: 'Insert Code',
				insert_code_language: 'Code Language',
				file: 'Insert File',
				align_left: 'Align text to the left',
				align_center: 'Center text',
				align_right: 'Align text to the right',
				align_justify: 'Justify text',
				horizontalrule: 'Insert Horizontal Rule',
				deleted: 'Deleted',
				anchor: 'Anchor',
				underline: 'Underline',
				alignment: 'Alignment',
				edit: 'Edit'

			}
		},

		linkify: {
			regexps: {
				youtube: /https?:\/\/(?:[0-9A-Z-]+\.)?(?:youtu\.be\/|youtube\.com\S*[^\w\-\s])([\w\-]{11})(?=[^\w\-]|$)(?![?=&+%\w.\-]*(?:['"][^<>]*>|<\/a>))[?=&+%\w.-]*/ig,
				vimeo: /https?:\/\/(www\.)?vimeo.com\/(\d+)($|\/)/,
				image: /((https?|www)[^\s]+\.)(jpe?g|png|gif)(\?[^\s-]+)?/ig,
				url: /(https?:\/\/(?:www\.|(?!www))[^\s\.]+\.[^\s]{2,}|www\.[^\s]+\.[^\s]{2,})/ig
			}
		},

		codemirror: false
	};

	// Functionality
	Redactor.fn = $.Redactor.prototype = {

		keyCode: {
			BACKSPACE: 8,
			DELETE: 46,
			UP: 38,
			DOWN: 40,
			ENTER: 13,
			SPACE: 32,
			ESC: 27,
			TAB: 9,
			CTRL: 17,
			META: 91,
			SHIFT: 16,
			ALT: 18,
			RIGHT: 39,
			LEFT: 37,
			LEFT_WIN: 91
		},

		// Initialization
		init: function(el, options) {
			this.$element = $(el);
			this.uuid = uuid++;

			// if paste event detected = true
			this.rtePaste = false;
			this.$pasteBox = false;

			this.loadModules();
			if (this.utils.isMobile()) options.minHeight = 100;
			this.loadOptions(options);
			
			
			

			// formatting storage
			this.formatting = {};

			// block level tags
			$.merge(this.opts.blockLevelElements, this.opts.alignmentTags);
			this.reIsBlock = new RegExp('^(' + this.opts.blockLevelElements.join('|' ) + ')$', 'i');

			// setup allowed and denied tags
			this.tidy.setupAllowed();

			// setup denied tags
			if (this.opts.deniedTags !== false) {
				var tags = ['html', 'head', 'link', 'body', 'meta', 'applet'];
				for (var i = 0; i < tags.length; i++) {
					this.opts.deniedTags.push(tags[i]);
				}
			}

			// load lang
			this.lang.load();

			// extend shortcuts
			$.extend(this.opts.shortcuts, this.opts.shortcutsAdd);

			// start callback
			this.core.setCallback('start');

			// build
			this.start = true;
			this.build.run();
			
			if (this.utils.isDesktop()) {
				$(window).on('resize',$.proxy(this.toolbar.observeScroll,this));
			}
		},

		loadOptions: function(options) {
			this.opts = $.extend( {},
				$.extend(true, {}, $.Redactor.opts),
				this.$element.data(),
				options
			);
		},
		getModuleMethods: function(object) {
			return Object.getOwnPropertyNames(object).filter(function(property) {
				return typeof object[property] == 'function';
			});
		},
		loadModules: function() {
			var len = $.Redactor.modules.length;
			for (var i = 0; i < len; i++) {
				this.bindModuleMethods($.Redactor.modules[i]);
			}
		},
		bindModuleMethods: function(module) {
			if (typeof this[module] == 'undefined') return;

			// init module
			this[module] = this[module]();

			var methods = this.getModuleMethods(this[module]);
			var len = methods.length;

			// bind methods
			for (var z = 0; z < len; z++) {
				this[module][methods[z]] = this[module][methods[z]].bind(this);
			}
		},

		alignment: function() {
			return {
				left: function() {
					this.alignment.set('');
				},
				right: function() {
					this.alignment.set('right');
				},
				center: function() {
					this.alignment.set('center');
				},
				justify: function() {
					this.alignment.set('justify');
				},
				set: function(type) {
					// focus
					if (!this.utils.browser('msie')) this.$editor.focus();

					this.buffer.set();
					this.selection.save();

					// get blocks
					this.alignment.blocks = this.selection.getBlocks();
					this.alignment.type = type;

					// set alignment
					if (this.alignment.isLinebreaksOrNoBlocks()) {
						this.alignment.setText();
					}
					else {
						this.alignment.setBlocks();
					}

					// sync
					this.selection.restore();
					this.code.sync();
				},
				setText: function() {
					var wrapper = this.selection.wrap('div');
					$(wrapper).attr('data-tagblock', 'redactor').css('text-align', this.alignment.type);
				},
				setBlocks: function() {
					$.each(this.alignment.blocks, $.proxy(function(i, el) {
						var $el = this.utils.getAlignmentElement(el);
						if (!$el) return;

						if (this.alignment.isNeedReplaceElement($el)) {
							this.alignment.replaceElement($el);
						}
						else {
							this.alignment.alignElement($el);
						}

					}, this));
				},
				isLinebreaksOrNoBlocks: function() {
					return (this.opts.linebreaks && this.alignment.blocks[0] === false);
				},
				isNeedReplaceElement: function($el) {
					return (this.alignment.type === '' && typeof($el.data('tagblock')) !== 'undefined');
				},
				replaceElement: function($el) {
					$el.replaceWith($el.html());
				},
				alignElement: function($el) {
					$el.css('text-align', this.alignment.type);
					this.utils.removeEmptyAttr($el, 'style');
				}
			};
		},
		block: function() {
			return {
				formatting: function(name) {
					this.block.clearStyle = false;
					var type, value;

					if (typeof this.formatting[name].data != 'undefined') type = 'data';
					else if (typeof this.formatting[name].attr != 'undefined') type = 'attr';
					else if (typeof this.formatting[name]['class'] != 'undefined') type = 'class';

					if (typeof this.formatting[name].clear != 'undefined') {
						this.block.clearStyle = true;
					}

					if (type) value = this.formatting[name][type];

					this.block.format(this.formatting[name].tag, type, value);

				},
				format: function(tag, type, value) {
					if (tag == 'quote') tag = 'blockquote';

					var formatTags = ['p', 'pre', 'blockquote', 'h1', 'h2', 'h3', 'h4', 'h5', 'h6'];
					if ($.inArray(tag, formatTags) == -1) return;

					this.block.isRemoveInline = (tag == 'pre' || tag.search(/h[1-6]/i) != -1);

					// focus
					if (!this.utils.browser('msie')) this.$editor.focus();

					this.block.blocks = this.selection.getBlocks();

					this.block.blocksSize = this.block.blocks.length;
					this.block.type = type;
					this.block.value = value;

					this.buffer.set();
					this.selection.save();

					this.block.set(tag);

					this.selection.restore();
					this.code.sync();

				},
				set: function(tag) {
					this.selection.get();
					this.block.containerTag = this.range.commonAncestorContainer.tagName;

					if (this.range.collapsed) {
						this.block.setCollapsed(tag);
					}
					else {
						this.block.setMultiple(tag);
					}
				},
				setCollapsed: function(tag) {
					var block = this.block.blocks[0];
					if (block === false) return;

					if (block.tagName == 'LI') {
						if (tag != 'blockquote') return;

						this.block.formatListToBlockquote();
						return;
					}

					var isContainerTable = (this.block.containerTag  == 'TD' || this.block.containerTag  == 'TH');
					if (isContainerTable && !this.opts.linebreaks) {

						document.execCommand('formatblock', false, '<' + tag + '>');

						block = this.selection.getBlock();
						this.block.toggle($(block));

					}
					else if (block.tagName.toLowerCase() != tag) {
						if (this.opts.linebreaks && tag == 'p') {
							$(block).prepend('<br>').append('<br>');
							this.utils.replaceWithContents(block);
						}
						else {
							var $formatted = this.utils.replaceToTag(block, tag);

							this.block.toggle($formatted);

							if (tag != 'p' && tag != 'blockquote') $formatted.find('img').remove();
							if (this.block.isRemoveInline) this.utils.removeInlineTags($formatted);
							if (tag == 'p' || this.block.headTag) $formatted.find('p').contents().unwrap();

							this.block.formatTableWrapping($formatted);
						}
					}
					else if (tag == 'blockquote' && block.tagName.toLowerCase() == tag) {
						// blockquote off
						if (this.opts.linebreaks) {
							$(block).prepend('<br>').append('<br>');
							this.utils.replaceWithContents(block);
						}
						else {
							var $el = this.utils.replaceToTag(block, 'p');
							this.block.toggle($el);
						}
					}
					else if (block.tagName.toLowerCase() == tag) {
						this.block.toggle($(block));
					}

					if (typeof this.block.type == 'undefined' && typeof this.block.value == 'undefined') {
						$(block).removeAttr('class').removeAttr('style');
					}

				},
				setMultiple: function(tag) {
					var block = this.block.blocks[0];
					var isContainerTable = (this.block.containerTag  == 'TD' || this.block.containerTag  == 'TH');

					if (block !== false && this.block.blocksSize === 1) {
						if (block.tagName.toLowerCase() == tag &&  tag == 'blockquote') {
							// blockquote off
							if (this.opts.linebreaks) {
								$(block).prepend('<br>').append('<br>');
								this.utils.replaceWithContents(block);
							}
							else {
								var $el = this.utils.replaceToTag(block, 'p');
								this.block.toggle($el);
							}
						}
						else if (block.tagName == 'LI') {
							if (tag != 'blockquote') return;

							this.block.formatListToBlockquote();
						}
						else if (this.block.containerTag == 'BLOCKQUOTE') {
							this.block.formatBlockquote(tag);
						}
						else if (this.opts.linebreaks && ((isContainerTable) || (this.range.commonAncestorContainer != block))) {
							this.block.formatWrap(tag);
						}
						else {
							if (this.opts.linebreaks && tag == 'p') {
								$(block).prepend('<br>').append('<br>');
								this.utils.replaceWithContents(block);
							}
							else if (block.tagName === 'TD') {
								this.block.formatWrap(tag);
							}
							else {
								var $formatted = this.utils.replaceToTag(block, tag);

								this.block.toggle($formatted);

								if (this.block.isRemoveInline) this.utils.removeInlineTags($formatted);
								if (tag == 'p' || this.block.headTag) $formatted.find('p').contents().unwrap();
							}
						}
					}
					else {

						if (this.opts.linebreaks || tag != 'p') {
							if (tag == 'blockquote') {
								var count = 0;
								for (var i = 0; i < this.block.blocksSize; i++) {
									if (this.block.blocks[i].tagName == 'BLOCKQUOTE') count++;
								}

								// only blockquote selected
								if (count == this.block.blocksSize) {
									$.each(this.block.blocks, $.proxy(function(i,s) {
										var $formatted = false;
										if (this.opts.linebreaks) {
											$(s).prepend('<br>').append('<br>');
											$formatted = this.utils.replaceWithContents(s);
										}
										else {
											$formatted = this.utils.replaceToTag(s, 'p');
										}

										if ($formatted && typeof this.block.type == 'undefined' && typeof this.block.value == 'undefined') {
											$formatted.removeAttr('class').removeAttr('style');
										}

									}, this));

									return;
								}

							}


							this.block.formatWrap(tag);
						}
						else {
							var classSize = 0;
							var toggleType = false;
							if (this.block.type == 'class') {
								toggleType = 'toggle';
								classSize = $(this.block.blocks).filter('.' + this.block.value).length;

								if (this.block.blocksSize == classSize) toggleType = 'toggle';
								else if (this.block.blocksSize > classSize) toggleType = 'set';
								else if (classSize === 0) toggleType = 'set';

							}

							var exceptTags = ['ul', 'ol', 'li', 'td', 'th', 'dl', 'dt', 'dd'];
							$.each(this.block.blocks, $.proxy(function(i,s) {
								if ($.inArray(s.tagName.toLowerCase(), exceptTags) != -1) return;

								var $formatted = this.utils.replaceToTag(s, tag);

								if (toggleType) {
									if (toggleType == 'toggle') this.block.toggle($formatted);
									else if (toggleType == 'remove') this.block.remove($formatted);
									else if (toggleType == 'set') this.block.setForce($formatted);
								}
								else this.block.toggle($formatted);

								if (tag != 'p' && tag != 'blockquote') $formatted.find('img').remove();
								if (this.block.isRemoveInline) this.utils.removeInlineTags($formatted);
								if (tag == 'p' || this.block.headTag) $formatted.find('p').contents().unwrap();

								if (typeof this.block.type == 'undefined' && typeof this.block.value == 'undefined') {
									$formatted.removeAttr('class').removeAttr('style');
								}


							}, this));
						}
					}
				},
				setForce: function($el) {
					// remove style and class if the specified setting
					if (this.block.clearStyle) {
						$el.removeAttr('class').removeAttr('style');
					}

					if (this.block.type == 'class') {
						$el.addClass(this.block.value);
						return;
					}
					else if (this.block.type == 'attr' || this.block.type == 'data') {
						$el.attr(this.block.value.name, this.block.value.value);
						return;
					}
				},
				toggle: function($el) {
					// remove style and class if the specified setting
					if (this.block.clearStyle) {
						$el.removeAttr('class').removeAttr('style');
					}

					if (this.block.type == 'class') {
						$el.toggleClass(this.block.value);
						return;
					}
					else if (this.block.type == 'attr' || this.block.type == 'data') {
						if ($el.attr(this.block.value.name) == this.block.value.value) {
							$el.removeAttr(this.block.value.name);
						}
						else {
							$el.attr(this.block.value.name, this.block.value.value);
						}

						return;
					}
					else {
						$el.removeAttr('style class');
						return;
					}
				},
				remove: function($el) {
					$el.removeClass(this.block.value);
				},
				formatListToBlockquote: function() {
					var block = $(this.block.blocks[0]).closest('ul, ol', this.$editor[0]);

					$(block).find('ul, ol').contents().unwrap();
					$(block).find('li').append($('<br>')).contents().unwrap();

					var $el = this.utils.replaceToTag(block, 'blockquote');
					this.block.toggle($el);
				},
				formatBlockquote: function(tag) {
					document.execCommand('outdent');
					document.execCommand('formatblock', false, tag);

					this.clean.clearUnverified();
					this.$editor.find('p:empty').remove();

					var formatted = this.selection.getBlock();

					if (tag != 'p') {
						$(formatted).find('img').remove();
					}

					if (!this.opts.linebreaks) {
						this.block.toggle($(formatted));
					}

					this.$editor.find('ul, ol, tr, blockquote, p').each($.proxy(this.utils.removeEmpty, this));

					if (this.opts.linebreaks && tag == 'p') {
						this.utils.replaceWithContents(formatted);
					}

				},
				formatWrap: function(tag) {
					if (this.block.containerTag == 'UL' || this.block.containerTag == 'OL') {
						if (tag == 'blockquote') {
							this.block.formatListToBlockquote();
						}
						else {
							return;
						}
					}

					var formatted = this.selection.wrap(tag);
					if (formatted === false) return;

					var $formatted = $(formatted);

					this.block.formatTableWrapping($formatted);

					var $elements = $formatted.find(this.opts.blockLevelElements.join(',') + ', td, table, thead, tbody, tfoot, th, tr');

					if ((this.opts.linebreaks && tag == 'p') || tag == 'pre' || tag == 'blockquote') {
						$elements.append('<br />');
					}

					$elements.contents().unwrap();

					if (tag != 'p' && tag != 'blockquote') $formatted.find('img').remove();

					$.each(this.block.blocks, $.proxy(this.utils.removeEmpty, this));

					$formatted.append(this.selection.getMarker(2));

					if (!this.opts.linebreaks) {
						this.block.toggle($formatted);
					}

					this.$editor.find('ul, ol, tr, blockquote, p').each($.proxy(this.utils.removeEmpty, this));
					$formatted.find('blockquote:empty').remove();

					if (this.block.isRemoveInline) {
						this.utils.removeInlineTags($formatted);
					}

					if (this.opts.linebreaks && tag == 'p') {
						this.utils.replaceWithContents($formatted);
					}

				},
				formatTableWrapping: function($formatted) {
					if ($formatted.closest('table', this.$editor[0]).length === 0) return;

					if ($formatted.closest('tr', this.$editor[0]).length === 0) $formatted.wrap('<tr>');
					if ($formatted.closest('td', this.$editor[0]).length === 0 && $formatted.closest('th').length === 0) {
						$formatted.wrap('<td>');
					}
				},
				removeData: function(name, value) {
					var blocks = this.selection.getBlocks();
					$(blocks).removeAttr('data-' + name);

					this.code.sync();
				},
				setData: function(name, value) {
					var blocks = this.selection.getBlocks();
					$(blocks).attr('data-' + name, value);

					this.code.sync();
				},
				toggleData: function(name, value) {
					var blocks = this.selection.getBlocks();
					$.each(blocks, function() {
						if ($(this).attr('data-' + name)) {
							$(this).removeAttr('data-' + name);
						}
						else {
							$(this).attr('data-' + name, value);
						}
					});
				},
				removeAttr: function(attr, value) {
					var blocks = this.selection.getBlocks();
					$(blocks).removeAttr(attr);

					this.code.sync();
				},
				setAttr: function(attr, value) {
					var blocks = this.selection.getBlocks();
					$(blocks).attr(attr, value);

					this.code.sync();
				},
				toggleAttr: function(attr, value) {
					var blocks = this.selection.getBlocks();
					$.each(blocks, function() {
						if ($(this).attr(name)) {
							$(this).removeAttr(name);
						}
						else {
							$(this).attr(name, value);
						}
					});
				},
				removeClass: function(className) {
					var blocks = this.selection.getBlocks();
					$(blocks).removeClass(className);

					this.utils.removeEmptyAttr(blocks, 'class');

					this.code.sync();
				},
				setClass: function(className) {
					var blocks = this.selection.getBlocks();
					$(blocks).addClass(className);

					this.code.sync();
				},
				toggleClass: function(className) {
					var blocks = this.selection.getBlocks();
					$(blocks).toggleClass(className);

					this.code.sync();
				}
			};
		},
		buffer: function() {
			return {
				set: function(type) {
					if (typeof type == 'undefined' || type == 'undo') {
						this.buffer.setUndo();
					}
					else {
						this.buffer.setRedo();
					}
				},
				setUndo: function() {
					this.selection.save();
					this.opts.buffer.push(this.$editor.html());
					this.selection.restore();
				},
				setRedo: function() {
					this.selection.save();
					this.opts.rebuffer.push(this.$editor.html());
					this.selection.restore();
				},
				getUndo: function() {
					this.$editor.html(this.opts.buffer.pop());
				},
				getRedo: function() {
					this.$editor.html(this.opts.rebuffer.pop());
				},
				add: function() {
					this.opts.buffer.push(this.$editor.html());
				},
				undo: function() {
					if (this.opts.buffer.length === 0) return;

					this.buffer.set('redo');
					this.buffer.getUndo();

					this.selection.restore();

					setTimeout($.proxy(this.observe.load, this), 50);
				},
				redo: function() {
					if (this.opts.rebuffer.length === 0) return;

					this.buffer.set('undo');
					this.buffer.getRedo();

					this.selection.restore();

					setTimeout($.proxy(this.observe.load, this), 50);
				}
			};
		},
		build: function() {
			return {
				run: function() {
					this.build.createContainerBox();
					this.build.loadContent();
					this.build.loadEditor();
					this.build.enableEditor();
					this.build.setCodeAndCall();
				},
				isTextarea: function() {
					return (this.$element[0].tagName === 'TEXTAREA');
				},
				createContainerBox: function() {
					this.$box = $('<div class="redactor-box" />');
				},
				createTextarea: function() {
					this.$textarea = $('<textarea />').attr('name', this.build.getTextareaName());
				},
				getTextareaName: function() {
					return ((typeof(name) == 'undefined')) ? 'content-' + this.uuid : this.$element.attr('id');
				},
				loadContent: function() {
					var func = (this.build.isTextarea()) ? 'val' : 'html';
					this.content = $.trim(this.$element[func]());
				},
				enableEditor: function() {
					this.$editor.attr({ 'contenteditable': true, 'dir': this.opts.direction });
				},
				loadEditor: function() {
					var func = (this.build.isTextarea()) ? 'fromTextarea' : 'fromElement';
					this.build[func]();
				},
				fromTextarea: function() {
					this.$editor = $('<div />');
					this.$textarea = this.$element;
					this.$box.insertAfter(this.$element).append(this.$editor).append(this.$element);
					this.$editor.addClass('redactor-editor');

					this.$element.hide();
				},
				fromElement: function() {
					this.$editor = this.$element;
					this.build.createTextarea();
					this.$box.insertAfter(this.$editor).append(this.$editor).append(this.$textarea);
					this.$editor.addClass('redactor-editor');

					this.$textarea.hide();
				},
				setCodeAndCall: function() {
					// set code
					this.code.set(this.content);

					this.build.setOptions();
					this.build.callEditor();

					// code mode
					if (this.opts.visual) return;
					setTimeout($.proxy(this.code.showCode, this), 200);
				},
				callEditor: function() {
					this.build.disableMozillaEditing();
					this.build.setEvents();
					this.build.setHelpers();

					// load toolbar
					if (this.opts.toolbar) {
						this.opts.toolbar = this.toolbar.init();
						this.toolbar.build();
					}

					// plugins
					this.build.plugins();

					// observers
					setTimeout($.proxy(this.observe.load, this), 4);

					// init callback
					this.core.setCallback('init');
				},
				setOptions: function() {
					// textarea direction
					$(this.$textarea).attr('dir', this.opts.direction);

					if (this.opts.linebreaks) this.$editor.addClass('redactor-linebreaks');

					if (this.opts.tabindex) this.$editor.attr('tabindex', this.opts.tabindex);

					if (this.opts.minHeight) this.$editor.css('minHeight', this.opts.minHeight);
					if (this.opts.maxHeight) this.$editor.css('maxHeight', this.opts.maxHeight);

				},
				setEventDropUpload: function(e) {
					e.preventDefault();

					if (!this.opts.dragImageUpload || !this.opts.dragFileUpload) return;

					var files = e.dataTransfer.files;
					this.upload.directUpload(files[0], e);
				},
				setEventDrop: function(e) {
					this.code.sync();
					setTimeout(this.clean.clearUnverified, 1);
					this.core.setCallback('drop', e);
				},
				setEvents: function() {
					// drop
					this.$editor.on('drop.redactor', $.proxy(function(e) {
						e = e.originalEvent || e;

						if (window.FormData === undefined || !e.dataTransfer) return true;

						if (e.dataTransfer.files.length === 0) {
							return this.build.setEventDrop(e);
						}
						else {
							this.build.setEventDropUpload(e);
						}

						setTimeout(this.clean.clearUnverified, 1);
						this.core.setCallback('drop', e);

					}, this));


					// click
					this.$editor.on('click.redactor', $.proxy(function(e) {
						var event = this.core.getEvent();
						var type = (event == 'click' || event == 'arrow') ? false : 'click';

						this.core.addEvent(type);
						this.utils.disableSelectAll();
						this.core.setCallback('click', e);

					}, this));

					// paste
					this.$editor.on('paste.redactor', $.proxy(this.paste.init, this));

					// cut
					this.$editor.on('cut.redactor', $.proxy(this.code.sync, this));

					// keydown
					this.$editor.on('keydown.redactor', $.proxy(this.keydown.init, this));

					// keyup
					this.$editor.on('keyup.redactor', $.proxy(this.keyup.init, this));

					// textarea keydown
					if ($.isFunction(this.opts.codeKeydownCallback)) {
						this.$textarea.on('keydown.redactor-textarea', $.proxy(this.opts.codeKeydownCallback, this));
					}

					// textarea keyup
					if ($.isFunction(this.opts.codeKeyupCallback)) {
						this.$textarea.on('keyup.redactor-textarea', $.proxy(this.opts.codeKeyupCallback, this));
					}

					// focus
					if ($.isFunction(this.opts.focusCallback)) {
						this.$editor.on('focus.redactor', $.proxy(this.opts.focusCallback, this));
					}

					var clickedElement;
					$(document).on('mousedown', function(e) { clickedElement = e.target; });

					// blur
					this.$editor.on('blur.redactor', $.proxy(function(e) {
						if (this.rtePaste) return;
						if (!this.build.isBlured(clickedElement)) return;

						this.utils.disableSelectAll();
						if ($.isFunction(this.opts.blurCallback)) this.core.setCallback('blur', e);

					}, this));
				},
				isBlured: function(clickedElement) {
					var $el = $(clickedElement);

					return (!$el.hasClass('redactor-toolbar, redactor-dropdown') && $el.parents('.redactor-toolbar, .redactor-dropdown').length === 0);
				},
				setHelpers: function() {
					// linkify
					if (this.linkify.isEnabled()) {
						this.linkify.format();
					}

					// placeholder
					this.placeholder.enable();

					// focus
					if (this.opts.focus) setTimeout(this.focus.setStart, 100);
					if (this.opts.focusEnd) setTimeout(this.focus.setEnd, 100);

				},
				plugins: function() {
					if (!this.opts.plugins) return;
					if (!RedactorPlugins) return;

					$.each(this.opts.plugins, $.proxy(function(i, s) {
						if (typeof RedactorPlugins[s] === 'undefined') return;

						if ($.inArray(s, $.Redactor.modules) !== -1) {
							$.error('Plugin name "' + s + '" matches the name of the Redactor\'s module.');
							return;
						}

						if (!$.isFunction(RedactorPlugins[s])) return;

						this[s] = RedactorPlugins[s]();

						// get methods
						var methods = this.getModuleMethods(this[s]);
						var len = methods.length;

						// bind methods
						for (var z = 0; z < len; z++) {
							this[s][methods[z]] = this[s][methods[z]].bind(this);
						}

						if ($.isFunction(this[s].init)) this[s].init();


					}, this));

				},
				disableMozillaEditing: function() {
					if (!this.utils.browser('mozilla')) return;

					// FF fix
					try {
						document.execCommand('enableObjectResizing', false, false);
						document.execCommand('enableInlineTableEditing', false, false);
					} catch (e) {}
				}
			};
		},
		button: function() {
			return {
				build: function(btnName, btnObject) {
					var $button = $('<a href="#" class="fa fa-'+btnObject.icon+' re-icon re-'+btnName+'" rel="' + btnName + '" />').attr('tabindex', '-1');
					
					// click
					if (btnObject.func || btnObject.command || btnObject.dropdown) {
						this.button.setEvent($button, btnName, btnObject);
					}

					// dropdown
					if (btnObject.dropdown) {
						var $dropdown = $('<div class="redactor-dropdown redactor-dropdown-' + this.uuid + ' redactor-dropdown-box-' + btnName + '" style="display: none;">');
						$button.data('dropdown', $dropdown);
						this.dropdown.build(btnName, $dropdown, btnObject.dropdown);
					}

					// tooltip
					if (this.utils.isDesktop()) {
						this.button.createTooltip($button, btnName, btnObject.title);
					}

					return $button;
				},
				setEvent: function($button, btnName, btnObject) {
					$button.on('touchstart click', $.proxy(function(e) {
						if ($button.hasClass('redactor-button-disabled')) return false;

						var type = 'func';
						var callback = btnObject.func;

						if (btnObject.command) {
							type = 'command';
							callback = btnObject.command;
						}
						else if (btnObject.dropdown) {
							type = 'dropdown';
							callback = false;
						}

						this.button.onClick(e, btnName, type, callback);

					}, this));
				},
				createTooltip: function($button, name, title) {
					var $tooltip = $('<span>').addClass('redactor-toolbar-tooltip redactor-toolbar-tooltip-' + name).hide().html(title);
					$tooltip.appendTo('body');

					$button.on('mouseover', function() {
						if ($(this).hasClass('redactor-button-disabled')) return;

						var pos = $button.offset();

						$tooltip.show();
						$tooltip.css({
							top: (pos.top + $button.innerHeight()) + 'px',
							left: (pos.left + $button.innerWidth()/2 - $tooltip.innerWidth()/2) + 'px'
						});
					});

					$button.on('mouseout', function() {
						$tooltip.hide();
					});

				},
				onClick: function(e, btnName, type, callback) {
					this.button.caretOffset = this.caret.getOffset();

					e.preventDefault();

					if (this.utils.browser('msie')) e.returnValue = false;

					if (type == 'command') this.inline.format(callback);
					else if (type == 'dropdown') this.dropdown.show(e, btnName);
					else this.button.onClickCallback(e, callback, btnName);
				},
				onClickCallback: function(e, callback, btnName) {
					var func;

					if ($.isFunction(callback)) callback.call(this, btnName);
					else if (callback.search(/\./) != '-1') {
						func = callback.split('.');
						if (typeof this[func[0]] == 'undefined') return;

						if (btnName == "fontcolor" || btnName == "backcolor") this[func[0]][func[1]](btnName, e);
						else this[func[0]][func[1]](btnName);
					}
					else this[callback](btnName, e);

					this.observe.buttons(e, btnName);
				},
				get: function(key) {
					return this.$toolbar.find('a.re-' + key);
				},
				setActive: function(key) {
					this.button.get(key).addClass('redactor-act');
				},
				setInactive: function(key) {
					this.button.get(key).removeClass('redactor-act');
				},
				setInactiveAll: function(key) {
					if (typeof key === 'undefined') {
						this.$toolbar.find('a.re-icon').removeClass('redactor-act');
					}
					else {
						this.$toolbar.find('a.re-icon').not('.re-' + key).removeClass('redactor-act');
					}
				},
				setActiveInVisual: function() {
					this.$toolbar.find('a.re-icon').not('a.re-html').removeClass('redactor-button-disabled');
				},
				setInactiveInCode: function() {
					this.$toolbar.find('a.re-icon').not('a.re-html').addClass('redactor-button-disabled');
				},
				changeIcon: function(key, classname) {
					this.button.get(key).addClass('re-' + classname);
				},
				removeIcon: function(key, classname) {
					this.button.get(key).removeClass('re-' + classname);
				},
				setAwesome: function(key, name) {
					var $button = this.button.get(key);
					$button.removeClass('redactor-btn-image').addClass('fa-redactor-btn');
					$button.html('<i class="fa ' + name + '"></i>');
				},
				addCallback: function($btn, callback) {
					var type = (callback == 'dropdown') ? 'dropdown' : 'func';
					var key = $btn.attr('rel');
					$btn.on('touchstart click', $.proxy(function(e) {
						if ($btn.hasClass('redactor-button-disabled')) return false;
						this.button.onClick(e, key, type, callback);

					}, this));
				},
				addDropdown: function($btn, dropdown) {
					var key = $btn.attr('rel');
					this.button.addCallback($btn, 'dropdown');

					var $dropdown = $('<div class="redactor-dropdown redactor-dropdown-' + this.uuid + ' redactor-dropdown-box-' + key + '" style="display: none;">');
					$btn.data('dropdown', $dropdown);

					// build dropdown
					if (dropdown) this.dropdown.build(key, $dropdown, dropdown);

					return $dropdown;
				},
				add: function(key, title) {
					if (!this.opts.toolbar) return;

					var btn = this.button.build(key, { title: title });
					btn.addClass('redactor-btn-image');

					this.$toolbar.append($('<li>').append(btn));

					return btn;
				},
				addFirst: function(key, title) {
					if (!this.opts.toolbar) return;

					var btn = this.button.build(key, { title: title });
					btn.addClass('redactor-btn-image');
					this.$toolbar.prepend($('<li>').append(btn));

					return btn;
				},
				addAfter: function(afterkey, key, title) {
					if (!this.opts.toolbar) return;

					var btn = this.button.build(key, { title: title });
					btn.addClass('redactor-btn-image');
					var $btn = this.button.get(afterkey);

					if ($btn.length !== 0) $btn.parent().after($('<li>').append(btn));
					else this.$toolbar.append($('<li>').append(btn));

					return btn;
				},
				addBefore: function(beforekey, key, title) {
					if (!this.opts.toolbar) return;

					var btn = this.button.build(key, { title: title });
					btn.addClass('redactor-btn-image');
					var $btn = this.button.get(beforekey);

					if ($btn.length !== 0) $btn.parent().before($('<li>').append(btn));
					else this.$toolbar.append($('<li>').append(btn));

					return btn;
				},
				remove: function(key) {
					this.button.get(key).remove();
				}
			};
		},
		caret: function() {
			return {
				setStart: function(node) {
					// inline tag
					if (!this.utils.isBlock(node)) {
						var space = this.utils.createSpaceElement();

						$(node).prepend(space);
						this.caret.setEnd(space);
					}
					else {
						this.caret.set(node, 0, node, 0);
					}
				},
				setEnd: function(node) {
					this.caret.set(node, 1, node, 1);
				},
				set: function(orgn, orgo, focn, foco) {
					// focus
					// disabled in 10.0.7
					// if (!this.utils.browser('msie')) this.$editor.focus();

					orgn = orgn[0] || orgn;
					focn = focn[0] || focn;

					if (this.utils.isBlockTag(orgn.tagName) && orgn.innerHTML === '') {
						orgn.innerHTML = this.opts.invisibleSpace;
					}

					if (orgn.tagName == 'BR' && this.opts.linebreaks === false) {
						var parent = $(this.opts.emptyHtml)[0];
						$(orgn).replaceWith(parent);
						orgn = parent;
						focn = orgn;
					}

					this.selection.get();

					try {
						this.range.setStart(orgn, orgo);
						this.range.setEnd(focn, foco);
					}
					catch (e) {}

					this.selection.addRange();
				},
				setAfter: function(node) {
					try {
						var tag = $(node)[0].tagName;

						// inline tag
						if (tag != 'BR' && !this.utils.isBlock(node)) {
							var space = this.utils.createSpaceElement();

							$(node).after(space);
							this.caret.setEnd(space);
						}
						else {
							if (tag != 'BR' && this.utils.browser('msie')) {
								this.caret.setStart($(node).next());
							}
							else {
								this.caret.setAfterOrBefore(node, 'after');
							}
						}
					}
					catch (e) {
						var space = this.utils.createSpaceElement();
						$(node).after(space);
						this.caret.setEnd(space);
					}
				},
				setBefore: function(node) {
					// block tag
					if (this.utils.isBlock(node)) {
						this.caret.setEnd($(node).prev());
					}
					else {
						this.caret.setAfterOrBefore(node, 'before');
					}
				},
				setAfterOrBefore: function(node, type) {
					// focus
					if (!this.utils.browser('msie')) this.$editor.focus();

					node = node[0] || node;

					this.selection.get();

					if (type == 'after') {
						try {

							this.range.setStartAfter(node);
							this.range.setEndAfter(node);
						}
						catch (e) {}
					}
					else {
						try {
							this.range.setStartBefore(node);
							this.range.setEndBefore(node);
						}
						catch (e) {}
					}


					this.range.collapse(false);
					this.selection.addRange();
				},
				getOffsetOfElement: function(node) {
					node = node[0] || node;

					this.selection.get();

					var cloned = this.range.cloneRange();
					cloned.selectNodeContents(node);
					cloned.setEnd(this.range.endContainer, this.range.endOffset);

					return $.trim(cloned.toString()).length;
				},
				getOffset: function() {
					var offset = 0;
					var sel = window.getSelection();

					if (sel.rangeCount > 0) {
						var range = window.getSelection().getRangeAt(0);
						var caretRange = range.cloneRange();
						caretRange.selectNodeContents(this.$editor[0]);
						caretRange.setEnd(range.endContainer, range.endOffset);
						offset = caretRange.toString().length;
					}

					return offset;
				},
				setOffset: function(start, end) {
					if (typeof end == 'undefined') end = start;
					if (!this.focus.isFocused()) this.focus.setStart();

					var sel = this.selection.get();
					var node, offset = 0;
					var walker = document.createTreeWalker(this.$editor[0], NodeFilter.SHOW_TEXT, null, null);

					while (node = walker.nextNode()) {
						offset += node.nodeValue.length;
						if (offset > start) {
							this.range.setStart(node, node.nodeValue.length + start - offset);
							start = Infinity;
						}

						if (offset >= end) {
							this.range.setEnd(node, node.nodeValue.length + end - offset);
							break;
						}
					}

					this.range.collapse(false);
					this.selection.addRange();
				},
				// deprecated
				setToPoint: function(start, end) {
					this.caret.setOffset(start, end);
				},
				getCoords: function() {
					return this.caret.getOffset();
				}
			};
		},
		clean: function() {
			return {
				onSet: function(html) {
					html = this.clean.savePreCode(html);

					// convert script tag
					html = html.replace(/<script(.*?[^>]?)>([\w\W]*?)<\/script>/gi, '<pre class="redactor-script-tag" style="display: none;" $1>$2</pre>');

					// replace dollar sign to entity
					html = html.replace(/\$/g, '&#36;');

					// replace special characters in links
					html = html.replace(/<a href="(.*?[^>]?)®(.*?[^>]?)">/gi, '<a href="$1&reg$2">');

					if (this.opts.replaceDivs) html = this.clean.replaceDivs(html);
					if (this.opts.linebreaks)  html = this.clean.replaceParagraphsToBr(html);

					// save form tag
					html = this.clean.saveFormTags(html);

					// convert font tag to span
					var $div = $('<div>');
					$div.html(html);
					var fonts = $div.find('font[style]');
					if (fonts.length !== 0) {
						fonts.replaceWith(function() {
							var $el = $(this);
							var $span = $('<span>').attr('style', $el.attr('style'));
							return $span.append($el.contents());
						});

						html = $div.html();
					}
					$div.remove();

					// remove font tag
					html = html.replace(/<font(.*?[^<])>/gi, '');
					html = html.replace(/<\/font>/gi, '');

					// tidy html
					html = this.tidy.load(html);

					// paragraphize
					if (this.opts.paragraphize) html = this.paragraphize.load(html);

					// verified
					html = this.clean.setVerified(html);

					// convert inline tags
					html = this.clean.convertInline(html);

					return html;
				},
				onSync: function(html) {
					// remove spaces
					html = html.replace(/[\u200B-\u200D\uFEFF]/g, '');
					html = html.replace(/&#x200b;/gi, '');

					if (this.opts.cleanSpaces) {
						html = html.replace(/&nbsp;/gi, ' ');
					}

					if (html.search(/^<p>(||\s||<br\s?\/?>||&nbsp;)<\/p>$/i) != -1) {
						return '';
					}

					// reconvert script tag
					html = html.replace(/<pre class="redactor-script-tag" style="display: none;"(.*?[^>]?)>([\w\W]*?)<\/pre>/gi, '<script$1>$2</script>');

					// restore form tag
					html = this.clean.restoreFormTags(html);

					var chars = {
						'\u2122': '&trade;',
						'\u00a9': '&copy;',
						'\u2026': '&hellip;',
						'\u2014': '&mdash;',
						'\u2010': '&dash;'
					};
					// replace special characters
					$.each(chars, function(i,s) {
						html = html.replace(new RegExp(i, 'g'), s);
					});

					// remove br in the of li
					html = html.replace(new RegExp('<br\\s?/?></li>', 'gi'), '</li>');
					html = html.replace(new RegExp('</li><br\\s?/?>', 'gi'), '</li>');
					// remove verified
					html = html.replace(/<div(.*?[^>]) data-tagblock="redactor"(.*?[^>])>/gi, '<div$1$2>');
					html = html.replace(/<(.*?) data-verified="redactor"(.*?[^>])>/gi, '<$1$2>');
					html = html.replace(/<span(.*?[^>])\srel="(.*?[^>])"(.*?[^>])>/gi, '<span$1$3>');
					html = html.replace(/<img(.*?[^>])\srel="(.*?[^>])"(.*?[^>])>/gi, '<img$1$3>');
					html = html.replace(/<img(.*?[^>])\sstyle="" (.*?[^>])>'/gi, '<img$1 $2>');
					html = html.replace(/<img(.*?[^>])\sstyle (.*?[^>])>'/gi, '<img$1 $2>');
					
					while (html.search(/<span class="redactor-invisible-space">(.|\n)*?<\/span>/m) > -1) {
						html = html.replace(/<span class="redactor-invisible-space">((.|\n)*?)<\/span>/gi, '$1');
					}
					html = html.replace(/ data-save-url="(.*?[^>])"/gi, '');

					// remove image resize
					html = html.replace(/<span(.*?)id="redactor-image-box"(.*?[^>])>([\w\W]*?)<img(.*?)><\/span>/gi, '$3<img$4>');
					html = html.replace(/<span(.*?)id="redactor-image-resizer"(.*?[^>])>(.*?)<\/span>/gi, '');
					html = html.replace(/<span(.*?)id="redactor-image-editter"(.*?[^>])>(.*?)<\/span>/gi, '');

					// remove font tag
					html = html.replace(/<font(.*?[^<])>/gi, '');
					html = html.replace(/<\/font>/gi, '');

					// tidy html
					html = this.tidy.load(html);

					// link nofollow
					if (this.opts.linkNofollow) {
						html = html.replace(/<a(.*?)rel="nofollow"(.*?[^>])>/gi, '<a$1$2>');
						html = html.replace(/<a(.*?[^>])>/gi, '<a$1 rel="nofollow">');
					}

					// reconvert inline
					html = html.replace(/\sdata-redactor-(tag|class|style)="(.*?[^>])"/gi, '');
					html = html.replace(new RegExp('<(.*?) data-verified="redactor"(.*?[^>])>', 'gi'), '<$1$2>');
					html = html.replace(new RegExp('<(.*?) data-verified="redactor">', 'gi'), '<$1>');

					return html;
				},
				onPaste: function(html, setMode) {
					html = $.trim(html);

					html = html.replace(/\$/g, '&#36;');

					// convert dirty spaces
					html = html.replace(/<span class="s1">/gi, '<span>');
					html = html.replace(/<span class="Apple-converted-space">&nbsp;<\/span>/gi, ' ');
					html = html.replace(/<span class="Apple-tab-span"[^>]*>\t<\/span>/gi, '\t');
					html = html.replace(/<span[^>]*>(\s|&nbsp;)<\/span>/gi, ' ');
					html = html.replace(/<span><\/span>/,'');

					if (this.opts.pastePlainText) {
						return this.clean.getPlainText(html);
					}

					if (!this.utils.isSelectAll() && typeof setMode == 'undefined') {
						if (this.utils.isCurrentOrParent(['FIGCAPTION', 'A'])) {
							return this.clean.getPlainText(html, false);
						}

						if (this.utils.isCurrentOrParent('PRE')) {
							html = html.replace(/”/g, '"');
							html = html.replace(/“/g, '"');
							html = html.replace(/‘/g, '\'');
							html = html.replace(/’/g, '\'');

							return this.clean.getPreCode(html);
						}

						if (this.utils.isCurrentOrParent(['BLOCKQUOTE', 'H1', 'H2', 'H3', 'H4', 'H5', 'H6'])) {
							html = this.clean.getOnlyImages(html);

							if (!this.utils.browser('msie')) {
								var block = this.selection.getBlock();
								if (block && block.tagName == 'P') {
									html = html.replace(/<img(.*?)>/gi, '<p><img$1></p>');
								}
							}

							return html;
						}

						if (this.utils.isCurrentOrParent(['TD'])) {
							html = this.clean.onPasteTidy(html, 'td');

							if (this.opts.linebreaks) html = this.clean.replaceParagraphsToBr(html);

							html = this.clean.replaceDivsToBr(html);

							return html;
						}


						if (this.utils.isCurrentOrParent(['LI'])) {
							return this.clean.onPasteTidy(html, 'li');
						}
					}


					html = this.clean.isSingleLine(html, setMode);

					if (!this.clean.singleLine) {
						if (this.opts.linebreaks)  html = this.clean.replaceParagraphsToBr(html);
						if (this.opts.replaceDivs) html = this.clean.replaceDivs(html);

						html = this.clean.saveFormTags(html);
					}


					html = this.clean.onPasteWord(html);
					html = this.clean.onPasteExtra(html);

					html = this.clean.onPasteTidy(html, 'all');


					// paragraphize
					if (!this.clean.singleLine && this.opts.paragraphize) {
						html = this.paragraphize.load(html);
					}

					html = this.clean.removeDirtyStyles(html);
					html = this.clean.onPasteRemoveSpans(html);
					html = this.clean.onPasteRemoveEmpty(html);


					html = this.clean.convertInline(html);

					return html;
				},
				onPasteWord: function(html) {
					// comments
					html = html.replace(/<!--[\s\S]*?-->/gi, '');

					// style
					html = html.replace(/<style[^>]*>[\s\S]*?<\/style>/gi, '');

					if (/(class=\"?Mso|style=\"[^\"]*\bmso\-|w:WordDocument)/.test(html)) {
						html = this.clean.onPasteIeFixLinks(html);

						// shapes
						html = html.replace(/<img(.*?)v:shapes=(.*?)>/gi, '');
						html = html.replace(/src="file\:\/\/(.*?)"/, 'src=""');

						// list
						html = html.replace(/<p(.*?)class="MsoListParagraphCxSpFirst"([\w\W]*?)<\/p>/gi, '<ul><li$2</li>');
						html = html.replace(/<p(.*?)class="MsoListParagraphCxSpMiddle"([\w\W]*?)<\/p>/gi, '<li$2</li>');
						html = html.replace(/<p(.*?)class="MsoListParagraphCxSpLast"([\w\W]*?)<\/p>/gi, '<li$2</li></ul>');
						// one line
						html = html.replace(/<p(.*?)class="MsoListParagraph"([\w\W]*?)<\/p>/gi, '<ul><li$2</li></ul>');
						// remove ms word's bullet
						html = html.replace(/·/g, '');
						html = html.replace(/<p class="Mso(.*?)"/gi, '<p');

						// classes
						html = html.replace(/ class=\"(mso[^\"]*)\"/gi,	"");
						html = html.replace(/ class=(mso\w+)/gi, "");

						// remove ms word tags
						html = html.replace(/<o:p(.*?)>([\w\W]*?)<\/o:p>/gi, '$2');

						// ms word break lines
						html = html.replace(/\n/g, ' ');

						// ms word lists break lines
						html = html.replace(/<p>\n?<li>/gi, '<li>');
					}

					// remove nbsp
					if (this.opts.cleanSpaces) {
						html = html.replace(/(\s|&nbsp;)+/g, ' ');
					}

					return html;
				},
				onPasteExtra: function(html) {
					// remove google docs markers
					html = html.replace(/<b\sid="internal-source-marker(.*?)">([\w\W]*?)<\/b>/gi, "$2");
					html = html.replace(/<b(.*?)id="docs-internal-guid(.*?)">([\w\W]*?)<\/b>/gi, "$3");

					// google docs styles
			 		html = html.replace(/<span[^>]*(font-style: italic; font-weight: bold|font-weight: bold; font-style: italic)[^>]*>/gi, '<span style="font-weight: bold;"><span style="font-style: italic;">');
			 		html = html.replace(/<span[^>]*font-style: italic[^>]*>/gi, '<span style="font-style: italic;">');
					html = html.replace(/<span[^>]*font-weight: bold[^>]*>/gi, '<span style="font-weight: bold;">');
					html = html.replace(/<span[^>]*text-decoration: underline[^>]*>/gi, '<span style="text-decoration: underline;">');

					html = html.replace(/<img>/gi, '');
					html = html.replace(/\n{3,}/gi, '\n');
					html = html.replace(/<font(.*?)>([\w\W]*?)<\/font>/gi, '$2');

					// remove dirty p
					html = html.replace(/<p><p>/gi, '<p>');
					html = html.replace(/<\/p><\/p>/gi, '</p>');
					html = html.replace(/<li>(\s*|\t*|\n*)<p>/gi, '<li>');
					html = html.replace(/<\/p>(\s*|\t*|\n*)<\/li>/gi, '</li>');

					// remove space between paragraphs
					html = html.replace(/<\/p>\s<p/gi, '<\/p><p');

					// remove safari local images
					html = html.replace(/<img src="webkit-fake-url\:\/\/(.*?)"(.*?)>/gi, '');

					// bullets
					html = html.replace(/<p>•([\w\W]*?)<\/p>/gi, '<li>$1</li>');

					// FF fix
					if (this.utils.browser('mozilla')) {
						html = html.replace(/<br\s?\/?>$/gi, '');
					}

					return html;
				},
				onPasteTidy: function(html, type) {
					// remove all tags except these
					var tags = ['span', 'a', 'pre', 'blockquote', 'small', 'em', 'strong', 'code', 'kbd', 'mark', 'address', 'cite', 'var', 'samp', 'dfn', 'sup', 'sub', 'b', 'i', 'u', 'del',
								'ol', 'ul', 'li', 'dl', 'dt', 'dd', 'p', 'br', 'video', 'audio', 'iframe', 'embed', 'param', 'object', 'img', 'table',
								'td', 'th', 'tr', 'tbody', 'tfoot', 'thead', 'h1', 'h2', 'h3', 'h4', 'h5', 'h6'];
					var tagsEmpty = false;
					var attrAllowed =  [
							['a', '*'],
							['img', ['src', 'alt']],
							['span', ['class', 'rel', 'data-verified']],
							['iframe', '*'],
							['video', '*'],
							['audio', '*'],
							['embed', '*'],
							['object', '*'],
							['param', '*'],
							['source', '*']
						];

					if (type == 'all') {
						tagsEmpty = ['p', 'span', 'h1', 'h2', 'h3', 'h4', 'h5', 'h6'];
						attrAllowed =  [
							['table', 'class'],
							['td', ['colspan', 'rowspan']],
							['a', '*'],
							['img', ['src', 'alt', 'data-redactor-inserted-image']],
							['span', ['class', 'rel', 'data-verified']],
							['iframe', '*'],
							['video', '*'],
							['audio', '*'],
							['embed', '*'],
							['object', '*'],
							['param', '*'],
							['source', '*']
						];
					}
					else if (type == 'td') {
						// remove all tags except these and remove all table tags: tr, td etc
						tags = ['ul', 'ol', 'li', 'span', 'a', 'small', 'em', 'strong', 'code', 'kbd', 'mark', 'cite', 'var', 'samp', 'dfn', 'sup', 'sub', 'b', 'i', 'u', 'del',
								'ol', 'ul', 'li', 'dl', 'dt', 'dd', 'br', 'iframe', 'video', 'audio', 'embed', 'param', 'object', 'img', 'h1', 'h2', 'h3', 'h4', 'h5', 'h6'];

					}
					else if (type == 'li') {
						// only inline tags and ul, ol, li
						tags = ['ul', 'ol', 'li', 'span', 'a', 'small', 'em', 'strong', 'code', 'kbd', 'mark', 'cite', 'var', 'samp', 'dfn', 'sup', 'sub', 'b', 'i', 'u', 'del', 'br',
								'iframe', 'video', 'audio', 'embed', 'param', 'object', 'img'];
					}

					var options = {
						deniedTags: (this.opts.deniedTags) ? this.opts.deniedTags : false,
						allowedTags: (this.opts.allowedTags) ? this.opts.allowedTags : tags,
						removeComments: true,
						removePhp: true,
						removeAttr: (this.opts.removeAttr) ? this.opts.removeAttr : false,
						allowedAttr: (this.opts.allowedAttr) ? this.opts.allowedAttr : attrAllowed,
						removeEmpty: tagsEmpty
					};

					return this.tidy.load(html, options);
				},
				onPasteRemoveEmpty: function(html) {
					html = html.replace(/<(p|h[1-6])>(|\s|\n|\t|<br\s?\/?>)<\/(p|h[1-6])>/gi, '');

					// remove br in the end
					if (!this.opts.linebreaks) html = html.replace(/<br>$/i, '');

					return html;
				},
				onPasteRemoveSpans: function(html) {
					html = html.replace(/<span>(.*?)<\/span>/gi, '$1');
					html = html.replace(/<span[^>]*>\s|&nbsp;<\/span>/gi, ' ');

					return html;
				},
				onPasteIeFixLinks: function(html) {
					if (!this.utils.browser('msie')) return html;

					var tmp = $.trim(html);
					if (tmp.search(/^<a(.*?)>(.*?)<\/a>$/i) === 0) {
						html = html.replace(/^<a(.*?)>(.*?)<\/a>$/i, "$2");
					}

					return html;
				},
				isSingleLine: function(html, setMode) {
					this.clean.singleLine = false;

					if (!this.utils.isSelectAll() && typeof setMode == 'undefined') {
						var blocks = this.opts.blockLevelElements.join('|').replace('P|', '').replace('DIV|', '');

						var matchBlocks = html.match(new RegExp('</(' + blocks + ')>', 'gi'));
						var matchContainers = html.match(/<\/(p|div)>/gi);

						if (!matchBlocks && (matchContainers === null || (matchContainers && matchContainers.length <= 1))) {
							var matchBR = html.match(/<br\s?\/?>/gi);
							var matchIMG = html.match(/<img(.*?[^>])>/gi);
							if (!matchBR && !matchIMG) {
								this.clean.singleLine = true;
								html = html.replace(/<\/?(p|div)(.*?)>/gi, '');
							}
						}
					}

					return html;
				},
				stripTags: function(input, allowed) {
					allowed = (((allowed || '') + '').toLowerCase().match(/<[a-z][a-z0-9]*>/g) || []).join('');
					var tags = /<\/?([a-z][a-z0-9]*)\b[^>]*>/gi;

					return input.replace(tags, function ($0, $1) {
						return allowed.indexOf('<' + $1.toLowerCase() + '>') > -1 ? $0 : '';
					});
				},
				savePreCode: function(html) {
					html = this.clean.savePreFormatting(html);
					html = this.clean.saveCodeFormatting(html);

					return html;
				},
				savePreFormatting: function(html) {
					var pre = html.match(/<pre(.*?)>([\w\W]*?)<\/pre>/gi);
					if (pre !== null) {
						$.each(pre, $.proxy(function(i,s) {
							var arr = s.match(/<pre(.*?)>([\w\W]*?)<\/pre>/i);

							arr[2] = arr[2].replace(/<br\s?\/?>/g, '\n');
							arr[2] = arr[2].replace(/&nbsp;/g, ' ');

							if (this.opts.preSpaces) {
								arr[2] = arr[2].replace(/\t/g, Array(this.opts.preSpaces + 1).join(' '));
							}

							arr[2] = this.clean.encodeEntities(arr[2]);

							// $ fix
							arr[2] = arr[2].replace(/\$/g, '&#36;');

							html = html.replace(s, '<pre' + arr[1] + '>' + arr[2] + '</pre>');

						}, this));
					}

					return html;
				},
				saveCodeFormatting: function(html) {
					var code = html.match(/<code(.*?[^>])>(.*?)<\/code>/gi);
					if (code !== null) {
						$.each(code, $.proxy(function(i,s) {
							var arr = s.match(/<code(.*?[^>])>(.*?)<\/code>/i);

							arr[2] = arr[2].replace(/&nbsp;/g, ' ');
							arr[2] = this.clean.encodeEntities(arr[2]);

							// $ fix
							arr[2] = arr[2].replace(/\$/g, '&#36;');

							html = html.replace(s, '<code' + arr[1] + '>' + arr[2] + '</code>');

						}, this));
					}

					return html;
				},
				getTextFromHtml: function(html) {
					html = html.replace(/<br\s?\/?>|<\/H[1-6]>|<\/p>|<\/div>|<\/li>|<\/td>/gi, '\n');

					var tmp = document.createElement('div');
					tmp.innerHTML = html;
					html = tmp.textContent || tmp.innerText;

					return $.trim(html);
				},
				getPlainText: function(html, paragraphize) {
					html = this.clean.getTextFromHtml(html);
					html = html.replace(/\n/g, '<br />');

					if (this.opts.paragraphize && typeof paragraphize == 'undefined' && !this.utils.browser('mozilla')) {
						html = this.paragraphize.load(html);
					}

					return html;
				},
				getPreCode: function(html) {
					html = html.replace(/<img(.*?) style="(.*?)"(.*?[^>])>/gi, '<img$1$3>');
					html = html.replace(/<img(.*?)>/gi, '&lt;img$1&gt;');
					html = this.clean.getTextFromHtml(html);

					if (this.opts.preSpaces) {
						html = html.replace(/\t/g, Array(this.opts.preSpaces + 1).join(' '));
					}

					html = this.clean.encodeEntities(html);

					return html;
				},
				getOnlyImages: function(html) {
					html = html.replace(/<img(.*?)>/gi, '[img$1]');

					// remove all tags
					html = html.replace(/<([Ss]*?)>/gi, '');

					html = html.replace(/\[img(.*?)\]/gi, '<img$1>');

					return html;
				},
				getOnlyLinksAndImages: function(html) {
					html = html.replace(/<a(.*?)href="(.*?)"(.*?)>([\w\W]*?)<\/a>/gi, '[a href="$2"]$4[/a]');
					html = html.replace(/<img(.*?)>/gi, '[img$1]');

					// remove all tags
					html = html.replace(/<(.*?)>/gi, '');

					html = html.replace(/\[a href="(.*?)"\]([\w\W]*?)\[\/a\]/gi, '<a href="$1">$2</a>');
					html = html.replace(/\[img(.*?)\]/gi, '<img$1>');

					return html;
				},
				encodeEntities: function(str) {
					str = String(str).replace(/&amp;/g, '&').replace(/&lt;/g, '<').replace(/&gt;/g, '>').replace(/&quot;/g, '"');
					return str.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
				},
				removeDirtyStyles: function(html) {
					if (this.utils.browser('msie')) return html;

					var div = document.createElement('div');
					div.innerHTML = html;

					this.clean.clearUnverifiedRemove($(div));

					html = div.innerHTML;
					$(div).remove();

					return html;
				},
				clearUnverified: function() {
					if (this.utils.browser('msie')) return;

					this.clean.clearUnverifiedRemove(this.$editor);

					var headers = this.$editor.find('h1, h2, h3, h4, h5, h6');
					headers.find('span').removeAttr('style');
					headers.find(this.opts.verifiedTags.join(', ')).removeAttr('style');

					this.code.sync();
				},
				clearUnverifiedRemove: function($editor) {
					$editor.find(this.opts.verifiedTags.join(', ')).removeAttr('style');
					$editor.find('span').not('[data-verified="redactor"]').removeAttr('style');

					$editor.find('span[data-verified="redactor"], img[data-verified="redactor"]').each(function(i, s) {
						var $s = $(s);
						$s.attr('style', $s.attr('rel'));
					});

				},
				cleanEmptyParagraph: function() {
					var p = this.$editor.find("p").first();

					if (this.utils.isEmpty(p.html())) {
						p.remove();
					}
				},
				setVerified: function(html) {
					if (this.utils.browser('msie')) return html;

					html = html.replace(new RegExp('<img(.*?[^>])>', 'gi'), '<img$1 data-verified="redactor">');
					html = html.replace(new RegExp('<span(.*?[^>])>', 'gi'), '<span$1 data-verified="redactor">');

					var matches = html.match(new RegExp('<(span|img)(.*?)style="(.*?)"(.*?[^>])>', 'gi'));

					if (matches) {
						var len = matches.length;
						for (var i = 0; i < len; i++) {
							try {

								var newTag = matches[i].replace(/style="(.*?)"/i, 'style="$1" rel="$1"');
								html = html.replace(matches[i], newTag);

							}
							catch (e) {}
						}
					}

					return html;
				},
				convertInline: function(html) {
					var $div = $('<div />').html(html);

					var tags = this.opts.inlineTags;
					tags.push('span');

					$div.find(tags.join(',')).each(function() {
						var $el = $(this);
						var tag = this.tagName.toLowerCase();
						$el.attr('data-redactor-tag', tag);

						if (tag == 'span') {
							if ($el.attr('style')) $el.attr('data-redactor-style', $el.attr('style'));
							else if ($el.attr('class')) $el.attr('data-redactor-class', $el.attr('class'));
						}

					});

					html = $div.html();
					$div.remove();

					return html;
				},
				normalizeLists: function() {
					this.$editor.find('li').each(function(i,s) {
						var $next = $(s).next();
						if ($next.length !== 0 && ($next[0].tagName == 'UL' || $next[0].tagName == 'OL')) {
							$(s).append($next);
						}

					});
				},
				removeSpaces: function(html) {
					html = html.replace(/\n/g, '');
					html = html.replace(/[\t]*/g, '');
					html = html.replace(/\n\s*\n/g, "\n");
					html = html.replace(/^[\s\n]*/g, ' ');
					html = html.replace(/[\s\n]*$/g, ' ');
					html = html.replace( />\s{2,}</g, '> <'); // between inline tags can be only one space
					html = html.replace(/\n\n/g, "\n");
					html = html.replace(/[\u200B-\u200D\uFEFF]/g, '');

					return html;
				},
				replaceDivs: function(html) {
					if (this.opts.linebreaks) {
						html = html.replace(/<div><br\s?\/?><\/div>/gi, '<br />');
						html = html.replace(/<div(.*?)>([\w\W]*?)<\/div>/gi, '$2<br />');
					}
					else {
						html = html.replace(/<div(.*?)>([\w\W]*?)<\/div>/gi, '<p$1>$2</p>');
					}

					html = html.replace(/<div(.*?[^>])>/gi, '');
					html = html.replace(/<\/div>/gi, '');

					return html;
				},
				replaceDivsToBr: function(html) {
					html = html.replace(/<div\s(.*?)>/gi, '<p>');
					html = html.replace(/<div><br\s?\/?><\/div>/gi, '<br /><br />');
					html = html.replace(/<div>([\w\W]*?)<\/div>/gi, '$1<br /><br />');

					return html;
				},
				replaceParagraphsToBr: function(html) {
					html = html.replace(/<p\s(.*?)>/gi, '<p>');
					html = html.replace(/<p><br\s?\/?><\/p>/gi, '<br />');
					html = html.replace(/<p>([\w\W]*?)<\/p>/gi, '$1<br /><br />');
					html = html.replace(/(<br\s?\/?>){1,}\n?<\/blockquote>/gi, '</blockquote>');

					return html;
				},
				saveFormTags: function(html) {
					return html.replace(/<form(.*?)>([\w\W]*?)<\/form>/gi, '<section$1 rel="redactor-form-tag">$2</section>');
				},
				restoreFormTags: function(html) {
					return html.replace(/<section(.*?) rel="redactor-form-tag"(.*?)>([\w\W]*?)<\/section>/gi, '<form$1$2>$3</form>');
				}
			};
		},
		code: function() {
			return {
				set: function(html) {
					html = $.trim(html.toString());

					// clean
					html = this.clean.onSet(html);

					this.$editor.html(html);
					this.code.sync();

					if (html !== '') this.placeholder.remove();

					setTimeout($.proxy(this.buffer.add, this), 15);
					if (this.start === false) this.observe.load();

				},
				get: function() {
					var code = this.$textarea.val();

					// indent code
					code = this.tabifier.get(code);

					return code;
				},
				sync: function() {
					setTimeout($.proxy(this.code.startSync, this), 10);
				},
				startSync: function() {
					var html = this.$editor.html();

					// is there a need to synchronize
					if (this.code.syncCode && this.code.syncCode == html) {
						// do not sync
						return;
					}

					// save code
					this.code.syncCode = html;

					// before clean callback
					html = this.core.setCallback('syncBefore', html);

					// clean
					html = this.clean.onSync(html);
/*
					var iframes = $(html).find("iframe");
					for (var i=0, loop=iframes.length;i<loop;i++) {
						$(iframes[i]).width("100%");
						$(iframes[i]).height(9 * $(iframes[i]).width() / 16);
					}
*/
					// set code
					this.$textarea.val(html);

					// after sync callback
					this.core.setCallback('sync', html);

					if (this.start === false) {
						this.core.setCallback('change', html);
					}
					
					this.start = false;

					if (this.opts.codemirror) {
						this.$textarea.next('.CodeMirror').each(function(i, el) {
							el.CodeMirror.setValue(html);
						});
					}
				},
				toggle: function() {
					if (this.opts.visual) {
						this.code.showCode();
					}
					else {
						this.code.showVisual();
					}
				},
				showCode: function() {
					this.code.offset = this.caret.getOffset();
					var scroll = $(window).scrollTop();

					var	width = this.$editor.innerWidth(),
						height = this.$editor.outerHeight();

					this.$editor.hide();

					var html = this.$textarea.val();
					this.modified = this.clean.removeSpaces(html);

					// indent code
					html = this.tabifier.get(html);

					this.$textarea.val(html);

					if (this.opts.codemirror) {
						this.$textarea.next('.CodeMirror').each(function(i, el) {
							$(el).show();
							el.CodeMirror.setValue(html);
							el.CodeMirror.setSize(width, height);
							el.CodeMirror.refresh();
							el.CodeMirror.focus();
						});
					}
					else {
						this.$textarea.outerHeight(height).show().focus();
						this.$textarea.on('keydown.redactor-textarea-indenting', this.code.textareaIndenting);

						$(window).scrollTop(scroll);

						if (this.$textarea[0].setSelectionRange) {
							this.$textarea[0].setSelectionRange(0, 0);
						}

						this.$textarea[0].scrollTop = 0;
					}

					this.opts.visual = false;

					this.button.setInactiveInCode();
					this.button.setActive('html');
					this.core.setCallback('source', html);
				},
				showVisual: function() {
					var html;

					if (this.opts.visual) return;

					if (this.opts.codemirror) {
						this.$textarea.next('.CodeMirror').each(function(i, el) {
							html = el.CodeMirror.getValue();
						});
					}
					else {
						html = this.$textarea.hide().val();
					}

					if (this.modified !== this.clean.removeSpaces(html)) {
						this.code.set(html);
					}

					if (this.opts.codemirror) {
						this.$textarea.next('.CodeMirror').hide();
					}

					this.$editor.show();

					if (!this.utils.isEmpty(html)) {
						this.placeholder.remove();
					}

					this.caret.setOffset(this.code.offset);

					this.$textarea.off('keydown.redactor-textarea-indenting');

					this.button.setActiveInVisual();
					this.button.setInactive('html');

					this.observe.load();
					this.opts.visual = true;
					this.core.setCallback('visual', html);
				},
				textareaIndenting: function(e) {
					if (e.keyCode !== 9) return true;

					var $el = this.$textarea;
					var start = $el.get(0).selectionStart;
					$el.val($el.val().substring(0, start) + "\t" + $el.val().substring($el.get(0).selectionEnd));
					$el.get(0).selectionStart = $el.get(0).selectionEnd = start + 1;

					return false;
				}
			};
		},
		core: function() {
			return {
				getObject: function() {
					return $.extend({}, this);
				},
				getEditor: function() {
					return this.$editor;
				},
				getBox: function() {
					return this.$box;
				},
				getElement: function() {
					return this.$element;
				},
				getTextarea: function() {
					return this.$textarea;
				},
				getToolbar: function() {
					return (this.$toolbar) ? this.$toolbar : false;
				},
				addEvent: function(name) {
					this.core.event = name;
				},
				getEvent: function() {
					return this.core.event;
				},
				setCallback: function(type, e, data) {
					var callback = this.opts[type + 'Callback'];
					if ($.isFunction(callback)) {
						return (typeof data == 'undefined') ? callback.call(this, e) : callback.call(this, e, data);
					}
					else {
						return (typeof data == 'undefined') ? e : data;
					}
				},
				destroy: function() {
					this.core.setCallback('destroy');

					// off events and remove data
					this.$element.off('.redactor').removeData('redactor');
					this.$editor.off('.redactor');

					$(document).off('click.redactor-image-delete.' + this.uuid);
					$(document).off('click.redactor-image-resize-hide.' + this.uuid);
					$(document).off('touchstart.redactor.' + this.uuid + ' click.redactor.' + this.uuid);
//					$("body").off('scroll.redactor.' + this.uuid);
					$(document).off('scroll.redactor.' + this.uuid);

					// common
					this.$editor.removeClass('redactor-editor redactor-linebreaks redactor-placeholder');
					this.$editor.removeAttr('contenteditable');

					var html = this.code.get();

					// dropdowns off
					this.$toolbar.find('a').each(function() {
						var $el = $(this);
						if ($el.data('dropdown')) {
							$el.data('dropdown').remove();
							$el.data('dropdown', {});
						}
					});


					if (this.build.isTextarea()) {
						this.$box.after(this.$element);
						this.$box.remove();
						this.$element.val(html).show();
					}
					else {
						this.$box.after(this.$editor);
						this.$box.remove();
						this.$element.html(html).show();
					}

					// paste box
					if (this.$pasteBox) this.$pasteBox.remove();

					// buttons tooltip
					$('.redactor-toolbar-tooltip').remove();

					
				}
			};
		},
		dropdown: function() {
			return {
				build: function(name, $dropdown, dropdownObject) {
					if (name == 'formatting' && this.opts.formattingAdd) {
						$.each(this.opts.formattingAdd, $.proxy(function(i,s) {
							var name = s.tag,
								func;

							if (typeof s['class'] != 'undefined') {
								name = name + '-' + s['class'];
							}

							s.type = (this.utils.isBlockTag(s.tag)) ? 'block' : 'inline';

							if (typeof s.func !== "undefined") {
								func = s.func;
							}
							else {
								func = (s.type == 'inline') ? 'inline.formatting' : 'block.formatting';
							}

							if (this.opts.linebreaks && s.type == 'block' && s.tag == 'p') return;

							this.formatting[name] = {
								tag: s.tag,
								style: s.style,
								'class': s['class'],
								attr: s.attr,
								data: s.data,
								clear: s.clear
							};

							dropdownObject[name] = {
								func: func,
								title: s.title
							};

						}, this));

					}

					$.each(dropdownObject, $.proxy(function(btnName, btnObject) {
						var $item = $('<a href="#" class="redactor-dropdown-' + btnName + '">' + btnObject.title + '</a>');
						if (name == 'formatting') $item.addClass('redactor-formatting-' + btnName);

						$item.on('click', $.proxy(function(e) {
							e.preventDefault();

							var type = 'func';
							var callback = btnObject.func;
							if (btnObject.command) {
								type = 'command';
								callback = btnObject.command;
							}
							else if (btnObject.dropdown) {
								type = 'dropdown';
								callback = btnObject.dropdown;
							}

							this.button.onClick(e, btnName, type, callback);
							this.dropdown.hideAll();

						}, this));

						$dropdown.append($item);

					}, this));
				},
				show: function(e, key) {
					if (!this.opts.visual) {
						e.preventDefault();
						return false;
					}

					var $button = this.button.get(key);

					// Always re-append it to the end of <body> so it always has the highest sub-z-index.
					var $dropdown = $button.data('dropdown').appendTo(document.body);

					// ios keyboard hide
					if (this.utils.isMobile() && !this.utils.browser('msie')) {
						document.activeElement.blur();
					}

					if ($button.hasClass('dropact')) {
						this.dropdown.hideAll();
					}
					else {
						this.dropdown.hideAll();
						this.core.setCallback('dropdownShow', { dropdown: $dropdown, key: key, button: $button });

						this.button.setActive(key);

						$button.addClass('dropact');

						var keyPosition = $button.offset();

						// fix right placement
						var dropdownWidth = $dropdown.width();
						if ((keyPosition.left + dropdownWidth) > $(document).width()) {
							keyPosition.left = Math.max(0, keyPosition.left - dropdownWidth);
						}

						var left = keyPosition.left + 'px';
						if (this.$toolbar.hasClass('toolbar-fixed-box')) {
							var top = this.$toolbar.innerHeight() + $("iModuleNavigation.fixed").outerHeight(true);
							var position = 'fixed';
							$dropdown.css({ position: position, left: left, top: top + 'px' }).show();
						}
						else {
							var top = ($button.innerHeight() + keyPosition.top) + 'px';

							$dropdown.css({ position: 'absolute', left: left, top: top }).show();
						}

						this.core.setCallback('dropdownShown', { dropdown: $dropdown, key: key, button: $button });
					}

					$(document).one('click', $.proxy(this.dropdown.hide, this));
					this.$editor.one('click', $.proxy(this.dropdown.hide, this));

					e.stopPropagation();
				},
				hideAll: function() {
					this.$toolbar.find('a.dropact').removeClass('redactor-act').removeClass('dropact');

					$(document.body).removeClass('body-redactor-hidden').css('margin-right', 0);
					$('.redactor-dropdown-' + this.uuid).hide();
					this.core.setCallback('dropdownHide');
				},
				hide: function (e) {
					var $dropdown = $(e.target);
					if (!$dropdown.hasClass('dropact')) {
						$dropdown.removeClass('dropact');
						this.dropdown.hideAll();
					}
				}
			};
		},
		file: function() {
			return {
				show: function() {
					$("#"+this.$textarea.attr("id")+"-attachment input[name=wysiwyg_file]").trigger("click");
				}
			};
		},
		focus: function() {
			return {
				setStart: function() {
					this.$editor.focus();

					var first = this.$editor.children().first();

					if (first.length === 0) return;
					if (first[0].length === 0 || first[0].tagName == 'BR' || first[0].nodeType == 3) {
						return;
					}

					if (first[0].tagName == 'UL' || first[0].tagName == 'OL') {
						var child = first.find('li').first();
						if (!this.utils.isBlock(child) && child.text() === '') {
							// empty inline tag in li
							this.caret.setStart(child);
							return;
						}
					}

					if (this.opts.linebreaks && !this.utils.isBlockTag(first[0].tagName)) {
						this.selection.get();
						this.range.setStart(this.$editor[0], 0);
						this.range.setEnd(this.$editor[0], 0);
						this.selection.addRange();

						return;
					}

					// if node is tag
					this.caret.setStart(first);
				},
				setEnd: function() {
					if (this.utils.browser('mozilla') || this.utils.browser('msie')) {
						var last = this.$editor.children().last();

						this.$editor.focus();
						this.caret.setEnd(last);
					}
					else {
						this.selection.get();

						try {
							this.range.selectNodeContents(this.$editor[0]);
							this.range.collapse(false);

							this.selection.addRange();
						}
						catch (e) {}
					}

				},
				isFocused: function() {
					var focusNode = document.getSelection().focusNode;
					if (focusNode === null) return false;

					if (this.opts.linebreaks && $(focusNode.parentNode).hasClass('redactor-linebreaks')) return true;
					else if (!this.utils.isRedactorParent(focusNode.parentNode)) return false;

					return this.$editor.is(':focus');
				}
			};
		},
		insertcode: function() {
			return {
				show: function() {
					var contentHtml = '<label>'+this.lang.get("insert_code_text")+'</label>';
					contentHtml+= '<div class="inputBlock">'
									+ '<textarea id="redactor-insert-code-area" style="height: 160px; font-family:Menlo, Monaco, monospace, sans-serif, defaultFont;" class="textareaControl"></textarea>'
									+ '<div class="helpBlock"></div>'
								+ '</div>';
					
					contentHtml+= '<div class="blank"></div>';
								
					contentHtml+= '<input type="hidden" id="redactor-insert-code-language">';
					contentHtml+= '<label>'+this.lang.get("insert_code_language")+'</label>';
					contentHtml+= '<div class="inputBlock">'
									+ '<div id="redactor-insert-code-language-select" class="selectControl" data-field="#redactor-insert-code-language">'
										+ '<button>'+this.lang.get("none")+' <span class="arrow"></span></button>'
										+ '<ul>'
											+ '<li data-value="none">'+this.lang.get("none")+'</li>'
											+ '<li data-value="php">PHP</li>'
											+ '<li data-value="js">JavaScript</li>'
											+ '<li data-value="css">CSS</li>'
											+ '<li data-value="python">Python</li>'
											+ '<li data-value="xml">XML</li>'
											+ '<li data-value="html">HTML</li>'
											+ '<li data-value="cpp">C/C++</li>'
											+ '<li data-value="csharp">C#</li>'
											+ '<li data-value="applescript">Apple Script</li>'
											+ '<li data-value="actionscript3">Action Script</li>'
											+ '<li data-value="bash">bash/shell</li>'
											+ '<li data-value="delphi">Delphi</li>'
											+ '<li data-value="java">Java</li>'
											+ '<li data-value="perl">Perl</li>'
											+ '<li data-value="python">Python</li>'
											+ '<li data-value="sql">SQL</li>'
										+ '</ul>'
									+ '</div>'
								+ '</div>';
					
					var buttons = [{
						text:this.lang.get("cancel"),
						style:"default",
						click:function() {
							iModule.modal.close();
						}
					}];
					
					var submit = {
						text:this.lang.get("insert"),
						style:"submit",
						click:$.proxy(this.insertcode.insert,this)
					};

					iModule.modal.show(this.lang.get("insert_code"),contentHtml,submit,buttons);
					iModule.initSelectControl($("#redactor-insert-code-language-select"));
					
					this.selection.save();
					$('#redactor-insert-code-area').focus();
					$('#redactor-insert-code-area').on("keydown",function(e) {
						if (e.keyCode == 9) {
							if (e.shiftKey == false) {
								var myValue = "\t";
								var startPos = this.selectionStart;
								var endPos = this.selectionEnd;
								var scrollTop = this.scrollTop;
								this.value = this.value.substring(0, startPos) + myValue + this.value.substring(endPos,this.value.length);
								this.focus();
								this.selectionStart = startPos + myValue.length;
								this.selectionEnd = startPos + myValue.length;
								this.scrollTop = scrollTop;
							}
							e.preventDefault();
						}
					});
	
				},
				insert: function() {
					var language = $('#redactor-insert-code-language').val();
					var data = $('#redactor-insert-code-area').val().replace(/</g,'&lt;').replace(/>/g,'&gt;');
	
					if (language != '' && language != 'none') data = '<pre class="brush: '+language+'">'+data+'</pre>';
					else data = '<pre>'+data+'</pre>';
	
					this.selection.restore();
					iModule.modal.close();
	
					var current = this.selection.getBlock() || this.selection.getCurrent();
	
					if (current) $(current).after(data);
					else {
						this.insert.html(data);
					}
	
					this.code.sync();
				}
			}
		},
		video: function() {
			return {
				reUrlYoutube: /https?:\/\/(?:[0-9A-Z-]+\.)?(?:youtu\.be\/|youtube\.com\S*[^\w\-\s])([\w\-]{11})(?=[^\w\-]|$)(?![?=&+%\w.-]*(?:['"][^<>]*>|<\/a>))[?=&+%\w.-]*/ig,
				reUrlVimeo: /https?:\/\/(www\.)?vimeo.com\/(\d+)($|\/)/,
				show: function() {
					var contentHtml = '<label>'+this.lang.get("video_html_code")+'</label>';
					contentHtml+= '<div class="inputBlock">'
									+ '<textarea id="redactor-insert-video-area" style="height: 160px; font-family:Menlo, Monaco, monospace, sans-serif, defaultFont;" class="textareaControl"></textarea>'
									+ '<div class="helpBlock"></div>'
								+ '</div>';
					
					var buttons = [{
						text:this.lang.get("cancel"),
						style:"default",
						click:function() {
							iModule.modal.close();
						}
					}];
					
					var submit = {
						text:this.lang.get("insert"),
						style:"submit",
						click:$.proxy(this.video.insert,this)
					};

					iModule.modal.show(this.lang.get("video"),contentHtml,submit,buttons);
					
					this.selection.save();
					$('#redactor-insert-video-area').focus();
	
				},
				insert: function() {
					var data = $('#redactor-insert-video-area').val();
	
					if (!data.match(/<iframe|<video/gi)) {
						data = this.clean.stripTags(data);
	
						// parse if it is link on youtube & vimeo
						var iframeStart = '<iframe style="width: 500px; height: 281px;" src="',
							iframeEnd = '" frameborder="0" allowfullscreen></iframe>';
	
						if (data.match(this.video.reUrlYoutube)) {
							data = data.replace(this.video.reUrlYoutube, iframeStart + '//www.youtube.com/embed/$1' + iframeEnd);
						}
						else if (data.match(this.video.reUrlVimeo)) {
							data = data.replace(this.video.reUrlVimeo, iframeStart + '//player.vimeo.com/video/$2' + iframeEnd);
						}
					}
	
					this.selection.restore();
					iModule.modal.close();
	
					var current = this.selection.getBlock() || this.selection.getCurrent();
	
					if (current) $(current).after(data);
					else {
						this.insert.html(data);
					}
	
					this.code.sync();
				}
	
			};
		},
		image: function() {
			return {
				show: function() {
					$("#"+this.$textarea.attr("id")+"-attachment input[name=wysiwyg_image]").trigger("click");
				},
				showEdit: function($image) {
					var contentHtml = '<label>'+this.lang.get("title")+'</label>';
					contentHtml+= '<div class="inputBlock">'
									+ '<input type="text" id="redactor-image-title" class="inputControl">'
									+ '<div class="helpBlock"></div>'
								+ '</div>';
					contentHtml+= '<div class="blank"></div>';
								
					contentHtml+= '<input type="hidden" id="redactor-image-align">';
					contentHtml+= '<label>'+this.lang.get("image_position")+'</label>';
					contentHtml+= '<div class="inputBlock">'
									+ '<div id="redactor-image-align-select" class="selectControl" data-field="#redactor-image-align">'
										+ '<button>'+this.lang.get("none")+' <span class="arrow"></span></button>'
										+ '<ul>'
											+ '<li data-value="none">'+this.lang.get("none")+'</li>'
											+ '<li data-value="left">'+this.lang.get("left")+'</li>'
											+ '<li data-value="center">'+this.lang.get("center")+'</li>'
											+ '<li data-value="right">'+this.lang.get("right")+'</li>'
										+ '</ul>'
									+ '</div>'
								+ '</div>';
					
					var buttons = [{
						text:this.lang.get("cancel"),
						style:"default",
						click:function() {
							iModule.modal.close();
						}
					},{
						text:this.lang.get("_delete"),
						style:"danger",
						click:$.proxy(function() { this.image.remove($image); },this)
					}];
					
					var submit = {
						text:this.lang.get("save"),
						style:"submit",
						click:$.proxy(function() { this.image.update($image); },this)
					};

					iModule.modal.show(this.lang.get("edit"),contentHtml,submit,buttons);
					
					$("#redactor-image-title").val($image.attr("alt"));

					var floatValue = ($image.css('display') == 'block' && $image.css('float') == 'none') ? 'center' : $image.css('float');
					$('#redactor-image-align').val(floatValue);
					
					iModule.initSelectControl($("#redactor-image-align-select"));
				},
				setFloating: function($image) {
					var floating = $('#redactor-image-align').val();

					var imageFloat = '';
					var imageDisplay = '';
					var imageMargin = '';

					switch (floating) {
						case 'left':
							imageFloat = 'left';
							imageMargin = '0 ' + this.opts.imageFloatMargin + ' ' + this.opts.imageFloatMargin + ' 0';
						break;
						case 'right':
							imageFloat = 'right';
							imageMargin = '0 0 ' + this.opts.imageFloatMargin + ' ' + this.opts.imageFloatMargin;
						break;
						case 'center':
							imageDisplay = 'block';
							imageMargin = 'auto';
						break;
					}

					$image.css({ 'float': imageFloat, display: imageDisplay, margin: imageMargin });
					$image.attr('rel', $image.attr('style'));
				},
				update: function($image) {
					this.image.hideResize();
					this.buffer.set();

					$image.attr('alt', $('#redactor-image-title').val());

					this.image.setFloating($image);

					iModule.modal.close();
					this.observe.images();
					this.code.sync();


				},
				setEditable: function($image) {
					if (this.opts.imageEditable) {
						$image.on('dragstart', $.proxy(this.image.onDrag, this, $image));
					}

					$image.on('mousedown', $.proxy(this.image.hideResize, this));
					$image.on('click.redactor touchstart', $.proxy(function(e) {
						this.observe.image = $image;

						if (this.$editor.find('#redactor-image-box').length !== 0) return false;

						this.image.resizer = this.image.loadEditableControls($image);

						$(document).on('click.redactor-image-resize-hide.' + this.uuid, $.proxy(this.image.hideResize, this));
						this.$editor.on('click.redactor-image-resize-hide.' + this.uuid, $.proxy(this.image.hideResize, this));

						// resize
						if (!this.opts.imageResizable) return;

						this.image.resizer.on('mousedown.redactor touchstart.redactor', $.proxy(function(e) {
							this.image.setResizable(e, $image);
						}, this));


					}, this));
				},
				setResizable: function(e, $image) {
					e.preventDefault();

					this.image.resizeHandle = {
						x : e.pageX,
						y : e.pageY,
						el : $image,
						ratio: $image.width() / $image.height(),
						h: $image.height()
					};

					e = e.originalEvent || e;

					if (e.targetTouches) {
						 this.image.resizeHandle.x = e.targetTouches[0].pageX;
						 this.image.resizeHandle.y = e.targetTouches[0].pageY;
					}

					this.image.startResize();


				},
				startResize: function() {
					$(document).on('mousemove.redactor-image-resize touchmove.redactor-image-resize', $.proxy(this.image.moveResize, this));
					$(document).on('mouseup.redactor-image-resize touchend.redactor-image-resize', $.proxy(this.image.stopResize, this));
				},
				moveResize: function(e) {
					e.preventDefault();

					e = e.originalEvent || e;

					var height = this.image.resizeHandle.h;

					if (e.targetTouches) height += (e.targetTouches[0].pageY -  this.image.resizeHandle.y);
					else height += (e.pageY -  this.image.resizeHandle.y);

					var width = Math.round(height * this.image.resizeHandle.ratio);

					if (height < 50 || width < 100) return;

					var height = Math.round(this.image.resizeHandle.el.width() / this.image.resizeHandle.ratio);

					this.image.resizeHandle.el.attr({width: width, height: height});
					this.image.resizeHandle.el.width(width);
					this.image.resizeHandle.el.height(height);

					this.code.sync();
				},
				stopResize: function() {
					this.handle = false;
					$(document).off('.redactor-image-resize');

					this.image.hideResize();
				},
				onDrag: function(e,$image) {
					this.$editor.on('drop.redactor-image-inside-drop', $.proxy(function() {
						e.attr("data-drag-origin","true");
						setTimeout($.proxy(this.image.onDrop, this), 1);

					}, this));
				},
				onDrop: function() {
					this.image.fixImageSourceAfterDrop();
					this.observe.images();
					this.$editor.off('drop.redactor-image-inside-drop');
					this.clean.clearUnverified();
					this.code.sync();
				},
				fixImageSourceAfterDrop: function() {
					this.image.remove(this.$editor.find('img[data-drag-origin]'));
				},
				hideResize: function(e) {
					if (e && $(e.target).closest('#redactor-image-box', this.$editor[0]).length !== 0) return;
					if (e && e.target.tagName == 'IMG') {
						var $image = $(e.target);
						$image.attr('data-save-url', $image.attr('src'));
					}

					var imageBox = this.$editor.find('#redactor-image-box');
					if (imageBox.length === 0) return;

					if (this.opts.imageEditable) {
						this.image.editter.remove();
					}

					$(this.image.resizer).remove();

					imageBox.find('img').css({
						marginTop: imageBox[0].style.marginTop,
						marginBottom: imageBox[0].style.marginBottom,
						marginLeft: imageBox[0].style.marginLeft,
						marginRight: imageBox[0].style.marginRight
					});

					imageBox.css('margin', '');
					imageBox.find('img').css('opacity', '');
					imageBox.replaceWith(function() {
						return $(this).contents();
					});

					$(document).off('click.redactor-image-resize-hide.' + this.uuid);
					this.$editor.off('click.redactor-image-resize-hide.' + this.uuid);

					if (typeof this.image.resizeHandle !== 'undefined') {
						this.image.resizeHandle.el.attr('rel', this.image.resizeHandle.el.attr('style'));
					}

					this.code.sync();

				},
				loadResizableControls: function($image, imageBox) {
					if (this.opts.imageResizable && !this.utils.isMobile()) {
						var imageResizer = $('<span id="redactor-image-resizer" data-redactor="verified"></span>');

						if (!this.utils.isDesktop()) {
							imageResizer.css({ width: '15px', height: '15px' });
						}

						imageResizer.attr('contenteditable', false);
						imageBox.append(imageResizer);
						imageBox.append($image);

						return imageResizer;
					}
					else {
						imageBox.append($image);
						return false;
					}
				},
				loadEditableControls: function($image) {
					var imageBox = $('<span id="redactor-image-box" data-redactor="verified">');
					imageBox.css('float', $image.css('float')).attr('contenteditable', false);

					if ($image[0].style.margin != 'auto') {
						imageBox.css({
							marginTop: $image[0].style.marginTop,
							marginBottom: $image[0].style.marginBottom,
							marginLeft: $image[0].style.marginLeft,
							marginRight: $image[0].style.marginRight
						});

						$image.css('margin', '');
					}
					else {
						imageBox.css({ 'display': 'block', 'margin': 'auto' });
					}

					$image.css('opacity', '.5').after(imageBox);


					if (this.opts.imageEditable) {
						// editter
						this.image.editter = $('<span id="redactor-image-editter" data-redactor="verified">' + this.lang.get('edit') + '</span>');
						this.image.editter.attr('contenteditable', false);
						this.image.editter.on('click', $.proxy(function() {
							this.image.showEdit($image);
						}, this));

						imageBox.append(this.image.editter);

						// position correction
						var editerWidth = this.image.editter.innerWidth();
						this.image.editter.css('margin-left', '-' + editerWidth/2 + 'px');
					}

					return this.image.loadResizableControls($image, imageBox);

				},
				remove: function(image) {
					var $image = $(image);
					var $link = $image.closest('a', this.$editor[0]);
					var $figure = $image.closest('figure', this.$editor[0]);
					var $parent = $image.parent();
					if ($('#redactor-image-box').length !== 0) {
						$parent = $('#redactor-image-box').parent();
					}

					var $next;
					if ($figure.length !== 0) {
						$next = $figure.next();
						$figure.remove();
					}
					else if ($link.length !== 0) {
						$parent = $link.parent();
						$link.remove();
					}
					else {
						$image.remove();
					}

					$('#redactor-image-box').remove();

					if ($figure.length !== 0) {
						this.caret.setStart($next);
					}
					else {
						this.caret.setStart($parent);
					}

					// delete callback
					this.core.setCallback('imageDelete', $image[0].src, $image);

					iModule.modal.close();
					this.code.sync();
				}
			};
		},
		indent: function() {
			return {
				increase: function() {
					// focus
					if (!this.utils.browser('msie')) this.$editor.focus();

					this.buffer.set();
					this.selection.save();

					var block = this.selection.getBlock();

					if (block && block.tagName == 'LI') {
						this.indent.increaseLists();
					}
					else if (block === false && this.opts.linebreaks) {
						this.indent.increaseText();
					}
					else {
						this.indent.increaseBlocks();
					}

					this.selection.restore();
					this.code.sync();
				},
				increaseLists: function() {
					document.execCommand('indent');

					this.indent.fixEmptyIndent();
					this.clean.normalizeLists();
					this.clean.clearUnverified();
				},
				increaseBlocks: function() {
					$.each(this.selection.getBlocks(), $.proxy(function(i, elem) {
						if (elem.tagName === 'TD' || elem.tagName === 'TH') return;

						var $el = this.utils.getAlignmentElement(elem);

						var left = this.utils.normalize($el.css('margin-left')) + this.opts.indentValue;
						$el.css('margin-left', left + 'px');

					}, this));
				},
				increaseText: function() {
					var wrapper = this.selection.wrap('div');
					$(wrapper).attr('data-tagblock', 'redactor');
					$(wrapper).css('margin-left', this.opts.indentValue + 'px');
				},
				decrease: function() {
					this.buffer.set();
					this.selection.save();

					var block = this.selection.getBlock();
					if (block && block.tagName == 'LI') {
						this.indent.decreaseLists();
					}
					else {
						this.indent.decreaseBlocks();
					}

					this.selection.restore();
					this.code.sync();
				},
				decreaseLists: function () {
					document.execCommand('outdent');

					var current = this.selection.getCurrent();

					var $item = $(current).closest('li', this.$editor[0]);
					var $parent = $item.parent();
					if ($item.length !== 0 && $parent.length !== 0 && $parent[0].tagName == 'LI') {
						$parent.after($item);
					}

					this.indent.fixEmptyIndent();

					if (!this.opts.linebreaks && $item.length === 0) {
						document.execCommand('formatblock', false, 'p');
						this.$editor.find('ul, ol, blockquote, p').each($.proxy(this.utils.removeEmpty, this));
					}

					this.clean.clearUnverified();
				},
				decreaseBlocks: function() {
					$.each(this.selection.getBlocks(), $.proxy(function(i, elem) {
						var $el = this.utils.getAlignmentElement(elem);
						var left = this.utils.normalize($el.css('margin-left')) - this.opts.indentValue;

						if (left <= 0) {
							if (this.opts.linebreaks && typeof($el.data('tagblock')) !== 'undefined') {
								$el.replaceWith($el.html() + '<br />');
							}
							else {
								$el.css('margin-left', '');
								this.utils.removeEmptyAttr($el, 'style');
							}
						}
						else {
							$el.css('margin-left', left + 'px');
						}

					}, this));
				},
				fixEmptyIndent: function() {
					var block = this.selection.getBlock();

					if (this.range.collapsed && block && block.tagName == 'LI' && this.utils.isEmpty($(block).text())) {
						var $block = $(block);
						$block.find('span').not('.redactor-selection-marker').contents().unwrap();
						$block.append('<br>');
					}
				}
			};
		},
		inline: function() {
			return {
				formatting: function(name) {
					var type, value;

					if (typeof this.formatting[name].style != 'undefined') type = 'style';
					else if (typeof this.formatting[name]['class'] != 'undefined') type = 'class';

					if (type) value = this.formatting[name][type];

					this.inline.format(this.formatting[name].tag, type, value);

				},
				format: function(tag, type, value) {
					// Stop formatting pre and headers
					if (this.utils.isCurrentOrParent('PRE') || this.utils.isCurrentOrParentHeader()) return;

					var tags = ['b', 'bold', 'i', 'italic', 'underline', 'strikethrough', 'deleted', 'superscript', 'subscript'];
					var replaced = ['strong', 'strong', 'em', 'em', 'u', 'del', 'del', 'sup', 'sub'];

					for (var i = 0; i < tags.length; i++) {
						if (tag == tags[i]) tag = replaced[i];
					}

					if (this.opts.allowedTags) {
						if ($.inArray(tag, this.opts.allowedTags) == -1) return;
					}
					else {
						if ($.inArray(tag, this.opts.deniedTags) !== -1) return;
					}

					this.inline.type = type || false;
					this.inline.value = value || false;

					this.buffer.set();

					if (!this.utils.browser('msie')) {
						this.$editor.focus();
					}

					this.selection.get();

					if (this.range.collapsed) {
						this.inline.formatCollapsed(tag);
					} else {
						this.inline.formatMultiple(tag);
					}
				},
				formatCollapsed: function(tag) {
					var current = this.selection.getCurrent();
					var $parent = $(current).closest(tag + '[data-redactor-tag=' + tag + ']', this.$editor[0]);

					// inline there is
					if ($parent.length !== 0 && (this.inline.type != 'style' && $parent[0].tagName != 'SPAN')) {
						// remove empty
						if (this.utils.isEmpty($parent.text())) {
							this.caret.setAfter($parent[0]);

							$parent.remove();
							this.code.sync();
						}
						else if (this.utils.isEndOfElement($parent)) {
							this.caret.setAfter($parent[0]);
						}

						return;
					}

					// create empty inline
					var node = $('<' + tag + '>').attr('data-verified', 'redactor').attr('data-redactor-tag', tag);
					node.html(this.opts.invisibleSpace);

					node = this.inline.setFormat(node);

					var node = this.insert.node(node);
					this.caret.setEnd(node);

					this.code.sync();
				},
				formatMultiple: function(tag) {
					this.inline.formatConvert(tag);
					this.selection.save();
					document.execCommand('strikethrough');
					this.$editor.find('strike').each($.proxy(function(i,s) {
						var $el = $(s);

						this.inline.formatRemoveSameChildren($el, tag);

						var $span;
						if (this.inline.type) {
							$span = $('<span>').attr('data-redactor-tag', tag).attr('data-verified', 'redactor');
							$span = this.inline.setFormat($span);
						}
						else {
							$span = $('<' + tag + '>').attr('data-redactor-tag', tag).attr('data-verified', 'redactor');
						}

						$el.replaceWith($span.html($el.contents()));

						if (tag == 'span') {
							var $parent = $span.parent();
							if ($parent && $parent[0].tagName == 'SPAN' && this.inline.type == 'style') {
								var arr = this.inline.value.split(';');

								for (var z = 0; z < arr.length; z++) {
									if (arr[z] === '') return;
									var style = arr[z].split(':');
									$parent.css(style[0], '');

									if (this.utils.removeEmptyAttr($parent, 'style')) {
										$parent.replaceWith($parent.contents());
									}

								}

							}
						}

					}, this));

					// clear text decoration
					if (tag != 'span') {
						this.$editor.find(this.opts.inlineTags.join(', ')).each($.proxy(function(i,s) {
							var $el = $(s);
							var property = $el.css('text-decoration');
							if (property == 'line-through') {
								$el.css('text-decoration', '');
								this.utils.removeEmptyAttr($el, 'style');
							}
						}, this));
					}

					if (tag != 'del') {
						var _this = this;
						this.$editor.find('inline').each(function(i,s) {
							_this.utils.replaceToTag(s, 'del');
						});
					}

					this.selection.restore();
					this.code.sync();

				},
				formatRemoveSameChildren: function($el, tag) {
					var self = this;
					$el.children(tag).each(function() {
						var $child = $(this);

						if (!$child.hasClass('redactor-selection-marker')) {
							if (self.inline.type == 'style') {
								var arr = self.inline.value.split(';');

								for (var z = 0; z < arr.length; z++) {
									if (arr[z] === '') return;

									var style = arr[z].split(':');
									$child.css(style[0], '');

									if (self.utils.removeEmptyAttr($child , 'style')) {
										$child.replaceWith($child.contents());
									}

								}
							}
							else {
								$child.contents().unwrap();
							}
						}

					});
				},
				formatConvert: function(tag) {
					this.selection.save();

					var find = '';
					if (this.inline.type == 'class') find = '[data-redactor-class=' + this.inline.value + ']';
					else if (this.inline.type == 'style') {
						find = '[data-redactor-style="' + this.inline.value + '"]';
					}

					var self = this;
					if (tag != 'del') {
						this.$editor.find('del').each(function(i,s) {
							self.utils.replaceToTag(s, 'inline');
						});
					}

					if (tag != 'span') {
						this.$editor.find(tag).each(function() {
							var $el = $(this);
							$el.replaceWith($('<strike />').html($el.contents()));

						});
					}

					this.$editor.find('[data-redactor-tag="' + tag + '"]' + find).each(function() {
						if (find === '' && tag == 'span' && this.tagName.toLowerCase() == tag) return;

						var $el = $(this);
						$el.replaceWith($('<strike />').html($el.contents()));

					});

					this.selection.restore();
				},
				setFormat: function(node) {
					switch (this.inline.type) {
						case 'class':

							if (node.hasClass(this.inline.value)) {
								node.removeClass(this.inline.value);
								node.removeAttr('data-redactor-class');
							}
							else {
								node.addClass(this.inline.value);
								node.attr('data-redactor-class', this.inline.value);
							}


						break;
						case 'style':

							node[0].style.cssText = this.inline.value;
							node.attr('data-redactor-style', this.inline.value);

						break;
					}

					return node;
				},
				removeStyle: function() {
					this.buffer.set();
					var current = this.selection.getCurrent();
					var nodes = this.selection.getInlines();

					this.selection.save();

					if (current && current.tagName === 'SPAN') {
						var $s = $(current);

						$s.removeAttr('style');
						if ($s[0].attributes.length === 0) {
							$s.replaceWith($s.contents());
						}
					}

					$.each(nodes, $.proxy(function(i,s) {
						var $s = $(s);
						if ($.inArray(s.tagName.toLowerCase(), this.opts.inlineTags) != -1 && !$s.hasClass('redactor-selection-marker')) {
							$s.removeAttr('style');
							if ($s[0].attributes.length === 0) {
								$s.replaceWith($s.contents());
							}
						}
					}, this));

					this.selection.restore();
					this.code.sync();

				},
				removeStyleRule: function(name) {
					this.buffer.set();
					var parent = this.selection.getParent();
					var nodes = this.selection.getInlines();

					this.selection.save();

					if (parent && parent.tagName === 'SPAN') {
						var $s = $(parent);

						$s.css(name, '');
						this.utils.removeEmptyAttr($s, 'style');
						if ($s[0].attributes.length === 0) {
							$s.replaceWith($s.contents());
						}
					}

					$.each(nodes, $.proxy(function(i,s) {
						var $s = $(s);
						if ($.inArray(s.tagName.toLowerCase(), this.opts.inlineTags) != -1 && !$s.hasClass('redactor-selection-marker')) {
							$s.css(name, '');
							this.utils.removeEmptyAttr($s, 'style');
							if ($s[0].attributes.length === 0) {
								$s.replaceWith($s.contents());
							}
						}
					}, this));

					this.selection.restore();
					this.code.sync();
				},
				removeFormat: function() {
					this.buffer.set();
					var current = this.selection.getCurrent();

					this.selection.save();

					document.execCommand('removeFormat');

					if (current && current.tagName === 'SPAN') {
						$(current).replaceWith($(current).contents());
					}


					$.each(this.selection.getNodes(), $.proxy(function(i,s) {
						var $s = $(s);
						if ($.inArray(s.tagName.toLowerCase(), this.opts.inlineTags) != -1 && !$s.hasClass('redactor-selection-marker')) {
							$s.replaceWith($s.contents());
						}
					}, this));

					this.selection.restore();
					this.code.sync();

				},
				toggleClass: function(className) {
					this.inline.format('span', 'class', className);
				},
				toggleStyle: function(value) {
					this.inline.format('span', 'style', value);
				}
			};
		},
		insert: function() {
			return {
				set: function(html, clean) {
					this.placeholder.remove();

					html = this.clean.setVerified(html);

					if (typeof clean == 'undefined') {
						html = this.clean.onPaste(html, false);
					}

					this.$editor.html(html);
					this.selection.remove();
					this.focus.setEnd();
					this.clean.normalizeLists();
					this.code.sync();
					this.observe.load();

					if (typeof clean == 'undefined') {
						setTimeout($.proxy(this.clean.clearUnverified, this), 10);
					}
				},
				text: function(text,isForce) {
					this.placeholder.remove();

					text = text.toString();
					if (isForce !== true) {
						text = $.trim(text);
						text = this.clean.getPlainText(text, false);
					}

					this.$editor.focus();

					if (this.utils.browser('msie')) {
						this.insert.htmlIe(text);
					}
					else {
						this.selection.get();

						this.range.deleteContents();
						var el = document.createElement("div");
						el.innerHTML = text;
						var frag = document.createDocumentFragment(), node, lastNode;
						while ((node = el.firstChild)) {
							lastNode = frag.appendChild(node);
						}

						this.range.insertNode(frag);

						if (lastNode) {
							var range = this.range.cloneRange();
							range.setStartAfter(lastNode);
							range.collapse(true);
							this.sel.removeAllRanges();
							this.sel.addRange(range);
						}
					}

					this.code.sync();
					this.clean.clearUnverified();
				},
				htmlWithoutClean: function(html) {
					this.insert.html(html, false);
				},
				html: function(html, clean) {
					this.placeholder.remove();

					if (typeof clean == 'undefined') clean = true;

					this.$editor.focus();

					html = this.clean.setVerified(html);

					if (clean) {
						html = this.clean.onPaste(html);
					}

					if (this.utils.browser('msie')) {
						this.insert.htmlIe(html);
					}
					else {
						if (this.clean.singleLine) this.insert.execHtml(html);
						else document.execCommand('insertHTML', false, html);

						this.insert.htmlFixMozilla();

					}

					this.clean.normalizeLists();

					// remove empty paragraphs finaly
					if (!this.opts.linebreaks) {
//						this.$editor.find('p').each($.proxy(this.utils.removeEmpty, this));
					}
					
					this.code.sync();
					this.observe.load();

					if (clean) {
						this.clean.clearUnverified();
					}

				},
				htmlFixMozilla: function() {
					// FF inserts empty p when content was selected dblclick
					if (!this.utils.browser('mozilla')) return;

					var $next = $(this.selection.getBlock()).next();
					if ($next.length > 0 && $next[0].tagName == 'P' && $next.html() === '') {
						$next.remove();
					}

				},
				htmlIe: function(html) {
					if (this.utils.isIe11()) {
						var parent = this.utils.isCurrentOrParent('P');
						var $html = $('<div>').append(html);
						var blocksMatch = $html.contents().is('p, :header, dl, ul, ol, div, table, td, blockquote, pre, address, section, header, footer, aside, article');

						if (parent && blocksMatch) this.insert.ie11FixInserting(parent, html);
						else this.insert.ie11PasteFrag(html);

						return;
					}

					document.selection.createRange().pasteHTML(html);

				},
				execHtml: function(html) {
					html = this.clean.setVerified(html);

					this.selection.get();

					this.range.deleteContents();

					var el = document.createElement('div');
					el.innerHTML = html;

					var frag = document.createDocumentFragment(), node, lastNode;
					while ((node = el.firstChild)) {
						lastNode = frag.appendChild(node);
					}

					this.range.insertNode(frag);

					this.range.collapse(true);
					this.caret.setAfter(lastNode);

				},
				node: function(node, deleteContents) {
					node = node[0] || node;

					var html = this.utils.getOuterHtml(node);
					html = this.clean.setVerified(html);

					if (html.match(/</g) !== null) {
						node = $(html)[0];
					}

					this.selection.get();

					if (deleteContents !== false) {
						this.range.deleteContents();
					}
					
					this.$editor.append(node);
					this.range.collapse(false);
					this.selection.addRange();

					return node;
				},
				nodeToPoint: function(node, x, y) {
					node = node[0] || node;

					this.selection.get();

					var range;
					if (document.caretPositionFromPoint) {
						var pos = document.caretPositionFromPoint(x, y);

						this.range.setStart(pos.offsetNode, pos.offset);
						this.range.collapse(true);
						this.range.insertNode(node);
					}
					else if (document.caretRangeFromPoint) {
						range = document.caretRangeFromPoint(x, y);
						range.insertNode(node);
					}
					else if (typeof document.body.createTextRange != "undefined") {
						range = document.body.createTextRange();
						range.moveToPoint(x, y);
						var endRange = range.duplicate();
						endRange.moveToPoint(x, y);
						range.setEndPoint("EndToEnd", endRange);
						range.select();
					}
				},
				nodeToCaretPositionFromPoint: function(e, node) {
					node = node[0] || node;

					var range;
					var x = e.clientX, y = e.clientY;
					if (document.caretPositionFromPoint) {
						var pos = document.caretPositionFromPoint(x, y);
						var sel = document.getSelection();
						range = sel.getRangeAt(0);
						range.setStart(pos.offsetNode, pos.offset);
						range.collapse(true);
						range.insertNode(node);
					}
					else if (document.caretRangeFromPoint) {
						range = document.caretRangeFromPoint(x, y);
						range.insertNode(node);
					}
					else if (typeof document.body.createTextRange != "undefined") {
						range = document.body.createTextRange();
						range.moveToPoint(x, y);
						var endRange = range.duplicate();
						endRange.moveToPoint(x, y);
						range.setEndPoint("EndToEnd", endRange);
						range.select();
					}

				},
				ie11FixInserting: function(parent, html) {
					var node = document.createElement('span');
					node.className = 'redactor-ie-paste';
					this.insert.node(node);

					var parHtml = $(parent).html();

					parHtml = '<p>' + parHtml.replace(/<span class="redactor-ie-paste"><\/span>/gi, '</p>' + html + '<p>') + '</p>';
					$(parent).replaceWith(parHtml);
				},
				ie11PasteFrag: function(html) {
					this.selection.get();
					this.range.deleteContents();

					var el = document.createElement("div");
					el.innerHTML = html;

					var frag = document.createDocumentFragment(), node, lastNode;
					while ((node = el.firstChild)) {
						lastNode = frag.appendChild(node);
					}

					this.range.insertNode(frag);
					this.range.collapse(false);
					this.selection.addRange();
				}
			};
		},
		keydown: function() {
			return {
				init: function(e) {
					if (this.rtePaste) return;

					var key = e.which;
					var arrow = (key >= 37 && key <= 40);

					this.keydown.ctrl = e.ctrlKey || e.metaKey;
					this.keydown.current = this.selection.getCurrent();
					this.keydown.parent = this.selection.getParent();
					this.keydown.block = this.selection.getBlock();

					// detect tags
					this.keydown.pre = this.utils.isTag(this.keydown.current, 'pre');
					this.keydown.blockquote = this.utils.isTag(this.keydown.current, 'blockquote');
					this.keydown.figcaption = this.utils.isTag(this.keydown.current, 'figcaption');

					// shortcuts setup
					this.shortcuts.init(e, key);

					this.keydown.checkEvents(arrow, key);
					this.keydown.setupBuffer(e, key);
					this.keydown.addArrowsEvent(arrow);
					this.keydown.setupSelectAll(e, key);

					// callback
					var keydownStop = this.core.setCallback('keydown', e);
					if (keydownStop === false) {
						e.preventDefault();
						return false;
					}

					// ie and ff exit from table
					if (this.opts.enterKey && (this.utils.browser('msie') || this.utils.browser('mozilla')) && (key === this.keyCode.DOWN || key === this.keyCode.RIGHT)) {
						var isEndOfTable = false;
						var $table = false;
						if (this.keydown.block && this.keydown.block.tagName === 'TD') {
							$table = $(this.keydown.block).closest('table', this.$editor[0]);
						}

						if ($table && $table.find('td').last()[0] === this.keydown.block) {
							isEndOfTable = true;
						}

						if (this.utils.isEndOfElement() && isEndOfTable) {
							var node = $(this.opts.emptyHtml);
							$table.after(node);
							this.caret.setStart(node);
						}
					}
					
					if ($(this.selection.getCurrent()).parents("a[data-idx]").length > 0) {
						if (key === this.keyCode.DELETE || key === this.keyCode.BACKSPACE) {
							e.preventDefault();
							$(this.selection.getCurrent()).parents("a[data-idx]").remove();
						} else if (key == this.keyCode.LEFT) {
							e.preventDefault();
							this.caret.setBefore($(this.selection.getCurrent()).parents("a[data-idx]"));
						} else if (key == this.keyCode.RIGHT) {
							e.preventDefault();
							this.caret.setAfter($(this.selection.getCurrent()).parents("a[data-idx]"));
						} else if (key !== this.keyCode.UP && key !== this.keyCode.DOWN) {
							e.preventDefault();
						}
					}

					// down
					if (this.opts.enterKey && key === this.keyCode.DOWN) {
						this.keydown.onArrowDown();
					}
					
					// turn off enter key
					if (!this.opts.enterKey && key === this.keyCode.ENTER) {
						e.preventDefault();
						// remove selected
						if (!this.range.collapsed) this.range.deleteContents();
						return;
					}

					// on enter
					if (key == this.keyCode.ENTER && !e.shiftKey && !e.ctrlKey && !e.metaKey) {
						var stop = this.core.setCallback('enter', e);
						if (stop === false) {
							e.preventDefault();
							return false;
						}

						if (this.keydown.blockquote && this.keydown.exitFromBlockquote(e) === true) {
							return false;
						}

						var current, $next;
						if (this.keydown.pre) {
							return this.keydown.insertNewLine(e);
						}
						else if (this.keydown.blockquote || this.keydown.figcaption) {
							current = this.selection.getCurrent();
							$next = $(current).next();

							if ($next.length !== 0 && $next[0].tagName == 'BR') {
								return this.keydown.insertBreakLine(e);
							}
							else if (this.utils.isEndOfElement() && (current && current != 'SPAN')) {
								return this.keydown.insertDblBreakLine(e);
							}
							else {
								return this.keydown.insertBreakLine(e);
							}
						}
						else if (this.opts.linebreaks && !this.keydown.block) {
							current = this.selection.getCurrent();
							$next = $(this.keydown.current).next();

							if ($next.length !== 0 && $next[0].tagName == 'BR') {
								return this.keydown.insertBreakLine(e);
							}
							else if (current !== false && $(current).hasClass('redactor-invisible-space')) {
								this.caret.setAfter(current);
								$(current).contents().unwrap();

								return this.keydown.insertDblBreakLine(e);
							}
							else {
								if (this.utils.isEndOfEditor()) {
									return this.keydown.insertDblBreakLine(e);
								}
								else if ($next.length === 0 && current === false && typeof $next.context != 'undefined') {
									return this.keydown.insertBreakLine(e);
								}

								return this.keydown.insertBreakLine(e);
							}
						}
						else if (this.opts.linebreaks && this.keydown.block) {
							setTimeout($.proxy(this.keydown.replaceDivToBreakLine, this), 1);
						}
						// paragraphs
						else if (!this.opts.linebreaks && this.keydown.block) {
							if (this.keydown.block.tagName !== 'LI') {
								setTimeout($.proxy(this.keydown.replaceDivToParagraph, this), 1);
							}
							else {
								current = this.selection.getCurrent();
								var $parent = $(current).closest('li', this.$editor[0]);
								var $list = $parent.closest('ul,ol', this.$editor[0]);

								if ($parent.length !== 0 && this.utils.isEmpty($parent.html()) && $list.next().length === 0 && this.utils.isEmpty($list.find("li").last().html())) {
									$list.find("li").last().remove();

									var node = $(this.opts.emptyHtml);
									$list.after(node);
									this.caret.setStart(node);

									return false;
								}
							}
						}
						else if (!this.opts.linebreaks && !this.keydown.block) {
							return this.keydown.insertParagraph(e);
						}
					}

					// Shift+Enter or Ctrl+Enter
					if (key === this.keyCode.ENTER && (e.ctrlKey || e.shiftKey)) {
						return this.keydown.onShiftEnter(e);
					}


					// tab or cmd + [
					if (key === this.keyCode.TAB || e.metaKey && key === 221 || e.metaKey && key === 219) {
						return this.keydown.onTab(e, key);
					}

					// image delete and backspace
					if (key === this.keyCode.BACKSPACE || key === this.keyCode.DELETE) {
						if (this.utils.browser('mozilla') && this.keydown.current && this.keydown.current.tagName === 'TD') {
							e.preventDefault();
							return false;
						}

						var nodes = this.selection.getNodes();
						if (nodes) {
							var len = nodes.length;
							var last;
							for (var i = 0; i < len; i++) {
								var children = $(nodes[i]).children('img');
								if (children.length !== 0) {
									var self = this;
									$.each(children, function(z,s) {
										var $s = $(s);
										if ($s.css('float') != 'none') return;

										// image delete callback
										self.core.setCallback('imageDelete', s.src, $s);
										last = s;
									});
								}
								else if (nodes[i].tagName == 'IMG') {
									if (last != nodes[i]) {
										// image delete callback
										this.core.setCallback('imageDelete', nodes[i].src, $(nodes[i]));
										last = nodes[i];
									}
								}
							}
						}
					}

					// backspace
					if (key === this.keyCode.BACKSPACE) {
						this.keydown.removeInvisibleSpace();
						this.keydown.removeEmptyListInTable(e);
					}

					this.code.sync();
				},
				checkEvents: function(arrow, key) {
					if (!arrow && (this.core.getEvent() == 'click' || this.core.getEvent() == 'arrow')) {
						this.core.addEvent(false);

						if (this.keydown.checkKeyEvents(key)) {
							this.buffer.set();
						}
					}
				},
				checkKeyEvents: function(key) {
					var k = this.keyCode;
					var keys = [k.BACKSPACE, k.DELETE, k.ENTER, k.SPACE, k.ESC, k.TAB, k.CTRL, k.META, k.ALT, k.SHIFT];

					return ($.inArray(key, keys) == -1) ? true : false;

				},
				addArrowsEvent: function(arrow) {
					if (!arrow) return;

					if ((this.core.getEvent() == 'click' || this.core.getEvent() == 'arrow')) {
						this.core.addEvent(false);
						return;
					}

					this.core.addEvent('arrow');
				},
				setupBuffer: function(e, key) {
					if (this.keydown.ctrl && key === 90 && !e.shiftKey && !e.altKey && this.opts.buffer.length) {
						e.preventDefault();
						this.buffer.undo();
						return;
					}
					// undo
					else if (this.keydown.ctrl && key === 90 && e.shiftKey && !e.altKey && this.opts.rebuffer.length !== 0) {
						e.preventDefault();
						this.buffer.redo();
						return;
					}
					else if (!this.keydown.ctrl) {
						if (key == this.keyCode.BACKSPACE || key == this.keyCode.DELETE || (key == this.keyCode.ENTER && !e.ctrlKey && !e.shiftKey) || key == this.keyCode.SPACE) {
							this.buffer.set();
						}
					}
				},
				setupSelectAll: function(e, key) {
					if (this.keydown.ctrl && key === 65) {
						this.utils.enableSelectAll();
					}
					else if (key != this.keyCode.LEFT_WIN && !this.keydown.ctrl) {
						this.utils.disableSelectAll();
					}
				},
				onArrowDown: function() {
					var tags = [this.keydown.blockquote, this.keydown.pre, this.keydown.figcaption];

					for (var i = 0; i < tags.length; i++) {
						if (tags[i]) {
							this.keydown.insertAfterLastElement(tags[i]);
							return false;
						}
					}
				},
				onShiftEnter: function(e) {
					this.buffer.set();

					if (this.utils.isEndOfElement()) {
						return this.keydown.insertDblBreakLine(e);
					}

					return this.keydown.insertBreakLine(e);
				},
				onTab: function(e, key) {
					if (!this.opts.tabKey) return true;
					if (this.utils.isEmpty(this.code.get()) && this.opts.tabAsSpaces === false) return true;

					e.preventDefault();

					var node;
					if (this.keydown.pre && !e.shiftKey) {
						this.insert.text('\t',true);
						this.code.sync();
					}
					else if (false && this.opts.tabAsSpaces !== false) {
						node = document.createTextNode(Array(this.opts.tabAsSpaces + 1).join('\u00a0'));
						this.insert.node(node);
						this.code.sync();
					}
					else {
						if (e.metaKey && key === 219) this.indent.decrease();
						else if (e.metaKey && key === 221) this.indent.increase();
						else if (!e.shiftKey) this.indent.increase();
						else this.indent.decrease();
					}

					return false;
				},
				replaceDivToBreakLine: function() {
					var blockElem = this.selection.getBlock();
					var blockHtml = blockElem.innerHTML.replace(/<br\s?\/?>/gi, '');
					if ((blockElem.tagName === 'DIV' || blockElem.tagName === 'P') && blockHtml === '' && !$(blockElem).hasClass('redactor-editor')) {
						var br = document.createElement('br');

						$(blockElem).replaceWith(br);
						this.caret.setBefore(br);

						this.code.sync();

						return false;
					}
				},
				replaceDivToParagraph: function() {
					var blockElem = this.selection.getBlock();
					var blockHtml = blockElem.innerHTML.replace(/<br\s?\/?>/gi, '');
					if (blockElem.tagName === 'DIV' && blockHtml === '' && !$(blockElem).hasClass('redactor-editor')) {
						var p = document.createElement('p');
						p.innerHTML = this.opts.invisibleSpace;

						$(blockElem).replaceWith(p);
						this.caret.setStart(p);

						this.code.sync();

						return false;
					}
					else if (this.opts.cleanStyleOnEnter && blockElem.tagName == 'P') {
						$(blockElem).removeAttr('class').removeAttr('style');
					}
				},
				insertParagraph: function(e) {
					e.preventDefault();

					this.selection.get();

					var p = document.createElement('p');
					p.innerHTML = this.opts.invisibleSpace;

					this.range.deleteContents();
					this.range.insertNode(p);

					this.caret.setStart(p);

					this.code.sync();

					return false;
				},
				exitFromBlockquote: function(e) {
					if (!this.utils.isEndOfElement()) return;

					var tmp = $.trim($(this.keydown.block).html());
					if (tmp.search(/(<br\s?\/?>){2}$/i) != -1) {
						e.preventDefault();

						if (this.opts.linebreaks) {
							var br = document.createElement('br');
							$(this.keydown.blockquote).after(br);

							this.caret.setBefore(br);
							$(this.keydown.block).html(tmp.replace(/<br\s?\/?>$/i, ''));
						}
						else {
							var node = $(this.opts.emptyHtml);
							$(this.keydown.blockquote).after(node);
							this.caret.setStart(node);
						}

						return true;

					}

					return;

				},
				insertAfterLastElement: function(element) {
					if (!this.utils.isEndOfElement()) return;

					this.buffer.set();

					if (this.opts.linebreaks) {
						var contents = $('<div>').append($.trim(this.$editor.html())).contents();
						var last = contents.last()[0];
						if (last.tagName == 'SPAN' && last.innerHTML === '') {
							last = contents.prev()[0];
						}

						if (this.utils.getOuterHtml(last) != this.utils.getOuterHtml(element)) return;

						var br = document.createElement('br');
						$(element).after(br);
						this.caret.setAfter(br);

					}
					else {
						if (this.$editor.contents().last()[0] !== element) return;

						var node = $(this.opts.emptyHtml);
						$(element).after(node);
						this.caret.setStart(node);
					}
				},
				insertNewLine: function(e) {
					e.preventDefault();

					var node = document.createTextNode('\n');

					this.selection.get();

					this.range.deleteContents();
					this.range.insertNode(node);

					this.caret.setAfter(node);

					this.code.sync();
					
					return false;
				},
				insertBreakLine: function(e) {
					return this.keydown.insertBreakLineProcessing(e);
				},
				insertDblBreakLine: function(e) {
					return this.keydown.insertBreakLineProcessing(e, true);
				},
				insertBreakLineProcessing: function(e, dbl) {
					e.stopPropagation();

					this.selection.get();

					var br1 = document.createElement('br');

					if (this.utils.browser('msie')) {
						this.range.collapse(false);
						this.range.setEnd(this.range.endContainer, this.range.endOffset);
					}
					else {
						this.range.deleteContents();
					}

					this.range.insertNode(br1);

					// move br outside A tag
					var $parentA = $(br1).parent("a");

					if ($parentA.length > 0) {
						$parentA.find(br1)
								.remove();

						$parentA.after(br1);
					}

					if (dbl === true) {
						var $next = $(br1).next();
						if ($next.length !== 0 && $next[0].tagName === 'BR' && this.utils.isEndOfEditor()) {
							this.caret.setAfter(br1);
							this.code.sync();
							return false;
						}

						var br2 = document.createElement('br');

						this.range.insertNode(br2);
						this.caret.setAfter(br2);
					}
					else {
						this.keydown.insertBreakLineProcessingAfter(br1);
					}

					this.code.sync();
					return false;
				},
				insertBreakLineProcessingAfter: function(node) {
					var space = this.utils.createSpaceElement();
					$(node).after(space);
					this.selection.selectElement(space);

					$(space).replaceWith(function() {
						return $(this).contents();
					});
				},
				removeInvisibleSpace: function() {
					var $current = $(this.keydown.current);
					if ($current.text().search(/^\u200B$/g) === 0) {
						$current.remove();
					}
				},
				removeEmptyListInTable: function(e) {
					var $current = $(this.keydown.current);
					var $parent = $(this.keydown.parent);
					var td = $current.closest('td', this.$editor[0]);

					if (td.length !== 0 && $current.closest('li', this.$editor[0]) && $parent.children('li').length === 1) {
						if (!this.utils.isEmpty($current.text())) return;

						e.preventDefault();

						$current.remove();
						$parent.remove();

						this.caret.setStart(td);
					}
				}
			};
		},
		keyup: function() {
			return {
			init: function(e) {
					if (this.rtePaste) return;

					var key = e.which;

					this.keyup.current = this.selection.getCurrent();
					this.keyup.parent = this.selection.getParent();
					var $parent = this.utils.isRedactorParent($(this.keyup.parent).parent());

					// callback
					var keyupStop = this.core.setCallback('keyup', e);
					if (keyupStop === false) {
						e.preventDefault();
						return false;
					}

					// replace to p before / after the table or body
					if (!this.opts.linebreaks && this.keyup.current.nodeType == 3 && this.keyup.current.length <= 1 && (this.keyup.parent === false || this.keyup.parent.tagName == 'BODY')) {
						this.keyup.replaceToParagraph();
					}

					// replace div after lists
					if (!this.opts.linebreaks && this.utils.isRedactorParent(this.keyup.current) && this.keyup.current.tagName === 'DIV') {
						this.keyup.replaceToParagraph(false);
					}


					if (!this.opts.linebreaks && $(this.keyup.parent).hasClass('redactor-invisible-space') && ($parent === false || $parent[0].tagName == 'BODY')) {
						$(this.keyup.parent).contents().unwrap();
						this.keyup.replaceToParagraph();
					}

					// linkify
					if (this.linkify.isEnabled() && this.linkify.isKey(key))
						this.linkify.format();

					if (key === this.keyCode.DELETE || key === this.keyCode.BACKSPACE) {
						// clear unverified
						this.clean.clearUnverified();

						if (this.observe.image) {
							e.preventDefault();

							this.image.hideResize();

							this.buffer.set();
							this.image.remove(this.observe.image);
							this.observe.image = false;

							return false;
						}

						// remove empty paragraphs
						this.$editor.find('p').each($.proxy(function(i, s) {
							this.utils.removeEmpty(i, $(s).html());
						}, this));

						// remove invisible space
						if (this.opts.linebreaks && this.keyup.current && this.keyup.current.tagName == 'DIV' && this.utils.isEmpty(this.keyup.current.innerHTML)) {
							$(this.keyup.current).after(this.selection.getMarkerAsHtml());
							this.selection.restore();
							$(this.keyup.current).remove();
						}

						// if empty
						return this.keyup.formatEmpty(e);
					}
				},
				replaceToParagraph: function(clone) {
					var $current = $(this.keyup.current);

					var node;
					if (clone === false) {
						node = $('<p>').append($current.html());
					}
					else {
						node = $('<p>').append($current.clone());
					}

					$current.replaceWith(node);
					var next = $(node).next();
					if (typeof(next[0]) !== 'undefined' && next[0].tagName == 'BR') {
						next.remove();
					}

					this.caret.setEnd(node);
				},
				formatEmpty: function(e) {
					var html = $.trim(this.$editor.html());

					if (!this.utils.isEmpty(html)) return;

					e.preventDefault();

					if (this.opts.linebreaks) {
						this.$editor.html(this.selection.getMarkerAsHtml());
						this.selection.restore();
					}
					else {
						html = '<p><br /></p>';

						this.$editor.html(html);
						this.focus.setStart();
					}

					this.code.sync();

					return false;
				}
			};
		},
		lang: function() {
			return {
				load: function() {
					this.opts.curLang = this.opts.langs[this.opts.lang];
				},
				get: function(name) {
					return (typeof this.opts.curLang[name] != 'undefined') ? this.opts.curLang[name] : '';
				}
			};
		},
		line: function() {
			return {
				insert: function() {
					this.buffer.set();

					var blocks = this.selection.getBlocks();
 					if (blocks[0] !== false && this.line.isExceptLastOrFirst(blocks)) {
	 					if (!this.utils.browser('msie')) this.$editor.focus();
	 					return;
					}

					if (this.utils.browser('msie')) {
						this.line.insertInIe();
					}
					else {
						this.line.insertInOthersBrowsers();
					}
				},
				isExceptLastOrFirst: function(blocks) {
					var exceptTags = ['li', 'td', 'th', 'blockquote', 'figcaption', 'pre', 'dl', 'dt', 'dd'];

					var first = blocks[0].tagName.toLowerCase();
					var last = this.selection.getLastBlock();

					last = (typeof last == 'undefined') ? first : last.tagName.toLowerCase();

					var firstFound = $.inArray(first, exceptTags) != -1;
					var lastFound = $.inArray(last, exceptTags) != -1;

					if ((firstFound && lastFound) || firstFound) {
						return true;
					}
				},
				insertInIe: function() {
					this.utils.saveScroll();
					this.buffer.set();

					this.insert.node(document.createElement('hr'));

					this.utils.restoreScroll();
					this.code.sync();
				},
				insertInOthersBrowsers: function() {
					this.buffer.set();

					var extra = '<p id="redactor-insert-line"><br /></p>';
					if (this.opts.linebreaks) extra = '<br id="redactor-insert-line">';

					document.execCommand('insertHTML', false, '<hr>' + extra);

					this.line.setFocus();
					this.code.sync();
				},
				setFocus: function() {
					var node = this.$editor.find('#redactor-insert-line');
					var next = $(node).next()[0];

					if (next) {
						this.caret.setAfter(node);
						node.remove();
					}
					else {
						node.removeAttr('id');
					}
				}
			};
		},
		fontcolor: function() {
			return {
				buildPicker: function(name,e) {//$dropdown, name, colors) {
					var button = this.button.get(name);
					if (button.hasClass('dropact')) {
						this.dropdown.hide(e);
						e.preventDefault();
					}
					
					var $dropdown = $('<div class="redactor-dropdown redactor-dropdown-' + this.uuid + ' redactor-dropdown-box-' + name + '" style="display: none;">');
					button.data('dropdown', $dropdown);
					
					var colors = [
						'#ffffff', '#000000', '#eeece1', '#1f497d', '#4f81bd', '#c0504d', '#9bbb59', '#8064a2', '#4bacc6', '#f79646', '#ffff00',
						'#f2f2f2', '#7f7f7f', '#ddd9c3', '#c6d9f0', '#dbe5f1', '#f2dcdb', '#ebf1dd', '#e5e0ec', '#dbeef3', '#fdeada', '#fff2ca',
						'#d8d8d8', '#595959', '#c4bd97', '#8db3e2', '#b8cce4', '#e5b9b7', '#d7e3bc', '#ccc1d9', '#b7dde8', '#fbd5b5', '#ffe694',
						'#bfbfbf', '#3f3f3f', '#938953', '#548dd4', '#95b3d7', '#d99694', '#c3d69b', '#b2a2c7', '#b7dde8', '#fac08f', '#f2c314',
						'#a5a5a5', '#262626', '#494429', '#17365d', '#366092', '#953734', '#76923c', '#5f497a', '#92cddc', '#e36c09', '#c09100',
						'#7f7f7f', '#0c0c0c', '#1d1b10', '#0f243e', '#244061', '#632423', '#4f6128', '#3f3151', '#31859b',  '#974806', '#7f6000'
					];
					
					var rule = (name == 'backcolor') ? 'background-color' : 'color';
	
					var len = colors.length;
					var self = this;
					var func = function(e) {
						e.preventDefault();
						self.fontcolor.set($(this).data('rule'), $(this).attr('rel'));
					};
	
					for (var z = 0; z < len; z++) {
						var color = colors[z];
	
						var $swatch = $('<a rel="' + color + '" data-rule="' + rule +'" href="#" style="float: left; font-size: 0; border: 2px solid #fff; padding: 0; margin: 0; width: 18px; height: 18px;"></a>');
						$swatch.css('background-color', color);
						$swatch.on('click', func);
	
						$dropdown.append($swatch);
					}
	
					var $elNone = $('<a href="#" style="display: block; clear: both; padding: 5px; font-size: 12px; line-height: 1;"></a>').html(this.lang.get('none'));
					$elNone.on('click', $.proxy(function(e) {
						e.preventDefault();
						this.fontcolor.remove(rule);
	
					}, this));
	
					$dropdown.append($elNone);
					$dropdown.width(242);
					this.dropdown.show(e,name);
				},
				set: function(rule, type) {
					this.inline.format('span', 'style', rule + ': ' + type + ';');
				},
				remove: function(rule) {
					this.inline.removeStyleRule(rule);
				}
			};
		},
		link: function() {
			return {
				show: function(e) {
					if (typeof e != 'undefined' && e.preventDefault) e.preventDefault();

					var contentHtml = '<label>'+this.lang.get('web')+'</label>';
					contentHtml+= '<div class="inputBlock">'
									+ '<input type="text" id="redactor-link-url" class="inputControl">'
									+ '<div class="helpBlock"></div>'
								+ '</div>';
					contentHtml+= '<div class="blank"></div>';
					contentHtml+= '<label>'+this.lang.get('text')+'</label>';
					contentHtml+= '<div class="inputBlock">'
									+ '<input type="text" id="redactor-link-url-text" class="inputControl">'
									+ '<div class="helpBlock"></div>'
								+ '</div>';
					
					var buttons = [{
						text:this.lang.get("cancel"),
						style:"default",
						click:function() {
							iModule.modal.close();
						}
					}];
					
					var submit = {
						text:this.lang.get("insert"),
						style:"submit",
						click:$.proxy(this.link.insert,this)
					};

					iModule.modal.show(this.lang.get("link_insert"),contentHtml,submit,buttons);
					
					this.selection.get();

					this.link.getData();
					this.link.cleanUrl();

					this.link.$inputUrl = $('#redactor-link-url');
					this.link.$inputText = $('#redactor-link-url-text');

					this.link.$inputText.val(this.link.text);
					this.link.$inputUrl.val(this.link.url);
					
					// hide link's tooltip
					$('.redactor-link-tooltip').remove();

					// show modal
					this.selection.save();
					this.link.$inputUrl.focus();
				},
				cleanUrl: function() {
					var thref = self.location.href.replace(/\/$/i, '');

					if (typeof this.link.url !== "undefined") {
						this.link.url = this.link.url.replace(thref, '');
						this.link.url = this.link.url.replace(/^\/#/, '#');
						this.link.url = this.link.url.replace('mailto:', '');

						// remove host from href
						if (!this.opts.linkProtocol) {
							var re = new RegExp('^(http|ftp|https)://' + self.location.host, 'i');
							this.link.url = this.link.url.replace(re, '');
						}
					}
				},
				getData: function() {
					this.link.$node = false;

					var $el = $(this.selection.getCurrent()).closest('a', this.$editor[0]);
					if ($el.length !== 0 && $el[0].tagName === 'A') {
						this.link.$node = $el;

						this.link.url = $el.attr('href');
						this.link.text = $el.text();
					}
					else {
						this.link.text = this.sel.toString();
						this.link.url = '';
					}

				},
				insert: function() {
					this.placeholder.remove();

					var link = this.link.$inputUrl.val();
					var text = this.link.$inputText.val();

					if ($.trim(link) === '') {
						this.link.$inputUrl.addClass('redactor-input-error').on('keyup', function() {
							$(this).removeClass('redactor-input-error');
							$(this).off('keyup');

						});

						return;
					}

					// mailto
					if (link.search('@') != -1 && /(http|ftp|https):\/\//i.test(link) === false) {
						link = 'mailto:' + link;
					}
					// url, not anchor
					else if (link.search('#') !== 0) {

						// test url (add protocol)
						var pattern = '((xn--)?[a-z0-9]+(-[a-z0-9]+)*\\.)+[a-z]{2,}';
						var re = new RegExp('^(http|ftp|https)://' + pattern, 'i');
						var re2 = new RegExp('^' + pattern, 'i');
						var re3 = new RegExp('\.(html|php)$', 'i');
						if (link.search(re) == -1 && link.search(re3) == -1 && link.search(re2) === 0 && this.opts.linkProtocol) {
							link = this.opts.linkProtocol + '://' + link;
						}
					}

					this.link.set(text, link);
					iModule.modal.close();
				},
				set: function(text, link) {
					text = $.trim(text.replace(/<|>/g, ''));

					this.selection.restore();

					if (text === '' && link === '') return;
					if (text === '' && link !== '') text = link;

					if (this.link.$node) {
						this.buffer.set();

						var $link = this.link.$node,
							$el   = $link.children();

						if ($el.length > 0) {
							while ($el.length) {
								$el = $el.children();
							}

							$el = $el.end();
						}
						else {
							$el = $link;
						}

						$link.attr('href', link);
						$el.text(text);

						$link.removeAttr('target');

						this.selection.selectElement($link);

						this.code.sync();
					}
					else {
						if (this.utils.browser('mozilla') && this.link.text === '') {
							var $a = $('<a />').attr('href', link).text(text);

							this.insert.node($a);
							this.selection.selectElement($a);
						}
						else {
							var $a;
							if (this.utils.browser('msie')) {
								$a = $('<a href="' + link + '">').text(text);

								$a = $(this.insert.node($a));

								if (this.selection.getText().match(/\s$/)) {
									$a.after(" ");
								}

								this.selection.selectElement($a);
							}
							else {
								document.execCommand('createLink', false, link);

								$a = $(this.selection.getCurrent()).closest('a', this.$editor[0]);
								if (this.utils.browser('mozilla')) {
									$a = $('a[_moz_dirty=""]');
								}

								$a.removeAttr('style').removeAttr('_moz_dirty');

								if (this.selection.getText().match(/\s$/)) {
									$a.after(" ");
								}

								if (this.link.text !== '' || this.link.text != text) {
									$a.text(text);

									this.selection.selectElement($a);
								}
							}
						}

						this.code.sync();
						this.core.setCallback('insertedLink', $a);

					}

					// link tooltip
					setTimeout($.proxy(function() {
						this.observe.links();

					}, this), 5);
				},
				unlink: function(e) {
					if (typeof e != 'undefined' && e.preventDefault) {
						e.preventDefault();
					}

					var nodes = this.selection.getNodes();
					if (!nodes) return;

					this.buffer.set();

					var len = nodes.length;
					for (var i = 0; i < len; i++) {
						var $node = $(nodes[i]).closest('a', this.$editor[0]);
						$node.replaceWith($node.contents());
					}

					// hide link's tooltip
					$('.redactor-link-tooltip').remove();

					this.code.sync();

				},
				toggleClass: function(className) {
					this.link.setClass(className, 'toggleClass');
				},
				addClass: function(className) {
					this.link.setClass(className, 'addClass');
				},
				removeClass: function(className) {
					this.link.setClass(className, 'removeClass');
				},
				setClass: function(className, func) {
					var links = this.selection.getInlinesTags(['a']);
					if (links === false) return;

					$.each(links, function() {
						$(this)[func](className);
					});
				}
			};
		},
		list: function() {
			return {
				toggle: function(cmd) {
					this.placeholder.remove();
					if (!this.utils.browser('msie')) this.$editor.focus();

					this.buffer.set();
					this.selection.save();

					var parent = this.selection.getParent();
					var $list = $(parent).closest('ol, ul', this.$editor[0]);

					if (!this.utils.isRedactorParent($list) && $list.length !== 0) {
						$list = false;
					}

					var isUnorderedCmdOrdered, isOrderedCmdUnordered;
					var remove = false;
					if ($list && $list.length) {
						remove = true;
						var listTag = $list[0].tagName;

						isUnorderedCmdOrdered = (cmd === 'orderedlist' && listTag === 'UL');
						isOrderedCmdUnordered = (cmd === 'unorderedlist' && listTag === 'OL');
					}

					if (isUnorderedCmdOrdered) {
						this.utils.replaceToTag($list, 'ol');
					}
					else if (isOrderedCmdUnordered) {
						this.utils.replaceToTag($list, 'ul');
					}
					else {
						if (remove) {
							this.list.remove(cmd);
						}
						else {
							this.list.insert(cmd);
						}
					}

					this.selection.restore();
					this.code.sync();
				},
				insert: function(cmd) {
					var parent = this.selection.getParent();
					var current = this.selection.getCurrent();
					var $td = $(current).closest('td, th', this.$editor[0]);

					if (this.utils.browser('msie') && this.opts.linebreaks) {
						this.list.insertInIe(cmd);
					}
					else {
						document.execCommand('insert' + cmd);
					}

					var $list = $(this.selection.getParent()).closest('ol, ul', this.$editor[0]);

					if ($td.length !== 0) {
						var prev = $td.prev();
						var html = $td.html();
						$td.html('');
						if (prev && prev.length === 1 && (prev[0].tagName === 'TD' || prev[0].tagName === 'TH')) {
							$(prev).after($td);
						}
						else {
							$(parent).prepend($td);
						}

						$td.html(html);
					}

					if (this.utils.isEmpty($list.find('li').text())) {
						var $children = $list.children('li');
						$children.find('br').remove();
						$children.append(this.selection.getMarkerAsHtml());
					}

					if ($list.length) {
						// remove block-element list wrapper
						var $listParent = $list.parent();
						if (this.utils.isRedactorParent($listParent) && $listParent[0].tagName != 'LI' && this.utils.isBlock($listParent[0])) {
							$listParent.replaceWith($listParent.contents());
						}
					}

					if (!this.utils.browser('msie')) {
						this.$editor.focus();
					}

					this.clean.clearUnverified();
				},
				insertInIe: function(cmd) {
					var wrapper = this.selection.wrap('div');
					var wrapperHtml = $(wrapper).html();

					var tmpList = (cmd == 'orderedlist') ? $('<ol>') : $('<ul>');
					var tmpLi = $('<li>');

					if ($.trim(wrapperHtml) === '') {
						tmpLi.append(this.selection.getMarkerAsHtml());
						tmpList.append(tmpLi);
						this.$editor.find('#selection-marker-1').replaceWith(tmpList);
					}
					else {
						var items = wrapperHtml.split(/<br\s?\/?>/gi);
						if (items) {
							for (var i = 0; i < items.length; i++) {
								if ($.trim(items[i]) !== '') {
									tmpList.append($('<li>').html(items[i]));
								}
							}
						}
						else {
							tmpLi.append(wrapperHtml);
							tmpList.append(tmpLi);
						}

						$(wrapper).replaceWith(tmpList);
					}
				},
				remove: function(cmd) {
					document.execCommand('insert' + cmd);

					var $current = $(this.selection.getCurrent());

					this.indent.fixEmptyIndent();

					if (!this.opts.linebreaks && $current.closest('li, th, td', this.$editor[0]).length === 0) {
						document.execCommand('formatblock', false, 'p');
						this.$editor.find('ul, ol, blockquote').each($.proxy(this.utils.removeEmpty, this));
					}

					var $table = $(this.selection.getCurrent()).closest('table', this.$editor[0]);
					var $prev = $table.prev();
					if (!this.opts.linebreaks && $table.length !== 0 && $prev.length !== 0 && $prev[0].tagName == 'BR') {
						$prev.remove();
					}

					this.clean.clearUnverified();

				}
			};
		},
		observe: function() {
			return {
				load: function() {
					this.observe.images();
					this.observe.links();
				},
				buttons: function(e, btnName) {
					var current = this.selection.getCurrent();
					var parent = this.selection.getParent();

					if (e !== false) {
						this.button.setInactiveAll();
					}
					else {
						this.button.setInactiveAll(btnName);
					}

					if (e === false && btnName !== 'html') {
						if ($.inArray(btnName, this.opts.activeButtons) != -1) this.button.toggleActive(btnName);
						return;
					}

					//var linkButtonName = (this.utils.isCurrentOrParent('A')) ? this.lang.get('link_edit') : this.lang.get('link_insert');
					//$('body').find('a.redactor-dropdown-link').text(linkButtonName);

					$.each(this.opts.activeButtonsStates, $.proxy(function(key, value) {
						var parentEl = $(parent).closest(key, this.$editor[0]);
						var currentEl = $(current).closest(key, this.$editor[0]);

						if (parentEl.length !== 0 && !this.utils.isRedactorParent(parentEl)) return;
						if (!this.utils.isRedactorParent(currentEl)) return;
						if (parentEl.length !== 0 || currentEl.closest(key, this.$editor[0]).length !== 0) {
							this.button.setActive(value);
						}

					}, this));

					var $parent = $(parent).closest(this.opts.alignmentTags.toString().toLowerCase(), this.$editor[0]);
					if (this.utils.isRedactorParent(parent) && $parent.length) {
						var align = ($parent.css('text-align') === '') ? 'left' : $parent.css('text-align');
						this.button.setActive('align' + align);
					}
				},
				addButton: function(tag, btnName) {
					this.opts.activeButtons.push(btnName);
					this.opts.activeButtonsStates[tag] = btnName;
				},
				images: function() {
					this.$editor.find('img').each($.proxy(function(i, img) {
						var $img = $(img);

						// IE fix (when we clicked on an image and then press backspace IE does goes to image's url)
						$img.closest('a', this.$editor[0]).on('click', function(e) { e.preventDefault(); });

						if (this.utils.browser('msie')) $img.attr('unselectable', 'on');

						this.image.setEditable($img);

					}, this));

					$(document).on('click.redactor-image-delete.' + this.uuid, $.proxy(function(e) {
						this.observe.image = false;
						if (e.target.tagName == 'IMG' && this.utils.isRedactorParent(e.target)) {
							this.observe.image = (this.observe.image && this.observe.image == e.target) ? false : e.target;
						}

					}, this));

				},
				links: function() {
					if (!this.opts.linkTooltip) return;

					this.$editor.find('a').on('touchstart.redactor.' + this.uuid + ' click.redactor.' + this.uuid, $.proxy(this.observe.showTooltip, this));
					this.$editor.on('touchstart.redactor.' + this.uuid + ' click.redactor.' + this.uuid, $.proxy(this.observe.closeTooltip, this));
					$(document).on('touchstart.redactor.' + this.uuid + ' click.redactor.' + this.uuid, $.proxy(this.observe.closeTooltip, this));
				},
				getTooltipPosition: function($link) {
					return $link.offset();
				},
				showTooltip: function(e) {
					var $el = $(e.target);

					if ($el[0].tagName == 'IMG')
						return;

					if ($el[0].tagName !== 'A')
						$el = $el.closest('a', this.$editor[0]);

					if ($el[0].tagName !== 'A')
						return;
					
					if ($el[0].getAttribute("data-idx")) return;

					var $link = $el;

					var pos = this.observe.getTooltipPosition($link);
					var tooltip = $('<span class="redactor-link-tooltip"></span>');

					var href = $link.attr('href');
					if (href === undefined) {
						href = '';
					}

					if (href.length > 24) href = href.substring(0, 24) + '...';

					var aLink = $('<a href="' + $link.attr('href') + '" target="_blank" />').html(href).addClass('redactor-link-tooltip-action');
					var aEdit = $('<a href="#" />').html(this.lang.get('edit')).on('click', $.proxy(this.link.show, this)).addClass('redactor-link-tooltip-action');
					var aUnlink = $('<a href="#" />').html(this.lang.get('unlink')).on('click', $.proxy(this.link.unlink, this)).addClass('redactor-link-tooltip-action');

					tooltip.append(aLink).append(' | ').append(aEdit).append(' | ').append(aUnlink);
					tooltip.css({
						top: (pos.top + parseInt($link.css('line-height'), 10)) + 'px',
						left: pos.left + 'px'
					});

					$('.redactor-link-tooltip').remove();
					$('body').append(tooltip);
				},
				closeTooltip: function(e) {
					e = e.originalEvent || e;

					var target = e.target;
					var $parent = $(target).closest('a', this.$editor[0]);
					if ($parent.length !== 0 && $parent[0].tagName === 'A' && target.tagName !== 'A') {
						return;
					}
					else if ((target.tagName === 'A' && this.utils.isRedactorParent(target)) || $(target).hasClass('redactor-link-tooltip-action')) {
						return;
					}

					$('.redactor-link-tooltip').remove();
				}

			};
		},
		paragraphize: function() {
			return {
				load: function(html) {
					if (this.opts.linebreaks) return html;
					if (html === '' || html === '<p></p>') return this.opts.emptyHtml;

					html = html + "\n";

					this.paragraphize.safes = [];
					this.paragraphize.z = 0;

					html = html.replace(/(<br\s?\/?>){1,}\n?<\/blockquote>/gi, '</blockquote>');

					html = this.paragraphize.getSafes(html);
					html = this.paragraphize.getSafesComments(html);
					html = this.paragraphize.replaceBreaksToNewLines(html);
					html = this.paragraphize.replaceBreaksToParagraphs(html);
					html = this.paragraphize.clear(html);
					html = this.paragraphize.restoreSafes(html);

					html = html.replace(new RegExp('<br\\s?/?>\n?<(' + this.opts.paragraphizeBlocks.join('|') + ')(.*?[^>])>', 'gi'), '<p><br /></p>\n<$1$2>');

					return $.trim(html);
				},
				getSafes: function(html) {
					var $div = $('<div />').append(html);

					// remove paragraphs in blockquotes
					$div.find('blockquote p').replaceWith(function() {
						return $(this).append('<br />').contents();
					});

					html = $div.html();

					$div.find(this.opts.paragraphizeBlocks.join(', ')).each($.proxy(function(i,s) {
						this.paragraphize.z++;
						this.paragraphize.safes[this.paragraphize.z] = s.outerHTML;
						html = html.replace(s.outerHTML, '\n{replace' + this.paragraphize.z + '}');

					}, this));

					return html;
				},
				getSafesComments: function(html) {
					var commentsMatches = html.match(/<!--([\w\W]*?)-->/gi);

					if (!commentsMatches) return html;

					$.each(commentsMatches, $.proxy(function(i,s) {
						this.paragraphize.z++;
						this.paragraphize.safes[this.paragraphize.z] = s;
						html = html.replace(s, '\n{replace' + this.paragraphize.z + '}');
					}, this));

					return html;
				},
				restoreSafes: function(html) {
					$.each(this.paragraphize.safes, function(i,s) {
						s = (typeof s !== 'undefined') ? s.replace(/\$/g, '&#36;') : s;
						html = html.replace('{replace' + i + '}', s);

					});

					return html;
				},
				replaceBreaksToParagraphs: function(html) {
					var htmls = html.split(new RegExp('\n', 'g'), -1);

					html = '';
					if (htmls) {
						var len = htmls.length;
						for (var i = 0; i < len; i++) {
							if (!htmls.hasOwnProperty(i)) return;

							if (htmls[i].search('{replace') == -1) {
								htmls[i] = htmls[i].replace(/<p>\n\t?<\/p>/gi, '');
								htmls[i] = htmls[i].replace(/<p><\/p>/gi, '');

								if (htmls[i] !== '') {
									html += '<p>' +  htmls[i].replace(/^\n+|\n+$/g, "") + "</p>";
								}
							}
							else html += htmls[i];
						}
					}

					return html;
				},
				replaceBreaksToNewLines: function(html) {
					html = html.replace(/<br \/>\s*<br \/>/gi, "\n\n");
					html = html.replace(/<br\s?\/?>\n?<br\s?\/?>/gi, "\n<br /><br />");

					html = html.replace(new RegExp("\r\n", 'g'), "\n");
					html = html.replace(new RegExp("\r", 'g'), "\n");
					html = html.replace(new RegExp("/\n\n+/"), 'g', "\n\n");

					return html;
				},
				clear: function(html) {
					html = html.replace(new RegExp('</blockquote></p>', 'gi'), '</blockquote>');
					html = html.replace(new RegExp('<p></blockquote>', 'gi'), '</blockquote>');
					html = html.replace(new RegExp('<p><blockquote>', 'gi'), '<blockquote>');
					html = html.replace(new RegExp('<blockquote></p>', 'gi'), '<blockquote>');

					html = html.replace(new RegExp('<p><p ', 'gi'), '<p ');
					html = html.replace(new RegExp('<p><p>', 'gi'), '<p>');
					html = html.replace(new RegExp('</p></p>', 'gi'), '</p>');
					html = html.replace(new RegExp('<p>\\s?</p>', 'gi'), '');
					html = html.replace(new RegExp("\n</p>", 'gi'), '</p>');
					html = html.replace(new RegExp('<p>\t?\t?\n?<p>', 'gi'), '<p>');
					html = html.replace(new RegExp('<p>\t*</p>', 'gi'), '');

					return html;
				}
			};
		},
		paste: function() {
			return {
				init: function(e) {
					if (!this.opts.cleanOnPaste) {
						setTimeout($.proxy(this.code.sync, this), 1);
						return;
					}

					this.rtePaste = true;

					this.buffer.set();
					this.selection.save();
					this.utils.saveScroll();

					this.paste.createPasteBox();

					$(window).on('scroll.redactor-freeze', $.proxy(function() {
						$(window).scrollTop(this.saveBodyScroll);
					}, this));

					setTimeout($.proxy(function() {
						var html = this.$pasteBox.html();

						this.$pasteBox.remove();

						this.selection.restore();
						this.utils.restoreScroll();

						this.paste.insert(html);

						$(window).off('scroll.redactor-freeze');

						if (this.linkify.isEnabled())
							this.linkify.format();

					}, this), 1);
				},
				createPasteBox: function() {
					this.$pasteBox = $('<div>').html('').attr('contenteditable', 'true').css({ position: 'fixed', width: 0, top: 0, left: '-9999px' });

					if (this.utils.browser('msie')) {
						this.$box.append(this.$pasteBox);
					}
					else {
						$('body').append(this.$pasteBox);
					}

					this.$pasteBox.focus();
				},
				insert: function(html) {
					html = this.core.setCallback('pasteBefore', html);

					// clean
					html = (this.utils.isSelectAll()) ? this.clean.onPaste(html, false) : this.clean.onPaste(html);

					html = this.core.setCallback('paste', html);

					if (this.utils.isSelectAll()) {
						this.insert.set(html, false);
					}
					else {
						this.insert.html(html, false);
					}

					this.utils.disableSelectAll();
					this.rtePaste = false;

					setTimeout($.proxy(this.clean.clearUnverified, this), 10);

					// clean empty spans
					setTimeout($.proxy(function() {
						var spans = this.$editor.find('span');
						$.each(spans, function(i,s) {
							var html = s.innerHTML.replace(/[\u200B-\u200D\uFEFF]/, '');
							if (html === '' && s.attributes.length === 0) $(s).remove();

						});

					}, this), 10);
				}
			};
		},
		placeholder: function() {
			return {
				enable: function() {
					if (!this.placeholder.is()) return;

					this.$editor.attr('placeholder', this.$element.attr('placeholder'));

					this.placeholder.toggle();
					this.$editor.on('keyup.redactor-placeholder', $.proxy(this.placeholder.toggle, this));

				},
				toggle: function() {
					var func = 'removeClass';
					if (this.utils.isEmpty(this.$editor.html(), false)) func = 'addClass';
					this.$editor[func]('redactor-placeholder');
				},
				remove: function() {
					this.$editor.removeClass('redactor-placeholder');
				},
				is: function() {
					if (this.opts.placeholder) {
						return this.$element.attr('placeholder', this.opts.placeholder);
					}
					else {
						return !(typeof this.$element.attr('placeholder') == 'undefined' || this.$element.attr('placeholder') === '');
					}
				}
			};
		},
		selection: function() {
			return {
				get: function() {
					this.sel = document.getSelection();

					if (document.getSelection && this.sel.getRangeAt && this.sel.rangeCount) {
						this.range = this.sel.getRangeAt(0);
					}
					else {
						this.range = document.createRange();
					}
				},
				addRange: function() {
					try {
						this.sel.removeAllRanges();
					} catch (e) {}

					this.sel.addRange(this.range);
				},
				getCurrent: function() {
					var el = false;
					this.selection.get();

					if (this.sel && this.sel.rangeCount > 0) {
						el = this.sel.getRangeAt(0).startContainer;
					}

					return this.utils.isRedactorParent(el);
				},
				getParent: function(elem) {
					elem = elem || this.selection.getCurrent();
					if (elem) {
						return this.utils.isRedactorParent($(elem).parent()[0]);
					}

					return false;
				},
				getBlock: function(node) {
					node = node || this.selection.getCurrent();

					while (node) {
						if (this.utils.isBlockTag(node.tagName)) {
							return ($(node).hasClass('redactor-editor')) ? false : node;
						}

						node = node.parentNode;
					}

					return false;
				},
				getInlines: function(nodes, tags) {
					this.selection.get();

					if (this.range && this.range.collapsed) {
						return false;
					}

					var inlines = [];
					nodes = (typeof nodes == 'undefined' || nodes === false) ? this.selection.getNodes() : nodes;
					var inlineTags = this.opts.inlineTags;
					inlineTags.push('span');

					if (typeof tags !== 'undefined') {
						for (var i = 0; i < tags.length; i++) {
							inlineTags.push(tags[i]);
						}
					}

					$.each(nodes, $.proxy(function(i,node) {
						if ($.inArray(node.tagName.toLowerCase(), inlineTags) != -1) {
							inlines.push(node);
						}

					}, this));

					return (inlines.length === 0) ? false : inlines;
				},
				getInlinesTags: function(tags) {
					this.selection.get();

					if (this.range && this.range.collapsed) {
						return false;
					}

					var inlines = [];
					var nodes =  this.selection.getNodes();
					$.each(nodes, $.proxy(function(i,node) {
						if ($.inArray(node.tagName.toLowerCase(), tags) != -1) {
							inlines.push(node);
						}

					}, this));

					return (inlines.length === 0) ? false : inlines;
				},
				getBlocks: function(nodes) {
					this.selection.get();

					if (this.range && this.range.collapsed) {
						return [this.selection.getBlock()];
					}

					var blocks = [];
					nodes = (typeof nodes == 'undefined') ? this.selection.getNodes() : nodes;
					$.each(nodes, $.proxy(function(i,node) {
						if (this.utils.isBlock(node)) {
							this.selection.lastBlock = node;
							blocks.push(node);
						}

					}, this));

					return (blocks.length === 0) ? [this.selection.getBlock()] : blocks;
				},
				getLastBlock: function() {
					return this.selection.lastBlock;
				},
				getNodes: function() {
					this.selection.get();

					var startNode = this.selection.getNodesMarker(1);
					var endNode = this.selection.getNodesMarker(2);
					var range = this.range.cloneRange();

					if (this.range.collapsed === false) {
						var startContainer = range.startContainer;
						var startOffset = range.startOffset;

						// end marker
						this.selection.setNodesMarker(range, endNode, false);

						// start marker
						range.setStart(startContainer, startOffset);
						this.selection.setNodesMarker(range, startNode, true);
					}
					else {
						this.selection.setNodesMarker(range, startNode, true);
						endNode = startNode;
					}

					var nodes = [];
					var counter = 0;

					var self = this;
					this.$editor.find('*').each(function() {
						if (this == startNode) {
							var parent = $(this).parent();
							if (parent.length !== 0 && parent[0].tagName != 'BODY' && self.utils.isRedactorParent(parent[0])) {
								nodes.push(parent[0]);
							}

							nodes.push(this);
							counter = 1;
						}
						else {
							if (counter > 0) {
								nodes.push(this);
								counter = counter + 1;
							}
						}

						if (this == endNode) {
							return false;
						}

					});

					var finalNodes = [];
					var len = nodes.length;
					for (var i = 0; i < len; i++) {
						if (nodes[i].id != 'nodes-marker-1' && nodes[i].id != 'nodes-marker-2') {
							finalNodes.push(nodes[i]);
						}
					}

					this.selection.removeNodesMarkers();

					return finalNodes;

				},
				getNodesMarker: function(num) {
					return $('<span id="nodes-marker-' + num + '" class="redactor-nodes-marker" data-verified="redactor">' + this.opts.invisibleSpace + '</span>')[0];
				},
				setNodesMarker: function(range, node, type) {
					try {
						range.collapse(type);
						range.insertNode(node);
					}
					catch (e) {}
				},
				removeNodesMarkers: function() {
					$(document).find('span.redactor-nodes-marker').remove();
					this.$editor.find('span.redactor-nodes-marker').remove();
				},
				fromPoint: function(start, end) {
					this.caret.setOffset(start, end);
				},
				wrap: function(tag) {
					this.selection.get();

					if (this.range.collapsed) return false;

					var wrapper = document.createElement(tag);
					wrapper.appendChild(this.range.extractContents());
					this.range.insertNode(wrapper);

					return wrapper;
				},
				selectElement: function(node) {
					this.caret.set(node, 0, node, 1);
				},
				selectAll: function() {
					this.selection.get();
					this.range.selectNodeContents(this.$editor[0]);
					this.selection.addRange();
				},
				remove: function() {
					this.selection.get();
					this.sel.removeAllRanges();
				},
				save: function() {
					this.selection.createMarkers();
				},
				createMarkers: function() {
					this.selection.get();

					var node1 = this.selection.getMarker(1);

					this.selection.setMarker(this.range, node1, true);

					if (this.range.collapsed === false) {
						var node2 = this.selection.getMarker(2);
						this.selection.setMarker(this.range, node2, false);
					}

					this.savedSel = this.$editor.html();
				},
				getMarker: function(num) {
					if (typeof num == 'undefined') num = 1;

					return $('<span id="selection-marker-' + num + '" class="redactor-selection-marker"  data-verified="redactor">' + this.opts.invisibleSpace + '</span>')[0];
				},
				getMarkerAsHtml: function(num) {
					return this.utils.getOuterHtml(this.selection.getMarker(num));
				},
				setMarker: function(range, node, type) {
					range = range.cloneRange();

					try {
						range.collapse(type);
						range.insertNode(node);
					}
					catch (e) {
						this.focus.setStart();
					}
				},
				restore: function() {
					var node1 = this.$editor.find('span#selection-marker-1');
					var node2 = this.$editor.find('span#selection-marker-2');

					if (node1.length !== 0 && node2.length !== 0) {
						this.caret.set(node1, 0, node2, 0);
					}
					else if (node1.length !== 0) {
						this.caret.set(node1, 0, node1, 0);
					}
					else {
						this.$editor.focus();
					}

					this.selection.removeMarkers();
					this.savedSel = false;

				},
				removeMarkers: function() {
					this.$editor.find('span.redactor-selection-marker').each(function(i,s) {
						var text = $(s).text().replace(/[\u200B-\u200D\uFEFF]/g, '');
						if (text === '') $(s).remove();
						else $(s).replaceWith(function() { return $(this).contents(); });
					});
				},
				getText: function() {
					this.selection.get();

					return this.sel.toString();
				},
				getHtml: function() {
					var html = '';

					this.selection.get();
					if (this.sel.rangeCount) {
						var container = document.createElement('div');
						var len = this.sel.rangeCount;
						for (var i = 0; i < len; ++i) {
							container.appendChild(this.sel.getRangeAt(i).cloneContents());
						}

						html = container.innerHTML;
					}

					return this.clean.onSync(html);
				},
				replaceWithHtml: function(html) {
					html = this.selection.getMarkerAsHtml(1) + html + this.selection.getMarkerAsHtml(2);

					this.selection.get();

					if (window.getSelection && window.getSelection().getRangeAt) {
						this.range.deleteContents();
						var div = document.createElement("div");
						div.innerHTML = html;

						var frag = document.createDocumentFragment(), child;
						while ((child = div.firstChild)) {
							frag.appendChild(child);
						}

						this.range.insertNode(frag);
					}
					else if (document.selection && document.selection.createRange) {
						this.range.pasteHTML(html);
					}

					this.selection.restore();
					this.code.sync();
				}
			};
		},
		shortcuts: function() {
			return {
				init: function(e, key) {
					// disable browser's hot keys for bold and italic
					if (!this.opts.shortcuts) {
						if ((e.ctrlKey || e.metaKey) && (key === 66 || key === 73)) e.preventDefault();
						return false;
					}

					$.each(this.opts.shortcuts, $.proxy(function(str, command) {
						var keys = str.split(',');
						var len = keys.length;
						for (var i = 0; i < len; i++) {
							if (typeof keys[i] === 'string') {
								this.shortcuts.handler(e, $.trim(keys[i]), $.proxy(function() {
									var func;
									if (command.func.search(/\./) != '-1') {
										func = command.func.split('.');
										if (typeof this[func[0]] != 'undefined') {
											this[func[0]][func[1]].apply(this, command.params);
										}
									}
									else {
										this[command.func].apply(this, command.params);
									}

								}, this));
							}

						}

					}, this));
				},
				handler: function(e, keys, origHandler) {
					// based on https://github.com/jeresig/jquery.hotkeys
					var hotkeysSpecialKeys = {
						8: "backspace", 9: "tab", 10: "return", 13: "return", 16: "shift", 17: "ctrl", 18: "alt", 19: "pause",
						20: "capslock", 27: "esc", 32: "space", 33: "pageup", 34: "pagedown", 35: "end", 36: "home",
						37: "left", 38: "up", 39: "right", 40: "down", 45: "insert", 46: "del", 59: ";", 61: "=",
						96: "0", 97: "1", 98: "2", 99: "3", 100: "4", 101: "5", 102: "6", 103: "7",
						104: "8", 105: "9", 106: "*", 107: "+", 109: "-", 110: ".", 111 : "/",
						112: "f1", 113: "f2", 114: "f3", 115: "f4", 116: "f5", 117: "f6", 118: "f7", 119: "f8",
						120: "f9", 121: "f10", 122: "f11", 123: "f12", 144: "numlock", 145: "scroll", 173: "-", 186: ";", 187: "=",
						188: ",", 189: "-", 190: ".", 191: "/", 192: "`", 219: "[", 220: "\\", 221: "]", 222: "'"
					};


					var hotkeysShiftNums = {
						"`": "~", "1": "!", "2": "@", "3": "#", "4": "$", "5": "%", "6": "^", "7": "&",
						"8": "*", "9": "(", "0": ")", "-": "_", "=": "+", ";": ": ", "'": "\"", ",": "<",
						".": ">",  "/": "?",  "\\": "|"
					};

					keys = keys.toLowerCase().split(" ");
					var special = hotkeysSpecialKeys[e.keyCode],
						character = String.fromCharCode( e.which ).toLowerCase(),
						modif = "", possible = {};

					$.each([ "alt", "ctrl", "meta", "shift"], function(index, specialKey) {
						if (e[specialKey + 'Key'] && special !== specialKey) {
							modif += specialKey + '+';
						}
					});


					if (special) possible[modif + special] = true;
					if (character) {
						possible[modif + character] = true;
						possible[modif + hotkeysShiftNums[character]] = true;

						// "$" can be triggered as "Shift+4" or "Shift+$" or just "$"
						if (modif === "shift+") {
							possible[hotkeysShiftNums[character]] = true;
						}
					}

					for (var i = 0, len = keys.length; i < len; i++) {
						if (possible[keys[i]]) {
							e.preventDefault();
							return origHandler.apply(this, arguments);
						}
					}
				}
			};
		},
		tabifier: function() {
			return {
				get: function(code) {
					if (!this.opts.tabifier) return code;

					// clean setup
					var ownLine = ['area', 'body', 'head', 'hr', 'i?frame', 'link', 'meta', 'noscript', 'style', 'script', 'table', 'tbody', 'thead', 'tfoot'];
					var contOwnLine = ['li', 'dt', 'dt', 'h[1-6]', 'option', 'script'];
					var newLevel = ['p', 'blockquote', 'div', 'dl', 'fieldset', 'form', 'frameset', 'map', 'ol', 'pre', 'select', 'td', 'th', 'tr', 'ul'];

					this.tabifier.lineBefore = new RegExp('^<(/?' + ownLine.join('|/?' ) + '|' + contOwnLine.join('|') + ')[ >]');
					this.tabifier.lineAfter = new RegExp('^<(br|/?' + ownLine.join('|/?' ) + '|/' + contOwnLine.join('|/') + ')[ >]');
					this.tabifier.newLevel = new RegExp('^</?(' + newLevel.join('|' ) + ')[ >]');

					var i = 0,
					codeLength = code.length,
					point = 0,
					start = null,
					end = null,
					tag = '',
					out = '',
					cont = '';

					this.tabifier.cleanlevel = 0;

					for (; i < codeLength; i++) {
						point = i;

						// if no more tags, copy and exit
						if (-1 == code.substr(i).indexOf( '<' )) {
							out += code.substr(i);

							return this.tabifier.finish(out);
						}

						// copy verbatim until a tag
						while (point < codeLength && code.charAt(point) != '<') {
							point++;
						}

						if (i != point) {
							cont = code.substr(i, point - i);
							if (!cont.match(/^\s{2,}$/g)) {
								if ('\n' == out.charAt(out.length - 1)) out += this.tabifier.getTabs();
								else if ('\n' == cont.charAt(0)) {
									out += '\n' + this.tabifier.getTabs();
									cont = cont.replace(/^\s+/, '');
								}

								out += cont;
							}

							if (cont.match(/\n/)) out += '\n' + this.tabifier.getTabs();
						}

						start = point;

						// find the end of the tag
						while (point < codeLength && '>' != code.charAt(point)) {
							point++;
						}

						tag = code.substr(start, point - start);
						i = point;

						var t;

						if ('!--' == tag.substr(1, 3)) {
							if (!tag.match(/--$/)) {
								while ('-->' != code.substr(point, 3)) {
									point++;
								}
								point += 2;
								tag = code.substr(start, point - start);
								i = point;
							}

							if ('\n' != out.charAt(out.length - 1)) out += '\n';

							out += this.tabifier.getTabs();
							out += tag + '>\n';
						}
						else if ('!' == tag[1]) {
							out = this.tabifier.placeTag(tag + '>', out);
						}
						else if ('?' == tag[1]) {
							out += tag + '>\n';
						}
						else if (t = tag.match(/^<(script|style|pre)/i)) {
							t[1] = t[1].toLowerCase();
							tag = this.tabifier.cleanTag(tag);
							out = this.tabifier.placeTag(tag, out);
							end = String(code.substr(i + 1)).toLowerCase().indexOf('</' + t[1]);

							if (end) {
								cont = code.substr(i + 1, end);
								i += end;
								out += cont;
							}
						}
						else {
							tag = this.tabifier.cleanTag(tag);
							out = this.tabifier.placeTag(tag, out);
						}
					}

					return this.tabifier.finish(out);
				},
				getTabs: function() {
					var s = '';
					for ( var j = 0; j < this.tabifier.cleanlevel; j++ ) {
						s += '\t';
					}

					return s;
				},
				finish: function(code) {
					code = code.replace(/\n\s*\n/g, '\n');
					code = code.replace(/^[\s\n]*/, '');
					code = code.replace(/[\s\n]*$/, '');
					code = code.replace(/<script(.*?)>\n<\/script>/gi, '<script$1></script>');

					this.tabifier.cleanlevel = 0;

					return code;
				},
				cleanTag: function (tag) {
					var tagout = '';
					tag = tag.replace(/\n/g, ' ');
					tag = tag.replace(/\s{2,}/g, ' ');
					tag = tag.replace(/^\s+|\s+$/g, ' ');

					var suffix = '';
					if (tag.match(/\/$/)) {
						suffix = '/';
						tag = tag.replace(/\/+$/, '');
					}

					var m;
					while (m = /\s*([^= ]+)(?:=((['"']).*?\3|[^ ]+))?/.exec(tag)) {
						if (m[2]) tagout += m[1].toLowerCase() + '=' + m[2];
						else if (m[1]) tagout += m[1].toLowerCase();

						tagout += ' ';
						tag = tag.substr(m[0].length);
					}

					return tagout.replace(/\s*$/, '') + suffix + '>';
				},
				placeTag: function (tag, out) {
					var nl = tag.match(this.tabifier.newLevel);

					if (tag.match(this.tabifier.lineBefore) || nl) {
						out = out.replace(/\s*$/, '');
						out += '\n';
					}

					if (nl && '/' == tag.charAt(1)) this.tabifier.cleanlevel--;
					if ('\n' == out.charAt(out.length - 1)) out += this.tabifier.getTabs();
					if (nl && '/' != tag.charAt(1)) this.tabifier.cleanlevel++;

					out += tag;

					if (tag.match(this.tabifier.lineAfter) || tag.match(this.tabifier.newLevel)) {
						out = out.replace(/ *$/, '');
						//out += '\n';
					}

					return out;
				}
			};
		},
		tidy: function() {
			return {
				setupAllowed: function() {
					if (this.opts.allowedTags) this.opts.deniedTags = false;
					if (this.opts.allowedAttr) this.opts.removeAttr = false;

					if (this.opts.linebreaks) return;

					var tags = ['p', 'section'];
					if (this.opts.allowedTags) this.tidy.addToAllowed(tags);
					if (this.opts.deniedTags) this.tidy.removeFromDenied(tags);

				},
				addToAllowed: function(tags) {
					var len = tags.length;
					for (var i = 0; i < len; i++) {
						if ($.inArray(tags[i], this.opts.allowedTags) == -1) {
							this.opts.allowedTags.push(tags[i]);
						}
					}
				},
				removeFromDenied: function(tags) {
					var len = tags.length;
					for (var i = 0; i < len; i++) {
						var pos = $.inArray(tags[i], this.opts.deniedTags);
						if (pos != -1) {
							this.opts.deniedTags.splice(pos, 1);
						}
					}
				},
				load: function(html, options) {
					this.tidy.settings = {
						deniedTags: this.opts.deniedTags,
						allowedTags: this.opts.allowedTags,
						removeComments: this.opts.removeComments,
						replaceTags: this.opts.replaceTags,
						replaceStyles: this.opts.replaceStyles,
						removeDataAttr: this.opts.removeDataAttr,
						removeAttr: this.opts.removeAttr,
						allowedAttr: this.opts.allowedAttr,
						removeWithoutAttr: this.opts.removeWithoutAttr,
						removeEmpty: this.opts.removeEmpty
					};

					$.extend(this.tidy.settings, options);

					html = this.tidy.removeComments(html);

					// create container
					this.tidy.$div = $('<div />').append(html);

					// clean
					this.tidy.replaceTags();
					this.tidy.replaceStyles();
					this.tidy.removeTags();

					this.tidy.removeAttr();
					this.tidy.removeEmpty();
					this.tidy.removeParagraphsInLists();
					this.tidy.removeDataAttr();
					this.tidy.removeWithoutAttr();

					html = this.tidy.$div.html();
					this.tidy.$div.remove();

					return html;
				},
				removeComments: function(html) {
					if (!this.tidy.settings.removeComments) return html;

					return html.replace(/<!--[\s\S]*?-->/gi, '');
				},
				replaceTags: function(html) {
					if (!this.tidy.settings.replaceTags) return html;

					var len = this.tidy.settings.replaceTags.length;
					var replacement = [], rTags = [];
					for (var i = 0; i < len; i++) {
						rTags.push(this.tidy.settings.replaceTags[i][1]);
						replacement.push(this.tidy.settings.replaceTags[i][0]);
					}

					$.each(replacement, $.proxy(function(key, value) {
						this.tidy.$div.find(value).replaceWith(function() {
							return $("<" + rTags[key] + " />", {html: $(this).html()});
						});
					}, this));
				},
				replaceStyles: function() {
					if (!this.tidy.settings.replaceStyles) return;

					var len = this.tidy.settings.replaceStyles.length;
					this.tidy.$div.find('span').each($.proxy(function(n,s) {
						var $el = $(s);
						var style = $el.attr('style');
						for (var i = 0; i < len; i++) {
							if (style && style.match(new RegExp('^' + this.tidy.settings.replaceStyles[i][0], 'i'))) {
								var tagName = this.tidy.settings.replaceStyles[i][1];
								$el.replaceWith(function() {
									var tag = document.createElement(tagName);
									return $(tag).append($(this).contents());
								});
							}
						}

					}, this));

				},
				removeTags: function() {
					if (!this.tidy.settings.deniedTags && this.tidy.settings.allowedTags) {
						this.tidy.$div.find('*').not(this.tidy.settings.allowedTags.join(',')).each(function(i, s) {
							if (s.innerHTML === '') $(s).remove();
							else $(s).contents().unwrap();
						});
					}

					if (this.tidy.settings.deniedTags) {
						this.tidy.$div.find(this.tidy.settings.deniedTags.join(',')).each(function(i, s) {
							if (s.innerHTML === '') $(s).remove();
							else $(s).contents().unwrap();
						});
					}
				},
				removeAttr: function() {
					var len;
					if (!this.tidy.settings.removeAttr && this.tidy.settings.allowedAttr) {

						var allowedAttrTags = [], allowedAttrData = [];
						len = this.tidy.settings.allowedAttr.length;
						for (var i = 0; i < len; i++) {
							allowedAttrTags.push(this.tidy.settings.allowedAttr[i][0]);
							allowedAttrData.push(this.tidy.settings.allowedAttr[i][1]);
						}


						this.tidy.$div.find('*').each($.proxy(function(n,s) {
							var $el = $(s);
							var pos = $.inArray($el[0].tagName.toLowerCase(), allowedAttrTags);
							var attributesRemove = this.tidy.removeAttrGetRemoves(pos, allowedAttrData, $el);

							if (attributesRemove) {
								$.each(attributesRemove, function(z,f) {
									$el.removeAttr(f);
								});
							}
						}, this));
					}

					if (this.tidy.settings.removeAttr) {
						len = this.tidy.settings.removeAttr.length;
						for (var i = 0; i < len; i++) {
							var attrs = this.tidy.settings.removeAttr[i][1];
							if ($.isArray(attrs)) attrs = attrs.join(' ');

							this.tidy.$div.find(this.tidy.settings.removeAttr[i][0]).removeAttr(attrs);
						}
					}

				},
				removeAttrGetRemoves: function(pos, allowed, $el) {
					var attributesRemove = [];

					// remove all attrs
					if (pos == -1) {
						$.each($el[0].attributes, function(i, item) {
							attributesRemove.push(item.name);
						});

					}
					// allow all attrs
					else if (allowed[pos] == '*') {
						attributesRemove = [];
					}
					// allow specific attrs
					else {
						$.each($el[0].attributes, function(i, item) {
							if ($.isArray(allowed[pos])) {
								if ($.inArray(item.name, allowed[pos]) == -1) {
									attributesRemove.push(item.name);
								}
							}
							else if (allowed[pos] != item.name) {
								attributesRemove.push(item.name);
							}

						});
					}

					return attributesRemove;
				},
				removeAttrs: function (el, regex) {
					regex = new RegExp(regex, "g");
					return el.each(function() {
						var self = $(this);
						var len = this.attributes.length - 1;
						for (var i = len; i >= 0; i--) {
							var item = this.attributes[i];
							if (item && item.specified && item.name.search(regex)>=0) {
								self.removeAttr(item.name);
							}
						}
					});
				},
				removeEmpty: function() {
					if (!this.tidy.settings.removeEmpty) return;

					this.tidy.$div.find(this.tidy.settings.removeEmpty.join(',')).each(function() {
						var $el = $(this);
						var text = $el.text();
						text = text.replace(/[\u200B-\u200D\uFEFF]/g, '');
						text = text.replace(/&nbsp;/gi, '');
						text = text.replace(/\s/g, '');

						if (text === '' && $el.children().length === 0) {
							$el.remove();
						}
					});
				},
				removeParagraphsInLists: function() {
					this.tidy.$div.find('li p').contents().unwrap();
				},
				removeDataAttr: function() {
					if (!this.tidy.settings.removeDataAttr) return;

					var tags = this.tidy.settings.removeDataAttr;
					if ($.isArray(this.tidy.settings.removeDataAttr)) tags = this.tidy.settings.removeDataAttr.join(',');

					this.tidy.removeAttrs(this.tidy.$div.find(tags), '^(data-)');

				},
				removeWithoutAttr: function() {
					if (!this.tidy.settings.removeWithoutAttr) return;

					this.tidy.$div.find(this.tidy.settings.removeWithoutAttr.join(',')).each(function() {
						if (this.attributes.length === 0) {
							$(this).contents().unwrap();
						}
					});
				}
			};
		},
		toolbar: function() {
			return {
				init: function() {
					return {
						html: {
							title: this.lang.get('html'),
							icon:"file-code-o",
							func: 'code.toggle'
						},
						formatting: {
							title: this.lang.get('formatting'),
							icon:"paragraph",
							dropdown: {
								p: {
									title: this.lang.get('paragraph'),
									func: 'block.format'
								},
								blockquote: {
									title: this.lang.get('quote'),
									func: 'block.format'
								},
								pre: {
									title: this.lang.get('code'),
									func: 'block.format'
								},
								h5: {
									title: this.lang.get('header5'),
									func: 'block.format'
								}
							}
						},
						bold: {
							title: this.lang.get('bold'),
							icon:"bold",
							func: 'inline.format'
						},
						italic: {
							title: this.lang.get('italic'),
							icon:"italic",
							func: 'inline.format'
						},
						deleted: {
							title: this.lang.get('deleted'),
							icon:"strikethrough",
							func: 'inline.format'
						},
						underline: {
							title: this.lang.get('underline'),
							icon:"underline",
							func: 'inline.format'
						},
						unorderedlist: {
							title: '&bull; ' + this.lang.get('unorderedlist'),
							icon:"list-ul",
							func: 'list.toggle'
						},
						orderedlist: {
							title: '1. ' + this.lang.get('orderedlist'),
							icon:"list-ol",
							func: 'list.toggle'
						},
						outdent: {
							title: '< ' + this.lang.get('outdent'),
							icon:"outdent",
							func: 'indent.decrease'
						},
						indent: {
							title: '> ' + this.lang.get('indent'),
							icon:"indent",
							func: 'indent.increase'
						},
						image: {
							title: this.lang.get('image'),
							icon:"picture-o",
							func: 'image.show'
						},
						file: {
							title: this.lang.get('file'),
							icon:"floppy-o",
							func: 'file.show'
						},
						link: {
							title: this.lang.get('link'),
							icon:"link",
							dropdown: {
								link: {
									title: this.lang.get('link_insert'),
									func: 'link.show'
								},
								unlink: {
									title: this.lang.get('unlink'),
									func: 'link.unlink'
								}
							}
						},
						alignleft: {
							title: this.lang.get("align_left"),
							icon:"align-left",
							func:"alignment.left"
						},
						aligncenter: {
							title: this.lang.get("align_center"),
							icon:"align-center",
							func:"alignment.center"
						},
						alignright: {
							title: this.lang.get("align_right"),
							icon:"align-right",
							func:"alignment.right"
						},
						alignjustify: {
							title: this.lang.get("align_justify"),
							icon:"align-justify",
							func:"alignment.justify"
						},
						alignment: {
							title: this.lang.get('alignment'),
							icon:"align-left",
							dropdown: {
								left: {
									title: this.lang.get('align_left'),
									func: 'alignment.left'
								},
								center: {
									title: this.lang.get('align_center'),
									func: 'alignment.center'
								},
								right: {
									title: this.lang.get('align_right'),
									func: 'alignment.right'
								},
								justify: {
									title: this.lang.get('align_justify'),
									func: 'alignment.justify'
								}
							}
						},
						horizontalrule: {
							title: this.lang.get('horizontalrule'),
							icon:"minus",
							func: 'line.insert'
						},
						fontcolor: {
							title: this.lang.get("fontcolor"),
							icon:"font",
							func: "fontcolor.buildPicker"
						},
						backcolor: {
							title: this.lang.get("backcolor"),
							icon:"font",
							func: "fontcolor.buildPicker"
						},
						video: {
							title: this.lang.get("video"),
							icon:"youtube-play",
							func: "video.show"
						},
						insertcode: {
							title: this.lang.get("insert_code"),
							icon:"code",
							func:"insertcode.show"
						}
					};
				},
				build: function() {
					this.toolbar.hideButtons();
					this.toolbar.isButtonSourceNeeded();

					if (this.opts.buttons.length === 0) return;

					this.$toolbar = this.toolbar.createContainer();

					this.toolbar.setOverflow();
					this.toolbar.append();
					this.toolbar.setFormattingTags();
					this.toolbar.loadButtons();
					this.toolbar.setFixed();

					// buttons response
					if (this.opts.activeButtons) {
						this.$editor.on('mouseup.redactor keyup.redactor focus.redactor', $.proxy(this.observe.buttons, this));
					}

				},
				createContainer: function() {
					return $('<ul>').addClass('redactor-toolbar').attr('id', 'redactor-toolbar-' + this.uuid);
				},
				setFormattingTags: function() {
					$.each(this.opts.toolbar.formatting.dropdown, $.proxy(function (i, s) {
						if ($.inArray(i, this.opts.formatting) == -1) delete this.opts.toolbar.formatting.dropdown[i];
					}, this));

				},
				loadButtons: function() {
					$.each(this.opts.buttons, $.proxy(function(i, btnName) {
						if (!this.opts.toolbar[btnName]) return;

						var btnObject = this.opts.toolbar[btnName];
						this.$toolbar.append($('<li>').append(this.button.build(btnName, btnObject)));

					}, this));
				},
				append: function() {
					if (this.opts.toolbarExternal) {
						this.$toolbar.addClass('redactor-toolbar-external');
						$(this.opts.toolbarExternal).html(this.$toolbar);
					}
					else {
						this.$box.prepend(this.$toolbar);
					}
				},
				setFixed: function() {
					if (!this.utils.isDesktop()) return;
					if (this.opts.toolbarExternal) return;
					if (!this.opts.toolbarFixed) return;

					this.toolbar.observeScroll();
					$(document).on('scroll.redactor.' + this.uuid, $.proxy(this.toolbar.observeScroll, this));

				},
				setOverflow: function() {
					if (this.utils.isMobile() && this.opts.toolbarOverflow) {
						this.$toolbar.addClass('redactor-toolbar-overflow');
					}
				},
				isButtonSourceNeeded: function() {
					if (this.opts.source) return;

					var index = this.opts.buttons.indexOf('html');
					if (index !== -1) {
						this.opts.buttons.splice(index, 1);
					}
				},
				hideButtons: function() {
					if (this.opts.buttonsHide.length === 0) return;

					$.each(this.opts.buttonsHide, $.proxy(function(i, s) {
						var index = this.opts.buttons.indexOf(s);
						this.opts.buttons.splice(index, 1);

					}, this));
				},
				observeScroll: function() {
					if (!this.opts.toolbarFixed) return;
					
					if (!this.utils.isDesktop()) {
						this.toolbar.observeScrollDisable();
						return;
					}
					
					var scrollTop = $(document).scrollTop();
					var boxTop = 1;
					
					var boxTop = this.$box.offset().top;
					
					if (boxTop == scrollTop) return;

					if (scrollTop + $("#iModuleNavigation.fixed").outerHeight(true) > boxTop) {
						this.toolbar.observeScrollEnable(scrollTop + $("#iModuleNavigation.fixed").outerHeight(true), boxTop);
					} else {
						this.toolbar.observeScrollDisable();
					}
				},
				observeScrollEnable: function(scrollTop, boxTop) {
					var end = boxTop + this.$box.height();
					var width = this.$box.outerWidth();

					this.$toolbar.parent().addClass("toolbar-fixed-box");
					this.$toolbar.css("top",$("#iModuleNavigation.fixed").outerHeight(true));
					this.$toolbar.css("left",this.$box.offset().left);
					this.$toolbar.css("width",this.$box.width());
					this.$box.css("paddingTop",this.$toolbar.height());
					
					if (scrollTop > end) {
						$('.redactor-dropdown-' + this.uuid + ':visible').hide();
					}
					
					this.toolbar.setDropdownsFixed();
					if (this.$box.hasClass("toolbar-fixed-box") == true) {
						this.$toolbar.css('visibility', (scrollTop < end) ? 'visible' : 'hidden');
					}
				},
				observeScrollDisable: function() {
					this.$toolbar.css({
						left:0,
						top:0,
						width:"auto"
					});
					
					this.$box.css("paddingTop",0);

					this.toolbar.unsetDropdownsFixed();
					this.$toolbar.parent().removeClass('toolbar-fixed-box');
					this.$toolbar.css('visibility','visible');
				},
				setDropdownsFixed: function() {
					var top = this.$toolbar.innerHeight() + $("#iModuleNavigation.fixed").outerHeight(true);
					var position = 'fixed';

					$('.redactor-dropdown-' + this.uuid).each(function() {
						$(this).css({ position: position, top: top + 'px' });
					});
				},
				unsetDropdownsFixed: function() {
					var top = (this.$toolbar.innerHeight() + this.$toolbar.offset().top);
					$('.redactor-dropdown-' + this.uuid).each(function() {
						$(this).css({ position: 'absolute', top: top + 'px' });
					});
				}
			};
		},
		upload: function() {
			return {
				init: function(id, url, callback) {
					this.upload.$el = $(id);
					this.upload.$droparea = $('<div id="redactor-droparea" />');

					this.upload.$placeholdler = $('<div id="redactor-droparea-placeholder" />').text(this.lang.get('upload_label'));

					this.upload.$placeholdler.append(this.upload.$input);
					this.upload.$droparea.append(this.upload.$placeholdler);
					this.upload.$el.append(this.upload.$droparea);

					this.upload.$droparea.off('redactor.upload');
					this.upload.$input.off('redactor.upload');

					this.upload.$droparea.on('dragover.redactor.upload', $.proxy(this.upload.onDrag, this));
					this.upload.$droparea.on('dragleave.redactor.upload', $.proxy(this.upload.onDragLeave, this));

					// drop
					this.upload.$droparea.on('drop.redactor.upload', $.proxy(function(e) {
						e.preventDefault();

						this.upload.$droparea.removeClass('drag-hover').addClass('drag-drop');
						this.upload.onDrop(e);

					}, this));
				},
				directUpload: function(file, e) {
					this.upload.direct = true;
					this.upload.traverseFile(file, e);
				},
				onDrop: function(e) {
					e = e.originalEvent || e;
					var files = e.dataTransfer.files;

					this.upload.traverseFile(files[0], e);
				},
				traverseFile: function(file, e) {
					file.status = "WAITING";
					file.autoInsert = true;
					Attachment.add(this.$textarea.attr("id")+"-attachment",file);
					Attachment.submit(this.$textarea.attr("id")+"-attachment");
				},
				onDrag: function(e) {
					e.preventDefault();
					this.upload.$droparea.addClass('drag-hover');
				},
				onDragLeave: function(e) {
					e.preventDefault();
					this.upload.$droparea.removeClass('drag-hover');
				}
			};
		},
		utils: function() {
			return {
				isMobile: function() {
					return /(iPhone|iPod|BlackBerry|Android)/.test(navigator.userAgent);
				},
				isDesktop: function() {
					return !/(iPhone|iPod|iPad|BlackBerry|Android)/.test(navigator.userAgent);
				},
				isString: function(obj) {
					return Object.prototype.toString.call(obj) == '[object String]';
				},
				isEmpty: function(html, removeEmptyTags) {
					html = html.replace(/[\u200B-\u200D\uFEFF]/g, '');
					html = html.replace(/&nbsp;/gi, '');
					html = html.replace(/<\/?br\s?\/?>/g, '');
					html = html.replace(/\s/g, '');
					html = html.replace(/^<p>[^\W\w\D\d]*?<\/p>$/i, '');
					html = html.replace(/<iframe(.*?[^>])>$/i, 'iframe');
					html = html.replace(/<source(.*?[^>])>$/i, 'source');

					// remove empty tags
					if (removeEmptyTags !== false) {
						html = html.replace(/<[^\/>][^>]*><\/[^>]+>/gi, '');
						html = html.replace(/<[^\/>][^>]*><\/[^>]+>/gi, '');
					}

					html = $.trim(html);

					return html === '';
				},
				normalize: function(str) {
					if (typeof(str) === 'undefined') return 0;
					return parseInt(str.replace('px',''), 10);
				},
				hexToRgb: function(hex) {
					if (typeof hex == 'undefined') return;
					if (hex.search(/^#/) == -1) return hex;

					var shorthandRegex = /^#?([a-f\d])([a-f\d])([a-f\d])$/i;
					hex = hex.replace(shorthandRegex, function(m, r, g, b) {
						return r + r + g + g + b + b;
					});

					var result = /^#?([a-f\d]{2})([a-f\d]{2})([a-f\d]{2})$/i.exec(hex);
					return 'rgb(' + parseInt(result[1], 16) + ', ' + parseInt(result[2], 16) + ', ' + parseInt(result[3], 16) + ')';
				},
				getOuterHtml: function(el) {
					return $('<div>').append($(el).eq(0).clone()).html();
				},
				getAlignmentElement: function(el) {
					if ($.inArray(el.tagName, this.opts.alignmentTags) !== -1) {
						return $(el);
					}
					else {
						return $(el).closest(this.opts.alignmentTags.toString().toLowerCase(), this.$editor[0]);
					}
				},
				removeEmptyAttr: function(el, attr) {
					var $el = $(el);
					if (typeof $el.attr(attr) == 'undefined') {
						return true;
					}

					if ($el.attr(attr) === '') {
						$el.removeAttr(attr);
						return true;
					}

					return false;
				},
				removeEmpty: function(i, s) {
					var $s = $($.parseHTML(s));

					$s.find('.redactor-invisible-space').removeAttr('style').removeAttr('class');

					if ($s.find('hr, br, img, iframe, source').length !== 0) return;
					var text = $.trim($s.text());

					if (this.utils.isEmpty(text, false)) {
						$s.remove();
					}
				},

				// save and restore scroll
				saveScroll: function() {
					this.saveEditorScroll = this.$editor.scrollTop();
					this.saveBodyScroll = $(window).scrollTop();

					if (this.opts.scrollTarget) this.saveTargetScroll = $(this.opts.scrollTarget).scrollTop();
				},
				restoreScroll: function() {
					if (typeof this.saveScroll === 'undefined' && typeof this.saveBodyScroll === 'undefined') return;

					$(window).scrollTop(this.saveBodyScroll);
					this.$editor.scrollTop(this.saveEditorScroll);

					if (this.opts.scrollTarget) $(this.opts.scrollTarget).scrollTop(this.saveTargetScroll);
					this.toolbar.observeScroll();
				},

				// get invisible space element
				createSpaceElement: function() {
					var space = document.createElement('span');
					space.className = 'redactor-invisible-space';
					space.innerHTML = this.opts.invisibleSpace;

					return space;
				},

				// replace
				removeInlineTags: function(node) {
					var tags = this.opts.inlineTags;
					tags.push('span');

					if (node.tagName == 'PRE') tags.push('a');

					$(node).find(tags.join(',')).not('span.redactor-selection-marker').contents().unwrap();
				},
				replaceWithContents: function(node, removeInlineTags) {
					var self = this;
					$(node).replaceWith(function() {
						if (removeInlineTags === true) self.utils.removeInlineTags(this);

						return $(this).contents();
					});

					return $(node);
				},
				replaceToTag: function(node, tag, removeInlineTags) {
					var replacement;
					var self = this;
					$(node).replaceWith(function() {
						replacement = $('<' + tag + ' />').append($(this).contents());

						for (var i = 0; i < this.attributes.length; i++) {
							replacement.attr(this.attributes[i].name, this.attributes[i].value);
						}

						if (removeInlineTags === true) self.utils.removeInlineTags(replacement);

						return replacement;
					});

					return replacement;
				},

				// start and end of element
				isStartOfElement: function() {
					var block = this.selection.getBlock();
					if (!block) return false;

					var offset = this.caret.getOffsetOfElement(block);

					return (offset === 0) ? true : false;
				},
				isEndOfElement: function(element) {
					if (typeof element == 'undefined') {
						var element = this.selection.getBlock();
						if (!element) return false;
					}

					var offset = this.caret.getOffsetOfElement(element);
					var text = $.trim($(element).text()).replace(/\n\r\n/g, '');

					return (offset == text.length) ? true : false;
				},
				isEndOfEditor: function() {
					var block = this.$editor[0];

					var offset = this.caret.getOffsetOfElement(block);
					var text = $.trim($(block).html().replace(/(<([^>]+)>)/gi,''));

					return (offset == text.length) ? true : false;
				},

				// test blocks
				isBlock: function(block) {
					block = block[0] || block;

					return block && this.utils.isBlockTag(block.tagName);
				},
				isBlockTag: function(tag) {
					if (typeof tag == 'undefined') return false;

					return this.reIsBlock.test(tag);
				},

				// tag detection
				isTag: function(current, tag) {
					var element = $(current).closest(tag, this.$editor[0]);
					if (element.length == 1) {
						return element[0];
					}

					return false;
				},

				// select all
				isSelectAll: function() {
					return this.selectAll;
				},
				enableSelectAll: function() {
					this.selectAll = true;
				},
				disableSelectAll: function() {
					this.selectAll = false;
				},

				// parents detection
				isRedactorParent: function(el) {
					if (!el) {
						return false;
					}

					if ($(el).parents('.redactor-editor').length === 0 || $(el).hasClass('redactor-editor')) {
						return false;
					}

					return el;
				},
				isCurrentOrParentHeader: function() {
					return this.utils.isCurrentOrParent(['H1', 'H2', 'H3', 'H4', 'H5', 'H6']);
				},
				isCurrentOrParent: function(tagName) {
					var parent = this.selection.getParent();
					var current = this.selection.getCurrent();

					if ($.isArray(tagName)) {
						var matched = 0;
						$.each(tagName, $.proxy(function(i, s) {
							if (this.utils.isCurrentOrParentOne(current, parent, s)) {
								matched++;
							}
						}, this));

						return (matched === 0) ? false : true;
					}
					else {
						return this.utils.isCurrentOrParentOne(current, parent, tagName);
					}
				},
				isCurrentOrParentOne: function(current, parent, tagName) {
					tagName = tagName.toUpperCase();

					return parent && parent.tagName === tagName ? parent : current && current.tagName === tagName ? current : false;
				},


				// browsers detection
				isOldIe: function() {
					return (this.utils.browser('msie') && parseInt(this.utils.browser('version'), 10) < 9) ? true : false;
				},
				isLessIe10: function() {
					return (this.utils.browser('msie') && parseInt(this.utils.browser('version'), 10) < 10) ? true : false;
				},
				isIe11: function() {
					return !!navigator.userAgent.match(/Trident\/7\./);
				},
				browser: function(browser) {
					var ua = navigator.userAgent.toLowerCase();
					var match = /(opr)[\/]([\w.]+)/.exec( ua ) ||
					/(chrome)[ \/]([\w.]+)/.exec( ua ) ||
					/(webkit)[ \/]([\w.]+).*(safari)[ \/]([\w.]+)/.exec(ua) ||
					/(webkit)[ \/]([\w.]+)/.exec( ua ) ||
					/(opera)(?:.*version|)[ \/]([\w.]+)/.exec( ua ) ||
					/(msie) ([\w.]+)/.exec( ua ) ||
					ua.indexOf("trident") >= 0 && /(rv)(?::| )([\w.]+)/.exec( ua ) ||
					ua.indexOf("compatible") < 0 && /(mozilla)(?:.*? rv:([\w.]+)|)/.exec( ua ) ||
					[];

					if (browser == 'safari') return (typeof match[3] != 'undefined') ? match[3] == 'safari' : false;
					if (browser == 'version') return match[2];
					if (browser == 'webkit') return (match[1] == 'chrome' || match[1] == 'opr' || match[1] == 'webkit');
					if (match[1] == 'rv') return browser == 'msie';
					if (match[1] == 'opr') return browser == 'webkit';

					return browser == match[1];
				}
			};
		},
		linkify: function() {
			return {
				isKey: function(key) {
					return key == this.keyCode.ENTER || key == this.keyCode.SPACE;
				},
				isEnabled: function() {
					return this.opts.convertLinks && (this.opts.convertUrlLinks || this.opts.convertImageLinks || this.opts.convertVideoLinks) && !this.utils.isCurrentOrParent('PRE');
				},
				format: function() {
					var linkify = this.linkify,
						opts	= this.opts;

					this.$editor
						.find(":not(iframe,img,a,pre)")
						.addBack()
						.contents()
						.filter(function() {
							return this.nodeType === 3 && $.trim(this.nodeValue) != "" && !$(this).parent().is("pre") && (this.nodeValue.match(opts.linkify.regexps.youtube) || this.nodeValue.match(opts.linkify.regexps.vimeo) || this.nodeValue.match(opts.linkify.regexps.image) || this.nodeValue.match(opts.linkify.regexps.url));
						})
						.each(function() {
							var text = $(this).text(),
								html = text;

							if (opts.convertVideoLinks && (html.match(opts.linkify.regexps.youtube) || html.match(opts.linkify.regexps.vimeo)) ) {
								html = linkify.convertVideoLinks(html);
							}
							else if (opts.convertImageLinks && html.match(opts.linkify.regexps.image)) {
								html = linkify.convertImages(html);
							}
							else if (opts.convertUrlLinks) {
								html = linkify.convertLinks(html);
							}

							$(this).before(text.replace(text, html))
								   .remove();
						});

					this.linkify.after();
				},
				convertVideoLinks: function(html) {
					var iframeStart = '<iframe width="500" height="281" src="',
						iframeEnd = '" frameborder="0" allowfullscreen></iframe>';

					if (html.match(this.opts.linkify.regexps.youtube)) {
						html = html.replace(this.opts.linkify.regexps.youtube, iframeStart + '//www.youtube.com/embed/$1' + iframeEnd);
					}

					if (html.match(this.opts.linkify.regexps.vimeo)) {
						html = html.replace(this.opts.linkify.regexps.vimeo, iframeStart + '//player.vimeo.com/video/$2' + iframeEnd);
					}

					return html;
				},
				convertImages: function(html) {
					var matches = html.match(this.opts.linkify.regexps.image);

					if (matches) {
						html = html.replace(html, '<img src="' + matches + '" />');
					}

					return html;
				},
				convertLinks: function(html) {
					var matches = html.match(this.opts.linkify.regexps.url);

					if (matches) {
						matches = $.grep(matches, function(v, k) { return $.inArray(v, matches) === k; });

						var length = matches.length;

						for (var i = 0; i < length; i++) {
							var href = matches[i],
								text = href,
								linkProtocol = this.opts.linkProtocol + '://';

							if (href.match(/(https?|ftp):\/\//i) !== null) {
								linkProtocol = "";
							}

							if (text.length > this.opts.linkSize) {
								text = text.substring(0, this.opts.linkSize) + '...';
							}

							text = decodeURIComponent(text);

							var regexB = "\\b";

							if ($.inArray(href.slice(-1), ["/", "&", "="]) != -1) {
								regexB = "";
							}

							// escaping url
							var regexp = new RegExp('(' + href.replace(/[\-\[\]\/\{\}\(\)\*\+\?\.\\\^\$\|]/g, "\\$&") + regexB + ')', 'g');

							html = html.replace(regexp, '<a href="' + linkProtocol + $.trim(href) + '">' + $.trim(text) + '</a>');
						}
					}

					return html;
				},
				after: function() {
					this.observe.load();
					this.code.sync();
				}
			}
		}
	};

	$(window).on('load.tools.redactor', function() {
		$('[data-tools="redactor"]').redactor();
	});

	// constructor
	Redactor.prototype.init.prototype = Redactor.prototype;
})(jQuery);