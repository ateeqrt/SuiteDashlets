<?php
require_once 'soap/SoapError.php';

class RtCxmServiceApi
{

    private function checkSaveOnNotify()
    {
        $notifyonsave = false;
        if (isset($_SESSION['notifyonsave']) && $_SESSION['notifyonsave'] == true) {
            $notifyonsave = true;
        } // if
        return $notifyonsave;
    }

    private function getNameValue($field, $value)
    {
        return array('name' => $field, 'value' => $value);
    }

    private function filterFields($value, $fields)
    {
        // $GLOBALS['log']->info('Begin: rtCxmServiceApi->filterFields');
        global $invalid_contact_fields;
        $filterFields = array();
        foreach ($fields as $field) {
            if (is_array($invalid_contact_fields)) {
                if (in_array($field, $invalid_contact_fields)) {
                    continue;
                } // if
            } // if
            if (isset($value->field_defs[$field])) {
                $var = $value->field_defs[$field];
                if (isset($var['source']) && ($var['source'] != 'db' && $var['source'] != 'custom_fields')
                    && $var['name'] != 'email1' && $var['name'] != 'email2' && (!isset($var['type']) || $var['type'] != 'relate')
                    && !(isset($var['type']) && $var['type'] == 'id' && isset($var['link']))
                ) {

                    if ($value->module_dir == 'Emails' && (($var['name'] == 'description') || ($var['name'] == 'description_html')
                            || ($var['name'] == 'from_addr_name') || ($var['name'] == 'reply_to_addr') || ($var['name'] == 'to_addrs_names')
                            || ($var['name'] == 'cc_addrs_names') || ($var['name'] == 'bcc_addrs_names') || ($var['name'] == 'raw_source'))
                    ) {

                    } else {
                        continue;
                    }
                }
            } // if
            $filterFields[] = $field;
        } // foreach
        // $GLOBALS['log']->info('End: rtCxmServiceApi->filterFields');
        return $filterFields;
    } // fn

    private function getNameValueListForFields($value, $fields)
    {
        // $GLOBALS['log']->info('Begin: rtCxmServiceApi->getNameValueListForFields');
        global $app_list_strings;
        global $invalid_contact_fields;

        $list = array();
        if (!empty($value->field_defs)) {
            if (empty($fields)) $fields = array_keys($value->field_defs);
            if (isset($value->assigned_user_name) && in_array('assigned_user_name', $fields)) {
                $list['assigned_user_name'] = $this->getNameValue('assigned_user_name', $value->assigned_user_name);
            }
            if (isset($value->assigned_name) && in_array('assigned_name', $fields)) {
                $list['team_name'] = $this->getNameValue('team_name', $value->assigned_name);
            }
            if (isset($value->modified_by_name) && in_array('modified_by_name', $fields)) {
                $list['modified_by_name'] = $this->getNameValue('modified_by_name', $value->modified_by_name);
            }
            if (isset($value->created_by_name) && in_array('created_by_name', $fields)) {
                $list['created_by_name'] = $this->getNameValue('created_by_name', $value->created_by_name);
            }

            $filterFields = $this->filterFields($value, $fields);

            foreach ($filterFields as $field) {
                $var = $value->field_defs[$field];
                if (isset($value->$var['name'])) {
                    $val = $value->$var['name'];
                    $type = $var['type'];

                    if (strcmp($type, 'date') == 0) {
                        $val = substr($val, 0, 10);
                    } elseif (strcmp($type, 'enum') == 0 && !empty($var['options'])) {
                    }
                    if ($type == 'encrypt' && !empty($var['write_only'])) {
                        $val = !empty($val);
                    }

                    $list[$var['name']] = $this->getNameValue($var['name'], $val);
                } // if
            } // foreach
        } // if
        // $GLOBALS['log']->info('End: rtCxmServiceApi->getNameValueListForFields');
        return $list;
    }

