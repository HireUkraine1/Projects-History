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
<div id="content"><?php echo $content_top; ?>
<div class="container">


  <h1><?php echo $heading_title; ?></h1>
  <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" class="form-horizontal">
    <h2><?php echo $text_your_details; ?></h2>
    <div class="content">

		<div class="form-group">
		  <label for="firstname" class="col-sm-2 control-label"><?php echo $entry_firstname; ?></label>
		  <div class="col-sm-10">
          <input class="form-control" id="firstname" placeholder="<?php echo $entry_firstname; ?>" type="text" name="firstname" value="<?php echo $firstname; ?>" />
            <?php if ($error_firstname) { ?>
            <span class="error"><?php echo $error_firstname; ?></span>
            <?php } ?>
		  </div>
		</div>
		<div class="form-group">
		  <label for="lastname" class="col-sm-2 control-label"><?php echo $entry_lastname; ?></label>
		  <div class="col-sm-10">
          <input class="form-control" id="lastname" placeholder="<?php echo $entry_lastname; ?>" type="text" name="lastname" value="<?php echo $lastname; ?>" />
            <?php if ($error_lastname) { ?>
            <span class="error"><?php echo $error_lastname; ?></span>
            <?php } ?>
		  </div>
		</div>
		<div class="form-group">	
		  <label for="email" class="col-sm-2 control-label"><?php echo $entry_email; ?></label>
		  <div class="col-sm-10">
          <input class="form-control" id="email" placeholder="<?php echo $entry_email; ?>" type="text" name="email" value="<?php echo $email; ?>" />
            <?php if ($error_email) { ?>
            <span class="error"><?php echo $error_email; ?></span>
            <?php } ?>
		  </div>
		</div>
		<div class="form-group">	
		  <label for="telephone" class="col-sm-2 control-label"><?php echo $entry_telephone; ?></label>
		  <div class="col-sm-10">
          <input class="form-control" id="telephone" placeholder="<?php echo $entry_telephone; ?>" type="text" name="telephone" value="<?php echo $telephone; ?>" />
            <?php if ($error_telephone) { ?>
            <span class="error"><?php echo $error_telephone; ?></span>
            <?php } ?>
		  </div>
		</div>
		<div class="form-group">	
		  <label for="fax" class="col-sm-2 just-label"><?php echo $entry_fax; ?></label>
		  <div class="col-sm-10">
          <input class="form-control" id="fax" placeholder="<?php echo $entry_fax; ?>" type="text" name="fax" value="<?php echo $fax; ?>" />
		  </div>
		</div>

    </div>
    <div class="buttons" style="overflow: hidden;">
      <div class="left"><a href="<?php echo $back; ?>" class="buy nbut"><?php echo $button_back; ?></a></div>
      <div class="right">
        <input type="submit" value="<?php echo $button_continue; ?>" class="buy nbut" />
      </div>
    </div>
  </form>
  
  
  <?php echo $content_bottom; ?>
  
  
</div>
</div>
<?php echo $footer; ?>