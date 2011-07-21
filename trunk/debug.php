<?php
include_once("defines.php");
include_once("util.php");

//$board = "WorkLife";
//$gid = 	"5435989";
//$id = "5435990";
//$bid = "383";

function d1() {
	global $board;
	$query = array("board" => $board, "ftype" => "6", "page" => "");
	$_text = _u(_get("http://www.newsmth.net/bbsdoc.php", $query));
	//$_text = getThreads($query);
	$p = "/c\\.o\\((\\d+),(\\d+),'(.*?)','(.*?)',(\\d+),'(.*?)',(\\d+),\\d+,\\d+\\)/";
	$ps = "/docWriter\\('(.*?)',(\\d+),(\\d+),(\\d+),(\\d+),(\\d+),(\\d+)/";
	$basic = preg_match($ps, $_text, $info);
	$cnt_threads= preg_match_all($p, $_text, $subs, PREG_SET_ORDER);
	print_r($info);
	$b = new Board();
	$b->bname = $info[0];
	$b->bid = $info[1];
	$b->page = $info[5];
	$b->total = $info[6];
	$threads = array();
	for ($i = 0; $i < $cnt_threads; $i++) {
		$sub = new Subject();
		$sub->gid = $subs[$i][1];
		$sub->author = $subs[$i][3];
		$sub->flag = $subs[$i][4];
		$sub->time = $subs[$i][5];
		$sub->title = $subs[$i][6];
		$sub->size = $subs[$i][7];
		array_push($threads, $sub);
	}
	print_r (array("board" => $b, "threads" => $threads));
}

function d2() {
	$query = array("board" => "WorkLife", "gid" => "5435989", "pno" => "2");
	$_text = _u(_get("http://www.newsmth.net/bbstcon.php", $query));
						//bid               tpage pno        psub  nsub  title 
	$p = "/tconWriter.*?(\\d+),\\d+,\\d+,(\\d+),(\\d+),\\d+,(\\d+),(\\d+),'(.*?)'/";
	$pt = "/\\[(\\d+),'(.*?)'\\]/";
	$r1 = preg_match($p, $_text, $info);
	$r2 = preg_match_all($pt, $_text, $postlist, PREG_SET_ORDER);
	$sub = new Subject();
	$sub->bname = $query["board"];
	$sub->gid = $query["gid"];
	$sub->bid = $info[1];
	$sub->tpage = $info[2];
	$sub->pno = $info[3];
	$sub->pthid = $info[4];
	$sub->nthid = $info[5];
	$sub->title = $info[6];
	$sub->author = isset($query["lz"])? $query["lz"] : $postlist[0][2];
	$posts = array();
	$urls = array();
	foreach ($postlist as $postline) {
		array_push($urls, "http://www.newsmth.net/bbscon.php?bid={$sub->bid}&id={$postline[1]}");
	}
	print_r($sub);
	print_r(d3($urls));
}

function d3($urls) {
	$mh = curl_multi_init();
	foreach ($urls as $i => $url) {
     	$conn[$i]=curl_init($url);
     	curl_setopt($conn[$i],CURLOPT_RETURNTRANSFER,1);
      	curl_multi_add_handle ($mh,$conn[$i]);
	}

	do {
        $mrc = curl_multi_exec($mh,$active);
    } while ($mrc == CURLM_CALL_MULTI_PERFORM);
    while ($active and $mrc == CURLM_OK) {
        if (curl_multi_select($mh) != -1) {
            do {
                $mrc = curl_multi_exec($mh, $active);
            } while ($mrc == CURLM_CALL_MULTI_PERFORM);
        }
    }

	foreach ($urls as $i => $url) {
    	$res[$i]=curl_multi_getcontent($conn[$i]);
      	curl_close($conn[$i]);
	}
	$posts = array();
	foreach ($res as $text) {
		array_push($posts, d4($text));
	}
	return $posts;
}

function d4($_text) {
	//$_text = $_text;
	preg_match("/发信人: ((.*?)\s.*?), 信区/", $_text, $longauthor);
	print_r($longauthor);
	//$longauthor = substr($_text, strpos($_text, "发信人") + 3, strpos($_text, "信区") - strpos($_text, "发信人"));
	$post = new Post();
	$post->longauthor = $longauthor[1];
	$post->author = $longauthor[2];
    $p = '/站内(.*)(--|※)/';
    //                            bname     bid         id     gid      rid        title
    $pt = "/conWriter\\(\\d,\\s'(\\w+)',\\s(\\d+),\\s(\\d+),\\s(\\d+),\\s(\\d+),.*'(.*?)'/";
	$content = "";
    $reply = "";
	$r1 = preg_match($pt, $_text, $info);
	$r2 = preg_match($p, $_text, $full);
	$post->bname = $info[1];
    $post->bid = $info[2];
    $post->id = $info[3];
    $post->gid = $info[4];
    $post->rid = $info[5];
    $post->title = $info[6];

	if ($r2 == 0)
		$content = "error";
	else {
        $content = _t($full[1]);
        
        $idx = strpos($content, "【");
        if ($idx != -1) {
            $reply = substr($content, $idx);
            $content = substr($content, 0, $idx);
            $pr = "/【.*?(\\w+).*?】\n?/";
            $reply = preg_replace($pr, "[\\1]", $reply);
        }
    }
	$post->content = trim($content);
    $post->reply = trim($reply);
    
    $pa = "/attach\\('(.*?)',\\s(\\d+),\\s(\\d+)\\);/";
    $pf = "/(jpg|png|jpeg|gif)/i";
    $cntatt = preg_match_all($pa, $_text, $atts, PREG_SET_ORDER);
    print_r($atts);
    $urls = array();
    foreach ($atts as $att) {
    	$fname = $att[1];
    	$ext = substr($fname, strrpos($fname, "."));
    	if (preg_match($pf, $fname) > 0) {
    		array_push($urls, "http://att.newsmth.net/att.php?p.{$post->bid}.{$post->id}.{$att[3]}{$ext}");
    	}
    }
    $post->images = $urls;
    return $post;
}