    public function setEntry($data = array())
    {
        global $current_user;
        $module_name = $data['module_name'];
        $name_value_list = $data['name_value_list'];
        $current_user->id = ($current_user->id) ?: '1';
        // $GLOBALS['log']->info('Begin: rtCxmServiceApi->set_entry');

        $error = new SoapError();

        $seed = BeanFactory::getBean($module_name);

        foreach ($name_value_list as $name => $value) {
            if (is_array($value) && $value['name'] == 'id') {
                $seed->retrieve($value['value']);
                break;
            } elseif ($name === 'id') {

                $seed->retrieve($value);
            }
        }

        $return_fields = array();
        foreach ($name_value_list as $name => $value) {
            if ($module_name == 'Users' && !empty($seed->id) && ($seed->id != $current_user->id) && $name == 'user_hash') {
                continue;
            }
            if (!empty($seed->field_name_map[$name]['sensitive'])) {
                continue;
            }

            if (is_array($value)) {
                $name = $value['name'];
                $value = $value['value'];
            }

            $seed->$name = $value;
            $return_fields[] = $name;

        }

        try {
            $seed->save($this->checkSaveOnNotify());
        } catch (Exception $ex) {//SugarApiExceptionNotAuthorized
            // $GLOBALS['log']->info('End: rtCxmServiceApi->set_entry');
            switch ($ex->messageLabel) {
                case 'ERR_USER_NAME_EXISTS':
                    $error_string = 'duplicates';
                    break;
                case 'ERR_REPORT_LOOP':
                    $error_string = 'user_loop';
                    break;
                default:
                    $error_string = 'error_user_create_update';
            }
            $error->set_error($error_string);
            return;
        }

        $return_entry_list = $this->getNameValueListForFields($seed, $return_fields);

        if ($seed->deleted == 1) {
            $seed->mark_deleted($seed->id);
        }

        // $GLOBALS['log']->info('End: rtCxmServiceApi->set_entry');
        return array('id' => $seed->id, 'entry_list' => $return_entry_list);
    }

    /**
     *   Retrieve number of records in a given module
     *
     * @param String module_name  -- module to retrieve number of records from
     * @param String query        -- allows webservice user to provide a WHERE clause
     * @param int deleted         -- specify whether or not to include deleted records
     *
     * @return Array  result_count - integer - Total number of records for a given module and query
     * @exception 'SoapFault' -- The SOAP error, if any
     */
    public function getEntriesCount($data)
    {
        $module_name = $data['module_name'];
        $query = $data['query'];
        $query = urldecode(base64_decode($query));
        $deleted = ($data['deleted']) ?: '0';

        // $GLOBALS['log']->info('Begin: rtCxmServiceApi->get_entries_count');

        $error = new SoapError();

        global $current_user;

        $seed = BeanFactory::getBean($module_name);

        $sql = 'SELECT COUNT(*) result_count FROM ' . $seed->table_name . ' ';

        $seed->add_team_security_where_clause($sql);

        $customJoin = $seed->getCustomJoin();
        $sql .= $customJoin['join'];

        // build WHERE clauses, if any
        $where_clauses = array();
        if (!empty($query)) {
            $where_clauses[] = $query;
        }
        if ($deleted == 0) {
            $where_clauses[] = $seed->table_name . '.deleted = 0';
        }

        // if WHERE clauses exist, add them to query
        if (!empty($where_clauses)) {
            $sql .= ' WHERE ' . implode(' AND ', $where_clauses);
        }

        $res = $GLOBALS['db']->query($sql);
        $row = $GLOBALS['db']->fetchByAssoc($res);

        // $GLOBALS['log']->info('End: rtCxmServiceApi->get_entries_count');
        return array(
            'result_count' => $row['result_count'],
        );
    }

    /**
     * Equivalent of get_list function within SugarBean but allows the possibility to pass in an indicator
     * if the list should filter for favorites.  Should eventually update the SugarBean function as well.
     *
     */
    private function getDataList(
        $seed,
        $order_by = "",
        $where = "",
        $row_offset = 0,
        $limit = -1,
        $max = -1,
        $show_deleted = 0,
        $favorites = false,
        $singleSelect = false
    )
    {

        // $GLOBALS['log']->debug("get_list:  order_by = '$order_by' and where = '$where' and limit = '$limit'");
        if (isset($_SESSION['show_deleted'])) {
            $show_deleted = 1;
        }
        $order_by = $seed->process_order_by($order_by, null);

        $seed->addVisibilityWhere($where);
        $params = array();
        if ($favorites)
            $params['favorites'] = true;

        $query = $seed->create_new_list_query(
            $order_by,
            $where,
            array(),
            $params,
            $show_deleted,
            '',
            false,
            null,
            $singleSelect
        );
        return $seed->process_list_query($query, $row_offset, $limit, $max, $where);
    }

