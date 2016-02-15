<?php
if ( ! function_exists( 'set_training_category' ) ) {

// Register Custom Taxonomy
function set_training_category() {

	$labels = array(
		'name'                       => _x( 'Categories', 'Taxonomy General Name', 'TrainingTD' ),
		'singular_name'              => _x( 'Category', 'Taxonomy Singular Name', 'TrainingTD' ),
		'menu_name'                  => __( 'Categories', 'TrainingTD' ),
		'all_items'                  => __( 'All Items', 'TrainingTD' ),
		'parent_item'                => __( 'Parent Item', 'TrainingTD' ),
		'parent_item_colon'          => __( 'Parent Item:', 'TrainingTD' ),
		'new_item_name'              => __( 'New Item Name', 'TrainingTD' ),
		'add_new_item'               => __( 'Add New Category', 'TrainingTD' ),
		'edit_item'                  => __( 'Edit Item', 'TrainingTD' ),
		'update_item'                => __( 'Update Item', 'TrainingTD' ),
		'view_item'                  => __( 'View Item', 'TrainingTD' ),
		'separate_items_with_commas' => __( 'Separate items with commas', 'TrainingTD' ),
		'add_or_remove_items'        => __( 'Add or remove items', 'TrainingTD' ),
		'choose_from_most_used'      => __( 'Choose from the most used', 'TrainingTD' ),
		'popular_items'              => __( 'Popular Items', 'TrainingTD' ),
		'search_items'               => __( 'Search Items', 'TrainingTD' ),
		'not_found'                  => __( 'Not Found', 'TrainingTD' ),
	);
	$rewrite = array(
		'slug'                       => _x( 'training-category', 'Training category URL slug', 'TrainingTD' ),
		'with_front'                 => true,
		'hierarchical'               => false,
	);
	$args = array(
		'labels'                     => $labels,
		'hierarchical'               => true,
		'public'                     => true,
		'show_ui'                    => true,
		'show_admin_column'          => true,
		'show_in_nav_menus'          => false,
		'show_tagcloud'              => false,
		'rewrite'                    => $rewrite,
		'show_in_quick_edit'         => true,
		'query_var'           		 => true,
	);
	register_taxonomy( 'training_category', array( 'training_session' ), $args );

}

// Hook into the 'init' action
add_action( 'init', 'set_training_category', 0 );

}