function d5() { 
	$text = <<<EOF
var o = new conWriter(0, 'MyPhoto', 874, 1690559, 1690559, 1690559, '<a href=\"bbssfav.php?act=choose&title=%B5%DA%D2%BB%B4%CE%B7%A2PP%A3%AC%BB%E9%C9%B4%D5%D5%A3%BA%A3%A9&url=bbscon.php%3Fbid%3D874%26id%3D1690559&type=0\">百宝箱</a>', 21992, 0,'第一次发PP，婚纱照：）');
o.h(1);
att = new attWriter(874,1690559,0,21992,1);
prints('发信人: loveappley (Iris), 信区: MyPhoto\n标  题: 第一次发PP，婚纱照：）\n发信站: 水木社区 (Tue Jul 12 23:24:31 2011), 站内\n\n昨天刚选的片，很高兴，发上来和大家分享，庆祝我们开始人生的新阶段。\n我和老公都是普通人，第一次发PP，大家轻拍啊。\n--\n\n\r[m\r[37m※ 来源:·水木社区 http:\/\/newsmth.net·[FROM: 117.73.245.*]\r[m\n\n');attach('1.jpg', 73865, 319);attach('2.jpg', 124875, 74202);attach('3.jpg', 118815, 199095);attach('4.jpg', 84050, 317928);attach('5.jpg', 103007, 401996);attach('6.jpg', 92097, 505021);attach('9.jpg', 140449, 597136);attach('8.jpg', 92848, 737603);attach('p_large_ATUD_3df400031ce05c42.jpg', 168139, 830469);o.h(0);o.t();
EOF;
	print_r( d4($text) );
}

function d6() {
	$_text = _get("http://www.newsmth.net/rssi.php", array("h" => 1));
	$cnt = preg_match_all("/<link>.*?(?<=board=)(\\w+).*?gid=(\\d+)/", $_text, $bname_and_id, PREG_SET_ORDER);
    preg_match_all("/<description>.*?发信人:\\s(.*?),.*?标\\s+题:\\s?(.*?)<br.*?站内(.*)(>--|※|\]\])/", $_text, $title_and_author, PREG_SET_ORDER);
    print_r($bname_and_id);
    print_r($title_and_author);
    $subs = array();
  	for ($i = 0; $i < $cnt; $i++) {
  		$sub = new Subject();
  		$sub->bname = $bname_and_id[$i][1];
  		$sub->gid = $bname_and_id[$i][2];
  		$sub->title = $title_and_author[$i][2];
  		$sub->author = l2s($title_and_author[$i][1]);
  		$sub->preview = $title_and_author[$i][3];
  		array_push($subs, $sub);
  	}
  	print_r($subs);
}

function l2ss($long) {
	return substr($long, 0, strpos($long, " ") - 1);
}
/*
$cookie = tempnam ("tmp", "CURLCOOKIE");
echo $cookie;
$id = $_REQUEST["id"];
$pass = $_REQUEST["pass"];
$_text = _post("http://www.newsmth.net/bbslogin.php?mainurl=atomic.php", array(
	"id" => $id, "passwd" => $pass, "kick_multi" => "1"), False, $cookie);
echo $_text;

if (strpos($_text, "location.href")) {
	$_text = _get("http://www.newsmth.net/atomic.php", array(), True, $cookie);
	echo $_text;
}*/

/*$text = <<<EOF
<html><head><meta http-equiv="content-type" content="text/html; charset=gb2312"><title>水木社区</title><style>a{text-decoration:none;}</style></head><body><p>欢迎 Silverna. <a href='?act=logout'>注销</a></p><p>顶层收藏夹: <a href="?act=board&board=Apple">Apple</a> <a href="?act=board&board=AutoWorld">AutoWorld</a> <a href="?act=board&board=Children">Children</a> <a href="?act=board&board=DigiHome">DigiHome</a> <a href="?act=board&board=Gentleman">Gentleman</a> <a href="?act=board&board=ITExpress">ITExpress</a> <a href="?act=board&board=OurEstate">OurEstate</a> <a href="?act=board&board=PocketLife">PocketLife</a> <a href="?act=board&board=RealEstate">RealEstate</a> <a href="?act=board&board=RealEstate_review">RealEstate_review</a> <a href="?act=board&board=SchoolEstate">SchoolEstate</a> <a href="?act=board&board=WorkLife">WorkLife</a> </p><p><a href='?act=mail'>信箱</a>: 0 封, 新信: 0 封. <a href='?act=mailpost'>写信</a></p><form action="" method="get"><input type="hidden" name="act" value="board"/> 
去讨论区: <input type="text" name="board" /> <input type="submit" value="Go"/> <a href='?'>回首页</a> 
</form>UTF8: OFF. 文章显示长度限制: 20000. &lt;精简歪脖 atppp 制造&gt;<p>友情链接<br/> 
			<li><a href='index1.html'>stiger 精简歪脖</a></li><li><a href='atomic2.php'>精简歪脖i</a></li></p></body></html>
EOF;

preg_match_all("/board=(\\w+)/", $text, $boards);
print_r($boards); */

echo tempnam("saemc://", "CUR");

?>