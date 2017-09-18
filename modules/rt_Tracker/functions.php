<?php
require_once('data/BeanFactory.php');

function directToFunction(){

    $api_files = 'custom/modules/rt_Tracker/clients/base/api/';

    if($_REQUEST['method'] == 'getUserConfig')
    {
        return getUserConfig();
    }

    elseif ($_REQUEST['method'] == 'fetchEmailBean'){

        require_once($api_files . 'rtcxm-helpers/fetchEmailBean.php');
        return json_encode( FetchEmailBean::fetch($_REQUEST['data']) );
    }

    elseif ($_REQUEST['method'] == 'trackDecode'){

        require_once($api_files . 'rtcxm-helpers/trackDecode.php');
        return json_encode( TrackDecode::decode($_REQUEST['data']) );
    }

    elseif ($_REQUEST['method'] == 'setUserConfig') {

        return setUserConfig($args);
    }

    elseif ($_REQUEST['method'] == 'getZFID') {

        return json_encode(getZFID());
    }

    elseif ($_REQUEST['method'] == 'validateCXMUser') {

        return json_encode(validateCXMUser());
    }

    elseif ($_REQUEST['method'] == 'getBean') {

        return json_encode(getBean());
    }

    elseif ($_REQUEST['method'] == 'validate') {

        return json_encode(validate());
    }

    elseif ($_REQUEST['method'] == 'getCustomCSS') {

        return json_encode( getCustomCSS() );
    }

    elseif ($_REQUEST['method'] == 'dbFieldExists') {

        return json_encode(dbFieldExists($_REQUEST['table'],$_REQUEST['field']));
    }

    elseif ($_REQUEST['method'] == 'getModLang') {

        return json_encode(getModLang());
    }

    elseif ($_REQUEST['method'] == 'getModListLang') {

        return json_encode(getModListLang());
    }
    elseif ($_REQUEST['method'] == 'recurUser') {

        return json_encode(recurUser());
    }

    elseif ($_REQUEST['method'] == 'getUser') {

        return json_encode(getUser());
    }
    elseif ($_REQUEST['method'] == 'version') {

        return json_encode(getVersion());
    }
    
}

    echo directToFunction();
    function getVersion()
    {
        global $sugar_config;
        $version = (double)$sugar_config['sugar_version'];
        $class = 'ft1';
        if ($version < 7.6)
            $class .= ' block-footer';
        return array('className' => $class);
    }    function getUser(){
        global $db;
        $user_id = $GLOBALS['current_user']->id;
        $sql = "SELECT id, first_name, last_name FROM users WHERE id = '{$user_id}' LIMIT 0,1";
        $result = $GLOBALS['db']->query($sql);
        if ($result->num_rows > 0) {
            while ($row = $db->fetchByAssoc($result)) {
                $user[$row['id']] = $row['first_name'] . ' ' . $row['last_name'];
            }
        }
        return $user;
    }
    function recurUser()
    {
        $isValid = json_decode($this->validate(), true);
        if ($isValid['isValid'] && $isValid['cxmValid']) {
            $file = $this->path . $this->ds . 'rtcxm-helpers' . $this->ds . 'recurUser.php';
            if (isset($args) && isset($_REQUEST['cookie_id'])) {
                $cookie_id = json_decode(base64_decode($_REQUEST['cookie_id']));
                require $file;
                return RecurUser::analysis($cookie_id);
            }
        } else {
            return $this->badRequest($isValid);
        }
    }
    function getModLang(){
        $mod_strings = return_module_language($GLOBALS['current_language'], 'rt_Tracker');
        return $mod_strings;
    }
    function getModListLang(){
        if (!isset($GLOBALS['currentModule'])) {
            return array();
        }
        else
            return $GLOBALS['currentModule'];
    }
    function getBean()
    {
        $module = $_REQUEST['module'];
        $options = $_REQUEST['options'];
        $bean = BeanFactory::getBean($module, null, $options, false);
        return $bean;
    }
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
        $_REQUEST['key'];

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
    function getUserConfig()
    {
        $ds = '/';
        $module = 'rt_Tracker';
        global $sugar_config;
        global $db;

        $file = 'modules' . $ds . $module . $ds . 'license' . $ds . 'config.php';
        require_once($file);


        $admin = new Administration();
        $admin->retrieveSettings();
        $last_validation = $admin->settings['SugarOutfitters_' . $outfitters_config['shortname']];
        $trimmed_last = trim($last_validation); //to be safe...
        $last_validation = base64_decode($last_validation);
        $last_validation = unserialize($last_validation);

        $data = array();
        $data['isRepaired'] = true;
        $data['isValidated'] = true;
        $enabled_active_users = array();
        $active_users = array();
        // $this->dbFieldExists('so_users','user_id') &&
        if (dbFieldExists('users', 'cxm_agent') && dbFieldExists('users', 'active_user')) {
            //get active users
            $active_users = get_user_array(false, 'Active', '', false, '', " AND cxm_agent=0");
            //get cxm enabled users
            $result = getEnabledUsers();
            if ($result->num_rows > 0) {
                while ($row = $db->fetchByAssoc($result)) {
                    $enabled_active_users[$row['id']] = $row['first_name'] . ' ' . $row['last_name'];
                }
            }
            $data['enabled_active_users'] = $enabled_active_users;
            $data['active_users'] = $active_users;
        } else {
            $data['isRepaired'] = false;//do quick repair and rebuild
        }
        if (isset($last_validation['last_result']['result']['validated']) &&
            !empty($last_validation['last_result']['result']['validated']) &&
            isset($sugar_config['outfitters_licenses']) &&
            isset($sugar_config['outfitters_licenses'][$outfitters_config['shortname']])
        ) {
            $data['license_key'] = $sugar_config['outfitters_licenses'][$outfitters_config['shortname']];
        } else {
            $data['isValidated'] = false;
        }

        $data['select2Onchange'] = true;
        if ($sugar_config['sugar_version']) {
            $version = explode('.', $sugar_config['sugar_version']);
            if ($version[0] >= 7 && $version[1] >= 1 && $version[2] >= 6) {
                $data['select2Onchange'] = false;
            }
        }


        return json_encode(array('data' => $data));
    }

    function getCustomCSS()
    {
        global $sugar_config;
        $filename = 'custom/themes/custom.css';
        $filecontents = file_get_contents($filename, true);
        return array('data' => $filecontents);
    }


    function dbFieldExists($table, $field){
      //$q = "SHOW COLUMNS FROM'" . $table . "' LIKE'" . $field . "'";
      $q = "SELECT '" . $field . "' FROM " . $table . "";
      $result = $GLOBALS['db']->query($q);

      if(mysqli_num_rows($result)) 
        return true;
      return false;
    }

    function getEnabledUsers()
    {
        $sql = 'SELECT `id`, `first_name`, `last_name` FROM `users` WHERE `cxm_agent` = "1"';
        $result = $GLOBALS['db']->query($sql);
        return $result;
    }


    function validateCXMUser()
    {
        $valid = false;
        $user_id = $GLOBALS['current_user']->id;
        // if($this->dbFieldExists('so_users','user_id')){
        $valid = isValidUser($user_id);
        // }
        if (session_status() == PHP_SESSION_DISABLED)
            session_start();
        $_SESSION['_rtvalidatecxm'] = ($valid) ? '1' : '0';

        $cxmValid = false;
        $q = "SELECT * FROM users WHERE id = '" .$user_id . "'  AND cxm_agent = 1";
        $result = $GLOBALS['db']->query($q);

        $exists = (mysqli_num_rows($result))?TRUE:FALSE;
        if($exists) {
            $cxmValid = true;
        }
        return array('isValid' => $valid, 'cxmValid' => $cxmValid);
    }

    function isValidUser($user_id)
    {
        global $db;
        $shortname = 'rt-cxm';
        $isValid = false;
        // $sql = "SELECT COUNT(`user_id`) AS `count` FROM so_users WHERE `user_id` = '{$user_id}' AND `shortname` = '{$shortname}'";
        $sql = "SELECT COUNT(*) AS `count` FROM config WHERE `category` = 'SugarOutfitters' AND `name` = '{$shortname}'";
        // $GLOBALS['log']->fatal($sql);
        $result = $db->query($sql);
        if ($result->num_rows > 0) {
            $row = $db->fetchByAssoc($result);
            $count = $row['count'];
            if ($count > 0)
                $isValid = true;
        }
        return $isValid;
    }
    function assignUsers($selectedUserIDS)
    {
        $ids = implode('","', $selectedUserIDS);
        $sql = 'UPDATE `users` SET `cxm_agent` = "0"';
        $GLOBALS['db']->query($sql);
        $sql = 'UPDATE `users` SET `cxm_agent` = "1" WHERE `ID` IN ("' . $ids . '")';
        $GLOBALS['db']->query($sql);
    }
    function setUserConfig($args)
    {
        if (!isset($GLOBALS['currentModule'])) {
            $GLOBALS['currentModule'] = "rt_Tracker";
        }
        $decoded = json_decode(base64_decode($_REQUEST['selectedUserIDS']), 1);
        $args['selectedUserIDS'] = $decoded;
        $data = array();
        $data['isRepaired'] = true;

        if (!is_admin($GLOBALS['current_user'])) {
            sugar_die("Unauthorized access to administration.");
        }

        // $this->dbFieldExists('so_users','user_id') &&
        if (dbFieldExists('users', 'cxm_agent') && dbFieldExists('users', 'active_user')) {
            if (!isset($args['selectedUserIDS'])) {
                $args['selectedUserIDS'] = array();
            }

            assignUsers($args['selectedUserIDS']);

            $file = 'modules/rt_Tracker/license/OutfittersLicense.php';
            require_once($file);

            $_REQUEST['licensed_users'] = $args['selectedUserIDS'];
            OutfittersLicense::add();

            $data['enabled_active_users'] = $args['selectedUserIDS'];
            $data['isValid'] = validateCXMUser()['isValid'];
        } else {
            $data['isRepaired'] = false;
        }
        return array('data' => $data);
    }


?>