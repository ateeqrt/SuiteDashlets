<?php

class rt_TrackerController extends SugarController {



	function action_license()
    {
        $this->view = "license";  //call for the view file in views dir
    }
    function action_saveLicense(){
		SugarApplication::redirect('index.php?module=rt_Tracker&action=saveLicense');		
    }
    function action_functions(){
		SugarApplication::redirect('index.php?module=rt_Tracker&action=functions');		
    }
}
?>
