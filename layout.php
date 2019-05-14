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

<span id="Test" class="test normal">a</span>
<span id="Test2" class="test double">a</span>


<script>
var test = document.getElementById("Test");
var height = (test.clientHeight + 1) + "px";
var width = (test.clientWidth + 1) + "px"
console.log(test.style.fontSize + ": " + width + "," + height);

test = document.getElementById("Test2");
height = (test.clientHeight + 1) + "px";
width = (test.clientWidth + 1) + "px"
console.log(test.style.fontSize, width, height);
</script>


</div>


</body>
</html>