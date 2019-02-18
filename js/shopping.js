$(function(){
    var entry_url = $("#entry_url").val();

    $("#cart_in").click(function(){ // IDcart_in が押された場合
        var item_id = $("#item_id").val();//hiddenの中にある値を取ってくる
        location.href = entry_url + "cart.php?item_id=" + item_id;// =の先に飛ぶよ～ cart.php に?以降のitem_idを送る
    });
});