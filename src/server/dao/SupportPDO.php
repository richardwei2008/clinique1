<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 14-8-12
 * Time: 下午10:16
 */

namespace customer;
require_once(dirname(__FILE__) ."/../database/DbConfig.php");

use \PDO;
use \PDOException;
use database\DbConfig;
class SupportPDO {
    private $conn = null;

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

    public function insertSingleRow($openid, $support_openid, $ipAddress){
        try {
            $clnq_user_support = array(
                ':openid' => $openid,
                ':support_openid' => $support_openid,
                ':ip_mac' => $ipAddress);

            $sql = 'INSERT INTO clnq_user_support(openid, support_openid, ip_mac)
                    VALUES(:openid, :support_openid, :ip_mac)';
            $q = $this->getConn()->prepare($sql);
            return $q->execute($clnq_user_support);
        } catch (Exception $ex) {
            echo($ex->getMessage());
        }
    }

    public function findNumberOfSupport($openid){
        try {
            $clnq_user_support = array(
                ':openid' => $openid
            );
            $sql = 'SELECT count(1) as numberOfSupport
                    FROM clnq_user_support
                    WHERE openid  = :openid';

            $q = $this->getConn()->prepare($sql);
            $q->execute($clnq_user_support);
            $q->setFetchMode(PDO::FETCH_ASSOC);
            while ($r = $q->fetchObject()) {
                return $r;
            }
        } catch (Exception $ex) {
            echo($ex->getMessage());
        }
    }

    public function findNumberOfSupportBetween($openid, $dateFrom, $dateTo){
        try {
            $clnq_user_support = array(
                ':openid'       => $openid,
                ':dateFrom'     => $dateFrom,
                ':dateTo'     => $dateTo
            );
            $sql = 'SELECT count(1) as numberOfSupport
                    FROM clnq_user_support
                    WHERE openid  = :openid
                    AND createtime BETWEEN :dateFrom AND :dateTo';

            $q = $this->getConn()->prepare($sql);
            $q->execute($clnq_user_support);
            $q->setFetchMode(PDO::FETCH_ASSOC);
            while ($r = $q->fetchObject()) {
                return $r;
            }
        } catch (Exception $ex) {
            echo($ex->getMessage());
        }
    }

    public function findNumberOfSupportBetweenBySupportOpenid($openid, $support_openid, $dateFrom, $dateTo){
        try {
            $clnq_user_support = array(
                ':openid'       => $openid,
                ':support_openid'       => $support_openid,
                ':dateFrom'     => $dateFrom,
                ':dateTo'     => $dateTo
            );
            $sql = 'SELECT count(1) as numberOfSupport
                    FROM clnq_user_support
                    WHERE openid  = :openid
                    AND support_openid  = :support_openid
                    AND createtime BETWEEN :dateFrom AND :dateTo';

            $q = $this->getConn()->prepare($sql);
            $q->execute($clnq_user_support);
            $q->setFetchMode(PDO::FETCH_ASSOC);
            while ($r = $q->fetchObject()) {
                return $r;
            }
        } catch (Exception $ex) {
            echo($ex->getMessage());
        }
    }

    public function findNumberOfSupportBetweenBySupportIp($openid, $ipAddress, $dateFrom, $dateTo){
        try {
            $clnq_user_support = array(
                ':openid'       => $openid,
                ':ip_mac'       => $ipAddress,
                ':dateFrom'     => $dateFrom,
                ':dateTo'     => $dateTo
            );
            $sql = 'SELECT count(1) as numberOfSupport
                    FROM clnq_user_support
                    WHERE openid  = :openid
                    AND ip_mac  = :ip_mac
                    AND createtime BETWEEN :dateFrom AND :dateTo';

            $q = $this->getConn()->prepare($sql);
            $q->execute($clnq_user_support);
            $q->setFetchMode(PDO::FETCH_ASSOC);
            while ($r = $q->fetchObject()) {
                return $r;
            }
        } catch (Exception $ex) {
            echo($ex->getMessage());
        }
    }

    public function findTopOfSupportBetween($dateFrom, $dateTo){
        try {
            $clnq_user_support = array(
                ':dateFrom'     => $dateFrom,
                ':dateTo'     => $dateTo
            );
            $sql = "SELECT s.openid, i.headimgurl, i.nickname, TRIM(LEADING '<br>' FROM t.tag ) as tag, count(1) as numberOfSupport
                    FROM clnq_user_support s LEFT JOIN clnq_user_tag t on (t.openid = s.openid) LEFT JOIN clnq_user_info i on (i.openid = s.openid)
                    WHERE s.createtime BETWEEN :dateFrom AND :dateTo
                    GROUP BY s.openid, i.headimgurl, t.tag
                    ORDER BY numberOfSupport DESC
                    LIMIT 10";

            $q = $this->getConn()->prepare($sql);
            $q->execute($clnq_user_support);
            $q->setFetchMode(PDO::FETCH_ASSOC);
            $r = $q->fetchAll();
            return $r;
        } catch (Exception $ex) {
            echo($ex->getMessage());
        }
    }
}

//// db setup test
// $obj = new SupportPDO();
//
// try {
//     if($obj->insertSingleRow('oxqECj7JTrpVG7BfJnNCUpQap012', 'oHTD_tpKyYb1rZsDEhNe3dL-Evhg')) {
//         echo 'A new task has been added successfully<br>';
//     } else {
//         echo 'Error adding the task';
//     }
// } catch (Exception $ex) {
//        echo($ex->getMessage());
// }
//
//try {
//    if($obj->insertSingleRow('oxqECj7JTrpVG7BfJnNCUpQap012', 'oHTD_tvXZwJs-rfQGKe_tz0bMro8')) {
//        echo 'A new task has been added successfully<br>';
//    } else {
//        echo 'Error adding the task';
//    }
//} catch (Exception $ex) {
//    echo($ex->getMessage());
//}
//
//try {
//    if($numberOfSupport = $obj->findNumberOfSupport('oxqECj7JTrpVG7BfJnNCUpQap012')) {
//        echo '<br>Found.<br>'.(var_dump($numberOfSupport));
//    } else {
//        echo '<br>Error.<br>';
//    }
//} catch (Exception $ex) {
//    echo($ex->getMessage());
//}

//try {
//    if($numberOfSupport = $obj->findNumberOfSupportBetween('oxqECj7JTrpVG7BfJnNCUpQap012', '2014-09-21', '2014-09-22')) {
//        echo '<br>Found.<br>'.(var_dump($numberOfSupport));
//    } else {
//        echo '<br>Error.<br>';
//    }
//} catch (Exception $ex) {
//    echo($ex->getMessage());
//}
//
//try {
//    if($numberOfSupport = $obj->findNumberOfSupportBetween('oxqECj7JTrpVG7BfJnNCUpQap012', '2014-09-22 14:00:00', '2014-09-22 15:00:00')) {
//        echo '<br>Found.<br>'.(var_dump($numberOfSupport));
//    } else {
//        echo '<br>Error.<br>';
//    }
//} catch (Exception $ex) {
//    echo($ex->getMessage());
//}

//
//try {
//    if($topOfSupport = $obj->findTopOfSupportBetween('2014-09-22', '2014-09-23')) {
//        echo '<br>Found.<br>'.(var_dump($topOfSupport));
//    } else {
//        echo '<br>Error.<br>';
//    }
//} catch (Exception $ex) {
//    echo($ex->getMessage());
//}
//
//echo "昨天:".date("Y-m-d",strtotime("-1 day")), "<br>";
//echo "今天:".date("Y-m-d")."<br>";

