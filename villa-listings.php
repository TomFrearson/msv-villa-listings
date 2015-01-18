<?php
/*
Plugin Name: Villa Listings
Plugin URI: http://wptechcentre.com/
Description: Declares a plugin that will create a custom post type displaying villa listings.
Version: 2.0
Author: Tom Frearson
Author URI: http://wptechcentre.com/
License: GPLv2
*/

/*-----------------------------------------------------------------------------------*/
/* 1. Flush rewrite rules on activation/deactivation */
/*-----------------------------------------------------------------------------------*/

if ( ! function_exists( 'villa_listings_activate' ) ) {
	function villa_listings_activate() {
		create_villa_listings();
		flush_rewrite_rules();
	}
}
register_activation_hook( __FILE__, 'villa_listings_activate' );

if ( ! function_exists( 'villa_listings_deactivate' ) ) {
	function villa_listings_deactivate() {
		create_villa_listings();
		flush_rewrite_rules();
	}
}
register_deactivation_hook( __FILE__, 'villa_listings_deactivate' );


/*-----------------------------------------------------------------------------------*/
/* 2. Register custom post type */
/*-----------------------------------------------------------------------------------*/

if ( ! function_exists( 'create_villa_listings' ) ) {
	function create_villa_listings() {
		$labels = array(
				'name' => _x( 'Villas', 'post type general name' ),
				'singular_name' => _x( 'Villa', 'post type singular name' ),
				'add_new' => _x( 'Add New', 'villa_listing' ),
				'add_new_item' => __( 'Add new Villa' ),
				'edit_item' => __( 'Edit Villa' ),
				'new_item' => __( 'New Villa' ),
				'view_item' => __( 'View Villa' ),
				'search_items' => __( 'Search Villas' ),
				'not_found' =>  __( 'No Villas found' ),
				'not_found_in_trash' => __( 'No Villas found in Trash' ),
				'parent_item_colon' => ''
			);
	 
		$args = array(
				'labels' => $labels,
				'public' => true,
				'publicly_queryable' => true,
				'show_ui' => true,
				'query_var' => true,
				'rewrite' => array( 'slug' => 'villas', 'with_front' => false, 'feeds' => true ),
				'capability_type' => 'post',
				'hierarchical' => false,
				'menu_icon' => plugins_url( 'images/listings-icon.ico', __FILE__ ),
				'menu_position' => 5,
				'has_archive' => true,
				'taxonomies' => array( 'villa_category', 'post_tag' ),
				'supports' => array( 'title', 'editor', 'author', 'thumbnail', 'excerpt', 'comments' ),
				'yarpp_support' => true
			);
		
		register_post_type( 'villa_listings', $args );
		
	} // End create_villa_listings()
}
add_action( 'init', 'create_villa_listings' );


/*-----------------------------------------------------------------------------------*/
/* 3. Register custom taxonomies */
/*-----------------------------------------------------------------------------------*/

if ( ! function_exists( 'create_villa_taxonomies' ) ) {
	function create_villa_taxonomies() {
		$labels = array(
				'name'              => _x( 'Villa Categories', 'taxonomy general name' ),
				'singular_name'     => _x( 'Villa Category', 'taxonomy singular name' ),
				'search_items'      => __( 'Search Villa Categories' ),
				'all_items'         => __( 'All Villa Categories' ),
				'parent_item'       => __( 'Parent Villa Category' ),
				'parent_item_colon' => __( 'Parent Villa Category:' ),
				'edit_item'         => __( 'Edit Villa Category' ),
				'update_item'       => __( 'Update Villa Category' ),
				'add_new_item'      => __( 'Add New Category' ),
				'new_item_name'     => __( 'New Villa Category' ),
				'menu_name'         => __( 'Categories' ),
		);
				
		$args = array(
				'hierarchical'      => true,
				'labels'            => $labels,
				'show_ui'           => true,
				'show_admin_column' => true,
				'query_var'         => true,
				'rewrite'           => array( 'slug' => 'villa-category' ),
		);
		register_taxonomy( 'villa_category', array( 'villa_listings' ), $args );
	}
	register_taxonomy_for_object_type( 'villa_category', 'villa_listings' );
}
add_action( 'init', 'create_villa_taxonomies' );


