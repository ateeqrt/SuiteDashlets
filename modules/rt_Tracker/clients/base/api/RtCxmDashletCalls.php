<?php

if (!defined('sugarEntry') || !sugarEntry) die('Not A Valid Entry Point');

class RtCxmDashletCalls extends SugarApi
{
    private $ds = DIRECTORY_SEPARATOR;
    private $path = __DIR__;

    public function registerApiRest()
    {
        return array(
            'createChatMessage' => array(
                'reqType' => 'GET',
                'path' => array('rtCXM', 'createChatMessage', '?'),
                'pathVars' => array('', '', 'data'),
                'method' => 'createChatMessage',
                'shortHelp' => 'This method creates a chat message record for cxm chat bean.',
                'longHelp' => '',
            ),
            'decodeTrackRec' => array(
                'reqType' => 'GET',
                'path' => array('rtCXM', 'decodeTrackRec', '?'),
                'pathVars' => array('', '', 'data'),
                'method' => 'decodeTrackRec',
                'shortHelp' => 'This method fetches, decodes, and assembles all track history for a visitor.',
                'longHelp' => '',
            ),
            'emailOnId' => array(
                'reqType' => 'GET',
                'path' => array('rtCXM', 'emailOnId', '?'),
                'pathVars' => array('', '', 'id'),
                'method' => 'emailOnId',
                'shortHelp' => 'This method fetches email address for a particular track.',
                'longHelp' => '',
            ),
            'endChat' => array(
                'reqType' => 'GET',
                'path' => array('rtCXM', 'endChat', '?'),
                'pathVars' => array('', '', 'cookie_id'),
                'method' => 'endChat',
                'shortHelp' => 'This method closes the chat session, and grades the session upon its responsiveness.',
                'longHelp' => '',
            ),
            'fetchCstmData' => array(
                'reqType' => 'GET',
                'path' => array('rtCXM', 'fetchCstmData', '?'),
                'pathVars' => array('', '', 'data'),
                'method' => 'fetchCstmData',
                'shortHelp' => 'This method fetches all the chat history for a particular Lead or Contact.',
                'longHelp' => '',
            ),
            'fetchEmailBean' => array(
                'reqType' => 'GET',
                'path' => array('rtCXM', 'fetchEmailBean', '?'),
                'pathVars' => array('', '', 'data'),
                'method' => 'fetchEmailBean',
                'shortHelp' => 'This method takes an email address and fetches information against it on social profiles (e.g. Google, Twitter, etc).',
                'longHelp' => '',
            ),
            'googleAnalyticsReport' => array(
                'reqType' => 'GET',
                'path' => array('rtCXM', 'googleAnalyticsReport', '?'),
                'pathVars' => array('', '', 'data'),
                'method' => 'googleAnalyticsReport',
                'shortHelp' => 'This method fetches a visitor\'s information from google analytics based on filters provided.',
                'longHelp' => '',
            ),
            'populateNotifications' => array(
                'reqType' => 'GET',
                'path' => array('rtCXM', 'populateNotifications'),
                'pathVars' => array('', ''),
                'method' => 'populateNotifications',
                'shortHelp' => 'This method retrieves, and assembles notifications for Chat element.',
                'longHelp' => '',
            ),
            'recurUser' => array(
                'reqType' => 'GET',
                'path' => array('rtCXM', 'recurUser', '?'),
                'pathVars' => array('', '', 'cookie_id'),
                'method' => 'recurUser',
                'shortHelp' => 'This method checks for a returning visitor and their most visited pages.',
                'longHelp' => '',
            ),
            'SaveFilePut' => array(
                'reqType' => 'GET',
                'path' => array('rtCXM', 'SaveFilePut', '?'),
                'pathVars' => array('', '', 'rtcxm'),
                'method' => 'SaveFilePut',
                'shortHelp' => 'This method converts the profile image from a URL to the image type for Sugar.',
                'longHelp' => '',
            ),
            'smartMessage' => array(
                'reqType' => 'GET',
                'path' => array('rtCXM', 'smartMessage', '?'),
                'pathVars' => array('', '', 'data'),
                'method' => 'smartMessage',
                'shortHelp' => 'This method returns an auto-generated message to suggest an opening message from user to visitor.',
                'longHelp' => '',
            ),
            'trackDecode' => array(
                'reqType' => 'GET',
                'path' => array('rtCXM', 'trackDecode', '?'),
                'pathVars' => array('', '', 'data'),
                'method' => 'trackDecode',
                'shortHelp' => 'This method decodes social information for a single profile.',
                'longHelp' => '',
            ),
            'trackerParent' => array(
                'reqType' => 'GET',
                'path' => array('rtCXM', 'trackerParent', '?'),
                'pathVars' => array('', '', 'id'),
                'method' => 'trackerParent',
                'shortHelp' => 'This method fetches parent for the given track.',
                'longHelp' => '',
            ),
            'Validate' => array(
                'reqType' => 'GET',
                'path' => array('rtCXM', 'Validate'),
                'pathVars' => array('', ''),
                'method' => 'validate',
                'shortHelp' => 'This method validates the session.',
                'longHelp' => '',
            ),
        );
    }

