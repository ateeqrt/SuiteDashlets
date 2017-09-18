<?php
$dictionary['rt_cxm_notif']['fields']['rt_tracker_notifications_n'] = array(
    'name' => 'rt_tracker_notifications_n',
    'type' => 'link',
    'relationship' => 'rt_tracker_notifications_n',
    'module' => 'rt_Tracker',
    'bean_name' => 'rt_Tracker',
    'source' => 'non-db',
    'vname' => 'rt_cxm_notif',
    'id_name' => 'cookie_id_c',
    'link-type' => 'one',
    'side' => 'right',
);
$dictionary['rt_cxm_notif']['fields']['link_id'] = array(
    'name' => 'link_id',
    'type' => 'text',
    'source' => 'non-db',
    'vname' => 'LBL_LINK_ID',
    'save' => true,
    'id_name' => 'cookie_id_c',
    'link' => 'rt_tracker_notifications_n',
    'table' => 'rt_tracker',
    'module' => 'rt_Tracker',
    'rname' => 'parent_id',
);
$dictionary['rt_cxm_notif']['fields']['link_module'] = array(
    'name' => 'link_module',
    'type' => 'text',
    'source' => 'non-db',
    'vname' => 'LBL_LINK_MODULE',
    'save' => true,
    'id_name' => 'cookie_id_c',
    'link' => 'rt_tracker_notifications_n',
    'table' => 'rt_tracker',
    'module' => 'rt_Tracker',
    'rname' => 'parent_type',
);