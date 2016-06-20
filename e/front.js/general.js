var cartLink = '/cart';

$(function()
{
	// Fadeout msgs
	$('.alert-fadeout').animate({'opacity':0.3}, 'slow');
	$('.alert-fadeout').mouseenter(function()
	{
		$(this).stop().animate({'opacity':1});
	}).mouseleave(function()
	{
		$(this).stop().animate({'opacity':0.3});
	});
	
	function add_to_cart_function (e)
	{
		e.preventDefault();
		
		var tblObj = $(this).parents('table:first');
		
		$("#utilityMessage").hide();
		
		var coords = 
		{
			'top'			: $(tblObj).offset().top,
			'left'		: $(tblObj).offset().left,
			'height'	: $(tblObj).outerHeight(),
			'width'		: $(tblObj).outerWidth()
		};
		
		$('<div>').addClass('utilityTableLineMessageBg').addClass('utilityTableLineMessage').css(coords).css({'opacity':0.8}).appendTo('body');
		$('<div>').addClass('utilityTableLineMessageText').addClass('utilityTableLineMessage').css(
		{
			'top'			: $(tblObj).offset().top + $(tblObj).outerHeight() / 2,
			'left'		: $(tblObj).offset().left,
			'width'		: $(tblObj).outerWidth()
		}).html('Товар добавляется в корзину').appendTo('body');
		
		var ajax_params = 
		{
			url: $(this).attr('href') + '/ajax',
			dataType: "json",
			success:function(r)
			{
				$('div.utilityTableLineMessage').remove();
				$("#utilityMessage").html('Товар добавлен в <a href="'+cartLink+'">корзину</a>').slideDown(function(){$(this).animate({'opacity':0.9});});
				setTimeout(function(){$("#utilityMessage").fadeOut();}, 20000);
				
				$('#cart_widget').show().find('tbody').empty();
				$.each(r._meta_cart_list, function(ind, el)
				{
					$('#cart_widget tbody').append('<tr class="cart_items"><td class="first">' + el.art_number + ' ' + el.sup_brand + ' ' + el.description + '</td><td>' + el.qty + '</td><td class="last widget_subtotal">' + el.subtotal_formatted + '</td></tr>');
				});
				
				show_hide_init();
				
				// @todo Put result into add-to-cart container
				// $.getJSON('/auto/cart_list_json', {'void':'void'}, function (result)
				// {
					// $('#cart_widget').show().find('tbody').empty();
					// $.each(result, function(ind, el)
					// {
						// $('#cart_widget tbody').append('<tr><td class="first">' + el.art_number + ' ' + el.sup_brand + ' ' + el.description + '</td><td>' + el.qty + '</td><td class="last widget_subtotal">' + el.subtotal_formatted + '</td></tr>');
					// });
				// })
			},
			error:function(r)
			{
				$('div.utilityTableLineMessage').remove();
				alert('Произошла ошибка при добавлении товара в корзину');
			}
		}
		
		if ($(this).data('item') != undefined)
		{
			var item = $(this).data('item');
			delete item.td_for_buttons;
			
			ajax_params.type = 'POST';
			ajax_params.data = {'item':$(this).data('item')};
		}
		
		$.ajax(ajax_params);
		
		// return false;
	}
	
	$('a.add_to_cart_link').click(add_to_cart_function);
	
	$('a.auto_compatibility_trigger').click(function()
	{
		$(this).text('Загрузка данных...').addClass('disabled');
		$('#compatibility').load($(this).attr('href'));
		return false;
	});
	
	// Tooltips
	
	$('.stock-buttons a.btn').tooltip();
	$('.genucat_btn.ttp').tooltip();
	$('.cat_btn.ttp').tooltip();
	
	// Stock improvements
	
	$('#stock tr.primary_article').each(function(index, el)
	{
		$(el).detach().prependTo('#stock tbody');
	});
	$('#stock tr.brands_match_neutral').each(function(index, el)
	{
		$(el).detach().appendTo('#stock tbody');
	});
	$('#stock tr.brands_mismatch').each(function(index, el)
	{
		$(el).detach().appendTo('#stock tbody');
	});
	
	$('.stock-label').tooltip({'placement':'right'});
	
	if ($('#stock tr.primary_article').length < 1 && $('#stock').hasClass('primary_number_present'))
	{
		$('#alert_no_primarynr').slideDown();
	}
	
	// Enable sorting
	$('#stock').stupidtable();
	
	// Add some styles to sortable column headers
	$('th[data-sort]').append('&nbsp;<i class="icon-random"></i>').wrapInner('<span />');
	
	// API puller
	if ($('body').hasClass('require_apis'))
	{
		var lowestPrices = new Object();
		var primaryList = new Object();
		var nonPrimaryList = new Object();
		
		// Show hidden arts in secondary list, filtered by hash
		function getMorePrices (e)
		{
			var hash_short = $(this).parents('tr').data('hash_short');
			var newList = new Array();
			
			$.each(nonPrimaryList[hash_short], function (ind, el) {
				newList.push(el);
			});
			
			$(this).parents('table#stock').addClass('prices-rebuild');
			buildTable(newList, '#stock', 'rebuild');
			$('<div id="listing-limited-alert" class="alert alert-info"><i class="icon-info-sign"></i> <strong>Показаны отдельные позиции.</strong></div>').append(" ", $('<a class="btn btn-mini" href="#stock">Показать все</a>').click(listAllPrices)).insertBefore('#stock');
		}
		
		// Reset secondary list
		function listAllPrices (e)
		{
			$('#listing-limited-alert').remove();
			$('#stock').removeClass('prices-rebuild');
			buildTable(lowestPrices, '#stock', 'sec');
		}
		
		function buildTable (theList, table, mode)
		{
			// Prep Date
			var today		= new Date();
			var dd			= today.getDate();
			var mm			= today.getMonth()+1;
			var yyyy		= today.getFullYear();
			
			if (dd < 10)
				dd = '0' + dd;
			if (mm < 10)
				mm = '0' + mm;
			
			// Put date into string
			var todays_str = dd + '.' + mm + '.' + yyyy;
			
			$(table).find('tbody tr').show(); // we show articles, in case they were hidden (by construct below)
			
			// Empty table contents
			// if (mode != 'append')
				$(table).find('tr.api_article').remove();
			
			if (mode == 'rebuild')
				$(table).find('tbody tr').hide(); // we hide native non-api articles
			
			// Walk through each of the elements on the list
			$.each(theList, function(ind, el) {
				// We clone more button for an article with this number
				var td_for_buttons = $('table.not_in_stock tr.article_'+el.art_number_clear+'_'+el.sup_brand_clear+' td.stock-buttons');
				if ($(td_for_buttons).is('*')) {
					el.td_for_buttons = $(td_for_buttons).clone();
				}
				else {
					el.td_for_buttons = $('<td class="stock-buttons"></td>');
				}
				
				if (mode == 'sec' && nonPrimaryList[el.hash_short].length > 1) {
					$(el.td_for_buttons)
						.append($('<a href="#stock" class="get-more-prices btn btn-small"></a>').append('<i class="icon-plus"></i> Цены ('+nonPrimaryList[el.hash_short].length+')').click(getMorePrices)).append(" ");
				}
				
				$(el.td_for_buttons)
					.append($('<a href="/cart/add/0" class="add_to_cart_link btn btn-small btn-primary"><i class="icon-shopping-cart icon-white"></i></a>').data('item', el).click(add_to_cart_function));
				
				// Label
				if ((el.art_number_clear == $('#stock').data('primarynr') || $('body').hasClass('api_list_combo_page')) && el.brands_match == true)
				{
					el.article_label = '<span class="label label-success stock-label" title="Позиция соответствует искомой">Соответствует искомой</span>';
					el.tr_class = 'normal_article';
				}
				else
				{
					if (el.brands_match == true || $('body').hasClass('api_list_article_page'))
					{
						el.article_label = '<span class="label label-info stock-label" title="Аналог искомой позиция">Аналог искомой</span>';
						el.tr_class = 'normal_article';
					}
					else if (el.brands_match == 'neutral')
					{
						el.article_label = '<span class="label stock-label" title="Найдено по кросс-номеру без проверки соответствия бренда">Найден по кросс-номеру</span> (бренд не указан)';
						el.tr_class = 'brands_match_neutral muted';
					}
					else if (el.brands_match == false)
					{
						el.article_label = '<span class="label stock-label" title="Бренды в базе аналогов и в прайсе по данной позиции не совпадают">Несовпадение по бренду:</span> ' + el.sup_brand + ' &ne; ' + el.brand_requested;
						el.tr_class = 'brands_mismatch muted';
					}
				}
				
				// Delivery rate
				if (el.hasOwnProperty('dd_percent')) {
					el.dd_percent_html = '<br><span title="Вероятность наличия" class="muted dd_percent">' + el.dd_percent + '%</span>';
				}
				else{
					el.dd_percent_html = '';
				}
				
				// Put line
				$(table).find('tbody').prepend
				(
					$
					(
						'<tr class="article_info article_in_stock api_article api_article_' 
						+ el.hash_short 
						+ ' ' 
						+ el.tr_class 
						+ '" data-hash_short="' 
						+ el.hash_short + '"><td class="art_status article_number"><i class="icon-star-empty" title=""></i> '  
						+ el.art_number + '</td><td><strong>' 
						+ el.sup_brand + '</strong></td><td class="stock-description">' 
						+ el.description + '<br>' + el.article_label 
						+ '</td><td data-sort-value="' + el.qty_limit + '"><abbr title="Обновлено: ' + todays_str + '">' 
						+ el.qty_limit + ' шт.</abbr>' 
						+ el.dd_percent_html + '</td><td class="delivery_days" data-sort-value="' 
						+ el.delivery_days + '"><abbr title="Средний срок поставки запчасти со склада">' 
						+ el.delivery_days + ' дн.</abbr></td><td class="article_price"  data-sort-value="' 
						+ el.price + '">' + el.price_f + '<br><span class="muted price_update_date">на ' 
						+ todays_str + '</span></td></tr>'
					)
					.append(el.td_for_buttons)
				);
			});
			
			// Add a tooltip
			$('.stock-label').tooltip({'placement':'right'});
			
			// Remove `sorting` classes to force asc sorting on click event
			$(table).find("th").data("sort-dir", null).removeClass("sorting-desc sorting-asc");
			
			if (mode != 'append')
				$(table).find('th.primary-sort').trigger('click');
		}
		
		// Vendors string to array
		$('body').data('vendors', String($('body').data('vendors')).split(' '));
		
		// Progress Bar
		$('#stock').before('<div id="api-progress-wrapper" class="progress progress-striped active"><div id="api-progress" class="bar" style="width: 5%;"></div></div>');
		
		var api_progress_offset = $('#api-progress-wrapper').offset();
		var api_progress_width = $('#api-progress-wrapper').width();
		var barStepSize = Math.ceil(100 / $('body').data('vendors').length / $('tr.article').length);
		var barCurrentLength = 0;
		
		// Make progress bar flying
		$(window).scroll(function() {
			if ($(window).scrollTop() > api_progress_offset.top && !$('#api-progress-flying').is('*')) {
				$('#api-progress-wrapper').wrap($('<div id="api-progress-flying"></div>').css('left', api_progress_offset.left-7).width(api_progress_width));
			}
			else if ($(window).scrollTop() < api_progress_offset.top && $('#api-progress-flying').is('*')) {
				$('#api-progress-wrapper').unwrap();
			}
		});
		
		
		
		$('.utilityAlerts').stop().hide();
	
		
		
		
		
		var query_count = 0;
		var respond_count = 0;
		
		function apiPuller(a_ind, a_el)
		{
			if ($(a_el).is('*')) {
				var art_number_clear = $(a_el).data('art_number_clear');
				var sup_brand_clear = $(a_el).data('sup_brand_clear');
			}
			else {
				var art_number_clear = a_el.art_number_clear;
				var sup_brand_clear = a_el.sup_brand_clear;
			}
			
			if ($('body').hasClass('api_list_article_page')) {
				var list_mode = 'article_page';
			}
			else {
				var list_mode = 'allnrs';
			}
			
			// Iterate through all available vendors
			$.each($('body').data('vendors'), function(v_ind, v_el)
			{
				query_count++;
				
				// Get articles for article number
				$.getJSON('/auto/api_pull_ajax/' + art_number_clear + '/' + sup_brand_clear + '/' + v_el + '/' + list_mode, function(data)
				{
					respond_count++;
					
					if (typeof data == 'object' && data != null)
					{
						// Go through each of the returned elements
						$.each(data, function(ind, el)
						{
							if (el.art_number_clear == $('#stock').data('primarynr') && el.brands_match == true)
							{
								el.is_primary = true;
								primaryList[el.hash] = el;
							}
							else
							{
								el.is_primary = false;
								
								if (!lowestPrices.hasOwnProperty(el.hash_short) || lowestPrices[el.hash_short].price > el.price) {
									lowestPrices[el.hash_short] = el;
								}
								
								if (!nonPrimaryList.hasOwnProperty(el.hash_short)) {
									nonPrimaryList[el.hash_short] = new Array();
								}
								
								nonPrimaryList[el.hash_short].push(el);
							}
						});
					}
					
					// Add to progress
					barCurrentLength = barCurrentLength + barStepSize;
					$('#api-progress').width(barCurrentLength + '%');
					
					// Finished?
					if (respond_count == query_count) {
						// Destroy progress bar
						$('#api-progress').width('100%');
						$('#api-progress-wrapper').slideUp(function() {
							$(this).remove();
							$('#api-progress-flying').remove();
						});
						
						if (!$.isEmptyObject(primaryList)) {
							// Use #stock table as a template for #stock_primary 
							if ($('#stock').data('primarynr') != undefined) {
								$('#stock').before($('#stock').clone().attr('id', 'stock_primary'));
								$('#stock_primary').after('<hr>').find('tbody').empty();
								$('#stock_primary').stupidtable();
							}
							// Build
							buildTable(primaryList, '#stock_primary', 'pri');
						}
						
						// Build
						buildTable(lowestPrices, '#stock', 'sec');
					}
					else
					{
						buildTable(lowestPrices, '#stock', 'append');
					}
				});
			});	
		}
		
		if ($('body').hasClass('api_list_article_page')) {
			apiPuller(false, $('body').data());
		}
		else {
			$.each($('tr.article'), apiPuller);
		}
	}
	
	// Model year filter
	$('#year_filter_top').slideDown();
	
	$('a.year_filter').tooltip().click(function()
	{
		if ($(this).attr('rel') == 'all')
		{
			$('tr.model_line').show();
		}
		else
		{
			$('tr.model_line').hide();
			$('tr.model_year_'+$(this).attr('rel')).show();
		}
		
		$('a.year_filter').removeClass('year_filter_selected');
		$(this).addClass('year_filter_selected');
		return false;
	});
		
	// [=] specials for m.jbauto
	// fading alert
	
	$('.fading-alert').animate({'opacity':0.3}, 'slow');
	$('.fading-alert').mouseenter(function()
	{
		$(this).stop().animate({'opacity':1});
	}).mouseleave(function()
	{
		$(this).stop().animate({'opacity':0.3});
	});
	
	// Sliding alerts
	
	$('div.sliding-alert').hide().slideDown();	
	
	// When nothing's in stock
	
	$('#not-in-stock-toggle').click(function()
	{
		$('a[href="#similar"]').filter('a[data-toggle="tab"]').trigger('click');
		return false;
	});
	
	// Cart improvements
	
	$('#cart_list a.cart-delete-item').removeClass('disabled').click(function()
	{
		$(this).parents('td.qty:first').find('input').val('0').trigger('change');
		return false;
	});
	$('#cart_list td.qty input').change(function()
	{
		if ($(this).val() == '0')
		{
			$(this).parents('td.qty').find('a.cart-delete-item').addClass('disabled');
		}
		else
		{
			$(this).parents('td.qty').find('a.cart-delete-item').removeClass('disabled');
		}
	});
	
	$('#update_cart_block').hide();
	
	$('#cart_list input').change(function()
	{
		$('.update_cart_slide').slideDown();
		$('.update_cart_fade').fadeIn();
		$('.update_cart_hide_slide').slideUp();
		$('.update_cart_hide_fade').fadeOut();
		$('#create_order_btn').prop('disabled',true).css('opacity',0.3);
		$('a.create_order_btn').attr('href','#').css('opacity',0.3);
		$('#update_cart_btn').addClass('btn-warning');
	});
	
	// Order Comment
	if (typeof localStorage != 'undefined')
	{
		function updCommentFields(comment)
		{
			if (typeof localStorage['order_comment'] != 'undefined')
			{
				$('#order_add_comment form textarea').val(localStorage['order_comment']);
				$('input[name="order_comment"]').val(localStorage['order_comment']);
			}
		}
		
		updCommentFields();
			
		$('a[href="#order_add_comment"]').show();
		$('#order_add_comment_form').submit(function(e)
		{
			e.preventDefault();
			localStorage['order_comment'] = $('#order_add_comment form textarea').val();
			
			updCommentFields();
			
			$('#order_add_comment').modal('hide');
		});
		
		$('form#order_form').submit(function(e)
		{
			delete localStorage['order_comment'];
		});
	}
	
	// ------------------
	// SAVE CARS
	// ------------------
	
	if (typeof JSON != 'undefined' && typeof localStorage != 'undefined')
	{
		// Trigger (box is hidden by default)
		$('#jbsmBox').show(); 
		
	
		if (typeof localStorage['jbsm'] == 'undefined')
		{
			var jbSavedModels = {};
		}
		else
		{
			var jbSavedModels = JSON.parse(localStorage['jbsm']);
		}
		
		// $(window).on('storage', jbSM_display_current_state); // Doesn't work correctly by some reason
		
		function jbSM_display_current_state()
		{
			// List cars
			$('li.jbsm-saved-car').remove();
			
			var mCounter=0;
			
			$.each(jbSavedModels, function(ind, el)
			{
				if (el.link.indexOf('find') > -1)
					var thehref = '/' + el.link;
				else
					var thehref = $('body').data('jsbm_urlbase') + '/' + el.link;
					
				var theobj = $('<li>').addClass('jbsm-saved-car').append($('<a>').attr('href', thehref).text(el.name));
				
				if ($('li.jbsm-list-header').is('*'))
					$(theobj).insertAfter('li.jbsm-list-header');
				else if ($('ul.jbsm-saved-cars').is('*'))
					$(theobj).appendTo('ul.jbsm-saved-cars');
					
				mCounter++;
			});
			
			$('.jbsm-saved-cars-num').text(mCounter);
			
			if (mCounter > 0)
			{
				$('li.jbsm-list-header, .jbsm-list').show();
			}
			else
			{
				$('li.jbsm-list-header, .jbsm-list').hide();
			}
			
			// Put the trigger box to it's correct state
			if ($('#jbsmBox').length > 0) //jbsmBox is trigger for this
			{
				if (typeof jbSavedModels[$('#jbsmBox').data('jbsm_tid')] != 'undefined')
				{
					$('#jbsmBox').removeClass('model-not-saved alert-info').addClass('model-saved alert-success');
				}
				else
				{
					$('#jbsmBox').removeClass('model-saved alert-success').addClass('model-not-saved alert-info');
				}
			}
		}
		
		jbSM_display_current_state();
		
		$('a.jbsm-trigger').click(function()
		{
			if ($(this).hasClass('jbsm-save-model'))
				jbSavedModels[$(this).data('jbsm_tid')] = {'name':$(this).data('jbsm_ttl'), 'link':$(this).data('jbsm_lnk')};
			else
				delete jbSavedModels[$(this).data('jbsm_tid')];
			
			var jSTR = JSON.stringify(jbSavedModels);
			localStorage["jbsm"] = jSTR;
			
			jbSM_display_current_state();
		
			return false;
		});
	}
	
	
	// Search bar improvements
	
	$('.search_type_selector a[href="#' + $('form#top_search_form').data('mode') + '"]').parent().addClass('active').siblings().removeClass('active');
	
	$('.search_type_selector a').click(function()
	{
		var searchbox = '#'+$(this).data('searchbox');
		
		$(searchbox).prop('placeholder', $(this).text());
		$(this).parent().addClass('active').siblings().removeClass('active');
		
		$(this).parents('form').attr('action', $(this).data('formaction'));
		
		if ($(searchbox).val().length > 0)
		{
			$(this).parents('form').submit();
		}
		else
		{
			$(searchbox).trigger('click').focus();
		}
		
		return false;
	});
	
	// P.Version improvements
	
	$('a#article-info-toggle').on('click', function(e)
	{
		e.preventDefault();
		$('a[href="#article-info"]').trigger('click');
	});
	
	// Moving `this` arts to the top
	$('#pv_articles tr.thisarticle').each(function(index, el)
	{
		$(el).detach().prependTo('#pv_articles tbody');
	});
	
	// MFG Filter
	$('#mfgfilter').show();
	
	$('.mfgfilter').keyup(function()
	{		
		if ($(this).val().length > 0)
		{
			var inp = $(this).val().toLowerCase();
			$('li.auto_manufacturer, li.mfg-navheader').hide();
			$('li[class^="mfg_' + inp + '"]').show();
			$(this).next('button').prop('disabled', true);
		}
		else
		{
			$('li.auto_manufacturer, li.mfg-navheader').show();
			$(this).next('button').prop('disabled', false);
		}
	});
	
	// Carousel
	$('#frontCarousel').carousel();
	
	function show_hide_init()
	{
		// Remove stuff, if there is nothing to show
		$('.removeifempty').show().each(function(ind, el)
		{
			if ($('.' + $(el).data('removeifempty')).length < 1)
			{
				$(el).hide();
			}
		});
		
		// Just the opposite of the above
		$('.showifempty').hide().each(function(ind, el)
		{
			if ($('.' + $(el).data('showifempty')).length < 1)
			{
				$(el).show();
			}
		});
	}
	
	show_hide_init();
	
	// Move Sidebar stuff to navbar's collapseable area
	$('.sidebar-collapseable ul').clone().addClass('mw-979-js-show').appendTo('.nav-collapse');
});

