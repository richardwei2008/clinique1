<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 14-8-12
 * Time: 下午11:09
 */

namespace customer;
header('Content-Type: text/html; charset=utf-8');
require_once(dirname(__FILE__) ."/../database/DbConfig.php");

use \PDO;
use \PDOException;
use database\DbConfig;

class SuccessLogPDO {

    private $conn = null;

    /**
     * @param null|\PDO $conn
     */
    public function setConn($conn)
    {
        $this->conn = $conn;
    }

    /**
     * @return null|\PDO
     */
    public function getConn()
    {
        if ($this->conn == null) {
            $connectionString = sprintf("mysql:host=%s;dbname=%s",
                DbConfig::DB_HOST,
                DbConfig::DB_NAME);
            try {
                $this->conn = new PDO($connectionString,
                    DbConfig::DB_USER,
                    DbConfig::DB_PASSWORD,
                    array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8",
                        PDO::MYSQL_ATTR_INIT_COMMAND => "SET CHARACTER SET utf8",
                    )
                );
            } catch (PDOException $pe) {
                die($pe->getMessage());
            }
        }
        return $this->conn;
    }

    /**
     * Open the database connection
     */
    public function __construct(){
        // open database connection

    }

    public function insertSingleRow($openid, $provinceCode, $provinceName, $cityCode, $cityName,
                                    $siteCode, $siteName, $cellphone){
        try {
            $clnq_user_success = array(
                ':openid'               => $openid,
                ':provinceCode'        => $provinceCode,
                ':provinceName'        => $provinceName,
                ':cityCode'             => $cityCode,
                ':cityName'             => $cityName,
                ':siteCode'             => $siteCode,
                ':siteName'             => $siteName,
                ':cellphone'            => $cellphone);
            $sql = 'INSERT INTO clnq_user_success(openid, province_code, province_name, city_code, city_name, site_code, site_name, cellphone)
                    VALUES(:openid, :provinceCode, :provinceName, :cityCode, :cityName, :siteCode, :siteName, :cellphone)';
            $q = $this->getConn()->prepare($sql);
            $result = $q->execute($clnq_user_success);
            // $q->debugDumpParams();
            return $result;
        } catch (Exception $ex) {
            echo($ex->getMessage());
        }
    }

    public function insertSingleRowWithIp($openid, $provinceCode, $provinceName, $cityCode, $cityName,
                                    $siteCode, $siteName, $cellphone, $ipAddress){
        try {
            $clnq_user_success = array(
                ':openid'               => $openid,
                ':provinceCode'        => $provinceCode,
                ':provinceName'        => $provinceName,
                ':cityCode'             => $cityCode,
                ':cityName'             => $cityName,
                ':siteCode'             => $siteCode,
                ':siteName'             => $siteName,
                ':cellphone'            => $cellphone,
                ':ip_mac'            => $ipAddress);
            $sql = 'INSERT INTO clnq_user_success(openid, province_code, province_name, city_code, city_name, site_code, site_name, cellphone, ip_mac)
                    VALUES(:openid, :provinceCode, :provinceName, :cityCode, :cityName, :siteCode, :siteName, :cellphone, :ip_mac)';
            $q = $this->getConn()->prepare($sql);
            $result = $q->execute($clnq_user_success);
            // $q->debugDumpParams();
            return $result;
        } catch (Exception $ex) {
            echo($ex->getMessage());
        }
    }

    public function insertSingleRowWithIpServerTime($openid, $provinceCode, $provinceName, $cityCode, $cityName,
                                          $siteCode, $siteName, $cellphone, $ipAddress, $serverTime){
        try {
            $clnq_user_success = array(
                ':openid'               => $openid,
                ':provinceCode'        => $provinceCode,
                ':provinceName'        => $provinceName,
                ':cityCode'             => $cityCode,
                ':cityName'             => $cityName,
                ':siteCode'             => $siteCode,
                ':siteName'             => $siteName,
                ':cellphone'            => $cellphone,
                ':ip_mac'            => $ipAddress,
                ':servertime'            => $serverTime
            );
            $sql = 'INSERT INTO clnq_user_success(openid, province_code, province_name, city_code, city_name, site_code, site_name, cellphone, ip_mac, servertime)
                    VALUES(:openid, :provinceCode, :provinceName, :cityCode, :cityName, :siteCode, :siteName, :cellphone, :ip_mac, :servertime)';
            $q = $this->getConn()->prepare($sql);
            $result = $q->execute($clnq_user_success);
            // $q->debugDumpParams();
            return $result;
        } catch (Exception $ex) {
            echo($ex->getMessage());
        }
    }

    public function findByOpenId($openid){
        try {
            $clnq_user_success = array(
                ':openid' => $openid
            );
            $sql = 'SELECT *
                    FROM clnq_user_success
                    WHERE openid  = :openid';
            $q = $this->getConn()->prepare($sql);
            $q->execute($clnq_user_success);
            $q->setFetchMode(PDO::FETCH_ASSOC);
            while ($r = $q->fetchObject()) {
                return $r;
            }
        } catch (Exception $ex) {
            echo($ex->getMessage());
        }
    }

    public function updateStatus($openid, $status){
        $clnq_user_success = array(
            ':status' => $status,
            ':openid' => $openid
        );
        $sql = 'UPDATE clnq_user_success
            SET status  = :status
            WHERE openid = :openid';
        $q = $this->getConn()->prepare($sql);
        return $q->execute($clnq_user_success);
    }

    public function findByOpenIdBetweenDate($openid, $dateFrom, $dateTo){
        try {
            $clnq_user_success = array(
                ':openid' => $openid,
                ':dateFrom' => $dateFrom,
                ':dateTo' => $dateTo
            );
            $sql = 'SELECT *
                    FROM clnq_user_success
                    WHERE openid  = :openid
                    AND createtime BETWEEN :dateFrom AND :dateTo';
            $q = $this->getConn()->prepare($sql);
            $q->execute($clnq_user_success);
            $q->setFetchMode(PDO::FETCH_ASSOC);
            while ($r = $q->fetchObject()) {
                return $r;
            }
        } catch (Exception $ex) {
            echo($ex->getMessage());
        }
    }

    public function findNumberOfWinner(){
        try {
            $clnq_user_success = array(
                ':dateFrom' => null,
                ':dateTo' => null
            );

            $sql = 'SELECT count(1) as numberOfWinner
                    FROM clnq_user_success
                    WHERE createtime';

            $q = $this->getConn()->prepare($sql);
            $q->execute($clnq_user_success);
            $q->setFetchMode(PDO::FETCH_ASSOC);
            while ($r = $q->fetchObject()) {
                return $r;
            }
        } catch (Exception $ex) {
            echo($ex->getMessage());
        }
    }

    public function findNumberOfWinnerBetweenServerTime($dateFrom, $dateTo){
        try {
            $clnq_user_success = array(
                ':dateFrom' => $dateFrom,
                ':dateTo' => $dateTo
            );

            $sql = 'SELECT count(1) as numberOfWinner
                    FROM clnq_user_success
                    WHERE servertime BETWEEN :dateFrom AND :dateTo';

            $q = $this->getConn()->prepare($sql);
            $q->execute($clnq_user_success);
            $q->setFetchMode(PDO::FETCH_ASSOC);
            while ($r = $q->fetchObject()) {
                return $r;
            }
        } catch (Exception $ex) {
            echo($ex->getMessage());
        }
    }

    public function findNumberOfWinnerBetweenDate($dateFrom, $dateTo){
        try {
            $clnq_user_success = array(
                ':dateFrom' => $dateFrom,
                ':dateTo' => $dateTo
            );

            $sql = 'SELECT count(1) as numberOfWinner
                    FROM clnq_user_success
                    WHERE createtime BETWEEN :dateFrom AND :dateTo';

            $q = $this->getConn()->prepare($sql);
            $q->execute($clnq_user_success);
            $q->setFetchMode(PDO::FETCH_ASSOC);
            while ($r = $q->fetchObject()) {
                return $r;
            }
        } catch (Exception $ex) {
            echo($ex->getMessage());
        }
    }

    public function checkNumberOfWinnerLessThanAllocation($siteCode){
        try {
            $clnq_user_success = array(
                ':siteCodeS' => $siteCode,
                ':siteCodeA' => $siteCode
            );

            $sql = "SELECT r.consume < r.allocation as isValid
                    FROM (
                    SELECT (
                        SELECT count( 1)
                        FROM clnq_user_success s
                        WHERE s.site_code =:siteCodeS
                    ) AS consume, (
                        SELECT a.allocation
                        FROM clnq_site_allocation a
                        WHERE a.store_no =:siteCodeA
                    ) AS allocation) r";
            $q = $this->getConn()->prepare($sql);
            $q->execute($clnq_user_success);
            $q->setFetchMode(PDO::FETCH_ASSOC);
            while ($r = $q->fetchObject()) {
                return $r;
            }
        } catch (Exception $ex) {
            echo($ex->getMessage());
        }
    }



    public function findNumberOfCellphone($cellphone){
        try {
            $clnq_user_success = array(
                ':cellphone' => $cellphone
            );

            $sql = 'SELECT count(1) as numberOfPhone
                    FROM clnq_user_success
                    WHERE cellphone  = :cellphone';

            $q = $this->getConn()->prepare($sql);
            $q->execute($clnq_user_success);
            $q->setFetchMode(PDO::FETCH_ASSOC);
            while ($r = $q->fetchObject()) {
                return $r;
            }
        } catch (Exception $ex) {
            echo($ex->getMessage());
        }
    }
}

