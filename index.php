<?php

include_once 'define.php';
include_once (topDir.'/classes/baseIndex.php');
$baseIndex = new baceIndex();
$dir = $baseIndex->getTempDir();
include_once $dir.'/index.tpl';
include_once $dir .'/footer.tpl';
