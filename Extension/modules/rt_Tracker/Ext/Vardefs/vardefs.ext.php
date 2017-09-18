<?php
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