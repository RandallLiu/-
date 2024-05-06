<?php
/**
* 	配置账号信息
*/

class WxPayConf_pub
{
	const CURL_TIMEOUT = 30;
    public static $keypre='';
    public static function getAPPID()
    {
        return Config\WxConfig::getConfig(self::$keypre.'weixin_appid');
    }
    public static function getTOKEN()
    {        
        return Config\WxConfig::getConfig(self::$keypre.'weixin_token');
    }
    public static function getMCHID()
    {        
        return Config\WxConfig::getConfig(self::$keypre.'weixin_mchid');
    }
    public static function getAPPSECRET()
    {        
        return Config\WxConfig::getConfig(self::$keypre.'weixin_appsecret');
    }
    public static function getKEY()
    {        
        return Config\WxConfig::getConfig(self::$keypre.'weixin_key');
    }

    public static function getSSLKEY_PATH()
    {        
        return Config\WxConfig::getConfig(self::$keypre.'weixin_sslkey_path');
    }
    public static function getSSLCERT_PATH()
    {        
        return Config\WxConfig::getConfig(self::$keypre.'weixin_sslcert_path');
    }

}
	
?>