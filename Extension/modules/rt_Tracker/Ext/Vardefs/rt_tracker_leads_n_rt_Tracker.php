<?php
$dictionary['rt_Tracker']['fields']['lead_name'] = array(
    'required'      => false,
    'source'        => 'non-db',
    'name'          => 'lead_name',
    'vname'         => 'Leads Name',
    'type'          => 'relate',
    'rname'         => 'name',
    'id_name'       => 'parent_id',
    'join_name'     => 'leads',
    'link'          => 'rt_tracker_leads_n',
    'table'         => 'leads',
    'isnull'        => 'true',
    'module'        => 'Leads',
);

$dictionary['rt_Tracker']['fields']['rt_tracker_leads_n'] = array(
    'name'          => 'rt_tracker_leads_n',
    'type'          => 'link',
    'relationship'  => 'rt_tracker_leads_n',
    'module'        => 'Leads',
    'bean_name'     => 'Lead',
    'source'        => 'non-db',
    'vname'         => 'rt_Trackers',
    'id_name'       => 'parent_id',
    'link-type'     => 'one',
    'side'          => 'right',
);