    //notify + cxm-chat
    public function createChatMessage($api, $args)
    {
        $isValid = json_decode($this->validate(), true);
        if ($isValid['isValid'] && $isValid['cxmValid']) {
            $file = $this->path . $this->ds . 'rtcxm-helpers' . $this->ds . 'createChatMessage.php';
            if (isset($args) && isset($args['data'])) {
                $_data = json_decode(base64_decode($args['data']), true);
                require $file;
                return CreateChatMessage::create($_data);
            }
        } else {
            return $this->badRequest($isValid);
        }
    }

    //track-flow
    public function decodeTrackRec($api, $args)
    {
        $isValid = json_decode($this->validate(), true);
        if ($isValid['isValid'] && $isValid['cxmValid']) {
            $file = $this->path . $this->ds . 'rtcxm-helpers' . $this->ds . 'decodeTrackRec.php';
            if (isset($args) && isset($args['data'])) {
                require $file;
                $_data = json_decode(base64_decode($args['data']), true);
                return DecodeTrackRec::decode($_data);
            }
        } else {
            return $this->badRequest($isValid);
        }
    }

    public function emailOnId($api, $args)
    {
        $isValid = json_decode($this->validate(), true);
        if ($isValid['isValid'] && $isValid['cxmValid']) {
            if (isset($args) && isset($args['id'])) {
                $id = json_decode(base64_decode($args['id']));
                $id = $_POST['id'];
                $track = BeanFactory::retrieveBean("rt_Tracker", $id, array('disable_row_level_security' => true));
                return json_encode($track->email_c);
            }
        } else {
            return $this->badRequest($isValid);
        }
    }

    public function endChat($api, $args)
    {
        $file = $this->path . $this->ds . 'rtcxm-helpers' . $this->ds . 'cxmApiHelper.php';
        $cookie_id = json_decode(base64_decode($args['cookie_id']), 1);
        if (isset($cookie_id) && $cookie_id) {
            require $file;
            $cxmhelper = new CxmApiHelper();
            $cxmhelper->endChat($cookie_id);
        }
    }

    // chat history
    public function fetchCstmData($api, $args)
    {
        $isValid = json_decode($this->validate(), true);
        if ($isValid['isValid'] && $isValid['cxmValid']) {
            if (isset($args) && isset($args['data'])) {
                $_data = json_decode(base64_decode($args['data']), true);
                $module = $_data['module'];
                $sql = '';

                if ($module == 'rt_cxm_notif') {
                    $date = $_data['date'];
                    $dt = new DateTime($date);
                    $date = $dt->format('Y-m-d H:i:s');
                    $sql = "SELECT * FROM rt_cxm_notif WHERE date_entered > '{$date}'";

                } elseif ($module == 'rt_cxm_chat') {
                    $id = $_data['id'];
                    $sql = "SELECT * FROM rt_cxm_chat WHERE client_no_c = '{$id}' ORDER BY date_entered";
                }
                $list = array();

                if ($sql !== '') {
                    $res = $GLOBALS['db']->query($sql);
                    while ($row = $GLOBALS['db']->fetchByAssoc($res)) {
                        $list[] = $row;
                    }
                }
                return json_encode($list);
            }
        } else {
            return $this->badRequest($isValid);
        }
    }