//// db setup test
//$obj = new SuccessLogPDO();
//
//// TEST
//
//// insert
// try {
//     if($obj->insertSingleRow('oxqECj7JTrpVG7BfJnNCUpQap012',
//            '5', '广东',
//            '020', '广州',
//            '46', '广州友谊', '13521932814')) {
//         echo '<br>A new task has been added successfully.<br>';
//     } else {
//         echo '<br>Error adding the task.<br>';
//     }
// } catch (Exception $ex) {
//        echo($ex->getMessage());
// }
//
//// find
//try {
//    if($success = $obj->findByOpenId('oxqECj7JTrpVG7BfJnNCUpQap012')) {
//        echo '<br>Found.<br>'.(var_dump($success));
//    } else {
//        echo '<br>Error.<br>';
//    }
//} catch (Exception $ex) {
//    echo($ex->getMessage());
//}
//
//try {
//    if($success = $obj->findByOpenIdBetweenDate('oxqECj7JTrpVG7BfJnNCUpQap012', '2014-09-22', '2014-09-23')) {
//        echo '<br>Found.<br>'.(var_dump($success));
//    } else {
//        echo '<br>Error.<br>';
//    }
//} catch (Exception $ex) {
//    echo($ex->getMessage());
//}
//
//try {
//    if($success = $obj->findNumberOfCellphone('13521932814')) {
//        echo '<br>Found.<br>'.(var_dump($success));
//    } else {
//        echo '<br>Error.<br>';
//    }
//
//} catch (Exception $ex) {
//    echo($ex->getMessage());
//}
//
//try {
//    if($success = $obj->findNumberOfWinnerBetweenDate( '2014-09-21', '2014-09-22')) {
//        echo '<br>Found.<br>'.(var_dump($success));
//    } else {
//        echo '<br>Error.<br>';
//    }
//} catch (Exception $ex) {
//    echo($ex->getMessage());
//}
//
//try {
//    if($success = $obj->findNumberOfWinnerBetweenServerTime('2014-10-13', '2014-10-14')) {
//        echo '<br>Found.<br>'.(var_dump($success));
//    } else {
//        echo '<br>Error.<br>';
//    }
//} catch (Exception $ex) {
//    echo($ex->getMessage());
//}
//
//try {
//    if($success = $obj->findNumberOfWinner()) {
//        echo '<br>Found.<br>'.(var_dump($success));
//    } else {
//        echo '<br>Error.<br>';
//    }
//} catch (Exception $ex) {
//    echo($ex->getMessage());
//}

//$v = explode('-', 'abc-txt');
//echo end($v).'<br>';
//echo $v[0];

