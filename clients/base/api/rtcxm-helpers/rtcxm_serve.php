<?php

class RtCxmServe
{
    public static function curlCall($id, $site_url, $zfid = '')
    {
        $url = 'https://rtcxmneon.rolustech.com/customerService/setLicense?license_id=';
        $url .= $id . '&sugar_url=' . $site_url . '&zfid=' . $zfid;
        $curl_request = curl_init();

        curl_setopt($curl_request, CURLOPT_URL, $url);
        curl_setopt($curl_request, CURLOPT_HEADER, false);
        curl_setopt($curl_request, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($curl_request, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl_request, CURLOPT_FOLLOWLOCATION, 0);
        curl_setopt($curl_request, CURLOPT_SSL_VERIFYPEER, false);

        $result = curl_exec($curl_request);

        if (curl_errno($curl_request)) {
            echo 'Curl error: ' . curl_errno($curl_request) . " : " . curl_error($curl_request);
            curl_close($curl_request);
            die("0");
        }

        curl_close($curl_request);
        return json_decode($result);
    }
}