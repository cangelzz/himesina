<?php
include_once("header.php");
include_once("defines.php");
include_once("util.php");
/*$script = <<<EOF
<script>
	$(document).ready(function () {
		$(".att").add("<img src='icon/attachment.png'/>");
	}
</script>
EOF;
	
echo $script;
*/
$board = $_REQUEST["board"];
$ftype = isset($_REQUEST["ftype"]) ? $_REQUEST["ftype"] : "6";
$page = isset($_REQUEST["page"]) ? $_REQUEST["page"] : "";
$query = array("board" => $board, "ftype" => $ftype, "page" => $page);
$data = getThreads($query);
$threads = $data["threads"];
$b = $data["board"];
print_header();
echo $b->make_nav();
echo "<ul class='threads' id='threads_ul'>\n";
foreach ($threads as $sub) {
	echo $sub->make_list();
}
echo "</ul>\n";
echo $b->make_nav();
include_once("footer.php");
?>