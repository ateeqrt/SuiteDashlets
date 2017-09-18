<?php

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
