$(function() {

	//add class active to current link
	var url = window.location.pathname, 
 			 urlRegExp = new RegExp(url.replace(/\/$/,'') + "$"); 

  $('.categry-nav a').each(function(){
    if(urlRegExp.test(this.href.replace(/\/$/,''))){
        $(this).parents().addClass('active');
    }
  });
 

	// home slider
	$('.home-slider').slick({
		arrows: false,
		// variableWidth: true,
		adaptiveHeight: true,
		fade: true,
		autoplaySpeed: 3000,
		dots: false,
		cssEase: 'linear',
		autoplay: true,
		infinite: true,
		draggable: false,
	});

	//home courses slider
	$('.courses-slider').slick({
		arrows: true,
		slidesToShow: 4,
		// adaptiveHeight: true,
		autoplaySpeed: 5000,
		dots: false,
		prevArrow:"<img class='a-left control-c prev slick-prev' src='img/arrow-left.png'>",
    nextArrow:"<img class='a-right control-c next slick-next' src='img/arrow-right.png'>",
		autoplay: true,
		infinite: true,
		// variableWidth: true,
		responsive: [
		    {
		      breakpoint: 1200,
		      settings: {
		        slidesToShow: 3,
		        slidesToScroll: 3,
		        infinite: true,
		        centerMode: true,
						arrows: false,
		        dots: true
		      }
		    },
		    {
		      breakpoint: 992,
		      settings: {
		        slidesToShow: 3,
		        slidesToScroll: 2,
		        variableWidth: true,
		        infinite: true,
						arrows: false,
		        dots: true,
		        centerMode: true,
		      }
		    },
		    {
		      breakpoint: 600,
		      settings: {
		        slidesToShow: 1,
		        slidesToScroll: 1,
		        centerMode: true,
		        variableWidth: true,
						arrows: false,
		      }
		    },
		  ]
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
	// call equalheight on load page function 
	$(window).load(function() {
		equalheight('.home .course-wrapper');
		equalheight('.home .course-wrapper .course-img');
		equalheight('.home .course-wrapper .course-description');
	});

	// call equalheight on resize page function 
	$(window).resize(function(){
		equalheight('.home .course-wrapper');
		equalheight('.home .course-wrapper .course-img');
		equalheight('.home .course-wrapper .course-description');
	});



  $(window).scroll(function(event){
  	if ($(this).scrollTop()){  
			$("header").addClass("nav-up");
		} else {
			$("header").removeClass("nav-up");
   	}
  });
	

	// clone desktop menu to mobile	
	$('.navigation > ul').clone().appendTo(".mobile-mnu");

	// action on click hamburger
	$('.hamburger-menu').on('click', function() {
		$(this).toggleClass("active");
		$('.bar').toggleClass('animate');
		$(".mobile-mnu").slideToggle("fast");
	})

	// home open category menu
	$(".main-category a").on("click", function(e) {
		var currCat = $(this) .data("id");
		if ( $(".hamburger-menu").hasClass("active") ) {
			e.preventDefault();
			$(".mobile-mnu").find(currCat).slideToggle("fast");
		}
	});
	
	//custom scroll
  $(".dropeddown").mCustomScrollbar({
  	scrollInertia:0,
  	autoDraggerLength: false,
  	setHeight: 150,
  	advanced: {
  	autoScrollOnFocus: false,
  	}
  });

	// custom select
  $('.drop-menu > .select').click(function () {
      $(this).attr('tabindex', 1).focus();
      $(this).toggleClass('active');
      $(this).parent().find('.dropeddown').slideToggle(200);
  });
  $('.drop-menu > .select').focusout(function () {
      $(this).removeClass('active');
      $(this).parent().find('.dropeddown').slideUp(200);
  });
  $('.drop-menu .dropeddown li').click(function () {
      $(this).parents('.drop-menu').find('span').text($(this).text());
      $(this).parents('.drop-menu').find('input').attr('value',$(this).attr('id'));
  });

	// clone desktop menu to mobile	
	$('.categry-nav').clone().removeClass("categry-nav").appendTo(".slide-mobile-nav").find("ul")

	// mobele menu slider on category pages
	$('.slide-mobile-nav ul').slick({
		arrows: false,
		variableWidth: true,
		autoplaySpeed: 4000,
		adaptiveHeight: true,
		dots: false,
		infinite: false,
		draggable: true,
		slidesToShow: 4,
    slidesToScroll: 3,
		responsive: [
    {
      breakpoint: 600,
      settings: {
        slidesToShow: 4,
        slidesToScroll: 3,
        variableWidth: true,
				arrows: false,
      }
    },
    {
      breakpoint: 480,
      settings: {
        slidesToShow: 2,
        slidesToScroll: 2,
        variableWidth: true,
				arrows: false,
      }
    }
		]
	});

	//cut text
	$('.course-description p').each(function() {
	    var title = $(this).text();
	    if (title.length > 60) {
	        title = title.substr(0, 60) + '...';
	    }
	    $(this).text(title);
	});

	//cut text
	$('.instructor-description p').each(function() {
	    var title = $(this).text();
	    if (title.length > 260) {
	        title = title.substr(0, 260) + '...';
	    }
	    $(this).text(title);
	});

	//cut text
	$('.course-description h5').each(function() {
	    var title = $(this).text();
	    if (title.length > 42) {
	        title = title.substr(0, 42) + '...';
	    }
	    $(this).text(title);
	});

	//equal height
	$('.masonry-items .course-wrapper .course-description').matchHeight();
	$('.masonry-items .course-wrapper ').matchHeight();
	$('.instructors-wrapper .course-item').matchHeight();
	$('.instructors-wrapper .course-img').matchHeight();

	//custom checkbox and radio button
	$('input').iCheck({
    checkboxClass: 'icheckbox_flat-blue',
    radioClass: 'iradio_flat-blue'
  });

	
	// init Isotope
	var $grid = $('.grid').isotope({
	  itemSelector: '.course-wrapper',
	  	masonry: {
       "gutter": 25
    	}
	});

	var filters = {};
	$('.filterForm').on( 'click', 'li', function() {
		var $this = $(this);
		var $buttonGroup = $this.parents('.dropeddown');
		var filterGroup = $buttonGroup.attr('data-filter-group');

		filters[ filterGroup ] = $this.attr('data-filter');
		var filterValue = concatValues( filters );

		$grid.isotope({ filter: filterValue });
	});

	function concatValues( obj ) {
	  var value = '';
	  for ( var prop in obj ) {
	    value += obj[ prop ];
	  }
	  return value;
	}



	// init Isotope on instructor training page
	var $grid = $('.instructor-training').isotope({
		itemSelector: '.instructor-course',
	});
	// bind filter on select change
	$('.filters-select > select').on( 'change', function() {
	  // get filter value from option value
	  var filterValue = this.value;
	  // use filterFn if matches value
	  $grid.isotope({ filter: filterValue });
	});


	// $(".membership-aside").stick_in_parent();




});



