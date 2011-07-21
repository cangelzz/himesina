<?php
include_once("header.php");
include_once("defines.php");
include_once("util.php");

$bid = $_REQUEST["bid"];
$id = $_REQUEST["id"];
$lz = isset($_REQUEST["lz"])? $_REQUEST["lz"] : NULL;
$p = isset($_REQUEST["p"])? $_REQUEST["p"] : NULL;
$post = getPost(array("bid" => $bid, "id" => $id, "p" => $p), $lz);
echo $post->make_list(0);
?>