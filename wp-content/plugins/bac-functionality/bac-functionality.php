<?php
/**
 * Plugin Name:       BrianCoords.com Custom Functionality
 * Description:       Custom .
 * Requires at least: 6.1
 * Requires PHP:      7.0
 * Version:           0.1.1
 * Author:            The WordPress Contributors
 * License:           GPL-2.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       bac-functionality
 *
 * @package           bac-functionality
 */

namespace BacCustomFunctionality;

/**
 * Test filtering for emoji
 *
 * @param array $data The post data.
 * @return array
 */
function test_filtering_emoji( $data ) {
	if ( ! empty( $data['post_content'] ) ) {
		$data['post_content'] = wp_encode_emoji( $data['post_content'] );
	}
	return $data;
}
add_filter( 'wp_insert_post_data', __NAMESPACE__ . '\test_filtering_emoji', 99 );



/**
 * Enqueue custom styles.
 *
 * @return void
 */
function enqueue_custom_styles() {
	wp_enqueue_style( 'bac-custom-styles', plugin_dir_url( __FILE__ ) . 'style.css', array(), '0.1.1', 'all' );
}
add_action( 'wp_enqueue_scripts', __NAMESPACE__ . '\enqueue_custom_styles' );
add_action( 'enqueue_block_editor_assets', __NAMESPACE__ . '\enqueue_custom_styles' );

/**
 * Add a link tag to the title of posts that have a custom link set.
 *
 * @param string $title The title of the post.
 * @param int    $id    The ID of the post.
 * @return string The title of the post.
 */
function bc_add_link_tag( $title, $id ) {

	if ( get_post_meta( $id, '_links_to', true ) ) {
		$title = '&#128279; ' . $title;
	}

	return $title;
}
add_filter( 'the_title', __NAMESPACE__ . '\bc_add_link_tag', 10, 2 );

/**
 * Add a custom post type to the front page query.
 *
 * @param WP_Query $query The main query.
 * @return WP_Query The main query.
 */
function bc_add_cpt_to_front_page_query( $query ) {
	if ( is_home() && $query->is_main_query() && ! is_admin() ) {
		$query->set( 'post_type', array( 'post', 'newsletter' ) );
	}
	return $query;
}
add_action( 'pre_get_posts', __NAMESPACE__ . '\bc_add_cpt_to_front_page_query' );

/**
 * Register a custom post type called "Newsletter".
 *
 * @see get_post_type_labels() for label keys.
 */