    //social-insights
    public function fetchEmailBean($api, $args)
    {
        $isValid = json_decode($this->validate(), true);
        if ($isValid['isValid'] && $isValid['cxmValid']) {
            $file = $this->path . $this->ds . 'rtcxm-helpers' . $this->ds . 'fetchEmailBean.php';
            if (isset($args) && isset($args['data'])) {
                $_data = json_decode(base64_decode($args['data']), true);
                require $file;
                return FetchEmailBean::fetch($_data);
            }
        } else {
            return $this->badRequest($isValid);
        }
    }

    //social-insights
    public function googleAnalyticsReport($api, $args)
    {
        $isValid = json_decode($this->validate(), true);
        if ($isValid['isValid'] && $isValid['cxmValid']) {
            $file = $this->path . $this->ds . 'rtcxm-helpers' . $this->ds . 'googleAnalyticsReport.php';

            if (isset($args) && isset($args['data'])) {
                $_data = json_decode(base64_decode($args['data']), true);
                require $file;
                return GoogleAnalyticsReport::fetch($_data);
            }
        } else {
            return $this->badRequest($isValid);
        }
    }

    //cxm-chat
    public function populateNotifications($api, $args)
    {
		// $GLOBALS['log']->fatal(print_r($isValid, true));
        $isValid = json_decode($this->validate(), true);
        if ($isValid['isValid'] && $isValid['cxmValid']) {
            $file = $this->path . $this->ds . 'rtcxm-helpers' . $this->ds . 'populateNotifications.php';
            require $file;
            return PopulateNotifications::populate();
        } else {
            return $this->badRequest($isValid);
        }
    }

    //js groupings
    public function recurUser($api, $args)
    {
        $isValid = json_decode($this->validate(), true);
        if ($isValid['isValid'] && $isValid['cxmValid']) {
            $file = $this->path . $this->ds . 'rtcxm-helpers' . $this->ds . 'recurUser.php';
            if (isset($args) && isset($args['cookie_id'])) {
                $cookie_id = json_decode(base64_decode($args['cookie_id']));
                require $file;
                return RecurUser::analysis($cookie_id);
            }
        } else {
            return $this->badRequest($isValid);
        }
    }

    public function SaveFilePut($api, $args)
    {
        $isValid = json_decode($this->validate(), true);
        if ($isValid['isValid'] && $isValid['cxmValid']) {
            $file = 'clients' . $this->ds . 'base' . $this->ds . 'api' . $this->ds . 'FileApi.php';
            if (isset($args) && isset($args['rtcxm'])) {
                require $file;
                $obj = new FileApi();
                $data = json_decode(base64_decode($args['rtcxm']), true);
                $args['record'] = $data['id'];
                $args['field'] = $data['field'];
                $args['module'] = $data['module'];
                $args['url'] = $data['url'];
                $result = $obj->saveFilePut($api, $args, $args['url']);
            }
        } else {
            return $this->badRequest($isValid);
        }
    }

    //notify + cxm-chat
    public function smartMessage($api, $args)
    {
        $isValid = json_decode($this->validate(), true);
        if ($isValid['isValid'] && $isValid['cxmValid']) {
            $file = $this->path . $this->ds . 'rtcxm-helpers' . $this->ds . 'smartMessage.php';
            if (isset($args) && isset($args['data'])) {
                $_data = json_decode(base64_decode($args['data']), true);
                require $file;
                return SmartMessage::autoGenerate($_data);
            }
        } else {
            return $this->badRequest($isValid);
        }
    }

