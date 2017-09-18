<?php
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