$(function()
{
	// Withdraw unnecessary stuff
	$('.utilityRemove').remove();
	
	// Fadeout msgs
	$('.alert-fadeout').animate({'opacity':0.3}, 'slow').mouseenter(function()
	{
		$(this).stop().animate({'opacity':1});
	}).mouseleave(function()
	{
		$(this).stop().animate({'opacity':0.3});
	});
	
	// Brand-management improvements
	$('#all_brands_trigger').change(function()
	{
		var $checkboxesGroup = $(this).parents('.checkboxes-group');

		if ($(this).prop("checked"))
		{
			$checkboxesGroup.find('input').not(this).prop('checked', true).trigger('change');
		}
		else
		{
			$checkboxesGroup.find('input').not(this).prop('checked', false).trigger('change');
		}
	});

	$('.labels-inline input[type="checkbox"]').change(function() {
		if ($(this).prop("checked")) {
			$(this).parent('label').addClass('checkbox-checked');
		} else {
			$(this).parent('label').removeClass('checkbox-checked');
		}
	}).each(function(ind, el) {
		if ($(this).prop("checked")) {
			$(this).parent('label').addClass('checkbox-checked');
		}
	});

	// Order Status Change
	$('.order_change_status').on('change', function(e)
	{
		$('#order_items_update_form select.order_line_status').trigger('change');
		var val1 = $(this).find('option:selected').val();
		$('select.order_line_status option').filter(function() {
			return $(this).val() == val1; 
		}).prop('selected', true);
	});
	
	// Order Delivery Change
	$('.order_change_d_mthd').on('change', function(e)
	{
		$('#order_items_update_form input:first').trigger('change');
		var val1 = $(this).find('option:selected').val();
		$('input.delivery-method-field').val(val1);
	});
	
	$('#add_item form').on('submit', function(e)
	{
		e.preventDefault();
		$('#add_item').modal('hide');
	});
	
	// Order Item Add
	$(document).on('keyup change', '#add_item form input, #add_item form select', function (e)
	{
		$('#order_items_update_form input:first').trigger('change');
		$('table tr.table-new-line').css('display', 'table-row');
		var slctr = "*[name='" + $(this).attr('name') + "']";
		
		if ($(this).prop('tagName') == 'SELECT')
		{
			var val1 = $(this).find('option:selected').val();
			
			$(slctr).find('option').prop('selected', false).filter(function() {
				return $(this).val() == val1; 
			}).prop('selected', true);
		}
		else if ($(this).prop('tagName') == 'INPUT')
		{
			$(slctr).val($(this).val());
		}
	});
	
	
	// Order Item Add (v.2)
	// Car Add
	
	// common
	// set current state
	if (typeof localStorage['jb_orderid'] !== 'undefined')
	{
		$('.jb_orderedit_num').text(localStorage['jb_orderid']);
		$('.jb_orderedit_orderlink').attr('href', '/admin/order_data/' +  localStorage['jb_orderid']);
		$('.jb_orderedit_savelink').attr('href',  '/admin/order_add_to/' + localStorage['jb_orderid']);
		
		$('.jb_addcar_savelink').each(function(ind,el)
		{
			var newhref = $(el).attr('href').replace('-ONUM-', localStorage['jb_orderid']);
			$(el).attr('href',  newhref);
		});
	}
	
	if (typeof localStorage['jb_orderhumanid'] !== 'undefined')
	{	
		$('.jb_orderedit_hnum').text(localStorage['jb_orderhumanid']);
	}
	
	// common
	// cancel
	var jb_orderedit_cancel = function(e)
	{
		if (typeof e !== 'undefined')
			e.preventDefault();
			
		delete localStorage['jb_orderedit'];
		delete localStorage['jb_orderid'];
		delete localStorage['jb_orderhumanid'];
		delete localStorage['jb_addcar'];
		delete localStorage['jb_usreid'];
		delete localStorage['jb_addschedule'];
		
		// $('body').removeClass('jb_orderedit_active');
	}
	$('.jb_orderedit_cancel').click(jb_orderedit_cancel);
	
	if ($('#jb_orderedit_orderpage').is('*'))
	{
		jb_orderedit_cancel();
	}
	
	// Order-Edit
	// create
	$('.order-add-line').click(function(e)
	{
		e.preventDefault();
		
		localStorage['jb_orderedit'] = 'true';
		localStorage['jb_orderid'] = $(this).data('orderid');
		localStorage['jb_orderhumanid'] = $(this).data('orderhumanid');
		window.location.href = $(this).attr('href');
	});
	
	// Order-Edit
	// set current state
	if (typeof localStorage['jb_orderedit'] !== 'undefined')
	{
		$('body').addClass('jb_orderedit_active');
	}
	
	// Order-Edit Car Add
	// create
	$('.order-add-car').click(function(e)
	{
		e.preventDefault();
		
		localStorage['jb_addcar'] = 'true';
		localStorage['jb_orderid'] = $(this).data('orderid');
		localStorage['jb_orderhumanid'] = $(this).data('orderhumanid');
		localStorage['jb_usreid'] = $(this).data('userid');
		window.location.href = $(this).attr('href');
	});
	
	// set current state
	if (typeof localStorage['jb_addcar'] !== 'undefined')
	{
		$('body').addClass('jb_addcar_active');
	}

	// Js-Form Yizzi-sqizzi! v.0.3 (+keyup) [How-to use: Just add .js-form to the form]
	$('.js-form button[type="submit"]').prop('disabled', true).css('opacity', 0.3).html('<i class="icon-ok-sign icon-white"></i> Изменения сохранены');
	$('.js-form.js-form-button-hide button[type="submit"]').hide();
	$('.js-form input, .js-form select, .js-form textarea').on('change keyup', function()
	{
		if (!$(this).parents('form.js-form').hasClass('js-form-changed'))
		{
			$(this).parents('form.js-form').addClass('js-form-changed');
			
			$('<div id="js-form_changed_alert">')
				.html('<i class="icon-exclamation-sign"></i> <strong>Не забудьте сохранить изменения</strong>. <button type="submit" class="btn btn-warning btn-mini">Сохранить</button> ')
				.addClass('alert')
				.prependTo($(this).parents('form.js-form'))
				.hide()
				.slideDown();
				
			$('.js-form button[type="submit"]').prop('disabled', false).addClass('btn-warning').css('opacity', 1).html('<i class="icon-ok-sign icon-white"></i> Сохранить изменения');
			$('.js-form.js-form-button-hide button[type="submit"]').show();
			
			$('.js-form .js-form-toggleable').hide();
		}
	});
	
	// Popovers
	$('.help-guide').popover('show');
	$('.help-guide.destroyable').click(function()
	{
		$(this).popover('destroy');
	});
	

});