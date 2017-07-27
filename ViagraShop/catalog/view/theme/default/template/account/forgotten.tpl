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
  <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" class="form-horizontal">
    <p><?php echo $text_email; ?></p>
    <h3><?php echo $text_your_email; ?></h3>
	
	<div class="content" style="max-width: 600px;">

	
        <div class="form-group">
		<label for="input-email" class="col-sm-3 control-label"><?php echo $entry_email; ?></label>
		<div class="col-sm-9">
        <input placeholder="<?php echo $entry_email; ?>" class="form-control" type="text" name="email" value="" id="input-email" />
		</div>
		</div>

		<div class="form-group">
			<div class="buttons col-sm-12">
			  <!--<div class="left"><a href="<?php echo $back; ?>" class="buy"><?php echo $button_back; ?></a></div>
			  <div class="right">-->
				<input type="submit" value="<?php echo $button_continue; ?>" class="buy nbut" />
			  <!--</div>-->
			</div>
		</div>
	
		
    </div>

	
  </form>
  <?php echo $content_bottom; ?>
  </div>
  </div>
<?php echo $footer; ?>