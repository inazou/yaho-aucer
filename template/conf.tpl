<!doctype html>
<html lang="ja">
    <head>
        <meta charset="utf-8">
        <title>Yaho!-auce</title>
        <meta name="viewport" content="width=device-width">
        <meta name="copyright" content="Template Party">
        <link rel="stylesheet" href="template/css/style.css">
        <link href="template/css/style-s.css" rel="stylesheet" type="text/css" media="only screen and (max-width:480px)">
        <link href="template/css/style-m.css" rel="stylesheet" type="text/css" media="only screen and (min-width:481px) and (max-width:800px)">
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
        <script language=javascript>
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
                        "./category.php" ,
                        {
                            val : opt
                        } ,
                        function(data , status){
                            $("#sCategory").html(data);
                        }
                    );
                });
            });
            
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

<h1><?php echo $msg; ?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $msgPage; ?></h1>
<p><?php echo $result; ?></p>

<?php echo $resItem; ?>

</section>
    <ul class="pageNav01">
<li><a href="1.html">&laquo; 前</a></li>
<li><a href="1.html">1</a></li>
<li><span style="color: black">2</span></li>
<li><a href="3.html">3</a></li>
<li><a href="4.html">4</a></li>
<li><a href="5.html">5</a></li>
<li><a href="6.html">6</a></li>
<li><a href="3.html">次 &raquo;</a></li>
</ul>

</div>
<!--/main-->

<div id="sub">

<nav id="mainmenu">
    <form method="get" action="./conf" id="search">
        <div id="searchBox">
            <input type="text" name="query" id="textBox" placeholder="キーワードから探す"><input type="submit" value="検索" id="btn">
        </div>
<ul>
    
<li><a href="javascript:void(0)" id="category" onclick="show('adSearch');"　>詳細検索設定</a></li>
</ul>
        <div id="layer_adSearch" style="display: none;position:relative" class="close">
                    
                        <p>
                            <label>
                                
                                カテゴリ: <select name = "category"id="mCategory">
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
                                
                                サブカテゴリ: <select name = "category" id="sCategory">
                                        <option value="">
                                            カテゴリを選択してください
                                        </option>
                                </select>
                            </label>
                        </p>
                        <p>
                            <label>
                                
                                並べ替え: <select name = "sort">
                                        <option value="">
                                            選択してください
                                        </option>
                                        <?php
                                            echo $sort;
                                        
                                        ?>
                                </select>
                            </label>
                        </p>
                        <p>表示順: <input type="radio" name="order" value="a" checked>昇順 <input type="radio" name="order" value="d">降順</p>
                        <p>出品者: <input type="radio" name="store" value="0" checked>すべて <input type="radio" name="store" value="1">ストア <input type="radio" name="store" value="2">個人</p>
                        <p>現在価格: 
                        </p>
                        <p>
                            <input type="text" name="aucminprice" id="priceBox" style="width: 90px">円 ～ 
                            <input type="text" name="aucmaxprice" id="priceBox" style="width: 90px">円
                        </p>
                        <p>即決価格: 
                        </p>
                        <p>
                            <input type="text" name="aucmin_bidorbuy_price" id="priceBox" style="width: 90px">円 ～ 
                            <input type="text" name="aucmax_bidorbuy_price" id="priceBox" style="width: 90px">円
                        </p>
                        <p>
                            <label> 
                                出品地域: 
                                <select name = "loc_cd">
                                        <option value="">
                                            すべて
                                        </option>
                                        <?php
                                            echo $locId;
                                        
                                        ?>
                                </select>
                            </label>
                             
                        </p>
                        <p>購入方法: <input type="radio" name="buynow" value="" checked>指定なし <input type="radio" name="buynow" value="1">即決</p>
                        <p>商品状態: <input type="radio" name="item_status" value="0" checked>指定なし <input type="radio" name="item_status" value="1">新品 <input type="radio" name="item_status" value="2">中古</p>
                        <p>アダルト: <input type="radio" name="adf" value="0" checked>指定なし <input type="radio" name="adf" value="1">あり</p>
                        
                        
                    </div>
        </form>
</nav>


<aside class="box1 mb1em">
<p>"xxx"の落札相場<br>
平均落札価格 XXX円<br>
最低落札価格 XXX円<br>
最高落札価格 XXX円</p>
</aside>


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
