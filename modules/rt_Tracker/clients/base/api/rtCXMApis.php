<?php

if (!defined('sugarEntry') || !sugarEntry) die('Not A Valid Entry Point');

class RtCXMApis extends SugarApi
{
    protected static $ds = DIRECTORY_SEPARATOR;
    protected static $module = 'rt_Tracker';

    public function registerApiRest()
    {
        return array(
            'setUserConfig' => array(
                'reqType' => 'GET',
                'path' => array('rtCXMConfigurations', 'setUserConfig', '?'),
                'pathVars' => array('', '', 'selectedUserIDS'),
                'method' => 'setUserConfig',
                'shortHelp' => 'This method is used for saving user configuration options',
                'longHelp' => '',
            ),
            'getUserConfig' => array(
                'reqType' => 'GET',
                'path' => array('rtCXMConfigurations', 'getUserConfig'),
                'pathVars' => array('', ''),
                'method' => 'getUserConfig',
                'shortHelp' => 'This method is used to get the user configuration options',
                'longHelp' => '',
            ),
            'setAgentStatus' => array(
                'reqType' => 'GET',
                'path' => array('rtCXMConfigurations', 'setAgentStatus', '?'),
                'pathVars' => array('', '', 'AID'),
                'method' => 'setAgentStatus',
                'shortHelp' => 'This method is used to set the status of the current user',
                'longHelp' => '',
            ),
            'getCSS' => array(
                'reqType' => 'GET',
                'path' => array('rtCXMConfigurations', 'getCSS'),
                'pathVars' => array('', ''),
                'method' => 'getCustomCSS',
                'shortHelp' => 'This method helps to load the custom CSS for CXM Dashlets',
                'longHelp' => '',
            ),
            'version' => array(
                'reqType' => 'GET',
                'path' => array('rtCXM', 'version'),
                'pathVars' => array('', ''),
                'method' => 'getVersion',
                'shortHelp' => 'This method decides the footer class according to the sugar version',
                'longHelp' => '',
            ),
            'cxmIncreaseLicense' => array(
                'reqType' => 'GET',
                'path' => array('rtCXMLicense', 'change', '?'),
                'pathVars' => array('', '', 'key'),
                'method' => 'change',
                'shortHelp' => 'This method boost user count for RT CXM',
                'longHelp' => '',
            ),
            'getInitialSettings' => array(
                'reqType' => 'GET',
                'path' => array('rtCXMLicense', 'getInitialSettings'),
                'pathVars' => array('', ''),
                'method' => 'getInitialSettings',
                'shortHelp' => 'This method validates SugarOutfitter key',
                'longHelp' => '',
            ),
            'cxmValidateLicense' => array(
                'reqType' => 'GET',
                'path' => array('rtCXMLicense', 'validate', '?'),
                'pathVars' => array('', '', 'key'),
                'method' => 'validate',
                'shortHelp' => 'This method validates SugarOutfitter key',
                'longHelp' => '',
            ),
            'cxmValidateDashlet' => array(
                'reqType' => 'GET',
                'path' => array('rtCXMLicense', 'validateCXMUser'),
                'pathVars' => array('', ''),
                'method' => 'validateCXMUser',
                'shortHelp' => 'This method validates User for Dashlets Accessibility',
                'longHelp' => '',
            ),
            'validateCXMModuleLicense' => array(
                'reqType' => 'GET',
                'path' => array('validateCXMModuleLicense', 'prefs'),
                'pathVars' => array('', ''),
                'method' => 'validateModule',
                'shortHelp' => 'This method is used for validating license for modules',
                'longHelp' => '',
            ),
        );
    }

    //boost user count
    public function change($api, $args)
    {
        if (isset($args) && isset($args['key'])) {
            $_REQUEST['key'] = $args['key'];
        }
        if (isset($args) && isset($args['user_count'])) {
            $_REQUEST['user_count'] = $args['user_count'];
        }
        $file = 'modules' . self::$ds . self::$module . self::$ds . 'license' . self::$ds . 'OutfittersLicense.php';
        require_once($file);

        if (!isset($GLOBALS['currentModule'])) {
            $GLOBALS['currentModule'] = "rt_Tracker";
        }
        return OutfittersLicense::change();
    }

