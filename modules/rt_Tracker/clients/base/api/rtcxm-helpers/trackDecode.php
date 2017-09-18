<?php

class TrackDecode
{
    public static function decode($_data)
    {
        $type = $_data['type'];
		$t = $type;
		if($type == 'general')
			$t = 'generic';
        $data = $_data['data'];
		$sql = "SELECT {$t} FROM rt_cxm_email WHERE email = '{$data}'";
		$res = $GLOBALS['db']->query($sql);
		$rt = array();
		if($res->num_rows > 0)
			$rt = $GLOBALS['db']->fetchByAssoc($res);
        $send = self::getData($type, $rt[$t]);
        return json_encode($send);
    }

    public static function getData($type, $adata)
    {
        $data = array();
        switch ($type) {
            case 'general':
                $general = unserialize(base64_decode($adata));
                $data['Name'] = $general['details']['name'];
                $data['Location'] = $general['details']['location'];
                $data['Social Accounts'] = [];
                foreach ($general['socialslist'] as $account) {
                    if ($account == "twitter") {
                        $dc = '<a name="cxm-twitter" id="cxm-2" data-action="change_tab">' . ucfirst($account) . '</a>';
                    } elseif ($account == "facebook") {
                        $dc = '<a name="cxm-facebook" id="cxm-3" data-action="change_tab">' . ucfirst($account) . '</a>';
                    } elseif (stristr($account, "google")) {
                        $dc = '<a name="cxm-google" id="cxm-4" data-action="change_tab">' . ucfirst($account) . '</a>';
                    } elseif ($account == "linkedin") {
                        $dc = '<a name="cxm-linkedin" id="cxm-5" data-action="change_tab">' . ucfirst($account) . '</a>';
                    } else {
                        $dc = ucfirst($account);
                    }
                    $data['Social_Accounts'][] = $dc;
                }
                break;
            case 'twitter':
                $twitter = unserialize(base64_decode($adata));
                if ($twitter) {
                    $data['Name'] = $twitter['personal']['name'];
                    $data['Location'] = $twitter['personal']['location'];
                    $data['Sname'] = $twitter['personal']['screen_name'];
                    $data['dp'] = ($twitter['personal']['display_picture']) ? $twitter['personal']['display_picture'] : '';
                    $data['Bio'] = $twitter['personal']['decription'];
                    $data['Followers'] = $twitter['personal']['followers_count'];
                    $data['Following'] = $twitter['personal']['friends_count'];
                    $data['verified'] = $twitter['personal']['verified'];
                    $data['ufollow'] = $twitter['personal']['following'];
                    $data['Tags'] = array();
                    $tags = array();
                    $used_tags = array_merge($twitter['tags'], $twitter['favorites']['tags']);
                    foreach ($used_tags as $tag) {
                        $tag = strtolower($tag);
                        if (array_key_exists($tag, $tags)) {
                            $tags[$tag] += 1;
                        } else {
                            $tags[$tag] = 1;
                        }
                    }
                    arsort($tags, SORT_NUMERIC);
                    foreach ($tags as $tag => $counts) {
                        $data['Tags'][] = $tag;
                    }

                    $data['Mentions'] = array();
                    $mentions = array();
                    $ments = array_merge($twitter['mentions'], $twitter['favorites']['by']);
                    foreach ($ments as $atag) {
                        $atag = strtolower($atag);
                        if (array_key_exists($atag, $mentions)) {
                            $mentions[$atag] += 1;
                        } else {
                            $mentions[$atag] = 1;
                        }
                    }
                    arsort($mentions, SORT_NUMERIC);
                    foreach ($mentions as $tag => $counts) {
                        $data['Mentions'][] = $tag;
                    }
                    $data['Trends'] = array_slice($twitter['trends'], 0, 10);
                } else {
                    return;
                }
                break;
            case 'facebook':
                $facebook = unserialize(base64_decode($adata));
                if ($facebook) {
                    $data['Name'] = $facebook['personal']['name'];
                    $data['Is_verified'] = $facebook['personal']['is_verified'];

                    if ($facebook['type'] === 'user') {
                        $data['Link'] = $facebook['personal']['link'];
                    }
                } else {
                    return;
                }
                break;
            case 'google':
                $google = unserialize(base64_decode($adata));
                if ($google) {
                    $data['Name'] = $google['displayName'];
                    $data['AboutMe'] = $google['aboutMe'];
                    foreach ($google as $key => $value) {
                        if ($key === 'etag')
                            continue;
                        if ($key === "displayName" || $key === "aboutMe")
                            continue;
                        if ($key === "other_details") {
                            if (isset($google[$key]['image']['url'])) {
                                $data['dp'] = $google[$key]['image']['url'];
                            }
                        }
                        $key = ucfirst($key);
                        if ($key == 'IsPlusUser') {
                            $value = '<i class="fa fa-check"/>';
                        }
                        $data[$key] = $value;
                    }
                } else {
                    return;
                }
                break;
            case 'linkedin':
                $linkedin = unserialize(base64_decode($adata));
                if ($linkedin) {
                    $data['Name'] = $linkedin['username'];
                    foreach ($linkedin as $key => $value) {
                        if ($key === "id" || $key === "username")
                            continue;
                        $key = ucfirst($key);
                        $data[$key] = $value;
                    }
                } else {
                    return '';
                }
                break;
        }
        return $data;
    }
}