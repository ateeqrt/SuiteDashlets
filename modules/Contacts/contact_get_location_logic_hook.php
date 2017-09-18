<?php

require_once('modules/Contacts/Contact.php');
if (!defined('sugarEntry') || !sugarEntry)
    die('Not A Valid Entry Point');

class cont_get_location_logic_hook {
    /* This is the logic hook for records in listview */

    function get_location(&$bean, $event, $arguments) {
        $id = $bean->fetched_row['id'];
        $focus = new Contact();
        $row = $focus->retrieve($bean->id);

        if (!empty($row->maps_lat) && !empty($row->maps_long)) {
            $bean->location_marker2 = '<a title="Location on map" id="location_marker" href="index.php?module=rt_maps&offset=1&from_module=Contact&return_module=Contacts&action=googlemaps&record=' . $id . '"><img src="custom/include/maps_images/redpin.jpg" border="0" alt="location"></a>';
        } else {
            $bean->location_marker2 = '<a id="location_marker" href="javascript:void(0)" title="Location not set"><img src="custom/include/maps_images/greypin.jpg" border="0" alt="location"></a>';
        }
    }

    /* This is the logic hook for records in detail/edit view */

    function get_location_detailview(&$bean, $event, $arguments) {
        $id = $bean->fetched_row['id'];

        if (!empty($bean->maps_lat) && !empty($bean->maps_long)) {
            $bean->location_marker2 = '<a title="Location on map" id="location_marker" href="index.php?module=rt_maps&offset=1&from_module=Contact&return_module=Contacts&action=googlemaps&record=' . $id . '"><img src="custom/include/maps_images/redpin.jpg" border="0" alt="location"></a>';
        } else {
            $bean->location_marker2 = '<a id="location_marker" href="javascript:void(0)" title="Location not set"><img src="custom/include/maps_images/greypin.jpg" border="0" alt="location"></a>';
        }
    }

}
