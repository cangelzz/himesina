<?php
include_once("header.php");
include_once("defines.php");
include_once("util.php");
$board = $_REQUEST["board"];
$gid = $_REQUEST["gid"];
$lz = isset($_REQUEST["lz"])? $_REQUEST["lz"] : NULL;
$page = isset($_REQUEST["pno"]) ? $_REQUEST["pno"] : "";
$query = array("board" => $board, "gid" => $gid, "pno" => $page);
$data = getSubjectList($query, $lz);
$subject = $data["subject"];
$posts = $data["posts"];
print_header();
echo "<h1>{$subject->title}</h1>\n";
$navlink = $subject->make_nav();
echo $navlink;
echo "<ul class='posts' id='posts_ul'>\n";
echo "</ul>\n";
echo $navlink;
echo "<script type='text/javascript'>var posts = " . json_encode($data["posts"]) . ";\n writePosts();</script>";
include_once("footer.php");
?>