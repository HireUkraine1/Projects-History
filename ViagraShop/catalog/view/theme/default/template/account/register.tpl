<?php echo $header; ?>
<?php if ($error_warning) { ?>
<div class="warning"><div class="container"><?php echo $error_warning; ?></div></div>
<?php } ?>

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


<div id="content">
<div class="container">
<?php echo $content_top; ?>
  <h1><?php echo $heading_title; ?></h1>
  <p><?php echo $text_account_already; ?></p>
  <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" class="form-horizontal">
    <h3><?php echo $text_your_details; ?></h3>
    <div class="content">
		<div class="form-group">
		  <label for="firstname" class="col-sm-2 control-label"><?php echo $entry_firstname; ?></label>
		  <div class="col-sm-10">
          <input class="form-control" placeholder="<?php echo $entry_firstname; ?>" type="text" id="firstname" name="firstname" value="<?php echo $firstname; ?>" />
            <?php if ($error_firstname) { ?>
            <span class="error"><?php echo $error_firstname; ?></span>
            <?php } ?>
			</div>
		</div>
		<div class="form-group">
		<label for="lastname" class="col-sm-2 control-label"><?php echo $entry_lastname; ?></label>
		  <div class="col-sm-10">
          <input class="form-control" placeholder="<?php echo $entry_lastname; ?>" type="text" name="lastname" id="lastname" value="<?php echo $lastname; ?>" />
            <?php if ($error_lastname) { ?>
            <span class="error"><?php echo $error_lastname; ?></span>
            <?php } ?>
			</div>
		</div>
		<div class="form-group">
		  <label for="email" class="col-sm-2 control-label"><?php echo $entry_email; ?></label>
		  <div class="col-sm-10">
          <input class="form-control" placeholder="<?php echo $entry_email; ?>" type="text" name="email" id="email" value="<?php echo $email; ?>" />
            <?php if ($error_email) { ?>
            <span class="error"><?php echo $error_email; ?></span>
            <?php } ?>
			</div>
		</div>
		<div class="form-group">
		<label for="telephone" class="col-sm-2 control-label"><?php echo $entry_telephone; ?></label>
		  <div class="col-sm-10">
          <input class="form-control" placeholder="<?php echo $entry_telephone; ?>" type="text" name="telephone" id="telephone" value="<?php echo $telephone; ?>" />
            <?php if ($error_telephone) { ?>
            <span class="error"><?php echo $error_telephone; ?></span>
            <?php } ?>
			</div>
		</div>
		<div class="form-group">
		  <label for="fax" class="col-sm-2 just-label"><?php echo $entry_fax; ?></label>
		  <div class="col-sm-10">
          <input class="form-control" placeholder="<?php echo $entry_fax; ?>" type="text" name="fax" id="fax" value="<?php echo $fax; ?>" />
		  </div>
		</div>
    </div>
	
	
	
    <h3><?php echo $text_your_address; ?></h3>
    <div class="content">

		<div class="form-group">
		  <label for="company" class="col-sm-2 just-label"><?php echo $entry_company; ?></label>
		  <div class="col-sm-10">
          <input id="company" placeholder="<?php echo $entry_company; ?>" class="form-control" type="text" name="company" value="<?php echo $company; ?>" />
		  </div>
		</div>
		
		<div class="form-group" style="display: <?php echo (count($customer_groups) > 1 ? 'block' : 'none'); ?>;">
          <?php echo $entry_customer_group; ?>
          <?php foreach ($customer_groups as $customer_group) { ?>
            <?php if ($customer_group['customer_group_id'] == $customer_group_id) { ?>
            <input class="form-control" type="radio" name="customer_group_id" value="<?php echo $customer_group['customer_group_id']; ?>" id="customer_group_id<?php echo $customer_group['customer_group_id']; ?>" checked="checked" />
            <label for="customer_group_id<?php echo $customer_group['customer_group_id']; ?>"><?php echo $customer_group['name']; ?></label>
            <br />
            <?php } else { ?>
            <input class="form-control" type="radio" name="customer_group_id" value="<?php echo $customer_group['customer_group_id']; ?>" id="customer_group_id<?php echo $customer_group['customer_group_id']; ?>" />
            <label for="customer_group_id<?php echo $customer_group['customer_group_id']; ?>"><?php echo $customer_group['name']; ?></label>
            <br />
            <?php } ?>
            <?php } ?>
		</div>
		
		<div class="form-group" id="company-id-display">
		  <label id="company-id-required" for="company_id" class="col-sm-2 control-label"><?php echo $entry_company_id; ?></label>
		  <div class="col-sm-10">
          <input placeholder="<?php echo $entry_company_id; ?>" id="company_id" class="form-control" type="text" name="company_id" value="<?php echo $company_id; ?>" />
            <?php if ($error_company_id) { ?>
            <span class="error"><?php echo $error_company_id; ?></span>
            <?php } ?>
		  </div>
		</div>
		
		<div class="form-group" id="tax-id-display">
		  <label id="tax-id-required" for="tax_id" class="col-sm-2 control-label"><?php echo $entry_tax_id; ?></label>
		  <div class="col-sm-10">
          <input placeholder="<?php echo $entry_tax_id; ?>" id="tax_id" class="form-control" type="text" name="tax_id" value="<?php echo $tax_id; ?>" />
            <?php if ($error_tax_id) { ?>
            <span class="error"><?php echo $error_tax_id; ?></span>
            <?php } ?>
		  </div>
		</div>
		
		<div class="form-group">
		  <label for="address_1" class="col-sm-2 control-label"><?php echo $entry_address_1; ?></label>
		  <div class="col-sm-10">
          <input placeholder="<?php echo $entry_address_1; ?>" class="form-control" id="address_1" type="text" name="address_1" value="<?php echo $address_1; ?>" />
            <?php if ($error_address_1) { ?>
            <span class="error"><?php echo $error_address_1; ?></span>
            <?php } ?>
		  </div>
		</div>
		
		<div class="form-group">
		  <label for="address_2" class="col-sm-2 just-label"><?php echo $entry_address_2; ?></label>
		  <div class="col-sm-10">
          <input placeholder="<?php echo $entry_address_2; ?>" class="form-control" id="address_2" type="text" name="address_2" value="<?php echo $address_2; ?>" />
		 </div>
		</div>
		
		<div class="form-group">
		  <label for="city" class="col-sm-2 control-label"><?php echo $entry_city; ?></label>
		  <div class="col-sm-10">
          <input placeholder="<?php echo $entry_city; ?>" id="city" class="form-control" type="text" name="city" value="<?php echo $city; ?>" />
            <?php if ($error_city) { ?>
            <span class="error"><?php echo $error_city; ?></span>
            <?php } ?>
		  </div>
		</div>
		
		<div class="form-group">
		<label id="postcode-required" for="postcode" class="col-sm-2 control-label"><?php echo $entry_postcode; ?></label>
		<div class="col-sm-10">
          <input placeholder="<?php echo $entry_postcode; ?>" id="postcode" class="form-control" type="text" name="postcode" value="<?php echo $postcode; ?>" />
            <?php if ($error_postcode) { ?>
            <span class="error"><?php echo $error_postcode; ?></span>
            <?php } ?>
		</div>
		</div>
		
		<div class="form-group">
		<label for="country_id" class="col-sm-2 control-label"><?php echo $entry_country; ?></label>
		<div class="col-sm-10">
          <select class="form-control" id="country_id" name="country_id">
              <option value=""><?php echo $text_select; ?></option>
              <?php foreach ($countries as $country) { ?>
              <?php if ($country['country_id'] == $country_id) { ?>
              <option value="<?php echo $country['country_id']; ?>" data-iso2="<?php echo $country['iso_code_2']; ?>" selected="selected"><?php echo $country['name']; ?></option>
              <?php } else { ?>
              <option value="<?php echo $country['country_id']; ?>" data-iso2="<?php echo $country['iso_code_2']; ?>"><?php echo $country['name']; ?></option>
              <?php } ?>
              <?php } ?>
            </select>
            <?php if ($error_country) { ?>
            <span class="error"><?php echo $error_country; ?></span>
            <?php } ?>
		</div>
		</div>
		
		<div class="form-group">
		<label for="zone_id" class="col-sm-2 control-label"><?php echo $entry_zone; ?></label>
		<div class="col-sm-10">
          <select class="form-control" id="zone_id" name="zone_id">
            </select>
            <?php if ($error_zone) { ?>
            <span class="error"><?php echo $error_zone; ?></span>
            <?php } ?>
			
		</div>
		</div>
    </div>
	
	
	
	
    <h3><?php echo $text_your_password; ?></h3>
    <div class="content">
		<div class="form-group">
		  <label for="password" class="col-sm-2 control-label"><?php echo $entry_password; ?></label>
		  <div class="col-sm-10">
          <input class="form-control" id="password" placeholder="<?php echo $entry_password; ?>" type="password" name="password" value="<?php echo $password; ?>" />
            <?php if ($error_password) { ?>
            <span class="error"><?php echo $error_password; ?></span>
            <?php } ?>
		  </div>
		</div>
		<div class="form-group">
			<label for="confirm" class="col-sm-2 control-label"><?php echo $entry_confirm; ?></label>
			<div class="col-sm-10">
            <input class="form-control" id="confirm" placeholder="<?php echo $entry_confirm; ?>" type="password" name="confirm" value="<?php echo $confirm; ?>" />
            <?php if ($error_confirm) { ?>
            <span class="error"><?php echo $error_confirm; ?></span>
            <?php } ?>
			</div>
		</div>
    </div>
	
	
    <h3><?php echo $text_newsletter; ?></h3>
    <div class="content">
		<div class="form-group">
        <label class="col-sm-2 just-label"><?php echo $entry_newsletter; ?></label>
		<div class="col-sm-10 newsletter">
          <?php if ($newsletter) { ?>
            <label><input type="radio" name="newsletter" value="1" checked="checked" /> <?php echo $text_yes; ?></label>
			&nbsp;&nbsp;
            <label><input type="radio" name="newsletter" value="0" /> <?php echo $text_no; ?></label>
            <?php } else { ?>
            <label><input type="radio" name="newsletter" value="1" /> <?php echo $text_yes; ?></label>
			&nbsp;&nbsp;
            <label><input type="radio" name="newsletter" value="0" checked="checked" /> <?php echo $text_no; ?></label>
            <?php } ?>
		</div>
		</div>
    </div>
	
	
    <?php if ($text_agree) { ?>
    <div class="buttons" style="overflow: hidden;">
      <div class="left">
        <?php if ($agree) { ?>
        <input type="checkbox" name="agree" value="1" checked="checked" />
        <?php } else { ?>
        <input type="checkbox" name="agree" value="1" />
        <?php } ?>
        &nbsp;<?php echo $text_agree; ?>
      </div>
    </div>
	<div class="buttons" style="overflow: hidden;">
		<input type="submit" value="<?php echo $button_continue; ?>" class="buy nbut" />
	</div>
    <?php } else { ?>
    <div class="buttons">
      <div class="left">
        <input type="submit" value="<?php echo $button_continue; ?>" class="buy nbut" />
      </div>
    </div>
    <?php } ?>
  </form>
  <?php echo $content_bottom; ?>
  
