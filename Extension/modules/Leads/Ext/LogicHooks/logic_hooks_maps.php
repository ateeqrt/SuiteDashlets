<?php

$hook_array['process_record'][] = Array(1,
    'process_record get_location',
    'custom/modules/Leads/lead_get_location_logic_hook.php',
    'lead_get_location_logic_hook',
    'get_location'
);

$hook_array['after_retrieve'][] = Array(1,
    'detailview/editview get_location',
    'custom/modules/Leads/lead_get_location_logic_hook.php',
    'lead_get_location_logic_hook',
    'get_location_detailview'
);
