<?php

function _get($url, $query, $follow = False, $cookie = NULL, $cookiejar = NULL) {
	if (!empty($quer));
		$url = $url . "?" . http_build_query($query);
	$curl = curl_init();
	curl_setopt($curl, CURLOPT_HTTPGET, true);
	curl_setopt($curl, CURLOPT_URL, $url);
	curl_setopt($curl, CURLOPT_HEADER, false);
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($curl, CURLOPT_FOLLOWLOCATION, $follow);
	if (isset($_SESSION["mycookie"])) {
		$cookie = tempnam("upload_tmp_dir", "CUR");
		file_put_contents($cookie, $_SESSION["mycookie"]);
		curl_setopt($curl, CURLOPT_COOKIEFILE, $cookie);
	}
	$data = curl_exec($curl);
	curl_close($curl);
	return $data;
}

function _post($url, $query, $follow = False, $cookiejar = NULL, $cookie = NULL) {
	$curl = curl_init();
	curl_setopt($curl, CURLOPT_POST, true);
	curl_setopt($curl, CURLOPT_URL, $url);
	curl_setopt($curl, CURLOPT_POSTFIELDS, $query);
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($curl, CURLOPT_FOLLOWLOCATION, $follow);
	if (isset($_SESSION["mycookie"])) {
		$cookie = tempnam("upload_tmp_dir", "CUR");
		file_put_contents($cookie, $_SESSION["mycookie"]);
		curl_setopt($curl, CURLOPT_COOKIEFILE, $cookie);
	}
	//if (empty($cookie) && isset($_SESSION["cookie"])) $cookie = $_SESSION["cookie"];
	
	if (!empty($cookiejar)) curl_setopt($curl, CURLOPT_COOKIEJAR, $cookiejar);
	$data = curl_exec($curl);
	curl_close($curl);
	return $data;
}

function _u($source) {
	return iconv("CP936", "UTF-8//IGNORE", $source);
}

function _g($source) {
	return iconv("UTF-8", "CP936//IGNORE", $source);
}

function _t($text) {
	$text = preg_replace("/\\\\r[\\[\\d;]*m/", "", $text);
	$text = preg_replace("/\\\\n/", "\n", $text);
	$text = preg_replace("/\\\\\//", "/", $text);
	$text = preg_replace("/\n\n/", "\n", $text);
	$text = trim($text);
	return $text;
}

function _r($text) {
	if (mb_strlen($text, "UTF-8") > 50) {
		return mb_substr($text, 0, 50, "UTF-8");
	}
	else
		return $text;
}

function getThreads($query) {
	$_text = _u(_get("http://www.newsmth.net/bbsdoc.php", $query));
	$p = "/c\\.o\\((\\d+),(\\d+),'(.*?)','(.*?)',(\\d+),'(.*?)',(\\d+),\\d+,\\d+\\)/";
	$ps = "/docWriter\\('(.*?)',(\\d+),(\\d+),(\\d+),(\\d+),(\\d+),(\\d+)/";
	$basic = preg_match($ps, $_text, $info);
	$cnt_threads= preg_match_all($p, $_text, $subs, PREG_SET_ORDER);

	$b = new Board();
	$b->bname = $info[1];
	$b->bid = $info[2];
	$b->start = $info[3];
	$b->page = $info[6];
	$b->total = $info[7];
	$b->ftype = $query["ftype"];
	$threads = array();
	for ($i = $cnt_threads - 1; $i > -1; $i--) {
		if (stripos($subs[$i][4], "d") !== False) continue;
		$sub = new Subject();
		$sub->gid = $subs[$i][1];
		$sub->author = $subs[$i][3];
		$sub->flag = $subs[$i][4];
		$sub->time = $subs[$i][5];
		$sub->title = $subs[$i][6];
		$sub->size = $subs[$i][7];
		$sub->ftype = $b->ftype;
		$sub->bname = $b->bname;
		$sub->bid = $b->bid;
		array_push($threads, $sub);
	}
	return array("board" => $b, "threads" => $threads);
}

function getSubject($query) {
	//$query = array("board" => "WorkLife", "gid" => "5435989", "pno" => "2");
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
	
	return array("subject" => $sub, "posts" => getPosts($urls, $sub->author));
}

function getSubjectList($query, $lz = NULL) {
	//$query = array("board" => "WorkLife", "gid" => "5435989", "pno" => "2");
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
	//print_r($postlist);
	$sub->author = isset($lz)? $lz : $postlist[0][2];
	$posts = array();
	foreach ($postlist as $postline) {
		array_push($posts, array($sub->bid, $postline[1], $sub->author));
		//array_push($urls, "http://www.newsmth.net/bbscon.php?bid={$sub->bid}&id={$postline[1]}");
	}
	
	return array("subject" => $sub, "posts" => $posts);
}

function getPosts($urls, $lz = NULL) {
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
		array_push($posts, parsePost($text, $lz));
	}
	return $posts;
}

