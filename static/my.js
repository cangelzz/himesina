/*$(document).ready(function () {
	$("li:even").addClass("even");
	$(".att a").prepend("<img src='icon/attachment.png'/>");
})	;
*/	

function toggleTop(el) {
	//$(".preview").hide();
	//$('.preview', $(el)).toggle();
	var flag = $(".selected").length == 0;
	$("li").removeClass("selected");
	if (flag) $(el).addClass("selected");
}

function goBoard() {
	if ($(".selected").length != 0)
		location.href = $(".selected").attr("boardlink");
}

function writePosts() {
	for (i in posts) {
		$("#posts_ul").append("<li id='p" + i +  "' onclick=\"javascript:$('.hidecomments', this).toggle()\">loading ...</li>");
		$("#p" + i).load("ipost.php", {"bid": posts[i][0], "id": posts[i][1], "lz": posts[i][2]});
	}
}


function sortul(ulid)
{
    var ul = document.getElementById(ulid);
    if (ul == null) return;
    var lis = ul.getElementsByTagName("li");
    var len = lis.length;
    if (len > 0) {
        for (var i=len-1;i>=0;i--)
            ul.appendChild(lis[i]);
    }
    
    var as = ul.parentElement.getElementsByClassName("btnCenter0 fleft");
    for (var a=0;a<as.length;a++)
    {
        var cname = as[a].className;
        if (cname.match(/btnSortAZ/)) {
            as[a].className = cname.replace("btnSortAZ", "btnSortZA"); 
            as[a].innerText = "∵";
            continue;
        }
        if (cname.match(/btnSortZA/)) {
            as[a].className = cname.replace("btnSortZA", "btnSortAZ");
            as[a].innerText = "∴";
            continue;
        }
    }
}

function toggleComment() {
    if ($(".expandall")[0].innerText == "≡") {
        $(".hidecomments").show();
        $(".expandall").text("－");
        $("a.excomments").text("-");
    } else {
        $(".hidecomments").hide();
        $(".expandall").text("≡");
        $("a.excomments").text("+");
    }
}

function toggleCommentSingle(id) {
    var ap = $("a#"+id);
    if (ap.text() == "+")
        ap.text("-");
    else ap.text("+");
    ap.next().toggle();
}
