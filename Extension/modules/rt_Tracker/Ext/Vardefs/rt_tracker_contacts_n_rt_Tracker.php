<?php

$dictionary['rt_Tracker']['fields']['rt_tracker_contacts_n'] = array(
    'name'          => 'rt_tracker_contacts_n',
    'type'          => 'link',
    'relationship'  => 'rt_tracker_contacts_n',
    'module'        => 'Contacts',
    'bean_name'     => 'Contact',
    'source'        => 'non-db',
    'vname'         => 'rt_Trackers',
    'id_name'       => 'parent_id',
    'link-type'     => 'one',
    'side'          => 'right',
);