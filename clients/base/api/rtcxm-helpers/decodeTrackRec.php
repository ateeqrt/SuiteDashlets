<?php

class DecodeTrackRec
{
    public static function decode($_data)
    {
        $action = $_data['action'];
        if ($action == 'extraction') {
            $list = [];
            $date = $_data['date'];
            $sql = "SELECT * FROM rt_tracker ";
            $sql .= "WHERE date_modified > '{$date}' ";
            $sql .= "ORDER BY parent_type DESC, date_entered DESC";
            $res = $GLOBALS['db']->query($sql);
			$unset = array();

            while ($row = $GLOBALS['db']->fetchByAssoc($res)) {
                if (!array_key_exists($row['cookie_id_c'], $list)) {
                    if (empty($row['parent_id']) && !empty($row['parent_type'])) {
                        unset($row['parent_type']);
						$unset[] = $row['id'];
					}
                    $list[$row['cookie_id_c']] = $row;
                    $list[$row['cookie_id_c']]['date_visited'] = explode("T", $row['date_entered'])[0];

                    $flow = explode(" ; ", $row['flow_c']);
                    $flow = explode("^*", $flow[0]);
                    $flow = explode("-*", $flow[0]);
                    $flow = explode("|HomePage|", $flow[0]);
                    $list[$row['cookie_id_c']]['page_visited'] = $flow[0];

                    if (isset($row['parent_type']) && !empty($row['parent_type'])) {
                        $bean = BeanFactory::getBean(
                            $row['parent_type'],
                            $row['parent_id'],
                            array('disable_row_level_security' => true)
                        );
                        if (!isset($bean->id)) {
							unset($list[$row['cookie_id_c']]['parent_type']);
							$unset[] = $row['id'];
						} else
							$list[$row['cookie_id_c']]['parent_name'] = $bean->name;
                    }
                }
            }
			
			if (count($unset) > 0) {
				$unset_str = implode("','", $unset);
				$sql = "UPDATE rt_tracker SET parent_type = NULL WHERE id IN ('{$unset_str}')";
				$GLOBALS['log']->fatal($sql);
				$GLOBALS['db']->query($sql);
			}
            return json_encode($list);
        } elseif ($action == 'tracking') {
            $cookie_id = $_data['cookie_id_c'];
            $track_flow = self::tracking($cookie_id);
            return json_encode($track_flow);
        } elseif ($action === 'gaRecView') {
            $id = $_data['id'];
            $module = $_data['module'];
            $sql = "SELECT cookie_id_c FROM rt_tracker ";
            $sql .= "WHERE parent_id = '{$id}' ";
            $sql .= "AND parent_type = '{$module}' ";
            $sql .= "ORDER BY date_entered";
            $res = $GLOBALS['db']->query($sql);
            $cookie_id_c = '';
            if ($res->num_rows > 0) {
                $row = $GLOBALS['db']->fetchByAssoc($res);
                $cookie_id_c = $row['cookie_id_c'];
            }
            $data['cookie_id_c'] = $cookie_id_c;
            $data['tracks'] = self::tracking($cookie_id_c);
            return json_encode($data);
        }
        return json_encode($action);
    }

    public static function tracking($cookie_id)
    {
        $sql = "SELECT * FROM rt_tracker ";
        $sql .= "WHERE cookie_id_c = '{$cookie_id}' ";
        $sql .= "ORDER BY date_entered";
        $res = $GLOBALS['db']->query($sql);
        $track_flow = [];
        $last_date = '';
        $itr = 0;
        if ($res->num_rows > 0) {
            while ($row = $GLOBALS['db']->fetchByAssoc($res)) {
                $now_date = explode(" ", $row['date_entered'])[0];
                if ($last_date !== '' && $last_date !== $now_date) {
                    $date = new DateTime($track_flow[$itr - 1]['start']);
                    $date->add(new DateInterval('PT1M'));
                    $track_flow[$itr - 1]['end'] = $date->format('Y-m-d H:i:s');
                } elseif ($itr > 0) {
                    $track_flow[$itr - 1]['end'] = $row['date_entered'];
                }
                $last_date = explode(" ", $row['date_entered'])[0];
                $track_flow[$itr]['start'] = $row['date_entered'];
                $track_flow[$itr]['flow'] = $row['flow_c'];
                $itr++;
            }
            if ($itr > 0) {
                $date = new DateTime($track_flow[$itr - 1]['start']);
                $date->add(new DateInterval('PT1M'));
                $track_flow[$itr - 1]['end'] = $date->format('Y-m-d H:i:s');
            }
        }
        return $track_flow;
    }
}