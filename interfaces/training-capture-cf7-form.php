<?php 
/**
 * Saving data from Contact Form 7 form to training_session post type
 * This code is based on the code in Form to Post Plugin: https://wordpress.org/plugins/form-to-post/
 * Some options have been removed, Support for our custom Taxonomies was added
 * @param $cf7 WPCF7_ContactForm
 * @return bool
 */

/**
 * Callback from Contact Form 7.
 * @param $cf7 WPCF7_ContactForm
 * @return bool
 */
function saveCF7FormToTrainingSession($cf7) {
	if (!isset($cf7->posted_data) && class_exists('WPCF7_Submission')) {
		// Contact Form 7 version 3.9 removed $cf7->posted_data and now
		// we have to retrieve it from an API
		$submission = WPCF7_Submission::get_instance();
		if ($submission) {
			$data = array();
			$data['title'] = $cf7->title();
			$data['posted_data'] = $submission->get_posted_data();
			$data['uploaded_files'] = $submission->uploaded_files();
			saveFormToTrainingSession((object) $data);
		}
	} else {
		saveFormToTrainingSession($cf7);
	}
	return true;
}



/**
 * Callback from Contact Form 7. CF7 passes an object with the posted data which is inserted into the database
 * by this function.
 * @param $cf7 WPCF7_ContactForm|object the former when coming from CF7
 * @return bool
 */
