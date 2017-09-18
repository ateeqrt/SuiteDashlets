<?php

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
