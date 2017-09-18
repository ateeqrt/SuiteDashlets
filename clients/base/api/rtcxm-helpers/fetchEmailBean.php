<?php

class FetchEmailBean
{
    public static function fetch($_data)
    {
        $_email = $_data['email'];
        $mod_id = $_data['lead_id'];
        $type = $_data['type'];
        $zfid = $_data['zfid'];

        if (!defined('DS'))
            define('DS', DIRECTORY_SEPARATOR);

        $rel = '';
        if ($type == 'Leads') {
            $rel = 'rt_cxm_email_leads_1';
            $key1 = 'rt_cxm_email_leads_1rt_cxm_email_ida';
            $key2 = 'rt_cxm_email_leads_1leads_idb';
        } elseif ($type == 'Contacts') {
            $rel = 'rt_cxm_email_contacts_1';
            $key1 = 'rt_cxm_email_contacts_1rt_cxm_email_ida';
            $key2 = 'rt_cxm_email_contacts_1contacts_idb';
        }

        //initialize the array to return
        $fc_data = array();
        $fc_data['email'] = $_email;

        //initialize array of keys
        $keys = array('generic' => 'gflag',
            'twitter' => 'tflag',
            'facebook' => 'fflag',
            'google' => 'gpflag',
            'linkedin' => 'lflag'
        );

        //retrieve cxm_email bean based on the provided email
        $bean = BeanFactory::getBean('rt_cxm_email')->retrieve_by_string_fields(array('email' => $_email));

        //flag for expiration
        $expired = false;

        //time handling variables
        $timezone = new DateTimeZone('UTC');    //set timezone
        $Date = new DateTime('now', $timezone);            //get date
        $Date->format("Y-m-d");                            //set the format

        //if the cxm_email bean exists
        if ($bean !== null && $bean->id !== null) {

            //get expiration date
            $expiry = new DateTime($bean->expiry, $timezone);

            //check if it is eligible
            if ($expiry > $Date && ($bean->expiry != '' && $bean->expiry != null)) {

                //if it is connected to the lead
                $con = false;
                if ($rel !== '') {
                    $table = $rel . '_c';
                    $sql = 'SELECT * FROM ' . $table . ' WHERE ' . $key2 . " = '" . $mod_id . "'";
                    $res = $GLOBALS['db']->query($sql);
                    $deleteList = array();

                    while ($row = $GLOBALS['db']->fetchByAssoc($res)) {
                        if ($row[$key1] == $bean->id) {
                            $con = true;
                        } else {
                            $deleteList[] = "'" . $row['id'] . "'";
                        }
                    }

                    if (count($deleteList) > 0) {
                        $sql = "DELETE FROM " . $table . " WHERE id IN ( " . implode(",", $deleteList) . " )";
                        $res = $GLOBALS['db']->query($sql);
                    }
                    $bean->load_relationship($rel);

                    if (!$con) {
                        $bean->$rel->add($mod_id);
                        $bean->save();
                    }
                }
                foreach ($keys as $key => $flag) {
                    if ($bean->$key) {
                        $fc_data[$key] = $bean->$key;
                    }
                }
            } else {
                $expired = true;
            }
        }
        $new = false;
        if ($bean === null || $bean->id === null || $bean->id == '') {

            //make call to fetch social records on web
            $bean = BeanFactory::newBean('rt_cxm_email');
            $bean->email = $_email;
            $bean->name = $_email;
            $expired = true;
            $new = true;
        }
        if ($expired) {

            //make call
            $data = self::curlCall($_email, $zfid);
            if (sizeof($data) > 0) {
                foreach ($data as $key => $val) {
                    if ($val) {
                        $fc_data[$key] = $val;
                        $bean->$key = $val;
                        $flag = $keys[$key];
                        $bean->$flag = 'true';
                    }
                }
            }
            $bean->expiry = $Date->add(new DateInterval('P1M'))->format('Y-m-d');
            $bean->save();
            $id = $bean->id;
            //load the relation
            if ($new) {
                $bean->load_relationship($rel);
                $bean->$rel->add($mod_id);
            }
        }
        if (count($fc_data) > 1) {
            return json_encode($fc_data);
        } else {
            return json_encode(null);
        }
    }

    public static function curlCall($email, $zfid)
    {
        $url = 'https://rtcxmneon.rolustech.com/customerService/socialService?email=' . $email . '&zfid=' . $zfid;
        $curl_request = curl_init();

        curl_setopt($curl_request, CURLOPT_URL, $url);
        curl_setopt($curl_request, CURLOPT_HEADER, false);
        curl_setopt($curl_request, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($curl_request, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl_request, CURLOPT_FOLLOWLOCATION, 0);
        curl_setopt($curl_request, CURLOPT_SSL_VERIFYPEER, false);

        $result = curl_exec($curl_request);

        if (curl_errno($curl_request)) {
            echo 'Curl error: ' . curl_errno($curl_request) . " : " . curl_error($curl_request);
            curl_close($curl_request);
            die("0");
        }

        curl_close($curl_request);
        $res = (array)json_decode($result);
        if (array_key_exists('failure', $res)) {
            header('HTTP/1.1 400 Bad Request');
            $response = $res['failure'];
            $json = getJSONobj();
            echo $response;
            exit;
        }
        return $res;
    }
}