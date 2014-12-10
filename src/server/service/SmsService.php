<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 14-10-9
 * Time: 下午2:14
 */

namespace message;


class SmsService {
    const TOKEN = "8002200130187412";
    public function sendSMS($mobile, $content, $token)
    {
        if( ! $mobile)
        {
            return false;
        }
        if( ! trim($content))
        {
            return false;
        }
        $url = 'http://www.wemediacn.net/webservice/smsservice.asmx/SendSMS?mobile='.$mobile.'&FormatID=8&Content='.urlencode($content).'&ScheduleDate=2010-1-1&TokenID=' . $token;
        $cu = curl_init();
        curl_setopt($cu, CURLOPT_URL, $url);
        curl_setopt($cu, CURLOPT_RETURNTRANSFER, 1);
        $ret = curl_exec($cu);
        curl_close($cu);
        return $ret;
    }

}

//$obj = new SmsService();
//$content = '感谢您参与倩碧真男人特供活动，恭喜您成功获得倩碧真男人特供旅行装一份。即日起请于2周内，凭该短信到XXXXXX专柜领取礼品。该短信转发无效。';
//$mobile = '15821927031';
//$token = '8002200130187412';
//$ret = $obj->sendSMS($mobile, $content, $token);
//echo '<br>Found.<br>'.$ret;