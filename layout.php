<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
	<link
	href="/style.css"
	rel="stylesheet">
    <title>Document</title>
    <script src="/jquery.js" type="text/javascript"></script>
    <style>
    </style>
</head>
<body>
<?php $board = new Board(); ?>
<div class="cover">
    <div class="cover-content"><span class="cover-title"><?php
    $title = "T  o  r  n  e  o    d  e  l  l  e    G  r  a  z  i  e";
    //dump(strlen($title));
    //dump(88 - strlen($title));
    $board->ww();
    $board->printRow(Row::Fast($title, (88 - strlen($title)) >> 1));
    ?></span><span class="cover-text"><?php
    $links_name = explode(',', "Home,Calendario,Gironi,Albo d'oro");
    $links = explode(',', "/,/calendar/11,/groups/11,/albo/11" );
    $board->ww();
    $link = '';
    for ($i = 0; $i < count($links); $i++) {
        $link .= sprintf("<a href=\"%s\">%s</a> | ", $links[$i], $links_name[$i]);
    }
    $link = substr($link, 0, -3);
    $board->printRow(Row::Fast($link, (88 - Board::notHtmlLen($link)) >> 1));
    ?></span>
    </div>
</div>
<div class="board">
    <?php echo $body; ?>
</div>

</div>

<div id="dumps" style="z-index: -1; position: absolute; left:0px; top: 100px;">
</div>

<script>
/*
var $dumps = $("pre.dump");

let n = -1;
let _top = -1;
let $div = $();
$dumps.each((i,e) => {
    let $e = $(e);
    let top_new = $(e).offset().top;
    //$div = $(`#dump-${n}`);
    if(top_new !== _top) {
        if(n >= 0) {
            $div.appendTo("body");
        }
        n = n + 1;
        $div = $(`<div id="dump-${n}" style="z-index: 1; position: absolute; left:0px; top: ${top_new}px;"></div>`);
    }
    
    $e.attr('id', "dump-x"+i);
    $e.css('left', "40px");
    console.log($e);
    $e.remove();

    $e.hide();

    $div.append(`<span onclick="showdump(${i})" style="z-index: 0;">dump ${i}</span><br/>`).append($e);

    _top = top_new
});
$div.appendTo("body");

function showdump(i) {
    $(`#dump-x${i}`).toggle();
}
*/
</script>

</body>
</html>