<?php
/*
Plugin Name: Custom Fields Permalink
Plugin URI: http://tec.toi-planning.net/wp/custom-fields-permalink
Description: This plugin enable to make a permalink from custom field's value.
Author: toiplan
Version: 0.1.0.1
Author URI: http://tec.toi-planning.net/
*/

function _custom_fields_permalink_replace($post, $key, $or) {
	$value = get_post_meta($post->ID, $key, true);
	if ($value) {
		return $value;
	}

	switch ($or) {
	case 'post_id':
		return $post->ID;
	case 'postname':
		return $post->post_name;
	default:
		return '';
	}
}

function custom_fields_permalink_filter($permalink, $post) {
	return preg_replace(
		'/%cfp_([^%]*?)(_or_(postname|post_id))?%/e',
		'_custom_fields_permalink_replace($post, "$1", "$3")',
		$permalink
	);
}
add_filter('post_link', 'custom_fields_permalink_filter', 10, 2);



function custom_fields_permalink_post_rewrite_rules($value) {
	$keys = array_keys($value);
	$tmp = $value;

	$value = array();
	$len = sizeof($keys);
	for ($i = 0; $i < $len; $i++) {
		$k = $keys[$i];

		if (preg_match('/%cfp_([^%]*?)(_or_(postname|post_id))?%/', $k)) {
			$nk = preg_replace(
				'/%cfp_([^%]*?)(_or_(postname|post_id))?%/',
				'([^/]+)',
				$k
			);
			$value[$nk] = preg_replace(
				'/%cfp_([^%]*?)(_or_(postname|post_id))?%/',
				'cfpk=$1&cfpo=$3&cfp=',
				$tmp[$k]
			);
		}
		else {
			$value[$k] = $tmp[$k];
		}
	}

	return $value;
}
add_filter(
	'post_rewrite_rules', 'custom_fields_permalink_post_rewrite_rules', 10, 1
);



function custom_fields_permalink_query_vars($value) {
	array_push($value, 'cfp', 'cfpk', 'cfpo');

	return $value;
}
add_filter(
	'query_vars', 'custom_fields_permalink_query_vars', 10, 1
);



$GLOBALS['custom_fields_permalink_processing'] = false;
function custom_fields_permalink_request($value) {
	if ($value['cfp']) {
		$cfp = $value['cfp'];
		$cfpk = $value['cfpk'];
		$cfpo = $value['cfpo'];
		unset(
			$value['cfp'],
			$value['cfpk'],
			$value['cfpo']
		);
		
		global $wpdb;
		if (! $cfpk) {
			return;
		}

		$ids_meta = $wpdb->get_col($wpdb->prepare(
			"SELECT post_id FROM $wpdb->postmeta WHERE meta_value = %s AND meta_key = %s", $cfp, $cfpk
		));


		$col = '';
		if ($cfpo == 'postname') {
			$col = 'post_name';
		}
		else if ($cfpo == 'post_id') {
			$col = 'ID';
		}

		if ($col) {
			$ids_posts = $wpdb->get_col($wpdb->prepare(
				"SELECT ID FROM $wpdb->posts WHERE $col = %s", $cfp
			));
		}
		else {
			$ids_posts = array();
		}


		$ids = array_merge($ids_meta, $ids_posts);
		if ($ids) {
			$GLOBALS['custom_fields_permalink_processing'] = true;
			$value['post__in'] = $ids;
		}
		else {
			$GLOBALS['custom_fields_permalink_processing'] = false;
			$keys = array_keys($value);
			$value[$keys[0]] .= '/' . $cfp;
		}
	}

	return $value;
}
add_filter(
	'request', 'custom_fields_permalink_request', 10, 1
);



function custom_fields_permalink_wp() {
	if ($GLOBALS['custom_fields_permalink_processing']) {
		$GLOBALS['more'] = 1;
		$GLOBALS['single'] = 1;

		global $wp_query;
		$wp_query->is_single = true;
	}
}
add_filter(
	'wp', 'custom_fields_permalink_wp', 0, 0
);


?>
