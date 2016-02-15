<?php
/*
 * Training shortcode to display training sessions
 * 
 */
 
//Set language for Dates and Times
setlocale(LC_TIME, get_locale() . '.UTF-8');

function training_by_date ( $atts ) {

// Shortcode arguments
extract(shortcode_atts(array(
    'groupby' => 'months', // months, or category
    'hidedelay' => '24',
    'datemode' => 'period', // all, first, period
    'showdate' => '1', // show and hide dates
    'showtime' => '1', // show and hide dates
    'classtable' => 'events-table',
    'classhddate' => 'event-list-date',
    'classhddesc' => 'event-list-time',
    'dateformat' => '',
    'timeformat' => '',
    'subhdelemt' => 'h3',
 ), $atts));

// ===== OUTPUT FUNCTION =====

ob_start();

// Set dateformat and timeformat if not set by the shortcode
if($dateformat == ''){
$dateformat = get_option('date_format');
}
if($timeformat == ''){
$timeformat = get_option('time_format');
}

// ===== LOOP: FULL EVENTS SECTION ====

// what should be "today" for the query so that training sessions will not disappear immediately at midnight
$todayDelay = strtotime('now') + ( get_option( 'gmt_offset' ) * 3600 ) - ($hidedelay * 3600);

// build query
global $wpdb;

// I dont use WP_Query because the I use the taxononomy a little so that each training can only be assinged to one taxonomy category. WP_Qery does not offer the posibilitiy to sort by taxonomy because usually this is not the way you use taxonomies. But for this case I thought it is the better way.
// In Case you like to change to WP_Qery one day everything is prepared, because the category is always also stored in a meta field _training_category as string. 

if ($groupby == 'category'){
$preparedQuery = $wpdb->prepare("SELECT * FROM $wpdb->posts wposts, $wpdb->postmeta as startmeta, $wpdb->postmeta endmeta, $wpdb->term_relationships trelationships, $wpdb->term_taxonomy ttaxonomy,  $wpdb->terms tterms
    WHERE wposts.ID = startmeta.post_id 
    AND wposts.ID = endmeta.post_id 
    AND wposts.ID = trelationships.object_id 
    AND trelationships.term_taxonomy_id = ttaxonomy.term_taxonomy_id 
    AND ttaxonomy.term_id = tterms.term_id 
    AND startmeta.meta_key = '_training_day_1' 
    AND endmeta.meta_key IN ('_training_day_1_end','_training_day_2_end', '_training_day_3_end', '_training_day_4_end', '_training_day_5_end')
	AND endmeta.meta_value > %d
	AND wposts.post_type = 'training_session'
    AND wposts.post_status = 'publish'
    AND ttaxonomy.taxonomy = 'training_category'
    GROUP BY wposts.ID
    ORDER BY tterms.slug ASC, startmeta.meta_value ASC", $todayDelay);
} elseif($groupby == 'months' OR $groupby =='weeks') {
$preparedQuery = $wpdb->prepare("SELECT * FROM $wpdb->posts wposts, $wpdb->postmeta as startmeta, $wpdb->postmeta endmeta, $wpdb->term_relationships trelationships, $wpdb->term_taxonomy ttaxonomy,  $wpdb->terms tterms
    WHERE wposts.ID = startmeta.post_id 
    AND wposts.ID = endmeta.post_id 
    AND wposts.ID = trelationships.object_id 
    AND trelationships.term_taxonomy_id = ttaxonomy.term_taxonomy_id 
    AND ttaxonomy.term_id = tterms.term_id 
    AND startmeta.meta_key = '_training_day_1' 
    AND endmeta.meta_key IN ('_training_day_1_end','_training_day_2_end', '_training_day_3_end', '_training_day_4_end', '_training_day_5_end')
	AND endmeta.meta_value > %d
	AND wposts.post_type = 'training_session'
    AND wposts.post_status = 'publish'
    AND ttaxonomy.taxonomy = 'training_category'
    GROUP BY wposts.ID
    ORDER BY startmeta.meta_value ASC, tterms.slug ASC", $todayDelay);
}

$trainings = $wpdb->get_results($preparedQuery, OBJECT);

// $args = array(
// 	'post_type'    => 'training_session',
// 	'meta_key'   => '_training_day_1',
// 	'orderby'    => 'meta_value_num',
// 	'order'      => 'ASC',
// 	'orderby'  => array( 'meta_value_num' => 'ASC', 'title' => 'ASC' ),
// 	'meta_key' => '_training_day_1'
// );
// 
// $the_query = new WP_Query( $args );
// 
// 
// // The Loop
// if ( $the_query->have_posts() ) {
// 	echo '<ul>';
// 	while ( $the_query->have_posts() ) {
// 		$the_query->the_post();
// 		echo '<li>' . get_the_title() . '</li>';
// 	}
// 	echo '</ul>';
// } else {
// 	// no posts found
// }
/* Restore original Post Data */
//wp_reset_postdata();


// - declare fresh day -
$groupcheck = null;
// - loop -
if ($trainings):
global $post;
foreach ($trainings as $post):
setup_postdata($post);
// set language for right date language

$category = get_the_terms( get_the_ID() , 'training_category');

$terms = get_the_terms( $post->ID, 'training_category' );
if ( $terms && ! is_wp_error( $terms ) ) {
	$cat = array();
	foreach ( $terms as $term ) {
		$cat[] = $term->name;
	}
}


$listheading ="<table cellpadding='0' cellspacing='0' class='$classtable'><thead><tr><th class='$classhddate' width='30%'>". __('Date','TrainingTD') . "</th><th class='$classhddesc' width='60%'>" .  __('Details','TrainingTD') . "</th></tr></thead><tbody>";
$listfooter ="</tbody></table>";

// - custom variables -
$custom = get_post_custom(get_the_ID());
$sd = $custom['_training_day_1'][0];
$ed = $custom['_training_day_1_end'][0];

if ($groupby == 'category'){
$subheading = $cat[0];
} elseif($groupby == 'months') {
$subheading = strftime ( "%B %Y", get_date_from_value($sd, 'U', false, $dateformat, $timeformat) ) ;
} elseif($groupby =='weeks'){
$subheading = __('Week','TrainingTD') . strftime ( " %W %Y", get_date_from_value($sd, 'U', false, $dateformat, $timeformat) );
}

//$subheading = get_date_from_value($sd, 'F Y', false, $dateformat, $timeformat);
//$subheading = $custom['_training_category'][0];
if ($groupcheck == null) { echo '<' . $subhdelemt .' class="full-events">' . $subheading . '</' . $subhdelemt .'>' . $listheading; }
if ($groupcheck != $subheading && $groupcheck != null) { echo $listfooter . '<' . $subhdelemt .' class="full-events">' . $subheading . '</' . $subhdelemt .'>' . $listheading; }

// - local time format -
$stime =  get_date_from_value($sd, true, true, $dateformat, $timeformat);
$etime =  get_date_from_value($ed, true, false, $dateformat, $timeformat);


// Create html output for dates
$dateout = html_dates_out($custom, $dateformat, $timeformat, $datemode, $showtime, $showdate);
?>
<tr>
<td><span class="ts-list-date"><?php echo $dateout; ?></span></td>
<td><a class="event-list-name" href="<?php echo get_permalink( $post_id ) ;?>" title="<?php echo the_title()?>" ><?php echo the_title() ;?></a></br> 
<?php if( isset($custom['_training_loc_gps_lat'][0]) AND isset($custom['_training_loc_gps_lng'][0]) AND !$custom['_training_loc_noexist'][0]){?>
 <i class="fa fa-map-marker"></i> <a class="event-loc-link" href="https://www.openstreetmap.org/?mlat=<?php echo $custom['_training_loc_gps_lat'][0] ;?>&mlon=<?php echo $custom['_training_loc_gps_lng'][0] ;?>#map=15/<?php echo $custom['_training_loc_gps_lat'][0] ;?>/<?php echo $custom['_training_loc_gps_lng'][0] ;?>" title="<?php _e('Show address on OpenStreetMap','TrainingTD') ?>" target="_blank">
<?php ;}?>
<?php if( $custom['_training_loc_noexist'][0]){ echo '';} else { echo $custom['_training_loc_name'][0];?>, <?php echo $custom['_training_loc_street'][0] ;?>, <?php echo $custom['_training_loc_postalcode'][0] ;?> <?php echo $custom['_training_loc_city'][0];}?>
<?php if( isset($custom['_training_loc_gps_lat'][0]) AND isset($custom['_training_loc_gps_lng'][0]) AND !$custom['_training_loc_noexist'][0]){?>
</a>
<?php ;}?>
</td>
</tr> 

<?php

// - fill daycheck with the current day -
$groupcheck = $subheading;

endforeach;
else :
endif;
echo $listfooter;

// ===== RETURN: FULL EVENTS SECTION =====

$output = ob_get_contents();
ob_end_clean();
return $output;
}

add_shortcode('training', 'training_by_date');
add_shortcode('fortbildungen', 'training_by_date');

function training_xml_generator( $atts ) {
	// Shortcode arguments
	extract(shortcode_atts(array(
		'check' => '1', // activate checks
		'small' => '', // which categories are small
		'dateformat' => '', // dateformat
		'timeformat' => '', // timeformat
		'startdate' => '', // starting date for export
		'enddate' => '',
		'monthsinfuture' => 8
	 ), $atts));
	
	// Set dateformat and timeformat if not set by the shortcode
	if($dateformat == ''){
	$dateformat = get_option('date_format');
	}
	if($timeformat == ''){
	$timeformat = get_option('time_format');
	}
	
	// Set dateformat and timeformat if not set by the shortcode
	if($startdate == ''){
	$startdate = strtotime('now') + ( get_option( 'gmt_offset' ) * 3600 );
	} else {
	$startdate = create_date_for_db($startdate, '00:00', $dateformat, $timeformat);
	}
	
	if($enddate == ''){
	$enddate = $startdate + 2592000*$monthsinfuture; // about ... months in future
	} else {
	$enddate = create_date_for_db($enddate, '00:00', $dateformat, $timeformat);
	}
	

	// ===== LOOP: FULL EVENTS SECTION ====
	// - query -

	global $wpdb;
	
	if ($ckeck == '1') {
	$preparedQuery = $wpdb->prepare("SELECT * FROM $wpdb->posts wposts, $wpdb->postmeta as startmeta, $wpdb->postmeta endmeta, $wpdb->term_relationships trelationships, $wpdb->term_taxonomy ttaxonomy,  $wpdb->terms tterms, $wpdb->postmeta print , $wpdb->postmeta check1 , $wpdb->postmeta check2, $wpdb->postmeta checkakz
		WHERE wposts.ID = startmeta.post_id 
		AND wposts.ID = endmeta.post_id 
		AND wposts.ID = trelationships.object_id 
		AND trelationships.term_taxonomy_id = ttaxonomy.term_taxonomy_id 
		AND ttaxonomy.term_id = tterms.term_id 
		AND wposts.ID = print.post_id
		AND wposts.ID = check1.post_id
		AND wposts.ID = check2.post_id
		AND wposts.ID = checkakz.post_id
		AND startmeta.meta_key = '_training_day_1' 
		AND endmeta.meta_key IN ('_training_day_1_end','_training_day_2_end', '_training_day_3_end', '_training_day_4_end', '_training_day_5_end')
		AND wposts.post_type = 'training_session'
		AND wposts.post_status = 'publish'
		AND ttaxonomy.taxonomy = 'training_category'
		AND print.meta_key = '_training_set_print'
		AND print.meta_value = '1'
		AND check1.meta_key = '_training_set_1c'
		AND check1.meta_value = '1'
		AND check2.meta_key = '_training_set_2c'
		AND check2.meta_value = '1'
		AND checkakz.meta_key = '_training_det_fibs_tag'
		AND checkakz.meta_value > ''
		GROUP BY wposts.ID
		ORDER BY tterms.slug ASC, startmeta.meta_value ASC");
	} else {
	$preparedQuery = $wpdb->prepare("SELECT * FROM $wpdb->posts wposts, $wpdb->postmeta as startmeta, $wpdb->postmeta endmeta, $wpdb->term_relationships trelationships, $wpdb->term_taxonomy ttaxonomy,  $wpdb->terms tterms, $wpdb->postmeta print
		WHERE wposts.ID = startmeta.post_id 
		AND wposts.ID = endmeta.post_id 
		AND wposts.ID = trelationships.object_id 
		AND trelationships.term_taxonomy_id = ttaxonomy.term_taxonomy_id 
		AND ttaxonomy.term_id = tterms.term_id 
		AND wposts.ID = print.post_id
		AND startmeta.meta_key = '_training_day_1' 
		AND endmeta.meta_key IN ('_training_day_1_end','_training_day_2_end', '_training_day_3_end', '_training_day_4_end', '_training_day_5_end')
		AND wposts.post_type = 'training_session'
		AND wposts.post_status = 'publish'
		AND ttaxonomy.taxonomy = 'training_category'
		AND print.meta_key = '_training_set_print'
		AND print.meta_value = '1'
		GROUP BY wposts.ID
		ORDER BY tterms.slug ASC, startmeta.meta_value ASC");
	}
	

	$trainings = $wpdb->get_results($preparedQuery, OBJECT);
	// - custom variables -


	$xmlfstline = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>';
	$xml.= '<Fortbildungen>';
	// trainigs found
	if ($trainings) { 
		global $post;
		$totalpages = 0;
		$countbig = 0;
		$countsmall = 0;
		$infotext = '';
		$catbefore = '';

		foreach ($trainings as $post) {
			setup_postdata($post);
			$custom = get_post_custom(get_the_ID());
			
			// do the datecheck
			if ($custom['_training_day_1'][0] > $startdate && $custom['_training_day_1'][0] < $enddate ){

			/// Prebuild Schularten und Labels
				if( isset($custom['_training_day_2'][0]) ) {
					$terminlabel ='Termine';
				} else { 
					$terminlabel = 'Termin';
				}
				$schularten = '';
				$schulartcount = 0;
				if( $custom['_training_det_schooltype_gs'][0] == '1' ) { $schularten.='GS, '; $schulartcount++; }
				if( $custom['_training_det_schooltype_ms'][0] == '1' ) { $schularten.='MS, '; $schulartcount++;}
				if( $custom['_training_det_schooltype_rs'][0] == '1' ) { $schularten.='RS, '; $schulartcount++;}
				if( $custom['_training_det_schooltype_gym'][0] == '1' ) { $schularten.='GYM, '; $schulartcount++;}
				if( $custom['_training_det_schooltype_fosbos'][0] == '1' ) { $schularten.='FOS/BOS, '; $schulartcount++;}
				if( $custom['_training_det_schooltype_bs'][0] == '1' ) { $schularten.='BS, '; $schulartcount++;}
				if( $custom['_training_det_schooltype_fos'][0] == '1' ) { $schularten.='FöS, '; $schulartcount++;}
				$schularten = substr($schularten, 0, -2);
				if( $schulartcount == 7 ) { $schularten = 'Alle'; }
				if( $schulartcount > 1 ) { $schulartenlabel = 'Schularten'; } else {  $schulartenlabel = 'Schulart'; }

				$category = get_the_terms( get_the_ID() , 'training_category');

				$terms = get_the_terms( $post->ID, 'training_category' );
				if ( $terms && ! is_wp_error( $terms ) ) {
					$catname = array();
					foreach ( $terms as $term ) {
						$catname[] = $term->name;
					}
				}


				if ( strpos($small, $catname[0] ) !==  false) {
					// Listendarstellung in kurzform maximal 7 Veranstaltungen auf einer Seite
					if ( $countbig > 0 ) { 
						$xmlc.= '<!-- Summe Groß: ' . $countbig . '-->'; $totalpages = $totalpages + ceil($countbig/2); $infotext.= $countbig . ' Veranstaltungen auf ' . ceil($countbig/2) .' Seiten';$countbig = 0;
					}
					if ($countsmall == 0) { 
						$infotext.=  "\n" . 'Kurz für ' . $catname[0] . ' ';
					}
					$xmlc.= '<!-- ' . $catname[0] . ' Kurzform -->';
					$xml.= '<Veranstaltung_Kurz>' . "\n";
					$xml.= '	<Titel>'. the_title('','',false) . '</Titel>' . "\n";
					$xml.= '	<Labels_Links>' . "\n";
					$xml.= '		<Label_Termine>' . $terminlabel . '</Label_Termine>' . "\n";
					$xml.= '		<Label_Referentinnen>'. referee_label( $custom['_training_det_referee_label'][0] ) . '</Label_Referentinnen>' . "\n";
					$xml.= '	</Labels_Links>' . "\n";
					$xml.= '	<Details_Links>' . "\n";
					$xml.= '		<Termin_1_Datum>'. get_date_from_value($custom['_training_day_1'][0], true, false, $dateformat, $timeformat) . '</Termin_1_Datum>' . "\n";
					$xml.= '		<Termin_1_Start>'. get_date_from_value($custom['_training_day_1'][0], false, true, $dateformat, $timeformat) . '</Termin_1_Start>' . "\n";
					$xml.= '		<Termin_1_Ende>'. get_date_from_value($custom['_training_day_1_end'][0], false, true, $dateformat, $timeformat) . '</Termin_1_Ende>' . "\n";
					if ( isset($custom['_training_day_2'][0]) ) { $xml.= '		<Termin_2_Datum>'. get_date_from_value($custom['_training_day_2'][0], true, false, $dateformat, $timeformat) . '</Termin_2_Datum>' . "\n"; } else {$xml.= '		<Termin_2_Datum></Termin_2_Datum>' . "\n"; }
					if ( isset($custom['_training_day_2'][0]) ) { $xml.= '		<Termin_2_Start>'. get_date_from_value($custom['_training_day_2'][0], false, true, $dateformat, $timeformat) . '</Termin_2_Start>' . "\n"; } else {$xml.= '		<Termin_2_Start></Termin_2_Start>' . "\n"; }
					if ( isset($custom['_training_day_2'][0]) ) { $xml.= '		<Termin_2_Ende>'. get_date_from_value($custom['_training_day_2_end'][0], false, true, $dateformat, $timeformat) . '</Termin_2_Ende>' . "\n"; } else {$xml.= '		<Termin_2_Ende></Termin_2_Ende>' . "\n"; }
					if ( isset($custom['_training_day_3'][0]) ) { $xml.= '		<Termin_3_Datum>'. get_date_from_value($custom['_training_day_3'][0], true, false, $dateformat, $timeformat) . '</Termin_3_Datum>' . "\n"; } else {$xml.= '		<Termin_3_Datum></Termin_3_Datum>' . "\n"; }
					if ( isset($custom['_training_day_3'][0]) ) { $xml.= '		<Termin_3_Start>'. get_date_from_value($custom['_training_day_3'][0], false, true, $dateformat, $timeformat) . '</Termin_3_Start>' . "\n"; } else {$xml.= '		<Termin_3_Start></Termin_3_Start>' . "\n"; }
					if ( isset($custom['_training_day_3'][0]) ) { $xml.= '		<Termin_3_Ende>'. get_date_from_value($custom['_training_day_3_end'][0], false, true, $dateformat, $timeformat) . '</Termin_3_Ende>' . "\n"; } else {$xml.= '		<Termin_3_Ende></Termin_3_Ende>' . "\n"; }
					if ( isset($custom['_training_day_4'][0]) ) { $xml.= '		<Termin_4_Datum>'. get_date_from_value($custom['_training_day_4'][0], true, false, $dateformat, $timeformat) . '</Termin_4_Datum>' . "\n"; } else {$xml.= '		<Termin_4_Datum></Termin_4_Datum>' . "\n"; }
					if ( isset($custom['_training_day_4'][0]) ) { $xml.= '		<Termin_4_Start>'. get_date_from_value($custom['_training_day_4'][0], false, true, $dateformat, $timeformat) . '</Termin_4_Start>' . "\n"; } else {$xml.= '		<Termin_4_Start></Termin_4_Start>' . "\n"; }
					if ( isset($custom['_training_day_4'][0]) ) { $xml.= '		<Termin_4_Ende>'. get_date_from_value($custom['_training_day_4_end'][0], false, true, $dateformat, $timeformat) . '</Termin_4_Ende>' . "\n"; } else {$xml.= '		<Termin_4_Ende></Termin_4_Ende>' . "\n"; }
					$xml.= '		<Referentinnen>'. $custom['_training_det_referee'][0] . '</Referentinnen>' . "\n";
					$xml.= '		<Preis>'. $custom['_training_det_fees'][0] . '</Preis>' . "\n";
					$xml.= '	</Details_Links>' . "\n";
					$xml.= '	<Labels_Rechts>' . "\n";
					$xml.= '		<Label_Schularten>' . $schulartenlabel . '</Label_Schularten>' . "\n";
					$xml.= '	</Labels_Rechts>' . "\n";
					$xml.= '	<Details_Rechts>' . "\n";
					$xml.= '		<Schularten>' . $schularten . '</Schularten>' . "\n";
					$xml.= '		<Anmeldeschluss>'. get_date_from_value( $custom['_training_day_deadline'][0] , true, false, $dateformat, $timeformat) . '</Anmeldeschluss>' . "\n";
					$xml.= '		<Aktenzeichen>'. $custom['_training_det_fibs_tag'][0] . '</Aktenzeichen>' . "\n";
					$xml.= '	</Details_Rechts>' . "\n";
					$xml.= '</Veranstaltung_Kurz>' . "\n";
					$countsmall ++;
					} else {
					if ( $countsmall > 0 ) { $xmlc.= '<!-- Summe Kurz: ' . $countsmall . '-->'; $totalpages = $totalpages + ceil($countsmall/7); $infotext.= ' mit ' .$countsmall . ' Veranstaltungen auf '. ceil($countsmall/7) .' Seiten';$countsmall = 0;}
					if ( $countbig == 0) { $infotext.= "\n" . 'Lang: ';}
					if ( $catname[0] != $catbefore) { $infotext.= $catname[0] . ', ';}
					$catbefore = $catname[0];
					/// Langdarstellung in Langform 2 Fortbildungen je Seite
					$xmlc.= '<!-- ' . $catname[0] . '-->';
					$xml.= '<Veranstaltung>' . "\n";
					$xml.= '	<Kategorie>'. $custom['_training_category'][0] . '</Kategorie>' . "\n";
					$xml.= '	<Titel>'. the_title('','',false) . '</Titel>' . "\n";
					$xml.= '	<Label>' . "\n";
					$xml.= '		<Label_Termine>' . $terminlabel . '</Label_Termine>' . "\n";
					$xml.= '		<Label_Referentinnen>'. referee_label( $custom['_training_det_referee_label'][0] ) . '</Label_Referentinnen>' . "\n";
					$xml.= '		<Label_Schularten>' . $schulartenlabel . '</Label_Schularten>' . "\n";
					$xml.= '	</Label>' . "\n";
					$xml.= '	<Details>' . "\n";
					$xml.= '		<Veranstalter>'. $custom['_training_det_orgname'][0] . '</Veranstalter>' . "\n";
					$xml.= '		<Veranstatler_Weitere></Veranstatler_Weitere>' . "\n";
					$xml.= '		<Kontakt_Name>'. $custom['_training_con_name'][0] . '</Kontakt_Name>' . "\n";
					$xml.= '		<Kontakt_Telefon>'. $custom['_training_con_phone'][0] . '</Kontakt_Telefon>' . "\n";
					$xml.= '		<Kontakt_E_Mail>'. $custom['_training_con_email'][0] . '</Kontakt_E_Mail>' . "\n";
					$xml.= '		<VOrt_Raum>'. $custom['_training_loc_room'][0] . '</VOrt_Raum>' . "\n";
					$xml.= '		<VOrt_Name>'. $custom['_training_loc_name'][0] . '</VOrt_Name>' . "\n";
					$xml.= '		<VOrt_Strasse>'. $custom['_training_loc_street'][0] . '</VOrt_Strasse>' . "\n";
					$xml.= '		<VOrt_PLZ>'. $custom['_training_loc_postalcode'][0] . '</VOrt_PLZ>' . "\n";
					$xml.= '		<VOrt_Ort>'. $custom['_training_loc_city'][0] . '</VOrt_Ort>' . "\n";
					$xml.= '		<Termin_1_Datum>'. get_date_from_value($custom['_training_day_1'][0], true, false, $dateformat, $timeformat) . '</Termin_1_Datum>' . "\n";
					$xml.= '		<Termin_1_Start>'. get_date_from_value($custom['_training_day_1'][0], false, true, $dateformat, $timeformat) . '</Termin_1_Start>' . "\n";
					$xml.= '		<Termin_1_Ende>'. get_date_from_value($custom['_training_day_1_end'][0], false, true, $dateformat, $timeformat) . '</Termin_1_Ende>' . "\n";
					if ( isset($custom['_training_day_2'][0]) ) { $xml.= '		<Termin_2_Datum>'. get_date_from_value($custom['_training_day_2'][0], true, false, $dateformat, $timeformat) . '</Termin_2_Datum>' . "\n"; } else {$xml.= '		<Termin_2_Datum></Termin_2_Datum>' . "\n"; }
					if ( isset($custom['_training_day_2'][0]) ) { $xml.= '		<Termin_2_Start>'. get_date_from_value($custom['_training_day_2'][0], false, true, $dateformat, $timeformat) . '</Termin_2_Start>' . "\n"; } else {$xml.= '		<Termin_2_Start></Termin_2_Start>' . "\n"; }
					if ( isset($custom['_training_day_2'][0]) ) { $xml.= '		<Termin_2_Ende>'. get_date_from_value($custom['_training_day_2_end'][0], false, true, $dateformat, $timeformat) . '</Termin_2_Ende>' . "\n"; } else {$xml.= '		<Termin_2_Ende></Termin_2_Ende>' . "\n"; }
					if ( isset($custom['_training_day_3'][0]) ) { $xml.= '		<Termin_3_Datum>'. get_date_from_value($custom['_training_day_3'][0], true, false, $dateformat, $timeformat) . '</Termin_3_Datum>' . "\n"; } else {$xml.= '		<Termin_3_Datum></Termin_3_Datum>' . "\n"; }
					if ( isset($custom['_training_day_3'][0]) ) { $xml.= '		<Termin_3_Start>'. get_date_from_value($custom['_training_day_3'][0], false, true, $dateformat, $timeformat) . '</Termin_3_Start>' . "\n"; } else {$xml.= '		<Termin_3_Start></Termin_3_Start>' . "\n"; }
					if ( isset($custom['_training_day_3'][0]) ) { $xml.= '		<Termin_3_Ende>'. get_date_from_value($custom['_training_day_3_end'][0], false, true, $dateformat, $timeformat) . '</Termin_3_Ende>' . "\n"; } else {$xml.= '		<Termin_3_Ende></Termin_3_Ende>' . "\n"; }
					if ( isset($custom['_training_day_4'][0]) ) { $xml.= '		<Termin_4_Datum>'. get_date_from_value($custom['_training_day_4'][0], true, false, $dateformat, $timeformat) . '</Termin_4_Datum>' . "\n"; } else {$xml.= '		<Termin_4_Datum></Termin_4_Datum>' . "\n"; }
					if ( isset($custom['_training_day_4'][0]) ) { $xml.= '		<Termin_4_Start>'. get_date_from_value($custom['_training_day_4'][0], false, true, $dateformat, $timeformat) . '</Termin_4_Start>' . "\n"; } else {$xml.= '		<Termin_4_Start></Termin_4_Start>' . "\n"; }
					if ( isset($custom['_training_day_4'][0]) ) { $xml.= '		<Termin_4_Ende>'. get_date_from_value($custom['_training_day_4_end'][0], false, true, $dateformat, $timeformat) . '</Termin_4_Ende>' . "\n"; } else {$xml.= '		<Termin_4_Ende></Termin_4_Ende>' . "\n"; }
					$xml.= '		<Referentinnen>'. $custom['_training_det_referee'][0] . '</Referentinnen>' . "\n";
					$xml.= '		<Preis>'. $custom['_training_det_fees'][0] . '</Preis>' . "\n";
					$xml.= '		<Aktenzeichen>'. $custom['_training_det_fibs_tag'][0] . '</Aktenzeichen>' . "\n";
					$xml.= '		<Schularten>' . $schularten . '</Schularten>' . "\n";
					$xml.= '		<Anmeldeschluss>'. get_date_from_value( $custom['_training_day_deadline'][0] , true, false, $dateformat, $timeformat) . '</Anmeldeschluss>' . "\n";
					$xml.= '	</Details>' . "\n";
					$content = apply_filters( 'the_content', get_the_content() );
					$content = str_replace( ']]>', ']]&gt;', $content );
					$xml.= '	<Beschreibung>'. strip_tags ( $content ) . '</Beschreibung>' . "\n";
					$link = str_replace( 'http://', '', $custom['_training_det_url'][0] );
					$link = str_replace( 'https://', '', $link );
					$xml.= '	<Link>' . $link . '</Link>' . "\n";
					$xml.= '</Veranstaltung>' . "\n";
					$countbig ++;
				}

			}
		}
	}
		if ( $countbig > 0 ) { 
			$xmlc.= '<!-- Summe Groß: ' . $countbig . '-->'; $totalpages = $totalpages + ceil($countbig/2); $infotext.= $countbig . ' Veranstaltungen auf ' . ceil($countbig/2) .' Seiten';$countbig = 0;
		}
		if ( $countsmall > 0 ) {
			$xmlc.= '<!-- Summe Kurz: ' . $countsmall . '-->'; $totalpages = $totalpages + ceil($countsmall/7); $infotext.= 'mit ' .$countsmall . ' Veranstaltungen auf '. ceil($countsmall/7) .' Seiten';$countsmall = 0;
		}

		$xml.= '</Fortbildungen>';
		$xmlscdline.= '<!--' . "\n" . 'Zusammenfassung' . "\n" . '############################' . $infotext . ' ' . "\n" . '############################' . "\n" . 'Insgesamt ' . $totalpages .' Seiten' . "\n" . '############################' . "\n" . 'Diese Datei ist eine für die Erstellung von inDesign-Printmedien optimierte XML-Version der Fortbildungen. Es müssen nicht zwangsläufig alle Veranstaltungen welche oben aufgelistet sind in der Datei enthalten sein (Backend, Veranstaltung wird gedruckt).'.  "\n" . '############################' . "\n" . '-->' . "\n";
		$xml = $xmlfstline . $xmlscdline . $xml;

		require_once(ABSPATH . '/wp-admin/includes/file.php');
		global $wp_filesystem;
		WP_Filesystem();
		$uploads = wp_upload_dir();
		$uploads_dir = trailingslashit($uploads['basedir']);

		$file = $uploads_dir . '/' . 'fortbildungen' . '.xml';
						if (!file_exists($file)) {
							if (!$wp_filesystem->put_contents($file, $xml, FS_CHMOD_FILE)) {
								return new WP_Error('writing_error', 'Error when writing file'); //return error object
							}
						} else {
							if (!$wp_filesystem->put_contents($file, $xml, FS_CHMOD_FILE)) {
								return new WP_Error('writing_error', 'Error when writing file'); //return error object
							}
						}
						
						$upload_dir = wp_upload_dir();
						return '<a href="' . $upload_dir['url'] .'/'.'fortbildungen.xml' . '" title="Fortbildungen als .xml für inDesign"><i class="fa fa-file-code-o"></i> xml</a>';
}
add_shortcode('fbpxml', 'training_xml_generator');

?>