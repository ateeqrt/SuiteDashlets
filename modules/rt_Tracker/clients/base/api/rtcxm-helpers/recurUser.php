<?php

class RecurUser
{
    public static function analysis($cookie_id)
    {
        // $cookie_id = $_POST['cookie_id'];
        $sql = "SELECT parent_type, parent_id FROM rt_tracker WHERE cookie_id_c = '{$cookie_id}' LIMIT 0,1";
        $res = $GLOBALS['db']->query($sql);

        if ($res->num_rows > 0) {
            $row = $GLOBALS['db']->fetchByAssoc($res);
            $parent = array();

            if ($row['parent_type'] === 'Leads' || $row['parent_type'] === 'Contacts') {
                $interval = 'P14D';
                $parent = array('name' => $row['parent_type'], 'id' => $row['parent_id']);
            } else
                $interval = 'P7D';

            $date1 = new DateTime();
            $date1->sub(new DateInterval($interval));
            $dt = $date1->format('Y-m-d H:i:s');

            $sql = "SELECT `flow_c`, `date_entered` FROM `rt_tracker` ";
            $sql .= "WHERE `cookie_id_c` = '{$cookie_id}' AND date_entered > '{$dt}' ";
            $sql .= "AND flow_c NOT LIKE '%|HomePage|%' ";
            $sql .= "AND flow_c NOT LIKE '%Cart%' ";
            $sql .= "AND flow_c NOT LIKE 'Home' ";
            $sql .= "AND flow_c NOT LIKE '%Contact%' ";
            $sql .= "AND flow_c NOT LIKE '%About%' ";
            $sql .= "AND flow_c NOT LIKE '%CheckOut%' ";
            $sql .= "ORDER BY `date_entered` DESC";

            $res = $GLOBALS['db']->query($sql);

            $count = 0;
            $page = '';
            $list = array();
            $dates = array();
            $i = 0;
            $retUser = false;

            if ($res->num_rows > 0) {
                while ($row = $GLOBALS['db']->fetchByAssoc($res)) {
                    if ($i == 0) {
                        $dates[$i] = $row['date_entered'];
                        $i++;
                    } elseif ($i == 1) {
                        $dates[$i] = $row['date_entered'];

                        //COMPARE HOURS
                        $hourdiff = round((strtotime($dates[0]) - strtotime($dates[1])) / 3600, 1);

                        //CHECK THE FLAG
                        if ($hourdiff >= 8)
                            $retUser = true;
                        $i++;
                    }
                    $f = $row['flow_c'];
                    if (stristr($f, "^*")) {
                        $f = str_replace('^*', "", $f);
                    }
                    if (stristr($f, "-*")) {
                        $f = str_replace("-*", "", $f);
                    }
                    if (array_key_exists($f, $list)) {
                        $list[$f] = $list[$f] + 1;
                    } else {
                        $list[$f] = 1;
                    }
                    $count++;
                }
            }

            arsort($list, SORT_NUMERIC);
            $key = key($list);

            $note = "SELECT `status_c` , `page_c` FROM `rt_cxm_notif` " .
                "WHERE `about_c` LIKE '%{$cookie_id}%' ORDER BY date_entered DESC LIMIT 0,1";
            $res = $GLOBALS['db']->query($note);
            $row = $GLOBALS['db']->fetchByAssoc($res);

            $data = array('visits' => $list[$key],
                'page_c' => $row['page_c'],
                'status_c' => $row['status_c'],
                'potential_interest' => $key,
                'returning_user' => $retUser
            );
            $data['notify'] = '';

            if (!empty($parent)) {
                $bean = BeanFactory::getBean(
                    $parent['name'],
                    $parent['id'],
                    array('disable_row_level_security' => true)
                );
                $data['notify'] = $bean->assigned_user_id;
            }

            return json_encode($data);
        }
        // die();
    }
}