<?php
include_once 'define.php';
include_once (topDir.'/classes/basePrice.php');
$basePrice = new basePrice();
$html = $basePrice->getHtml();

echo $html;