    private function getReturnValueForFields($value, $module, $fields)
    {
        // $GLOBALS['log']->info('Begin: rtCxmServiceApi->getReturnValueForFields');
        global $module_name, $current_user;
        $module_name = $module;
        if ($module == 'Users' && $value->id != $current_user->id) {
            $value->user_hash = '';
        }
        $value = clean_sensitive_data($value->field_defs, $value);
        // $GLOBALS['log']->info('End: rtCxmServiceApi->getReturnValueForFields');
        return array('id' => $value->id,
            'module_name' => $module,
            'name_value_list' => $this->getNameValueListForFields($value, $fields)
        );
    }

    private function getReturnValueForLinkFields($bean, $module, $link_name_to_value_fields_array)
    {
        // $GLOBALS['log']->info('Begin: rtCxmServiceApi->getReturnValueForLinkFields');
        global $module_name, $current_user;
        $module_name = $module;
        if ($module == 'Users' && $bean->id != $current_user->id) {
            $bean->user_hash = '';
        }
        $bean = clean_sensitive_data($bean->field_defs, $bean);

        if (empty($link_name_to_value_fields_array) || !is_array($link_name_to_value_fields_array)) {
            $GLOBALS['log']->debug('End: rtCxmServiceApi->getReturnValueForLinkFields - Invalid link information passed ');
            return array();
        }

        if ($this->isLogLevelDebug()) {
            $GLOBALS['log']->debug('rtCxmServiceApi->getReturnValueForLinkFields - link info = ' . var_export($link_name_to_value_fields_array, true));
        } // if
        $link_output = array();
        foreach ($link_name_to_value_fields_array as $link_name_value_fields) {
            if (!is_array($link_name_value_fields) || !isset($link_name_value_fields['name'])
                || !isset($link_name_value_fields['value'])
            ) {
                continue;
            }
            $link_field_name = $link_name_value_fields['name'];
            $link_module_fields = $link_name_value_fields['value'];
            if (is_array($link_module_fields) && !empty($link_module_fields)) {
                $result = $this->getRelationshipResults($bean, $link_field_name, $link_module_fields);
                if (!$result) {
                    $link_output[] = array('name' => $link_field_name, 'records' => array());
                    continue;
                }
                $list = $result['rows'];
                $filterFields = $result['fields_set_on_rows'];
                if ($list) {
                    $rowArray = array();
                    foreach ($list as $row) {
                        $nameValueArray = array();
                        foreach ($filterFields as $field) {
                            $nameValue = array();
                            if (isset($row[$field])) {
                                $nameValueArray[$field] = $this->getNameValue($field, $row[$field]);
                            } // if
                        } // foreach
                        $rowArray[] = $nameValueArray;
                    } // foreach
                    $link_output[] = array('name' => $link_field_name, 'records' => $rowArray);
                } // if
            } // if
        } // foreach
        // $GLOBALS['log']->debug('End: rtCxmServiceApi->getReturnValueForLinkFields');
        if ($this->isLogLevelDebug()) {
            $GLOBALS['log']->debug('rtCxmServiceApi->getReturnValueForLinkFields - output = ' . var_export($link_output, true));
        } // if
        return $link_output;
    } // fn

