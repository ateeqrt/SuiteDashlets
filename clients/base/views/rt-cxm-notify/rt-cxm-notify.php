<?php
if (!defined('sugarEntry')) define('sugarEntry', true);

$viewdefs['base']['view']['rt-cxm-notify'] = array(
    'dashlets' => array(
        array(
            //Display label for this dashlet
            'label' => 'LBL_CXM_NOTIFICATIONS',
            //Description label for this Dashlet
            'description' => 'LBL_CXM_NOTIFICATIONS_DESC',
            'config' => array(),
            'preview' => array(),
            //Filter array decides where this dashlet is allowed to appear
            'filter' => array(
                //Modules where this dashlet can appear
                'module' => array(
                    'Home',
                ),
                //Views where this dashlet can appear
                'view' => array(
                    'record',
                )
            )
        ),
    ),
    'panels' => array(
        array(
            'name' => 'dashlet_settings',
            'columns' => 2,
            'labelsOnTop' => true,
            'placeholders' => true,
            'fields' => array(
                array(
                    'name' => 'auto_refresh',
                    'label' => 'LBL_AUTO_REFRESH',
                    'type' => 'enum',
                    'options' => 'sugar7_notifications_dashlet_auto_refresh_options',
                ),
            ),
        ),
    ),
    'custom_toolbar' => array(
        'buttons' => array(
            array(
                "type" => "dashletaction",
                "css_class" => "dashlet-toggle btn btn-invisible minify",
                "icon" => "fa-chevron-up",
                "action" => "toggleMinify",
                "tooltip" => "LBL_DASHLET_TOGGLE",
            ),
            array(
                'dropdown_buttons' => array(
                    array(
                        'type' => 'dashletaction',
                        'action' => 'editClicked',
                        'label' => 'LBL_DASHLET_CONFIG_EDIT_LABEL',
                    ),
                    array(
                        'type' => 'dashletaction',
                        'action' => 'customRefreshClicked',
                        'label' => 'LBL_DASHLET_REFRESH_LABEL',
                    ),
                    array(
                        'type' => 'dashletaction',
                        'action' => 'removeClicked',
                        'label' => 'LBL_DASHLET_REMOVE_LABEL',
                    ),
                ),
            ),
        ),
    ),
);