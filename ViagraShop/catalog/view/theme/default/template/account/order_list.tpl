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
  <?php if ($orders) { ?>
  <?php foreach ($orders as $order) { ?>
  <div class="order-list">
    <div class="order-id"><b><?php echo $text_order_id; ?></b> #<?php echo $order['order_id']; ?></div>
    <div class="order-status"><b><?php echo $text_status; ?></b> <?php echo $order['status']; ?></div>
    <div class="order-content row">
      
	  <div class="col-sm-4"><b><?php echo $text_date_added; ?></b> <?php echo $order['date_added']; ?><br />
        <b><?php echo $text_products; ?></b> <?php echo $order['products']; ?><br />
        <b><?php echo $text_products_count; ?></b> <?php echo $order['products_count']; ?></div>
		
      <div class="col-sm-4"><b><?php echo $text_customer; ?></b> <?php echo $order['name']; ?><br />
        <b><?php echo $text_total; ?></b> <?php echo $order['total']; ?></div>
		
      <div class="col-sm-4 order-info">
		<?php if ($order['order_id'] == 2) { ?>
		<a class="buy nbut track" data-id="<?php echo $order['order_id']; ?>" href="javascript:">Track <span class="glyphicon glyphicon-play"></span></a>
		<?php } ?>
		<a class="buy nbut" href="<?php echo $order['href']; ?>"><?php echo $button_view; ?></a>
	  <?php /*
	  &nbsp;&nbsp;<a href="<?php echo $order['reorder']; ?>"><img src="catalog/view/theme/default/image/reorder.png" alt="<?php echo $button_reorder; ?>" title="<?php echo $button_reorder; ?>" /></a>
	  */ ?>
	  </div>
    </div>
  </div>
  <?php } ?>
  <div class="pagination"><?php echo $pagination; ?></div>
  <?php } else { ?>
  <div class="content"><?php echo $text_empty; ?></div>
  <?php } ?>
  <div class="buttons" style="overflow: hidden;">
    <div class="right"><a href="<?php echo $continue; ?>" class="buy nbut"><?php echo $button_continue; ?></a></div>
  </div>
  
  <br>
  
  <?php echo $content_bottom; ?>
  
</div>
</div>

  <!-- Modal -->
  <div class="modal fade" id="myModal" role="dialog">
    <div class="modal-dialog">
    
      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
        </div>
        <div class="modal-body">
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        </div>
      </div>
      
    </div>
  </div>

<?php echo $footer; ?>