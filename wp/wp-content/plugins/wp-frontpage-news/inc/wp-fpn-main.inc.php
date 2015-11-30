<?php
/** WP Frontpage News main class **/
class wpcuWPFnPlugin extends YD_Plugin {
	
	//TODO: separate front-end and back-end methods, only include necessary code
	
	const	CUSTOM_POST_NEWS_WIDGET_NAME	= 'wpcuwpfp-news-widget';
	const 	CUSTOM_POST_NONCE_NAME			= 'wpcufpn_editor_tabs';
	
	const 	POSITIVE_INT_GT1				= 'positive_integer_1+';		//Those fields need to have a positive integer value greater than 1
	const	BOOL							= 'bool';				 		//Booleans
	const	FILE_UPLOAD						= 'file_upload';				//File uploads
	const	LI_TO_ARRAY						= 'li_to_array';				//Convert sortable lists to array
	
	const	DEFAULT_IMG_PREFIX				= 'wpcufpn_default_img_';		//Default uploaded image file prefix
	const	MAIN_FRONT_STYLESHEET			= 'css/wpcufpn_front.css';		//Main front-end stylesheet
	const	MAIN_FRONT_SCRIPT				= 'js/wpcufpn_front.js';		//Main front-end jQuery script
	const 	DEFAULT_IMG						= 'img/default-image-fpnp.png';	//Default thumbnail image
	
	const	USE_LOCAL_JS_LIBS				= true;
	
	/** Field default values **/
	private $_field_defaults = array(
		'default_img'			=> '',
		'source_type'			=> 'src_category',
		'cat_source_order'		=> 'date',
		'cat_source_asc'		=> 'desc',
		'pg_source_order'		=> 'order',
		'pg_source_asc'			=> 'desc',
		'show_title'			=> 1,	// Wether or not to display the block title
		'amount_pages'			=> 1,
		'amount_cols'			=> 1,
		'pagination'			=> 0,
		'max_elts'				=> 5,
		'total_width'			=> 100,
		'total_width_unit'		=> 0,	//%
		'crop_title'			=> 2,
		'crop_title_len'		=> 1,
		'crop_text'				=> 2,
		'crop_text_len'			=> 2,
		'autoanimation'			=> 0,
		'autoanimation_trans'	=> 1,
		'theme'					=> 'default',
		'box_top'				=> array(),
		'box_left'				=> array('Thumbnail'),
		'box_right'				=> array('Date','Title','Text'),
		'box_bottom'			=> array(),
		'thumb_img'				=> 0,	// 0 == use featured image
		'thumb_width'			=> 60,	// in px
		'thumb_height'			=> 60,	// in px
		'crop_img'				=> 0,	// 0 == do not crop (== resize to fit)
		'margin_left'			=> 0,
		'margin_top'			=> 0,
		'margin_right'			=> 4,
		'margin_bottom'			=> 4,
		'date_fmt'				=> '',
		'read_more'				=> '',
		'default_img_previous'	=> '',	// Overridden in constructor
		'default_img'			=> ''	// Overridden in constructor
	);
	
	/** Specific field value properties to enforce **/
	private $_enforce_fields = array(
		'amount_pages'	=> self::POSITIVE_INT_GT1,
		'amount_cols'	=> self::POSITIVE_INT_GT1,
		'amount_rows'	=> self::POSITIVE_INT_GT1,
		'max_elts'		=> self::POSITIVE_INT_GT1,
		'default_img'	=> self::FILE_UPLOAD,
		'box_top'		=> self::LI_TO_ARRAY,
		'box_left'		=> self::LI_TO_ARRAY,
		'box_right'		=> self::LI_TO_ARRAY,
		'box_bottom'	=> self::LI_TO_ARRAY,
	);
	
	/** Drop-down menu values **/
	private $_pagination_values = array(
		'None',
		'Arrows',
		'Page numbers',
		'Arrows with numbers',
		'Square bullets'
	);
	public $_width_unit_values = array(
		'%',
		'em',
		'px'
	);
	private $_thumb_img_values = array(
		'Use featured image',
		'Use first attachment',
		'Use first image'
	);
	
	/**
	 * Headers for style.css files.
	 *
	 * @static
	 * @access private
	 * @var array
	 */
	private static $file_headers = array(
			'Name'        => 'Theme Name',
			'ThemeURI'    => 'Theme URI',
			'Description' => 'Description',
			'Author'      => 'Author',
			'AuthorURI'   => 'Author URI',
			'Version'     => 'Version',
			'Template'    => 'Template',
			'Status'      => 'Status',
			'Tags'        => 'Tags',
			'TextDomain'  => 'Text Domain',
			'DomainPath'  => 'Domain Path',
	);
	
	/**
	 * Counts how many widgets are being displayed
	 * @var int
	 */
	public	$widget_count = 0;
	
	/** 
	 * Constructor
	 * 
	 */
	public function __construct( $opts ) {

		parent::YD_Plugin( $opts );
		$this->form_blocks = $opts['form_blocks']; // YD Legacy (was to avoid "backlinkware")
		
		/** Check PHP and WP versions upon install **/
		register_activation_hook( dirname( dirname( __FILE__ ) ), array( $this, 'activate' ) );
		
		/** Setup default image **/
		$this->_field_defaults['default_img_previous'] = plugins_url( self::DEFAULT_IMG, dirname( __FILE__ ) );
		$this->_field_defaults['default_img'] = plugins_url( self::DEFAULT_IMG, dirname( __FILE__ ) );
		
		/** Sets up custom post types **/
		add_action( 'init', array( $this, 'setupCustomPostTypes' ) );
		
		/** Register our widget (implemented in separate wp-fpn-widget.inc.php class file) **/
		add_action( 'widgets_init', function(){
			register_widget( 'wpcuFPN_Widget' );
		});
		
		/** Register our shortcode **/
		add_shortcode('frontpage_news', array($this, 'applyShortcode'));
		
		if( is_admin() ) {
			
			/** Load tabs ui + drag&drop ui **/
			add_action('admin_enqueue_scripts', array( $this, 'loadAdminScripts' ) );
			
			/** Load admin css for tabs **/
			add_action( 'admin_init',	array( $this, 'addAdminStylesheets' ) );
			
			/** Customize custom post editor screen **/
			//add_action( 'admin_head', array( $this, 'changeIcon' ) );	//Unused
			add_action( 'admin_menu', array( $this, 'setupCustomMetaBoxes' ) );
			add_action( 'admin_menu', array( $this, 'setupCustomMenu' ) );
			add_action( 'save_post', array( $this, 'saveCustomPostdata' ) );
			
			/** Customize Tiny MCE Editor **/
			add_action( 'admin_init', array( $this, 'setupTinyMce' ) );
			add_action( 'in_admin_footer', array( $this, 'editorFooterScript' ) );
			
			/** Tiny MCE 4.0 fix **/
			if( get_bloginfo('version') >= 3.9 ) {
				add_action( 'media_buttons', array( $this, 'editorButton' ), 1000 ); //1000 = put it at the end
			}
			
			if( !class_exists(wpcuWPFnProPlugin) )
				add_filter( 'plugin_row_meta', array( $this, 'addProLink' ), 10, 2 );
			
		} else {
			
			/** Load our theme stylesheet on the front if necessary **/
			add_action( 'wp_print_styles',	array( $this, 'addStylesheet' ) );
			
			/** Load our fonts on the front if necessary **/
			add_action( 'wp_print_styles',	array( $this, 'addFonts' ) );

			/** Load our front-end slide control script **/
			add_action( 'wp_print_scripts', array( $this, 'addFrontScript' ) );
			//add_action( 'after_setup_theme', array( $this, 'child_theme_setup' ) );
		}
	}
	
