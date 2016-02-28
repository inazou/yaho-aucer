<?php

include_once 'define.php';
include_once (topDir.'/classes/baseConf.php');
$baseConf = new baseConf();

$dir = $baseConf->getTempDir();
$category = $baseConf->getCategory();
$sort = $baseConf->getSort();
$locId = $baseConf->getLocId();
$result = $baseConf->getResult();
$resItem = $baseConf->getResItem();
$msg = $baseConf->getMsg();
$msgPage = $baseConf->getMsgPage();
$pager = $baseConf->getPager();
$data = $baseConf->getData();
include_once 'template/conf.tpl';
