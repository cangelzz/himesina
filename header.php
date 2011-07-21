<?php
session_start();
function print_header() {
echo <<<EOF
<html><head>
<title>HIME</title>
<link rel="Stylesheet" href="static/my.css" media="screen" type="text/css" />
<link rel="Stylesheet" href="static/normal.css" media="screen" type="text/css" />
<!-- link rel="stylesheet" media="only screen and (-webkit-min-device-pixel-ratio: 2)" type="text/css" href="static/iphone4.css" /-->
<meta http-equiv="content-type" content="text/html; charset=utf-8">
<meta name="robots" content="noindex,nofollow,noarchive,nosnippet">
<meta name="viewport" content="width=device-width, user-scalable=no">
<meta name="viewport" content="initial-scale=1.0; maximum-scale=1.0; user-scalable=0;" />
<script>
function setOrientation() {
  var orient = (window.innerWidth||320)==320?"portrait":"landscape";
  var cl = document.body.className;
  cl = cl.replace(/portrait|landscape/, orient);
  document.body.className = cl;
};

window.addEventListener('load', setOrientation, false);
window.addEventListener('orientationchange', setOrientation, false);
</script>
<script src="static/jquery.js"></script><script src="static/my.js"></script>
</head><body>
EOF;
}
?>
