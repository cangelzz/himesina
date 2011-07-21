<?php
include_once("header.php");
include_once("defines.php");
include_once("util.php");

$bid = $_REQUEST["bid"];
$id = $_REQUEST["id"];
$p = isset($_REQUEST["p"])? $_REQUEST["p"] : NULL;
$post = getPost(array("bid" => $bid, "id" => $id, "p" => $p));
print_header();
echo "<h1>{$post->title}</h1>";
echo $post->make_nav_top();
echo "<ul class='singlepost'>\n";
//print_r($post);
echo $post->make_list();
echo "</ul>";
echo $post->make_nav_bottom();
include_once("footer.php");
?>