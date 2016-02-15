<?php
/////////////////////////////////////////////////////////////////
// Metabox with Training Session Details
/////////////////////////////////////////////////////////////////

function create_training_metabox() {
    add_meta_box('training_metabox', __('Training in Detail','TrainingTD'), 'training_metabox', 'training_session', 'normal', 'high');
}

function training_metabox () {

// Get data from Custom Post
global $post;
$custom = get_post_custom();
 
// Set date and time formats based on the installation settings of the current installation (Settings
$date_format = get_option('date_format');
$time_format = get_option('time_format');
$timezone = get_option('gmt_offset');

// Get current Timestamp 
$now = current_time( 'timestamp');
 
// Set todays date if a field is not yet set 
// if ($meta_day_1_start == null) { $meta_day_1_start = $current_timestamp  ; $meta_day_1_start = $current_timestamp + 3600;  $meta_day_1_end = $current_timestamp + 7200;}
 
// Security hidden field
echo '<input type="hidden" name="training_nonce" id="training_nonce" value="' . wp_create_nonce( plugin_basename(__FILE__) ) . '" />';

// Create the output
?>
<div class="training-meta">

<ul class="link">
    <li class="url" > <label> <?php _e('Link','TrainingTD') ?> </label> <input name="_training_det_url" class="trainlocname required" size="30" maxlength="200" aria-required="true" type="text" aria-invalid="false" placeholder="<?php __('Link','TrainingTD') ?>" value="<?php echo get_post_meta($post->ID, '_training_det_url', true); ?>" /></li>
</ul>

<ul class="settings">
<h4><?php _e('Settings and Status','TrainingTD') ?></h4>
<li><input name="_training_set_print" id="_training_set_print" type="checkbox" value="1" <?php if( 1 == get_post_meta($post->ID, '_training_set_print', true) ) {echo " CHECKED";}?> >&nbsp;<span class="list-item-label"><?php _e('Training will be printed', 'TrainingTD') ?></span></li>
<li><input name="_training_set_1c" id="_training_set_1c" type="checkbox" value="1" <?php if( 1 == get_post_meta($post->ID, '_training_set_1c', true) ) {echo " CHECKED";}?> >&nbsp;<span class="list-item-label"><?php _e('First check is done', 'TrainingTD') ?></span></li>
<li><input name="_training_set_2c" id="_training_set_2c" type="checkbox" value="1" <?php if( 1 == get_post_meta($post->ID, '_training_set_2c', true) ) {echo " CHECKED";}?> >&nbsp;<span class="list-item-label"><?php _e('Second check is done', 'TrainingTD') ?></span></li>
</ul>

<h4><?php _e('Category','TrainingTD') ?></h4>

<?php $terms = get_the_terms( $post->ID, 'training_category' );
if ( $terms && ! is_wp_error( $terms ) ) {
	$cat = array();
	foreach ( $terms as $term ) {
		$cat[] = $term->name;
	}
}
?>
<div id="taxonomy-select" class="taxonomy-field categorydiv">
        <?php wp_dropdown_categories( array( 'taxonomy' => 'training_category', 'hide_empty' => 0, 'name' => '_training_tax_category', 'selected' => $cat[0], 'orderby' => 'name', 'hierarchical' => 0, 'value_field' => 'name', 'show_option_none'   => 'Keine Kategorie') ); ?>
</div>
<h4><?php _e('Days','TrainingTD') ?></h4>
<span class="meta-note"><?php _e('Please use this date format:','TrainingTD') . ': '?> <?php echo date($date_format, $now); ?> <?php _e('from','TrainingTD') ?> <?php echo date($time_format, $now); ?> <?php _e('till','TrainingTD') ?> <?php echo date($time_format, $now + 3600); ?> <?php _e('o\'clock','TrainingTD') ?></span>

<ul class="multiday">
<span class="meta-note"><?php _e('If the Training takes place on many days for the whole day. The date of Day 1 will be the starting point and the date of Day 2 will be the ending point of the Training','TrainingTD') ?></span>
</br>
<input name="_training_set_multiday" id="_training_set_multiday" type="checkbox" value=TRUE <?php if(get_post_meta($post->ID, '_training_set_multiday', true) ) {echo " CHECKED";}?> >&nbsp;<span class="list-item-label"><?php _e('Long and whole-time training', 'TrainingTD') ?></span>
</ul>
<ul class="day1">
    <li class="date" > <label>1. <?php _e('Day','TrainingTD') ?> </label> <input name="_training_day_1" class="traindate" size="10" maxlength="10" aria-required="true" type="text" aria-invalid="false" placeholder="<?php __('Date','TrainingTD') ?>" value="<?php echo get_date_from_meta($post->ID,'_training_day_1', true, false); ?>" /></li>
    <li class="time-start" ><label> <?php _e('from','TrainingTD') ?> </label> <input name="_training_day_1_start" size="10" maxlength="8" aria-required="true" type="text" aria-invalid="false" placeholder="<?php __('Beginning','TrainingTD') ?>" value="<?php echo get_date_from_meta($post->ID,'_training_day_1', false, true); ?>" /></li>
    <li class="time-end" ><label> <?php _e('till','TrainingTD') ?> </label> <input name="_training_day_1_end" size="10" maxlength="8" aria-required="true" type="text" aria-invalid="false" placeholder="<?php __('Ending','TrainingTD') ?>" value="<?php echo get_date_from_meta($post->ID,'_training_day_1_end', false, true); ?>" /> <?php _e('o\'clock','TrainingTD') ?></li>
</ul>
<div class="clear"> </div>
<ul class="day2">
    <li class="date" > <label>2. <?php _e('Day','TrainingTD') ?> </label> <input name="_training_day_2" class="traindate" size="10" maxlength="10" aria-required="true" type="text" aria-invalid="false" placeholder="<?php __('Date','TrainingTD') ?>" value="<?php echo get_date_from_meta($post->ID, '_training_day_2', true, false); ?>" /></li>
    <li class="time-start" ><label> <?php _e('from','TrainingTD') ?> </label> <input name="_training_day_2_start" size="10" maxlength="8" aria-required="true" type="text" aria-invalid="false" placeholder="<?php __('Beginning','TrainingTD') ?>" value="<?php echo get_date_from_meta($post->ID, '_training_day_2', false, true); ?>" /></li>
    <li class="time-end" ><label> <?php _e('till','TrainingTD') ?> </label> <input name="_training_day_2_end" size="10" maxlength="8" aria-required="true" type="text" aria-invalid="false" placeholder="<?php __('Ending','TrainingTD') ?>" value="<?php echo get_date_from_meta($post->ID, '_training_day_2_end', false, true); ?>" /> <?php _e('o\'clock','TrainingTD') ?></li>
</ul>
<div class="clear"> </div>
<ul class="day3">
    <li class="date" > <label>3. <?php _e('Day','TrainingTD') ?> </label> <input name="_training_day_3" class="traindate" size="10" maxlength="10" aria-required="true" type="text" aria-invalid="false" placeholder="<?php __('Date','TrainingTD') ?>" value="<?php echo get_date_from_meta($post->ID, '_training_day_3', true, false); ?>" /></li>
    <li class="time-start" ><label> <?php _e('from','TrainingTD') ?> </label> <input name="_training_day_3_start" size="10" maxlength="8" aria-required="true" type="text" aria-invalid="false" placeholder="<?php __('Beginning','TrainingTD') ?>" value="<?php echo get_date_from_meta($post->ID, '_training_day_3', false, true); ?>" /></li>
    <li class="time-end" ><label> <?php _e('till','TrainingTD') ?> </label> <input name="_training_day_3_end" size="10" maxlength="8" aria-required="true" type="text" aria-invalid="false" placeholder="<?php __('Ending','TrainingTD') ?>" value="<?php echo get_date_from_meta($post->ID, '_training_day_3_end', false, true); ?>" /> <?php _e('o\'clock','TrainingTD') ?></li>
</ul>
<div class="clear"> </div>
<ul class="day4">
    <li class="date" > <label>4. <?php _e('Day','TrainingTD') ?> </label> <input name="_training_day_4" class="traindate" size="10" maxlength="10" aria-required="true" type="text" aria-invalid="false" placeholder="<?php __('Date','TrainingTD') ?>" value="<?php echo get_date_from_meta($post->ID, '_training_day_4', true, false); ?>" /></li>
    <li class="time-start" ><label> <?php _e('from','TrainingTD') ?></label> <input name="_training_day_4_start" size="10" maxlength="8" aria-required="true" type="text" aria-invalid="false" placeholder="<?php __('Beginning','TrainingTD') ?>" value="<?php echo get_date_from_meta($post->ID, '_training_day_4', false, true); ?>" /></li>
    <li class="time-end" ><label> <?php _e('till','TrainingTD') ?> </label> <input name="_training_day_4_end" size="10" maxlength="8" aria-required="true" type="text" aria-invalid="false" placeholder="<?php __('Ending','TrainingTD') ?>" value="<?php echo get_date_from_meta($post->ID, '_training_day_4_end', false, true); ?>" /> <?php _e('o\'clock','TrainingTD') ?></li>
</ul>
<div class="clear"> </div>
<ul class="day5">
    <li class="date" > <label>5. <?php _e('Day','TrainingTD') ?> </label> <input name="_training_day_5" class="traindate" size="10" maxlength="10" aria-required="true" type="text" aria-invalid="false" placeholder="<?php __('Date','TrainingTD') ?>" value="<?php echo get_date_from_meta($post->ID, '_training_day_5', true, false); ?>" /></li>
    <li class="time-start" ><label> <?php _e('from','TrainingTD') ?></label> <input name="_training_day_5_start" size="10" maxlength="8" aria-required="true" type="text" aria-invalid="false" placeholder="<?php __('Beginning','TrainingTD') ?>" value="<?php echo get_date_from_meta($post->ID, '_training_day_5', false, true); ?>" /></li>
    <li class="time-end" ><label> <?php _e('till','TrainingTD') ?> </label> <input name="_training_day_5_end" size="10" maxlength="8" aria-required="true" type="text" aria-invalid="false" placeholder="<?php __('Ending','TrainingTD') ?>" value="<?php echo get_date_from_meta($post->ID, '_training_day_5_end', false, true); ?>" /> <?php _e('o\'clock','TrainingTD') ?></li>
</ul>
<div class="clear"> </div>
<ul class="location">
	<h4><?php _e('Location','TrainingTD') ?></h4>
	<li><input name="_training_loc_noexist" id="_training_loc_noexist" type="checkbox" value=TRUE <?php if(get_post_meta($post->ID, '_training_loc_noexist', true) ) {echo " CHECKED";}?> >&nbsp;<span class="list-item-label"><?php _e('This training has no location', 'TrainingTD') ?></span></li>
	<li class="loc name" style="display:block"> <label> <?php  _e('Name','TrainingTD') ?> </label> <input name="_training_loc_name" class="trainlocname required" size="50" maxlength="50" aria-required="true" type="text" aria-invalid="false" placeholder="<?php __('Name','TrainingTD') ?>" value="<?php echo get_post_meta($post->ID, '_training_loc_name', true); ?>" /></li>
    <li class="loc street"><label> <?php  _e('Street','TrainingTD')  ?> </label> <input name="_training_loc_street" size="30" maxlength="30" aria-required="true" type="text" aria-invalid="false" placeholder="<?php __('Street','TrainingTD') ?>" value="<?php echo get_post_meta($post->ID, '_training_loc_street', true); ?>" /></li>
    <li class="loc postalcode"><label> <?php  _e('Postalcode','TrainingTD')  ?> </label> <input name="_training_loc_postalcode" size="5" maxlength="5" aria-required="true" type="text" aria-invalid="false" placeholder="<?php __('Postalcode','TrainingTD') ?>" value="<?php echo get_post_meta($post->ID, '_training_loc_postalcode', true); ?>" /></li>
    <li class="loc city"><label> <?php  _e('City','TrainingTD')  ?> </label> <input name="_training_loc_city" size="30" maxlength="30" aria-required="true" type="text" aria-invalid="false" placeholder="<?php __('City','TrainingTD') ?>" value="<?php echo get_post_meta($post->ID, '_training_loc_city', true); ?>" /></li>
	<li class="loc room"><label> <?php  _e('Room','TrainingTD')  ?> </label> <input name="_training_loc_room" size="30" maxlength="30" type="text" aria-invalid="false" placeholder="<?php __('Room','TrainingTD') ?>" value="<?php echo get_post_meta($post->ID, '_training_loc_room', true); ?>" /></li>
</ul>
<span class="loc meta-note"><?php _e('Automatically geocoded geographic coordinates for the address above. If they don\'t fit you may set them manual.','TrainingTD')?></span>
<ul class="loc coordinates">
	<li class="loc gps"><label> <?php  _e('Latitude','TrainingTD')  ?> </label> <input name="_training_loc_gps_lat" size="15" maxlength="15" type="text" aria-invalid="false" placeholder="<?php __('Latitude','TrainingTD') ?>" value="<?php echo get_post_meta($post->ID, '_training_loc_gps_lat', true); ?>" /> <label> <?php  _e('Longitude','TrainingTD')  ?> </label> <input name="_training_loc_gps_lng" size="15" maxlength="15" type="text" aria-invalid="false" placeholder="<?php __('Longitude','TrainingTD') ?>" value="<?php echo get_post_meta($post->ID, '_training_loc_gps_lng', true); ?>" /> <input name="_training_set_manpos" id="_training_set_manpos" type="checkbox" value=TRUE <?php if(get_post_meta($post->ID, '_training_set_manpos', true) ) {echo " CHECKED";}?> >&nbsp;<span class="list-item-label"><?php _e('Set GPS position manual.', 'TrainingTD') ?></span></li>
	<li class="loc link"><a class="event-loc-link" href="https://www.openstreetmap.org/?mlat=<?php echo get_post_meta($post->ID, '_training_loc_gps_lat', true); ?>&mlon=<?php echo get_post_meta($post->ID, '_training_loc_gps_lng', true); ?>#map=17/<?php echo get_post_meta($post->ID, '_training_loc_gps_lat', true); ?>/<?php echo get_post_meta($post->ID, '_training_loc_gps_lng', true); ?>" title="<?php _e('Show address on OpenStreetMap','TrainingTD') ?>" target="_blank"><?php _e('Show address on OpenStreetMap','TrainingTD') ?></a>
</ul>

<ul class="contact">
	<h4><?php _e('Contact','TrainingTD') ?></h4>
    <li class="name"><label> <?php _e('Name','TrainingTD') ?> </label> <input name="_training_con_name" class="trainlocname required" size="30" maxlength="50" aria-required="true" type="text" aria-invalid="false" placeholder="<?php __('Name','TrainingTD') ?>" value="<?php echo get_post_meta($post->ID, '_training_con_name', true); ?>" /></li>
    <li class="phone"><label> <?php  _e('Phone','TrainingTD')  ?> </label> <input name="_training_con_phone" size="20" maxlength="30" aria-required="true" type="text" aria-invalid="false" placeholder="<?php __('Phone','TrainingTD') ?>" value="<?php echo get_post_meta($post->ID, '_training_con_phone', true); ?>" /></li>
    <li class="email"><label> <?php  _e('Email','TrainingTD')  ?> </label> <input name="_training_con_email" size="40" maxlength="100" aria-required="true" type="email" aria-invalid="false" placeholder="<?php __('Email','TrainingTD') ?>" value="<?php echo get_post_meta($post->ID, '_training_con_email', true); ?>" /></li>
</ul>
<ul class="other">
	<h4><?php _e('Further detail','TrainingTD') ?></h4>
    <li class="orgname" > <label> <?php _e('Organiser','TrainingTD') ?> </label> <input name="_training_det_orgname" class="trainlocname required" size="30" maxlength="200" aria-required="true" type="text" aria-invalid="false" placeholder="<?php __('Organiser','TrainingTD') ?>" value="<?php echo get_post_meta($post->ID, '_training_det_orgname', true); ?>" /></li>
    <li class="fibs id" > <label> <?php _e('FIBS ID','TrainingTD') ?> </label> <input name="_training_det_fibs_id" class="traindetfibsid required" size="30" maxlength="200" aria-required="true" type="text" aria-invalid="false" placeholder="<?php __('FIBS ID','TrainingTD') ?>" value="<?php echo get_post_meta($post->ID, '_training_det_fibs_id', true); ?>" />     <?php if ( isset($custom['_training_det_fibs_id'][0])) { $out = '<a href="https://fibs.alp.dillingen.de/suche/details.php?v_id=' . $custom['_training_det_fibs_id'][0] . '" target ="_blank" >Zeige in FIBS</a>'; echo $out;} ?></li>

    <li class="fibs tag" > <label> <?php _e('FIBS Tag','TrainingTD') ?> </label> <input name="_training_det_fibs_tag" class="traindetfibstag required" size="30" maxlength="200" aria-required="true" type="text" aria-invalid="false" placeholder="<?php __('FIBS Tag','TrainingTD') ?>" value="<?php echo get_post_meta($post->ID, '_training_det_fibs_tag', true); ?>" /> </li>  
    <li class="deadline"><label> <?php  _e('Deadline','TrainingTD')  ?> </label> <input name="_training_day_deadline" size="10" maxlength="18" aria-required="true" type="text" aria-invalid="false" placeholder="<?php __('Deadline','TrainingTD') ?>" value="<?php echo get_date_from_meta($post->ID, '_training_day_deadline', true, false); ?>" /><label><?php _e('till','TrainingTD') ?></label><input name="_training_day_deadline_time" size="10" maxlength="18" aria-required="true" type="text" aria-invalid="false" placeholder="<?php __('Time','TrainingTD') ?>" value="<?php echo get_date_from_meta($post->ID, '_training_day_deadline', false, true, get_option('date_format'), 'H:i:s'); ?>" /><?php _e('o\'clock','TrainingTD') ?> <?php _e('Format','TrainingTD') ?> <?php echo date($date_format, $now); ?> 23:59:59 <?php _e('In case you only like to use a deadline date set time to 23:59:59!','TrainingTD') ?> </li>
    <li class="numparticipants"><label> <?php  _e('Maximum participants','TrainingTD')  ?> </label> <input name="_training_det_participants" size="5" maxlength="5" aria-required="true" type="number" aria-invalid="false" placeholder="<?php __('Max. participants','TrainingTD') ?>" value="<?php echo get_post_meta($post->ID, '_training_det_participants', true); ?>" /></li>
    <li class="fees"><label> <?php  _e('Fees','TrainingTD')  ?> </label> <input name="_training_det_fees" size="10" maxlength="6" aria-required="true" type="text" aria-invalid="false" placeholder="<?php __('Max. participants','TrainingTD') ?>" value="<?php echo get_post_meta($post->ID, '_training_det_fees', true); ?>" /></li>
</ul>
<label> <?php  _e('Referee label','TrainingTD')  ?> </label> <span class="referee"><select name="_training_det_referee_label" class=" " aria-required="true" aria-invalid="false"><option value=""><?php _e('Please select!','TrainingTD') ?></option><option value="female" <?php if('female' === get_post_meta($post->ID, '_training_det_referee_label', true) ) {echo ' selected="selected"';}?> > <?php _e('referee (female)','TrainingTD') ?></option><option value="male" <?php if('male' === get_post_meta($post->ID, '_training_det_referee_label', true) ) {echo ' selected="selected"';}?> ><?php _e('referee (male)','TrainingTD') ?></option><option value="many" <?php if('many' === get_post_meta($post->ID, '_training_det_referee_label', true) ) {echo ' selected="selected"';}?> ><?php _e('referee (many)','TrainingTD') ?></option></select></span>
<label> <?php  _e('Referee','TrainingTD')  ?> </label> <input name="_training_det_referee" size="50" maxlength="200" aria-required="true" type="text" aria-invalid="false" placeholder="<?php __('Referee','TrainingTD') ?>" value="<?php echo get_post_meta($post->ID, '_training_det_referee', true); ?>" /></li>
<ul class="school-types">
<h4><?php _e('Type of School','TrainingTD') ?></h4>
<li><label><input name="_training_det_schooltype_gs" value="1" type="checkbox" <?php if(1 == get_post_meta($post->ID, '_training_det_schooltype_gs', true) ) {echo " CHECKED";}?> >&nbsp;<span class="list-item-label">Grundschule</span></label></li>
<li><label><input name="_training_det_schooltype_ms" value="1" type="checkbox" <?php if(1 == get_post_meta($post->ID, '_training_det_schooltype_ms', true) ) {echo " CHECKED";}?> >&nbsp;<span class="list-item-label">Mittelschule</span></label></li>
<li><label><input name="_training_det_schooltype_rs" value="1" type="checkbox" <?php if(1 == get_post_meta($post->ID, '_training_det_schooltype_rs', true) ) {echo " CHECKED";}?> >&nbsp;<span class="list-item-label">Realschule</span></label></li>
<li><label><input name="_training_det_schooltype_gym" value="1" type="checkbox" <?php if(1 == get_post_meta($post->ID, '_training_det_schooltype_gym', true) ) {echo " CHECKED";}?> >&nbsp;<span class="list-item-label">Gymnasium</span></label></li>
<li><label><input name="_training_det_schooltype_fosbos" value="1" type="checkbox" <?php if(1 == get_post_meta($post->ID, '_training_det_schooltype_fosbos', true) ) {echo " CHECKED";}?> >&nbsp;<span class="list-item-label">Fach- und Berufsoberschulen</span></label></li>
<li><label><input name="_training_det_schooltype_bs" value="1" type="checkbox" <?php if(1 == get_post_meta($post->ID, '_training_det_schooltype_bs', true) ) {echo " CHECKED";}?> >&nbsp;<span class="list-item-label">Berufsschule</span></label></li>
<li><label><input name="_training_det_schooltype_fos" value="1" type="checkbox" <?php if(1 == get_post_meta($post->ID, '_training_det_schooltype_fos', true) ) {echo " CHECKED";}?> >&nbsp;<span class="list-item-label">Förderschule</span></label></li>
</ul>

<label> <?php  _e('Editor','TrainingTD') ?>: </label> <?php echo  get_post_meta($post->ID,'_training_det_editor_email',true); ?>
<div class="clear"> </div>
</div>
<?php

}

