<?php

class GoogleAnalyticsReport
{
    private static $fieldDefs = array(
        'id' => array(
            'name' => 'id',
            'vname' => 'LBL_ID',
            'type' => 'id',
            'required' => true,
            'reportable' => true,
        ),
        'deleted' => array(
            'name' => 'deleted',
            'vname' => 'LBL_DELETED',
            'type' => 'bool',
            'default' => '0',
            'reportable' => false,
            'comment' => 'Record deletion indicator',
        ),
        '_cid' => array(
            'name' => '_cid',
            'vname' => 'LBL_GCID',
            'type' => 'varchar',
            'len' => 255,
        ),
        'userType' => array(
            'name' => 'userType',
            'vname' => 'LBL_GUSER_TYPE',
            'type' => 'varchar',
            'len' => 255,
        ),
        'noOfVisits' => array(
            'name' => 'noOfVisits',
            'vname' => 'LBL_GNO_OF_VISITS',
            'type' => 'varchar',
            'len' => 255,
        ),
        'fullReferrer' => array(
            'name' => 'fullReferrer',
            'vname' => 'LBL_GFULL_REFERRER',
            'type' => 'varchar',
            'len' => 255,
        ),
        'socialNetwork' => array(
            'name' => 'socialNetwork',
            'vname' => 'LBL_GSOCIAL_NETWORK',
            'type' => 'varchar',
            'len' => 255,
        ),
        'hasSocialSourceReferral' => array(
            'name' => 'hasSocialSourceReferral',
            'vname' => 'LBL_GHAS_SOCIAL_SOURCE_REFERRAL',
            'type' => 'varchar',
            'len' => 255,
        ),
        'country' => array(
            'name' => 'country',
            'vname' => 'LBL_GCOUNTRY',
            'type' => 'varchar',
            'len' => 255,
        ),
        'city' => array(
            'name' => 'city',
            'vname' => 'LBL_GCITY',
            'type' => 'varchar',
            'len' => 255,
        ),
        'browser' => array(
            'name' => 'browser',
            'vname' => 'LBL_GBROWSER',
            'type' => 'varchar',
            'len' => 255,
        ),
        'browserVersion' => array(
            'name' => 'browserVersion',
            'vname' => 'LBL_GBROWSER_VERSION',
            'type' => 'varchar',
            'len' => 255,
        ),
        'operatingSystem' => array(
            'name' => 'operatingSystem',
            'vname' => 'LBL_GOS',
            'type' => 'varchar',
            'len' => 255,
        ),
        'operatingSystemVersion' => array(
            'name' => 'operatingSystemVersion',
            'vname' => 'LBL_GOS_VERSION',
            'type' => 'varchar',
            'len' => 255,
        ),
        'deviceCategory' => array(
            'name' => 'deviceCategory',
            'vname' => 'LBL_GDEVICE_CATEGORY',
            'type' => 'varchar',
            'len' => 255,
        ),
        'language' => array(
            'name' => 'language',
            'vname' => 'LBL_GLANGUAGE',
            'type' => 'varchar',
            'len' => 255,
        ),
        'userAgeBracket' => array(
            'name' => 'userAgeBracket',
            'vname' => 'LBL_GUSER_AGE_BRACKET',
            'type' => 'varchar',
            'len' => 255,
        ),
        'userGender' => array(
            'name' => 'userGender',
            'vname' => 'LBL_GUSER_GENDER',
            'type' => 'varchar',
            'len' => 255,
        ),
    );
    private static $completeFilters = array('userType',
        'fullReferrer',
        'socialNetwork',
        'hasSocialSourceReferral',
        'country',
        'city',
        'browser',
        'browserVersion',
        'operatingSystem',
        'operatingSystemVersion',
        'deviceCategory',
        'language',
        'userAgeBracket',
        'userGender'
    );

    public static function fetch($_data)
    {
        $zfid = $_data['zfid'];
        $cid = $_data['cid'];
        $filters = json_decode($_data['filters'], true);

        if (empty($filters))
            $filters = self::$completeFilters;

        $data = array();
        $str = implode(", ", $filters);
        $table = 'rt_ga_visitors';
        $sql = "SELECT * FROM {$table} WHERE _cid = '{$cid}'";
        $res = $GLOBALS['db']->query($sql);

        if ($res->num_rows > 0) {
            while ($row = $GLOBALS['db']->fetchByAssoc($res)) {
                foreach ($filters as $key) {
                    $data[$key] = unserialize($row[$key]);
                }
            }
        } else {
            $dataExists = false;
            $completeFilters = self::$completeFilters;
			
			$force_exit = false;
			if (empty($zfid)) {
				$force_exit = true;
				$response = 'LBL_MIS_KEY';
			} elseif (empty($cid)) {
				$force_exit = true;
				$response = 'LBL_MIS_USER_INF';
			}
			if ($force_exit) {
				header('HTTP/1.1 400 Bad Request');
				echo $response;
				exit;
			}
			
            $gdata = self::curlCall($zfid, $cid, json_encode($completeFilters));
            $completeFilters[] = "noOfVisits";

            foreach ($completeFilters as $key) {
                if (!empty($gdata[$key]))
                    $dataExists = true;
                if (in_array($key, $filters))
                    $data[$key] = $gdata[$key];
                $gdata[$key] = serialize($gdata[$key]);
            }
            if ($dataExists) {
                $gdata['_cid'] = $cid;
                $gdata['id'] = create_guid();
                $GLOBALS['db']->insertParams($table, self::$fieldDefs, $gdata);
            }
        }
        return $data;
    }

    public static function curlCall($zfid, $cid, $filters)
    {
        $url = 'https://rtcxmneon.rolustech.com/customerService/GoogleAnalyticsReport?zfid=';
        $url .= $zfid . '&cid=' . $cid . '&filters=' . $filters;

        $curl_request = curl_init();

        curl_setopt($curl_request, CURLOPT_URL, $url);
        curl_setopt($curl_request, CURLOPT_HEADER, false);
        curl_setopt($curl_request, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($curl_request, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl_request, CURLOPT_FOLLOWLOCATION, 0);
        curl_setopt($curl_request, CURLOPT_SSL_VERIFYPEER, false);

        $result = curl_exec($curl_request);
        $res = json_decode($result, true);

        if (curl_errno($curl_request)) {
            echo 'Curl error: ' . curl_errno($curl_request) . " : " . curl_error($curl_request);
            curl_close($curl_request);
            die("0");
        }

        curl_close($curl_request);
        if (array_key_exists('failure', $res)) {
            header('HTTP/1.1 400 Bad Request');
            $response = $res['failure'];
            echo $response;
            exit;
        }
        return json_decode($result, true);
    }
}