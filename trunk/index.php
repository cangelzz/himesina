<?php
include_once("header.php");

$favor = isset($_REQUEST["favor"]) ? explode("|", $_REQUEST["favor"]) : null;
$ftype = isset($_REQUEST["ftype"]) ? $_REQUEST["ftype"] : "6";
$user = isset($_SESSION["user"])? $_SESSION["user"] : NULL;
print_header();
echo "<h1>Welcome {$user}</h1>";
echo "<div class=\"nav\"><input id='boardtogo' type=\"text\" style='vertical-align:middle'/><a class='btnForward' onclick=\"this.href='board.php?board='+document.getElementById('boardtogo').value+'&ftype=6'\" style='vertical-align:middle'></a></div>\n";
if (!isset($_SESSION["user"])) {
	echo "<div style='text-align:center;background-color:#FFFFF0;padding:4px;font-size:small'><span style='vertical-align:middle'>请使用 ?favor=Board1|Board2&ftype=6 指定首页默认版面 </span></div>";
}
echo "<ul class='boards'>\n";
echo "<li><a href='top10.php?h=1'>全站十大</a></li>\n";
echo "<li onclick='javascript:$(this).next().toggle()'><a>分区十大</a></li>\n";
echo "<ul style='display:none;padding-left:20px'>";
echo "<li><a href='top10.php?h=2&s=1'>国内院校</a></li>\n";
echo "<li><a href='top10.php?h=2&s=2'>休闲娱乐</a></li>\n";
echo "<li><a href='top10.php?h=2&s=3'>游戏天地</a></li>\n";
echo "<li><a href='top10.php?h=2&s=4'>体育健身</a></li>\n";
echo "<li><a href='top10.php?h=2&s=5'>社会信息</a></li>\n";
echo "<li><a href='top10.php?h=2&s=6'>知性感性</a></li>\n";
echo "<li><a href='top10.php?h=2&s=7'>文化人文</a></li>\n";
echo "<li><a href='top10.php?h=2&s=8'>学术科学</a></li>\n";
echo "<li><a href='top10.php?h=2&s=9'>电脑技术</a></li>\n";
echo "</ul>";

$boards = isset($favor)? $favor : array("Apple", "AutoWorld", "Children");
$boards = isset($_SESSION["favor"])? $_SESSION["favor"] : $boards;

foreach ($boards as $b) {
	echo "<li><a href='board.php?board=" . $b . "&ftype=" . $ftype . "'><div style='display:inline-block'>" . strtoupper($b) . "</div><div style='display:inline-block;float:right'></div></a></li>\n";
}
echo "</ul>\n";
include_once("footer.php");
?>
