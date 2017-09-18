<?php
require_once('data/SugarBean.php');
require_once('data/BeanFactory.php');

ob_clean();


     function getZFID()
    {
        $module = 'rt_Tracker';
        $record_id = '1';
        $bean = BeanFactory::getBean($module, $record_id, array('disable_row_level_security' => true),false);
        return $bean->zfid;
    }

    function storeZFID($zfid)
    {
        $module = 'rt_Tracker';
        $record_id = '1';
        $query = "UPDATE rt_tracker SET zfid='" . $zfid . "' WHERE id='" . $record_id . "'";
        $GLOBALS['db']->query($query);
    }
    //validate license key
    function validate()
    {
        global $sugar_config;

        if (!isset($GLOBALS['currentModule'])) {
            $GLOBALS['currentModule'] = "rt_Tracker";
        }
        $_REQUEST['key'] = $_REQUEST['license_key'];

        $set = true;


        $file = 'modules/rt_Tracker/license/OutfittersLicense.php';
        require_once($file);

        $result = OutfittersLicense::validate();

        if ($set && isset($result['data']['validated']) && ($result['data']['validated'])) {
            //CHECK ZFID
            $zfid = getZFID();
            global $current_user;
            global $timedate;
            $time_now = $GLOBALS['timedate']->nowDb();
            $site_url = $sugar_config['site_url'];

            if(dbFieldExists('rt_tracker',$_REQUEST['key']))
                return;
            //ALSO CALL CXM SERVER TO ADD LIC
            $file = 'custom/clients/base/api/rtcxm-helpers/rtcxm_serve.php';
            require_once($file);

            $res = RtCxmServe::curlCall($_REQUEST['key'], $site_url, $zfid);
            if ($res->success == 'true') {
                //ADD ZFID TO RT TRACKER WITH ID = 1
            $save_prefs_query   = "INSERT INTO rt_tracker(id,name, date_entered, date_modified, modified_user_id, created_by, deleted, license_key,zfid) VALUES('" . 1 . "', '" . "CXM" . "', '" . $time_now . "', '" . $time_now . "', '" . $current_user->id . "', '" . $current_user->id . "', 0, '" . $_REQUEST['key'] . "', '" . $zfid . "' )";

            $GLOBALS['db']->query($save_prefs_query);
            storeZFID($res->zfid);
            }
        }

        //$GLOBALS['log']->fatal($sugar_config);
        return $result;
    }
    function dbFieldExists($table, $field){
      //$q = "SHOW COLUMNS FROM'" . $table . "' LIKE'" . $field . "'";
      $q = "SELECT '" . $field . "' FROM " . $table . "";
      $result = $GLOBALS['db']->query($q);

      $exists = (mysqli_num_rows($result))?TRUE:FALSE;
      if($exists) {
        return true;
      }
      return false;
    }
validate();

exit();
