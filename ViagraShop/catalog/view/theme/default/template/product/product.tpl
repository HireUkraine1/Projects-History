<?php echo $header; ?>

<div class="container">
  <div class="breadcrumb">
    <?php foreach ($breadcrumbs as $breadcrumb) { ?>
	<?php if($breadcrumb != end($breadcrumbs)) { ?>
    <?php echo $breadcrumb['separator']; ?><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a>
	<?php } else { ?>
	<?php echo $breadcrumb['separator']; ?><span><?php echo $breadcrumb['text']; ?></span>
    <?php } ?>
    <?php } ?>
  </div>
</div>

<?php echo $column_left; ?><?php echo $column_right; ?>
  
 <div id="content"><?php echo $content_top; ?>
  <div class="container">
  <div class="product-info">
  <h1><?php echo $heading_title; ?></h1>
  
  <div class="row">
  
  <div class="col-sm-6">
  
	<?php if ($thumb || $images) { ?>
	
	
		<ul class="thumbnails">
		<?php if ($thumb) { ?>
		<li><a title="<?php echo $heading_title; ?>" href="<?php echo $popup; ?>" class="thumbnail"><img alt="<?php echo $heading_title; ?>" title="<?php echo $heading_title; ?>" src="<?php echo $thumb; ?>"></a></li>
		<?php } ?>


		<?php if ($images) { ?>
		<?php foreach ($images as $image) { ?>
		<li class="image-additional"><a title="<?php echo $heading_title; ?>" href="<?php echo $image['popup']; ?>" class="thumbnail"> <img alt="<?php echo $heading_title; ?>" title="<?php echo $heading_title; ?>" src="<?php echo $image['thumb']; ?>"></a></li>
		<?php } ?>
		<?php } ?>

		</ul>

	<?php } ?>
  
  </div>
  <div class="col-sm-6">
	
 
	  <div class="description">
        <?php if ($manufacturer) { ?>
        <span><?php echo $text_manufacturer; ?></span> <a href="<?php echo $manufacturers; ?>"><?php echo $manufacturer; ?></a><br />
        <?php } ?>
        <span><?php echo $text_model; ?></span> <?php echo $model; ?><br />
        <?php if ($reward) { ?>
        <span><?php echo $text_reward; ?></span> <?php echo $reward; ?><br />
        <?php } ?>
        <span><?php echo $text_stock; ?></span> <?php echo $stock; ?></div>
		
      <?php if ($price) { ?>
      <div class="price">
        <?php if (!$special) { ?>
        <span class="price-now"><?php echo $price; ?></span>
		<input type="hidden" id="origin_price" value="<?php echo $price; ?>" />
        <?php } else { ?>
        <span class="price-old"><?php echo $price; ?></span> <span class="price-new"><?php echo $special; ?></span>
		<input type="hidden" id="origin_price" value="<?php echo $special; ?>" />
        <?php } ?>
        <?php if ($tax) { ?>
        <span class="price-tax"><?php echo $text_tax; ?> <?php echo $tax; ?></span><br />
        <?php } ?>
        <?php if ($points) { ?>
        <span class="reward"><small><?php echo $text_points; ?> <?php echo $points; ?></small></span><br />
        <?php } ?>
        <?php if ($discounts) { ?>
        <div class="discount">
          <?php foreach ($discounts as $discount) { ?>
          <?php echo sprintf($text_discount, $discount['quantity'], $discount['price']); ?><br />
          <?php } ?>
        </div>
        <?php } ?>
      </div>
      <?php } ?>
	  
      <?php if ($options) { ?>
      <div class="options">
        <?php /*<h2><?php echo $text_option; ?></h2>*/ ?>
        <!--<br />-->
        <?php foreach ($options as $option) { ?>
        <?php if ($option['type'] == 'select') { ?>
        <div id="option-<?php echo $option['product_option_id']; ?>" class="option">
          <?php if ($option['required']) { ?>
          
          <span><?php echo $option['name']; ?>:</span><br />
		  
		  
		  <?php } ?>
          <select class="form-control choose-mg" name="option[<?php echo $option['product_option_id']; ?>]">
            <?php /*if (count($option['option_value']) > 1) { ?>
			<option value=""><?php echo $text_select; ?></option>
			<?php }*/ ?>
            <?php foreach ($option['option_value'] as $option_value) { ?>
            
            <?php if ($option_value['price']) { ?>
			<option value="<?php echo $option_value['product_option_value_id']; ?>" data-mult="<?php echo $option_value['price_prefix']; ?>" data-how="<?php echo $option_value['price']; ?>"><?php echo $option_value['name']; ?></option>
            <?php } else { ?>
            <option value="<?php echo $option_value['product_option_value_id']; ?>" data-mult="+" data-how="$0"><?php echo $option_value['name']; ?></option>
            <?php } ?>
			
            <?php } ?>
          </select>
        </div>
        
        <?php } ?>
        <?php if ($option['type'] == 'radio') { ?>
        <div id="option-<?php echo $option['product_option_id']; ?>" class="option">
          <?php if ($option['required']) { ?>
          <span class="required">*</span>
          <?php } ?>
          <b><?php echo $option['name']; ?>:</b><br />
          <?php foreach ($option['option_value'] as $option_value) { ?>
          <input type="radio" name="option[<?php echo $option['product_option_id']; ?>]" value="<?php echo $option_value['product_option_value_id']; ?>" id="option-value-<?php echo $option_value['product_option_value_id']; ?>" />
          <label for="option-value-<?php echo $option_value['product_option_value_id']; ?>"><?php echo $option_value['name']; ?>
            <?php if ($option_value['price']) { ?>
            (<?php echo $option_value['price_prefix']; ?><?php echo $option_value['price']; ?>)
            <?php } ?>
          </label>
          <br />
          <?php } ?>
        </div>
        <br />
        <?php } ?>
        <?php if ($option['type'] == 'checkbox') { ?>
        <div id="option-<?php echo $option['product_option_id']; ?>" class="option">
          <?php if ($option['required']) { ?>
          <span class="required">*</span>
          <?php } ?>
          <b><?php echo $option['name']; ?>:</b><br />
          <?php foreach ($option['option_value'] as $option_value) { ?>
          <input type="checkbox" name="option[<?php echo $option['product_option_id']; ?>][]" value="<?php echo $option_value['product_option_value_id']; ?>" id="option-value-<?php echo $option_value['product_option_value_id']; ?>" />
          <label for="option-value-<?php echo $option_value['product_option_value_id']; ?>"><?php echo $option_value['name']; ?>
            <?php if ($option_value['price']) { ?>
            (<?php echo $option_value['price_prefix']; ?><?php echo $option_value['price']; ?>)
            <?php } ?>
          </label>
          <br />
          <?php } ?>
        </div>
        <br />
        <?php } ?>
        <?php if ($option['type'] == 'image') { ?>
        <div id="option-<?php echo $option['product_option_id']; ?>" class="option">
          <?php if ($option['required']) { ?>
          <span class="required">*</span>
          <?php } ?>
          <b><?php echo $option['name']; ?>:</b><br />
          <table class="option-image">
            <?php foreach ($option['option_value'] as $option_value) { ?>
            <tr>
              <td style="width: 1px;"><input type="radio" name="option[<?php echo $option['product_option_id']; ?>]" value="<?php echo $option_value['product_option_value_id']; ?>" id="option-value-<?php echo $option_value['product_option_value_id']; ?>" /></td>
              <td><label for="option-value-<?php echo $option_value['product_option_value_id']; ?>"><img src="<?php echo $option_value['image']; ?>" alt="<?php echo $option_value['name'] . ($option_value['price'] ? ' ' . $option_value['price_prefix'] . $option_value['price'] : ''); ?>" /></label></td>
              <td><label for="option-value-<?php echo $option_value['product_option_value_id']; ?>"><?php echo $option_value['name']; ?>
                  <?php if ($option_value['price']) { ?>
                  (<?php echo $option_value['price_prefix']; ?><?php echo $option_value['price']; ?>)
                  <?php } ?>
                </label></td>
            </tr>
            <?php } ?>
          </table>
        </div>
        <br />
        <?php } ?>
        <?php if ($option['type'] == 'text') { ?>
        <div id="option-<?php echo $option['product_option_id']; ?>" class="option">
          <?php if ($option['required']) { ?>
          <span class="required">*</span>
          <?php } ?>
          <b><?php echo $option['name']; ?>:</b><br />
          <input type="text" name="option[<?php echo $option['product_option_id']; ?>]" value="<?php echo $option['option_value']; ?>" />
        </div>
        <br />
        <?php } ?>
        <?php if ($option['type'] == 'textarea') { ?>
        <div id="option-<?php echo $option['product_option_id']; ?>" class="option">
          <?php if ($option['required']) { ?>
          <span class="required">*</span>
          <?php } ?>
          <b><?php echo $option['name']; ?>:</b><br />
          <textarea name="option[<?php echo $option['product_option_id']; ?>]" cols="40" rows="5"><?php echo $option['option_value']; ?></textarea>
        </div>
        <br />
        <?php } ?>
        <?php if ($option['type'] == 'file') { ?>
        <div id="option-<?php echo $option['product_option_id']; ?>" class="option">
          <?php if ($option['required']) { ?>
          <span class="required">*</span>
          <?php } ?>
          <b><?php echo $option['name']; ?>:</b><br />
          <input type="button" value="<?php echo $button_upload; ?>" id="button-option-<?php echo $option['product_option_id']; ?>" class="button">
          <input type="hidden" name="option[<?php echo $option['product_option_id']; ?>]" value="" />
        </div>
        <br />
        <?php } ?>
        <?php if ($option['type'] == 'date') { ?>
        <div id="option-<?php echo $option['product_option_id']; ?>" class="option">
          <?php if ($option['required']) { ?>
          <span class="required">*</span>
          <?php } ?>
          <b><?php echo $option['name']; ?>:</b><br />
          <input type="text" name="option[<?php echo $option['product_option_id']; ?>]" value="<?php echo $option['option_value']; ?>" class="date" />
        </div>
        <br />
        <?php } ?>
        <?php if ($option['type'] == 'datetime') { ?>
        <div id="option-<?php echo $option['product_option_id']; ?>" class="option">
          <?php if ($option['required']) { ?>
          <span class="required">*</span>
          <?php } ?>
          <b><?php echo $option['name']; ?>:</b><br />
          <input type="text" name="option[<?php echo $option['product_option_id']; ?>]" value="<?php echo $option['option_value']; ?>" class="datetime" />
        </div>
        <br />
        <?php } ?>
        <?php if ($option['type'] == 'time') { ?>
        <div id="option-<?php echo $option['product_option_id']; ?>" class="option">
          <?php if ($option['required']) { ?>
          <span class="required">*</span>
          <?php } ?>
          <b><?php echo $option['name']; ?>:</b><br />
          <input type="text" name="option[<?php echo $option['product_option_id']; ?>]" value="<?php echo $option['option_value']; ?>" class="time" />
        </div>
        <br />
        <?php } ?>
        <?php } ?>
      </div>
      <?php } ?>
	  
      <div class="cart">
        <div><?php echo $text_qty; ?>
          <input type="number" min="1" name="quantity" size="2" class="form-control" value="<?php echo $minimum; ?>" />
          <input type="hidden" name="product_id" size="2" value="<?php echo $product_id; ?>" />
          &nbsp;
          <input type="button" value="<?php echo $button_cart; ?>" id="button-cart" class="buy" />
		  <?php /*
          <span>&nbsp;&nbsp;<?php echo $text_or; ?>&nbsp;&nbsp;</span>
          <span class="links"><a onclick="addToWishList('<?php echo $product_id; ?>');"><?php echo $button_wishlist; ?></a><br />
            <a onclick="addToCompare('<?php echo $product_id; ?>');"><?php echo $button_compare; ?></a></span>
			*/ ?>
        </div>
        <?php if ($minimum > 1) { ?>
        <div class="minimum"><?php echo $text_minimum; ?></div>
        <?php } ?>
      </div>
      <?php if (true) { ?>
      <div class="review">
        <div>

	    <?php for ($i = 1; $i < 6; $i++) { ?>
	    <?php if ($i <= $rating) { ?>
	    <span class="glyphicon glyphicon glyphicon-star"></span>
	    <?php } else { ?>
	    <span class="glyphicon glyphicon-star-empty"></span>
	    <?php } ?>
	    <?php } ?>
		
		<a onclick="$('a[href=\'#tab-review\']').trigger('click'); $('html, body').animate({ scrollTop: $('#tabs').offset().top }, 500);"><?php echo $reviews; ?></a>&nbsp;&nbsp;/&nbsp;&nbsp;<a onclick="$('a[href=\'#tab-review\']').trigger('click'); $('html, body').animate({ scrollTop: $('#review-title').offset().top }, 500);"><?php echo $text_write; ?></a></div>
		<?php /*
        <div class="share">
		<!-- AddThis Button BEGIN -->
			<div class="share42init" data-image="<?php echo $thumb; ?>"></div>
			<script type="text/javascript" src="catalog/view/javascript/jquery/share42/share42.js"></script> 
		<!-- AddThis Button END --> 
        </div>
		*/ ?>
      </div>
      <?php } ?> 
	  
 
  </div>
  
  
  
  </div>
  </div>
  <ul id="tabs" class="htabs nav nav-tabs"><li><a href="#tab-description"><?php echo $tab_description; ?></a></li>
    <?php if ($attribute_groups) { ?>
    <li><a href="#tab-attribute"><?php echo $tab_attribute; ?></a></li>
    <?php } ?>
    <?php if ($review_status) { ?>
    <li><a href="#tab-review"><?php echo $tab_review; ?></a></li>
    <?php } ?>
    <?php if ($products) { ?>
    <li><a href="#tab-related"><?php echo $tab_related; ?> (<?php echo count($products); ?>)</a></li>
    <?php } ?>
  </ul>
  <div id="tab-description" class="tab-content"><?php echo $description; ?></div>
  <?php if ($attribute_groups) { ?>
  <div id="tab-attribute" class="tab-content">
    <table class="attribute">
      <?php foreach ($attribute_groups as $attribute_group) { ?>
      <thead>
        <tr>
          <td colspan="2"><?php echo $attribute_group['name']; ?></td>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($attribute_group['attribute'] as $attribute) { ?>
        <tr>
          <td><?php echo $attribute['name']; ?></td>
          <td><?php echo $attribute['text']; ?></td>
        </tr>
        <?php } ?>
      </tbody>
      <?php } ?>
    </table>
  </div>
  <?php } ?>
  <?php if ($review_status) { ?>
  <div id="tab-review" class="tab-content">
    <div id="review"></div>
    <h2 id="review-title"><?php echo $text_write; ?></h2>
    
	<label for="input-name" class="control-label"><?php echo $entry_name; ?></label>
    <input style="max-width: 300px;" type="text" id="input-name" class="form-control" name="name" value="" />
    <br />
	<label for="input-text" class="control-label"><?php echo $entry_review; ?></label>
    <textarea id="input-text" name="text" cols="40" rows="4" class="form-control" style="resize: none;"></textarea>
    <br />
    <label for="input-captcha" class="control-label"><?php echo $entry_rating; ?></label>&nbsp;&nbsp;&nbsp;<span><?php echo $entry_bad; ?></span>&nbsp;
    <input type="radio" name="rating" value="1" />
    &nbsp;
    <input type="radio" name="rating" value="2" />
    &nbsp;
    <input type="radio" name="rating" value="3" />
    &nbsp;
    <input type="radio" name="rating" value="4" />
    &nbsp;
    <input type="radio" name="rating" value="5" />
    &nbsp;<span><?php echo $entry_good; ?></span><br />
    <br />
    <label for="input-captcha" class="control-label"><?php echo $entry_captcha; ?></label><br>
    <input style="max-width: 100px; display: inline-block;" id="input-captcha" class="form-control" autocomplete="off" type="text" name="captcha" value="" />
    <img style="margin-top: -3px;" src="index.php?route=product/product/captcha" alt="" id="captcha" /><br />
    <br />
    <div class="buttons">
      <a id="button-review" class="buy"><?php echo $button_continue; ?></a>
    </div>
  </div>
  <?php } ?>
  <?php if ($products) { ?>
  <div id="tab-related" class="tab-content">
    <div class="box-product">
      <?php foreach ($products as $product) { ?>
      <div>
        <?php if ($product['thumb']) { ?>
        <div class="image"><a href="<?php echo $product['href']; ?>"><img src="<?php echo $product['thumb']; ?>" alt="<?php echo $product['name']; ?>" /></a></div>
        <?php } ?>
        <div class="name"><a href="<?php echo $product['href']; ?>"><?php echo $product['name']; ?></a></div>
        <?php if ($product['price']) { ?>
        <div class="price">
          <?php if (!$product['special']) { ?>
          <?php echo $product['price']; ?>
          <?php } else { ?>
          <span class="price-old"><?php echo $product['price']; ?></span> <span class="price-new"><?php echo $product['special']; ?></span>
          <?php } ?>
        </div>
        <?php } ?>
        <?php if ($product['rating']) { ?>
        <div class="rating"><img src="catalog/view/theme/default/image/stars-<?php echo $product['rating']; ?>.png" alt="<?php echo $product['reviews']; ?>" /></div>
        <?php } ?>
        <a onclick="addToCart('<?php echo $product['product_id']; ?>');" class="button"><?php echo $button_cart; ?></a></div>
      <?php } ?>
    </div>
  </div>
  <?php } ?>
  <?php if ($tags) { ?>
  <div class="tags"><b><?php echo $text_tags; ?></b>
    <?php for ($i = 0; $i < count($tags); $i++) { ?>
    <?php if ($i < (count($tags) - 1)) { ?>
    <a href="<?php echo $tags[$i]['href']; ?>"><?php echo $tags[$i]['tag']; ?></a>,
    <?php } else { ?>
    <a href="<?php echo $tags[$i]['href']; ?>"><?php echo $tags[$i]['tag']; ?></a>
    <?php } ?>
    <?php } ?>
  </div>
  <?php } ?>
  <?php echo $content_bottom; ?>
  </div>
  </div>
