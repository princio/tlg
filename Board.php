<?php

class Board {

    const WIDTH = 88;
    const SW = 10;
    private $curP = 0;
    private $cr = null;

    function __constructor() {}

    function print($str, $p = 0, $style = '') {
        echo $str;
    }

    function printRow($row) {
        if(!$row->isClosed())
            $row->close();
        
        echo $row;
    }

    function current() {
        return $this->rowLength($this->curP);
    }

    static function GetWidth($type, $l = null) {
        $l = $l === null ? self::WIDTH : $l;
        switch($l) {
            case "half":
                $l = $l >> 1;
                break;
            case "double":
                $l = $l << 1;
                break;
        }
        return $l;
    }

    function ww($n = 1) {
        for($i = $n-1; $i >= 0; --$i) {
            echo "\n";
        }
    }

    function newrow($rowtype = "normal") {
        $this->rowtype = $rowtype;
        echo "<div class=\"{$rowtype}\">";
    }

    
    static function notHtmlLen($str) {
        $nhl = iconv_strlen(strip_tags($str)); 
        return iconv_strlen($str) - $nhl > 0 ? $nhl : iconv_strlen($str);


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
        }
        return $str;
    }
}

class Row {

    private $pos;
    private $type;
    private $width = 0;
    private $closed = false;
    private $html = '';

    function __construct($type = "normal") {
        $this->type = $type;
        $this->width = Board::GetWidth($type);
        $this->pos = 0;
        $this->closed = false;
        if($type !== "normal")
            $this->html = "<span class=\"{$type}\">";
    }

    function isClosed() {
        return $this->closed;
    }

    static function Fast($str, $p = 0, $type = "normal") {
        $row = new Row($type);
        $row->print($str, $p);
        return $row;
    }

    function print($str, $p = 0, $style = '') {
        $l = Board::notHtmlLen($str);

        if($p === 0) {
            $p = $this->pos;
            $fp = $p;
            $lp = $fp + $l;
        } else
        if($p > 0) {
            $fp = $p;
            $lp = $fp + $l;
        } else
        if($p < 0) {
            $p = $p * -1;
            $fp = $p - $l;
            $lp = $p;
        }
        $ws = abs($fp - $this->pos);
        
        if($p < $this->pos) {
            throw new Exception("Position lesser than current position: {$p} < {$this->pos}.");
        }

        if($fp > $this->width) {
            throw new Exception("Position greater than line width: {$first_pos} > {$this->width}.");
        }
        if($lp > $this->width) {
            throw new Exception("Position + string length greater than line width: {$last_pos} > {$this->width}.");
        }

        if($style)
            $str = "<span class=\"{$style}\">{$str}</span>";

        $this->html .= str_repeat(' ', $ws) . $str;

        $this->pos = $lp;
    }

    function close() {
        
        if(false === $this->isClosed()) {
            if($this->type !== "normal") {
                $this->html .= '</span>';
            }
            $this->closed = true;
        }
        return $this;
    }

    function __toString() {
        return $this->html."\n";
    }

}