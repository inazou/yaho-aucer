<!DOCTYPE html>
<html lang = "ja">
    <head>
        <meta charset="UTF-8"/>
        <title>Yaho!-aucer</title>
        <link href="template/css/index.css" rel="stylesheet" type="text/css"/>
        <script language=javascript>
            <!--
                function show(inputData){
                    var objID=document.getElementById( "layer_" + inputData );
                    var buttonID=document.getElementById( "category_" + inputData );
                    if(objID.className =='close') {
                        objID.style.display='block';
                        objID.className ='open';
                    }else{
                        objID.style.display='none';
                        objID.className='close';
                    }
                }
            -->
        </script> 
    </head>
    <body>
        <div class="header-fixed">
            <div class="header">
                <h1>ヤフオク！検索統計サイト</h1>
            </div>
        </div>
        <div class="body-bk">
            <div class="topimg">
                <img src="template/images/yaho-aucer.png" alt="ロゴ" />
            </div>
        
            <form method="get" action="conf.php" class="search">

                <div class="searchBox">
                    <input type="text" name="query" class="textBox"><input type="submit" value="検索" class="btn">
                </div>

                <div class="b">
                
            
                    <a href="javascript:void(0)" id="category_詳細検索" onclick="show('詳細検索');"　>詳細検索</a>
                    <div id="layer_詳細検索" style="display: none;position:relative;margin-left:15pt" class="close">
                    
                        <p>
                            <label>
                                
                                カテゴリ:<select name = "category">
                                        <option value="">
                                            選択してください
                                        </option>
                                        <?php
                                            echo $category;
                                        
                                        ?>
                                </select>
                            </label>
                        </p>
                        展開行２<br>
                        展開行３<br>
                    </div>
                </div>
                        
            </form>
        </div>    
        