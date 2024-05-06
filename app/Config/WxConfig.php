<?php 
namespace Config;
use CodeIgniter\Config\BaseConfig;

class WxConfig extends BaseConfig
{
	public static function getConfig($key){
		///*智慧乡村小程序
		$config['vixcx_weixin_appid'] = "wx64eb2d15759d1e8e";
		$config['vixcx_weixin_appsecret'] = "38d041fa62ef0894e1c3c516ad00c506";

		$config['vixcx_weixin_mchid'] = "1531070501";
		$config['vixcx_weixin_key'] = "d4ed8655e316f73ecaf076423807a321";
		$config['vixcx_weixin_sslkey_path'] = APPPATH."wxcert1531070501/apiclient_key.pem";
		$config['vixcx_weixin_sslcert_path'] = APPPATH."wxcert1531070501/apiclient_cert.pem";


		return $config[$key];
	}

}
