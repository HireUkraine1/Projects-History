/*!
 * Main JS script loaded on every frontend page
 *
 * @copyright 2015 MoodleFreak.com
 * @author Luuk Verhoeven
 **/
jQuery(document).ready(function ($) {
    // Helpers

    // Fix issue with padding modal
    $(window).load(function () {
        var oldSSB = $.fn.modal.Constructor.prototype.setScrollbar;
        $.fn.modal.Constructor.prototype.setScrollbar = function () {
            oldSSB.apply(this);
            if (this.bodyIsOverflowing && this.scrollbarWidth) {
                $('body').css('padding-right', '');
            }
        }

        var oldRSB = $.fn.modal.Constructor.prototype.resetScrollbar;
        $.fn.modal.Constructor.prototype.resetScrollbar = function () {
            oldRSB.apply(this);
            $('body').css('padding-right', '');
        }
    });

    // Vars
    var parallexcontainers = $('.parallax-container');
    var navigation = $("#main-navigation");
    var logo = $('#main-logo');
    var logosmall = $('#small-logo');
    var labels = $('.label-line');
    var links = $('#main-navigation li a[data-scroll]');
    var recipes = $('.recipe-item .center-block');
    var $frame = $('#player1');
    var $reciper = $('#recipe-r');
    var $recipel = $('#recipe-l');

    recipes.click(function (e) {
        log('click');
        var vimeo = $(this).data('vimeo');
        log('load:' + vimeo);
        $frame.attr('src', vimeo);
        $('#recipe_modal').modal('show');
    });

    // place taste others below
    if ($reciper) {
        $reciper.height($recipel.height());
    }

    // Dynamic way to make the way points from navigation
    var way = [];
    links.each(function () {
        way.push($(this).attr('href'));
    });
    way = $(way.join(','));

    // Start smoothscroll
    smoothScroll.init({
        speed : 300,
        easing: 'easeInOutCubic',
        offset: 100
    });

    function navbar() {
        var offset = $(this).scrollTop();
        log('offset: ' + offset);
        if (offset > 413) {
            if (!navigation.hasClass("fixed")) {
                navigation.addClass("fixed").fadeIn();
                if (logo) {
                    logo.css({
                        'margin-top': -21,
                        'background': 'url("/uploads/logo_small_1.png") no-repeat scroll 0 0'
                    });
                } else {

                }
            }
        } else {
            if (navigation.hasClass("fixed")) {
                navigation.removeClass("fixed");
                if (logo) {
                    logo.css({
                        'margin-top': -20,
                        'background': 'url("/uploads/logo_big_1.png") no-repeat scroll 0 0'
                    });
                } else {

                }
            }
        }
    }


    $('.email').each(function (e) {
        var el = $(this);
        var email = el.data('name') + '@' + el.data('domain');
        el.html('<a href="mailto:' + email + '">' + email + '</a>');
    });

    // animation labels and marking nav based on scroll position
    way.waypoint({
        handler: function (direction) {
            var id = this.element.id;
            log(id);
            // set menu active
            links.parent('li').removeClass('active');
            links.each(function () {
                var href = $(this).attr('href');
                if (href == '#' + id) {
                    $(this).parent().addClass('active');
                }
            });
        }
    }, {
        offset: '75%'
    });

    function fix_sizes() {
        parallexcontainers.each(function (e) {
            var el = $(this);
            var img = el.find('img');
            // set container height based on original
            var height = img.height();
            // el.height(height);
        })
    }

    // On resize
    $(window).resize(function () {
        log('resize');
        fix_sizes();
    });

    // On scroll
    $(window).scroll(function () {
        navbar();
    });

    $('#recipe_modal').on('hidden.bs.modal', function () {
        log('close');
        // unload video
        $frame.attr('src', '');
    });

    // Fix layout
    setTimeout(function () {
        fix_sizes();
        navbar();
    }, 300);

    $('#myCarousel').carousel({
        interval: 10000
    });
});