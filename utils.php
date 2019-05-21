<?php
ini_set('display_startup_errors', 1);
ini_set('display_errors', 1);
error_reporting(E_ALL);

function hsc($str) {
    return htmlspecialchars($str);
}
function ws($n = 1) {
    return str_repeat(' ', $n);
}
function ews($n = 1) {
    echo ws($n);
}
function wc($c, $n = 1) {
    return str_repeat($c, $n);
}
function ewc($c, $n = 1) {
    echo wc($c, $n);
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
    } else {
        $str = sprintf($f, $str);
    }
    if(array_key_exists("class", $opts)) {
        $str = '<span class="'.$opts["class"].'">'.$str.'</span>';
    }
    return $str;
}
function emono($f, $str, $opts = []) {
    echo mono($f, $str, $opts);
}
function event_to_html($e, &$isH) {
    $type = $e['type'];
    $subtype = $e['subtype'];

    if($type === "goal") {
        if($subtype === "normal") {
            $t = '<span class="goal">  </span>';
        } else
        if($subtype === "own") {
            $isH = !$isH;
            $t = '<span class="own-goal">  </span>';
        }
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
    return $t;
}

function event_minute($row, $e, $i = 0) {
    
    if($e['minute'] !== null) {
        $m = $e['minute']."'";
        $row3->rel($m, $i, 'italic');
    }
}

function event2text($e, &$l, $minute_right = true) {
    $l=0;

    $e_html = event_to_html($e, $isH);

    $m = $e['minute'] !== null ? $e['minute']."'" : false;

    if($m) {
        $m = "<span class=\"italic\">$m</span>";
        $l = 6;
    } else {
        $l = 2;
    }
    
    if($minute_right) {
        return $e_html . $m;
    }
    else {
        $l *= -1;
        return $m . $e_html;
    }
}

function short_name($name) {
    preg_match_all("/([A-Z])/", $name, $matches);
    if(isset($matches[0])) {
        return implode('', $matches[0]);
    }
    else {
        return substr($name, 0, 3) . '.';
    }
}

function event_print($row, $e, &$isH, $i = 0, $minute_right = true) {
    $e_html = event_to_html($e, $isH);

    $m = $e['minute'] !== null ? $e['minute']."'" : false;

    if($minute_right) {
        $t1 = $e_html;
        $t2 = $m;
    } else {
        $t1 = $m;
        $t2 = $e_html;
    }

    $row->rel($t1, $i);
    $i = $i < 0 ? "back" : 0;
    $row->rel($t2, 0);
}

function rows_gen($n) {
    $rows = [];
    for($i=$n; $i > 0; --$i) {
        $rows[] = new Row();
    }
    return $rows;
}

function rows_print($board, $rows) {
    foreach ($rows as $row) {
        $board->printRow($row);
    }
}

function rows_setpos(&$rows, $p) {
    foreach ($rows as &$row) {
        $row->setPos($p);
    }
}
/*

function event_to_html($e, &$isH) {
    $type = $e['type'];
    $subtype = $e['subtype'];

    if($type === "goal") {
        if($subtype === "normal") {
            $t = '<span class="goal"> </span>';
        } else
        if($subtype === "own") {
            $isH = !$isH;
            $t = '<span class="own-goal"> </span>';
        }
        $t = ($isH?' ':'').$t.($isH?'':' ');
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
    $m = '';
    if($e['minute'])
        $m = sprintf("<span class=\"minute\">%3s</span>", $e['minute'].'\'');
    if($isH) {
        return $t . $m;
    } else {
        return $m . $t;
    }
}/**/

function righello($n=8) {
    $t = "\n|";
    for($i=0; $i < $n; $i++) {
        $t .= $i . '123456789';
    }
    return $t."\n";
}
function pidx($n, $nn='') { return "\n|".str_repeat(" ", $n > 0? $n-1:0) . '^'.$nn."=$n";}