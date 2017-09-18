<?php 
 //WARNING: The contents of this file are auto-generated


$dictionary['rt_Tracker']['fields']['rt_tracker_notifications_n'] = array(
    'name'          => 'rt_tracker_notifications_n',
    'type'          => 'link',
    'relationship'  => 'rt_tracker_notifications_n',
    'module'        => 'rt_Tracker',
    'bean_name'     => 'rt_Tracker',
    'source'        => 'non-db',
    'vname'         => 'rt_cxm_notif',
    'id_name'       => 'cookie_id_c',
    'link-type'     => 'many',
    'side'          => 'left',
);

$dictionary['rt_Tracker']['relationships']['rt_tracker_notifications_n'] = array(
    'lhs_module'        => 'rt_Tracker',
    'lhs_table'         => 'rt_tracker',
    'lhs_key'           => 'cookie_id_c',
    'rhs_module'        => 'rt_cxm_notif',
    'rhs_table'         => 'rt_cxm_notif',
    'rhs_key'           => 'cookie_id',
    'relationship_type' => 'one-to-many',
);


$dictionary['rt_Tracker']['fields']['rt_tracker_contacts_n'] = array(
    'name'          => 'rt_tracker_contacts_n',
    'type'          => 'link',
    'relationship'  => 'rt_tracker_contacts_n',
    'module'        => 'Contacts',
    'bean_name'     => 'Contact',
    'source'        => 'non-db',
    'vname'         => 'rt_Trackers',
    'id_name'       => 'parent_id',
    'link-type'     => 'one',
    'side'          => 'right',
);

$dictionary['rt_Tracker']['fields']['parent_type'] = array(
    'name'      => 'parent_type',
    'vname'     => 'Parent Type:',
    'type'      => 'parent_type',
    'dbType'    => 'varchar',
    'group'     => 'parent_name',
    'options'   => 'parent_type_display',
    'required'  => false,
    'len'       => '255',
    'comment'   => 'The Sugar object to which the call is related',
    'studio'    => array('wirelesslistview' => false),
    'options'   => 'parent_type_display',
);
$dictionary['rt_Tracker']['fields']['parent_name'] = array(
    'name'          => 'parent_name',
    'parent_type'   => 'record_type_display',
    'type_name'     => 'parent_type',
    'id_name'       => 'parent_id',
    'vname'         => 'Related to',
    'type'          => 'parent',
    'group'         => 'parent_name',
    'source'        => 'non-db',
    'options'       => 'parent_type_display',
    'studio'        => true,
);
$dictionary['rt_Tracker']['fields']['parent_id'] = array(
    'name'          => 'parent_id',
    'type'          => 'id',
    'group'         => 'parent_name',
    'reportable'    => false,
    'vname'         => 'Parent ID:',
);

$dictionary['rt_Tracker']['fields']['lead_name'] = array(
    'required'      => false,
    'source'        => 'non-db',
    'name'          => 'lead_name',
    'vname'         => 'Leads Name',
    'type'          => 'relate',
    'rname'         => 'name',
    'id_name'       => 'parent_id',
    'join_name'     => 'leads',
    'link'          => 'rt_tracker_leads_n',
    'table'         => 'leads',
    'isnull'        => 'true',
    'module'        => 'Leads',
);

$dictionary['rt_Tracker']['fields']['rt_tracker_leads_n'] = array(
    'name'          => 'rt_tracker_leads_n',
    'type'          => 'link',
    'relationship'  => 'rt_tracker_leads_n',
    'module'        => 'Leads',
    'bean_name'     => 'Lead',
    'source'        => 'non-db',
    'vname'         => 'rt_Trackers',
    'id_name'       => 'parent_id',
    'link-type'     => 'one',
    'side'          => 'right',
);
?>