function saveFormToTrainingSession($cf7) {
		 
	if (!isset($cf7->posted_data) ||
		!isset($cf7->posted_data['post_title']) ||
		!isset($cf7->posted_data['post_content'])
	) {
		// not a form submission intended to be made into a post
		return true;
	}
	
	if ('training_session' != $cf7->posted_data['post_type']){
		// we only save the custom post type training_session
		return true;
	} 
	
	// fixed post array values
	$post = array(
		'post_status' => 'draft', // => [ 'draft' | 'publish' | 'pending'| 'future' | 'private' ] //Set the status of the new post.
		'post_type' => 'training_session', // => [ 'post' | 'page' | 'link' | 'nav_menu_item' | custom post type ] //You may want to insert a regular post, page, link, a menu item or some custom post type
		'comment_status' => 'closed', // => [ 'closed' | 'open' ] // 'closed' means no comments.
		'post_category' => array(0)
	);

	global $user_ID;
	if (!isset($user_ID)) {
		$user_ID = 0; // non logged in
	}
	if ($user_ID != 0) {
		$post['post_author'] = $user_ID;
	}

	// setable post array values
	// we not allow all fields here some of them are set in code above
	// See http://codex.wordpress.org/Function_Reference/wp_insert_post
	$postFields = array(
		'ID', // => [ <post id> ] //Are you updating an existing post?
		'menu_order', // => [ <order> ] //If new post is a page, sets the order should it appear in the tabs.
		'ping_status', // => [ 'closed' | 'open' ] // 'closed' means pingbacks or trackbacks turned off
		'pinged', // => [ ? ] //?
		'post_author', // => [ <user ID> ] //The user ID number of the author.
		'post_content', // => [ <the text of the post> ] //The full text of the post.
		'post_date', // => [ Y-m-d H:i:s ] //The time post was made.
		'post_date_gmt', // => [ Y-m-d H:i:s ] //The time post was made, in GMT.
		'post_excerpt', // => [ <an excerpt> ] //For all your post excerpt needs.
		'post_name', // => [ <the name> ] // The name (slug) for your post
		'post_parent', // => [ <post ID> ] //Sets the parent of the new post.
		'post_password', // => [ ? ] //password for post?
		'post_title', // => [ <the title> ] //The title of your post. 
		'tags_input', // => [ '<tag>, <tag>, <...>' ] //For tags.
		'to_ping', // => [ ? ] //?
	);

	if (!isset($post['post_date_gmt']) && !isset($post['post_date'])) {
		$post['post_date_gmt'] = date('Y-m-d H:i:s');
	}

	// Add in any additional fields
	foreach ($postFields as $field) {
		if (isset($cf7->posted_data[$field])) {
			$post[$field] = stripslashes($cf7->posted_data[$field]);
		}
	}

	// Create the training_session post
	//error_log(print_r($post, true)); // debug
	$post_id = wp_insert_post($post); // wp_insert_post returns the new POST ID 
	
	/////////////////////////////////////////////////////////////////////////////////////////////////////
	// After creating the post we are able to set meta fields and custom taxonomies with the new POST ID 
	/////////////////////////////////////////////////////////////////////////////////////////////////////
	
	// Set taxnonomy training_category
	// singular and plural words (category vs categories) so both is possible
	if (isset($cf7->posted_data['training_category'])) {
	$categories = explode (',' , $cf7->posted_data['training_category']);
	wp_set_object_terms(  $post_id, $categories ,'training_category', false );
	}
	
	if (isset($cf7->posted_data['training_categories'])) {
	$categories = explode (',' , $cf7->posted_data['training_categories']);
	wp_set_object_terms(  $post_id, $categories ,'training_category', false );
	}
	
	// Set taxnonomy _training_tag
	// singular and plural words (tag vs tags) so both is possible
	if (isset($cf7->posted_data['training_tag'])) {
	$tags = explode (',' , $cf7->posted_data['training_tag']);
	wp_set_object_terms(  $post_id, $tags,'training_tag', false );
	}
	
	if (isset($cf7->posted_data['training_tags'])) {
	$tags = explode (',' , $cf7->posted_data['training_tag']);
	wp_set_object_terms(  $post_id, $tags,'training_tag', false );
	}

	
	// Set date and timesperator to minimize user input errors
	if (! isset($cf7->posted_data['dateseperator'])) {
	$dateseperator = '.'; // default datesperator if not set as hidden field
	} else {
	$dateseperator = $cf7->posted_data['dateseperator'];
	}
	if (! isset($cf7->posted_data['timeseperator'])) {
	$timeseperator = ':'; // default timeperator if not set as hidden field
	} else {
	$timeseperator = $cf7->posted_data['timeseperator'];
	}
	
	update_post_meta($post_id, '_training_set_print', 1);
	
	// Meta Tags for our custom post type training session
	foreach ($cf7->posted_data as $key => $val) {
	$prefix = '_'; // for testing you may use '' instead of '_'
		if (strpos($key, 'training_day_') === 0) {
			if (strpos($key, 'training_day_deadline') === 0) {
			update_post_meta($post_id, $prefix . 'training_day_deadline', create_date_for_db( replaceSeperator($cf7->posted_data['training_day_deadline'], $dateseperator ) , '23:59:59', $cf7->posted_data['dateformat'], 'H:i:s' ) );
			}	
			$daycount =  substr($key, 13, 1); // cutting the daynumber out of the key
			$case1 = 'training_day_' . $daycount;
			$case2 = $case1 . '_start';
			$case3 = $case1 . '_end';
			
			// If hours are not set by the user we set the hole day
			// if( empty ($cf7->posted_data[$case2]) ){
// 				$cf7->posted_data[$case2] = '0:00';
// 			}
// 			if( empty ($cf7->posted_data[$case3]) ){
// 				$cf7->posted_data[$case3] = '23:59';
// 			}
			
			// minimizing user mistakes by replacing to the right seperator if set as hidden field	
			switch ($key) {
				case $case1:
					if ( !empty($cf7->posted_data[$case1]) ){
						update_post_meta($post_id, $prefix . $case1, create_date_for_db( replaceSeperator( $cf7->posted_data[$case1] , $dateseperator ) , replaceSeperator( $cf7->posted_data[$case2] , $timeseperator ) , $cf7->posted_data['dateformat'], $cf7->posted_data['timeformat'] ) );
					}
				break;
				case $case2:
				// do nothing done in the step before
				break;
				case $case3:
					if ( !empty($cf7->posted_data[$case3]) ){
						update_post_meta($post_id, $prefix . $case3, create_date_for_db( replaceSeperator( $cf7->posted_data[$case1] , $dateseperator ) , replaceSeperator( $cf7->posted_data[$case3] , $timeseperator ) , $cf7->posted_data['dateformat'], $cf7->posted_data['timeformat'] ) );
					}
				break;
			}

		} elseif (strpos($key, 'training_det_schooltype') === 0) {
			foreach ($cf7->posted_data['training_det_schooltype'] as $key => $value){
				switch ($value) {
					case 'Grundschule':
					update_post_meta($post_id, $prefix . 'training_det_schooltype_gs', 1 );
					break;
					case 'Mittelschule':
					update_post_meta($post_id, $prefix . 'training_det_schooltype_ms', 1 );
					break;
					case 'Realschule':
					update_post_meta($post_id, $prefix . 'training_det_schooltype_rs', 1 );
					break;
					case 'Gymnasium':
					update_post_meta($post_id, $prefix . 'training_det_schooltype_gym', 1 );
					break;
					case 'Fach- und Berufsoberschulen':
					update_post_meta($post_id, $prefix . 'training_det_schooltype_fosbos', 1 );
					break;
					case 'Berufsschule':
					update_post_meta($post_id, $prefix . 'training_det_schooltype_bs', 1 );
					break;
					case 'Förderschule':
					update_post_meta($post_id, $prefix . 'training_det_schooltype_fos', 1 );
					break;
				}
			}			
		} elseif (strpos($key, 'training_det_audience') === 0) { 
			// Audience are added to tags
			wp_set_object_terms(  $post_id, $cf7->posted_data['training_det_audience'],'training_tag', true );
		} elseif (strpos($key, 'training_det_referee_label') === 0) { 
			switch ($cf7->posted_data['training_det_referee_label']) {
				case 'Referentin':
				update_post_meta($post_id, $prefix . 'training_det_referee_label', 'female' );
				break;
				case 'Referent':
				update_post_meta($post_id, $prefix . 'training_det_referee_label', 'male' );
				break;
				case 'Referierende':
				update_post_meta($post_id, $prefix . 'training_det_referee_label', 'many' );
				break;
			}	
		} elseif (strpos($key, 'training_') === 0) {
			$metaKey = $prefix . $key; // adding _ at the beginning so that fields will not appear in the custom fields metabox
			update_post_meta($post_id, $metaKey, $val);
			if (strpos($key, 'meta_') === 0) {
				$metaKey = strpos($key,5); // other meta fields may be added as well
				update_post_meta($post_id, $metaKey, $val);
			}
		
		}
	}

	return true;
}


