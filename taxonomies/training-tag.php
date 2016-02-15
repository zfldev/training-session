<?php
if ( ! function_exists( 'set_training_tag' ) ) {

// Register Custom Taxonomy
function set_training_tag() {

	$labels = array(
		'name'                       => _x( 'Tags', 'Taxonomy General Name', 'TrainingTD' ),
		'singular_name'              => _x( 'Tag', 'Taxonomy Singular Name', 'TrainingTD' ),
		'menu_name'                  => __( 'Tags', 'TrainingTD' ),
		'all_items'                  => __( 'All Items', 'TrainingTD' ),
		'parent_item'                => __( 'Parent Item', 'TrainingTD' ),
		'parent_item_colon'          => __( 'Parent Item:', 'TrainingTD' ),
		'new_item_name'              => __( 'New Item Name', 'TrainingTD' ),
		'add_new_item'               => __( 'Add New Tag', 'TrainingTD' ),
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
		'slug'                       => _x( 'training-tag', 'Training tag URL slug', 'TrainingTD' ),
		'with_front'                 => true,
		'hierarchical'               => false,
	);
	$args = array(
		'labels'                     => $labels,
		'hierarchical'               => false,
		'public'                     => true,
		'show_ui'                    => true,
		'show_admin_column'          => true,
		'show_in_nav_menus'          => false,
		'show_tagcloud'              => true,
		'rewrite'                    => $rewrite,
		'show_in_quick_edit'         => true,
		'query_var'             	 => true,
	);
	register_taxonomy( 'training_tag', array( 'training_session' ), $args );

}

// Hook into the 'init' action
add_action( 'init', 'set_training_tag', 0 );

} 