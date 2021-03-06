(function($){
$(function(){
	//initmasonry
	$(document).ready(function(){

		var $container = $( '.masonry' );
		$container.imagesLoaded( function(){
			$container.masonry( {
				itemSelector		: '.brick',
				transitionDuration	: '0.3s'
			} );
		} );

		//menu toggle on mobile view
		$('.menu-toggle').click(function(){
			$('.content-wrapper').toggleClass('menu-open');
		});

		//menu aim
		var useJsMenu = ($('.menu-wrapper .sub-menu').css('position') === 'absolute');
		$('.main-menu').menuAim({
			activate: function(row){
				if (useJsMenu) $(row).find('> .sub-menu').show();
			},
			deactivate: function(row){
				if (useJsMenu) $(row).find('> .sub-menu').hide();
			},
			exitMenu: function(){
				if (useJsMenu) return true;
			},
			submenuDirection: 'below',
			rowSelector: '> ul >li'
		});

		//top bar search form
		$('.search-wrapper .form-toggle').click(function(){
			$(this).toggle();
			$('.search-wrapper').addClass('active');
			$('.search-wrapper .search-form').toggle();
			$('.search-wrapper .search-field').focus();
		});

		$('.search-wrapper .search-field').blur(function(event){
			$('.search-wrapper').removeClass('active');
			$('.search-wrapper .search-field').val('');
			$('.search-wrapper .search-form').toggle();
			$('.search-wrapper .form-toggle').toggle();
		});

		var $menu = $('.home .menu-wrapper');
		var winHeight = $(window).height(),
			menuHeight = $menu.height(),
			offset = $('#wpadminbar').height(),
			heroImageStop = (winHeight-menuHeight-offset);

		if ($('.content-wrapper.cover').length){
			//fit hero to the exact size
			$('.cover .hero-image').css('height', heroImageStop+'px');
			$menu.css('top', heroImageStop+'px');

			//fix the menu in hero mode
			$(document).on('scroll', function(event){
				var scroll = $(window).scrollTop();

				if (heroImageStop-scroll <= 0) $menu.addClass('stuck');
				else if ($menu.hasClass('stuck')) $menu.removeClass('stuck');
			});
		}

		$('.post-video, .widget-video, .entry-content').fitVids();


		//back to top button
		var $toTop = $('#back-to-top');
		if ($(window).scrollTop() <= $(window).height()) $toTop.hide();

		$toTop.on('click', function(){
			$('html,body').animate({
				scrollTop: 0
			}, 400);
		});

		$(document).on('scroll', function(event){
			if ($(window).scrollTop() > $(window).height()) $toTop.fadeIn();
			else $toTop.fadeOut();
		});

		//things dependant on window size
		var resizeTimeout;

		function windowSizeChanged(){
			//usingflyout menu or not?
			var previousState = useJsMenu;
			useJsMenu = ($('.menu-wrapper .sub-menu').css('position') === 'absolute');

			if (previousState != useJsMenu){
				(useJsMenu) ? $('.menu-wrapper .sub-menu').hide() : $('.menu-wrapper .sub-menu').show();
			}

			//hero image stuff
			if ($('.content-wrapper.cover').length){
				winHeight = $(window).height();
				menuHeight = $menu.height();
				offset = $('#wpadminbar').height();
				heroImageStop = (winHeight-menuHeight-offset);
				$('.cover .hero-image').css('height', heroImageStop+'px');
				$menu.css('top', heroImageStop+'px');
			}
		}

		$(window).resize(function(){
			if (resizeTimeout) clearTimeout(resizeTimeout);
			resizeTimeout = setTimeout(windowSizeChanged, 100);
		});
	});
});
})(jQuery);