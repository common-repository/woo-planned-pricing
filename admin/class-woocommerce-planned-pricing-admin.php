<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       http://catsplugins.com
 * @since      1.0.0
 *
 * @package    Woocommerce_Planned_Pricing
 * @subpackage Woocommerce_Planned_Pricing/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Woocommerce_Planned_Pricing
 * @subpackage Woocommerce_Planned_Pricing/admin
 * @author     Nicholas To <togiang88@gmail.com>
 */
class Woocommerce_Planned_Pricing_Admin {

	public static $limit = 0;
	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Woocommerce_Planned_Pricing_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Woocommerce_Planned_Pricing_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/woocommerce-planned-pricing-admin.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Woocommerce_Planned_Pricing_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Woocommerce_Planned_Pricing_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/woocommerce-planned-pricing-admin.js', array( 'jquery' ), $this->version, false );

	}

	/**
	 * define ajax - wpp price.
	 *
	 * @since    1.0.0
	 */
	public function wpp_price_ajax() {
		
		// get current admin screen, or null
	    $screen = get_current_screen();

	    // verify admin screen object
	    if (is_object($screen)) {

	    	if ('product' == $screen->post_type) {

				wp_enqueue_script( 'wpp_price_ajax', plugin_dir_url( __FILE__ ) . 'js/woocommerce-planned-pricing-price-ajax.js', array( 'jquery' ), $this->version, false );

	            wp_localize_script(
	                'wpp_price_ajax',
	                'wpp_meta_box_obj',
	                [
	                    'url' => admin_url('admin-ajax.php'),
	                ]
	            );
	        }
	    }
	}

	/**
	 * handle ajax return
	 *
	 * @since    1.0.0
	 */
	public function wpp_meta_box_ajax_handler() {

	    if (isset($_POST['_add_price'])) {
	    	$this->add_price($_POST['_row_max']);
	    }

	    // ajax handlers must die
	    die;
	}

	/**
	 * Register the metabox for woocommerce.
	 *
	 * @since    1.0.0
	 */	
	public static function wpp_add_meta_boxes() {

		global $post;

		$ids_allowed = array();
		$cur_id = $post->ID;

		$args = array(
		  'numberposts' => 2,
		  'post_type' => 'product'
		);
		 
		$posts = get_posts( $args );

		if ( !empty($posts) )
			$ids_allowed = wp_list_pluck($posts, 'ID');

		if ( sizeof($ids_allowed) < 2 || in_array($cur_id, $ids_allowed) ) {
			add_meta_box(
				'wpp_pricing_table', // meta box id
				'Planned Pricing', // title
				array($this, 'wpp_custom_box_html'), // callback functuon
				'product', // post type
				'normal'
			);			
		}
	}

	/**
	 * Generate the metabox html.
	 *
	 * @since    1.0.0
	 */	
	public function wpp_custom_box_html($post) {

		global $post;
		
		// get wpp_meta data stored before
		$wpp_meta = get_post_meta($post->ID, 'wpp_meta', true);

		// set the first range number
		$row_max = 1;

		?>
		
		<!-- Main metabox html -->
		<div class="panel-wrap wpp-wrap">
			<table class="wpp-prices">
				<thead>
					<tr>
						<th>No.</th>
						<th>From</th>
						<th>To</th>
						<th>Price</th>
						<th>Notes</th>
						<th></th>
						<th></th>
					</tr>
				</thead>
				<tbody>
					<?php 
												
						if ( empty($wpp_meta) ) { // new product
							$this->add_price();
						} else {
							foreach ($wpp_meta as $row_no => $values) {
								
								// get and display row price values
								$this->add_price($row_no, $values); 

								// get current row max stored
								$row_max = $row_no;
							}
						}

					?>
				</tbody>
				<input type="hidden" id="row_max" value="<?php echo $row_max; ?>">
			</table>
		</div>
		<!-- Main metabox html -->
		
		<?php
	}

	public function add_price($row_no=null, $values=null) {

		// define row no and values
		$row_no = isset($row_no) ? $row_no : '1';
		$values = isset($values) ? $values : array();
		$tr_cls = "_no_{$row_no}";

		// define fields value
		$wpp_product_from 	= isset($values['wpp_product_from']) ? $values['wpp_product_from'] : '';
		$wpp_product_to 	= isset($values['wpp_product_to']) ? $values['wpp_product_to'] : '';
		$wpp_product_price 	= isset($values['wpp_product_price']) ? $values['wpp_product_price'] : '';
		$wpp_product_note 	= isset($values['wpp_product_note']) ? $values['wpp_product_note'] : '';

		?>
		<tr id="<?php echo $tr_cls; ?>">
			<td>#<?php echo $row_no; ?></td>
			<td class="wpp_product_number">
				<?php				
				// product_from
				woocommerce_wp_text_input( array( 'id' => 'wpp_product_from', 'type' => 'number', 'name' => 'wpp_meta['.$row_no.'][wpp_product_from]', 'desc_tip' => true, 'value' => $wpp_product_from ) );
				?>
			</td>
			<td class="wpp_product_number">
				<?php				
				// product_to
				woocommerce_wp_text_input( array( 'id' => 'wpp_product_to', 'type' => 'number', 'name' => 'wpp_meta['.$row_no.'][wpp_product_to]', 'desc_tip' => true, 'value' => $wpp_product_to ) );
				?>
			</td>
			<td class="wpp_product_number">
				<?php				
				// product_price
				woocommerce_wp_text_input( array( 'id' => 'wpp_product_price', 'type' => 'number', 'name' => 'wpp_meta['.$row_no.'][wpp_product_price]', 'desc_tip' => true, 'value' => $wpp_product_price ) );
				?>
			</td>
			<td class="wpp_product_area">
				<p class="form-field wpp_product_note_field ">
					<label for="wpp_product_note"></label>
					<textarea class="short" style="" name="wpp_meta[<?php echo $row_no; ?>][wpp_product_note]" id="wpp_product_note" placeholder="" rows="2" cols="20" value=""><?php echo esc_textarea($wpp_product_note); ?></textarea> 
				</p>
			</td>
			<td class="wpp_product_add">
				<?php	
				// add_price
				woocommerce_wp_text_input( array( 'id' => 'wpp_add_price', 'type' => 'button', 'value' => 'Add Price', 'class' => 'button' ) );
				?>
			</td>
			<td class="wpp_product_remove">
				<?php	
				if ($row_no > 1) // remove_price
					woocommerce_wp_text_input( array( 'id' => 'wpp_remove_price', 'type' => 'button', 'value' => 'Remove', 'class' => 'button' ) );
				?>
			</td>
		</tr>
		<?php

	}

	/**
	 * save prices meta.
	 *
	 * @since    1.0.0
	 */	
	public function wpp_save_post_meta($data) {

		if ( isset($_POST['wpp_meta'])) {
			update_post_meta( $_POST['post_ID'], 'wpp_meta', $_POST['wpp_meta'] );
		}

	}

}