/*-----------------------------------------------------------------------------------*/
/* 4. Add settings page */
/*-----------------------------------------------------------------------------------*/

function villa_listings_menu() {
	add_options_page( 'Villa Listings Options', 'Villa Listings', 'manage_options', 'villa-listings', 'villa_listings_options' );
}
add_action( 'admin_menu', 'villa_listings_menu' );

function villa_listings_options() {
	if ( !current_user_can( 'manage_options' ) )  {
		wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
	}
	echo '<div class="wrap">';
	echo '<p>Here is where the form would go if I actually had options.</p>';
	echo '</div>';
}

/* Register settings */
if ( ! function_exists( 'register_villa_listings_settings' ) ) {
	function register_villa_listings_settings() {
		register_setting( 'villa_listings', 'villa_listings' );
	}
}
add_action( 'admin_init', 'register_villa_listings_settings' );

/* Set defaults */
if ( ! function_exists( 'set_villa_listings_defaults' ) ) {
	function set_villa_listings_defaults() {
		global $villa_features;
		add_option( 'villa_listings', $villa_features, '', 'no' );
	}
}
register_activation_hook(__FILE__, 'set_villa_listings_defaults');

/* Remove settings */
if ( ! function_exists( 'remove_villa_listings_options' ) ) {
	function remove_villa_listings_options() {
		delete_option( 'villa_listings' );
	}
}
register_uninstall_hook( __FILE__, 'remove_villa_listings_options' );
//register_deactivation_hook( __FILE__, 'remove_villa_listings_options' ); //testing only


/*-----------------------------------------------------------------------------------*/
/* 4. Add Dashboard widget */
/*-----------------------------------------------------------------------------------*/

if ( ! function_exists( 'villa_listings_dashboard_widgets' ) ) {
	function villa_listings_dashboard_widgets() {
		if( current_user_can( 'import' ) ) {
			wp_add_dashboard_widget(
				'villa_listings_dashboard_widget',
				'Villa Listings',
				'villa_listings_dashboard_widget'
			);
		}
	}
}
add_action( 'wp_dashboard_setup', 'villa_listings_dashboard_widgets' );

if ( ! function_exists( 'villa_listings_dashboard_widget' ) ) {
	function villa_listings_dashboard_widget() {
		?>
		<a href="post-new.php?post_type=villa_listings" class="button" style="margin-top: 5px;">Add new villa</a>
		<div style="margin-top: 15px; border-top: solid 1px #ececec;">
			<ul>
				<li><h4>Help and Support</h4></li>
				<li><a href="http://wptechcentre.com/">User Guide</a></li>
				<li><a href="http://wptechcentre.com/">Plugin Home Page</a></li>
			</ul>
		</div>
		<?php
	}
}


/*-----------------------------------------------------------------------------------*/
/* 4. Define villa features global array */
/*-----------------------------------------------------------------------------------*/

