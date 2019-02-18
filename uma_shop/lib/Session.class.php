<?php
/*
ファイルパス：C:\xampp\htdocs\uma_shop\lib\Session.class.php
ファイル名：Session.class.php (セッション関係ののクラスファイル、Model)
セッション：サーバー側に一時的にデータを保存する仕組みのこと
基本的に、keyで判断をして、IDを取るというのが流れ
*/

namespace uma_shop\lib;

class Session
{
    public $session_key = '';
    public $db = NULL;

    public function __construct($db)//このクラスでも$dbを使えるようにする
    {
        // セッションをスタートする
        session_start();
        // セッションIDを取得する
        $this->session_key = session_id();
        // DBの登録
        $this->db = $db;
    }

    public function checkSession()
    {
        // セッションIDのチェック
        $customer_no = $this->selectSession();
        // セッションIDがある(過去にショッピングカートに来たことがある)
        if ($customer_no !== false) {
            $_SESSION['customer_no'] = $customer_no;//$_SESSION:session用の関数 どのページでも使える
        } else {
            // セッションIDがない(初めてこのサイトに来ている)
            $res = $this->insertSession();
            if ($res === true) {
                $_SESSION['customer_no'] = $this->db->getLastId();// customer_noを取ってくる
            } else {
                $_SESSION['customer_no'] = '';
            }
        }
    }

    private function selectSession()
    {
        $table = '  session ';//テーブル名
        $col = '  customer_no ';//取ってくるカラム
        $where = '  session_key = ? ';//絞り込み prepared statement
        $arrVal = [$this->session_key];//配列で↑を入れる

        $res = $this->db->select($table, $col, $where, $arrVal);//セレクトを実行　このクラスのdbというクラス(PDOクラス)のselectというメゾットを実行
        return (count($res) !== 0) ? $res[0]['customer_no'] : false;//countが0でなければ0行目にあるcustomer_noを取ってくる
    }

    private function insertSession()
    {
        $table = '  session ';
        $insData = ['session_key ' => $this->session_key];
        $res = $this->db->insert($table, $insData);
        return $res;
    }
}
