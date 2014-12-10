<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 14-8-13
 * Time: 上午8:32
 */
namespace customer;
require_once(dirname(__FILE__) ."/../database/DbConfig.php");
require_once(dirname(__FILE__) ."/../config/AppConfig.php");

require_once(dirname(__FILE__) . "/../dao/SuccessLogPDO.php");
require_once(dirname(__FILE__) . "/../dao/SupportPDO.php");
require_once(dirname(__FILE__) . "/../service/SmsService.php");
require_once(dirname(__FILE__) . "/../service/MacService.php");

use \PDO;
use \PDOException;
use database\DbConfig;
use message\SmsService;
use security\MacService;

header('Content-Type: application/json; charset=utf-8');

$requestObj = json_decode(file_get_contents("php://input"));
$type = $requestObj->type;
$logService = new SuccessLogService();

if ($type === 'CHECK') {
    $logService->checkSuccess($requestObj->data);
} else if ($type === 'ADD') {
    $logService->logSuccess($requestObj->data);
}





class SuccessLogService {
    public function checkSuccess($requestObj) {
        $openid = $requestObj->openid;
        if ($openid == null) {
            echo json_encode(array('success'=>false, 'type'=>'warn', 'code'=>'E001', 'request'=>$requestObj, 'data'=>null, 'message'=>"请返回首页重新登录授权!"));
            return;
        }
        $supportPDO = new SupportPDO();
        $ret = $supportPDO->findNumberOfSupport($openid);
        if (null === $ret || $ret->numberOfSupport < 3) {
            echo json_encode(array('success'=>false, 'type'=>'warn', 'code'=>'E003', 'request'=>$requestObj, 'data'=>$ret, 'message'=>"未达到3位好友认同，<br>无法领奖！"));
            return;
        }

        $successLogPDO = new SuccessLogPDO();
        $ret = $successLogPDO->findNumberOfWinner();
        if (null !== $ret && $ret->numberOfWinner > 3000) {
            echo json_encode(array('success'=>false, 'type'=>'warn', 'code'=>'E005', 'request'=>$requestObj, 'data'=>$ret, 'message'=>"真男人特供旅行装已领完!"));
            return;
        }

        $ret = $successLogPDO->findNumberOfWinnerBetweenServerTime(date("Y-m-d"), date("Y-m-d",strtotime("+1 day")));
        if (null !== $ret && $ret->numberOfWinner > 300) {
            echo json_encode(array('success'=>false, 'type'=>'warn', 'code'=>'E002', 'request'=>$requestObj, 'data'=>$ret, 'message'=>"真男人，今日特供已满，<br>明天再来试试吧！"));
            return;
        }

        $find = $successLogPDO->findByOpenId($openid);
        if ($find) {
            echo json_encode(array('success'=>false, 'type'=>'warn', 'code'=>'E003', 'request'=>$requestObj, 'data'=>$find, 'message'=>"您已经提交过领奖信息，<br>请耐心等待领奖短信！"));
        } else {
            echo json_encode(array('success'=>true, 'type'=>'info', 'request'=>$requestObj, 'data'=>$find, 'message'=>"Success"));
        }

    }

    public function logSuccess($requestObj) {
        $openid = $requestObj->openid;
        if ($openid == null) {
            echo json_encode(array('success'=>false, 'type'=>'warn', 'code'=>'E001', 'request'=>$requestObj, 'data'=>null, 'message'=>"请返回首页重新登录授权!"));
            return;
        }
        $provinceCode = $requestObj->provinceCode;
        $provinceName = $requestObj->provinceName;
        $cityCode = $requestObj->cityCode;
        $cityName = $requestObj->cityName;
        $siteCode = $requestObj->siteCode;
        $siteName = $requestObj->siteName;
        $cellphone  = $requestObj->cellphone;
        if ($this->isInvalidInput($provinceCode) || $this->isInvalidInput($provinceName) ||
            $this->isInvalidInput($cityCode) || $this->isInvalidInput($cityName) ||
            $this->isInvalidInput($siteCode) || $this->isInvalidInput($siteName) ||
            $this->isInvalidInput($cellphone)) {
            echo json_encode(array('success'=>false, 'type'=>'warn', 'code'=>'E001', 'request'=>$requestObj, 'data'=>null, 'message'=>"填写完整信息！"));
            return;
        }
        $successLogPDO = new SuccessLogPDO();

        $find = $successLogPDO->findByOpenId($openid);
        if ($find) {
            echo json_encode(array('success'=>false, 'type'=>'warn', 'code'=>'E003', 'request'=>$requestObj, 'data'=>$find, 'message'=>"您已经提交过领奖信息，<br>请耐心等待领奖短信！"));
            return;
        }

        $supportPDO = new SupportPDO();
        $ret = $supportPDO->findNumberOfSupport($openid);
        if (null === $ret || $ret->numberOfSupport < 3) {
            echo json_encode(array('success'=>false, 'type'=>'warn', 'code'=>'E003', 'request'=>$requestObj, 'data'=>$ret, 'message'=>"未达到3位好友认同，<br>无法领奖！"));
            return;
        }

        $ret = $successLogPDO->findNumberOfWinner();
        if (null !== $ret && $ret->numberOfWinner > 3000) {
            echo json_encode(array('success'=>false, 'type'=>'warn', 'code'=>'E005', 'request'=>$requestObj, 'data'=>$ret, 'message'=>"真男人特供旅行装已领完！"));
            return;
        }

        $ret = $successLogPDO->findNumberOfWinnerBetweenServerTime(date("Y-m-d"), date("Y-m-d",strtotime("+1 day")));
        if (null !== $ret && $ret->numberOfWinner > 200) {
            echo json_encode(array('success'=>false, 'type'=>'warn', 'code'=>'E002', 'request'=>$requestObj, 'data'=>$ret, 'message'=>"真男人，今日特供已满，<br>明天再来试试吧！"));
            return;
        }

        $ret = $successLogPDO->checkNumberOfWinnerLessThanAllocation($siteCode);
        if (null !== $ret && !$ret->isValid) {
            echo json_encode(array('success'=>false, 'type'=>'warn', 'code'=>'E004', 'request'=>$requestObj, 'data'=>$ret, 'message'=>"该专柜真男人特供旅行装已领完，<br>请选择其他倩碧专柜！"));
            return;
        }
        $macService = new MacService(PHP_OS);
        $ipAddress = $macService->getIp();
        $ret = $successLogPDO->insertSingleRowWithIpServerTime($openid, $provinceCode, $provinceName, $cityCode, $cityName, $siteCode, $siteName, $cellphone, $ipAddress, date("Y-m-d H:i:s", time()));
        if ($ret) {
            $smsService = new SmsService();
            $content = '恭喜您成功获得倩碧真男人特供旅行装一份。即日起请于2周内，凭该短信到'.$siteName.'专柜领取礼品。该短信转发无效。';
            $status = $smsService->sendSMS($cellphone, $content, SmsService::TOKEN);
            $successLogPDO->updateStatus($openid, $status);
            echo json_encode(array('success'=>true, 'type'=>'info', 'request'=>$requestObj, 'data'=>$ret, 'message'=>"success"));
        } else {
            echo json_encode(array('success'=>false, 'type'=>'warn', 'code'=>'E001', 'request'=>$requestObj, 'data'=>$ret, 'message'=>"服务器繁忙，<br>请稍后再来！"));
        }
    }

    public function isInvalidInput($input) {
        if (null === $input || 'null' === $input || '' === trim($input) || '-1' === $input) {
            return true;
        }
        return false;
    }
}

