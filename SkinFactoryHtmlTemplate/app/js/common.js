$(function() {

	//bootstrap select
	$('.lang').selectpicker({
		width: 85
	});

	$(".banner-wrap").owlCarousel({
		items: 1,
		loop: true,
		smartSpeed: 700,
		autoplay: true,
		loop: true
	});

	$(".testimonials-carousel").owlCarousel({
		items: 1,
		loop: true,
		smartSpeed: 700,
		// autoplay: true,
		dots: true
	});

	// main manu
	$('ul.sf-menu').superfish({
		pathLevels: 2,
		cssArrows: false,
	}).after("<div id='mobile-menu'>").clone().appendTo("#mobile-menu");
	$("#mobile-menu").find("*").attr("style", "");
	$("#mobile-menu").children("ul").removeClass("sf-menu desktop-menu")
	.parent().mmenu({
		extensions: [ "widescreen", "effect-menu-slide", "pagedim-black", "theme-dark", "border-offset", "shadow-page" ],
		navbar: {
			title: "BodyiCE"
		},
		"navbars": [
      {
         "position": "top"
      },
      {
         "position": "bottom",
         "content": [
            "<a class='fa fa-envelope' href='#/'></a>",
            "<a class='fa fa-twitter' href='#/'></a>",
            "<a class='fa fa-facebook' href='#/'></a>"
         ]
      }
    ],
    "setSelected": {
      "hover": true
    }
	});

	// mobile button menu
	$(".toggle-mnu").click(function() {
		$(this).addClass("on");
	});
	// close mobile button menu
	var api = $("#mobile-menu").data("mmenu");
	api.bind("closed", function () {
		$(".toggle-mnu").removeClass("on");
	});

	$(".sf-menu ul .sf-with-ul").parent().css("position", "static");

	// owl carousel
	$('.clients-carousel').owlCarousel({
		items: 6,
		loop: true,
		responsive : {
	    0 : {
				nav: false,
				items: 2
	    },
	    480 : {
				nav: false,
				items: 3,
	    },
	    768 : {
				nav: false,
				items: 4
	    },
	    992: {
				nav: true,
				items: 6
	    }
		},
		nav: true,
		navText: ["<i class='fa fa-angle-left'></i>","<i class='fa fa-angle-right'></i>"]
	});

	$('.acc_ctrl').on('click', function(e) {
    e.preventDefault();
    if ($(this).hasClass('active')) {
      $(this).removeClass('active');
      $(this).next().stop().slideUp(300);
    } else {
      $(this).addClass('active');
      $(this).next().stop().slideDown(300);
    }
	});

	$('.page-content .acc_ctrl').click(function(){
    $(this).find('i').toggleClass('fa fa-minus fa fa-plus');
	});


	//equal heights 
	equalheight = function(container){
		var currentTallest = 0,
		     currentRowStart = 0,
		     rowDivs = new Array(),
		     $el,
		     topPosition = 0;
		 $(container).each(function() {
		   $el = $(this);
		   $($el).height('auto')
		   topPostion = $el.position().top;

		   if (currentRowStart != topPostion) {
		     for (currentDiv = 0 ; currentDiv < rowDivs.length ; currentDiv++) {
		       rowDivs[currentDiv].height(currentTallest);
		     }
		     rowDivs.length = 0; // empty the array
		     currentRowStart = topPostion;
		     currentTallest = $el.height();
		     rowDivs.push($el);
		   } else {
		     rowDivs.push($el);
		     currentTallest = (currentTallest < $el.height()) ? ($el.height()) : (currentTallest);
		  }
		   for (currentDiv = 0 ; currentDiv < rowDivs.length ; currentDiv++) {
		     rowDivs[currentDiv].height(currentTallest);
		   }
		 });
		}

	$(window).load(function() {
		equalheight('.category-product');
		equalheight('.category-product .product-img');
	});

	$(window).resize(function(){
		equalheight('.category-product');
		equalheight('.category-product .product-img');
	});

	$('.owl-carousel').on('resized.owl.carousel', function(event) {
	    $(window).trigger("resize");
	});




});