$GLOBALS['villa_features'] = array(
		'24_room_service' => '24hr Room Service',
		'bar_pub' => 'Bar/Pub',
		'casino' => 'Casino',
		'disabled_facilities' => 'Disabled Facilities',
		'family_room' => 'Family Room',
		'nightclub' => 'Nightclub',
		'restaurant' => 'Restaurant',
		'salon' => 'Salon',
		'smoking_area' => 'Smoking Area',
		'airport_transfer' => 'Airport Transfer',
		'bicycle_rental' => 'Bicycle Rental',
		'coffee_shop' => 'Coffee Shop',
		'elevator' => 'Elevator',
		'laundry_service' => 'Laundry Service',
		'pets_allowed' => 'Pets Allowed',
		'room_service' => 'Room Service',
		'shops' => 'Shops',
		'tours' => 'Tours',
		'babysitting' => 'Babysitting',
		'business_center' => 'Business Center',
		'concierge' => 'Concierge',
		'executive_floor' => 'Executive Floor',
		'meeting_facilities' => 'Meeting Facilities',
		'poolside_bar' => 'Poolside Bar',
		'safety_deposit_boxes' => 'Safety Deposit Boxes',
		'shuttle_service' => 'Shuttle Service',
		'wifi_public_areas' => 'Wi-Fi in Public Areas',
		'fitness_center' => 'Fitness Center',
		'golf_course' => 'Golf Course (on site)',
		'indoor_pool' => 'Indoor Pool',
		'massage' => 'Massage',
		'private_beach' => 'Private Beach',
		'squash_courts' => 'Squash Courts',
		'water_sports' => 'Water Sports',
		'games_room' => 'Games Room',
		'golf_course' => 'Golf Course (within 3km)',
		'jacuzzi' => 'Jacuzzi',
		'outdoor_pool' => 'Outdoor Pool',
		'sauna' => 'Sauna',
		'steamroom' => 'Steamroom',
		'garden' => 'Garden',
		'hot_spring_bath' => 'Hot Spring Bath',
		'kids_club' => 'Kids Club',
		'pool_kids' => 'Pool (kids)',
		'spa' => 'Spa',
		'tennis_courts' => 'Tennis courts',
		'free_wifi_access' => 'Free Wi-Fi Access',
		'car_park' => 'Car Park',
		'valet_parking' => 'Valet Parking'
	);


/*-----------------------------------------------------------------------------------*/
/* 5. Display villa categories - shortcode */
/*-----------------------------------------------------------------------------------*/

if ( ! function_exists( 'shortcode_all_post_categories' ) ) {
	function shortcode_all_post_categories ( $atts ) {
		$defaults = array(
			'sep' => ', ',
			'before' => '',
			'after' => '',
			'taxonomy' => array( //use an array to include all post types
				'post' => 'category',
				'villa_listing' => 'villa_category'
			)
		);
		$atts = shortcode_atts( $defaults, $atts );
		
		$atts = array_map( 'wp_kses_post', $atts );
		
		foreach( $atts['taxonomy'] as $post_type => $taxonomy ) { //use foreach to loop through array
			$terms = get_the_terms( get_the_ID(), esc_html( $taxonomy ) );
			$cats = '';
			
			if ( is_array( $terms ) && 0 < count( $terms ) ) {
				$links_array = array();
				foreach ( $terms as $k => $v ) {
					$term_name = get_term_field( 'name', $v->term_id, $taxonomy );
					$links_array[] = '<a href="' . esc_url( get_term_link( $v, $taxonomy ) ) . '" title="' . esc_attr( sprintf( __( 'View all items in %s', '' ), $term_name ) ) . '">' . esc_html( $term_name ) . '</a>';
				}
			}
		}
		$cats = join( $atts['sep'], $links_array ); //convert array into string outside loop
		
		$output = sprintf('<span class="categories">%2$s%1$s%3$s</span> ', $cats, $atts['before'], $atts['after']);
		return apply_filters( 'shortcode_all_post_categories', $output, $atts );
	} // End shortcode_all_post_categories()
}

if ( ! function_exists( 'modify_post_categories_shortcode' ) ) {
	function modify_post_categories_shortcode() {
		remove_shortcode( 'post_categories' );
		add_shortcode( 'post_categories', 'shortcode_all_post_categories' );
	}
}
add_action( 'init', 'modify_post_categories_shortcode' ); //needed to remove old shortcode


/*-----------------------------------------------------------------------------------*/
/* 6. Enable meta boxes */
/*-----------------------------------------------------------------------------------*/