	/**
	 * Plugin Activation hook function to check for Minimum PHP and WordPress versions
	 * @see http://wordpress.stackexchange.com/questions/76007/best-way-to-abort-plugin-in-case-of-insufficient-php-version
	 * 
	 * @param string $wp Minimum version of WordPress required for this plugin
	 * @param string $php Minimum version of PHP required for this plugin
	 */
	public function activate( $wp = '3.2', $php = '5.3.1' ) {
		global $wp_version;
		if ( version_compare( PHP_VERSION, $php, '<' ) )
			$flag = 'PHP';
		elseif
		( version_compare( $wp_version, $wp, '<' ) )
		$flag = 'WordPress';
		else
			return;
		$version = 'PHP' == $flag ? $php : $wp;
		deactivate_plugins( basename( __FILE__ ) );
		wp_die('<p>The <strong>WP Frontpage News</strong> plugin requires '.$flag.'  version '.$version.' or greater.</p>','Plugin Activation Error',  array( 'response'=>200, 'back_link'=>TRUE ) );
	}
	
	/** 
	 * Sets up WP custom post types
	 * 
	 */
	public function setupCustomPostTypes() {
		$labels = array(
			'name' 					=> __( 'WP Frontpage News Blocks', 'wpcufpn' ),
			'singular_name' 		=> __( 'WPFN Block', 'wpcufpn' ),
			'add_new' 				=> __( 'Add New', 'wpcufpn' ),
			'add_new_item' 			=> __( 'Add New WPFN Block', 'wpcufpn' ),
			'edit_item' 			=> __( 'Edit WPFN Block', 'wpcufpn' ),
			'new_item' 				=> __( 'New WPFN Block', 'wpcufpn' ),
			'all_items' 			=> __( 'All News Blocks', 'wpcufpn' ),
			'view_item' 			=> __( 'View WPFN Block', 'wpcufpn' ),
			'search_items'			=> __( 'Search WPFN Blocks', 'wpcufpn' ),
			'not_found' 			=> __( 'No WPFN Block found', 'wpcufpn' ),
			'not_found_in_trash'	=> __( 'No WPFN Block found in Trash', 'wpcufpn' ),
			'parent_item_colon' 	=> '',
			'menu_name' 			=> __( 'Frontpage News', 'wpcufpn' )
		);
		register_post_type( self::CUSTOM_POST_NEWS_WIDGET_NAME, array(
			'public'		=> false,
			'show_ui'		=> true,
			'menu_position'	=> 5,
			'labels'		=> $labels,
			'supports'		=> array(
				'title', 'author'
			),
			'menu_icon'				=> plugins_url( 'img/wpfpn-menu-icon.png', dirname( __FILE__ ) )
		) );
	}
	
	/**
	 * Append our theme stylesheet if necessary
	 * 
	 */
	function addStylesheet() {
		/*
		TODO: is there a way to load our theme stylesheet only where necessary?
		global $wpcufpn_needs_stylesheet;
		if( !$wpcufpn_needs_stylesheet )
			return;
		*/
		
		$myStyleUrl 	= plugins_url( self::MAIN_FRONT_STYLESHEET, dirname( __FILE__ ) );
		$myStylePath	= plugin_dir_path( dirname( __FILE__ ) ) . self::MAIN_FRONT_STYLESHEET;
		
		if ( file_exists( $myStylePath ) ) {
			wp_register_style( 'myStyleSheets', $myStyleUrl );
			wp_enqueue_style( 'myStyleSheets' );
		}
	}
	
	/**
	 * Append our fonts if necessary
	 *
	 */
	function addFonts() {
		/*
		TODO: is there a way to load our fonts only where necessary?
		global $wpcufpn_needs_fonts;
		if( !$wpcufpn_needs_fonts )
			return;
		*/
	
		$myFontsUrl 	= 	'http://fonts.googleapis.com/css?' .
							'family=Raleway:400,500,600,700,800,900|' .
							'Alegreya:400,400italic,700,700italic,900,900italic|' .
							'Varela+Round' .
							'&subset=latin,latin-ext';
	
		wp_register_style( 'myFonts', $myFontsUrl );
		wp_enqueue_style( 'myFonts' );
	}
	
	/**
	 * Append our front-end script if necessary
	 * 
	 */
	function addFrontScript() {
		//TODO: load only if necessary (is this possible ?)
		
		wp_enqueue_script(
			'wpcufpn-front',
			plugins_url( self::MAIN_FRONT_SCRIPT, dirname( __FILE__ ) ),
			array( 'jquery' ),
			'0.1',
			true
		);
	}
	
	/**
	 * Save our custom setting fields in the WP database
	 * 
	 * @param inc $post_id
	 * @return inc $post_id (unchanged)
	 */
	public function saveCustomPostdata( $post_id ) {
		
		if ( self::CUSTOM_POST_NEWS_WIDGET_NAME != get_post_type( $post_id ) )
			return $post_id;
		
		if ( ! isset( $_POST[self::CUSTOM_POST_NONCE_NAME . '_nonce'] ) )
			return $post_id;
		
		$nonce = $_POST[self::CUSTOM_POST_NONCE_NAME . '_nonce'];
		if ( ! wp_verify_nonce( $nonce, self::CUSTOM_POST_NONCE_NAME ) )
			return $post_id;
		
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )
			return $post_id;
		
		if ( ! current_user_can( 'edit_post', $post_id ) )
			return $post_id;
		
		$my_settings = get_post_meta( $post->ID, '_wpcufpn_settings', true );
		$my_settings = wp_parse_args( $my_settings, $this->_field_defaults );
		
		/** File uploads **/
		//error_log( 'FILES: ' . serialize( $_FILES ) );	//Debug
		foreach( $_FILES as $field_name => $field_value ) {
			if( preg_match( '/^wpcufpn_/', $field_name ) ) {
				//error_log( 'matched wpcufpn_' );			//Debug
				$new_field_name = preg_replace( '/^wpcufpn_/', '', $field_name );
				if( is_uploaded_file( $_FILES[$field_name]['tmp_name'] ) ) {
					$uploads = wp_upload_dir();
					$upload_dir = ( $uploads['path'] ) . '/';
					$upload_url = ( $uploads['url'] ) . '/';
					if( preg_match( '/(\.[^\.]+)$/', $_FILES[$field_name]['name'], $matches ) )
						$ext = $matches[1];
					$upload_file = self::DEFAULT_IMG_PREFIX . date("YmdHis") . $ext;
					if ( rename( $_FILES[$field_name]['tmp_name'],
							$upload_dir . $upload_file )
					) {
						chmod( $upload_dir . $upload_file, 0664 );
						// $this->update_msg .= __( 'Temporary file ' ) . $_FILES["game_image"]["tmp_name"] .
						//	" was moved to " . $upload_dir . $upload_file;
						//var_dump( $_FILES["game_image"] );
						$my_settings[$new_field_name] = $upload_url . $upload_file;
						//error_log( 'renamed ' . $upload_url . $upload_file );	//Debug
					} else {
						$this->update_msg .= __( 'Processing of temporary uploader file has failed' .
								' please check for file directory ' ) . $upload_dir;
						//error_log( $this->update_msg );	//Debug
					}
				} else {
					//error_log( '!is_uploaded_file(' . $_FILES[$field_name]['tmp_name'] . ')' );	//Debug
					
					/** keep the previous image **/
					if( isset( $_POST[$field_name . '_previous'] ) && $_POST[$field_name . '_previous'] )
						$my_settings[$new_field_name] = $_POST[$field_name . '_previous'];
				}
			}
		}
		
