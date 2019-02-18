<?php
/*
ファイルパス：C:\xampp\htdocs\uma_shop\Bootstrap.class.php
ファイル名：Bootstrap.class.php(設定に関するプログラム)
*/
namespace uma_shop;

require_once dirname(__FILE__) . '\..\vendor\autoload.php';

class Bootstrap
{
	const DB_HOST = 'localhost';
	const DB_NAME = 'uma_shop';
	const DB_USER = 'uma_shop_user';
    const DB_PASS = 'uma_shop_pass';
    const DB_TYPE = 'mysql';
	const APP_DIR = 'c:/xampp/htdocs/';
	const TEMPLATE_DIR = self::APP_DIR . 'templates/uma_shop/';
	const CACHE_DIR = self::APP_DIR . 'templates_c/uma_shop/';
	const APP_URL = 'http://localhost/';
	const ENTRY_URL = self::APP_URL . 'uma_shop/';
	const DIRECTORY_SEPARATOR = '/';

	public static function loadClass($class)
	{
		$path = str_replace( '\\', self::DIRECTORY_SEPARATOR, self::APP_DIR . $class . '.class.php');
		require_once $path;
	}
}

//これを実行しないとオートローダーとして動かない
spl_autoload_register([
	'uma_shop\Bootstrap',
	'loadClass'
]);