if ( ! function_exists( 'villa_listings_meta' ) ) {
	function villa_listings_meta( $post_type, $post ) {
		add_meta_box(
			'villa_listings_features',
			__( 'Villa Features' ),
			'display_villa_listings_features',
			'villa_listings',
			'normal',
			'default'
		);
		add_meta_box(
			'agoda_id',
			__( 'Agoda ID' ),
			'get_agoda_id',
			'villa_listings',
			'side',
			'default'
		);
	}
}
add_action( 'add_meta_boxes', 'villa_listings_meta', 10, 2 );


/*-----------------------------------------------------------------------------------*/
/* 7. Meta box content for features */
/*-----------------------------------------------------------------------------------*/

if ( ! function_exists( 'display_villa_listings_features' ) ) {
	function display_villa_listings_features( $villa_listing ) {
		global $villa_features;
		?>
		<table width="100%">
			<tbody>
				<?php
					$i = 0;
					foreach( $villa_features as $key => $value ) {
						$villa_feature = intval( get_post_meta( $villa_listing->ID, $key, true ) );
				?>
					<?php
						if( $i == 0 ) {
							echo '<tr height="30">';
						} ?>
							<td width="70"><?php echo $value . ' '; ?></td>
							<td width="50"><input type="checkbox" name="<?php echo $key; ?>" value="1" <?php if( $villa_feature == 1 ) echo 'checked="checked"'; ?> /></td>
					<?php
						if( $i == 6 ) {
							echo '</tr>';
						} 
					?>
					
					<?php
						$i++;
						if ( $i == 6 ) {
							$i = 0;
						}
					} ?>
			</tbody>
		</table>
		<?php
	}
}


/*-----------------------------------------------------------------------------------*/
/* 8. Meta box content for Agoda ID */
/*-----------------------------------------------------------------------------------*/

if ( ! function_exists( 'get_agoda_id' ) ) {
	function get_agoda_id( $villa_listing ) {
		?>
		<table>
			<?php
				$agoda_id = esc_html( get_post_meta( $villa_listing->ID, 'agoda_id', true ) );
			?>
				<tr>
					<td style="width: auto;">Agoda ID: </td>
					<td><input type="text" size="18" name="agoda_id" value="<?php echo $agoda_id; ?>" /></td>
				</tr>
		</table>
		<?php
	}
}


/*-----------------------------------------------------------------------------------*/
/* 9. Save listings data */
/*-----------------------------------------------------------------------------------*/

if ( ! function_exists( 'add_villa_listing_fields' ) ) {
	function add_villa_listing_fields( $villa_listing_id, $villa_listing ) {
		global $villa_features;
			if ( $villa_listing->post_type == 'villa_listings' ) {
				foreach( $villa_features as $key => $value ) { //error here when updating villa listings (data loss? output buffering?)
					if ( isset( $_POST[$key] ) ) {
						update_post_meta( $villa_listing_id, $key, 1 );
					} else {
						update_post_meta( $villa_listing_id, $key, 0 );
					}
				}
			
			if ( isset( $_POST['agoda_id'] ) && $_POST['agoda_id'] != '' ) {
					update_post_meta( $villa_listing_id, 'agoda_id', $_POST['agoda_id'] );
			}
		}
	}
}
add_action( 'save_post', 'add_villa_listing_fields', 10, 2 );


/*-----------------------------------------------------------------------------------*/
/* 10. Display featured image inside the content */
/*-----------------------------------------------------------------------------------*/

if ( ! function_exists( 'display_featured_villa_image' ) ) {
	function display_featured_villa_image( $content ) {
		if( get_post_type() == 'villa_listings' && is_single() && has_post_thumbnail() ) {
			$content .= the_post_thumbnail( 'medium', array( 'class' => 'thumbnail alignright' ) );
			return $content;
		}
		else {
			return $content;
		}
	}
}
add_filter( 'the_content', 'display_featured_villa_image', 10, 1 );


/*-----------------------------------------------------------------------------------*/
/* 11. Villa listing images */
/*-----------------------------------------------------------------------------------*/

