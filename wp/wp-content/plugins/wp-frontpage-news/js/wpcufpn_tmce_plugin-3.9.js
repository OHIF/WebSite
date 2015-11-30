/** wpcuFPN Custom plugin for TinyME Editor v.0.1 **/
(function($){
	var wpcufpn_select_open = false;
	
    tinymce.create('tinymce.plugins.wpcufpn', {
    	
    	init : function(ed, url) {
    		var t = this;
    		
    		t.url = url;
    		t.editor = ed;
    		
    		ed.onBeforeSetContent.add(function(ed, o) {
    			o.content = t._do_wpcufpn(o.content);
    		});
    		
        	ed.onPostProcess.add(function(ed, o) {
        		if (o.get)
        			o.content = t._get_wpcufpn(o.content);
        	});
    	},
    	
    	_do_wpcufpn : function(co) {
    		return co.replace(/\[frontpage_news([^\]]*)\]/g, function(a,b){
    			return '<img src="'+tinymce.baseURL+'/skins/lightgray/img/trans.gif" class="wpcufpned mceItem" title="frontpage_news'+tinymce.DOM.encode(b)+'" />';
    		});
    	},
    	
        _get_wpcufpn : function(co) {
        	function getAttr(s, n) {
        		n = new RegExp(n + '=\"([^\"]+)\"', 'g').exec(s);
        		return n ? tinymce.DOM.decode(n[1]) : '';
        	};
        	return co.replace(/(?:<p[^>]*>)*(<img[^>]+>)(?:<\/p>)*/g, function(a,im) {
        		var cls = getAttr(im, 'class');
        		if ( cls.indexOf('wpcufpned') != -1 )
        			return '<p>['+tinymce.trim(getAttr(im, 'title'))+']</p>';
        		return a;
        	});
        },

        createControl : function(id, controlManager) {
            if (id == 'wpcufpn_button') {
                var button = controlManager.createButton('wpcufpn_button', {
                    title : 'Insert Front Page News shortcode', // title of the button
                    image : '../wp-content/plugins/wp-frontpage-news/img/wpcufpn_tmce_icon.png',  // path to the button's image
                    onclick : function() {
                    	
                    	if( wpcufpn_select_open ) {
                    		$('#wpcufpn_widgetlist').hide('slide', function() { $('#wpcufpn_widgetlist').remove() });
                    		//$('#wpcufpn_widgetlist').remove();
                    		wpcufpn_select_open = false;
                    		return true;
                    	}
                    	
                    	wpcufpn_select_open = true;
                    	
                    	//console.log( 'opening select' );	//Debug
                    	var html = '<div id="wpcufpn_widgetlist">' +
                    		'<select id="wpcufpn_widget_select" size="7">';
                    	$.each(wpcufpn_widgets, function( index, value ){
                    		if( 'undefined' !== typeof value )
                    			html = html + '<option value="' + index + '">' + value + '</option>';
                    	});
                    	html = html + '</select>' +
                    		'</div>';
                    	
                		var select = $( html );
                    	//select.appendTo($('#content_wpcufpn_button').parent()).hide().show( 'slide' );
                    	select.appendTo($('div#content_toolbargroup').parent()).hide().show( 'slide' );
                    	
                    	select.on( 'change', function(e){
                        	//console.log( 'selected e: ' + $('option:selected', this).val() );	//Debug
                        	//console.log( e );													//Debug
                        	insertShortcode( $('option:selected', this).val(), $('option:selected', this).text() );
                        	$(this).hide('slide', function() { $('#wpcufpn_widgetlist').remove() });
                        	wpcufpn_select_open = false;
                        });
                    	
                    	return false;
                    }
                });
                return button;
            }
            return null;
        }
    });
 
    /** Registers the plugin. **/
    tinymce.PluginManager.add('wpcufpn', tinymce.plugins.wpcufpn);
    
    function insertShortcode( widget_id, widget_title ) {
    	var shortcode = '[frontpage_news';
    	if( null != widget_id )
    		shortcode += ' widget="' + widget_id + '"';
    	if( null != widget_title )
    		shortcode += ' name="' + widget_title + '"';
    	shortcode += ']';
    	
    	/** Inserts the shortcode into the active editor and reloads display **/
    	var ed = tinyMCE.activeEditor;
		ed.execCommand('mceInsertContent', 0, shortcode);            			
		setTimeout(function() { ed.hide(); }, 1);
	    setTimeout(function() { ed.show(); }, 10);
    }
})( jQuery );