    /**
     * Retrieve a list of beans.  This is the primary method for getting list of SugarBeans from Sugar using the SOAP API.
     *
     * @param String $module_name -- The name of the module to return records from.  This name should be the name the module was developed under (changing a tab name is studio does not affect the name that should be passed into this method)..
     * @param String $query -- SQL where clause without the word 'where'
     * @param String $order_by -- SQL order by clause without the phrase 'order by'
     * @param integer $offset -- The record offset to start from.
     * @param Array $select_fields -- A list of the fields to be included in the results. This optional parameter allows for only needed fields to be retrieved.
     * @param Array $link_name_to_fields_array -- A list of link_names and for each link_name, what fields value to be returned. For ex.'link_name_to_fields_array' => array(array('name' =>  'email_addresses', 'value' => array('id', 'email_address', 'opt_out', 'primary_address')))
     * @param integer $max_results -- The maximum number of records to return.  The default is the sugar configuration value for 'list_max_entries_per_page'
     * @param integer $deleted -- false if deleted records should not be include, true if deleted records should be included.
     * @return Array 'result_count' -- integer - The number of records returned
     *               'next_offset' -- integer - The start of the next page (This will always be the previous offset plus the number of rows returned.  It does not indicate if there is additional data unless you calculate that the next_offset happens to be closer than it should be.
     *               'entry_list' -- Array - The records that were retrieved
     *                 'relationship_list' -- Array - The records link field data. The example is if asked about accounts email address then return data would look like Array ( [0] => Array ( [name] => email_addresses [records] => Array ( [0] => Array ( [0] => Array ( [name] => id [value] => 3fb16797-8d90-0a94-ac12-490b63a6be67 ) [1] => Array ( [name] => email_address [value] => hr.kid.qa@example.com ) [2] => Array ( [name] => opt_out [value] => 0 ) [3] => Array ( [name] => primary_address [value] => 1 ) ) [1] => Array ( [0] => Array ( [name] => id [value] => 403f8da1-214b-6a88-9cef-490b63d43566 ) [1] => Array ( [name] => email_address [value] => kid.hr@example.name ) [2] => Array ( [name] => opt_out [value] => 0 ) [3] => Array ( [name] => primary_address [value] => 0 ) ) ) ) )
     * @exception 'SoapFault' -- The SOAP error, if any
     */
    public function getEntryList($data = array())
    {
        $module_name = $data['module_name'];
        $query = urldecode(base64_decode($data['query']));
        $order_by = $data['order_by'];
        $offset = $data['offset'];
        $select_fields = $data['select_fields'];
        $link_name_to_fields_array = $data['link_name_to_fields_array'];
        $max_results = $data['max_results'];
        $deleted = $data['deleted'];
        $favorites = ($data['favorites']) ?: false;

        // $GLOBALS['log']->info('Begin: rtCxmServiceApi->get_entry_list');
        global $beanList, $beanFiles;
        $error = new SoapError();
        $using_cp = false;
        if ($module_name == 'CampaignProspects') {
            $module_name = 'Prospects';
            $using_cp = true;
        }

        // If the maximum number of entries per page was specified, override the configuration value.
        if ($max_results > 0) {
            global $sugar_config;
            $sugar_config['list_max_entries_per_page'] = $max_results;
        } // if

        $seed = BeanFactory::getBean($module_name);

        if ($query == '') {
            $where = '';
        } // if
        if ($offset == '' || $offset == -1) {
            $offset = 0;
        } // if
        if ($deleted) {
            $deleted = -1;
        }
        if ($using_cp) {
            $response = $seed->retrieveTargetList($query, $select_fields, $offset, -1, -1, $deleted);
        } else {
            $response = $this->getDataList($seed, $order_by, $query, $offset, -1, -1, $deleted, $favorites);
        } // else
        $list = $response['list'];

        $output_list = array();
        $linkoutput_list = array();

        foreach ($list as $value) {
            if (isset($value->emailAddress)) {
                $value->emailAddress->handleLegacyRetrieve($value);
            } // if
            $value->fill_in_additional_detail_fields();

            $output_list[] = $this->getReturnValueForFields($value, $module_name, $select_fields);
            if (!empty($link_name_to_fields_array)) {
                $linkoutput_list[] = $this->getReturnValueForLinkFields($value, $module_name, $link_name_to_fields_array);
            }
        } // foreach

        // Calculate the offset for the start of the next page
        $next_offset = $offset + sizeof($output_list);

        $returnRelationshipList = array();
        foreach ($linkoutput_list as $rel) {
            $link_output = array();
            foreach ($rel as $row) {
                $rowArray = array();
                foreach ($row['records'] as $record) {
                    $rowArray[]['link_value'] = $record;
                }
                $link_output[] = array('name' => $row['name'], 'records' => $rowArray);
            }
            $returnRelationshipList[]['link_list'] = $link_output;
        }

        $totalRecordCount = $response['row_count'];
        if (!empty($sugar_config['disable_count_query']))
            $totalRecordCount = -1;

        // $GLOBALS['log']->info('End: rtCxmServiceApi->get_entry_list - SUCCESS');
        return array('result_count' => sizeof($output_list),
            'total_count' => $totalRecordCount,
            'next_offset' => $next_offset,
            'entry_list' => $output_list,
            'relationship_list' => $returnRelationshipList
        );
    } // fn
}