function bcdotcom_newsletter_cpt_init() {
	$labels = array(
		'name'                  => _x( 'Newsletters', 'Post type general name', 'textdomain' ),
		'singular_name'         => _x( 'Newsletter', 'Post type singular name', 'textdomain' ),
		'menu_name'             => _x( 'Newsletters', 'Admin Menu text', 'textdomain' ),
		'name_admin_bar'        => _x( 'Newsletter', 'Add New on Toolbar', 'textdomain' ),
		'add_new'               => __( 'Add New', 'textdomain' ),
		'add_new_item'          => __( 'Add New Newsletter', 'textdomain' ),
		'new_item'              => __( 'New Newsletter', 'textdomain' ),
		'edit_item'             => __( 'Edit Newsletter', 'textdomain' ),
		'view_item'             => __( 'View Newsletter', 'textdomain' ),
		'all_items'             => __( 'All Newsletters', 'textdomain' ),
		'search_items'          => __( 'Search Newsletters', 'textdomain' ),
		'parent_item_colon'     => __( 'Parent Newsletters:', 'textdomain' ),
		'not_found'             => __( 'No Newsletters found.', 'textdomain' ),
		'not_found_in_trash'    => __( 'No Newsletters found in Trash.', 'textdomain' ),
		'featured_image'        => _x( 'Newsletter Cover Image', 'Overrides the “Featured Image” phrase for this post type. Added in 4.3', 'textdomain' ),
		'set_featured_image'    => _x( 'Set cover image', 'Overrides the “Set featured image” phrase for this post type. Added in 4.3', 'textdomain' ),
		'remove_featured_image' => _x( 'Remove cover image', 'Overrides the “Remove featured image” phrase for this post type. Added in 4.3', 'textdomain' ),
		'use_featured_image'    => _x( 'Use as cover image', 'Overrides the “Use as featured image” phrase for this post type. Added in 4.3', 'textdomain' ),
		'archives'              => _x( 'Newsletter archives', 'The post type archive label used in nav menus. Default “Post Archives”. Added in 4.4', 'textdomain' ),
		'insert_into_item'      => _x( 'Insert into Newsletter', 'Overrides the “Insert into post”/”Insert into page” phrase (used when inserting media into a post). Added in 4.4', 'textdomain' ),
		'uploaded_to_this_item' => _x( 'Uploaded to this Newsletter', 'Overrides the “Uploaded to this post”/”Uploaded to this page” phrase (used when viewing media attached to a post). Added in 4.4', 'textdomain' ),
		'filter_items_list'     => _x( 'Filter Newsletters list', 'Screen reader text for the filter Newsletters heading on the post type listing screen. Default “Filter posts list”/”Filter pages list”. Added in 4.4', 'textdomain' ),
		'items_list_navigation' => _x( 'Newsletters list navigation', 'Screen reader text for the pagination heading on the post type listing screen. Default “Posts list navigation”/”Pages list navigation”. Added in 4.4', 'textdomain' ),
		'items_list'            => _x( 'Newsletters list', 'Screen reader text for the items list heading on the post type listing screen. Default “Posts list”/”Pages list”. Added in 4.4', 'textdomain' ),
	);

	$args = array(
		'labels'             => $labels,
		'public'             => true,
		'publicly_queryable' => true,
		'show_ui'            => true,
		'show_in_menu'       => true,
		'query_var'          => true,
		'rewrite'            => 'newsletter',
		'show_in_rest'       => 'true',
		'capability_type'    => 'post',
		'has_archive'        => 'newsletter',
		'hierarchical'       => false,
		'menu_icon'          => 'dashicons-email-alt2',
		'menu_position'      => null,
		'supports'           => array( 'title', 'editor', 'revisions', 'author', 'excerpt', 'comments' ),
		'taxonomies'         => array( 'category' ),
	);

	register_post_type( 'newsletter', $args );
}
add_action( 'init', __NAMESPACE__ . '\bcdotcom_newsletter_cpt_init' );



/**
 * Register a custom post type called "Draft".
 *
 * @see get_post_type_labels() for label keys.
 */
