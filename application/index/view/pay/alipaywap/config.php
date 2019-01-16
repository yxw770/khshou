<?php
$directory = isset($directory) ? $directory : 'alipaywap';
$orderid = isset($orderid) ? $orderid : '';
$config = array (	
		//应用ID,您的APPID。
		'app_id' => "2018121562576045",

		//商户私钥，您的原始格式RSA私钥
		'merchant_private_key' => "MIIEowIBAAKCAQEAxZ01Gh41ITYOLRQNtUJFFe+12Zqu6sO4ql7QUyART0y4vTEVVPxxMVrgnLBzgUDUErbXYoYtvOrFI1KwdXAN+JBU4bdeZC+WVuAn/KhWdh/07uS8Aoc/7FKD9U+Cn5yHXVOZsLxCL+Nz5CxLW3LglN02+Vm1f7b4eAILnn++fwmhLRcyjBtVj2UVTU/7qkkkSNMGSD0eotOjxiWzJAZXVaRXibNZq3Q02NDQ/zK8/Lp3IqRrhirHb1546kt+X+tAy0ETdmxm5ZvFm9YeR2HewXxGE3q74Z06Q/aa1qpv8GUDuHF1xQM6eygQDGyu+3om4dQK2krCrUSIb6z0Q7swGwIDAQABAoIBAE89PXjOmhFKIp8SxnhjLV6hczLhYrhCaP7v6+sZFbfNlNpQHzSn0R+wSBasbnRqsV8br/wSv65cGVaTpqxAlWmRPmgP5iqYJlmJop8eRSUypT1RvM3qu8ggQkeQbVlhI6pZhmwm4Qdg1iytUj5GLyWiGpQb07p1fOZqM4yHvrQjTMwhiyohUUCJ/kyFLJuwBSS3aR9SIEV1HPO8ssHrpGgTSPXv1kO5N866uIWiyFfGGSBPJgOy+ARC59pt1rjcA5nuR0A13qHHuZh8Vd++mUrRdcxErLm8czQmz5TZW4SPTxzppx+viTIYvzGnPSpjCdz21H1Gwj0w1vDzEJS21xkCgYEA61nmdgxmYHPjBJcQ3GAVuew9KU5x6Iyhpbh13x5wvVYD/uEB6URzIPKTL0It3/S4zqoI8B2GxJ1vPYDlGiAzNrG2n8TgLdvaamMqal3WUO9vIkhg96TZbwZ7PbgvCNrysyPK8jombVG6qHJhDkwTQBMXF3/WkNv+Un/WTSX29X0CgYEA1vO23aSc+Ejc1tn26Aboy9u8w1ajhKWtA+tcW3Ga9M2MNcWlrdFbxtp6O2EzhqWcoPTUbayvoDQSg93asf/sceM0CEIaOljOGD7lcpWO2yU6SUWiyFCqTPkiceG74P2/MuVStNJtgSvJD42XZvXBAlZdKCbhHFF2pQ1EknJbz3cCgYBIXy/QU0XUGS7RRYcIzX9A2hWAsz+x7DT1GgEdKGYwwtedtCF7UvI4Sn/aQ3aJ7N47puvyspGzulnvxsgsvYQiKInpHYh2can7lxz+8nYqE0bQewNeg9HdI1gYhZ/pEDMbxUDuNdWFPmGw1ONlmx08UeKC9mvLxeqyAd+rf6YunQKBgGL5680ZPFIzMsUXrlJexBkCgGOt5DNzGjzAxlEw+XoZn6Mu9EAaM9lXxorLEi/A2GNg+OPbbS8maxQfNtFZl6VFSjM3RN5y/8s4QuzFIveTp8gPcYotYo149jxcBefuz3h/EdtDPbsJz1YDC6EULiCGZfTUGjmw5SHQ/y8zy0S3AoGBALlQ1BNAwd+dbLILAf3V+KnupnOlrVDMI0S8t27VrXiiWJUbw/8Il8kQyPhvR32OD2bEJlHB/K+N6ptmPFWnAxuu0T5+urEWCDeaVOCHEnp7QYMhxVe2hDnMKkuuv0KukVc6GWHkR1gUQXNFK3E5E4gO3gf8SxhMAjQS5tDq4z4I",

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