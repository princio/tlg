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
    if($isH) {
        return $t . sprintf("<span class=\"minute\">%3s</span>", $e['minute'].'\'');
    } else {
        return sprintf("<span class=\"minute\">%3s</span>", $e['minute'].'\'') . $t;
    }
}