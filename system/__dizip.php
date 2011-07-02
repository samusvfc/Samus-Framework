<?php

error_reporting(E_ALL);
$fileName = "psdb_13_06_10.zip";
$extractTo = "./";

$zip = new ZipArchive();
$zip->open($fileName);
$zip->extractTo($extractTo);


$zip->close();

echo "<hr />Sucesso<hr />";