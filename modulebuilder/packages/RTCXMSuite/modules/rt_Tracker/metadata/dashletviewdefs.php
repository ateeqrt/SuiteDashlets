<?php
$dashletData['rtcxm_rt_TrackerDashlet']['searchFields'] = array (
  'date_entered' => 
  array (
    'default' => '',
  ),
  'date_modified' => 
  array (
    'default' => '',
  ),
  'assigned_user_id' => 
  array (
    'type' => 'assigned_user_name',
    'default' => 'Administrator',
  ),
);
$dashletData['rtcxm_rt_TrackerDashlet']['columns'] = array (
  'name' => 
  array (
    'width' => '40%',
    'label' => 'LBL_LIST_NAME',
    'link' => true,
    'default' => true,
    'name' => 'name',
  ),
  'date_entered' => 
  array (
    'width' => '15%',
    'label' => 'LBL_DATE_ENTERED',
    'default' => true,
    'name' => 'date_entered',
  ),
  'date_modified' => 
  array (
    'width' => '15%',
    'label' => 'LBL_DATE_MODIFIED',
    'name' => 'date_modified',
    'default' => false,
  ),
  'created_by' => 
  array (
    'width' => '8%',
    'label' => 'LBL_CREATED',
    'name' => 'created_by',
    'default' => false,
  ),
  'assigned_user_name' => 
  array (
    'width' => '8%',
    'label' => 'LBL_LIST_ASSIGNED_USER',
    'name' => 'assigned_user_name',
    'default' => false,
  ),
  'license_key' => 
  array (
    'type' => 'varchar',
    'label' => 'LBL_LICENSE_KEY',
    'width' => '10%',
    'default' => false,
  ),
  'track_record' => 
  array (
    'type' => 'text',
    'studio' => 'visible',
    'label' => 'LBL_TRACK_RECORD',
    'sortable' => false,
    'width' => '10%',
    'default' => false,
  ),
  'zfid' => 
  array (
    'type' => 'varchar',
    'label' => 'LBL_ZFID',
    'width' => '10%',
    'default' => false,
  ),
  'cookie_id' => 
  array (
    'type' => 'varchar',
    'label' => 'LBL_COOKIE_ID',
    'width' => '10%',
    'default' => false,
  ),
);
