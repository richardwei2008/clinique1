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

require_once(dirname(__FILE__) . "/../dao/SupportPDO.php");
require_once(dirname(__FILE__) . "/../dao/TagPDO.php");
require_once(dirname(__FILE__) . "/../service/MacService.php");

use \PDO;
use \PDOException;
use database\DbConfig;
use app\AppConfig;
use security\MacService;

header('Content-Type: application/json; charset=utf-8');

$requestObj = json_decode(file_get_contents("php://input"));

$supportService = new SupportService();

$type = $requestObj->type;
if ($type === 'TOP') {
    $supportService->requireTop($requestObj);
} else if ($type === 'ME') {
    $supportService->requireMe($requestObj);
} else if ($type === 'SUPPORT') {
    $supportService->addSupport($requestObj);
}

class SupportService {
    public function requireTop($requestObj) {
        $supportPDO = new SupportPDO();
        $tops = $supportPDO->findTopOfSupportBetween(date("Y-m-d"), date("Y-m-d",strtotime("+1 day")));
        if (null !== $tops) {
            echo json_encode(array('success'=>true, 'type'=>'info', 'request'=>$requestObj, 'data'=>$tops, 'message'=>"Success"));
        } else {
            echo json_encode(array('success'=>false, 'type'=>'warn', 'code'=>'E001', 'request'=>$requestObj, 'data'=>$tops, 'message'=>"抱歉，未能获得排名信息！"));
        }
    }

    public function requireMe($requestObj) {
        $openid = $requestObj->openid;
        $supportPDO = new SupportPDO();
        $ret= $supportPDO->findNumberOfSupport($openid);
        $tagPDO = new TagPDO();
        $tag = $tagPDO->findTag($openid);
        if (null !== $ret) {
            echo json_encode(array('success'=>true, 'type'=>'info', 'request'=>$requestObj, 'data'=>$ret, 'tag'=>$tag, 'message'=>"Success"));
        } else {
            echo json_encode(array('success'=>false, 'type'=>'warn', 'code'=>'E001', 'request'=>$requestObj, 'data'=>$ret, 'message'=>"抱歉，未能获得排名信息！"));
        }
    }

    public function addSupport($requestObj) {
        $openid = $requestObj->openid;
        $support_openid = $requestObj->support_openid;
        if ($this->isEmpty($openid) || $this->isEmpty($support_openid)) {
            echo json_encode(array('success'=>false, 'type'=>'warn', 'code'=>'E001', 'request'=>$requestObj, 'data'=>null, 'message'=>"服务器繁忙，<br>请稍后再来！"));
            return;
        }
        $supportPDO = new SupportPDO();
        $macService = new MacService(PHP_OS);
        $ipAddress = $macService->getIp();

        $find = $supportPDO->findNumberOfSupportBetweenBySupportOpenid($openid, $support_openid, date("Y-m-d"), date("Y-m-d",strtotime("+1 day")));
        if (null !== $find && $find->numberOfSupport > 0) {
            echo json_encode(array('success'=>false, 'type'=>'warn', 'code'=>'E002', 'request'=>$requestObj, 'data'=>$find, 'message'=>"你已经赞同过了，<br>请明天再继续吧！"));
        } else {
            $find = $supportPDO->findNumberOfSupportBetweenBySupportIp($openid, $ipAddress, date("Y-m-d"), date("Y-m-d",strtotime("+1 day")));
            if (null !== $find && $find->numberOfSupport > 0) {
                echo json_encode(array('success'=>false, 'type'=>'warn', 'code'=>'E002', 'request'=>$requestObj, 'data'=>$find, 'message'=>"你已经赞同过了，<br>请明天再继续吧！<br>(请勿刷票)"));
                return;
            }
            $ret = $supportPDO->insertSingleRow($openid, $support_openid, $ipAddress);
            if ($ret) {
                echo json_encode(array('success'=>true, 'type'=>'info', 'request'=>$requestObj, 'data'=>$ret, 'message'=>"Successfully updated "));
            } else {
                echo json_encode(array('success'=>false, 'type'=>'warn', 'code'=>'E001', 'request'=>$requestObj, 'data'=>$ret, 'message'=>"服务器繁忙，<br>请稍后再来！"));
            }
        }
    }

    public function isEmpty($input) {
        if (null === $input || 'null' === $input || '' === trim($input)) {
            return true;
        }
        return false;
    }
}

