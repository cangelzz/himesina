<?php
include_once("header.php");
include_once("defines.php");
include_once("util.php");

$bid = $_REQUEST["bid"];
$id = $_REQUEST["id"];
$data = getPostGroup(array("bid" => $bid, "id" => $id));
$title = $data["title"];
$posts = $data["posts"];
//print_r($posts);
print_header();
echo "<h1>{$title}</h1>";
if ($webkit) 
	echo "<div class='nav'><a class='btnExpand' href='javascript:$(\"li\").click()'></a></div>";
echo "<ul class='posts'>\n";
if (!empty($posts)) {
	foreach ($posts as $post) {
		echo $post->make_list();
	}
}
echo "</ul>";
include_once("footer.php");
?>