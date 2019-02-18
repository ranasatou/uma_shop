<?php
/*
ファイルパス：C:\xampp\htdocs\uma_shop\list.php
ファイル名:list.php(商品一覧を表示するプログラム、Controller)
アクセスURL:http://localhost/uma_shop/list.php
*/
namespace uma_shop;

require_once dirname(__FILE__) . '\Bootstrap.class.php';

use uma_shop\Bootstrap;
use uma_shop\lib\PDODatabase;
use uma_shop\lib\Session;
use uma_shop\lib\Item;

$db = new PDODatabase(Bootstrap::DB_HOST, Bootstrap::DB_USER, Bootstrap::DB_PASS, Bootstrap::DB_NAME, Bootstrap::DB_TYPE);
$ses = new Session($db);// ↑で取得したdbをぶちこむ
$itm = new Item($db);

// テンプレート指定
$loader = new \Twig_Loader_Filesystem(Bootstrap::TEMPLATE_DIR);
$twig = new \Twig_Environment($loader, [
	'cache' => Bootstrap::CACHE_DIR
]);

// SessionKeyを見て、DBへの登録状態をチェックする
$ses->checkSession();//SessionクラスのcheckSessionメゾットを使う
$ctg_id = (isset($_GET['ctg_id']) === true && preg_match('/^[0-9]+$/', $_GET['ctg_id']) === 1) ? $_GET['ctg_id'] : '';
// カテゴリーがクリックされていたら　　　　　　　0から9の数字の羅列であれば
// カテゴリーリスト(一覧)を取得する
$cateArr = $itm->getCategoryList();
// 商品リストを取得する
$dataArr = $itm->getItemList($ctg_id);// Itemクラスの
$context = [];
$context['cateArr'] = $cateArr;
$context['dataArr'] = $dataArr;
$template = $twig->loadTemplate('list.html.twig');
$template->display($context);