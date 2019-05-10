<?php

require './dumper/autoload.php';

ini_set('display_startup_errors', 1);
ini_set('display_errors', 1);
error_reporting(E_ALL);

function ws($n = 1) {
    return str_repeat(' ', $n);
}
function ews($n = 1) {
    echo ws($n);
}
function ww($n = 1) {
    return str_repeat("\n", $n);
}
function eww($n = 1) {
    echo ww($n);
}
function monoopen($class = "normal") {
    echo "<span class=\"{$class}\">";
}
function monoclose() {
    echo "</span>";
}
function mono($f, $str, $opts = []) {
    if(strlen($str) !== strlen(strip_tags($str))) {
        $chr = chr(255);
        $ns = [];
        $str = preg_replace_callback('~<\w+.*?>.*</\w+>~', function($matches) use ($str, &$ns, $chr) {
            $n = strlen(strip_tags($matches[0]));
            $pos = strpos($str, $matches[0]);
            $ns[] = [$n, $pos, $matches[0]];
            return str_repeat($chr, $n);
        }, $str);
        $str = sprintf($f, $str);
        $begin = 0;
        $t = '';
        foreach ($ns as $n) {
            $xx = str_repeat($chr, $n[0]);
            $str = preg_replace("~{$xx}~", $n[2], $str, 1);
        }
        return $str;
    }

    return sprintf($f, $str);
}
function emono($f, $str, $opts = []) {
    echo mono($f, $str, $opts);
}
function event_to_html($e, $isH) {
    $type = $e['type'];
    $subtype = $e['subtype'];

    if($type === "goal") {
        $t = '<span class="goal">  </span>';
    }
    if($type === "own-goal") {
        $t = 'G-';
    }
    if($type === "booking") {
        if($subtype === "yc") {
            $t = '<span class="yc">  </span>';
        }
        if($subtype === "y2c") {
            $t = '<span style="border-radius: 5px; background-color: #f1ed09;"> </span><span style="background-color: red;"> </span>';
        }
        if($subtype === "rc") {
            $t = '<span style="background-color: red;">  </span>';
        }
    }
    if($isH) {
        return $t . sprintf("%3s", $e['minute'].'\'');
    } else {
        return sprintf("%3s", $e['minute'] .'\'') . $t;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
    <style>
    .test
    {
        position: absolute;
        visibility: hidden;
        height: auto;
        width: auto;
        white-space: nowrap; /* Thanks to Herb Caudill comment */
    }
    .board {
        font-family: "monospace";
        white-space: pre;
    }
    .big {
        height: 38px;
        transform-origin: left bottom;
        -webkit-transform-origin: 0 0;
        -moz-transform-origin: 0 0;
        display: inline-block;
        transform: scale(2);
        -webkit-transform: scale(2);
        -moz-transform: scale(2);
    }
    .normal {
        font-size: 16px;
    }
    .own-goal {
        box-shadow: 0 0 0 4px black inset;
        border-radius: 50%;
        background-color: red;
    }
    .goal {
        box-shadow: 0 0 0 4px black inset;
        border-radius: 50%;
        background-color: #00ff00;
    }
    .rc {
        border-radius: 50%;
        background-color: red;
        box-shadow: 0 0 0 4px black inset;
    }
    .yc {
        border-radius: 10%;
        background-color: #f1ed09;
        box-shadow: 0 0 0 2px #f0ce08 inset;
    }
    </style>
</head>
<body>
<?php


$servername = "localhost";
$username = "princio";
$password = "pomo";


try {
    $new = new PDO("mysql:host=$servername;dbname=newdb", $username, $password);
    // set the PDO error mode to exception
    $new->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
}
catch(PDOException $e)
{
    echo "Connection failed: " . $e->getMessage();
}

$fid = 349;

$sql = "SELECT * FROM fixture_view WHERE id = ?";
$fix_sel = $new->prepare($sql);
$fix_sel->execute([$fid]);
$fix = $fix_sel->fetch(PDO::FETCH_ASSOC);


$sql = "SELECT * FROM fixture_players WHERE fixture_id = ?";
$app_sel = $new->prepare($sql);
$app_sel->execute([ $fid ]);
$_apps = $app_sel->fetchAll(PDO::FETCH_ASSOC);

$surname_max = 0;
$name_max = 0;

$ha = [];
$aa = [];
foreach ($_apps as $a) {
    $id = array_shift($a);
    $apps[$id] = $a;
    if($a['team_id'] === $fix['home_team_id']) {
        $ha[$id] = $a;
    } else {
        $aa[$id] = $a;
    }
    $surname_max = max($surname_max, strlen($a['surname']));
    $name_max = max($name_max, strlen($a['name']));
}

$sql = "SELECT `event`.* FROM `event` JOIN `app` ON `app_id` = app.id WHERE app.fixture_id = ? ORDER BY `event`.`minute`, `event`.`id`";
$evt_sel = $new->prepare($sql);
$evt_sel->execute([ $fid ]);
$evts = $evt_sel->fetchAll(PDO::FETCH_ASSOC);

echo '<div class="board">';


monoopen("big");
emono("%20s", $fix['home']);
ews(3);
emono("%-20s", $fix['away']);
monoclose();
eww();

foreach ($evts as $e) {
    monoopen();
    $a = $apps[$e['app_id']];
    $isH = $a['team_id'] === $fix['home_team_id'];
    if($isH) {
        emono("%40s", $a['name'] . ' ' . $a['surname'] . '  ' .  event_to_html($e, $isH));
    } else {
        ews(40 + 6);
        $t1 = event_to_html($e, $isH);
        
        emono("%-40s", $t1 . '  ' . $a['name'] . ' ' . $a['surname']);
    }
    eww();
    monoclose();
}

eww();
eww();
eww();

$ha = array_values($ha);
$aa = array_values($aa);

for ($i = 0; $i < max(count($ha), count($aa)); $i++) {
    monoopen();
    if($ha[$i]) {
        emono("%-{$name_max}s", $ha[$i]['name']);
        ews();
        emono("%-{$surname_max}s", $ha[$i]['surname']);
        ews(43 - $name_max - $surname_max);
    }
    if($aa[$i]) {
        ews(43 - $name_max - $surname_max);
        emono("%{$surname_max}s", $aa[$i]['surname']);
        ews();
        emono("%{$name_max}s", $aa[$i]['name']);
    }
    eww();
    monoclose();
}

echo '</div>';
?>

<span id="Test" class="test normal">a</span>
<span id="Test2" class="test big">a</span>


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



</body>
</html>