/** wpcuFPN front-end jQuery script v.0.1 **/

var currentSlide		= new Array( 0 );
var slideLength			= 1;
var slideDirection		= 'left';
var logging 			= false;
//var autoanimate			= false;
//var	transition_type		= 'slide';

(function($){
	$( document ).ready(function() {
		//console_log( 'Doc ready.' );
		
		/** Enable slideshow navigation arrows **/
		$('.slidebtn').click(function(e){
			e.preventDefault();
			console_log( 'slidebtn clicked' );
			autoanimate = false;
			window.autoanimate = false;
			scrollSlideshow( e );
			return false;
		});
		
		/** Enable slideshow page navigation **/
		$('.pagi_p').click(function(e){
			e.preventDefault();
			console_log( 'pagi_p clicked' );
			autoanimate = false;
			window.autoanimate = false;
			scrollSlideshow( e );
			return false;
		});
		
		/** Setup slideshow containers heights **/
		$('.wpcufpn_container').each( function(i){
			var cont_id = $(this).attr('id').substr(15);
			//console.log('container id: '+cont_id);
			var nbrows = wpfpn_nbrows[cont_id];
			console_log('nb rows: '+nbrows);
			if( $(this).hasClass('horizontal') ) {
				height = 0;
				$('li',this).each( function(j){
					if( $(this).outerHeight() > height )
						height = $(this).outerHeight();
				});
				var liheight = height;
				if( nbrows > 1 ) height = height * nbrows;
				$(this).height(height);
				$('ul li',this).height(liheight);
			} else {
				$(this).height($('ul',this).height());
			}
		});
		
		/** Start autoanimate if necessary **/
		console_log( 'window.autoanimate: ' + window.autoanimate );
		if( window.autoanimate ) {
			var autoanimate = true;
			auto_animate();
		}
	});
	
	function auto_animate() {
		if( autoanimate ) {
			//TODO: use element class to animate'em all
			scrollSlideshow( $.Event( 'dblclick', { target: $('.wpcufpn_container:first') } ) );
			setTimeout(auto_animate,5000);
		}
	}
	
	/** Slideshow controls **/
	function scrollSlideshow( event ) {
		
		//TODO: get transition type from element class
		if( 'undefined' === typeof transition_type || !transition_type )
			transition_type = 'slide';
				
		if(autoanimate) {
			slideLength = Math.floor( $('.wpcufpn_listposts:first',$(event.target).parent()).width() / $('.wpcufpn_container:first',$(event.target).parent()).width() );
			slider = $((event.target).parent());
		} else {
			slideLength = Math.floor( $('.wpcufpn_listposts:first',$(event.target.parentElement).parent()).width() / $('.wpcufpn_container:first',$(event.target.parentElement).parent()).width() );
			slider = $($(event.target.parentElement).parent());
			//slider = $((event.target).parent);
		}
		
		var speed = 'fast';
		if( 'slide' == transition_type ) {
			var speed = 'fast';
		}
		if( 'fade' == transition_type ) {
			var speed = 0;
		}
		$('.wpcufpn_listposts',slider).css({
			'-moz-transition':'none',
	    	'-webkit-transition':'none',
	    	'-o-transition':'color 0 ease-in',
	    	'transition':'none'
		});
		
		console_log('slider:');
		console_log(slider);
		console_log( 'slideLength: ' + slideLength );
		var slideShowClass = $(event.target).parent().parent().attr('class');
		var slideShowId = slideShowClass.substr(slideShowClass.indexOf('wpcufpn_widget_')+15,slideShowClass.length);
		if( !slideShowId )
			slideShowId = 0;
		
		if( !slideShowId in currentSlide || !currentSlide[slideShowId] )
			currentSlide[slideShowId] = 0;
		
		console_log( 'slideShow ID: ' + slideShowId );
		console_log( 'currentSlide: ' + currentSlide[slideShowId] );
		
		if ( 
			event.type == "swipeleft" || 
			( event.type == "dblclick" && slideDirection == 'left' ) ||
			$(event.target).hasClass('slide_right')
		) {
			console_log('sliding left ' + transition_type);
			if( currentSlide[slideShowId] <= -(slideLength-1) ) {
				if( 'slide' == transition_type )
					bounceSlide( 'left', slider, event );
				slideDirection = 'right';
				return;
			} else {
				currentSlide[slideShowId] --;
			}
		}
		if (
			event.type == "swiperight" || 
			( event.type == "dblclick" && slideDirection == 'right' ) ||
			$(event.target).hasClass('slide_left')
		) {
			console_log('sliding right ' + transition_type);
			if( currentSlide[slideShowId] >= 0 ) {
				if( 'slide' == transition_type )
					bounceSlide( 'right', slider, event );
				slideDirection = 'left';
				return;
			} else {
				currentSlide[slideShowId] ++;
			}
		}
		if( $(event.target).hasClass('pagi_p') ) {
			var page_to = $(event.target).text();
			page_to --;
			page_to = - page_to;
			console_log( 'page_to: ' + page_to );
			if( page_to == currentSlide[slideShowId] )
				return;
			if( page_to > currentSlide[slideShowId] ) {
				step = 1
			} else {
				step = -1
			}
			while( currentSlide[slideShowId] != page_to ) {
				currentSlide[slideShowId] = currentSlide[slideShowId] + step;
				console_log( 'sliding to: ' + currentSlide[slideShowId] );
				if( 'fade' == transition_type ) {
					$('.wpcufpn_listposts',slider).fadeOut('slow',function(){$('.wpcufpn_listposts',slider).css({
						'marginLeft' : $('.wpcufpn_container:first',slider).width() * currentSlide[slideShowId]
					})}).fadeIn('slow');
				} else {
					$('.wpcufpn_listposts',slider).animate({
						'marginLeft' : $('.wpcufpn_container:first',slider).width() * currentSlide[slideShowId]
					}, speed);
				}
				//if( 'fade' == transition_type )
				//	$('.wpcufpn_listposts:first',slider).fadeIn(600);
			}
			console_log('parent:');
			console_log($(event.target).parent().parent());
			console_log('ppn_'+(1-currentSlide[slideShowId]));
			$('div.wpcufpn_nav .pagi_p', $(event.target).parent().parent()).removeClass('active');
			$('div.wpcufpn_nav .pagi_p.ppn_'+(1-currentSlide[slideShowId]), $(event.target).parent().parent()).addClass('active');
			return;
		}
		console_log('currentSlide: ' + currentSlide[slideShowId]);
		
		if( 'fade' == transition_type ) {
			$('.wpcufpn_listposts',slider).fadeOut('slow',function(){$('.wpcufpn_listposts',slider).css({
				'marginLeft' : $('.wpcufpn_container:first',slider).width() * currentSlide[slideShowId]
			})}).fadeIn('slow');
		} else {
			$('.wpcufpn_listposts',slider).animate({
				'marginLeft' : $('.wpcufpn_container:first',slider).width() * currentSlide[slideShowId]
			}, speed);
		}
		//if( 'fade' == transition_type )
		//	$('.wpcufpn_listposts:first',slider).fadeIn(600);
		//$('#image_ph ul#pagination li.on').removeClass('on');
		//$('#image_ph ul#pagination li.sp_' + (-currentSlide[slideShowId]) ).addClass('on');
	}
	
	/** Makes slideshow bounce a bit when reaching end of slides **/
	function bounceSlide( direction, slider, event ) {
		
		console_log('bouncing ' + direction);
		
		var slideShowClass = $(event.target).parent().parent().attr('class');
		var slideShowId = slideShowClass.substr(slideShowClass.indexOf('wpcufpn_widget_')+15,slideShowClass.length);
		if( !slideShowId )
			slideShowId = 0;
		
		if( !slideShowId in currentSlide || !currentSlide[slideShowId] )
			currentSlide[slideShowId] = 0;
		
		if( direction == 'left' ) {
			amp = -15;
		} else {
			amp = 15;
		}
		$('.wpcufpn_listposts:first',slider).animate({
			'marginLeft' : ( $('.wpcufpn_container:first',slider).width() * currentSlide[slideShowId] ) + amp
		}, 'fast', 'swing', function() {
			$('.wpcufpn_listposts:first',slider).animate({
				'marginLeft' : ( $('.wpcufpn_container:first',slider).width() * currentSlide[slideShowId] )
			}, 'fast');
		});
	}
	
	function console_log( msg ) {
		if(logging && window.console) {
			window.console.log( msg );
		}
	}
	
})( jQuery );