// We need this, 'cause Object.keys() isn't widely supported yet.
// UPD1: We don't need this 'cause we have $.isEmptyObject();
// function isObjEmpty(obj) {
	// for(var key in obj) {
		// if(obj.hasOwnProperty(key))
			// return false;
	// }
	// return true;
// }

// JS Sorter
(function(d){d.fn.stupidtable=function(b){return this.each(function(){var a=d(this);b=b||{};b=d.extend({},d.fn.stupidtable.default_sort_fns,b);a.on("click","th",function(){var n=a.children("tbody").children("tr"),e=d(this),j=0,m=d.fn.stupidtable.dir;a.find("th").slice(0,e.index()).each(function(){var a=d(this).attr("colspan")||1;j+=parseInt(a,10)});var l=e.data("sort-dir")===m.ASC?m.DESC:m.ASC,p=l==m.DESC?e.data("sort-desc")||e.data("sort")||null:e.data("sort")||null;null!==p&&(a.trigger("beforetablesort",
{column:j,direction:l}),a.css("display"),setTimeout(function(){var k=[],c=b[p];n.each(function(a,b){var c=d(b).children().eq(j),e=c.data("sort-value"),c="undefined"!==typeof e?e:c.text();k.push(c)});var f=[],g=0;if(e.data("sort-dir")&&!e.data("sort-desc"))for(c=k.length-1;0<=c;c--)f.push(c);else for(var h=k.slice(0).sort(c),c=0;c<k.length;c++){for(g=d.inArray(k[c],h);-1!=d.inArray(g,f);)g++;f.push(g)}a.find("th").data("sort-dir",null).removeClass("sorting-desc sorting-asc");e.data("sort-dir",l).addClass("sorting-"+
l);g=n.slice(0);for(h=c=0;h<f.length;h++)c=f[h],g[c]=n[h];f=d(g);a.children("tbody").append(f);a.trigger("aftertablesort",{column:j,direction:l});a.css("display")},10))})})};d.fn.stupidtable.dir={ASC:"asc",DESC:"desc"};d.fn.stupidtable.default_sort_fns={"int":function(b,a){return parseInt(b,10)-parseInt(a,10)},"float":function(b,a){return parseFloat(b)-parseFloat(a)},string:function(b,a){return b<a?-1:b>a?1:0},"string-ins":function(b,a){b=b.toLowerCase();a=a.toLowerCase();return b<a?-1:b>a?1:0}}})(jQuery);