		/** Normal fields **/
		foreach( $_POST as $field_name => $field_value ) {
			if( preg_match( '/^wpcufpn_/', $field_name ) ) {
				if( preg_match( '/_none$/', $field_name ) )
					continue;
				$field_name = preg_replace( '/^wpcufpn_/', '', $field_name );
				if( is_array( $field_value ) ) {
					$my_settings[$field_name] = $field_value;
				} else {
					if( preg_match( '/^box_/', $field_name ) ) {
						/** No sanitizing for those fields that are supposed to contain html **/
						$my_settings[$field_name] = $field_value;
					} else {
						$my_settings[$field_name] = sanitize_text_field( $field_value );
					}
					
					/** Enforce specific field value properties **/
					if( isset(  $this->_enforce_fields[$field_name] ) ) {
						if( self::POSITIVE_INT_GT1 == $this->_enforce_fields[$field_name] ) {
							$my_settings[$field_name] = intval($my_settings[$field_name]);
							if( $my_settings[$field_name] < 1 )
								$my_settings[$field_name] = 1;
						}
						if( self::BOOL == $this->_enforce_fields[$field_name] ) {
							$my_settings[$field_name] = intval($my_settings[$field_name]);
							if( $my_settings[$field_name] < 1 )
								$my_settings[$field_name] = 0;
							if( $my_settings[$field_name] >= 1 )
								$my_settings[$field_name] = 1;
						}
						if( self::FILE_UPLOAD == $this->_enforce_fields[$field_name] ) {
							//Do nothing I guess.
						}
						if( self::LI_TO_ARRAY == $this->_enforce_fields[$field_name] ) {
							if( $field_value ) {
								$values = preg_split( '/<\/li><li[^>]*>/i', $field_value );
							} else {
								$values = array();
							}
							if($values)
								array_walk($values, function(&$value, $key){
									$value = strip_tags($value);
								});
							$my_settings[$field_name] = $values;
						}
					}
				}
			}
		}
		
		update_post_meta( $post_id, '_wpcufpn_settings', $my_settings );
		
