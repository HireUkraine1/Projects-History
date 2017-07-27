<?php echo $header; ?>
<?php if ($success) { ?>
<div class="success"><div class="container"><?php echo $success; ?></div></div>
<?php } ?>
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
<div id="content"><?php echo $content_top; ?>
<div class="container">

  <h1><?php echo $heading_title; ?></h1>
  <div class="login-content">
    
    <div class="row">
	
    <div class="col-sm-6">
      <h2><?php echo $text_returning_customer; ?></h2>
      <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" class="form-horizontal">
        <div class="content">
			<div class="form-group">
				<label for="input-email" class="control-label col-sm-3"><?php echo $entry_email; ?></label>
				<div class="col-sm-9">
				<input id="input-email" type="text" placeholder="<?php echo $entry_email; ?>" name="email" value="<?php echo $email; ?>" class="form-control" />
				</div>
			</div>
			<div class="form-group">
				<label for="input-pass" class="control-label col-sm-3"><?php echo $entry_password; ?></label>
				<div class="col-sm-9">
				<input id="input-pass" type="password" placeholder="<?php echo $entry_password; ?>" name="password" value="<?php echo $password; ?>" class="form-control" />
				</div>
			</div>
			<div class="form-group">
				<div class="col-sm-12">
					<a class="forgotten-link" href="<?php echo $forgotten; ?>"><?php echo $text_forgotten; ?></a>
				</div>
			</div>
			<div class="form-group">
				<div class="col-sm-8">
				  <input type="submit" value="<?php echo $button_login; ?>" class="buy nbut" />
				  <?php if ($redirect) { ?>
				  <input type="hidden" name="redirect" value="<?php echo $redirect; ?>" />
				  <?php } ?>
			  </div>
		  </div>
        </div>
      </form>
    </div>
	
	<div class="col-sm-6">
      <h2><?php echo $text_new_customer; ?></h2>
      <div class="content">
		<div class="form-group">
        <p><?php echo $text_register_account; ?></p>
		</div>
		<div class="form-group">
        <a href="<?php echo $register; ?>" class="buy nbut"><?php echo $button_continue; ?></a>
		</div>
		</div>
    </div>
	
    </div>
	
  </div>
  <?php echo $content_bottom; ?>
  
</div>
</div>
<script type="text/javascript"><!--
$('#login input').keydown(function(e) {
	if (e.keyCode == 13) {
		$('#login').submit();
	}
});
//--></script> 
<?php echo $footer; ?>