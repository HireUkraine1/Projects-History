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
<div id="content">
<div class="container">

<?php echo $content_top; ?>
    <h1><?php echo $heading_title; ?></h1>
    <?php if (isset($error_warning)) { ?> 
        <?php if ($error_warning) { ?>
            <div class="warning"><div class="container"><?php echo $error_warning; ?></div></div>
        <?php } ?>
    <?php } ?>
    
