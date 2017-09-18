<?php

require_once('modules/Leads/Lead.php');
if (!defined('sugarEntry') || !sugarEntry)
    die('Not A Valid Entry Point');

class lead_get_location_logic_hook {

    function get_location(&$bean, $event, $arguments) {
        //$id = $bean->id; 	
        $id = $bean->fetched_row['id'];
        $focus = new Lead();
        $row = $focus->retrieve($bean->id);
        if (!empty($row->maps_lat) && !empty($row->maps_long)) {
            $bean->location_marker2 = '<a title="Location on map" id="location_marker" href="index.php?module=rt_maps&offset=1&from_module=Lead&return_module=Leads&action=googlemaps&record=' . $id . '"><img src="custom/include/maps_images/redpin.jpg" border="0" alt="location"></a>';
        } else {
            $bean->location_marker2 = '<a id="location_marker" href="javascript:void(0)" title="Location not set"><img src="custom/include/maps_images/greypin.jpg" border="0" alt="location"></a>';
        }
    }

    /* This is the logic hook for records in detail/edit view */

    function get_location_detailview(&$bean, $event, $arguments) {
        //$id = $bean->id; 	
        $id = $bean->fetched_row['id'];
        if (!empty($bean->maps_lat) && !empty($bean->maps_long)) {
            $bean->location_marker2 = '<a title="Location on map" id="location_marker" href="index.php?module=rt_maps&offset=1&from_module=Lead&return_module=Leads&action=googlemaps&record=' . $id . '"><img src="custom/include/maps_images/redpin.jpg" border="0" alt="location"></a>';
        } else {
            $bean->location_marker2 = '<a id="location_marker" href="javascript:void(0)" title="Location not set"><img src="custom/include/maps_images/greypin.jpg" border="0" alt="location"></a>';
        }
    }

}