    //social-insights
    public function trackDecode($api, $args)
    {
        $isValid = json_decode($this->validate(), true);
        if ($isValid['isValid'] && $isValid['cxmValid']) {
            $file = $this->path . $this->ds . 'rtcxm-helpers' . $this->ds . 'trackDecode.php';
            if (isset($args) && isset($args['data'])) {
                $_data = json_decode(base64_decode($args['data']), true);
                require $file;
                return TrackDecode::decode($_data);
            }
        } else {
            return $this->badRequest($isValid);
        }
    }

    //chat history
    public function trackerParent($api, $args)
    {
        $isValid = json_decode($this->validate(), true);
        if ($isValid['isValid'] && $isValid['cxmValid']) {
            if (isset($args) && isset($args['id'])) {
                $id = json_decode(base64_decode($args['id']));

                $data = array('cid' => '', 'user_name' => '');

                $bean = BeanFactory::getBean('rt_Tracker');
                $bean->retrieve_by_string_fields(array('parent_id' => $id));

                if ($bean->cookie_id_c) {
                    $data['cid'] = $bean->cookie_id_c;
                    $chat = BeanFactory::getBean('rt_cxm_chat');
                    $chat->retrieve_by_string_fields(array('client_no_c' => $bean->cookie_id_c));

                    if ($chat->assigned_user_id) {
                        $user = BeanFactory::getBean('Users', $chat->assigned_user_id);
                        $data['user_name'] = $user->user_name;
                    }
                }
                return json_encode($data);
            }
        } else {
            return $this->badRequest($isValid);
        }
    }

    public function validate()
    {
        if (isset($_SESSION) && isset($_SESSION['rtvalidatecxm']) && isset($_SESSION['rtcxm_valid'])) {
            $date = $_SESSION['rtcxm_valid'];
            $datenow = new DateTime();
            if ($datenow > $date) {
                //validity time has passed
				$res = $this->setVdy();
                return json_encode(array('isValid' => $res['result'], 'cxmValid' => $res['cxm']));
            } else {
                //still valid
                return json_encode(array('isValid' => true));
            }
        } else {
			$res = $this->setVdy();
			return json_encode(array('isValid' => $res['result'], 'cxmValid' => $res['cxm']));
        }
    }

    private function setVdy()
    {
        $isValid = $this->callvalidate();
        if ($isValid['result'] && $isValid['cxm']) {
            $_SESSION['rtvalidatecxm'] = '1';
        } else {
            $_SESSION['rtvalidatecxm'] = '0';
        }
        $date = new DateTime();
        $date->add(new DateInterval('P1D'));
        $_SESSION['rtcxm_valid'] = $date;
        return $isValid;
    }

    private function callvalidate()
    {
        global $current_user;
        if (!isset($GLOBALS['currentModule'])) {
            $GLOBALS['currentModule'] = "rt_Tracker";
        }
        $file = 'modules' . $this->ds . 'rt_Tracker' . $this->ds . 'license' . $this->ds . 'OutfittersLicense.php';
        require_once($file);
        $result = OutfittersLicense::isValid("rt_Tracker", $current_user->id);
		$result2 = $this->cxmValid();
        if ($result !== true) {
            $result = false;
        }
        return array('result' => $result, 'cxm' => $result2);
    }
	
	private function cxmValid()
	{
		$bean = BeanFactory::getBean("rt_Tracker", "1", array('disable_row_level_security' => true));
		if (!empty($bean->zfid))
			return true;
		return false;
	}
	
	private function badRequest($isValid)
	{
		header('HTTP/1.1 400 Bad Request');
		// return $isValid;
		$err = array('isValid' => false, 'msg' => 'LBL_CXM_LIC_NV');
		if ($isValid['isValid'] && !$isValid['cxmValid'])
			$err['msg'] = 'LBL_CXM_NV';
		return $err;
	}
}
