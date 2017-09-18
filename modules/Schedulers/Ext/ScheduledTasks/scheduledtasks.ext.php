<?php 
 //WARNING: The contents of this file are auto-generated



/**
 * Push the job to jobs list
 * Calculate the latitude and longitude
 * Get all the accounts where the lat long is null
 * Fill the lat long on the basis of address
 * Get all the leads where lat long is null
 * Fill the lat long on the basis of address
 * Get all the contacts where lat long is null
 * Fill the lat long values on the basis of address. 
 * Code By FN-RT

 * */
ini_set('display_errors', 1);

array_push($job_strings, 'CalculateLatLong');

/* * *
 * Function to calculate the lat longs for accounts, contacts and leads.
 * */

function CalculateLatLong() {
    require_once('modules/rt_maps/GMaps.php');
    require_once('modules/rt_maps/helper/addresshelper.php');
    // To apply limit in record fetching for calculating lattitude and longitude.
    $limit = 1000;
    $GMap = new GMaps(); // we need to create it only once to reduce the overhead of creating GMaps object on Heap
    $add_helper = new AddressHelper; // helper class for contacts, leads, accounts to query data for saving lat and long within them
    global $db, $sugar_config;

    echo "<script src='https://maps.googleapis.com/maps/api/js?v=3.exp&sensor=false'></script>";

    $queried_modules = array('accounts', 'contacts', 'leads'); // this array contains the modules to save lat, long in them
    //$queried_modules = array('accounts'); // this array contains the modules to save lat, long in them
    /**
     * 	this will iterate through queried modules and within inner while loop lat,long will be fetched and saved in each module 
     */
    for ($i = 0; $i < 3; $i++) {
        $resultant_module = $add_helper->getScheduleResultSet($queried_modules[$i], $limit);
        while ($row_queried_module = $db->fetchByAssoc($resultant_module)) {
            $unprocessed_address = ($queried_modules[$i] == 'accounts') ? $row_queried_module['billing_address_street'] . "," . $row_queried_module['billing_address_city'] . "," . $row_queried_module['billing_address_state'] . "," . $row_queried_module['billing_address_postalcode'] . "," . $row_queried_module['billing_address_country'] : $row_queried_module['primary_address_street'] . "," . $row_queried_module['primary_address_city'] . "," . $row_queried_module['primary_address_state'] . "," . $row_queried_module['primary_address_postalcode'] . "," . $row_queried_module['primary_address_country'];
            $md5Address = md5($unprocessed_address);
            $trimmed_address = $add_helper->process_address_value($unprocessed_address);
            if (!empty($trimmed_address)) {
                $search_address = strip_tags($trimmed_address);
                if (!empty($search_address)) {
                    $encoded_address = str_replace(' ', '+', $trimmed_address);
                    $curl_query_string = curl_init();

                    //Set query data here with the URL
                    curl_setopt($curl_query_string, CURLOPT_URL, 'https://maps.google.com/maps/api/geocode/json?address=' . $encoded_address . '&sensor=false&key=' . $sugar_config['GOOGLEAPISRKEY']);
                    curl_setopt($curl_query_string, CURLOPT_RETURNTRANSFER, 1);
                    curl_setopt($curl_query_string, CURLOPT_TIMEOUT, '3');

                    $geocode = trim(curl_exec($curl_query_string));
                    curl_close($curl_query_string);

                    $geocode_output = json_decode($geocode);

                    if (isset($geocode_output->error_message) && isset($geocode_output->status)) {
                        $GLOBALS['log']->fatal("Geocode Error:" . $row_queried_module['id'] . ' **' . $queried_modules[$i] . '** ' . $geocode_output->error_message);
                        $GLOBALS['log']->fatal("Geocode Status:" . $geocode_output->status);
                    }
                    // Set the lat long for the contacts					
                    if (!empty($geocode_output->results[0])) {
                        $update_module_query = "UPDATE " . $queried_modules[$i] . " SET non_geo_coded_address='" . $md5Address . "', maps_lat='" . $geocode_output->results[0]->geometry->location->lat . "', maps_long='" . $geocode_output->results[0]->geometry->location->lng . "' WHERE id='" . $row_queried_module['id'] . "'";
                        $result_queried_module = $db->query($update_module_query);
                    } // end if when google maps has latitude and longitude against queried address
                    if ($geocode_output->status == 'ZERO_RESULTS') {
                        $update_module_query = "UPDATE " . $queried_modules[$i] . " SET non_geo_coded_address='" . $md5Address . "',maps_lat='',maps_long='' WHERE id='" . $row_queried_module['id'] . "'";
                        $result_queried_module = $db->query($update_module_query);
                    }
                    // }// end if case when $search address is located properly and successfully
                } else {
                    $update_module_query = "UPDATE " . $queried_modules[$i] . " SET non_geo_coded_address='" . $md5Address . "',maps_lat='',maps_long=''  WHERE id='" . $row_queried_module['id'] . "'";
                    $result_queried_module = $db->query($update_module_query);
                }
            } else {
                $update_module_query = "UPDATE " . $queried_modules[$i] . " SET non_geo_coded_address='" . $md5Address . "',maps_lat='',maps_long=''  WHERE id='" . $row_queried_module['id'] . "'";
                $result_queried_module = $db->query($update_module_query);
            }
        } // end inner while loop that is querying data from concerned module and request to google for lat,long fetching n saving
    } // end outer for loop that is iterating through queried modules

    return true;
}

?>