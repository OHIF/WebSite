/** wpcuFPN back-end jQuery script v.0.1 **/

(function($){
	
	$( document ).ready(function() {
		
		/** Theme preview drop-down **/
		$('select#theme').change( function(e){
			var theme_img = themes[$(this).val()]['theme_url']+'/screenshot.png';
			console.log( theme_img );
			$('.wpcufpn-theme-preview img').fadeOut(200, function(){
				$(this).attr('src',theme_img).bind('onreadystatechange load', function(){
					if (this.complete) $(this).fadeIn(400);
				});
			});
		});
		
		/** Automatically setup default pagination **/
		$('#amount_pages').live('focus', function(){
		      $(this).attr('oldValue',$(this).val());
		});

		$('#amount_pages').live('change', function(){
		      var oldValue = $(this).attr('oldValue');
		      var currentValue = $(this).val();
		      if( oldValue == 1 && currentValue > 1 ) {
		    	  
		    	  if( $('#pagination').val() == 0 ) {		    		  
		    		  $('#pagination').eq(0).prop('selected', false);
		    		  $('#pagination option:eq(0)').prop('selected', false);
		    		  
		    		  $('#pagination option:eq(3)').prop('selected', true);
		    		  $('#pagination').eq(3).prop('selected', true);
		    		  $('#pagination').val(3);
		    		  $('#pagination').change();
		    		  //$('#pagination')[3].selected = true;
		    	  }
		      }
		      
		      if( oldValue > 1 && currentValue == 1 ) {
		    	  
		    	  if( $('#pagination').val() > 0 ) {
		    		  $('#pagination').eq(0).prop('selected', true);
		    		  $('#pagination option:eq(0)').prop('selected', true);
		    		  $('#pagination').change();
		    	  }
		      }
		});
		
	});
	
})( jQuery );