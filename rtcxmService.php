<?php
if (!defined('sugarEntry') || !sugarEntry) die('Not A Valid Entry Point');

$ds = DIRECTORY_SEPARATOR;
require_once 'custom' . $ds . 'clients' . $ds . 'base' . $ds . 'api' . $ds . 'rtCxmServiceApi.php';

$apiClass = new RtCxmServiceApi();

$method = ($_REQUEST['method']) ?: '';

$args = ($_REQUEST['rest_data']) ? json_decode(html_entity_decode($_REQUEST['rest_data']), true) : array();
//  && method_exists($apiClass, $method)
if ($method) {
    switch ($method) {
        case 'set_entry':
            echo json_encode($apiClass->setEntry($args));
            break;
        case 'get_entries_count':
            echo json_encode($apiClass->getEntriesCount($args));
            break;
        case 'get_entry_list':
            echo json_encode($apiClass->getEntryList($args));
            break;
        default:
            echo json_encode('');
    }
}