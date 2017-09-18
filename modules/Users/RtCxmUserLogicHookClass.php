<?php

if (!defined('sugarEntry') || !sugarEntry) die('Not A Valid Entry Point');

class RtCxmUserLogicHookClass
{
    public function afterLoginMethod($bean, $event, $arguments)
    {
        //logic
        $bean->active_user = '1';
        $bean->save();
    }

    public function beforeLogoutMethod($bean, $event, $arguments)
    {
        //logic
        $bean->active_user = '0';
        $bean->save();
    }
}