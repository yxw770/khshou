<?php
$directory = isset($directory) ? $directory : 'alipay';
$orderid = isset($orderid) ? $orderid : '';
$config = array (	
		//应用ID,您的APPID。
		'app_id' => "2018121562576045",

		//商户私钥，您的原始格式RSA私钥
		'merchant_private_key' => "MIIEowIBAAKCAQEAyrdTOUiI7VG1M7X/z1vOu1EKvu56QK42kVbeivfsQQpwlKGe4Re787NN3x92TyzUZ+Q9tJiJnZxrH9rKjNctmPcsNb+iJvwYyTVJ7k6U5Zl3XCk/5m36CzL4rkX5MIYJmouWZAB0M9wxm3jICLx3DZscozhC9f5MlGfgcrOjIpyKBCApVgbCqhKshTVvnPozIEERdAeiEMuRAnRBiM86REDw21/qARYqzZRrB+1nhk8R5iFO/x0ex4PYk2wOfR6Vt9DH0A5oHUK4vWmkDA7fpnPnyYq4CjV7MOFcUWAN2m7z7vO3KCGT+XeLaudGPf+oRhzgGAejZ+TzysoGEVpqdwIDAQABAoIBAG9J48OgAaQf5lXERfMF2OHXJQobDDy0J7r0sOokH6y5WYMPk5TJneK1fjvPHvlqiBWdJQ8favV/KQRs7iQSAbNnhvrfANtcLRNRUQwmUfIWpYOQFcegetRpIEM0oRA1ldIlbePqp+DMsuNClLbPGKD8leU5fvFjsZEL0hnGavoVcdGteCoxWu3Ku9QKxHout/JlF4e4tvCSa+C4R9YcNfwO34hVBpeov2Fmgo5MYY/JwR/MdWTVIuq3Tb4ZGxw9GuRSnMQmTu8ibiUbz9NYv/xLIzX6r+bgPBBT55zZmzLY2qxiI5zV/21hfYZzgFCInTAEc3kla9bFNnLWkLdAI5ECgYEA8cHpqncZP8xopwvi+ydhCBpmw86973Legoo1BqUD/X+BEqh+hMu4RxRgbnWHSh3xx8tEXTLOZo8Pgv7u+ligs4kwSQcSJM6tQstefXyDgtOZ3FNLfKkZyq55/5AYyw6AHEa78xo9ilmi/2j9YHQv5gehuQefC+++VGhu6sRzm58CgYEA1qibRvubDwVw3C20NwT5/60GihdUXaxA6e6NI2DSrBIX6HNMTX0JT2XMezB3AeuTH8lahTlm659KLFDWd1r1IJR2zmnnVEfklJgdM9gCXOxGEKmOTgDJs2TpRfAm8z6ouZRGbF2vIGs/wqVfVF3JxQsBNS8DFeCx7kVPsJgYwikCgYBxNDwWdm58UVsv3YEL2gS1pqc4STbnSQKoXc82rU6RBYOLGRslU7+WiRQepSoUqrDsvWHlwkSkAZlh3b5Ju/aEG1lg2M7GLYE0Jca5O/TY7fy+DsLqPMS3A7xczzzM2J1koDqiO0dV4WNR9fevkrhbHne8DLWNAVY+cCiABj2cQQKBgHlkZFuiEjBOfWKmnhG3SMHkUZxqURWgQh2JAFwPd1RD7y1Pdskjes5h3V+VKtf1JwCE2CIbMi7xp/qjxIU+9Pz1uLJGhcHN3mCCHZGiCMQQAheOK6HFJEvFN36LrxtERhqc1a6W1GDbBfXHm7+UQnSiIo63BWGr7jO3Erfla6rxAoGBAIsj9XUrkMNTcru6aovDid4PxvuIVDqYfl77tGCDFTKFfF3Rpa8hEZHor8hIAFWhgUAOrBfMlm3IDqZiyYlA1vpLopr5F0+nR9+Uu+jM5N0w3X6hvXAQDlOk/lzNcRByS2LyVDro0sHZiCjL9OPf4T5vlvHl2ZDR9Dbc5snBWHBn",

		//异步通知地址
		'notify_url' => request()->domain().'/notify/'.$directory,

		//同步跳转
		'return_url' => request()->domain().'/orderquery?kw='.$orderid,

		//编码格式
		'charset' => "UTF-8",

		//签名方式
		'sign_type'=>"RSA2",

		//支付宝网关
		'gatewayUrl' => "https://openapi.alipay.com/gateway.do",

		//支付宝公钥,查看地址：https://openhome.alipay.com/platform/keyManage.htm 对应APPID下的支付宝公钥。
		'alipay_public_key' => "MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAvWNSN0vwIcA5XiFI0fpj1TLzBIHrkSmnbVwM3UOnhV0jvs5svxvBUdPqL5dSHCzqT9P+C1NEVLSI6iKpGtEScHZ+NI/u2Eb+CVGqLAX5qyo/KAGKvTSLPFUnomAGUd3oNNrapgivfgdl1YC3rv4QSnay/wLw4xihnYzvB45mPDGFNvh4WXELMVN6VAoMNMAaGA9VMVeiUPX8U6owx9SIxReNqgVKW4Lr5uCCq5m6+dj+sQMwHseOTat5jAW69Lq9dMkSq0NjnDaL0tj2TJt56lPwzOtqNsMm54VWTfIsp4z1SWjOw9j/WrNXwCeymT90fpilE0laK7LexDckRX82oQIDAQAB",
		
	
);