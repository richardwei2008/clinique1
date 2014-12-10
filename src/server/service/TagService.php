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

require_once(dirname(__FILE__) . "/../dao/TagPDO.php");


use \PDO;
use \PDOException;
use database\DbConfig;

header('Content-Type: application/json; charset=utf-8');

$requestObj = json_decode(file_get_contents("php://input"));
$type = $requestObj->type;
$tagService = new TagService();

if ($type === 'READ') {
    $tagService->findTag($requestObj);
} else if ($type === 'CHOOSE') {
    $tagService->chooseTag($requestObj);
}


class TagService {
    public function chooseTag($requestObj) {
        $openid = $requestObj->openid;
        $tag = urldecode($requestObj->tag);
        if ($openid == null || $tag == null) {
            echo json_encode(array('success'=>false, 'type'=>'warn', 'code'=>'E002', 'request'=>$requestObj, 'data'=>null, 'message'=>"服务器繁忙，<br>请稍后再来！"));
            return;
        }
        $tagPDO = new TagPDO();
        $existTag = $tagPDO->findTag($openid);
        if ($existTag) {
            echo json_encode(array('success'=>false, 'type'=>'error', 'code'=>'E001', 'request'=>$requestObj, 'data'=>$existTag, 'message'=>"您已参加过游戏，<br>快点查看我的吧！"));
            return;
        }
        $ret = $tagPDO->insertSingleRow($openid, $tag);
        if ($ret) {
            echo json_encode(array('success'=>true, 'type'=>'info', 'request'=>$requestObj, 'data'=>$tag, 'message'=>"Successfully updated "));
        } else {
            echo json_encode(array('success'=>false, 'type'=>'warn', 'code'=>'E001', 'request'=>$requestObj, 'data'=>$ret, 'message'=>"服务器繁忙，<br>请稍后再来！"));
        }
    }

    public function findTag($requestObj) {
        $openid = $requestObj->openid;
        if ($openid == null) {
            echo json_encode(array('success'=>false, 'type'=>'warn', 'code'=>'E002', 'request'=>$requestObj, 'data'=>null, 'message'=>"服务器繁忙，<br>请稍后再来！"));
            return;
        }
        $tagPDO = new TagPDO();
        $ret = $tagPDO->findTag($openid);
        if ($ret) {
            echo json_encode(array('success'=>false, 'type'=>'error', 'code'=>'E001', 'request'=>$requestObj, 'data'=>$ret, 'message'=>"您已参加过游戏，<br>快点查看我的吧！"));
        } else {
            echo json_encode(array('success'=>true, 'type'=>'info', 'request'=>$requestObj, 'data'=>$ret, 'message'=>"Success"));
        }
    }

}

