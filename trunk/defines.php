<?php
$webkit = preg_match("/webkit/i", $_SERVER["HTTP_USER_AGENT"]);
	
class Subject {
	var $bname;
	var $cname;
	var $gid;
	var $title;
	var $author;
	var $flag;
	var $time;
	var $size;
	var $pno;
	var $tpage;
	var $nthid;
	var $pthid;
	var $lz;
	var $ftype = "6";
	var $preview;
	function make_class($rtype = "0") {
		$classes = array();
		if (strpos($this->flag, "@") !== False) array_push($classes, "att");
		if (strpos($this->flag, "*") !== False) array_push($classes, "unread");
		if (stripos($this->flag, "g") !== False) array_push($classes, "digest");
		if (stripos($this->flag, "m") !== False) array_push($classes, "mark");
		if ($rtype == "0" && !strstr($this->title, "Re:")) array_push($classes, "mainsub");
        if (count($classes) == 0)
            return "";
        else
            return " class='" . implode(" ", $classes) . "'";
	}
	function make_flags() {
		$flags = array();
		if (strpos($this->flag, "@")) array_push($flags, "<img src='icon/flag_att.png'>");
		if (stripos($this->flag, "g")) array_push($flags, "<img src='icon/flag_digest.png'>");
		if (stripos($this->flag, "m")) array_push($flags, "<img src='icon/flag_mark.png'>");
		return implode($flags);
	}
	
	function build_query($page = NULL) {
		global $webkit;
		$link = "";
		switch ($this->ftype) {
			case "6":
			case "1":
			case "3":
				$link = "subject.php?" . http_build_query(array("board" => $this->bname, "gid" => $this->gid, "pno" => $page ,"lz" => $this->author));
				break;
			case "0": 
				$link = "post.php?" . http_build_query(array("bid" => $this->bid, "id" => $this->gid));
				break;
		}
		return $webkit? str_replace("subject.php", "isubject.php", $link) : $link;
	}
	function build_query_sub($gid) {
		global $webkit;
		$link = "";
		switch ($this->ftype) {
			case "6":
			case "1":
			case "3":
				$link = "subject.php?" . http_build_query(array("board" => $this->bname, "gid" => $gid));
				break;
			case "0": 
				$link = "post.php?" . http_build_query(array("bid" => $this->bid, "id" => $gid));
				break;
		}
		return $webkit? str_replace("subject.php", "isubject.php", $link) : $link;
	}
	
	function build_query_board() {
		return "board.php?" . http_build_query(array("board" => $this->bname, "ftype" => $this->ftype));
	}
	
	function build_query_postgroup() {
		return "postgroup.php?" . http_build_query(array("bid" => $this->bid, "id" => $this->gid));
	}
	
	function make_list() {
		global $webkit;
		switch ($this->ftype) {
			case "6":
			case "1":
			case "3":
				$target = $webkit? "isubject.php" : "subject.php";
				break;
			case "0":
				$target = "post.php";
				break;
		}
		$c = $this->make_class($this->ftype);
		$flags = $this->make_flags();
		//print_r(stripos($this->title, "合集"));
		if (stripos($this->title, "合集") !== False)
			$li = "<li$c><a href='" . $this->build_query_postgroup() . "'>{$flags}&nbsp;{$this->title}&nbsp;<span class='author'>{$this->author}</span></a></li>\n";
		else
			$li = "<li$c><a href='" . $this->build_query($this->pno) . "'>{$flags}&nbsp;{$this->title}&nbsp;<span class='author'>{$this->author}</span></a></li>\n";
		return $li;
	}
	
	function make_list_top() {
		return "<li onclick=\"javascript:toggleTop(this)\" boardlink='board.php?board={$this->bname}'>[{$this->cname}] {$this->title}&nbsp<span class='author'>{$this->author}</span><div class='preview'>{$this->preview}<a class='btnForward btn' href='" . $this->build_query() . "'></a></div></li>";
	}
	
	
	function make_nav() {
		global $webkit;
		if ($webkit) {
			$back = "<a class='btnBack' href='javascript:history.go(-1)'></a>\n";
			$prevpage = ($this->pno == "1")? "" : "<a class='btnUp' href='" . $this->build_query(intval($this->pno) - 1) . "'></a>\n";
			$nextpage = ($this->pno == $this->tpage)? "" : "<a class='btnDown'  href='" . $this->build_query(intval($this->pno) + 1) . "'></a>\n";
			$prevth = ($this->gid == $this->pthid)? "" : "<a class='btnLeft'  href='" . $this->build_query_sub($this->pthid) . "'></a>\n";
			$nextth = ($this->gid == $this->nthid)? "" : "<a class='btnRight'  href='" . $this->build_query_sub($this->nthid) . "'></a>\n";
			$boardlink = "<a class='btnBoard'  href='" . $this->build_query_board() . "'></a>";
			$expand = "<a class='btnExpand' href='javascript:$(\"li\").click()'></a>";
		} else {
			$back = "<a href='javascript:history.go(-1)'><img src='icon/back.png'></a>\n";
			$prevpage = ($this->pno == "1")? "" : "<a href='" . $this->build_query(intval($this->pno) - 1) . "'><img src='icon/up.png'></a>\n";
			$nextpage = ($this->pno == $this->tpage)? "" : "<a href='" . $this->build_query(intval($this->pno) + 1) . "'><img src='icon/down.png'></a>\n";
			$prevth = ($this->gid == $this->pthid)? "" : "<a href='" . $this->build_query_sub($this->pthid) . "'><img src='icon/left.png'></a>\n";
			$nextth = ($this->gid == $this->nthid)? "" : "<a href='" . $this->build_query_sub($this->nthid) . "'><img src='icon/right.png'></a>\n";
			$boardlink = "<a href='" . $this->build_query_board() . "'><img src='icon/board.png'></a>";
			$expand = "";
		}
		$navlink = "<div class='nav'>{$back} {$boardlink} {$prevth}{$nextth} {$prevpage}{$nextpage} {$expand}</div>\n";
		return $navlink;
	}
}