<script><!--
$(document).ready(function() {
	
	$('.thumbnails').magnificPopup({
		type:'image',
		delegate: 'a',
		gallery: {
			enabled:true
		}
	});
	
});
//--></script> 
<script><!--
$('#button-cart').bind('click', function() {
	$.ajax({
		url: 'index.php?route=checkout/cart/add',
		type: 'post',
		data: $('.product-info input[type=\'number\'], .product-info input[type=\'text\'], .product-info input[type=\'hidden\'], .product-info input[type=\'radio\']:checked, .product-info input[type=\'checkbox\']:checked, .product-info select, .product-info textarea'),
		dataType: 'json',
		success: function(json) {
			$('.success, .warning, .attention, information, .error').remove();
			
			if (json['error']) {
				if (json['error']['option']) {
					for (i in json['error']['option']) {
						$('#option-' + i).after('<span class="error">' + json['error']['option'][i] + '</span>');
					}
				}
			} 
			
			if (json['success']) {
				$('#notification').html('<div class="success" style="display: none;"><div class="container">' + json['success'] + '<img src="catalog/view/theme/default/image/close.png" alt="" class="close" /></div></div>');
					
				$('.success').fadeIn('slow');
					
				$('#cart-total').html(json['total']);
				
				$('html, body').animate({ scrollTop: 0 }, 'slow'); 
			}	
		}
	});
});
//--></script>
<script><!--
$('#review .pagination a').on('click', function() {
	$('#review').fadeOut('slow');
		
	$('#review').load(this.href);
	
	$('#review').fadeIn('slow');
	
	return false;
});			