if ( ! function_exists( 'show_listing_images' ) ) {
	function show_listing_images( $content ) {
		if( get_post_type() == 'villa_listings' && is_single() ) {
			$attachments = get_children( array(
							'post_parent' => get_the_id(),
							'post_status' => 'inherit',
							'post_type' => 'attachment',
							'post_mime_type' => 'image',
							'order' => 'ASC',
							'orderby' => 'menu_order ID'));
			
			foreach( $attachments as $att_id => $attachment ) {
				$img = wp_get_attachment_url( $attachment->ID );
				$img_atts = wp_get_attachment_image_src( $attachment->ID );
				$height = $img_atts[2];
				
			$images_array[] = '<img src="' . esc_url( $img ) . '" class="previews thumbnail" alt="' . esc_attr( $title ) . '" title="' . esc_attr( $title ) . '" />';
			}
			$images = implode( "", $images_array ); //output string of images
			
			$content .= '<section class="entry photos"><div class="show_photo">' . $images . '</div></section><div style="clear: both;"></div>';
			return $content;
		}
		else {
			return $content;
		}
	}
}
add_filter( 'the_content', 'show_listing_images', 10, 1 ); //set priority to 10 (first)


/*-----------------------------------------------------------------------------------*/
/* 12. Villa listing features */
/*-----------------------------------------------------------------------------------*/

if ( ! function_exists( 'show_listing_features' ) ) {
	function show_listing_features( $content ) {
		if( get_post_type() == 'villa_listings' && is_single() ) {
			global $villa_features;
			$id = get_the_id();
			$disc = ' <img src="' . plugins_url( '/images/disc.png', __FILE__ ) . '" width="25" height="25" />';
			$tick = ' <img src="' . plugins_url( '/images/tick.png', __FILE__ ) . '" width="25" height="25" />';
			$i = 0;
			
			foreach( $villa_features as $key => $value ) {
				$box = get_post_meta( $id, $key, true );
				$i++;
				if( $i == 1 || $i == 13 || $i == 25 || $i == 37 ) {
					$start = '<div class="list-column"><ul>';
				} else {
					$start = '';
				}
				
				if( $box == 0 ) {
					$output = '<li>' . $value . $disc . '</li>';
				} else {
					$output = '<li>' . $value . $tick . '</li>';
				}
				
				if( $i == 12 || $i == 24 || $i == 36 || $i == 48 ) {
					$end = '</ul></div>';
				} else {
					$end = '';
				}
				$features_array[] = $start . $output . $end;
			}
			$features = implode( "", $features_array ); //output string of features
			
			$content .= '<section class="entry features"><div id="feature-list">' . $features . '</div></section><div style="clear: both;"></div>';
			return $content;
		}
		else {
			return $content;
		}
	}
}
add_filter( 'the_content', 'show_listing_features', 20, 1 ); //set priority to 20 (second)


/*-----------------------------------------------------------------------------------*/
/* 13. Agoda Search Box (pass agoda_id as argument?) */
/*-----------------------------------------------------------------------------------*/

if ( ! function_exists( 'show_agoda_searchbox' ) ) {
	function show_agoda_searchbox( $content ) {
		$hotel_id = get_post_meta( get_the_id(), 'agoda_id', true );
		if( get_post_type() == 'villa_listings' && is_single() && $hotel_id != '' ) {
			$cid = '1606450';
			$content .= '<h2>When do you want to go to ' . get_the_title() . '?</h2>
							<p style="margin-bottom: 0;">Enter your dates and click <em>Search</em> to check price and availability with our trusted partners, Agoda.</p>
							<div id="SearchBox">&nbsp;</div>
							<script type="text/javascript">
								var AgodaSearch = new AgodaSearchBox({
								cid: ' . $cid . ',
								filterCityName: "",
								fixedCityName: false,
								fixedCityNameVisible: true,
								hotelID: "' . $hotel_id . '",
								checkInDateBefore: 3,
								night: 2,
								language: 1,
								currencyCode: "USD",
								newWindow: false,
								header: "",
								footer: "",
								style: "",
								Element: "SearchBox"
								});
							</script>
							<p>Booking with Agoda is easy and secure. Their prices are very competitive and they have hundreds of real customer reviews to help you decide where to stay.</p>';
			return $content;
		}
		else {
			return $content;
		}
	}
}
//add_filter( 'the_content', 'show_agoda_searchbox', 30, 1 ); //set priority to 30 (third)

