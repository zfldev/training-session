<?php
if ( ! function_exists('training_session') ) {

// Register Custom Post Type
function training_session() {

	$labels = array(
		'name'                => _x( 'Training sessions', 'Post Type General Name', 'TrainingTD' ),
		'singular_name'       => _x( 'Training', 'Post Type Singular Name', 'TrainingTD' ),
		'menu_name'           => __( 'Training', 'TrainingTD' ),
		'name_admin_bar'      => __( 'Training', 'TrainingTD' ),
		'parent_item_colon'   => __( 'Parent Item:', 'TrainingTD' ),
		'all_items'           => __( 'All Items', 'TrainingTD' ),
		'add_new_item'        => __( 'Add New Item', 'TrainingTD' ),
		'add_new'             => __( 'Add New', 'TrainingTD' ),
		'new_item'            => __( 'New Item', 'TrainingTD' ),
		'edit_item'           => __( 'Edit Item', 'TrainingTD' ),
		'update_item'         => __( 'Update Item', 'TrainingTD' ),
		'view_item'           => __( 'View Item', 'TrainingTD' ),
		'search_items'        => __( 'Search Item', 'TrainingTD' ),
		'not_found'           => __( 'Not found', 'TrainingTD' ),
		'not_found_in_trash'  => __( 'Not found in Trash', 'TrainingTD' ),
	);
	$rewrite = array(
		'slug'                => _x( 'training', 'Training URL slug', 'TrainingTD' ),
		'with_front'          => true,
		'pages'               => true,
		'feeds'               => true,
	);
	$args = array(
		'label'               => __( 'training_session', 'TrainingTD' ),
		'description'         => __( 'Further vocational training for teachers', 'TrainingTD' ),
		'labels'              => $labels,
		'supports'            => array( 'title', 'editor', 'excerpt', 'thumbnail', 'revisions', 'custom-fields', ),
		'taxonomies'          => array( 'training_category', ' training_tag' ),
		'hierarchical'        => false,
		'public'              => true,
		'show_ui'             => true,
		'show_in_menu'        => true,
		'menu_position'       => 5,
		'menu_icon'           => 'dashicons-awards',
		'show_in_admin_bar'   => true,
		'show_in_nav_menus'   => true,
		'can_export'          => true,
		'has_archive'         => true,
		'exclude_from_search' => false,
		'publicly_queryable'  => true,
		'query_var'           => 'training_session',
		'rewrite'             => $rewrite,
		'capability_type'     => 'post',
	);
	register_post_type( 'training_session', $args );

}

// Hook into the 'init' action
add_action( 'init', 'training_session', 0 );

} 