		return $post_id;
	}
	
	/**
	 * Loads js/ajax scripts
	 * 
	 */
	public function loadAdminScripts( $hook ) {
		
		/** Only load on post edit admin page **/
		if( 'post.php' != $hook && 'post-new.php' != $hook )
			return $hook;
		
		if( wpcuWPFnPlugin::CUSTOM_POST_NEWS_WIDGET_NAME != get_post_type() )
			return $hook;
				
		wp_enqueue_script('jquery');
		wp_enqueue_script('jquery-ui');
		wp_enqueue_script('jquery-ui-core');
		wp_enqueue_script('jquery-ui-tabs');
		wp_enqueue_script('jquery-ui-mouse');
		wp_enqueue_script('jquery-ui-sortable');
		wp_enqueue_script('jquery-ui-draggable');
		wp_enqueue_script('jquery-ui-droppable');
		wp_enqueue_script('jquery-ui-button');
		wp_enqueue_script('jquery-ui-slider');
		
		wp_enqueue_script(
			'uniform',
			plugins_url( 'js/jquery.uniform.min.js', dirname( __FILE__ ) ),
			array( 'jquery' ),
			'0.1',
			true
		);
		
		wp_enqueue_script(
			'wpcufpn-back',
			plugins_url( 'js/wpcufpn_back.js', dirname( __FILE__ ) ),
			array( 'jquery' ),
			'0.1',
			true
		);
	}
	
	/**
	 * Load additional admin stylesheets
	 * of jquery-ui
	 *
	 */
	function addAdminStylesheets() {
		
		/**/
		if( USE_LOCAL_JS_LIBS ) {
			wp_register_style( 'uiStyleSheet', plugins_url( 'css/jquery-ui-custom.css', dirname( __FILE__ ) ) );
		} else {
			wp_register_style( 'uiStyleSheet', 'http://code.jquery.com/ui/1.9.2/themes/base/jquery-ui.css' );
		}
		/**/
		wp_enqueue_style( 'uiStyleSheet' );
		
		wp_register_style( 'wpcufpnAdmin', plugins_url( 'css/wpcufpn_admin.css', dirname( __FILE__ ) ) );
		wp_enqueue_style( 'wpcufpnAdmin' );
		
		wp_register_style( 'unifStyleSheet', plugins_url( 'css/uniform/css/uniform.default.css', dirname( __FILE__ ) ) );
		wp_enqueue_style( 'unifStyleSheet' );
	}
	
	/**
	 * Change our custom post type icon
	 * 
	 */
	/* Unused <- changed in admin css stylesheet
	public function changeIcon() {
		if( wpcuWPFnPlugin::CUSTOM_POST_NEWS_WIDGET_NAME != get_post_type() )
			return;
		?>
		<style>
		#icon-album.icon32.icon32-posts-wpcuwpfp-news-widget {
			background:url('<?php plugins_url( self::EDIT_ICON, dirname( __FILE__ ) ) ?>') no-repeat;
		}
		</style>
		<?php
	}
	*/
	
	/**
	 * Customizes the default custom post type editor screen:
	 * - removes default meta-boxes
	 * - adds our own settings meta-boxes
	 * 
	 */
	public function setupCustomMetaBoxes() {
		remove_meta_box('slugdiv', self::CUSTOM_POST_NEWS_WIDGET_NAME, 'core');
		remove_meta_box('authordiv', self::CUSTOM_POST_NEWS_WIDGET_NAME, 'core');
		
		add_meta_box( 
			'wpcufpnnavtabsbox', 
			__( 'WP Frontpage News Block Settings', 'wpcufpn' ), 
			array( $this, 'editorTabs' ), 
			self::CUSTOM_POST_NEWS_WIDGET_NAME, 
			'normal', 
			'core' 
		);
	}
	
	/**
	 * Adds our admin menu item(s)
	 * 
	 */
	public function setupCustomMenu() {
		add_submenu_page(
			'edit.php?post_type=wpcuwpfp-news-widget',
			'About...',
			'About...',
			'activate_plugins',
			'about-wpfpn',
			array( $this, 'displayAboutTab' )
		);
	}
	
	/**
	 * Create navigation tabs in the main configuration screen
	 * 
	 */	
	public function editorTabs() {
		wp_nonce_field( self::CUSTOM_POST_NONCE_NAME, self::CUSTOM_POST_NONCE_NAME . '_nonce' );
				
		//TODO: externalize js, cleanup obsolete/commented code
		?>

		<div style="background:#fff" class="ui-tabs ui-widget ui-widget-content ui-corner-all">
		
			<script type="text/javascript">
				(function($) {
					$(document).ready(function() {

						$('.wpcufpntabs').tabs();

						$('#tab-1 ul.hidden').hide();
						
						$('.source_type_sel').click(function(e){
							console.log( 'clicked ' + $(this).val() );
							//$( '.wpcufpntabs' ).tabs( 'load', $(this).val() );
							$( '#tab-' + $(this).val() ).click();
						});

						$( '#tab-' + $('input[name=wpcufpn_source_type]:checked').val() ).click();

						/** You can check the all box or any other boxes, but not both **/
						$('#cat_all').click(function(e){
							if( $(this).is(':checked') ) {
								$('.cat_cb').attr('checked', false);
							}
						});
						$('.cat_cb').click(function(e){
							if( $(this).is(':checked') ) {
								$('#cat_all').attr('checked', false);
							}
						});

						/** UI switches **/
						$("select, input, button").uniform();
						//$('.radioset').buttonset();

						/** Drag & Drop widget configurator **/
						
						/*
						$('ul.arrow_col li').click(function(e) {
							console_log( 'Clicked on ' + $(this).text() );
							$('.drop_zone_col .top').append('<div class="draggable">' + $(this).text() + '</div>');
							$('.draggable').draggable();
						});
						*/
						
						/*
						$('.drop_zone_col .wpcu-inner-admin-block ul').droppable({
							drop: function( event, ui ) {
								$( this ).addClass( "ui-state-highlight" );
								$(ui.draggable).css('background','#F00');
								console_log( 'dropped! ui:' + $(ui.draggable).text() );
								$(this).append('<div class="locked">' + $(ui.draggable).text() + '&nbsp;<a href="#">x</a></div>');
								$(ui.draggable).remove();
							}
						});
						*/

						/*
						$('ul.arrow_col').droppable({
							drop: function( event, ui ) {
								console_log( 'coming back: ' + $(ui.draggable).text() + ' - ' + ui.helper );
								console_log( ui.helper );
								if($(ui.helper).hasClass('ui-sortable-helper')) {
									$(ui.helper).toggle( "pulsate" ).toggle( "pulsate" );
									$(ui.draggable).remove();
								}
							}
						});
						*/
						
						$('ul.arrow_col, .drop_zone_col .wpcu-inner-admin-block ul').sortable({
							connectWith: 'ul',
							update: function( event, ui ) {
								//console.log( ui.item );
								$(ui.item).animate({opacity: 0.5}, 90).animate({opacity: 1}, 90);
								//console_log( 'sortable was updated: ' + $(this).parent().attr('id') );
								$('#wpcufpn_' + $(this).parent().attr('id')).val( $(this).html() );
							},
							containment: '#wpcufpn_config_zone',
							over: function(event, ui) {
								$(this).parent().addClass('dragover');
							},
							out: function(event, ui) {
								$(this).parent().removeClass('dragover');
							}
						});
						$('ul.arrow_col, .drop_zone_col .wpcu-inner-admin-block ul').disableSelection();
						
						/*
						$('ul.arrow_col li').draggable({ 
							connectToSortable: '.sortable',
							helper: 'clone',
							containment: '#wpcufpn_config_zone'
						});
						*/
						
						/*
						$('#trashbin').sortable({
							update: function( event, ui ) {
								console_log( 'trash was updated: ' + $(ui.item).text() );
								$(ui.item).remove();
							},
							receive: function( event, ui ) {
								console_log( 'trash received: ' + $(ui.item).text() );
							}
						});
						$('#trashbin').disableSelection();
						*/

						$('.slider').slider({
							min: 0,
							max: 50,
							slide: function( event, ui ) {
								console.log( event );
								console.log( ui );
								field = event.target.id.substr(7);
								console.log( field );	//Debug
								$( "#" + field ).val( ui.value );
							}
						});
						$('.slider').each(function() {
							//console.log( this.id );
							var field = this.id.substr(7);
							$(this).slider({
								min: 0,
								max: 50,
								value: $( "#" + field ).val(),
								slide: function( event, ui ) {
									//console.log( event );
									//console.log( ui );
									//field = event.target.id.substr(7);
									//console.log( field );	//Debug
									$( "#" + field ).val( ui.value );
								}
							});
						});
						$('#margin_sliders input').change( function() {
							$('#slider_' + this.id).slider( 'value', $(this).val() );
						});

						$('form').attr( 'enctype', 'multipart/form-data' );
						
					});	//document.ready()
				})( jQuery );
				function console_log( msg ) {
					if(window.console) {
						window.console.log( msg );
					}
				}
			</script>
		
			<div id="wpcufpnnavtabs" class="wpcufpntabs">
				<ul>
					<li><a href="#tab-1"><?php _e( 'Content source', 'wpcufpn' ); ?></a></li>
					<li><a href="#tab-2"><?php _e( 'Display and theme', 'wpcufpn' ); ?></a></li>
					<li><a href="#tab-3"><?php _e( 'Images source', 'wpcufpn' ); ?></a></li>
					<li><a href="#tab-4"><?php _e( 'Advanced', 'wpcufpn' ); ?></a></li>
				</ul>

				<div id="tab-1" class="metabox_tabbed_content wpcufpntabs">
					<?php $this->displayContentSourceTab(); ?>
				</div>
				
				<div id="tab-2" class="metabox_tabbed_content">
					<?php $this->displayDisplayThemeTab(); ?>
				</div>
				
				<div id="tab-3" class="metabox_tabbed_content">
					<?php $this->displayImageSourceTab(); ?>
				</div>
				
				<div id="tab-4" class="metabox_tabbed_content">
					<?php $this->displayAdvancedTab(); ?>
				</div>
				
			</div>
			
		</div>
		<?php
	}
	
	/**
	 * Wp Frontpage News Widget Content source Settings tab
	 * 
	 */
	private function displayContentSourceTab() {
		global $post;
		$checked = array();
		$settings = get_post_meta( $post->ID, '_wpcufpn_settings', true );
		if( empty( $settings ) )
			$settings = $this->_field_defaults;
		
		if( !isset($settings['source_type']) || !$settings['source_type'] )
			$settings['source_type'] = 'src_category';
		
		$source_type_checked[$settings['source_type']] = ' checked="checked"';
		
		
		$tabs = array(
			'tab-1-1' => array(
				'id'		=> 'tab-src_category',
				'name'		=> __( 'Post categories', 'wpcufpn' ),
				'value'		=> 'src_category',
				'method'	=> array( $this, 'displayContentSourceCategoryTab' )
			),
			'tab-1-2' => array(
				'id'	=> 'tab-src_page',
				'name'	=> __( 'Pages', 'wpcufpn' ),
				'value'		=> 'src_page',
				'method'	=> array( $this, 'displayContentSourcePageTab' )
			)
		);
		$tabs = apply_filters( 'wpcufpn_src_type', $tabs );
		?>

		<ul class="hidden">
			<?php foreach( $tabs as $tabhref => $tab ) : ?>
			<li><a href="#<?php echo $tabhref; ?>" id="<?php echo $tab['id']; ?>"><?php echo $tab['name']; ?></a></li>
			<?php endforeach; ?>
		</ul>
		
		<ul class="horizontal">
			<?php $idx=0; ?>
			<?php foreach( $tabs as $tabhref => $tab ) : ?>
			<li><input type="radio" name="wpcufpn_source_type" id="sct<?php echo ++$idx; ?>" value="<?php echo $tab['value']; ?>" class="source_type_sel" <?php echo $source_type_checked[$tab['value']]; ?> />
				<label for="sct<?php echo ++$idx; ?>" class="post_radio"><?php echo $tab['name']; ?></label></li>
			<?php endforeach; ?>
		</ul>
		
		<?php foreach( $tabs as $tabhref => $tab ) : ?>
			<div id="<?php echo $tabhref; ?>">
				<?php call_user_func( $tab['method'] ); ?>
			</div>
		<?php endforeach; ?>
		
		<?php
	}
	
	/**
	 * Wp Frontpage News Widget Display and theme Settings tab
	 *
	 */
	private function displayDisplayThemeTab() {
		global $post;
		$settings = get_post_meta( $post->ID, '_wpcufpn_settings', true );
		if( empty( $settings ) )
			$settings = $this->_field_defaults;
		
		if( isset($settings['show_title']) )
			$show_title_checked[$settings['show_title']] = ' checked="checked"';
		if( isset($settings['pagination']) )
			$pagination_selected[$settings['pagination']] = ' selected="selected"';
		if( isset($settings['total_width_unit']) )
			$units_selected[$settings['total_width_unit']] = ' selected="selected"';
		
		echo '<div class="wpcu-inner-admin-col">';
		
		// -block---------------------------------- //
		echo '<div class="wpcu-inner-admin-block">';
		echo '<ul class="fields">';
		
		/** Show title radio button set **/
		echo '<li class="field"><label class="coltab">' . __( 'Show title', 'wpcufpn' ) . '</label>' .
				'<span class="radioset">' .
				'<input id="show_title1" type="radio" name="wpcufpn_show_title" value="0" ' . (isset($show_title_checked[0])?$show_title_checked[0]:'') . '/>' .
				'<label for="show_title1">' . __('Off', 'wpcufpn') . '</label>' .
				'<input id="show_title2" type="radio" name="wpcufpn_show_title" value="1" ' . (isset($show_title_checked[1])?$show_title_checked[1]:'') . '/>' .
				'<label for="show_title2">' . __('On', 'wpcufpn') . '</label>' .
				'</span>';
		echo '</li>';
		
		echo '<li class="field"><label for="amount_pages" class="coltab">' . __( 'Number of pages with posts', 'wpcufpn' ) . '</label>' .
			'<input id="amount_pages" type="text" name="wpcufpn_amount_pages" value="' . htmlspecialchars( isset($settings['amount_pages'])?$settings['amount_pages']:'' ) . '" class="short-text" /></li>';
		echo '<li class="field"><label for="amount_cols" class="coltab">' . __( 'Number of columns', 'wpcufpn' ) . '</label>' .
			'<input id="amount_cols" type="text" name="wpcufpn_amount_cols" value="' . htmlspecialchars( isset($settings['amount_cols'])?$settings['amount_cols']:'' ) . '" class="short-text" /></li>';
		/* Deactivated for now (vertical sliders) , TODO: reactivate
		echo '<li class="field"><label for="amount_rows" class="coltab">' . __( 'Number of rows', 'wpcufpn' ) . '</label>' .
			'<input id="amount_rows" type="text" name="wpcufpn_amount_rows" value="' . htmlspecialchars( $settings['amount_rows'] ) . '" class="short-text" /></li>';
		*/
		
		/** Pagination drop-down **/
		echo '<li class="field"><label for="pagination" class="coltab">' . __( 'Pagination', 'wpcufpn' ) . '</label>' .
				'<select id="pagination" name="wpcufpn_pagination">';
		foreach( $this->_pagination_values as $value=>$text ) {
			echo '<option value="' . $value . '" ' . (isset($pagination_selected[$value])?$pagination_selected[$value]:'') . '>';
			echo htmlspecialchars( __( $text, 'wpcufpn' ) );
			echo '</option>';
		}
		echo '</select></li>';
		
		echo '<li class="field"><label for="max_elts" class="coltab">' . __( 'Max number of elements', 'wpcufpn' ) . '</label>' .
				'<input id="max_elts" type="text" name="wpcufpn_max_elts" value="' . htmlspecialchars( isset($settings['max_elts'])?$settings['max_elts']:'' ) . '" class="short-text" /></li>';
		echo '<li class="field"><label for="total_width" class="coltab">' . __( 'Total width', 'wpcufpn' ) . '</label>' .
				'<input id="total_width" type="text" name="wpcufpn_total_width" value="' . htmlspecialchars( isset($settings['total_width'])?$settings['total_width']:'' ) . '" class="short-text" />';
		
		/** Width units drop-down **/
		echo '<select id="total_width_unit" name="wpcufpn_total_width_unit">';
		foreach( $this->_width_unit_values as $value=>$text ) {
			echo '<option value="' . $value . '" ' . $units_selected[$value] . '>' .
				$text . '</option>';
		}
		echo '</select></li>';
		
		do_action( 'wpcufpn_displayandtheme_add_fields', $settings );
		echo '</ul>';	//fields
		echo '</div>';	//wpcu-inner-admin-block
		// ---------------------------------------- //
		
		if( !class_exists(wpcuWPFnProPlugin) ) {
			echo '<div class="wpcu-inner-admin-block wpcu yellowed"><p>' .
				__(
					'Additional advanced customization features<br/> and various beautiful ' .
					'pre-configured templates and themes<br/> are available with the optional ' .
					'<a href="http://www.wpcode-united.com/wordpress-plugin/wp-frontpage-news">pro add-on</a>.'
				) .
			'</p></div>';
		} else {
			do_action( 'wpcufpn_displaytheme_col1_add_fields', $settings );
		}
		
		echo '</div>';	//wpcu-inner-admin-col
		echo '<div class="wpcu-inner-admin-col">';
		
		if( isset($settings['theme']) )
			$theme_selected[$settings['theme']] = ' selected="selected"';
		
		// -block---------------------------------- //
		echo '<div class="wpcu-inner-admin-block with-title with-border">';
		echo '<h4>Theme choice and preview</h4>';
		echo '<ul class="fields">';
		
		/** Theme drop-down **/
		echo '<li class="field"><label for="theme" class="coltab">' . __( 'Theme', 'wpcufpn' ) . '</label>' .
				'<select id="theme" name="wpcufpn_theme">';
		$all_themes = (array)$this->themeLister();
		wp_localize_script( 'wpcufpn-back', 'themes', $all_themes );
		
		//var_dump( $all_themes );	//Debug
		foreach( $all_themes as $dir=>$theme ) {
			echo '<option value="' . $dir . '" ' . $theme_selected[$dir] . '>';
			echo $theme['name'];
			echo '</option>';	
		}
		echo '</select></li>';
		
		echo '</ul>';	//fields
		echo '<div class="wpcufpn-theme-preview">';
		
		/** enforce default (first found theme) **/
		if( !isset($settings['theme']) || 'default' == $settings['theme'] ) {
			reset($all_themes);
			$settings['theme'] = key($all_themes);
		}
		
		if( isset($all_themes[$settings['theme']]['theme_url']) ) {
			$screenshot_file_url = $all_themes[$settings['theme']]['theme_url'] . '/screenshot.png';
			$screenshot_file_path = $all_themes[$settings['theme']]['theme_root'] . '/screenshot.png';
		} else {
			$screenshot_file = false;
		}
		//echo 'screenshot file: ' . $screenshot_file . '<br/>';	//Debug
		if( $screenshot_file_path && file_exists( $screenshot_file_path ) ) {
			echo '<img alt="preview" src="' . $screenshot_file_url . 
				'" style="width:100%;height:100%;" />';
		}
		echo '</div>';
		echo '</div>';	//wpcu-inner-admin-block
		// ---------------------------------------- //
		
		$box_top = $box_left = $box_right = $box_bottom = '';
		
		// -block---------------------------------- //
		echo '<div id="wpcufpn_config_zone" class="wpcu-inner-admin-block with-title with-border">';
		echo '<h4>A news item</h4>';
		echo '<div class="wpcufpn-drag-config"></div>';
		echo '<div class="arrow_col_wrapper"><ul class="arrow_col">';
		echo '<li>Title</li>';
		echo '<li>Text</li>';
		//echo '<li>First image</li>';	<- Unused
		echo '<li>Thumbnail</li>';
		echo '<li>Read more</li>';
		echo '<li>Author</li>';
		echo '<li>Date</li>';
		echo '</ul></div>';	//arrow_col
		echo '<div class="drop_zone_col">';
		echo '<div id="box_top" class="wpcu-inner-admin-block with-title with-border top">';
		echo '<h5>Top</h5><ul class="sortable">';
		if( isset($settings['box_top']) && !empty($settings['box_top']) && $settings['box_top'] )
			echo $box_top = '<li>' . join( '</li><li>', $settings['box_top'] ) . '</li>';
		echo '</ul>';
		echo '</div>';
		echo '<div id="box_left" class="wpcu-inner-admin-block with-title with-border left">';
		echo '<h5>Left</h5><ul class="sortable">';
		if( isset($settings['box_left']) && !empty($settings['box_left']) && $settings['box_left'] )
			echo $box_left = '<li>' . join( '</li><li>', $settings['box_left'] ) . '</li>';
		echo '</ul>';
		echo '</div>';
		echo '<div id="box_right" class="wpcu-inner-admin-block with-title with-border right">';
		echo '<h5>Right</h5><ul class="sortable">';
		if( isset($settings['box_right']) && !empty($settings['box_right']) && $settings['box_right'] )
			echo $box_right = '<li>' . join( '</li><li>', $settings['box_right'] ) . '</li>';
		echo '</ul>';
		echo '</div>';
		echo '<div id="box_bottom" class="wpcu-inner-admin-block with-title with-border bottom">';
		echo '<h5>Bottom</h5><ul class="sortable">';
		if( isset($settings['box_bottom']) && !empty($settings['box_bottom']) && $settings['box_bottom'] )
			echo $box_bottom = '<li>' . join( '</li><li>', $settings['box_bottom'] ) . '</li>';
		echo '</ul>';
		echo '</div>';
		
		//echo '<div id="trash_cont"><ul id="trashbin" class="sortable"></ul></div>';
		
		echo '</div>';	//drop_zone_col
		
		echo '</div>';	//wpcu-inner-admin-block #wpcufpn_config_zone
		echo '<input type="hidden" id="wpcufpn_box_top" name="wpcufpn_box_top" value="' . htmlspecialchars( $box_top ) . '"/>';
		echo '<input type="hidden" id="wpcufpn_box_left" name="wpcufpn_box_left" value="' . htmlspecialchars( $box_left ) . '"/>';
		echo '<input type="hidden" id="wpcufpn_box_right" name="wpcufpn_box_right" value="' . htmlspecialchars( $box_right ) . '"/>';
		echo '<input type="hidden" id="wpcufpn_box_bottom" name="wpcufpn_box_bottom" value="' . htmlspecialchars( $box_bottom ) . '"/>';
		// ---------------------------------------- //
		
		echo '</div>';	//wpcu-inner-admin-col
	}
	
	/**
	 * Wp Frontpage News Widget Image source Settings tab
	 *
	 */
	private function displayImageSourceTab() {
		global $post;
		$settings = get_post_meta( $post->ID, '_wpcufpn_settings', true );
		if( empty( $settings ) )
			$settings = $this->_field_defaults;
		
		if( isset($settings['thumb_img']) )
			$thumb_selected[$settings['thumb_img']] = ' selected="selected"';
		
		echo '<ul class="fields">';
		
		/** Thumbnail image src drop-down **/
		echo '<li class="field"><label for="thumb_img" class="coltab">' . __( 'Thumbnail image', 'wpcufpn' ) . '</label>' .
			'<select id="thumb_img" name="wpcufpn_thumb_img">';
		foreach( $this->_thumb_img_values as $value=>$text ) {
			echo '<option value="' . $value . '" ' . $thumb_selected[$value] . '>';
			echo htmlspecialchars( __( $text, 'wpcufpn' ) );
			echo '</option>';
		}
		echo '</select></li>';
		
		/** Thumbnail size combined width x height setting fields **/
		echo '<li class="field"><label for="thumb_width" class="coltab">' . __( 'Thumbnail size', 'wpcufpn' ) . '</label>' .
				'<span class="width_height_settings">' .
				'<input id="thumb_width" type="text" name="wpcufpn_thumb_width" value="' . htmlspecialchars( isset($settings['thumb_width'])?$settings['thumb_width']:'' ) . '" class="short-text" />' .
				'x' .
				'<input id="thumb_height" type="text" name="wpcufpn_thumb_height" value="' . htmlspecialchars( isset($settings['thumb_height'])?$settings['thumb_height']:'' ) . '" class="short-text" />' .
				'px' .
			'</span></li>';
		
		do_action( 'wpcufpn_displayimagesource_crop_add_fields', $settings );
		
		/** Sliders **/
		// -block---------------------------------- //
		echo '<div id="margin_sliders" class="wpcu-inner-admin-block with-title with-border">';
		echo '<h4>Image margin</h4>';
		echo '<ul class="fields">';
		echo '<li class="field"><label for="margin_left" class="coltab">' . __( 'Margin left', 'wpcufpn' ) . '</label>' .
				'<span id="slider_margin_left" class="slider"></span>' .
				'<input id="margin_left" type="text" name="wpcufpn_margin_left" value="' . htmlspecialchars( isset($settings['margin_left'])?$settings['margin_left']:'' ) . '" class="short-text" /></li>';
		echo '<li class="field"><label for="margin_top" class="coltab">' . __( 'Margin top', 'wpcufpn' ) . '</label>' .
				'<span id="slider_margin_top" class="slider"></span>' .
				'<input id="margin_top" type="text" name="wpcufpn_margin_top" value="' . htmlspecialchars( isset($settings['margin_top'])?$settings['margin_top']:'' ) . '" class="short-text" /></li>';
		echo '<li class="field"><label for="margin_right" class="coltab">' . __( 'Margin right', 'wpcufpn' ) . '</label>' .
				'<span id="slider_margin_right" class="slider"></span>' .
				'<input id="margin_right" type="text" name="wpcufpn_margin_right" value="' . htmlspecialchars( isset($settings['margin_right'])?$settings['margin_right']:'' ) . '" class="short-text" /></li>';
		echo '<li class="field"><label for="margin_bottom" class="coltab">' . __( 'Margin bottom', 'wpcufpn' ) . '</label>' .
				'<span id="slider_margin_bottom" class="slider"></span>' .
				'<input id="margin_bottom" type="text" name="wpcufpn_margin_bottom" value="' . htmlspecialchars( isset($settings['margin_bottom'])?$settings['margin_bottom']:'' ) . '" class="short-text" /></li>';
		echo '</ul>';	//fields
		echo '</div>';	//wpcu-inner-admin-block
		// ---------------------------------------- //
		
		if( !class_exists(wpcuWPFnProPlugin) ) {
			echo '<p class="wpcu pro_reminder">' . 
				__(
					'Additional advanced customization features are available with the optional ' .
					'<a href="http://www.wpcode-united.com/wordpress-plugin/wp-frontpage-news">pro add-on</a>.'
				) . 
			'</p>';
		} else {
			do_action( 'wpcufpn_imagesource_add_fields', $settings );
		}
	}
	
	/**
	 * Wp Frontpage News Widget Advanced Settings tab
	 *
	 */
	private function displayAdvancedTab() {
		global $post;
		$settings = get_post_meta( $post->ID, '_wpcufpn_settings', true );
		if( empty( $settings ) )
			$settings = $this->_field_defaults;
		
		echo '<ul class="fields">';
		
		echo '<li class="field"><label for="date_fmt" class="coltab">' . __( 'Date format', 'wpcufpn' ) . '</label>' .
			'<input id="date_fmt" type="text" name="wpcufpn_date_fmt" value="' . htmlspecialchars( isset($settings['date_fmt'])?$settings['date_fmt']:'' ) . '" class="short-text" /></li>';
		
		echo '</ul>';	//fields
		
		if( !class_exists(wpcuWPFnProPlugin) ) {
			echo '<div class="wpcu yellowed halfed">';
			echo '<p>' . __('Looking out for more <em>advanced</em> features?') . '</p>';
			echo '<p>' . __('&rarr;&nbsp;Check out our optional <a href="http://www.wpcode-united.com/wordpress-plugin/wp-frontpage-news">"Pro" add-on</a>.') . '</p>';
			echo '</div>';
		} else {
			do_action( 'wpcufpn_displayadvanced_add_fields', $settings );
		}
				
	}
	
	/**
	 * Wp Frontpage News Widget About tab
	 *
	 */
	public function displayAboutTab() {
		
		echo '<div class="about_content">';
		
		/** Support information **/
		echo '<p>WP Frontpage News WordPress plugin version ' . $this->version . '</p>';
		if( !class_exists(wpcuWPFnProPlugin) ) {
			echo '<p>Compatible with optional "<a href="http://www.wpcode-united.com/wordpress-plugin/wp-frontpage-news">Pro add-on</a>" version 0.2.x</p>';
			echo '<p><em class="grayed">Optional "pro" add-on is currently not installed or not enabled ' .
				'&rarr;get it <a href="http://www.wpcode-united.com/wordpress-plugin/wp-frontpage-news">here</a>!</em></p>';
		} else {
			do_action( 'wpcufpn_display_about', $this->version );
		}
		echo '<p>' . __('Initially released in october 2013 by <a href="http://www.wpcode-united.com/">WP Code United</a>') . '</p>';
		echo '<p>' . __('Author: ') . 'Yann Dubois</p>';
		echo '<p>' . __('Your current version of WordPress is: ') . get_bloginfo('version') . '</p>';
		echo '<p>' . __('Your current version of PHP is: ') . phpversion() . '</p>';
		echo '<p>' . __('Your hosting provider\'s web server currently runs: ') . $_SERVER['SERVER_SOFTWARE'] . '</p>';
		echo '<p><em>' . __('Please specify all of the above information when contacting us for support.') . '</em></p>';
		
		echo '<p><a href="http://www.wpcode-united.com/wordpress-plugin/wp-frontpage-news">WP Frontpage News official support site</a></p>';
		echo '<a href="http://www.wpcode-united.com/wordpress-plugin/wp-frontpage-news">';
		echo '<img src="' . plugins_url( 'img/wpcu-logo.png', dirname( __FILE__ ) ) . '" alt="WPCU Logo" /></a>';
		echo '</div>';
	}
	
	/**
	 * Content source tab for post categories
	 * 
	 */
	private function displayContentSourceCategoryTab() {
		
		global $post;
		$checked = array();
		$settings = get_post_meta( $post->ID, '_wpcufpn_settings', true );
		if( empty( $settings ) )
			$settings = $this->_field_defaults;
		
		if( !isset($settings['source_category']) || empty($settings['source_category']) || !$settings['source_category'] )
			$settings['source_category'] = array( '_all' );
		
		foreach( $settings['source_category'] as $cat ) {
			$source_cat_checked[$cat] = ' checked="checked"';
		};
		
		if( isset($settings['cat_source_order']) )
			$source_order_selected[$settings['cat_source_order']] = ' selected="selected"';
		if( isset($settings['cat_source_asc']) )
			$source_asc_selected[$settings['cat_source_asc']] = ' selected="selected"';
		
		echo '<ul class="fields">';
		
		echo '<li class="field">';
		echo '<ul>';
		echo '<li><input id="cat_all" type="checkbox" name="source_category[]" value="_all" ' . $source_cat_checked['_all'] . ' />' .
			'<label for="cat_all" class="post_cb">All</li>';
		$cats = get_categories();
		foreach( $cats as $cat ) {
			echo '<li><input id="ccb_' . $cat->term_id . '" type="checkbox" name="wpcufpn_source_category[]" value="' .
				$cat->term_id . '" ' . $source_cat_checked[$cat->term_id] . ' class="cat_cb" />';
			echo '<label for="ccb_' . $cat->term_id . '" class="post_cb">' . $cat->name . '</label></li>';
		}
		echo '</ul>';
		echo '</li>';	//field
		
		echo '<li class="field">';
		echo '<label for="cat_source_order" class="coltab">' . __( 'Order by', 'wpcufpn' ) . '</label>';
		echo '<select name="wpcufpn_cat_source_order" id="cat_source_order" >';
		echo '<option value="date" ' . $source_order_selected['date'] . '>' . __( 'By date', 'wpcufpn' ) . '</option>';
		echo '<option value="title" ' . $source_order_selected['title'] . '>' . __( 'By title', 'wpcufpn' ) . '</option>';
		//echo '<option value="order" ' . $source_order_selected['order'] . '>' . __( 'By order', 'wpcufpn' ) . '</option>';
		echo '</select>';
		echo '</li>';	//field
		
		echo '<li class="field">';
		echo '<label for="cat_source_asc" class="coltab">' . __( 'News sort order', 'wpcufpn' ) . '</label>';
		echo '<select name="wpcufpn_cat_source_asc" id="cat_source_asc">';
		echo '<option value="asc" ' . $source_asc_selected['asc'] . '>' . __( 'Ascending', 'wpcufpn' ) . '</option>';
		echo '<option value="desc" ' . $source_asc_selected['desc'] . '>' . __( 'Descending', 'wpcufpn' ) . '</option>';
		echo '</select>';
		echo '</li>';	//field
		
		if( !class_exists(wpcuWPFnProPlugin) ) {
			echo '</ul><p class="wpcu pro_reminder">' .
				__(
					'Additional content source options are available with the optional ' .
					'<a href="http://www.wpcode-united.com/wordpress-plugin/wp-frontpage-news">pro add-on</a>.'
				) .
			'</p><ul>';
		} else {
			do_action( 'wpcufpn_source_category_add_fields', $settings );
		}
		
		echo '</ul>';	//fields
	}
	
	/**
	 * Content source tab for pages
	 *
	 */
	private function displayContentSourcePageTab() {
		global $post;
		$checked = array();
		$settings = get_post_meta( $post->ID, '_wpcufpn_settings', true );
		if( empty( $settings ) )
			$settings = $this->_field_defaults;

		if(isset($settings['pg_source_order']))
			$source_order_selected[$settings['pg_source_order']] = ' selected="selected"';
		if(isset($settings['pg_source_asc']))
			$source_asc_selected[$settings['pg_source_asc']] = ' selected="selected"';
		
		echo '<ul class="fields">';
		
		echo '<li class="field">';
		echo '<ul>';
		echo '<li><input id="pages_all" type="checkbox" name="source_pages[]" value="_all" checked="checked"  disabled="disabled" />' .
				'<label for="pages_all" class="post_cb">All</li>';
		echo '</ul>';
		echo '</li>';	//field
		
		echo '<li class="field">';
		echo '<label for="pg_source_order" class="coltab">' . __( 'Order by', 'wpcufpn' ) . '</label>';
		echo '<select name="wpcufpn_pg_source_order" id="pg_source_order" >';
		echo '<option value="order" ' . $source_order_selected['order'] . '>' . __( 'By order', 'wpcufpn' ) . '</option>';
		echo '<option value="title" ' . $source_order_selected['title'] . '>' . __( 'By title', 'wpcufpn' ) . '</option>';
		echo '<option value="date" ' . $source_order_selected['date'] . '>' . __( 'By date', 'wpcufpn' ) . '</option>';
		echo '</select>';
		echo '</li>';	//field
		
		echo '<li class="field">';
		echo '<label for="pg_source_asc" class="coltab">' . __( 'Pages sort order', 'wpcufpn' ) . '</label>';
		echo '<select name="wpcufpn_pg_source_asc" id="pg_source_asc">';
		echo '<option value="asc" ' . $source_asc_selected['asc'] . '>' . __( 'Ascending', 'wpcufpn' ) . '</option>';
		echo '<option value="desc" ' . $source_asc_selected['desc'] . '>' . __( 'Descending', 'wpcufpn' ) . '</option>';
		echo '</select>';
		echo '</li>';	//field
		
		if( !class_exists(wpcuWPFnProPlugin) ) {
			echo '</ul><p class="wpcu pro_reminder">' .
				__(
						'Additional content source options are available with the optional ' .
						'<a href="http://www.wpcode-united.com/wordpress-plugin/wp-frontpage-news">pro add-on</a>.'
				) .
			'</p><ul>';
		} else {
			do_action( 'wpcufpn_source_page_add_fields', $settings );
		}
		
		echo '</ul>';	//fields
	}
	
	/**
	 * Builds the drop-down list of available themes
	 * for this plugin
	 * 
	 */
	function themeLister() {
		$found_themes = array();
		$theme_root = dirname( dirname( __FILE__ ) ) . '/themes';
		//echo 'theme dir: ' . $theme_root . '<br/>';	//Debug
		
		$dirs = @ scandir( $theme_root );
		foreach ( $dirs as $k=>$v ) {
			if( ! is_dir( $theme_root . '/' . $dir ) || $dir[0] == '.' || $dir == 'CVS' ) {
				unset( $dirs[$k] );
			} else {
				$dirs[$k] = array(
					'path' => $theme_root . '/' . $v,
					'url' => plugins_url( 'themes/' . $v, dirname( __FILE__ ) )
				);
			}
		}
		
		/** Load Pro add-on themes **/
		$dirs = apply_filters( 'wpcufpn_themedirs', $dirs );
		
		if ( ! $dirs )
			return false;
		//var_dump( $dirs );	//Debug
		foreach ( $dirs as $dir ) {
			//echo 'dir: ' . $dir . '<br/>';	//debug
			if ( file_exists( $dir['path'] . '/style.css' ) ) {
				$headers = get_file_data( $dir['path'] . '/style.css', self::$file_headers, 'theme' );
				//var_dump( $headers );	//Debug
				$name = $headers['Name'];
				if( 'Default theme' == $name )
					$name = ' ' . $name;	// <- this makes it sort always first
				$found_themes[ $dir['path'] ] = array(
					'name'			=> $name,
					'dir'			=> basename( $dir['path'] ),
					'theme_file'	=> $dir['path'] . '/style.css',
					'theme_root'	=> $dir['path'],
					'theme_url'		=> $dir['url']
				);
			}
		}
		asort( $found_themes );
		return $found_themes;
	}
	
	/** 
	 * Customize Tiny MCE Editor 
	 * 
	 */
	public function setupTinyMce() {
		if ( current_user_can( 'edit_posts' ) && current_user_can( 'edit_pages' ) ) {
			add_filter( 'mce_buttons', array( $this, 'filter_mce_button' ) );
			add_filter( 'mce_external_plugins', array( $this, 'filter_mce_plugin' ) );
			add_filter( 'mce_css', array( $this, 'plugin_mce_css' ) );
		}
	}
	public function filter_mce_button( $buttons ) {
		array_push( $buttons, '|', 'wpcufpn_button' );
		return $buttons;
	}
	public function filter_mce_plugin( $plugins ) {
		if( get_bloginfo('version') < 3.9 ) {
			$plugins['wpcufpn'] = plugins_url( 'js/wpcufpn_tmce_plugin.js', dirname( __FILE__ ) );
		} else {
			$plugins['wpcufpn'] = plugins_url( 'js/wpcufpn_tmce_plugin-3.9.js', dirname( __FILE__ ) );
		}
		return $plugins;
	}
	public function plugin_mce_css( $mce_css ) {
		if ( ! empty( $mce_css ) )
			$mce_css .= ',';
	
		$mce_css .= plugins_url( 'css/wpcufpn_tmce_plugin.css', dirname( __FILE__ ) );
	
		return $mce_css;
	}
	
	/**
	 * Add insert button above tinyMCE 4.0 (WP 3.9+)
	 * 
	 */
	public function editorButton() {

		$args = wp_parse_args( $args, array(
			'text'      => __( 'Add Frontpage News', 'wpcufpn' ),
			'class'     => 'button',
			'icon'      => plugins_url( 'img/wpfpn-menu-icon.png', dirname( __FILE__ ) ),
			'echo'      => true
		) );

		/** Prepare icon **/
		if ( $args['icon'] ) $args['icon'] = '<img src="' . $args['icon'] . '" /> ';
		
		/** Print button **/
		//$button = '<a href="javascript:void(0);" class="wpcufpn-button ' . $args['class'] . '" title="' . $args['text'] . '" data-target="' . $args['target'] . '" data-mfp-src="#su-generator" data-shortcode="' . (string) $args['shortcode'] . '">' . $args['icon'] . $args['text'] . '</a>';
		$button = '<a href="#TB_inline?height=150&width=150&inlineId=wpcufpn-popup-wrap&modal=true" ' .
			'class="wpcufpn-button thickbox ' . $args['class'] . '" ' .
			'title="' . $args['text'] . '">' . 
			$args['icon'] . $args['text'] . 
		'</a>';
		
		/** Prepare insertion popup **/
		add_action( 'admin_footer', array( $this, 'insertPopup' ) );
		
		if ( $args['echo'] ) echo $button;
		return $button;
	}
	
	/**
	 * Prepare block insertion popup for admin editor with tinyMCE 4.0 (WP 3.9+)
	 * 
	 */
	public function insertPopup() {
		?>
		<div id="wpcufpn-popup-wrap" class="media-modal wp-core-ui" style="display:none">
			<a class="media-modal-close" href="javascript:tb_remove();" title="Close"><span class="media-modal-icon"></span></a>
			<div id="wpcufpn-select-content" class="media-modal-content">

				<div class="wpcufpn-frame-title" style="margin-left: 30px;"><h1><?php echo __( 'WP Frontpage News', 'wpcufpn' ); ?></h1></div>
			
				<div id="wpcufpn_widgetlist" style="margin:50px auto;">
				<?php if( $widgets = get_posts( array( 'post_type'=>self::CUSTOM_POST_NEWS_WIDGET_NAME, 'posts_per_page'=>-1 ) ) ) : ?>
					<select id="wpcufpn_widget_select">
					<option><?php echo __('Select which block to insert:', 'wpcufpn' ); ?></option>
					<?php foreach( $widgets as $widget ) : ?>
						<option value="<?php echo $widget->ID; ?>"><?php echo $widget->post_title; ?></option>
					<?php endforeach; ?>
					</select>
				<?php else : ?>
					<p><?php echo __( 'No Frontpage News Widget has been created.', 'wpcufpn' ); ?></p>
					<p><?php echo __( 'Please create one to use this button.', 'wpcufpn' ); ?></p>
				<?php endif; ?>
				</div>
				
				<script>
				(function($){
		        	$('#wpcufpn_widgetlist').on( 'change', function(e){
		            	//console.log( 'selected e: ' + $('option:selected', this).val() );	//Debug
		            	//console.log( e );													//Debug
		            	insertShortcode( $('option:selected', this).val(), $('option:selected', this).text() );
		            	$('#wpcufpn_widgetlist').find('option:first').attr('selected', 'selected');
		            	tb_remove();
		            });
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
				</script>
			</div>
		</div>
		<?php
	}
	
	/**
	 * Adds a js script to the post and page editor screen footer
	 * to configure our tinyMCE extension
	 * with the list of available widgets
	 * 
	 */
	public function editorFooterScript() {
		//TODO: return false if not page/post edit screen
		
		echo '<script>';
		echo "var wpcufpn_widgets = new Array();\n";
		$widgets = get_posts( array( 'post_type'=>self::CUSTOM_POST_NEWS_WIDGET_NAME, 'posts_per_page'=>-1 ) );
		foreach( $widgets as $widget )
			echo "wpcufpn_widgets['$widget->ID']='$widget->post_title';\n";
		echo '</script>';
	}
	
	/**
	 * Returns content of our shortcode
	 * 
	 */
	public function applyShortcode( $args = array() ) {
		$html = '';
		
		if( $widget_id = $args['widget'] ) {
			$widget = get_post( $widget_id );
			$widget->settings = get_post_meta( $widget->ID, '_wpcufpn_settings', true );
			if( !empty( $widget->settings ) ) {
				$front = new wpcuFPN_Front( $widget );
				$html .= $front->display( false );
			} else {
				$html .= "\n<!-- WPFN: this News Widget is not initialized -->\n";
			}
		}
		
		return $html;
	}
	
	/**
	 * Sets up the settings page in the WP back-office
	 *
	 */
	private function display_page() {
	
		include( 'back-office-display.inc.php' );
	
	}
	
	public function addProLink( $links, $file ) {
		$base = plugin_basename( $this->plugin_file );
		if ( $file == $base ) {
			$links[] = '<a href="http://www.wpcode-united.com/wordpress-plugin/wp-frontpage-news">'
				. __('Get "pro" add-on') . '</a>';
			$links[] = '<a href="http://www.wpcode-united.com/wordpress-plugin/wp-frontpage-news">'
				. __('Support') . '</a>';
		}
		return $links;
	}
	
	// \/------------------------------------------ STANDARD ------------------------------------------\/
		
	/**
	 * overloaded
	 * Displays a standard plugin settings page in the Settings menu of the WordPress administration interface
	 *
	 * @see trunk/inc/YD_Plugin#plugin_options()
	 */
	public function plugin_options() {
		
		/** reserved to contributors **/
		if ( !current_user_can( 'edit_posts' ) )  {	
			wp_die( __('You do not have sufficient permissions to access this page.') );
		}
		if( class_exists( 'ydfgOP' ) ) {
			$op = new ydfgOP( $this );
		} else {
			$op = new YD_OptionPage( $this );
		}
		if ( $this->option_page_title ) {
			$op->title = $this->option_page_title;
		} else {
			$op->title = __( $this->plugin_name, $this->tdomain );
		}
		$op->sanitized_name = $this->sanitized_name;
		$op->yd_logo = '';
		$op->support_url = $this->support_url;
		$op->initial_funding = $this->initial_funding; 			// array( name, url )
		$op->additional_funding = $this->additional_funding;	// array of arrays
		$op->version = $this->version;
		$op->translations = $this->translations;
		$op->plugin_dir = $this->plugin_dir;
		$op->has_cache = $this->has_cache;
		$op->option_page_text = $this->option_page_text;
		$op->plg_tdomain = $this->tdomain;
		$op->donate_block = $this->op_donate_block;
		$op->credit_block = $this->op_credit_block;
		$op->support_block = $this->op_support_block;
		$this->option_field_labels['disable_backlink'] = 'Disable backlink in the blog footer:';
		$op->option_field_labels = $this->option_field_labels;
		$op->form_add_actions = $this->form_add_actions;
		$op->form_method =  $this->form_method;
		if( $_GET['do'] || $_POST['do'] ) $op->do_action( $this );
		$op->header();
		if( class_exists( 'ydfgOP' ) ) {
			$op->styles();
		}
		$op->option_values = get_option( $this->option_key );

		$this->display_page();
		
		if( $this->has_cron ) $op->cron_status( $this->crontab );
	}
}
?>