<?php
if(!defined('sugarEntry') || !sugarEntry) die('Not A Valid Entry Point');

$dictionary['rt_ga_visitors'] = array(
	'table' => 'rt_ga_visitors',
);
$dictionary['rt_ga_visitors']['fields'] = array(
	'id' => array(
		'name' => 'id',
		'vname' => 'LBL_ID',
		'type' => 'varchar',
		'len' => '36',
	),
	'deleted' => array(
		'name' => 'deleted',
		'vname' => 'LBL_DELETED',
		'type' => 'bool',
		'default' => '0',
		'reportable' => false,
		'comment' => 'Record deletion indicator',
	),
	'_cid' => array(
		'name' => '_cid',
		'vname' => 'LBL_GCID',
		'type' => 'varchar',
		'len' => '255',
	),
	'userType' => array(
		'name' => 'userType',
		'vname' => 'LBL_GUSER_TYPE',
		'type' => 'varchar',
		'len' => '255',
	),
	'noOfVisits' => array(
		'name' => 'noOfVisits',
		'vname' => 'LBL_GNO_OF_VISITS',
		'type' => 'varchar',
		'len' => '255',
	),
	'fullReferrer' => array(
		'name' => 'fullReferrer',
		'vname' => 'LBL_GFULL_REFERRER',
		'type' => 'varchar',
		'len' => '255',
	),
	'socialNetwork' => array(
		'name' => 'socialNetwork',
		'vname' => 'LBL_GSOCIAL_NETWORK',
		'type' => 'varchar',
		'len' => '255',
	),
	'hasSocialSourceReferral' => array(
		'name' => 'hasSocialSourceReferral',
		'vname' => 'LBL_GHAS_SOCIAL_SOURCE_REFERRAL',
		'type' => 'varchar',
		'len' => '255',
	),
	'country' => array(
		'name' => 'country',
		'vname' => 'LBL_GCOUNTRY',
		'type' => 'varchar',
		'len' => '255',
	),
	'city' => array(
		'name' => 'city',
		'vname' => 'LBL_GCITY',
		'type' => 'varchar',
		'len' => '255',
	),
	'browser' => array(
		'name' => 'browser',
		'vname' => 'LBL_GBROWSER',
		'type' => 'varchar',
		'len' => '255',
	),
	'browserVersion' => array(
		'name' => 'browserVersion',
		'vname' => 'LBL_GBROWSER_VERSION',
		'type' => 'varchar',
		'len' => '255',
	),
	'operatingSystem' => array(
		'name' => 'operatingSystem',
		'vname' => 'LBL_GOS',
		'type' => 'varchar',
		'len' => '255',
	),
	'operatingSystemVersion' => array(
		'name' => 'operatingSystemVersion',
		'vname' => 'LBL_GOS_VERSION',
		'type' => 'varchar',
		'len' => '255',
	),
	'deviceCategory' => array(
		'name' => 'deviceCategory',
		'vname' => 'LBL_GDEVICE_CATEGORY',
		'type' => 'varchar',
		'len' => '255',
	),
	'language' => array(
		'name' => 'language',
		'vname' => 'LBL_GLANGUAGE',
		'type' => 'varchar',
		'len' => '255',
	),
	'userAgeBracket' => array(
		'name' => 'userAgeBracket',
		'vname' => 'LBL_GUSER_AGE_BRACKET',
		'type' => 'varchar',
		'len' => '255',
	),
	'userGender' => array(
		'name' => 'userGender',
		'vname' => 'LBL_GUSER_GENDER',
		'type' => 'varchar',
		'len' => '255',
	),
);

$dictionary['rt_ga_visitors']['indices'] = array(
	'id' => array(
		'name' => 'rt_ga_visitorspk',
		'type' => 'primary',
		'fields' => array(
			0 => 'id',
		),
	),
	'_cid' => array(
		'name' => '_cid',
		'type' => 'index',
		'fields' => array(
			0 => '_cid',
		),
	),
);