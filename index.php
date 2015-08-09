<?php

include_once 'define.php';
include_once (topDir.'/classes/baseIndex.php');
$baseIndex = new baceIndex();
$dir = $baseIndex->getTempDir();
$category = $baseIndex->getCategory();
include_once $dir.'/index.tpl';
