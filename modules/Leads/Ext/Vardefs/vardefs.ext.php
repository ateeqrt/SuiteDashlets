<?php 
 //WARNING: The contents of this file are auto-generated


 // created: 2017-08-17 14:42:01
$dictionary['Lead']['fields']['jjwg_maps_lng_c']['inline_edit']=1;

 

$dictionary['Lead']['fields']['generic_flag'] = array(
    'name' => 'generic_flag',
    'type' => 'relate',
    'source' => 'non-db',
    'vname' => 'LBL_CXM_GENERAL',
    'save' => true,
    'id_name' => 'rt_cxm_email_leads_1rt_cxm_email_ida',
    'link' => 'rt_cxm_email_leads_1',
    'table' => 'rt_cxm_email',
    'module' => 'rt_cxm_email',
    'rname' => 'gflag',
);
$dictionary['Lead']['fields']['facebook_flag'] = array(
    'name' => 'facebook_flag',
    'type' => 'relate',
    'source' => 'non-db',
    'vname' => 'LBL_CXM_FACEBOOK',
    'save' => true,
    'id_name' => 'rt_cxm_email_leads_1rt_cxm_email_ida',
    'link' => 'rt_cxm_email_leads_1',
    'table' => 'rt_cxm_email',
    'module' => 'rt_cxm_email',
    'rname' => 'fflag',
);
$dictionary['Lead']['fields']['google_flag'] = array(
    'name' => 'google_flag',
    'type' => 'relate',
    'source' => 'non-db',
    'vname' => 'LBL_CXM_GOOGLE',
    'save' => true,
    'id_name' => 'rt_cxm_email_leads_1rt_cxm_email_ida',
    'link' => 'rt_cxm_email_leads_1',
    'table' => 'rt_cxm_email',
    'module' => 'rt_cxm_email',
    'rname' => 'gpflag',
);
$dictionary['Lead']['fields']['twitter_flag'] = array(
    'name' => 'twitter_flag',
    'type' => 'relate',
    'source' => 'non-db',
    'vname' => 'LBL_CXM_TWITTER',
    'save' => true,
    'id_name' => 'rt_cxm_email_leads_1rt_cxm_email_ida',
    'link' => 'rt_cxm_email_leads_1',
    'table' => 'rt_cxm_email',
    'module' => 'rt_cxm_email',
    'rname' => 'tflag',
);
$dictionary['Lead']['fields']['linkedin_flag'] = array(
    'name' => 'linkedin_flag',
    'type' => 'relate',
    'source' => 'non-db',
    'vname' => 'LBL_CXM_LINKEDIN',
    'save' => true,
    'id_name' => 'rt_cxm_email_leads_1rt_cxm_email_ida',
    'link' => 'rt_cxm_email_leads_1',
    'table' => 'rt_cxm_email',
    'module' => 'rt_cxm_email',
    'rname' => 'lflag',
);

 // created: 2017-08-17 14:42:02
$dictionary['Lead']['fields']['jjwg_maps_geocode_status_c']['inline_edit']=1;

 


$dictionary['Lead']['fields']['maps_lat'] = array(
    'name' => 'maps_lat',
    'vname' => 'LBL_LATTITUDE',
    'type' => 'varchar',
    'len' => '255',
    'audited' => false,
    'required' => false,
    'comment' => ''
);
$dictionary['Lead']['fields']['maps_long'] = array(
    'name' => 'maps_long',
    'vname' => 'LBL_LONGITUDE',
    'type' => 'varchar',
    'len' => '255',
    'audited' => false,
    'required' => false,
    'comment' => ''
);
$dictionary["Lead"]["fields"]["location_marker2"] = array(
    'name' => 'location_marker2',
    'vname' => 'LBL_LOCATION_MARKER',
    'type' => 'varchar',
    'len' => '150',
    'source' => 'non-db',
);
$dictionary["Lead"]["fields"]["non_geo_coded_address"] = array(
    'name' => 'non_geo_coded_address',
    'vname' => 'LBL_NON_GEO_CODING_ADDRESS',
    'type' => 'varchar',
    'len' => '100',
    'default_value' => '',
    'readonly' => true,
);


//
$dictionary['Lead']['fields']['rt_tracker_leads_n'] = array(
    'name' => 'rt_tracker_leads_n',
    'type' => 'link',
    'relationship' => 'rt_tracker_leads_n',
    'module' => 'rt_Tracker',
    'bean_name' => 'rt_Tracker',
    'source' => 'non-db',
    'vname' => 'Leads',
    'id_name' => 'parent_id',
    'link-type' => 'many',
    'side' => 'left',
);

$dictionary['Lead']['relationships']['rt_tracker_leads_n'] = array(
    'lhs_module' => 'Leads',
    'lhs_table' => 'leads',
    'lhs_key' => 'id',
    'rhs_module' => 'rt_Tracker',
    'rhs_table' => 'rt_tracker',
    'rhs_key' => 'parent_id',
    'relationship_type' => 'one-to-many',
);

 // created: 2017-08-17 14:42:02
$dictionary['Lead']['fields']['jjwg_maps_lat_c']['inline_edit']=1;

 

 // created: 2017-08-17 14:42:02
$dictionary['Lead']['fields']['jjwg_maps_address_c']['inline_edit']=1;

 

// created: 2016-03-15 11:32:27
$dictionary["Lead"]["fields"]["rt_cxm_email_leads_1"] = array(
    'name' => 'rt_cxm_email_leads_1',
    'type' => 'link',
    'relationship' => 'rt_cxm_email_leads_1',
    'source' => 'non-db',
    'module' => 'rt_cxm_email',
    'bean_name' => 'rt_cxm_email',
    'side' => 'right',
    'vname' => 'Leads',
    'id_name' => 'rt_cxm_email_leads_1rt_cxm_email_ida',
    'link-type' => 'one',
);
$dictionary["Lead"]["fields"]["rt_cxm_email_leads_1_name"] = array(
    'name' => 'rt_cxm_email_leads_1_name',
    'type' => 'relate',
    'source' => 'non-db',
    'vname' => 'cxm_emails',
    'save' => true,
    'id_name' => 'rt_cxm_email_leads_1rt_cxm_email_ida',
    'link' => 'rt_cxm_email_leads_1',
    'table' => 'rt_cxm_email',
    'module' => 'rt_cxm_email',
    'rname' => 'name',
);
$dictionary["Lead"]["fields"]["rt_cxm_email_leads_1rt_cxm_email_ida"] = array(
    'name' => 'rt_cxm_email_leads_1rt_cxm_email_ida',
    'type' => 'id',
    'source' => 'non-db',
    'vname' => 'cxm_emails ID',
    'id_name' => 'rt_cxm_email_leads_1rt_cxm_email_ida',
    'link' => 'rt_cxm_email_leads_1',
    'table' => 'rt_cxm_email',
    'module' => 'rt_cxm_email',
    'rname' => 'id',
    'reportable' => false,
    'side' => 'right',
    'massupdate' => false,
    'duplicate_merge' => 'disabled',
    'hideacl' => true,
);

?>