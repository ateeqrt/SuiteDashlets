<?php

class CreateChatMessage
{
    public static function create($_data)
    {
        $client_no_c = $_data['client_no_c'];
        $message_c = $_data['message_c'];
        $name = $_data['name'];
        $sender = $_data['sender'];
        $sender_id = $_data['sender_id'];
        $first = $_data['first'];
		
		require_once('include/SugarQuery/SugarQuery.php');
		$SugarQuery = new SugarQuery();
		$SugarQuery->from(BeanFactory::newBean('rt_cxm_chat'));
		$SugarQuery->select(array('assigned_user_id'));
		$SugarQuery->where()->contains('client_no_c', $client_no_c);
		$SugarQuery->limit(1);
		$result = $SugarQuery->execute();
		if (isset($result[0])) {
			if (!empty($result[0]['assigned_user_id']) && $result[0]['assigned_user_id'] !== $sender_id) {
				header('HTTP/1.1 400 Bad Request');
				return 'LBL_CNT_REPLY';
			}
		}

        $chat_bean = BeanFactory::newBean("rt_cxm_chat");
        $chat_bean->message_c = $message_c;
        $chat_bean->sender_c = "user:" . $sender;
        $chat_bean->client_no_c = $client_no_c;
        $chat_bean->assigned_user_id = $sender_id;

        $chat_bean->save();

        //UPDATE RELATED NOTIFICATIONS TO CHAT STARTED IF IT'S THE FIRST MESSAGE
        if ($first) {
            $sql = "UPDATE rt_cxm_notif SET status_c = 'Chat Started' WHERE about_c LIKE '%{$client_no_c}%'";
            $res = $GLOBALS['db']->query($sql);
        }

        //UNKNOWN VISITOR
        // if (stristr($name, "visitor")) {

            //check for lead existence
            $sql = "SELECT `parent_id`, `parent_type` ";
            $sql .= "FROM rt_tracker WHERE `cookie_id_c` = '{$client_no_c}' ";
            $sql .= "AND `parent_id` IS NOT NULL LIMIT 0,1";
            $res = $GLOBALS['db']->query($sql);

			$create = false;
			if ($res->num_rows < 1)
				$create = true;
			else {
				$row = $GLOBALS['db']->fetchByAssoc($res);
				if (empty($row['parent_id']) || empty($row['parent_type']))
					$create = true;
			}
			if ($create) {
				$user = new User();
				$user->retrieve_by_string_fields(array('is_admin'=>'1'));
				$team = new Team();
				$team->retrieve_by_string_fields(array('name'=>'CXM Website Visitor'));
				
                $lead = new Lead();
                $lead->first_name = explode(" ", $name)[0];
                $lead->last_name = explode(" ", $name)[1];
				$lead->assigned_user_id = (!empty($sender_id)) ? $sender_id : $user->id;
				$lead->load_relationship('teams');
				$lead->teams->add(array($team->id));
                $lead->lead_source = 'Web Site';
                $lead->save();

                //UPDATE TRACKERS' PARENT ID AND TYPE
                $sql = "UPDATE rt_tracker SET `parent_id` = '{$lead->id}', `parent_type` = 'Leads' ";
                $sql .= "WHERE `cookie_id_c` = '{$client_no_c}'";
                $res = $GLOBALS['db']->query($sql);

                //UPDATE CHATS' ASSIGNED USER
                $inner_sql = "SELECT id FROM rt_cxm_chat WHERE client_no_c = '{$client_no_c}'";
                $sql = "UPDATE rt_cxm_chat SET `assigned_user_id` = '{$sender_id}' ";
                $sql .= "WHERE client_no_c = '{$client_no_c}'";//WHERE `id` IN '({$inner_sql})'";
                $res = $GLOBALS['db']->query($sql);
            }
        // }
    }
}