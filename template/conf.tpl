<!doctype html>
<html lang="ja">
    <head>
        <meta charset="utf-8">
        <title>Yaho!-aucer</title>
        <meta name="viewport" content="width=device-width">
        <meta name="copyright" content="Template Party">
        <meta name="keywords" content="ヤフオク, 検索, 出品, yahoo, ヤフー, オークション, 価格">
        <meta name="description" content="ヤフーオークション出品者向け価格検索サイト。現在どれぐらいの価格なら出品物が入札されるのか簡単に検索できます。">
        <link rel="stylesheet" href="template/css/style.css" type="text/css">
        <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.7.0/jquery.min.js"></script>
<!--[if lt IE 9]>
<script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script>
<style>
#main h1 {
	background: #b3b3b3 url(images/bg1.png);
}
nav#mainmenu ul li a {
	background: #FFF url(images/arrow1.png) no-repeat 10px center;
}
</style>
<![endif]-->
        <script type="text/javascript">
            
            //詳細検索の表示非表示切り替え
            function show(inputData){
                var objID=document.getElementById( "layer_" + inputData );
                if(objID.className =='close') {
                    objID.style.display='block';
                    objID.className ='open';
                }else{
                    objID.style.display='none';
                    objID.className='close';
                }
            }
            $(function(){
                $('#mCategory').change(function(){
                    //  親要素が変更した時の処理
                    var opt = $("#mCategory option:selected").val();
                    $.post(
                        "./category" ,
                        {
                            val : opt
                        } ,
                        function(data){
                            $("#sCategory").html(data);
                        }
                    );
                });
            });
            
            $(document).ready(function(){
                var opt = $("#mCategory option:selected").val();
                var sOpt = <?php if(isset($data["sCategory"])){echo json_encode($data["sCategory"]);}else{echo "''";} ?>;
                $.post(
                    "./category" ,
                    {
                        val : opt,
                        sVal : sOpt
                    } ,
                    function(data){
                        $("#sCategory").html(data);
                        getPrice();
                    }
                );
        
                function getPrice(){
                    $.post(
                        "./price" ,
                        {
                            query : $("#textBox").val(),
                            mCategory : $("#mCategory option:selected").val(),
                            sCategory : $("#sCategory option:selected").val(),
                            store : $('input[name="store"]:checked').val(),
                            loc_cd : $("#loc_cd option:selected").val(),
                            buynow : $('input[name="buynow"]:checked').val(),
                            item_status : $('input[name="item_status"]:checked').val(),
                            adf : $('input[name="adf"]:checked').val()
                        } ,
                        function(data){
                            $("#price").html(data);
                        }
                    );
                }
            })
        </script> 
    </head>
<body>

<div id="container">

<header>
    <h1>
        <img src="template/images/yaho-aucer.png" width="150" height="50" alt=""><br>ヤフオク！出品者向け検索サイト
    </h1>
    
</header>

<div id="contents">

<div id="main">

<section class="list">

    <h1><div class="msg"><?php echo $msg; ?></div><div class="msgPage"><?php echo $msgPage; ?>&nbsp;</div><br clear="all"></h1>
<p><?php echo $result; ?></p>

<?php echo $resItem; ?>

</section>
<?php if(isset($pager)) echo $pager; ?>

</div>
<!--/main-->

<div id="sub">

<nav id="mainmenu">
    <form method="get" action="./conf" id="search">
        <div id="searchBox">
            <input type="text" name="query" id="textBox" value="<?php if(isset($data['query'])) echo htmlspecialchars($data['query'], ENT_QUOTES, 'UTF-8'); ?>" placeholder="キーワードから探す"><input type="submit" value="検索" id="btn">
        </div>
<ul>
    
