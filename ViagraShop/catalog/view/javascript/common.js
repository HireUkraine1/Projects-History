$(document).ready(function() {
	
	$('#footer h4 span').on('click', function() {
		var th = $(this).parent();
		if(th.hasClass('active')) {
			th.removeClass('active').next().slideUp();
		} else {
			th.addClass('active').next().slideDown();
		}
	});
	
	/* Ajax Cart */
	/*
	$('#cart > .heading a').live('click', function() {
		$('#cart').addClass('active');
		
		$('#cart').load('index.php?route=module/cart #cart > *');
		
		$('#cart').live('mouseleave', function() {
			$(this).removeClass('active');
		});
	});
	*/
	
	$(document).on('click', '.success img, .warning img, .attention img, .information img', function() {
		$(this).parent().parent().fadeOut('slow', function() {
			$(this).remove();
		});
	});	
	
	$('a.track').on('click', function() {
		var th = $(this),
		id = th.data('id');
		$.ajax({
			url: 'track.php',
			type: 'post',
			data: 'track_id=' + id,
			beforeSend: function() {
				th.hide(0).after('<img style="padding: 0 10px;" src="catalog/view/theme/default/image/loading.gif" alt="load" />');
			},
			success: function(res) {
				$('#myModal .modal-body').html(res);
				$("#myModal").modal({show: true});
				th.show(0).next().remove();
			}
		});
	});
	
	
});

function getURLVar(key) {
	var value = [];
	
	var query = String(document.location).split('?');
	
	if (query[1]) {
		var part = query[1].split('&');

		for (i = 0; i < part.length; i++) {
			var data = part[i].split('=');
			
			if (data[0] && data[1]) {
				value[data[0]] = data[1];
			}
		}
		
		if (value[key]) {
			return value[key];
		} else {
			return '';
		}
	}
} 

function addToCart(product_id, quantity) {
	quantity = typeof(quantity) != 'undefined' ? quantity : 1;

	$.ajax({
		url: 'index.php?route=checkout/cart/add',
		type: 'post',
		data: 'product_id=' + product_id + '&quantity=' + quantity,
		dataType: 'json',
		success: function(json) {
			$('.success, .warning, .attention, .information, .error').remove();
			
			if (json['redirect']) {
				location = json['redirect'];
			}
			
			if (json['success']) {
				$('#notification').html('<div class="success" style="display: none;">' + json['success'] + '<img src="catalog/view/theme/default/image/close.png" alt="" class="close" /></div>');
				
				$('.success').fadeIn('slow');
				
				$('#cart-total').html(json['total']);
				
				$('html, body').animate({ scrollTop: 0 }, 'slow'); 
			}	
		}
	});
}