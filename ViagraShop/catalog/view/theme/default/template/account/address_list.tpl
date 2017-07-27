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
  <h3><?php echo $text_address_book; ?></h3>
  <?php foreach ($addresses as $result) { ?>
  <div class="content">
    <table style="width: 100%;">
      <tr>
        <td><?php echo $result['address']; ?></td>
      </tr>
	  <tr>
        <td style="text-align: left; padding-top: 20px;"><a href="<?php echo $result['update']; ?>" class="buy nbut"><?php echo $button_edit; ?></a> &nbsp; <a href="<?php echo $result['delete']; ?>" class="buy nbut"><?php echo $button_delete; ?></a></td>
	  </tr>
    </table>
  </div>
  <?php } ?>
  
  <br>
  
  <div class="buttons" style="overflow: hidden;">
    <div class="left"><a href="<?php echo $back; ?>" class="buy nbut"><?php echo $button_back; ?></a></div>
    <div class="right"><a href="<?php echo $insert; ?>" class="buy nbut"><?php echo $button_new_address; ?></a></div>
  </div>
  <?php echo $content_bottom; ?>
</div>
</div>
<?php echo $footer; ?>