<?php
if (isset($_SESSION["user"])) {
	echo "<div id='loginbar'>已登录: {$_SESSION['user']} <a href='login.php?act=logout'>[注销]</a></div>";
} else {
	$out = <<<EOF
<div id="loginbar"><a href='javascript:$("#loginform").toggle()' class='btnLogin btn' style='vertical-align:middle'></a>
<form action="login.php" method="POST" id='loginform'>
<input type="text" name="id" width="30" />
<input type="password" name="passwd" width="30" />
<input type="submit" value="登录" />
</form>
</div>

EOF;
	echo $out;
}
?>

<div id="footer"></div>
<div id="copyright"><a href="index.php">home</a><a href="http://code.google.com/p/himesina/" target="_blank">code</a><a href="about.php">about</a><div>
</body></html>