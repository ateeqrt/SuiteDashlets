<?php

class SmartMessage
{
    public static function autoGenerate($_data)
    {
        $cid = $_data['cid'];
        $name = $_data['name'];
        $assignee = $_data['uname'];

        $sql = "SELECT parent_id, parent_type, flow_c ";
        $sql .= "FROM rt_tracker ";
        $sql .= "WHERE cookie_id_c = '{$cid}' ";
        $sql .= "ORDER BY date_entered DESC ";
        $sql .= "LIMIT 0,1";

        $res = $GLOBALS['db']->query($sql);
        $message = '';
        if ($res->num_rows > 0) {
            $row = $GLOBALS['db']->fetchByAssoc($res);
            $type = $row['parent_type'];
            $flow = $row['flow_c'];
            $flow = str_ireplace('^*', '', $flow);
            if ($flow == '' || (strcasecmp("home", $flow) == 0) || stristr($flow, 'homepage') || stristr($flow, 'about')
                || stristr($flow, 'contact') || stristr($flow, 'checkout') || stristr($flow, 'cart')
            )
                $flow = 'our services,';
            else
                $flow = '"' . $flow . '"';
            // $flow = str_ireplace('|homepage|','',$flow);
            $rep_name = '';
            switch ($type) {
                case 'Leads':
                    $focus = new rt_cxm_chat();
                    $focus->retrieve_by_string_fields(array('name' => 'Leads'));
                    if ($focus->id == '') {
                        $focus->name = "Leads";
                        $focus->message_c = "Hi [Lead Name],
				My name is [Assignee]. If you need more information about [Page Title] then I am the person you want to talk to, lets chat it will only take a minute.
			Regards [Assignee]";
                        $focus->save();
                    }
                    $message = $focus->message_c;
                    $rep_name = '[Lead Name]';
                    $rep_assg = '[Assignee]';
                    break;
                case 'Contacts':
                    $focus = new rt_cxm_chat();
                    $focus->retrieve_by_string_fields(array('name' => 'Contacts'));
                    if ($focus->id == '') {
                        $focus->name = "Contacts";
                        $focus->message_c = "Hi [Contact Name],
				Its good to see you again. I have more information about the [Page Title], lets chat it will only take a minute.
			Regards [Assignee]";
                        $focus->save();
                    }
                    $message = $focus->message_c;
                    $rep_name = '[Contact Name]';
                    $rep_assg = '[Assignee]';
                    break;
                default:
                    $focus = new rt_cxm_chat();
                    $focus->retrieve_by_string_fields(array('name' => 'UNKNOWN'));
                    if ($focus->id == '') {
                        $focus->name = "UNKNOWN";
                        $focus->message_c = "Hi,
				My name is [Current User]. If you need more information about [Page Title] then I am the person you want to talk to, lets chat it will only take a minute.
			Regards [Current User]";
                        $focus->save();
                    }
                    $message = $focus->message_c;
                    $rep_assg = '[Current User]';
                    break;
            }
            if ($rep_name !== '')
                $message = str_replace($rep_name, $name, $message);
            $message = str_replace($rep_assg, $assignee, $message);
            $message = str_replace('[Page Title]', $flow, $message);
        }
        return $message;
    }
}