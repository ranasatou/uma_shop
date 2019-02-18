$(function() {//クリックイベントをすぐにキャッチ、下の処理を動かせるように宣言
	$('#address_search').click(function() {//idがaddress_search

	var zip1 = $('#zip1').val();//var:変数の宣言 id属性(#)
	var zip2 = $('#zip2').val();

	var entry_url = $('#entry_url').val();

	if (zip1.match(/[0-9]{3}/) === null || zip2.match(/[0-9]{4}/) === null) {//正規表現に合ってなかったらnull
		alert('正確な郵便番号を入力してください。');
		return false; //ページ遷移をしない POSTしない
	} else {
		$.ajax({//ajaxを使う宣言
			type : "get",//postかgetを選ぶ
			url : entry_url + "postcode_search.php?zip1=" + escape(zip1) + "&zip2=" + escape(zip2),//+は文字列連結

			//URLのプログラムでechoされたものがdata
			//通信成功：done、通信失敗：fail
			success : function(data) {//()内の変数はなんでもOK　
				if (data == 'no' || data == '') {
					alert('該当する郵便番号がありません');
				} else {
					$('#address').val(data);//id属性がaddressに変数dataが入る
				}
			}
		});
		}
	});
});
