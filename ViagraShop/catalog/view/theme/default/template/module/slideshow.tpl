<div class="slideshow">
	<div id="slideshow<?php echo $module; ?>">
	<?php foreach ($banners as $banner) { ?>

	<div class="slide-item" style="background: #e4f7ff url(image/<?php echo $banner['image']; ?>) center 0 no-repeat;">
		<div class="container">
			<div class="info">
				<?php echo $banner['desc1']; ?>
				<?php if ($banner['link']) { ?>
				<a href="<?php echo $banner['link']; ?>">Order Now</a>
				<?php } ?>
			</div>
		</div>
		
	</div>



	<?php } ?>
	</div>
</div>
<script type="text/javascript"><!--
$(document).ready(function() {
	
	var slick_param = {
		speed: 300,
		infinite: true,
		slidesToScroll: 1,
		arrows: false,
		dots: true,
		pauseOnDotsHover: true,
		slidesToShow: 1,
		autoplaySpeed: 3000,
		//autoplay: true
	};
	
	$('#slideshow<?php echo $module; ?>').slick(slick_param);
	
});
--></script>