function bcdotcom_draft_cpt_init() {
	$labels = array(
		'name'                  => _x( 'Drafts', 'Post type general name', 'textdomain' ),
		'singular_name'         => _x( 'Draft', 'Post type singular name', 'textdomain' ),
		'menu_name'             => _x( 'Drafts', 'Admin Menu text', 'textdomain' ),
		'name_admin_bar'        => _x( 'Draft', 'Add New on Toolbar', 'textdomain' ),
		'add_new'               => __( 'Add New', 'textdomain' ),
		'add_new_item'          => __( 'Add New Draft', 'textdomain' ),
		'new_item'              => __( 'New Draft', 'textdomain' ),
		'edit_item'             => __( 'Edit Draft', 'textdomain' ),
		'view_item'             => __( 'View Draft', 'textdomain' ),
		'all_items'             => __( 'All Drafts', 'textdomain' ),
		'search_items'          => __( 'Search Drafts', 'textdomain' ),
		'parent_item_colon'     => __( 'Parent Drafts:', 'textdomain' ),
		'not_found'             => __( 'No Drafts found.', 'textdomain' ),
		'not_found_in_trash'    => __( 'No Drafts found in Trash.', 'textdomain' ),
		'featured_image'        => _x( 'Draft Cover Image', 'Overrides the “Featured Image” phrase for this post type. Added in 4.3', 'textdomain' ),
		'set_featured_image'    => _x( 'Set cover image', 'Overrides the “Set featured image” phrase for this post type. Added in 4.3', 'textdomain' ),
		'remove_featured_image' => _x( 'Remove cover image', 'Overrides the “Remove featured image” phrase for this post type. Added in 4.3', 'textdomain' ),
		'use_featured_image'    => _x( 'Use as cover image', 'Overrides the “Use as featured image” phrase for this post type. Added in 4.3', 'textdomain' ),
		'archives'              => _x( 'Draft archives', 'The post type archive label used in nav menus. Default “Post Archives”. Added in 4.4', 'textdomain' ),
		'insert_into_item'      => _x( 'Insert into Draft', 'Overrides the “Insert into post”/”Insert into page” phrase (used when inserting media into a post). Added in 4.4', 'textdomain' ),
		'uploaded_to_this_item' => _x( 'Uploaded to this Draft', 'Overrides the “Uploaded to this post”/”Uploaded to this page” phrase (used when viewing media attached to a post). Added in 4.4', 'textdomain' ),
		'filter_items_list'     => _x( 'Filter Drafts list', 'Screen reader text for the filter Drafts heading on the post type listing screen. Default “Filter posts list”/”Filter pages list”. Added in 4.4', 'textdomain' ),
		'items_list_navigation' => _x( 'Drafts list navigation', 'Screen reader text for the pagination heading on the post type listing screen. Default “Posts list navigation”/”Pages list navigation”. Added in 4.4', 'textdomain' ),
		'items_list'            => _x( 'Drafts list', 'Screen reader text for the items list heading on the post type listing screen. Default “Posts list”/”Pages list”. Added in 4.4', 'textdomain' ),
	);

	$args = array(
		'labels'             => $labels,
		'public'             => false,
		'publicly_queryable' => false,
		'show_ui'            => true,
		'show_in_menu'       => true,
		'query_var'          => true,
		'rewrite'            => false,
		'show_in_rest'       => 'true',
		'capability_type'    => 'post',
		'has_archive'        => 'newsletter',
		'hierarchical'       => false,
		'menu_icon'          => 'dashicons-edit',
		'menu_position'      => null,
		'supports'           => array( 'title', 'editor', 'revisions', 'author', 'excerpt' ),
		'taxonomies'         => array( 'post_tag' ),
	);

	register_post_type( 'draft', $args );
}
add_action( 'init', __NAMESPACE__ . '\bcdotcom_draft_cpt_init' );



/**
 * Register a custom post type called "Newsletter".
 *
 * @see get_post_type_labels() for label keys.
 */
