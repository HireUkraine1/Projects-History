<?php echo $header; ?>
<?php if ($success) { ?>
<div class="success"><div class="container"><?php echo $success; ?></div></div>
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
  <h3><?php echo $text_my_account; ?></h3>
  <div class="content">
    <ul>
      <li><a href="<?php echo $edit; ?>"><?php echo $text_edit; ?></a></li>
      <li><a href="<?php echo $password; ?>"><?php echo $text_password; ?></a></li>
      <li><a href="<?php echo $address; ?>"><?php echo $text_address; ?></a></li>
      <?php /*<li><a href="<?php echo $wishlist; ?>"><?php echo $text_wishlist; ?></a></li>*/ ?>
    </ul>
  </div>
  <h3><?php echo $text_my_orders; ?></h3>
  <div class="content">
    <ul>
      <li><a href="<?php echo $order; ?>"><?php echo $text_order; ?></a></li>
      <?php /*<li><a href="<?php echo $download; ?>"><?php echo $text_download; ?></a></li>*/ ?>
      <?php if (false) { ?>
      <li><a href="<?php echo $reward; ?>"><?php echo $text_reward; ?></a></li>
      <?php } ?>
      <?php /*<li><a href="<?php echo $return; ?>"><?php echo $text_return; ?></a></li>*/ ?>
      <?php /*<li><a href="<?php echo $transaction; ?>"><?php echo $text_transaction; ?></a></li>*/ ?>
    </ul>
  </div>
  <h3><?php echo $text_my_newsletter; ?></h3>
  <div class="content">
    <ul>
      <li><a href="<?php echo $newsletter; ?>"><?php echo $text_newsletter; ?></a></li>
    </ul>
  </div>
  <?php echo $content_bottom; ?>
  
</div>
</div>
<?php echo $footer; ?> 