function parsePost($_text, $lz = NULL) {
	$_text = _u($_text);
	preg_match("/发信人: ((.*?)\s.*?), 信区/", $_text, $longauthor);
	$post = new Post();
	$post->longauthor = $longauthor[1];
	$post->author = $longauthor[2];
    $p = '/站内(.*?)(\\\\n--\\\\n|※)/';
    //                            bname     bid         id     gid      rid        title
    $pt = "/conWriter\\(\\d,\\s'(\\w+)',\\s(\\d+),\\s(\\d+),\\s(\\d+),\\s(\\d+),.*'(.*?)'/";
	$content = "";
    $reply = "";
	$r1 = preg_match($pt, $_text, $info);
	$r2 = preg_match($p, $_text, $full);
	//print_r($full);
	$post->bname = $info[1];
    $post->bid = $info[2];
    $post->id = $info[3];
    $post->gid = $info[4];
    $post->rid = $info[5];
    $post->title = $info[6];
    $post->lz = $lz;

	if ($r2 == 0)
		$content = "error";
	else {
        $content = _t($full[1]);
        
        $idx = strpos($content, "【");
        if ($idx !== False) {
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
    $urls = array();
    foreach ($atts as $att) {
    	$fname = $att[1];
    	$ext = substr($fname, strrpos($fname, "."));
    	if (preg_match($pf, $fname) > 0) {
    		array_push($urls, "http://att.newsmth.net/att.php?n.{$post->bid}.{$post->id}.{$att[3]}{$ext}");
    	}
    }
    $post->images = $urls;
    return $post;
}

function getImageBase64($url) {
	list($width, $height, $type) = getimagesize($url);
	switch ($type) {
		case IMAGETYPE_GIF: $source = imagecreatefromgif($url); break;
		case IMAGETYPE_JPEG: $source = imagecreatefromjpeg($url); break;
		case IMAGETYPE_PNG: $source = imagecreatefrompng($url); break;
	}
	if (!isset($source)) return "";
	
	if ($width > 300) {
		$percent = 300 / $width;
		$newwidth = $width * $percent;
		$newheight = $height * $percent;

		// Load
		$thumb = imagecreatetruecolor($newwidth, $newheight);
		// Resize
		imagecopyresized($thumb, $source, 0, 0, 0, 0, $newwidth, $newheight, $width, $height);
	}
	
	ob_start();
	if ($width > 300) imagejpeg($thumb);
	else imagejpeg($source);
	$data = ob_get_clean();
	return $data;
	
}

function getPost($query, $lz = NULL) {
	$_text = _get("http://www.newsmth.net/bbscon.php", $query, True);
	return parsePost($_text, $lz);
}

function getPostGroup($query) {
	$_text = _u(_get("http://www.newsmth.net/bbscon.php", $query));
	$cnt = preg_match("/站内(.*);o\\.h/", $_text, $all);
//	print_r($all);
	preg_match("/conWriter\\(\\d,\\s'(\\w+)',\\s(\\d+),\\s(\\d+),\\s(\\d+),\\s(\\d+),.*'(.*?)'/", $_text, $info);
//	print_r($info);
	$posts = array();
	if (!$cnt) {
		$post = new PostG();
		$post->author = "error";
		$post->content = "error";
		array_push($posts, $post);
		return $posts;
	}
	$allposts = explode("☆─────────────────────────────────────☆", $all[1]);
//	print_r($allposts);
	$p = "/.*?\\s(\\w+)\\s.*?提到:(.*)/";
	for ($i = 0; $i < count($allposts); $i++) {
		$cnt = preg_match($p, $allposts[$i], $match);
		if (!$cnt) continue;
		$post = new PostG();
		$post->author = $match[1];
		if (!isset($lz)) {
			$post->lz = $post->author;
			$lz = $post->author;
		}
		else $post->lz = $lz;
		$content = _t($match[2]);
		$reply = "";
		$idx = strpos($content, "【");
		if ($idx) {
			$reply = substr($content, $idx);
			$content = substr($content, 0, $idx);
			$reply = preg_replace("/【 在\\s(.*?)\\s.*?】/", "[\\1]", $reply);
		}
		$post->content = $content;
		$post->reply = $reply;
		array_push($posts, $post);
	}
//	print_r($posts);
	return array("title" => $info[6], "posts" => $posts);
}

function getTops($query) {
	$_text = _get("http://www.newsmth.net/rssi.php", $query);
	$cnt = preg_match_all("/<link>.*?(?<=board=)(\\w+).*?gid=(\\d+)/", $_text, $bname_and_id, PREG_SET_ORDER);
    preg_match_all("/<description>.*?发信人:\\s(.*?),.*?标\\s+题:\\s?(.*?)<br.*?站内(.*)(>--|※|\]\])/", $_text, $title_and_author, PREG_SET_ORDER);
    preg_match_all("/<title>\[(.*?)\]/", $_text, $cnames, PREG_SET_ORDER); 
    //print_r($bname_and_id);
    //print_r($title_and_author);
    $subs = array();
  	for ($i = 0; $i < $cnt; $i++) {
  		$sub = new Subject();
  		$sub->bname = $bname_and_id[$i][1];
  		$sub->cname = $cnames[$i][1];
  		$sub->gid = $bname_and_id[$i][2];
  		$sub->title = $title_and_author[$i][2];
  		$sub->author = l2s($title_and_author[$i][1]);
  		$sub->preview = p2t($title_and_author[$i][3]);
  		array_push($subs, $sub);
  	}
  	return $subs;
}
function p2t($pre) {
	$idx = strpos($pre, ">--<");
	if ($idx) $pre = substr($pre, 0, $idx);
	//$pre = preg_replace("/(<br\/>)+/", "<br>", $pre);
	return trim($pre, "<br/>");
}

function l2s($long) {
	return substr($long, 0, strpos($long, " "));
}

function getBoards($text) {
	preg_match_all("/board=(\\w+)/", $text, $boards);
	return $boards[1];
}

?>