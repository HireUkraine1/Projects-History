<!DOCTYPE html>
<html dir="<?php echo $direction; ?>" lang="<?php echo $lang; ?>">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title><?php echo $title; if (isset($_GET['page'])) { echo " - ". ((int) $_GET['page'])." ".$text_page;} ?></title>
<base href="<?php echo $base; ?>" />
<?php if ($description) { ?>
<meta name="description" content="<?php echo $description; if (isset($_GET['page'])) { echo " - ". ((int) $_GET['page'])." ".$text_page;} ?>" />
<?php } ?>
<?php if ($keywords) { ?>
<meta name="keywords" content="<?php echo $keywords; ?>" />
<?php } ?>
<meta property="og:title" content="<?php echo $title; if (isset($_GET['page'])) { echo " - ". ((int) $_GET['page'])." ".$text_page;} ?>" />
<meta property="og:type" content="website" />
<meta property="og:url" content="<?php echo $og_url; ?>" />
<?php if ($og_image) { ?>
<meta property="og:image" content="<?php echo $og_image; ?>" />
<?php } else { ?>
<meta property="og:image" content="<?php echo $logo; ?>" />
<?php } ?>
<meta property="og:site_name" content="<?php echo $name; ?>" />
<?php if ($icon) { ?>
<link href="<?php echo $icon; ?>" rel="icon" />
<?php } ?>
<?php foreach ($links as $link) { ?>
<link href="<?php echo $link['href']; ?>" rel="<?php echo $link['rel']; ?>" />
<?php } ?>
<link type="text/css" rel="stylesheet" href="http://fonts.googleapis.com/css?family=Open+Sans:400,300,300italic,400italic,600,600italic,700,700italic&amp;subset=latin,cyrillic-ext,latin-ext,cyrillic" />
<link rel="stylesheet" href="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css">
<link rel="stylesheet" type="text/css" href="catalog/view/theme/default/stylesheet/stylesheet.css?ver=5" />
<?php foreach ($styles as $style) { ?>
<link rel="<?php echo $style['rel']; ?>" type="text/css" href="<?php echo $style['href']; ?>" media="<?php echo $style['media']; ?>" />
<?php } ?>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.0/jquery.min.js"></script>
<?php /*
<script type="text/javascript" src="catalog/view/javascript/jquery/ui/jquery-ui-1.8.16.custom.min.js"></script>
<link rel="stylesheet" type="text/css" href="catalog/view/javascript/jquery/ui/themes/ui-lightness/jquery-ui-1.8.16.custom.css" />
*/ ?>
<script src="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js"></script>
<script type="text/javascript" src="catalog/view/javascript/common.js?ver=5"></script>
<?php foreach ($scripts as $script) { ?>
<script type="text/javascript" src="<?php echo $script; ?>"></script>
<?php } ?>
<!--[if IE 7]> 
<link rel="stylesheet" type="text/css" href="catalog/view/theme/default/stylesheet/ie7.css" />
<![endif]-->
<!--[if lt IE 7]>
<link rel="stylesheet" type="text/css" href="catalog/view/theme/default/stylesheet/ie6.css" />
<script type="text/javascript" src="catalog/view/javascript/DD_belatedPNG_0.0.8a-min.js"></script>
<script type="text/javascript">
DD_belatedPNG.fix('#logo img');
</script>
<![endif]-->

<?php echo $google_analytics; ?>

</head>
<body>
<header id="header">
	<div class="container">
		<?php if ($logo) { ?>
		<div id="logo">
		<?php if ($home == $og_url) { ?>
		<img src="image/data/hlogo.png" alt="<?php echo $name; ?>" title="<?php echo $name; ?>" /><span><?php echo $text_site_name; ?></span>
		<?php } else { ?>
		<a href="/"><img src="image/data/hlogo.png" alt="<?php echo $name; ?>" title="<?php echo $name; ?>" /><span><?php echo $text_site_name; ?></span></a>
		<?php } ?>
		</div>
		<?php } ?>
		<?php echo $cart; ?>

		<div id="welcome">
		<?php if (!$logged) { ?>
		<?php echo $text_welcome; ?>
		<?php } else { ?>
		<?php echo $text_logged; ?>
		<?php } ?>
		</div>
	</div>
</header>

<div id="menu">
	<div class="container">
		<nav class="navbar">
			<div class="container-fluid">
				<div class="navbar-header">
					<div class="label-active"><?php echo $text_menu; ?></div>
					<button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#navMenu">
						<span class="icon-bar"></span>
						<span class="icon-bar"></span>
						<span class="icon-bar"></span>
					</button>
				</div>
				<div class="collapse navbar-collapse" id="navMenu">
					<ul class="nav navbar-nav">
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
		</nav>
	</div>
</div>

<div id="notification"></div>
