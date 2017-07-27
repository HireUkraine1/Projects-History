<?php echo $header; ?><?php echo $column_left; ?><?php echo $column_right; ?>

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

<div id="content"><?php echo $content_top; ?>
<div class="container">

  <h1><?php echo $heading_title; ?></h1>
  <?php echo $text_message; ?>
  <br>
  <div class="buttons">
    <div class="left"><a href="<?php echo $continue; ?>" class="buy nbut"><?php echo $button_continue; ?></a></div>
  </div>
  <?php echo $content_bottom; ?>
  
  
  </div>
  </div>
<?php echo $footer; ?>