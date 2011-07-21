<?php
include_once("header.php");
include_once("defines.php");
include_once("util.php");
$board = $_REQUEST["board"];
$gid = $_REQUEST["gid"];
$page = isset($_REQUEST["pno"]) ? $_REQUEST["pno"] : "";
$query = array("board" => $board, "gid" => $gid, "pno" => $page);
$data = getSubject($query);
$subject = $data["subject"];
$posts = $data["posts"];
print_header();
echo "<h1>{$subject->title}</h1>\n";
$navlink = $subject->make_nav();
echo $navlink;
echo "<ul class='posts' id='posts_ul'>\n";
foreach ($posts as $post) {
	echo $post->make_list();
}
echo "</ul>\n";
echo $navlink;
include_once("footer.php");
?>