</div>
</div>


<script type="text/javascript"><!--
$('input[name=\'customer_group_id\']:checked').on('change', function() {
	var customer_group = [];
	
<?php foreach ($customer_groups as $customer_group) { ?>
	customer_group[<?php echo $customer_group['customer_group_id']; ?>] = [];
	customer_group[<?php echo $customer_group['customer_group_id']; ?>]['company_id_display'] = '<?php echo $customer_group['company_id_display']; ?>';
	customer_group[<?php echo $customer_group['customer_group_id']; ?>]['company_id_required'] = '<?php echo $customer_group['company_id_required']; ?>';
	customer_group[<?php echo $customer_group['customer_group_id']; ?>]['tax_id_display'] = '<?php echo $customer_group['tax_id_display']; ?>';
	customer_group[<?php echo $customer_group['customer_group_id']; ?>]['tax_id_required'] = '<?php echo $customer_group['tax_id_required']; ?>';
<?php } ?>	

	if (customer_group[this.value]) {
		if (customer_group[this.value]['company_id_display'] == '1') {
			$('#company-id-display').show();
		} else {
			$('#company-id-display').hide();
		}
		
		if (customer_group[this.value]['company_id_required'] == '1') {
			$('#company-id-required').removeClass('just-label').addClass('control-label');
		} else {
			$('#company-id-required').removeClass('control-label').addClass('just-label');
		}
		
		if (customer_group[this.value]['tax_id_display'] == '1') {
			$('#tax-id-display').show();
		} else {
			$('#tax-id-display').hide();
		}
		
		if (customer_group[this.value]['tax_id_required'] == '1') {
			$('#tax-id-required').removeClass('just-label').addClass('control-label');
		} else {
			$('#tax-id-required').removeClass('control-label').addClass('just-label');
		}	
	}
});

