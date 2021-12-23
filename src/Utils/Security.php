<?php
namespace CRM\UTILS;

class Security
{
	public static function getRequestData($variable = 0, $method = 'get')
	{
		return $_GET[$variable] ?? '';
	}	
}
