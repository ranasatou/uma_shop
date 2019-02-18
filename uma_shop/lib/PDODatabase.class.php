<?php
/*
ファイルパス：C:\xampp\htdocs\uma_shop\lib\PDODatabase.class.php
ファイル名：PDODatabase.class.php(商品に関するプログラムのクラスファイル、Model)
PDO(PHP Data Objects) : PHP標準(5.1.0以降)のDB接続クラス
おすすめ～～～
*/

namespace uma_shop\lib;

class PDODatabase
{
	private $dbh = NULL;
	private $db_host = '';
	private $db_user = '';
	private $db_pass = '';
	private $db_name = '';
	private $db_type = '';
	private $order = '';
	private $limit = '';
	private $offset = '';
	private $groupby = '';
	
	public function __construct($db_host, $db_user, $db_pass, $db_name, $db_type)
	{
		$this->dbh = $this->connectDB($db_host, $db_user, $db_pass, $db_name, $db_type);
		$this->db_host = $db_host;
		$this->db_user = $db_user;
		$this->db_pass = $db_pass;
		$this->db_name = $db_name;
		// SQL関連
		$this->order = '';// 並び順
		$this->limit = '';// 何件表示するか
		$this->offset = '';// 何ページ目か ページャー
		$this->groupby = '';// 集計
	}

	private function connectDB($db_host, $db_user, $db_pass, $db_name, $db_type)
	{
		try { //接続エラー発生→PDOExceptionオブジェクトがスローされる→例外処理をキャッチする
			switch ($db_type) {
                case 'mysql':
                    $dsn = 'mysql:host=' . $db_host . ';dbname=' . $db_name;
                    $dbh = new \PDO($dsn, $db_user, $db_pass);// \PDOクラス 一個前の階層 インスタンス化するとクエリ実行する
                    $dbh->query('SET NAMES utf8');
                    break; // ループの処理終わり

                case 'pgsql':
					$dsn = 'pgsql:dbname=' . $db_name . ' host=' . $db_host . ' port=5432';
					$dbh = new \PDO($dsn, $db_user, $db_pass);
					break;
				}
			}catch (\PDOException $e) {// 接続失敗したときはエラーメッセージが出る
				var_dump($e->getMessage());
				exit();// PHPの処理全てを終わり
			}

			return $dbh;
	}

	public function setQuery($query = '', $arrVal = [])
	{
		$stmt = $this->dbh->prepare($query);
		$stmt->execute($arrVal);
	}

	public function select($table, $column = '', $where = '', $arrVal = [])
	{
		$sql = $this->getSql('select', $table, $where, $column);//getSqlを実行してSQL文をつくる

		$stmt = $this->dbh->prepare($sql);
		$stmt->execute($arrVal);

		// データを連想配列に格納
		$data = [];
		while ($result = $stmt->fetch(\PDO::FETCH_ASSOC)) {// i行ずつ$dataに格納
			array_push($data, $result);
		}	
		return $data;
	}

	public function count($table, $where = '', $arrVal = [])
	{
		$sql = $this->getSql('count', $table, $where);
		$stmt = $this->dbh->prepare($sql);

		$stmt->execute($arrVal);
		$result = $stmt->fetch(\PDO::FETCH_ASSOC);

		return intval($result['NUM']);
    }
    
    public function setOrder($order = '')
    {
        if ($strOrder !== '')
            $this->order = ' ORDER BY ' . $strOrder;
    }

	public function setLimitOff($limit = '', $offset = '')
	{
		if ($limit !== "") $this->limit = " LIMIT " . $limit;
		if ($offset !== "") $this->offset = " OFFSET " . $offset;
	}

	public function setGroupBy($groupby)
	{
		if ($groupby !== "") $this->groupby = ' GROUP BY ' . $groupby;
	}

	private function getSql($type, $table, $where = '', $column = '')
	{
		switch ($type) {
			case 'select'://データを取ってくる
				$columnKey = ($column !== '') ? $column : "*";
				break;

			case 'count':
				$columnKey = 'COUNT(*) AS NUM ';
				break;
			
			default:
				break;
		}

		$whereSQL = ($where !== '') ? ' WHERE ' . $where : '';
		$other = $this->groupby . " " . $this->order . " " . $this->limit . " " . $this->offset;

		// sql文の作成
		$sql = " SELECT " . $columnKey . " FROM " . $table . $whereSQL . $other;
		return $sql;
	}

	public function insert($table, $insData = [])
	{
        list ($preSt, $insDataVal, $columns) = $this->getPreparedStatement('insert', $insData, $table);
        // list:returnしたときに返すとき、配列で返せる
        $sql = " INSERT INTO "
                . $table
                . " ("
                . $columns
                . ") VALUES ("
                . $preSt
                . ") ";

        $stmt = $this->dbh->prepare($sql);
        $res = $stmt->execute($insDataVal);

        return $res;
    }

    public function update($table, $insData = [], $where, $arrWhereVal = [])
    {
        list ($preSt, $insDataVal) = $this->getPreparedStatement('update', $insData, $table);

        // sql文の作成
        $sql = " UPDATE "
                . $table
                . " SET "
                . $preSt
                . " WHERE "
                . $where;

        $updateData = array_merge($insDataVal, $arrWhereVal);
        $stmt = $this->dbh->prepare($sql);
        $res = $stmt->execute($updateData);

        return $res;
    }

    public function getPreparedStatement($mode, $insData, $table)
    {
        if (! empty($insData)) {
            $insDataKey = array_keys($insData);// 連想配列からkeyを取り出して配列にする
            $insDataVal = array_values($insData);// 連想配列から値を取り出して配列にする
            $preCnt = count($insDataKey);// ?の数

            switch ($mode) {
                case 'insert':
                    $columns = implode(',', $insDataKey);// implode:配列を文字列化
                    $arrPreSt = array_fill(0, $preCnt, '?');// array_fill:配列をつくってその中に?を入れる　0から$preCnt分の?
                    $preSt = implode(',', $arrPreSt);
                    return [$preSt, $insDataVal, $columns];
                    break;

                case 'update':
                    for ($i = 0; $i < $preCnt; $i ++) {
                        $arrPreSt[$i] = $insDataKey[$i] . " =? ";
                    }

                    $preSt =implode(',', $arrPreSt);

                    return [$preSt, $insDataVal];
                    break;
            }
        } else {
        return false;
        }
    }

    public function getLastId()
    {
        return $this->dbh->lastInsertId();// customer_noを取ってくる
    }
}