<?php

include_once 'define.php';
include_once (topDir.'/classes/baseConf.php');
$baseConf = new baseConf();

$dir = $baseConf->getTempDir();
$category = $baseConf->getCategory();
$sort = $baseConf->getSort();
$result = $baseConf->getResult();
$resItem = $baseConf->getResItem();
$msg = $baseConf->getMsg();
include_once 'template/service.html';
