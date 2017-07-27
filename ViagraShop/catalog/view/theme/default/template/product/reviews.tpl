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
		<?php echo $description; ?>
		<div class="rewiew-wrap">
			
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
		<?php echo $content_bottom; ?>
	</div>
</div>
<?php echo $footer; ?>