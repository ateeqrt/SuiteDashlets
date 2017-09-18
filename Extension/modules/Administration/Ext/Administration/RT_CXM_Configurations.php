<?php

// License view in admin
$admin_option_defs = array();
$admin_option_defs['Administration']['rt_cxm_license_view'] = array(
    'rt_Tracker',
    'LBL_RT_TRACKER_LICENSE',
    'LBL_RT_TRACKER_LICENSE_DESC',
    'javascript:parent.SUGAR.App.router.navigate("rt_Tracker/layout/license", {trigger: true});'
);
$admin_group_header[] = array(
    'RT CXM',
    '',
    false,
    $admin_option_defs,
    ''
);