$('input[name=\'customer_group_id\']:checked').trigger('change');
//--></script> 
<script type="text/javascript"><!--
$('select[name=\'country_id\']').bind('change', function() {
	$.ajax({
		url: 'index.php?route=account/register/country&country_id=' + this.value,
		dataType: 'json',
		beforeSend: function() {
			$('select[name=\'country_id\']').after('<span class="wait">&nbsp;<img src="catalog/view/theme/default/image/loading.gif" alt="" /></span>');
		},
		complete: function() {
			$('.wait').remove();
		},			
		success: function(json) {
			if (json['postcode_required'] == '1') {
				$('#postcode-required').removeClass('just-label').addClass('control-label');
			} else {
				$('#postcode-required').removeClass('control-label').addClass('just-label');
			}
			
			html = '<option value=""><?php echo $text_select; ?></option>';
			
			if (json['zone'] != '') {
				for (i = 0; i < json['zone'].length; i++) {
        			html += '<option value="' + json['zone'][i]['zone_id'] + '"';
					if (json['zone'][i]['zone_id'] == '<?php echo $zone_id; ?>') {
	      				html += ' selected="selected"';
	    			}
	
	    			html += '>' + json['zone'][i]['name'] + '</option>';
				}
			} else {
				html += '<option value="0" selected="selected"><?php echo $text_none; ?></option>';
			}
			
			$('select[name=\'zone_id\']').html(html);
		},
		error: function(xhr, ajaxOptions, thrownError) {
			alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
		}
	});
});

$('select[name=\'country_id\']').trigger('change');
//--></script> 
<script type="text/javascript"><!--
$(document).ready(function() {
	$('.colorbox').colorbox({
		width: 640,
		height: 480
	});
});
//--></script> 
<?php echo $footer; ?>