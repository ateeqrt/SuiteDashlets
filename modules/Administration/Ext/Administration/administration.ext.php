<?php 
 //WARNING: The contents of this file are auto-generated



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


$admin_option_defs = array();
$admin_option_defs['Administration']['rtsalesmapsettings'] = array(
    'User Settings',
    'LBL_SALESMAP_USERSETTINGS_TITLE',
    'LBL_SALESMAP_USERSETTINGS_DESC',
    './index.php?module=rt_maps&action=usersettings'
);
$admin_option_defs['Administration']['rtsalesmap_googleAPI'] = array(
    'Google API Key',
    'LBL_SALESMAP_API_TITLE',
    'LBL_SALESMAP_API_DESC',
    './index.php?module=rt_maps&action=configuregapi'
);
$admin_group_header[] = array('LBL_RTSALESMAPADDON', '', false, $admin_option_defs, 'LBL_RTSALESMAPDESC');

?>