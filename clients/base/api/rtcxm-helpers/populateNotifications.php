<?php

class PopulateNotifications
{
    public static function populate()
    {
        $date1 = new DateTime();
        $date1->sub(new DateInterval('P1D'));
        $dt = $date1->format('Y-m-d H:i:s');

        $list = array();
        $i = 0;
        $names = array();

        $sql = "SELECT * FROM rt_cxm_notif WHERE date_entered > '{$dt}' ORDER BY date_entered DESC";

        $res = $GLOBALS['db']->query($sql);

        while ($row = $GLOBALS['db']->fetchByAssoc($res)) {
            $arr = explode(" ", $row['about_c']);
            $name = $arr[0];
            if (in_array($name, $names))
                continue;
            $names[] = $name;
            $list[$i] = $row;
            $cookie_id = '';
            if ($row['cookie_id']) {
                $cookie_id = $row['cookie_id'];
            } else {
                $cookie_id = $arr[2];
            }
            $list[$i]['link_id'] = '';
            $sql = "SELECT parent_id, parent_type FROM rt_tracker WHERE cookie_id_c = '{$cookie_id}' ";
            $sql .= "AND parent_id IS NOT NULL LIMIT 0,1";
            $rs = $GLOBALS['db']->query($sql);
            if ($rs->num_rows > 0) {
                $rsw = $GLOBALS['db']->fetchByAssoc($rs);
                $list[$i]['link_id'] = $rsw['parent_id'];
                $list[$i]['link_module'] = $rsw['parent_type'];
            }
            $i++;
        }
        return json_encode($list);
    }
}