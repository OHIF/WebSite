<?php
/**
 * Adds WP Front Page News Widget widget.
 * 
 * this class is instantiated by registering the widget 
 * with WP in wp-fpn-main.inc.php's constructor
 * 
 */
class wpcuFPN_Widget extends WP_Widget {
	
	const PRO_VERSION_URL = 'http://www.wpcode-united.com/wordpress-plugin/wp-frontpage-news';
	
	/**
	 * Register widget with WordPress.
	 * 
	 */
	public function __construct() {
		parent::__construct(
			'wpcufpn_widget', // Base ID
			'WP Front Page News Widget', // Name
			array( 'description' => __( 'WP Front Page News Widget instance', 'wpcufpn' ), ) // Args
		);
	}
	
	/**
	 * Front-end display of widget.
	 *
	 * @see WP_Widget::widget()
	 *
	 * @param array $args     Widget arguments.
	 * @param array $instance Saved values from database.
	 */
	public function widget( $args, $instance ) {
		global $wpcu_wpfn;
		if( !class_exists(wpcuWPFnProPlugin) && ( $wpcu_wpfn->widget_count ++ > 0 ) ) {
			return false;
		}
		
		echo $args['before_widget'];
				
		$widget = get_post( $instance['news_widget_id'] );
		$widget->settings = get_post_meta( $widget->ID, '_wpcufpn_settings', true );
		$front = new wpcuFPN_Front( $widget );
		
		if(
			isset( $front->widget->settings['show_title'] ) &&
			$front->widget->settings['show_title'] == 1
		)
			echo $args['before_title'] . $front->widget->post_title . $args['after_title'];
		
		$front->display( true, true );
		
		echo $args['after_widget'];
	}
	
	/**
	 * Back-end widget form.
	 *
	 * @see WP_Widget::form()
	 *
	 * @param array $instance Previously saved values from database.
	 */
	public function form( $instance ) {
		global $wpcu_wpfn;
		if( !class_exists(wpcuWPFnProPlugin) && ( $wpcu_wpfn->widget_count ++ > 1 ) ) {
			echo __( 'This widget can be used only once with the free version of the WP Frontpage News plugin.' );
			echo ' ';
			echo __( 'If you need to configure multiple instances of the widget, please check-out' );
			echo ' <a href="' . self::PRO_VERSION_URL . '">';
			echo __( 'the pro version.' );
			echo '</a>';
			return false;
		}
		
		if ( isset( $instance[ 'news_widget_id' ] ) ) {
			$news_widget_id = $instance[ 'news_widget_id' ];
		} else {
			if( $widget = $this->findFirstWidget() ) {
				$news_widget_id = $widget->ID;
			} else {
				echo '<p>' . __( 'No Frontpage News Widget has been created.', 'wpcufpn' ) . '</p>';
				//TODO: add link to widget creation edit page
				echo '<p>' . __( 'Please create one to use this widget.', 'wpcufpn' ) . '</p>';
				$news_widget_id = 0;
				return false;
			}
		}
		$selected[$news_widget_id] = ' selected="selected"';
		$widgets = get_posts( array( 'post_type'=>wpcuWPFnPlugin::CUSTOM_POST_NEWS_WIDGET_NAME, 'posts_per_page'=>-1 ) );
		?>
		<p>
		<label for="<?php echo $this->get_field_id( 'news_widget_id' ); ?>"><?php _e( 'News Widget:', 'wpcufpn' ); ?></label> 
		<select id="<?php echo $this->get_field_id( 'news_widget_id' ); ?>" name="<?php echo $this->get_field_name( 'news_widget_id' ); ?>">
		<?php foreach( $widgets as $widget ) : ?>		
			<option value="<?php echo $widget->ID; ?>" <?php echo $selected[$widget->ID]; ?>><?php echo $widget->post_title; ?></option>
		<?php endforeach; ?>
		</select>
		</p>
		<?php
		//echo 'number: ' . $this->number . '<br/>';									//Debug
		
		//echo 'single widget done: ' . $wpcu_wpfn->single_widget_done . '<br/>';		//Debug
		
		wp_reset_postdata();
	}
	
	/**
	 * Sanitize widget form values as they are saved.
	 *
	 * @see WP_Widget::update()
	 *
	 * @param array $new_instance Values just sent to be saved.
	 * @param array $old_instance Previously saved values from database.
	 *
	 * @return array Updated safe values to be saved.
	 */
	public function update( $new_instance, $old_instance ) {
		$instance = array();
		$instance['news_widget_id'] = ( ! empty( $new_instance['news_widget_id'] ) ) ? strip_tags( $new_instance['news_widget_id'] ) : '';
		
		return $instance;
	}

	/**
	 * TODO: CHECK: Is this really useful?
	 * 
	 * @return mixed|boolean
	 */
	private function findFirstWidget() {
		$widgets = get_posts( array( 'post_type'=>wpcuWPFnPlugin::CUSTOM_POST_NEWS_WIDGET_NAME ) );
		if( is_array( $widgets ) && !empty( $widgets ) ) {
			return array_shift( $widgets );
		} else {
			return false;
		}
		wp_reset_postdata();
	}
}
?>