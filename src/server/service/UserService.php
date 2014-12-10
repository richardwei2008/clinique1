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

require_once(dirname(__FILE__) . "/../dao/UserPDO.php");
require_once(dirname(__FILE__) . "/../dao/TagPDO.php");

use \PDO;
use \PDOException;
use database\DbConfig;
use app\AppConfig;

header('Content-Type: application/json; charset=utf-8');

$requestObj = json_decode(file_get_contents("php://input"));

session_start();
//if(isset($_SESSION['user'])) {
//    echo(json_encode($_SESSION['user']));
////    echo(json_encode(array('success'=>$requestObj)));
//} else {
//    echo(json_encode(null));
//}
$type = $requestObj->type;

$userService = new UserService();
if ($type === 'READ') {
    $userService->readUser($requestObj);
} else if ($type === 'ADD') {
    $userService->addUser($requestObj);
}

class UserService {
    public function makeGuess($requestObj) {

    }

    public function readUser($requestObj) {
        $openid = $requestObj->openid;
        if ($this->isEmpty($openid)) {
            echo json_encode(array('success'=>false, 'type'=>'error', 'code'=>'E002', 'request'=>$requestObj, 'data'=>null, 'message'=>"openid不能为空，请重试"));
            return;
        }
        $userPDO = new UserPDO();
        $user = $userPDO->findByOpenId($openid);
        $tagPDO = new TagPDO();
        $tag = $tagPDO->findTag($openid);
        echo json_encode(array('success'=>true, 'type'=>'info', 'code'=>'I001', 'request'=>$requestObj, 'data'=>$user, 'tag'=>$tag, 'message'=>"Success"));
    }

    public function addUser($requestObj) {
        $openid = $requestObj->openid;
        $userPDO = new UserPDO();
        if ($this->isEmpty($openid)) {
            echo json_encode(array('success'=>false, 'type'=>'error', 'code'=>'E002', 'request'=>$requestObj, 'data'=>null, 'message'=>"openid不能为空，请重试"));
            return;
        }
        $subscribe = '0';
        if (!is_null($requestObj->data->subscribe)) {
            $subscribe = $requestObj->data->subscribe;
        }
        $nickname = $requestObj->data->nickname;
        if ($nickname === 'undefined') {
            $nickname = null;
        }
        $sex = '1';
        if (!is_null($requestObj->data->sex)) {
            $sex = $requestObj->data->sex;
        }
        $language = $requestObj->data->language;
        $city = $requestObj->data->city;
        $province = $requestObj->data->province;
        $country = $requestObj->data->country;

        $headimgurl = $requestObj->data->headimgurl;
        if ($headimgurl === 'undefined') {
            $headimgurl = null;
        }
        $subscribetime = '0';
        if (is_integer($requestObj->data->subscribetime)) {
            $subscribetime = $requestObj->data->subscribetime;
        }

        $user = $userPDO->findByOpenId($openid);
        if ($user) {
            if (!$this->isEmpty($province)) {
                $ret = $userPDO->updateUser($openid, $subscribe, $nickname, $sex, $language, $city, $province, $country, $headimgurl, $subscribetime);
            }
            echo json_encode(array('success'=>true, 'type'=>'warn', 'code'=>'E003', 'request'=>$requestObj, 'data'=>$ret, 'message'=>"Success"));
        } else {
            $ret = $userPDO->insertFullSingleRow($openid, $subscribe, $nickname, $sex, $language, $city, $province, $country, $headimgurl, $subscribetime);
            if($ret) {
                echo json_encode(array('success'=>true, 'type'=>'info', 'code'=>'I001', 'request'=>$requestObj, 'data'=>$requestObj->data, 'message'=>"Success"));
            } else {
                echo json_encode(array('success'=>false, 'type'=>'error', 'code'=>'E001', 'request'=>$requestObj, 'data'=>$requestObj->data, 'message'=>"授权失败<br>请回到首页重新授权！"));
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