/**
 * Save Detail in Metabox to database
 * @param 
 * @return
 */
function save_training_details($post_id, $post){

	// verify this came from the our screen and with proper authorization,
	// because save_post can be triggered at other times
	if ( !wp_verify_nonce( $_POST['training_nonce'], plugin_basename(__FILE__) )) {
	return $post->ID;
	}

	// Is the user allowed to edit the post or page?
	if ( !current_user_can( 'edit_post', $post->ID ))
		return $post->ID;

 	// OK, authenticated, find and save the data
	$format =  get_option('date_format') . '-' . get_option('time_format');

 	update_post_meta($post->ID, '_training_day_1', create_date_for_db($_POST['_training_day_1'] , $_POST['_training_day_1_start'] ) );
	update_post_meta($post->ID, '_training_day_1_end', create_date_for_db( $_POST['_training_day_1'] , $_POST['_training_day_1_end'] ) );
	
	if ( !empty($_POST['_training_day_2']) ){
	update_post_meta($post->ID, '_training_day_2', create_date_for_db($_POST['_training_day_2']  , $_POST['_training_day_2_start'] ) );
	update_post_meta($post->ID, '_training_day_2_end', create_date_for_db($_POST['_training_day_2']  , $_POST['_training_day_2_end'] ) );
	} else {
	delete_post_meta($post->ID, '_training_day_2');
	delete_post_meta($post->ID, '_training_day_2_end');
	}
	
	if ( !empty($_POST['_training_day_3']) ){
	update_post_meta($post->ID, '_training_day_3', create_date_for_db($_POST['_training_day_3']  , $_POST['_training_day_3_start'] ) );
	update_post_meta($post->ID, '_training_day_3_end', create_date_for_db($_POST['_training_day_3']  , $_POST['_training_day_3_end'] ) );
	} else {
	delete_post_meta($post->ID, '_training_day_3');
	delete_post_meta($post->ID, '_training_day_3_end');
	}
	
	if ( !empty($_POST['_training_day_4']) ){
	update_post_meta($post->ID, '_training_day_4', create_date_for_db($_POST['_training_day_4']  , $_POST['_training_day_4_start'] ) );
	update_post_meta($post->ID, '_training_day_4_end', create_date_for_db($_POST['_training_day_4']  , $_POST['_training_day_4_end'] ) );
	} else {
	delete_post_meta($post->ID, '_training_day_4');
	delete_post_meta($post->ID, '_training_day_4_end');
	}
	
	if ( !empty($_POST['_training_day_5']) ){
	update_post_meta($post->ID, '_training_day_5', create_date_for_db($_POST['_training_day_5']  , $_POST['_training_day_5_start'] ) );
	update_post_meta($post->ID, '_training_day_5_end', create_date_for_db($_POST['_training_day_5']  , $_POST['_training_day_5_end'] ) );
	} else {
	delete_post_meta($post->ID, '_training_day_5');
	delete_post_meta($post->ID, '_training_day_5_end');
	}
	
	//Location
	update_post_meta($post->ID, '_training_loc_noexist', sanitize_text_field($_POST['_training_loc_noexist']) );
	update_post_meta($post->ID, '_training_loc_name', sanitize_text_field($_POST['_training_loc_name']) );
	update_post_meta($post->ID, '_training_loc_street', sanitize_text_field($_POST['_training_loc_street']) );
	update_post_meta($post->ID, '_training_loc_postalcode', sanitize_text_field($_POST['_training_loc_postalcode']) );
	update_post_meta($post->ID, '_training_loc_city', sanitize_text_field($_POST['_training_loc_city']) );
	update_post_meta($post->ID, '_training_loc_room', sanitize_text_field($_POST['_training_loc_room']) );
	
	//Contact
	update_post_meta($post->ID, '_training_con_name', sanitize_text_field($_POST['_training_con_name']) );
	update_post_meta($post->ID, '_training_con_phone', sanitize_text_field($_POST['_training_con_phone']) );
	update_post_meta($post->ID, '_training_con_email', sanitize_email($_POST['_training_con_email']) );
	
	//Further Detail
	update_post_meta($post->ID, '_training_det_orgname', sanitize_text_field($_POST['_training_det_orgname']) );
	update_post_meta($post->ID, '_training_det_fibs_id', sanitize_text_field($_POST['_training_det_fibs_id']) );
	update_post_meta($post->ID, '_training_det_fibs_tag', sanitize_text_field($_POST['_training_det_fibs_tag']) );
	if ( !empty($_POST['_training_day_deadline']) ) {update_post_meta($post->ID, '_training_day_deadline', create_date_for_db($_POST['_training_day_deadline'] , $_POST['_training_day_deadline_time'], get_option('date_format'), 'H:i:s') ) ;} else { delete_post_meta($post->ID, '_training_day_deadline'); } ;
	update_post_meta($post->ID, '_training_det_participants', sanitize_text_field($_POST['_training_det_participants']) );
	update_post_meta($post->ID, '_training_det_fees', sanitize_text_field($_POST['_training_det_fees']) );
	update_post_meta($post->ID, '_training_det_url', sanitize_text_field($_POST['_training_det_url']) );

	
	//Referee
	update_post_meta($post->ID, '_training_det_referee_label', sanitize_text_field($_POST['_training_det_referee_label']) );
	update_post_meta($post->ID, '_training_det_referee', sanitize_text_field($_POST['_training_det_referee']) );
	
	//Schooltypes
	update_post_meta($post->ID, '_training_det_schooltype_gs', sanitize_text_field($_POST['_training_det_schooltype_gs']) );
	update_post_meta($post->ID, '_training_det_schooltype_ms', sanitize_text_field($_POST['_training_det_schooltype_ms']) );
	update_post_meta($post->ID, '_training_det_schooltype_rs', sanitize_text_field($_POST['_training_det_schooltype_rs']) );
	update_post_meta($post->ID, '_training_det_schooltype_gym', sanitize_text_field($_POST['_training_det_schooltype_gym']) );
	update_post_meta($post->ID, '_training_det_schooltype_fosbos', sanitize_text_field($_POST['_training_det_schooltype_fosbos']) );
	update_post_meta($post->ID, '_training_det_schooltype_bs', sanitize_text_field($_POST['_training_det_schooltype_bs']) );
	update_post_meta($post->ID, '_training_det_schooltype_fos', sanitize_text_field($_POST['_training_det_schooltype_fos']) );
	
	if (!isset($_POST['_training_set_print'])){
	update_post_meta($post->ID, '_training_set_print', 0);
	} else {
	update_post_meta($post->ID, '_training_set_print', 1);
	}
	update_post_meta($post->ID, '_training_set_1c', sanitize_text_field($_POST['_training_set_1c']) );
	update_post_meta($post->ID, '_training_set_2c', sanitize_text_field($_POST['_training_set_2c']) );
	update_post_meta($post->ID, '_training_set_manpos', sanitize_text_field($_POST['_training_set_manpos']) );
	update_post_meta($post->ID, '_training_set_multiday', sanitize_text_field($_POST['_training_set_multiday']) );
	
	
	// GPS
	if (! $_POST['_training_loc_noexist'] ){
		if ($_POST['_training_set_manpos']) {
		// Manuelle GPS Position
		update_post_meta($post->ID, '_training_loc_gps_lat', sanitize_text_field($_POST['_training_loc_gps_lat']) );
		update_post_meta($post->ID, '_training_loc_gps_lng', sanitize_text_field($_POST['_training_loc_gps_lng']) );
		} else {
		// Automatische GPS Position
		$locgps = geocode_training_address($_POST['_training_loc_street'], $_POST['_training_loc_postalcode'], $_POST['_training_loc_city'],'');
		update_post_meta($post->ID, '_training_loc_gps_lat', $locgps[0] );
		update_post_meta($post->ID, '_training_loc_gps_lng', $locgps[1] );
		}
	}
	
	//Set Taxonomies
	wp_set_object_terms( $post->ID, sanitize_text_field( $_POST['_training_tax_category'] ),'training_category', false );
	update_post_meta($post->ID, '_training_category', $_POST['_training_tax_category'] );
  
}

