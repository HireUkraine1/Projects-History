<?php if ($reviews) { ?>

<div class="rewiew-wrap">
<?php foreach ($reviews as $review) { ?>
<div class="rewiew-item">
  <div class="name"><?php echo $review['author']; ?></div>
  <div class="date"><?php echo $review['date_added']; ?></div>

  <div class="rating">
  <?php /*
  <img src="catalog/view/theme/default/image/stars-<?php echo $review['rating'] . '.png'; ?>" alt="<?php echo $review['reviews']; ?>" />
  */ ?>
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


<?php /*
<div class="pagination"><?php echo $pagination; ?></div>
*/ ?>
<?php } else { ?>
<div class="content"><?php echo $text_no_reviews; ?></div>
<?php } ?>
