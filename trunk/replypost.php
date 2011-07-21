<?php
include_once("header.php");
include_once("util.php");
include_once("defines.php");

$act = $_REQUEST["act"];
if ($act == "new") {
	$posturl = "http://www.newsmth.net/bbssnd.php?board={$_REQUEST['board']}&reid=0";
	$query = array("title" => _g($_REQUEST["title"]), "text" => _g($_REQUEST["text"] . "\r\n--\r\nSent from cc.hime"));
	$response = _u(_post($posturl, $query));
	if (strpos($response, "成功")) {
		header("Location: board.php?board={$_REQUEST['board']}");
	}
	else header('HTTP/1.0 404 Not Found');
	//print_r($response);
}
?>