    //check in db if column exist or not
    private function dbFieldExists($table, $column)
    {
        global $db;
        $cols = $db->get_columns($table);

        if (is_array($cols)) {
            if (isset($cols[$column])) {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    private function assignUsers($selectedUserIDS)
    {
        $ids = implode('","', $selectedUserIDS);
        $sql = 'UPDATE `users` SET `cxm_agent` = "0"';
        $GLOBALS['db']->query($sql);
        $sql = 'UPDATE `users` SET `cxm_agent` = "1" WHERE `ID` IN ("' . $ids . '")';
        $GLOBALS['db']->query($sql);
    }

    public function setUserConfig($api, $args)
    {
        if (!isset($GLOBALS['currentModule'])) {
            $GLOBALS['currentModule'] = "rt_Tracker";
        }
        $args['selectedUserIDS'] = json_decode(base64_decode($args['selectedUserIDS']), 1);
        $data = array();
        $data['isRepaired'] = true;

        if (!is_admin($GLOBALS['current_user'])) {
            sugar_die("Unauthorized access to administration.");
        }

        // $this->dbFieldExists('so_users','user_id') &&
        if ($this->dbFieldExists('users', 'cxm_agent') && $this->dbFieldExists('users', 'active_user')) {
            if (!isset($args['selectedUserIDS'])) {
                $args['selectedUserIDS'] = array();
            }

            $this->assignUsers($args['selectedUserIDS']);

            $file = 'modules' . self::$ds . self::$module . self::$ds . 'license' . self::$ds . 'OutfittersLicense.php';
            require_once($file);

            $_REQUEST['licensed_users'] = $args['selectedUserIDS'];
            OutfittersLicense::add();

            $data['enabled_active_users'] = $args['selectedUserIDS'];
            $data['isValid'] = $this->validateCXMUser()['isValid'];
        } else {
            $data['isRepaired'] = false;
        }
        return array('data' => $data);
    }

    private function getEnabledUsers()
    {
        $sql = 'SELECT `id`, `user_name` FROM `users` WHERE `cxm_agent` = "1"';
        return $GLOBALS['db']->query($sql);
    }

    public function getUserConfig()
    {

        global $sugar_config;
        global $db;

        $file = 'modules' . self::$ds . self::$module . self::$ds . 'license' . self::$ds . 'config.php';
        require_once($file);

        $file = 'include' . self::$ds . 'SugarQuery' . self::$ds . 'SugarQuery.php';
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
        if ($this->dbFieldExists('users', 'cxm_agent') && $this->dbFieldExists('users', 'active_user')) {
            //get active users
            $active_users = get_user_array(false, 'Active', '', false, '', " AND is_group=0");
            //get cxm enabled users
            $result = $this->getEnabledUsers();
            if ($result->num_rows > 0) {
                while ($row = $db->fetchByAssoc($result)) {
                    $enabled_active_users[$row['id']] = $row['user_name'];
                }
            }
            $data['enabled_active_users'] = $enabled_active_users;
            $data['active_users'] = $active_users;
        } else {
            $data['isRepaired'] = false;//do quick repair and rebuild
        }
        if (isset($last_validation['last_result']['result']['licensed_user_count']) &&
            !empty($last_validation['last_result']['result']['licensed_user_count']) &&
            isset($sugar_config['outfitters_licenses']) &&
            isset($sugar_config['outfitters_licenses'][$outfitters_config['shortname']])
        ) {
            $data['licensed_user_count'] = $last_validation['last_result']['result']['licensed_user_count'];
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
        return array('data' => $data);
    }

    public function setAgentStatus($api, $args)
    {
        if (isset($_COOKIE['_cxmagentstatus']) && isset($args['AID'])) {
            global $db;
            $status = $_COOKIE['_cxmagentstatus'];
            $id = json_decode(base64_decode($args['AID']));
            $table = 'users';
            $sql = "UPDATE `{$table}` SET `active_requests` = '{$status}' ";
            $sql .= " WHERE ID = '{$id}'";
            // $GLOBALS['log']->fatal('SET AGENT STATUS QUERY : '.$sql);
            $db->query($sql);
        }
    }

    public function getCustomCSS()
    {
        global $sugar_config;
        $filename = $sugar_config['site_url'] . self::$ds . 'custom' . self::$ds . 'themes' . self::$ds . 'custom.css';
        $filecontents = $this->url_get_contents($filename);
        return array('data' => $filecontents);
    }

    public function getVersion()
    {
        global $sugar_config;
        $version = (double)$sugar_config['sugar_version'];
        $class = 'ft1';
        if ($version < 7.6)
            $class .= ' block-footer';
        return array('className' => $class);
    }

    public function url_get_contents($Url)
    {
        if (!function_exists('curl_init')) {
            return;
        }
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $Url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $output = curl_exec($ch);
        curl_close($ch);
        return $output;
    }

    private function getZFID()
    {
        $module = 'rt_Tracker';
        $record_id = '1';
        $bean = BeanFactory::retrieveBean($module, $record_id, array('disable_row_level_security' => true));
        return $bean->zfid;
    }

    private function storeZFID($zfid)
    {
        $module = 'rt_Tracker';
        $record_id = '1';
        $tracker = BeanFactory::getBean($module, $record_id, array('disable_row_level_security' => true));
        $tracker->zfid = $zfid;
        $tracker->save();
    }

    public function getInitialSettings()
    {
        $data = array();
        $data['isRepaired'] = true;
        // $this->dbFieldExists('so_users','user_id') &&
        if ($this->dbFieldExists('users', 'cxm_agent') && $this->dbFieldExists('users', 'active_user')) {
            $data['zfid'] = $this->getZFID();
        } else {
            $data['isRepaired'] = false;//do quick repair and rebuild
        }
        return array('data' => $data);
    }

    //validate license key
    public function validate($api, $args)
    {
        global $sugar_config;

        if (!isset($GLOBALS['currentModule'])) {
            $GLOBALS['currentModule'] = "rt_Tracker";
        }
        $set = false;

        if (isset($args) && isset($args['key'])) {
            $_REQUEST['key'] = $args['key'];
            $set = true;
        }

        $file = 'modules' . self::$ds . self::$module . self::$ds . 'license' . self::$ds . 'OutfittersLicense.php';
        require_once($file);

        $result = OutfittersLicense::validate();

        if ($set && isset($result['data']['validated']) && ($result['data']['validated'])) {
            //CHECK ZFID
            $zfid = $this->getZFID();
            $site_url = $sugar_config['site_url'];

            //ALSO CALL CXM SERVER TO ADD LIC
            $file = __DIR__ . self::$ds . 'rtcxm-helpers' . self::$ds . 'rtcxm_serve.php';
            require_once($file);

            $res = RtCxmServe::curlCall($args['key'], $site_url, $zfid);
            if ($res->success == 'true') {
                //ADD ZFID TO RT TRACKER WITH ID = 1
                $this->storeZFID($res->zfid);
            }
        }
        return $result;
    }

    private function isValidUser($user_id)
    {
        global $db;
        $shortname = 'sales_map';
        $isValid = false;
        // $sql = "SELECT COUNT(`user_id`) AS `count` FROM so_users WHERE `user_id` = '{$user_id}' AND `shortname` = '{$shortname}'";
        $sql = "SELECT COUNT(*) AS `count` FROM config WHERE `category` = 'SugarOutfittersCXM' AND `name` = '{$shortname}'";
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

    public function validateCXMUser()
    {
        $valid = false;
        $user_id = $GLOBALS['current_user']->id;
        // if($this->dbFieldExists('so_users','user_id')){
        $valid = $this->isValidUser($user_id);
        // }
        if (session_status() == PHP_SESSION_DISABLED)
            session_start();
        $_SESSION['_rtvalidatecxm'] = ($valid) ? '1' : '0';
        return array('isValid' => $valid);
    }

    public function validateModule($api, $args)
    {
        global $current_user;

        if (!isset($GLOBALS['currentModule'])) {
            $GLOBALS['currentModule'] = "rt_Tracker";
        }
        $file = 'modules' . self::$ds . 'rt_Tracker' . self::$ds . 'license' . self::$ds . 'OutfittersLicense.php';
        require_once($file);
        return OutfittersLicense::isValid("rt_Tracker", $current_user->id);
    }
}