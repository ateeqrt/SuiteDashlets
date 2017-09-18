<?php 
 //WARNING: The contents of this file are auto-generated


 // created: 2017-08-17 14:42:01
$dictionary['Contact']['fields']['jjwg_maps_lng_c']['inline_edit']=1;

 

$dictionary['Contact']['fields']['generic_flag'] = array(
    'name' => 'generic_flag',
    'type' => 'relate',
    'source' => 'non-db',
    'vname' => 'General',
    'save' => true,
    'id_name' => 'rt_cxm_email_contacts_1rt_cxm_email_ida',
    'link' => 'rt_cxm_email_contacts_1',
    'table' => 'rt_cxm_email',
    'module' => 'rt_cxm_email',
    'rname' => 'gflag',
);
$dictionary['Contact']['fields']['facebook_flag'] = array(
    'name' => 'facebook_flag',
    'type' => 'relate',
    'source' => 'non-db',
    'vname' => 'Facebook',
    'save' => true,
    'id_name' => 'rt_cxm_email_contacts_1rt_cxm_email_ida',
    'link' => 'rt_cxm_email_contacts_1',
    'table' => 'rt_cxm_email',
    'module' => 'rt_cxm_email',
    'rname' => 'fflag',
);
$dictionary['Contact']['fields']['google_flag'] = array(
    'name' => 'google_flag',
    'type' => 'relate',
    'source' => 'non-db',
    'vname' => 'Google',
    'save' => true,
    'id_name' => 'rt_cxm_email_contacts_1rt_cxm_email_ida',
    'link' => 'rt_cxm_email_contacts_1',
    'table' => 'rt_cxm_email',
    'module' => 'rt_cxm_email',
    'rname' => 'gpflag',
);
$dictionary['Contact']['fields']['twitter_flag'] = array(
    'name' => 'twitter_flag',
    'type' => 'relate',
    'source' => 'non-db',
    'vname' => 'Twitter',
    'save' => true,
    'id_name' => 'rt_cxm_email_contacts_1rt_cxm_email_ida',
    'link' => 'rt_cxm_email_contacts_1',
    'table' => 'rt_cxm_email',
    'module' => 'rt_cxm_email',
    'rname' => 'tflag',
);
$dictionary['Contact']['fields']['linkedin_flag'] = array(
    'name' => 'linkedin_flag',
    'type' => 'relate',
    'source' => 'non-db',
    'vname' => 'Linkedin',
    'save' => true,
    'id_name' => 'rt_cxm_email_contacts_1rt_cxm_email_ida',
    'link' => 'rt_cxm_email_contacts_1',
    'table' => 'rt_cxm_email',
    'module' => 'rt_cxm_email',
    'rname' => 'lflag',
);


 // created: 2017-08-17 14:42:01
$dictionary['Contact']['fields']['jjwg_maps_geocode_status_c']['inline_edit']=1;

 


$dictionary['Contact']['fields']['maps_lat'] = array(
    'name' => 'maps_lat',
    'vname' => 'LBL_LATTITUDE',
    'type' => 'varchar',
    'len' => '255',
    'audited' => false,
    'required' => false,
    'comment' => ''
);
$dictionary['Contact']['fields']['maps_long'] = array(
    'name' => 'maps_long',
    'vname' => 'LBL_LONGITUDE',
    'type' => 'varchar',
    'len' => '255',
    'audited' => false,
    'required' => false,
    'comment' => ''
);

$dictionary["Contact"]["fields"]["location_marker2"] = array(
    'name' => 'location_marker2',
    'vname' => 'LBL_LOCATION_MARKER',
    'type' => 'varchar',
    'len' => '150',
    'source' => 'non-db',
);
$dictionary["Contact"]["fields"]["non_geo_coded_address"] = array(
    'name' => 'non_geo_coded_address',
    'vname' => 'LBL_NON_GEO_CODING_ADDRESS',
    'type' => 'varchar',
    'len' => '100',
    'default_value' => '',
    'readonly' => true,
);


 // created: 2017-08-17 14:42:01
$dictionary['Contact']['fields']['jjwg_maps_lat_c']['inline_edit']=1;

 

// created: 2016-04-21 08:37:11
$dictionary["Contact"]["fields"]["rt_cxm_email_contacts_1"] = array(
    'name' => 'rt_cxm_email_contacts_1',
    'type' => 'link',
    'relationship' => 'rt_cxm_email_contacts_1',
    'source' => 'non-db',
    'module' => 'rt_cxm_email',
    'bean_name' => 'rt_cxm_email',
    'side' => 'right',
    'vname' => 'Contacts',
    'id_name' => 'rt_cxm_email_contacts_1rt_cxm_email_ida',
    'link-type' => 'one',
);
$dictionary["Contact"]["fields"]["rt_cxm_email_contacts_1_name"] = array(
    'name' => 'rt_cxm_email_contacts_1_name',
    'type' => 'relate',
    'source' => 'non-db',
    'vname' => 'cxm_emails',
    'save' => true,
    'id_name' => 'rt_cxm_email_contacts_1rt_cxm_email_ida',
    'link' => 'rt_cxm_email_contacts_1',
    'table' => 'rt_cxm_email',
    'module' => 'rt_cxm_email',
    'rname' => 'name',
);
$dictionary["Contact"]["fields"]["rt_cxm_email_contacts_1rt_cxm_email_ida"] = array(
    'name' => 'rt_cxm_email_contacts_1rt_cxm_email_ida',
    'type' => 'id',
    'source' => 'non-db',
    'vname' => 'cxm_emails ID',
    'id_name' => 'rt_cxm_email_contacts_1rt_cxm_email_ida',
    'link' => 'rt_cxm_email_contacts_1',
    'table' => 'rt_cxm_email',
    'module' => 'rt_cxm_email',
    'rname' => 'id',
    'reportable' => false,
    'side' => 'right',
    'massupdate' => false,
    'duplicate_merge' => 'disabled',
    'hideacl' => true,
);



$dictionary['Contact']['fields']['rt_tracker_contacts_n'] = array(
    'name' => 'rt_tracker_contacts_n',
    'type' => 'link',
    'relationship' => 'rt_tracker_contacts_n',
    'module' => 'rt_Tracker',
    'bean_name' => 'rt_Tracker',
    'source' => 'non-db',
    'vname' => 'Contacts',
    'id_name' => 'parent_id',
    'link-type' => 'many',
    'side' => 'left',
);

$dictionary['Contact']['relationships']['rt_tracker_contacts_n'] = array(
    'lhs_module' => 'Contacts',
    'lhs_table' => 'contacts',
    'lhs_key' => 'id',
    'rhs_module' => 'rt_Tracker',
    'rhs_table' => 'rt_tracker',
    'rhs_key' => 'parent_id',
    'relationship_type' => 'one-to-many',
);


 // created: 2017-08-17 14:42:01
$dictionary['Contact']['fields']['jjwg_maps_address_c']['inline_edit']=1;

 
?>