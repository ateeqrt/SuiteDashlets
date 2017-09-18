<?php
//
$dictionary['Lead']['fields']['rt_tracker_leads_n'] = array(
    'name' => 'rt_tracker_leads_n',
    'type' => 'link',
    'relationship' => 'rt_tracker_leads_n',
    'module' => 'rt_Tracker',
    'bean_name' => 'rt_Tracker',
    'source' => 'non-db',
    'vname' => 'Leads',
    'id_name' => 'parent_id',
    'link-type' => 'many',
    'side' => 'left',
);

$dictionary['Lead']['relationships']['rt_tracker_leads_n'] = array(
    'lhs_module' => 'Leads',
    'lhs_table' => 'leads',
    'lhs_key' => 'id',
    'rhs_module' => 'rt_Tracker',
    'rhs_table' => 'rt_tracker',
    'rhs_key' => 'parent_id',
    'relationship_type' => 'one-to-many',
);