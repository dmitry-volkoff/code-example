<?php
namespace CRM\Utils;

define('');//url on create Webhook

// коробка
//define('C_REST_WEB_HOOK_URL','');//url on creat Webhook

//define('C_REST_CURRENT_ENCODING','utf8');
//define('C_REST_IGNORE_SSL',true);//turn off validate ssl by curl
define('C_REST_IGNORE_SSL',true);

define('ALLOW_LOG',0);

if (ALLOW_LOG)
{
	define('C_REST_LOG_TYPE_DUMP', true); //logs save var_export for viewing convenience
	define('C_REST_BLOCK_LOG', false);
	//define('C_REST_LOGS_DIR', __DIR__ .'/logs/'); //directory path to save the log
}
else
{
	define('C_REST_LOG_TYPE_DUMP', false);
	define('C_REST_BLOCK_LOG', true);//turn off default logs
}