<?php
namespace security;
/**
 * 获取网卡的MAC地址原码；目前支持WIN/LINUX系统
 * 获取机器网卡的物理（MAC）地址
 **/
class MacService
{
    protected $return_array = array(); // 返回带有MAC地址的字串数组
    protected $mac_addr;

    /**
     * @return mixed
     */
    public function getMacAddr()
    {
        return $this->mac_addr;
    }

    protected $ip;

    function MacService($os_type)
    {
        switch (strtolower($os_type)) {
            case "linux":
                $this->forLinux();
                break;
            case "solaris":
                break;
            case "unix":
                break;
            case "aix":
                break;
            default:
                $this->forWindows();
                break;

        }
        $temp_array = array();
        foreach ($this->return_array as $value) {

            if (
            preg_match("/[0-9a-f][0-9a-f][:-]" . "[0-9a-f][0-9a-f][:-]" . "[0-9a-f][0-9a-f][:-]" . "[0-9a-f][0-9a-f][:-]" . "[0-9a-f][0-9a-f][:-]" . "[0-9a-f][0-9a-f]/i", $value,
                $temp_array)
            ) {
                $this->mac_addr = $temp_array[0];
                break;
            }

        }
        unset($temp_array);
        return $this->mac_addr;
    }


    function forWindows()
    {
        @exec("ipconfig /all", $this->return_array);
        if ($this->return_array)
            return $this->return_array;
        else {
            $ipconfig = $_SERVER["WINDIR"] . "\system32\ipconfig.exe";
            if (is_file($ipconfig))
                @exec($ipconfig . " /all", $this->return_array);
            else
                @exec($_SERVER["WINDIR"] . "\system\ipconfig.exe /all", $this->return_array);
            return $this->return_array;
        }
    }


    function forLinux()
    {
        @exec("ifconfig -a", $this->return_array);
        return $this->return_array;
    }

    function getIp()
    {
        if (@ $_SERVER["HTTP_X_FORWARDED_FOR"])
            $this->ip = $_SERVER["HTTP_X_FORWARDED_FOR"];
        else
            if (@ $_SERVER["HTTP_CLIENT_IP"])
                $this->ip = $_SERVER["HTTP_CLIENT_IP"];
            else
                if (@ $_SERVER["REMOTE_ADDR"])
                    $this->ip = $_SERVER["REMOTE_ADDR"];
                else
                    if (@ getenv("HTTP_X_FORWARDED_FOR"))
                        $this->ip = getenv("HTTP_X_FORWARDED_FOR");
                    else
                        if (@ getenv("HTTP_CLIENT_IP"))
                            $this->ip = getenv("HTTP_CLIENT_IP");
                        else
                            if (@ getenv("REMOTE_ADDR"))
                                $this->ip = getenv("REMOTE_ADDR");
                            else
                                $this->ip = "Unknown";
        return $this->ip;
    }


}

//// 调用这个类的一个实例：
//$mac = new MacService(PHP_OS);
//echo $mac->getMacAddr(); //输入mac地址
//echo $mac->getIp(); //输出ip
//?>