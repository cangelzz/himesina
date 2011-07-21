<?php
include_once("header.php");
include_once("util.php");
include_once("defines.php");

$act = $_REQUEST["act"];
$form = <<<EOF
<form action="replypost.php" method="POST">
<input type="text" name="title" />
<input type="submit" value="发表"/>
<textarea name="text"></textarea>
<input type="hidden" name="board" value="{$_REQUEST["board"]}">
<input type="hidden" name="act" value="{$act}">
</form>
EOF;
if ($act == "new") {
	print_header();
	echo $form;
}





include_once("footer.php");
?>