<?php
session_start();
//memcache_init();
$act = isset($_REQUEST["act"])? $_REQUEST["act"] : "";
if ($act == "logout") {
	//unlink($_SESSION["cookie"]);
	session_destroy();
	header("location: index.php");
	return;
}

include_once("util.php");
$id = $_POST["id"];
$passwd = $_POST["passwd"];
$kick_multi = "1";
$cookie = tempnam(ini_get("upload_tmp_dir"), "CUR");
$_SESSION["cookie"] = $cookie;

//echo $cookie;
$_text = _post("http://www.newsmth.net/bbslogin.php?mainurl=atomic.php", array(
	"id" => $id, "passwd" => $passwd, "kick_multi" => "1"), False, $cookie);

if (strpos($_text, "location.href") != False) {
	$_SESSION["user"] = $id;
	$_SESSION["mycookie"] = file_get_contents($cookie);
	$_text = _get("http://www.newsmth.net/atomic.php", array(), True, $cookie);
	$_SESSION["favor"] = getBoards($_text);
	
	
//echo $_text;
}
//echo "done";
//print_r($_SESSION);

header("Location: index.php");
//echo $_text;

?>