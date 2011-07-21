<?php
include_once("header.php");
include_once("defines.php");
include_once("util.php");

$h = $_REQUEST["h"];
$s = isset($_REQUEST["s"])? $_REQUEST["s"] : NULL;
if ($h == "1") $title = "全站十大";
else {
	switch ($s) {
		case "1": $title ="国内院校"; break;
		case "2": $title ="休闲娱乐"; break;
		case "3": $title ="游戏天地"; break;
		case "4": $title ="体育健身"; break;
		case "5": $title ="社会信息"; break;
		case "6": $title ="知性感性"; break;
		case "7": $title ="文化人文"; break;
		case "8": $title ="学术科学"; break;
		case "9": $title ="电脑技术"; break;
		
	}
}

$subs = getTops(array("h" => $h, "s" => $s));
print_header();
echo "<h1>{$title}</h1>";
$navlink = "<div class='nav'><a class='btnBack' href='javascript:history.go(-1)'></a><a class='btnGoBoard' href='javascript:goBoard()'></a></div>";
echo $navlink;
echo "<ul class='top10'>\n";
foreach ($subs as $sub) {
	echo $sub->make_list_top();
}
echo "</ul>";

include_once("footer.php");
?>