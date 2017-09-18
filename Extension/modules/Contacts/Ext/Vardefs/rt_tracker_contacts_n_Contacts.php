<?php

$dictionary['Contact']['fields']['rt_tracker_contacts_n'] = array(
    'name' => 'rt_tracker_contacts_n',
    'type' => 'link',
    'relationship' => 'rt_tracker_contacts_n',
    'module' => 'rt_Tracker',
    'bean_name' => 'rt_Tracker',
    'source' => 'non-db',
    'vname' => 'Contacts',
    'id_name' => 'parent_id',
    'link-type' => 'many',
    'side' => 'left',
);

$dictionary['Contact']['relationships']['rt_tracker_contacts_n'] = array(
    'lhs_module' => 'Contacts',
    'lhs_table' => 'contacts',
    'lhs_key' => 'id',
    'rhs_module' => 'rt_Tracker',
    'rhs_table' => 'rt_tracker',
    'rhs_key' => 'parent_id',
    'relationship_type' => 'one-to-many',
);
