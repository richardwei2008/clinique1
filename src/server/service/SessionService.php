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

use \PDO;
use \PDOException;
use database\DbConfig;
use app\AppConfig;

header('Content-Type: application/json; charset=utf-8');

$requestObj = json_decode(file_get_contents("php://input"));

session_start();
if(isset($_SESSION['user'])) {
    echo(json_encode($_SESSION['user']));
} else {
    echo(json_encode(null));
}
return;
