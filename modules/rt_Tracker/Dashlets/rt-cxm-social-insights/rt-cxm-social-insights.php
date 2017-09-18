<?php
if(!defined('sugarEntry') || !sugarEntry) die('Not A Valid Entry Point');


require_once('include/Dashlets/DashletGeneric.php');
require_once('modules/rt_Tracker/rt_Tracker.php');

class rt_TrackerDashlet extends DashletGeneric { 
    public function rt_TrackerDashlet($id, $def = null) {
		global $current_user, $app_strings;
		require('modules/rt_Tracker/metadata/dashletviewdefs.php');

        parent::DashletGeneric($id, $def);

        if(empty($def['title'])) $this->title = translate('LBL_HOMEPAGE_TITLE', 'rt_Tracker');

        $this->searchFields = $dashletData['rt_TrackerDashlet']['searchFields'];
        $this->columns = $dashletData['rt_TrackerDashlet']['columns'];

        $this->seedBean = new rt_Tracker();        
    }

	public function display()
    {	
    	$ss = new Sugar_Smarty();
        $mod_strings = return_module_language($GLOBALS['current_language'], 'rt_Tracker');
            $sData = [];
            $email = '';
            $social_status = '';
            $p1 = false;
            $loc = false;
            //collection initialize
            $sql = 'SELECT * FROM leads ORDER BY date_modified DESC';
            $collection = App.data.createMixedBeanCollection();
            $collection.module_list = ["Contacts", "Leads"];
            $obj = {field: 'date_modified', direction: 'desc'};
            $collection.orderBy = obj;
            $context.set('collection', $collection);
            $flags = ['general', 'facebook', 'twitter', 'google', 'linkedin'];
            $tabs = [];
            $.each($flags, function (i, f) {
                $tabs[f] = false;
            });
            $bean_id = '';
            $bean_type = '';
            $isValid = "<script type='text/javascript'>window.rtvalidatecxm;</script>";
        $ss->assign('MOD', $mod_strings);
    	return parent::display() . $ss->fetch('custom/modules/rt_Tracker/Dashlets/rt-cxm-social-insights/rt-cxm-social-insights.tpl');
    }
    



	public function saveOptions($req) 
    {
        $options = array();
        
        if ( isset($req['title']) ) {
            $options['title'] = $req['title'];
        }
        $options['autoRefresh'] = empty($req['autoRefresh']) ? '0' : $req['autoRefresh'];
        
        return $options;
    }
}