if ( ! function_exists( 'show_enquiry_box' ) ) {
	function show_enquiry_box( $content ) {
		if( get_post_type() == 'villa_listings' && is_single() ) {
			$content .= '<h2>To enquire about staying at ' . get_the_title() . ', please send an email to:</h2><h3 style="text-align:center">jimbo@myseminyakvillas.com</h3>';
			return $content;
		}
		else {
			return $content;
		}
	}
}
add_filter( 'the_content', 'show_enquiry_box', 30, 1 ); //set priority to 30 (third)

/*-----------------------------------------------------------------------------------*/
/* 14. Display post tags on villa listings */
/*-----------------------------------------------------------------------------------*/

if ( ! function_exists( 'villa_listing_tags' ) ) {
	function villa_listing_tags( $content ) {
		if( is_single() && get_post_type() == 'villa_listings' ) {
			$content .= '<div class="post-utility">' . do_shortcode( '[post_tags before=""]' ) . '</div>';
			return $content;
		} else {
			return $content;
		}
	}
}
add_filter( 'the_content', 'villa_listing_tags', 40, 1 ); //set priority to 40 (fourth)


/*-----------------------------------------------------------------------------------*/
/* 15. Add villa listings to archive pages (tag, author) */
/*-----------------------------------------------------------------------------------*/

if ( ! function_exists( 'add_villa_listing_archives' ) ) {
	function add_villa_listing_archives( $query ) {
		if ( $query->is_author || $query->is_tag )
			$query->set( 'post_type', array(
				'post',
				'villa_listings'
			) );
	}
}
add_filter( 'pre_get_posts', 'add_villa_listing_archives' );


/*-----------------------------------------------------------------------------------*/
/* 16. Modify villa category archive page meta title */
/*-----------------------------------------------------------------------------------*/

if ( ! function_exists( 'filter_villa_category_title' ) ) {
	function filter_villa_category_title( $title ) {
		
		$my_title = 'Best Seminyak '; //set user title here
		
		if ( is_tax( 'villa_category' ) ) {
			$raw_title = $my_title . single_term_title( '', false ) . ' Villas';
			$title = $raw_title . ' - ';
		}
		return $title;
	}
}
add_filter( 'wp_title', 'filter_villa_category_title', 10, 1 );


/*-----------------------------------------------------------------------------------*/
/* 17. Load scripts for villa listings */
/*-----------------------------------------------------------------------------------*/

if ( ! function_exists( 'villa_listing_scripts' ) ) {
	function villa_listing_scripts() {
		if( get_post_type() == 'villa_listings' && is_single() ) {
			wp_enqueue_script(
				'thumbhover',
				plugins_url( 'js/jquery_thumbhover.js', __FILE__ ),
				array( 'jquery' ),
				false,
				true
			);
			wp_enqueue_script(
				'searchbox',
				'//ajaxsearch.partners.agoda.com/partners/SearchBox/Scripts/Agoda.SearchBoxV2.1.js',
				array( 'jquery' ),
				false,
				false
			);
		}
	}
}
add_action( 'wp_enqueue_scripts', 'villa_listing_scripts' );


/*-----------------------------------------------------------------------------------*/
/* 18. Add villa listings to main RSS feed */
/*-----------------------------------------------------------------------------------*/

if ( ! function_exists( 'add_cpt_main_feed' ) ) {
	function add_cpt_main_feed( $qv ) {
		if( isset( $qv['feed'] ) )
			$qv['post_type'] = array( 'post', 'villa_listings' );
		return $qv;
	}
}
add_filter( 'request', 'add_cpt_main_feed' );
?>
