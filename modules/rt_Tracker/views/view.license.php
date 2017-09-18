<?php
class rt_TrackerViewLicense extends SugarView{
	function rt_TrackerViewLicense(){
        $dv = new stdClass();
		parent::SugarView();
	}

	public function preDisplay(){
        parent::preDisplay();
    	if (!isset($this->dv))
            $this->dv = new stdClass();
        $this->dv->tpl = 'custom/modules/rt_Tracker/tpls/license.tpl';

	}


	public function display(){
        //ini_set("display_errors",true);
        //error_reporting(E_ALL);
        parent::display();
        
		$ss = new Sugar_Smarty();

        ob_start();
        //include("custom/modules/rt_Tracker/functions.php");
        //$response = json_decode(ob_get_contents(),true);
        require_once("custom/modules/rt_Tracker/functions.php");
        $response = json_decode(getUserConfig(),true);
        ob_end_clean();


  		$ss->assign("title","RTCXM License Configuration Page");
		$mod_strings = return_module_language($GLOBALS['current_language'], 'rt_Tracker');
        $continueURL  = "#Administration";//"";



        //$GLOBALS['log']->fatal(print_r($response['data'],true));

        //$GLOBALS['log']->fatal(print_r($response,true));

        $isRepaired = true;
        $isValidated = true;



        if(isset($response['data']['isRepaired']) && $response['data']['isRepaired'] == false){
            $isRepaired = true;
        }

        if(isset($response['data']['isValidated']) && $response['data']['isValidated'] == false){
            $isValidated = true;
        }
        global $sugar_config;
        global $db;

        $file = 'modules/rt_Tracker/license/config.php';
        require_once($file);
        $admin = new Administration();
        $admin->retrieveSettings();

        $last_validation = $admin->settings['SugarOutfitters_' . $outfitters_config['shortname']];
        $trimmed_last = trim($last_validation); //to be safe...
        $last_validation = base64_decode($last_validation);
        $last_validation = unserialize($last_validation);


        if (isset($last_validation['last_result']['result']['validated']) &&
            !empty($last_validation['last_result']['result']['validated']) &&
            isset($sugar_config['outfitters_licenses']) &&
            isset($sugar_config['outfitters_licenses'][$outfitters_config['shortname']])
        ) {
            $license_key = $sugar_config['outfitters_licenses'][$outfitters_config['shortname']];
        } else {
            $isValidated = false;
        }

        //$GLOBALS['log']->fatal($admin->settings);
        $isAdmin = false;
        if (is_admin($GLOBALS['current_user'])) {
            $isAdmin = true;        
        }
        $active_users = $response['data']['active_users'] ?: array();
        $enabled_users = $response['data']['enabled_active_users'] ?: array();
        $disabled_users = $response['data']['disabled'] ?: array();

        $i = 0;
        $enabled_active_users = array();
        foreach ($enabled_users as $key => $value) {
            $enabled_active_users[$i] = array('id' => $key, 'name' => $value);
            $i++;
        }

        $i = 0;
        $activeUsers = array();
        foreach ($active_users as $key => $value) {
            $activeUsers[$i] = array('id' => $key, 'name' => $value);
            $i++;
        }


        $ss->assign('isAdmin',$isAdmin);
        $ss->assign('isRepaired', $isRepaired);
        $ss->assign('isValidated', $isValidated);
		$ss->assign('MOD', $mod_strings);
        $ss->assign('isAdmin', $isAdmin);
        $ss->assign('enabled_users',$enabled_active_users);
		$ss->assign('active_users', $activeUsers);
        $ss->assign('license_key', $license_key);
        $ss->assign('disabled_users', $disabled_users);

		$ss->display($this->dv->tpl);
	}

}
?>