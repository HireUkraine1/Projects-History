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
  <h1><?php echo $heading_title; ?></h1>
  <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" class="form-horizontal">
    <h3><?php echo $text_password; ?></h3>
    
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
	
    <div class="buttons" style="overflow: hidden;">
      <div class="left"><a href="<?php echo $back; ?>" class="buy nbut"><?php echo $button_back; ?></a></div>
      <div class="right"><input type="submit" value="<?php echo $button_continue; ?>" class="buy nbut" /></div>
    </div>
  </form>
  
  <br><br>
  
  <?php echo $content_bottom; ?>
  
</div>
<?php echo $footer; ?>