$('#review').load('index.php?route=product/product/review&product_id=<?php echo $product_id; ?>');

$('#button-review').bind('click', function() {
	$.ajax({
		url: 'index.php?route=product/product/write&product_id=<?php echo $product_id; ?>',
		type: 'post',
		dataType: 'json',
		data: 'name=' + encodeURIComponent($('input[name=\'name\']').val()) + '&text=' + encodeURIComponent($('textarea[name=\'text\']').val()) + '&rating=' + encodeURIComponent($('input[name=\'rating\']:checked').val() ? $('input[name=\'rating\']:checked').val() : '') + '&captcha=' + encodeURIComponent($('input[name=\'captcha\']').val()),
		beforeSend: function() {
			$('.success, .warning').remove();
			$('#button-review').attr('disabled', true);
			$('#review-title').after('<div class="attention"><div class="container"><img src="catalog/view/theme/default/image/loading.gif" alt="" /> <?php echo $text_wait; ?></div></div>');
		},
		complete: function() {
			$('#button-review').attr('disabled', false);
			$('.attention').remove();
		},
		success: function(data) {
			if (data['error']) {
				$('#review-title').after('<div class="warning"><div class="container">' + data['error'] + '</div></div>');
				$('html, body').animate({ scrollTop: $('#review-title').offset().top }, 500);
			}
			
			if (data['success']) {
				$('#review-title').after('<div class="success"><div class="container">' + data['success'] + '</div></div>');
								
				$('input[name=\'name\']').val('');
				$('textarea[name=\'text\']').val('');
				$('input[name=\'rating\']:checked').attr('checked', '');
				$('input[name=\'captcha\']').val('');
				$('html, body').animate({ scrollTop: $('#review-title').offset().top }, 500);
			}
		}
	});
});
//--></script> 
<script><!--
$('#tabs a').tabs();
//--></script>
<?php /*
<script type="text/javascript" src="catalog/view/javascript/jquery/ui/jquery-ui-timepicker-addon.js"></script> 
<script type="text/javascript"><!--
$(document).ready(function() {
	
	$('.date').datepicker({dateFormat: 'yy-mm-dd'});
	$('.datetime').datetimepicker({
		dateFormat: 'yy-mm-dd',
		timeFormat: 'h:m'
	});
	$('.time').timepicker({timeFormat: 'h:m'});
});
//--></script>
*/ ?>
<script>
$('.choose-mg').change(function(){
	var opt = $(this).find('option:selected'),
	mult = opt.data('mult'),
	how = parseFloat(opt.data('how').replace('$', '')),
	prw = $('.product-info .price .price-now'),
	price = parseFloat($('#origin_price').val().replace('$', ''));
	
	if (mult == '+') {
		var res = price + how;
	} else {
		var res = price - how;
	}
	prw.text('$' + (res.formatMoney()));
});

Number.prototype.formatMoney = function(c, d, t){
	var n = this, 
	c = isNaN(c = Math.abs(c)) ? 2 : c, 
	d = d == undefined ? "." : d, 
	t = t == undefined ? "," : t, 
	s = n < 0 ? "-" : "", 
	i = parseInt(n = Math.abs(+n || 0).toFixed(c)) + "", 
	j = (j = i.length) > 3 ? j % 3 : 0;
	return s + (j ? i.substr(0, j) + t : "") + i.substr(j).replace(/(\d{3})(?=\d)/g, "$1" + t) + (c ? d + Math.abs(n - i).toFixed(c).slice(2) : "");
};
</script>
<?php echo $footer; ?>