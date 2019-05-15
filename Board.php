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
    private $text = '';
    private $slices = [];
    private $encloser;

    function __construct($type = "normal") {
        $this->type = $type;
        $this->width = Board::GetWidth($type);
        $this->pos = 0;
        $this->closed = false;
    }

    function isClosed() {
        return $this->closed;
    }

    static function Fast($str, $p = 0, $type = "normal") {
        $row = new Row($type);
        $row->print($str, $p);
        return $row;
    }

    function getPos() { return $this->pos; }

    function setPos($pos) {
        if(gettype($pos) === "integer" && $pos >= 0 && $pos <= 88) {
            $this->pos = $pos;
        }
        else {
            throw new Exception("Row: wrong pos.");
        }
    }

    function incr($str, $i, $style) {
        $this->print($str, $this->pos + $i, $style = '');
    }

    function print($str, $p = 0, $style = '') {
        $l = mb_strlen(strip_tags($str));

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

        $slices = [];
        $t = preg_replace_callback('~<(.*?)>(.*?)</(\w+)>~', function($matches) use ($fp, $str, &$slices) {
            $nh_str = strip_tags($str);
            $p = mb_strpos($nh_str, $matches[2], 0, "UTF-8");

            array_shift($matches);

            $s = new Slice($p, $matches);

            $slices[] = $s;

            return $s->text;            
        }, $str);

        $this->slices[$fp] = $slices;


        if($fp > $this->width) {
            throw new Exception("Position greater than line width: {$fp} > {$this->width}.");
        }
        if($lp > $this->width) {
            throw new Exception("Position + string length greater than line width: {$lp} > {$this->width}.");
        }

        if($style) {
            $this->styles[] = [ $this->pos, iconv_strlen($str), "span", $style ];
        }

        $this->text .= str_repeat(' ', $ws) . $t;
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

        $t = $this->text;
        $t2 = '';
        $p0 = 0;

        foreach($this->slices as $fp => $sls) {
            foreach ($sls as $s) {
                $p1 = $fp + $s->p;
                
                $t2 .= substr($t, $p0, $p1 - $p0) . $s;

                $p0 = $p1 + $s->getL();
            }
        }

        if($this->type) $t2 = "<span class=\"{$this->type}\">{$t2}</span>";

        return $t2."\n";
    }

}


class Slice {

    public $p = 0;
    public $text = '';
    public $atts = [];
    public $tag;
    public $l;

    function __construct($p, $three) {
        $this->p = $p;

        $tag_whole = $three[0];
        
        $this->text = $three[1];
        $tag_end = $three[2];
        preg_match('~^\s*(\w+)\s*~', $tag_whole, $mtag);
        preg_match_all('~(\w+)="([^"]+)"~', $tag_whole, $matts);
        $this->tag = $mtag[1];
        array_shift($matts);
        
        $this->atts = $matts;
    }

    function getL() { return mb_strlen($this->text); }
    
    function __toString() {
        $h = "<{$this->tag}";

        for($i=0; $i<count($this->atts[0]); $i++) {
            $h .= ' '.$this->atts[0][$i]."=\"". $this->atts[1][$i].'"';
        }

        $h .= ">".$this->text."</{$this->tag}>";

        return $h;
    }
}