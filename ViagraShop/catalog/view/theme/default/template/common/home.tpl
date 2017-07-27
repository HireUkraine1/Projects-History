<?php echo $header; ?><?php echo $column_left; ?><?php echo $column_right; ?>
<?php echo $content_top; ?>

<div id="content">
<div class="container">
	<h1 class="main-h1"><?php echo $text_our_products; ?></h1>
	
  <div class="product-list">
    <?php foreach ($products as $product) { ?>
    <div>
      <?php if ($product['thumb']) { ?>
      <div class="image"><a href="<?php echo $product['href']; ?>"><img src="<?php echo $product['thumb']; ?>" title="<?php echo $product['name']; ?>" alt="<?php echo $product['name']; ?>" /></a></div>
      <?php } ?>
      <div class="name"><a href="<?php echo $product['href']; ?>"><?php echo $product['name']; ?></a></div>
      
	  <div class="row">
	  <div class="price col-sm-6">
	  <?php if ($product['opt_count'] > 1) { ?>
	  <span><?php echo $text_from; ?></span>
	  <?php } ?>
	  <?php if ($product['price']) { ?>
        <?php if (!$product['special']) { ?>
        <?php echo $product['price']; ?>
        <?php } else { ?>
        <span class="price-old"><?php echo $product['price']; ?></span> <span class="price-new"><?php echo $product['special']; ?></span>
        <?php } ?>
        <?php if ($product['tax']) { ?>
        <br />
        <span class="price-tax"><?php echo $text_tax; ?> <?php echo $product['tax']; ?></span>
        <?php } ?>
      <?php } ?>
	  </div>
      <div class="cart col-sm-6">
		<a class="buy" href="<?php echo $product['href']; ?>"><?php echo $button_cart; ?></a>
      </div>
	  
	  </div>
	  
    </div>
    <?php } ?>
  </div>
	
</div>
<?php echo $content_bottom; ?>
</div>


<div id="banner2">
	<div class="container">
		<?php echo $banner2; ?>
	</div>
</div>

<div>
	<div class="container rewiew-wrap">
		<h2>What our happy clients say</h2>
		
		<?php foreach($reviews as $review) { ?>
		<div class="rewiew-item">
			<div class="name"><?php echo $review['author']; ?></div>
			<div class="date"><?php echo $review['date_added']; ?></div>
			<div class="product"><span>About:</span> <a href="<?php echo $review['href']; ?>"><?php echo $review['name']; ?></a></div>
			<div class="rating">
			<?php for ($i = 1; $i < 6; $i++) { ?>
			<?php if ($i <= $review['rating']) { ?>
			<span class="glyphicon glyphicon glyphicon-star"></span>
			<?php } else { ?>
			<span class="glyphicon glyphicon-star-empty"></span>
			<?php } ?>
			<?php } ?>
			</div>
			<div class="desc"><?php echo $review['text']; ?></div>
		</div>
		<?php } ?>
		
	</div>
</div>

<?php echo $footer; ?>