<li><a href="javascript:void(0)" id="category" onclick="show('adSearch');"　>詳細検索設定</a></li>
</ul>
        <div id="layer_adSearch" style="display: none;position:relative" class="close">
                    
                        <p>
                            <label>
                                
                                カテゴリ: <select name = "mCategory"id="mCategory">
                                        <option value="">
                                            選択してください
                                        </option>
                                        <?php
                                            echo $category;
                                        
                                        ?>
                                </select>
                            </label>
                        </p>
                        <p>
                            <label>
                                
                                サブカテゴリ: <select name = "sCategory" id="sCategory">
                                        <option value="">
                                            カテゴリを選択してください
                                        </option>
                                </select>
                            </label>
                        </p>
                        <p>
                            <label>
                                
                                並べ替え: <select name = "sort" id="sort">
                                        <option value="">
                                            選択してください
                                        </option>
                                        <?php
                                            echo $sort;
                                        
                                        ?>
                                </select>
                            </label>
                        </p>
                        <p>表示順: <input type="radio" name="order" value="a" checked>昇順 <input type="radio" name="order" value="d" <?php if(isset($data['order']) && $data['order'] == 'd') echo 'checked'; ?> >降順</p>
                        <p>出品者: <input type="radio" name="store" value="0" checked>すべて <input type="radio" name="store" value="1" <?php if(isset($data['store']) && $data['store'] == 1) echo 'checked'; ?> >ストア <input type="radio" name="store" value="2" <?php if(isset($data['store']) && $data['store'] == 2) echo 'checked'; ?> >個人</p>
                        <p>現在価格: 
                        </p>
                        <p>
                            <input type="text" name="aucminprice" value="<?php if(isset($data['aucminprice'])) echo htmlspecialchars($data['aucminprice'], ENT_QUOTES, 'UTF-8'); ?>" id="priceBox" style="width: 90px">円 ～ 
                            <input type="text" name="aucmaxprice" value="<?php if(isset($data['aucmaxprice'])) echo htmlspecialchars($data['aucmaxprice'], ENT_QUOTES, 'UTF-8'); ?>" id="priceBox" style="width: 90px">円
                        </p>
                        <p>即決価格: 
                        </p>
                        <p>
                            <input type="text" name="aucmin_bidorbuy_price" value="<?php if(isset($data['aucmin_bidorbuy_price'])) echo htmlspecialchars($data['aucmin_bidorbuy_price'], ENT_QUOTES, 'UTF-8'); ?>" id="priceBox" style="width: 90px">円 ～ 
                            <input type="text" name="aucmax_bidorbuy_price" value="<?php if(isset($data['aucmax_bidorbuy_price'])) echo htmlspecialchars($data['aucmax_bidorbuy_price'], ENT_QUOTES, 'UTF-8'); ?>" id="priceBox" style="width: 90px">円
                        </p>
                        <p>
                            <label> 
                                出品地域: 
                                <select name = "loc_cd" id="loc_cd">
                                        <option value="">
                                            すべて
                                        </option>
                                        <?php
                                            echo $locId;
                                        
                                        ?>
                                </select>
                            </label>
                             
                        </p>
                        <p>購入方法: <input type="radio" name="buynow" value="" checked>指定なし <input type="radio" name="buynow" value="1" <?php if(isset($data['buynow']) && $data['buynow'] == 1) echo 'checked'; ?> >即決</p>
                        <p>商品状態: <input type="radio" name="item_status" value="0" checked>指定なし <input type="radio" name="item_status" value="1" <?php if(isset($data['item_status']) && $data['item_status'] == 1) echo 'checked'; ?> >新品 <input type="radio" name="item_status" value="2" <?php if(isset($data['item_status']) && $data['item_status'] == 2) echo 'checked'; ?> >中古</p>
                        <p>アダルト: <input type="radio" name="adf" value="0" checked>なし <input type="radio" name="adf" value="1" <?php if(isset($data['adf']) && $data['adf'] == 1) echo 'checked'; ?> >あり</p>
                        
                        
                    </div>
        </form>
</nav>

<div id="price">
</div>

</div>
<!--/sub-->

<p id="pagetop"><a href="#">↑ PAGE TOP</a></p>


</div>
<!--/contents-->

<footer>
<small>Copyright&copy; 2015  Sota.Inami　All Rights Reserved.</small>
<span class="pr"><a href="http://template-party.com/" target="_blank">Web Design:Template-Party</a></span>
</footer>

</div>
<!--/container-->




</body>
</html>
