<?php
if (!defined('sugarEntry') || !sugarEntry) die('Not A Valid Entry Point');

class RtCxmLeadLogicHookClass
{

    /**
     * Summary - Leads Before Save Logic Hook.
     * *Description* - This before save method is used when a Leads is updated. It
     * checks if the name has been changed, hence, updating all related tracks and
     * notifications. And if the lead has been converted, it sets the related notes and
     * contacts ids, and updates all related tracks that is updating their parent type
     * and if to the contacts bean the lead has been converted into.
     *
     * @param object $bean Bean Reference Object
     * @param object $event
     * @param object $arguments
     */
    public function beforeSaveMethod(&$bean, $event, $arguments)
    {
        global $db;
        $different = true;
        if (isset($arguments['isUpdate']) && $arguments['isUpdate'] == true) {

            $fields = array('phone_mobile', 'description', 'last_name', 'first_name',
                'primary_address_street', 'primary_address_city', 'primary_address_state',
                'primary_address_country', 'alt_address_street', 'alt_address_city',
                'alt_address_state', 'alt_address_country');

            $cxm_msg = ' (RtCXM Field)';

            foreach ($fields as $field) {
                if ($bean->$field != '' && stristr($bean->$field, $cxm_msg)) {
                    $bean->$field = str_ireplace($cxm_msg, '', $bean->$field);
                }
            }

            if ($bean->converted == $bean->fetched_row['converted'])
                $different = false;

            $name = $bean->first_name . ' ' . $bean->last_name;
            $name2 = $bean->first_name . '-' . $bean->last_name;
            $old_name = $bean->fetched_row['first_name'] . ' ' . $bean->fetched_row['last_name'];

            if ($name !== $old_name) {
                $sql = "UPDATE rt_tracker SET name = '{$name}' WHERE parent_id = '{$bean->id}' ";
                $db->query($sql);
                $sql = "SELECT cookie_id_c, email_c FROM rt_tracker WHERE parent_id = '{$bean->id}' ";
                $sql .= "ORDER BY date_entered DESC LIMIT 0,1";
                $res = $db->query($sql);

                if ($res->num_rows > 0) {
                    $row = $db->fetchByAssoc($res);
                    $c = $row['cookie_id_c'];
                    if (empty($row['email_c']))
                        $row['email_c'] = 'null';
                    $n = $name2 . " " . $row['email_c'] . " " . $row['cookie_id_c'];
                    $sql = "UPDATE rt_cxm_notif SET about_c = '{$n}' WHERE cookie_id = '{$c}' ";
                    $db->query($sql);
                }
            }
        }
        if ($bean->converted && $different) {
            $module = 'Contacts';
            $id = $bean->contact_id;
            $table = 'rt_tracker';
            $sql = "UPDATE `" . $table . "` SET `parent_type` = '" . $module . "' , `parent_id` = '" . $id . "' ";
            $sql .= "WHERE `parent_type` = 'Leads' AND `parent_id` = '" . $bean->id . "'";
            $result = $db->query($sql);
            $sql = "UPDATE `notes` SET `contact_id` = '" . $id . "' ";
            $sql .= "WHERE `parent_type` = 'Leads' AND `parent_id` = '" . $bean->id . "'";
            // $GLOBALS['log']->fatal($sql);
            $result = $db->query($sql);
            // $GLOBALS['log']->fatal($result->num_rows ." rows affected.");
        }
    }
}
