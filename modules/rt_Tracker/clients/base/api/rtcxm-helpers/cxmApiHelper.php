<?php

class CxmApiHelper
{
    private function getSessionID($cookie_id)
    {
        $table = 'rt_cxm_chat';
        $sql = "SELECT `session_id` FROM `{$table}` ";
        $sql .= "WHERE `client_no_c` = '{$cookie_id}' ";
        $sql .= "ORDER BY `date_entered` DESC LIMIT 0,1";
        $result = $GLOBALS['db']->query($sql);
        // $GLOBALS['log']->fatal('QUERY : '.$sql);
        $id = '';
        if ($result->num_rows > 0) {
            $row = $GLOBALS['db']->fetchByAssoc($result);
            if ($row['session_id'])
                $id = $row['session_id'];
        }
        return $id;
    }

    private function makeReport($session_id)
    {
        //GET SESSION CHAT
        $table = 'rt_cxm_chat';
        $sql = "SELECT * FROM `{$table}` WHERE `session_id` = '{$session_id}'";

        // $GLOBALS['log']->fatal('QUERY : '.$sql);
        $result = $GLOBALS['db']->query($sql);
        $rtcxm = false;
        $agent = false;
        $visitor = false;

        //LOOP THROUGH MESSAGES
        // $GLOBALS['log']->fatal('num of rows fetched : '.$result->num_rows);
        while ($row = $GLOBALS['db']->fetchByAssoc($result)) {

            // $GLOBALS['log']->fatal(print_r($row,true));
            if (stristr($row['sender_c'], 'RtCXMOperator2227')) {
                $rtcxm = true;
            } elseif (stristr($row['sender_c'], 'user')) {
                $agent = true;
            } elseif (stristr($row['sender_c'], 'visitor')) {
                $visitor = true;
            }
        }
        $report = '';

        //IF SENDER HAS RTCXM
        if ($rtcxm) {

            //IF SENDER ALSO HAS {{SUGAR_USER}}
            if ($agent) {

                //REPORT = POORLY RESPONDANT
                $report = 'LBL_CHAT_REPORT_POOR';
            } else {

                //REPORT = NON-RESPONDANT AT ALL
                $report = 'LBL_CHAT_REPORT_BAD';
            }
        } elseif ($agent) {

            //REPORT = RESPONSIVE CHAT
            $report = 'LBL_CHAT_REPORT_GOOD';
        } elseif ($visitor) {

            //REPORT = NON-RESPONDANT AT ALL
            $report = 'LBL_CHAT_REPORT_BAD';
        } else {
            $report = 'LBL_CHAT_REPORT_NA';
        }
        return $report;
    }

    private function setReport($session_id, $report)
    {
        $table = 'rt_cxm_chat';
        $sql = "UPDATE `{$table}` ";
        $sql .= "SET `session_status` = 'CLOSED', `report` = '{$report}' ";
        $sql .= "WHERE `session_id` = '{$session_id}'";
        // $GLOBALS['log']->fatal('QUERY : '.$sql);
        $GLOBALS['db']->query($sql);
    }

    public function endChat($cookie_id)
    {

        //GET SESSION ID
        $id = $this->getSessionID($cookie_id);

        //GENERATE REPORT
        $report = $this->makeReport($id);

        //UPDATE DB
        $this->setReport($id, $report);
    }
}