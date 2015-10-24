<?php
include_once 'define.php';
include_once (topDir.'/classes/baseCategory.php');
$baseCategory = new baseCategory();
$html = $baseCategory->getHtml();
echo $html;