class Board {
	var $bname;
	var $bid;
	var $ftype;
	var $page;
    var $total;
    var $start;
    function build_query($p = "") {
    	return "board.php?" . http_build_query(array("board" => $this->bname, "ftype" => $this->ftype, "page" => $p));
    }
    function build_query_ftype($type) {
  		return "board.php?" . http_build_query(array("board" => $this->bname, "ftype" => $type));
    }
    function make_nav() {
    	$ft = "";
    	$ft2 = "";
		switch ($this->ftype) {
			case "6": $ft = "S"; $ft2 = "N"; break;
			case "0": $ft = "N"; $ft2 = "S"; break;
		}
		global $webkit;
		if ($webkit) {
			$back = "<a class='btnBack'  href='index.php'></a>";//"<a href='index.php'><img src='icon/back.png'></a>";
			$prev = "<a class='btnUp'  href='" . $this->build_query($this->page - 1) . "'></a>";//"<a href='" .  . "'><img src='icon/up.png'></a>";
			if (intval($this->start) <= intval($this->total) - 30) 
				$next = "<a class='btnDown'  href='" . $this->build_query($this->page + 1) . "'></a>";//"<a href='" . $this->build_query($this->page + 1) . "'><img src='icon/down.png'></a>";
			else $next = "";
			$types = array();
			array_push($types, ($this->ftype == "6")? "<a class='btnSubject'></a>" : "<a class='btnSubject disabled' href='" . $this->build_query_ftype(6) . "'></a>");
			array_push($types, ($this->ftype == "0")? "<a class='btnSingle'></a>" : "<a class='btnSingle disabled' href='" . $this->build_query_ftype(0) . "'></a>");
			array_push($types, ($this->ftype == "1")? "<a class='btnDigest'></a>" : "<a class='btnDigest disabled' href='" . $this->build_query_ftype(1) . "'></a>");
			array_push($types, ($this->ftype == "3")? "<a class='btnMark'></a>" : "<a class='btnMark disabled' href='" . $this->build_query_ftype(3) . "'></a>");
			$types = implode("\n", $types);
			$newth = "<a class='btnNew'  href='reply.php?act=new&board={$this->bname}'></a>\n";
		} else {
			$back = "<a href='index.php'><img src='icon/back.png'></a>";
			$prev = "<a href='" . $this->build_query($this->page - 1) . "'><img src='icon/up.png'></a>";
			if (intval($this->start) <= intval($this->total) - 30) 
				$next = "<a href='" . $this->build_query($this->page + 1) . "'><img src='icon/down.png'></a>";
			else $next = "";
			$types = array();
			array_push($types, ($this->ftype == "6")? "<img src='icon/th_subject.png'>" : "<a href='" . $this->build_query_ftype(6) . "'><img src='icon/th_subject.png' class='disabled'></a>");
			array_push($types, ($this->ftype == "0")? "<img src='icon/th_single.png'>" : "<a href='" . $this->build_query_ftype(0) . "'><img src='icon/th_single.png' class='disabled'></a>");
			array_push($types, ($this->ftype == "1")? "<img src='icon/th_digest.png'>" : "<a href='" . $this->build_query_ftype(1) . "'><img src='icon/th_digest.png' class='disabled'></a>");
			array_push($types, ($this->ftype == "3")? "<img src='icon/th_mark.png'>" : "<a href='" . $this->build_query_ftype(3) . "'><img src='icon/th_mark.png' class='disabled'></a>");
			$types = implode("\n", $types);
			$newth = "<a href='reply.php?act=new&board={$this->bname}'><img src='icon/new_post.png'></a>\n";
		}
		$navlink = "<div class='nav'>{$back} {$types} {$newth} {$prev}{$next}</div>";
    	return $navlink;
    }
}

class Post {
	var $bname;
	var $bid;
	var $id;
	var $gid;
	var $rid;
	var $lz;
	var $author;
	var $longauthor;
	var $title;
	var $content;
	var $reply;
	var $images;
	
