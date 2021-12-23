<?php declare(strict_types=1);
namespace CRM\Utils;

require_once(__DIR__ . '/../crest.php');

// Extended Crest class. Позволяет работать с несколькими битриксами.

class CrestExt extends Crest
{
	public static $web_hook_url = '';

	/**
	* @return mixed setting application for query
	*/

	protected static function getAppSettings()
	{
		if (defined("C_REST_WEB_HOOK_URL") && !empty(C_REST_WEB_HOOK_URL))
		{
			$arData = [
				'client_endpoint' => C_REST_WEB_HOOK_URL,
				'is_web_hook'     => 'Y'
			];
			if (! empty(static::$web_hook_url))
			{
				$arData['client_endpoint'] = static::$web_hook_url;
			}

			$isCurrData = true;
		}
		else
		{
			$arData = static::getSettingData();
			$isCurrData = false;
			if (
				!empty($arData[ 'access_token' ]) &&
				!empty($arData[ 'domain' ]) &&
				!empty($arData[ 'refresh_token' ]) &&
				!empty($arData[ 'application_token' ]) &&
				!empty($arData[ 'client_endpoint' ])
			) 
			{
				$isCurrData = true;
			}
		}

		return ($isCurrData) ? $arData : false;
	}
}