function bcdotcom_presentation_cpt_init() {
	$labels = array(
		'name'                  => _x( 'Presentations', 'Post type general name', 'textdomain' ),
		'singular_name'         => _x( 'Presentation', 'Post type singular name', 'textdomain' ),
		'menu_name'             => _x( 'Presentations', 'Admin Menu text', 'textdomain' ),
		'name_admin_bar'        => _x( 'Presentation', 'Add New on Toolbar', 'textdomain' ),
		'add_new'               => __( 'Add New', 'textdomain' ),
		'add_new_item'          => __( 'Add New Presentation', 'textdomain' ),
		'new_item'              => __( 'New Presentation', 'textdomain' ),
		'edit_item'             => __( 'Edit Presentation', 'textdomain' ),
		'view_item'             => __( 'View Presentation', 'textdomain' ),
		'all_items'             => __( 'All Presentations', 'textdomain' ),
		'search_items'          => __( 'Search Presentations', 'textdomain' ),
		'parent_item_colon'     => __( 'Parent Presentations:', 'textdomain' ),
		'not_found'             => __( 'No Presentations found.', 'textdomain' ),
		'not_found_in_trash'    => __( 'No Presentations found in Trash.', 'textdomain' ),
		'featured_image'        => _x( 'Presentation Cover Image', 'Overrides the “Featured Image” phrase for this post type. Added in 4.3', 'textdomain' ),
		'set_featured_image'    => _x( 'Set cover image', 'Overrides the “Set featured image” phrase for this post type. Added in 4.3', 'textdomain' ),
		'remove_featured_image' => _x( 'Remove cover image', 'Overrides the “Remove featured image” phrase for this post type. Added in 4.3', 'textdomain' ),
		'use_featured_image'    => _x( 'Use as cover image', 'Overrides the “Use as featured image” phrase for this post type. Added in 4.3', 'textdomain' ),
		'archives'              => _x( 'Presentation archives', 'The post type archive label used in nav menus. Default “Post Archives”. Added in 4.4', 'textdomain' ),
		'insert_into_item'      => _x( 'Insert into Presentation', 'Overrides the “Insert into post”/”Insert into page” phrase (used when inserting media into a post). Added in 4.4', 'textdomain' ),
		'uploaded_to_this_item' => _x( 'Uploaded to this Presentation', 'Overrides the “Uploaded to this post”/”Uploaded to this page” phrase (used when viewing media attached to a post). Added in 4.4', 'textdomain' ),
		'filter_items_list'     => _x( 'Filter Presentations list', 'Screen reader text for the filter Presentations heading on the post type listing screen. Default “Filter posts list”/”Filter pages list”. Added in 4.4', 'textdomain' ),
		'items_list_navigation' => _x( 'Presentations list navigation', 'Screen reader text for the pagination heading on the post type listing screen. Default “Posts list navigation”/”Pages list navigation”. Added in 4.4', 'textdomain' ),
		'items_list'            => _x( 'Presentations list', 'Screen reader text for the items list heading on the post type listing screen. Default “Posts list”/”Pages list”. Added in 4.4', 'textdomain' ),
	);

	$args = array(
		'labels'             => $labels,
		'public'             => true,
		'publicly_queryable' => true,
		'show_ui'            => true,
		'show_in_menu'       => true,
		'query_var'          => true,
		'rewrite'            => 'presentation',
		'show_in_rest'       => 'true',
		'capability_type'    => 'post',
		'has_archive'        => false,
		'hierarchical'       => false,
		'menu_icon'          => 'dashicons-welcome-view-site',
		'menu_position'      => null,
		'supports'           => array( 'title', 'editor', 'revisions', 'author' ),
	);

	register_post_type( 'presentation', $args );
}
add_action( 'init', __NAMESPACE__ . '\bcdotcom_presentation_cpt_init' );




register_block_style(
	'core/cover',
	array(
		'name'         => 'example-pattern', // .is-style-example-pattern
		'label'        => 'Example Pattern',
		'inline_style' => '
		.is-style-example-pattern{
			position: relative;
		}
		.is-style-example-pattern img{
			transition:all 300ms ease;
			opacity:0;
		}
		.is-style-example-pattern:focus-within img,
		.is-style-example-pattern:hover img{
			transform: scale(1.2);
			opacity:1;
		}
		.is-style-example-pattern a::after{
			content: " ";
			position: absolute;
			cursor: pointer;
			top: 0;
			left: 0;
			width: 100%;
			height: 100%;
		}
		',
	)
);

register_block_style(
	'core/list',
	array(
		'label'        => 'Emoji List Pointing',
		'name'         => 'emoji-list-1',
		'inline_style' => '
		.is-style-emoji-list-1 {
			list-style:none;
			padding-left:0;
    	}
		.is-style-emoji-list-1.wp-block-list li {
			display:flex;
    	}
		.is-style-emoji-list-1 li::before {
			  content: "👉 ";
			  margin-right: 0.25rem;
    	}
		',
	)
);


register_block_style(
	'core/list',
	array(
		'label'        => 'Emoji List Checkbox',
		'name'         => 'emoji-list-2',
		'inline_style' => '
		.is-style-emoji-list-2 {
			list-style:none;
			padding-left:0;
    	}
		.is-style-emoji-list-2.wp-block-list li {
			display:flex;
    	}
		.is-style-emoji-list-2 li::before {
			  content: "✅ ";
			  margin-right: 0.25rem;
    	}
		',
	)
);