	function make_list($type = 1) {
		global $webkit;
		$auth = ($this->author == $this->lz)? "<div class='authorlz'>{$this->author}</div>" : "<div class='author'>{$this->author}</div>";
		$content = "<div class='content'>{$this->content}</div>";
		$refer = $webkit? "<div class='hidecomments'>{$this->reply}</div>" : "<div class='comments'>" . _r($this->reply) . "</div>";
		$imgs = "";
		if (!empty($this->images)) {
			foreach ($this->images as $img) {
				//$imgs .= "<a href='{$img}' target='_blank'><img src='icon/image.png'></a>\n";
				$imgs .= "<a href='{$img}' target='_blank'><img src=\"data:image/jpeg;base64," . base64_encode(getImageBase64($img)) . "\"></a>\n";
			}
		}
		if (!empty($imgs)) $imgs = "<div>{$imgs}</div>";
		if ($type != 1) 
			return $webkit? "{$auth}{$content}{$imgs}{$refer}</li>\n" : "<li>{$auth}{$content}{$imgs}{$refer}";
		else 
			return $webkit? "<li onclick=\"javascript:$('.hidecomments', this).toggle()\">{$auth}{$content}{$imgs}{$refer}</li>\n" : "<li>{$auth}{$content}{$imgs}{$refer}</li>\n";
	}
	function build_query($page) {
		return "post.php?" . http_build_query(array("bid" => $this->bid, "id" => $this->id, "p" => $page));
	}
	
	function build_query_id($tid) {
		return "post.php?" . http_build_query(array("bid" => $this->bid, "id" => $tid));
	}
	
	function build_query_board() {
		return "board.php?" . http_build_query(array("board" => $this->bname, "ftype" => "0"));
	}
	function build_query_subject() {
		global $webkit;
		$target = $webkit? "isubject.php?" : "subject.php?";
		return $target . http_build_query(array("board" => $this->bname, "gid" => $this->gid));
	}
	
	function make_nav_top() {
		global $webkit;
		if ($webkit) {
			$back = "<a class='btnBack'  href='javascript:history.go(-1)'></a>\n";
			$first = "<a class='btnFirst'  href='" . $this->build_query_id($this->gid) . "'></a>\n";
			$referpage = "<a class='btnBackRefer'  href='" . $this->build_query_id($this->rid) . "'></a>\n";
			$boardlink = "<a class='btnBoard'  href='" . $this->build_query_board() . "'></a>\n";
			$expand = "<a class='btnExpand'  href='" . $this->build_query_subject($this->gid) . "'></a>\n";
		} else {
			$back = "<a href='javascript:history.go(-1)'><img src='icon/back.png'></a>\n";
			$first = "<a href='" . $this->build_query_id($this->gid) . "'><img src='icon/first.png'></a>\n";
			$referpage = "<a href='" . $this->build_query_id($this->rid) . "'><img src='icon/backrefer.png'></a>\n";
			$boardlink = "<a href='" . $this->build_query_board() . "'><img src='icon/board.png'></a>\n";
			$expand = "<a href='" . $this->build_query_subject($this->gid) . "'><img src='icon/expandall.png'></a>\n";
		}
		$navlink = "<div class='nav'>{$back} {$first}{$referpage} {$expand}{$boardlink}</div>\n";
		return $navlink;
	}
	function make_nav_bottom() {
		global $webkit;
		if ($webkit) {
			$prevpage = "<a class='btnUp'  href='" . $this->build_query("p") . "'></a>\n";
			$nextpage = "<a class='btnDown'  href='" . $this->build_query("n") . "'></a>\n";
			$prevth = "<a class='btnLeft'  href='" . $this->build_query("tp") . "'></a>\n";
			$nextth = "<a class='btnRight'  href='" . $this->build_query("tn") . "'></a>\n";
		} else {
			$prevpage = "<a href='" . $this->build_query("p") . "'><img src='icon/up.png'></a>\n";
			$nextpage = "<a href='" . $this->build_query("n") . "'><img src='icon/down.png'></a>\n";
			$prevth = "<a href='" . $this->build_query("tp") . "'><img src='icon/left.png'></a>\n";
			$nextth = "<a href='" . $this->build_query("tn") . "'><img src='icon/right.png'></a>\n";
		}
		$navlink = "<div class='nav'>{$prevpage}{$nextpage} {$prevth}{$nextth} </div>\n";
		return $navlink;
	}
}

class PostG {
	var $lz;
	var $author;
	var $longauthor;
	var $content;
	var $reply;
	
	function make_list() {
		global $webkit;
		$auth = ($this->author == $this->lz)? "<div class='authorlz'>{$this->author}</div>" : "<div class='author'>{$this->author}</div>";
		$content = "<div class='content'>{$this->content}</div>";
		$refer = $webkit? "<div class='hidecomments'>{$this->reply}</div>" : "<div class='comments'>" . _r($this->reply) . "</div>";
		return $webkit? "<li onclick=\"javascript:$('.hidecomments', this).toggle()\">{$auth}{$content}{$imgs}{$refer}</li>\n" : "<li>{$auth}{$content}{$imgs}{$refer}</li>\n";
	}
}

?>