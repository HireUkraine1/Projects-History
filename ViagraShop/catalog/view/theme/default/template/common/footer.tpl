<footer id="footer">
	<div class="container">
		<div class="flogo"><a href="/"><img src="image/data/flogo.png" alt="<?php echo $name; ?>" title="<?php echo $name; ?>" /><span><?php echo $text_site_name; ?></span></a></div>
		<div class="row">
			<div class="col-sm-3">
				<h4><?php echo $text_menu; ?><span class="glyphicon glyphicon-plus-sign"></span><span class="glyphicon glyphicon-minus-sign"></span></h4>
				<div class="inner">
					<ul class="fmenu">
						<li><a href="/"><?php echo $menu_item1; ?></a></li>
						<li><a href="<?php echo $menu_item2_link; ?>"><?php echo $menu_item2; ?></a></li>
						<li><a href="<?php echo $menu_item3_link; ?>"><?php echo $menu_item3; ?></a></li>
						<li><a href="<?php echo $menu_item4_link; ?>"><?php echo $menu_item4; ?></a></li>
						<li><a href="<?php echo $menu_item5_link; ?>"><?php echo $menu_item5; ?></a></li>
						<li><a href="<?php echo $menu_item6_link; ?>"><?php echo $menu_item6; ?></a></li>
						<li><a href="<?php echo $account; ?>"><?php echo $menu_item7; ?></a></li>
					</ul>
				</div>
			</div>
			<div class="col-sm-3">
				<h4><?php echo $text_working; ?><span class="glyphicon glyphicon-plus-sign"></span><span class="glyphicon glyphicon-minus-sign"></span></h4>
				<div class="inner">
					<?php echo $text_working_time; ?>
				</div>
			</div>
			<div class="col-sm-3">
				<h4><?php echo $text_adress; ?><span class="glyphicon glyphicon-plus-sign"></span><span class="glyphicon glyphicon-minus-sign"></span></h4>
				<div class="inner">
					<p><?php echo $adress; ?></p>
				</div>
			</div>
			<div class="col-sm-3 social">
				<h4><?php echo $text_social; ?></h4>
				<p>
					<a href="#" class="fb" title="<?php echo $text_fb; ?>"></a>
					<a href="#" class="tw" title="<?php echo $text_tw; ?>"></a>
					<a href="#" class="gp" title="<?php echo $text_gp; ?>"></a>					
				</p>
			</div>
		</div>
		<div class="power"><?php echo $powered; ?></div>
	</div>
</footer>
</body></html>