/**
 * Set the the columns in the admin list view
 * @param 
 * @return
 */ 
function training_admin_edit_columns($columns) {
 
$columns = array(
    "cb" => "<input type=\"checkbox\" />",
    "taxonomy-training_category" => __('Category', 'TrainingTD'),
    "title" => __('Title', 'TrainingTD'),
    "training_col_days" => __('Days', 'TrainingTD'),
    "training_col_contact" => __('Contact', 'TrainingTD'),
    "training_col_further_detail" => __('Detail', 'TrainingTD'),
    "date" => __('Input', 'TrainingTD'),
    //"training_col_ev_desc" => "Description",
    //"training_col_ev_thumb" => "Thumbnail",
    
   
    );
return $columns;
}

/**
 * Format the the columns in the admin list view
 * @param 
 * @return
 */ 
 function training_admin_custom_columns($column) {
	global $post;
	$date_format = get_option('date_format');
	$time_format = get_option('time_format');
	$custom = get_post_custom();
	switch ($column) {
		case "training_col_days":
			// - show dates -
			if ( isset($custom['_training_day_1'][0]) ) {
				if ( isDateOld( $custom['_training_day_1'][0] ) OR isDateOld( $custom['_training_day_1_end'][0] ) ) { $spanopen = '<span class="training-error"/>'; $spanclose = '</span>'; } else { $spanopen = ''; $spanclose = '';}
				$day1_out = $spanopen . '<strong>' . get_date_from_value($custom['_training_day_1'][0], true, false) . '</strong> ' . get_date_from_value($custom['_training_day_1'][0], false, true) . ' – ' . get_date_from_value($custom['_training_day_1_end'][0], false, true) . ' ' . __('o\'clock', 'TrainingTD') . $spanclose;
				echo $day1_out . '<br />';
			}
			if ( isset($custom['_training_day_2'][0]) ) {
				if ( isDateOld( $custom['_training_day_2'][0] ) OR isDateOld( $custom['_training_day_2_end'][0] )) { $spanopen = '<span class="training-error"/>'; $spanclose = '</span>'; } else { $spanopen = ''; $spanclose = '';}
				$day2_out = $spanopen . '<strong>' . date($date_format, $custom['_training_day_2'][0]) . '</strong> ' . date($time_format, $custom['_training_day_2'][0]) . ' – ' . date($time_format, $custom['_training_day_2_end'][0]) . ' ' . __('o\'clock', 'TrainingTD') . $spanclose;
				echo $day2_out . '<br />';
			}
			if ( isset($custom['_training_day_3'][0]) ) {
				if ( isDateOld( $custom['_training_day_3'][0] ) OR isDateOld( $custom['_training_day_3_end'][0] )) { $spanopen = '<span class="training-error"/>'; $spanclose = '</span>'; } else { $spanopen = ''; $spanclose = '';}
				$day3_out = $spanopen . '<strong>' . date($date_format, $custom['_training_day_3'][0]) . '</strong> ' . date($time_format, $custom['_training_day_3'][0]) . ' – ' . date($time_format, $custom['_training_day_3_end'][0]) . ' ' . __('o\'clock', 'TrainingTD') . $spanclose;
				echo $day3_out . '<br />';
			}
			if ( isset($custom['_training_day_4'][0]) ) {
				if ( isDateOld( $custom['_training_day_4'][0] ) OR isDateOld( $custom['_training_day_4_end'][0] )) { $spanopen = '<span class="training-error"/>'; $spanclose = '</span>'; } else { $spanopen = ''; $spanclose = '';}
			$day4_out = $spanopen . '<strong>' . date($date_format, $custom['_training_day_4'][0]) . '</strong> ' . date($time_format, $custom['_training_day_4'][0]) . ' – ' . date($time_format, $custom['_training_day_4_end'][0]) . ' ' . __('o\'clock', 'TrainingTD') . $spanclose;
			echo $day4_out . '<br />';
			}
			if ( isset($custom['_training_day_5'][0]) ) {
				if ( isDateOld( $custom['_training_day_5'][0] ) OR isDateOld( $custom['_training_day_5_end'][0] )) { $spanopen = '<span class="training-error"/>'; $spanclose = '</span>'; } else { $spanopen = ''; $spanclose = '';}
			$day5_out = $spanopen . '<strong>' . date($date_format, $custom['_training_day_5'][0]) . '</strong> ' . date($time_format, $custom['_training_day_5'][0]) . ' – ' . date($time_format, $custom['_training_day_5_end'][0]) . ' ' . __('o\'clock', 'TrainingTD') . $spanclose;
			echo $day5_out . '<br />';
			}				
			
			
			if ( $custom['_training_set_print'][0] AND $custom['_training_set_1c'][0] AND $custom['_training_set_2c'][0] AND $custom['_training_det_fibs_tag'][0] > ' ' ) {
			echo '<br /><span class="dashicons dashicons-media-document training-ok"/> </span> <span class="training-ok"/>' . __('will be printed', 'TrainingTD') . '</span>';
			} else {
			echo '<br /><span class="dashicons dashicons-media-document training-error"/> </span> <span class="training-error"/>' . __('won\'t be printed', 'TrainingTD') . '</span>';
			}
			if ( $custom['_training_set_1c'][0] ) {
			echo '<br /><span class="dashicons dashicons-yes training-ok"/> </span> <span class="training-ok"/>' . __('1. check', 'TrainingTD') . '</span><br />';
			} else {
			echo '<br /><span class="dashicons dashicons-no-alt training-warning"/> </span> <span class="training-warning"/>' . __('1. check', 'TrainingTD') . '</span> <br />';
			}
			if ( $custom['_training_set_2c'][0] ) {
			echo '<span class="dashicons dashicons-yes training-ok"/> </span> <span class="training-ok"/>' . __('2. check', 'TrainingTD') . '</span> <br />';
			} else {
			echo '<span class="dashicons dashicons-no-alt training-warning"/> </span> <span class="training-warning"/>' . __('2. check', 'TrainingTD') . '</span> <br />';
			}
			if ( $custom['_training_det_fibs_tag'][0] > ' ' ) {
			echo '<span class="dashicons dashicons-tag training-ok"/> </span> <span class="training-ok"/>' . __('AKZ set', 'TrainingTD') . '</span> <br />';
			} else {
			echo '<span class="dashicons dashicons-tag training-warning"/> </span> <span class="training-warning"/>' . __('AKZ missing', 'TrainingTD') . '</span> <br />';
			}
		break;
		case "training_col_contact":
			// - show contact -
			if ( isset($custom['_training_con_name'][0]) ) {
			$name_out = $custom['_training_con_name'][0];
			echo $name_out . '<br />';
			}
			if ( isset($custom['_training_con_phone'][0]) ) {
			$phone_out = $custom['_training_con_phone'][0];
			echo $phone_out . '<br />';
			}
			if ( isset($custom['_training_con_email'][0]) ) {
			$email_out = '<a href="mailto:' . $custom['_training_con_email'][0] . '" title="' . __('Send email to','TrainingTD') . ' ' . $custom['_training_con_name'][0] . '">' . $custom['_training_con_email'][0] . '</a>';		
			echo $email_out . '<br />';
			}
			if ( isset($custom['_training_det_editor_email'][0]) ) {
			$out = '<strong>' . __('Editor','TrainingTD') . '</strong><br />' . '<a href="mailto:' . $custom['_training_det_editor_email'][0] . '" title="' . __('Send email to','TrainingTD') . ' ' . $custom['_training_det_editor_email'][0] . '">' . $custom['_training_det_editor_email'][0] . '</a>';		
			echo $out;
			}	
		break;
		case "training_col_ev_desc";
			the_excerpt();
		break;
		case "training_col_further_detail":
			// - show details -
			if ( isset($custom['_training_det_orgname'][0]) ) {
			$out = '<span class="list-label"/>' . __('Organiser', 'TrainingTD') . ':</span> ' . $custom['_training_det_orgname'][0];
			echo $out . '<br />';
			}
			if ( $custom['_training_det_fibs_id'][0] > '' && $custom['_training_det_fibs_tag'][0] > '') {
			$out = ' <a href="http://fibs.alp.dillingen.de/suche/details.php?v_id=' . $custom['_training_det_fibs_id'][0] . '" target="_blank" >' . $custom['_training_det_fibs_tag'][0] . '</a>';
			echo $out . '<br />';
			} else { $error = '<span class="training-warning"/>' . __('AKZ and ID missing', 'TrainingTD') . '</span><br />'; echo $error;}
			if ( isset($custom['_training_day_deadline'][0]) ) {
			$out = '<span class="list-label"/>' .  __('Deadline', 'TrainingTD') . ':</span> ' . get_date_from_value($custom['_training_day_deadline'][0], true, true ) . ' ' . __('o\'clock', 'TrainingTD');
			echo $out . '<br />';
			}
			if ( isset($custom['_training_det_participants'][0]) ) {
			$out = '<span class="list-label"/>' .  __('Max. participants', 'TrainingTD') . ':</span> ' . $custom['_training_det_participants'][0];
			echo $out . '<br />';
			}
			if ( isset($custom['_training_det_fees'][0]) ) {
			$out = '<span class="list-label"/>' .  __('Fees', 'TrainingTD') . ':</span> ' .  $custom['_training_det_fees'][0] . ' '. __('EUR', 'TrainingTD');
			echo $out . '<br />';
			}
			if ( isset($custom['_training_det_referee_label'][0]) ) {
				$label = '<span class="list-label"/>' . referee_label('default') . ':</span> ';
				if ( isset($custom['_training_det_referee_label'][0]) ) { $label = referee_label($custom['_training_det_referee_label'][0]);	}
				$out = '<span class="list-label"/>' . $label . ':</span> ' . $custom['_training_det_referee'][0];
				echo $out . '<br />';
			}	
		break;
 	}
}


