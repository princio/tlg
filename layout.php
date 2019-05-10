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

<div class="board navbar"><div class="nav-buttons logo">  t  o  r  n  e  o    d  e  l  l  e    g  r  a  z  i  e </div>
<div class="nav-buttons">  TDC  |  Home  | Calendario  |  Gironi  |  Albo d'oro </div>
</div>

<div class="full-board"><?php echo $body;?></div>

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


<div class="board">
<span class="double">a</span>
<span class="normal">a</span>
</div>

</body>
</html>