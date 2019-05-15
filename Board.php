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
            $l = $pos - $this->pos;
            if($l > 0 && mb_strlen($this->text) < $l) {
                $this->text .= str_repeat(' ', abs($pos - $this->pos));
            }
            $this->pos = $pos;
            //dump($this->pos);
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

        $this->pos = $lp;
        
        $str = $this->text . str_repeat(' ', $ws) . $str;

        $ms = [];


        // dump("_____________________________________________________");
        // mb_ereg_search_init($str, '<(.*?)>(.*?)</(\w+)>');
        // mb_ereg_search();
        // $r = mb_ereg_search_getpos();
        // for($i=0; $i<3 && $r !== false; $i++) { dump($r); $r = mb_ereg_search_pos(); }
        // mb_ereg_search_init($str, '<(.*?)>(.*?)</(\w+)>');
        // mb_ereg_search();
        // $r = mb_ereg_search_getregs();
        // for($i=0; $i<3 && $r !== false; $i++) { dump($r); $r = mb_ereg_search_regs(); }
        // dump("_____________________________________________________");


        $r = preg_match_all('~<(.*?)>(.*?)</(\w+)>~', $str, $matches, PREG_OFFSET_CAPTURE);
        $hl = 0;
        $p_html_pre = 0;
        $mb = 0;
        for($i = 0; $i < $r; $i++) {
            $t = $matches[2][$i][0];

            $html = $matches[0][$i][0];

            $p = $matches[0][$i][1];
            $ss = substr($str, 0, $p);
            $mb = 1+ $p -(strlen($ss) - mb_strlen($ss));


            $this->slices[] = new Slice($mb - $hl, $html);


            dump(righello()
            ."\n$str\n"
            .strip_tags($str)."\n"
            .pidx($mb-$hl, "mb-hl")."\n"
            .pidx($p, "p_html")."\n"
            .pidx($mb, "mb")."\n"
            ."|$t|\n"
            ."|$ss|\n"
            .mb_strlen($ss)."\n"
            ."$html\n"
            .mb_strlen($html)."\n"
            .mb_strlen($t));


            $hl += mb_strlen($html) - mb_strlen($t);
        }

        $this->text = strip_tags($str);
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
        $pb = 0;

        $sls = $this->slices;
        $csls = count($sls);

        if($csls > 0) {
            for($i = 0; $i < $csls; ++$i) {
                $s = $sls[$i];
                $sp = $s->p-1;
                $st = mb_substr($t, $pb, $sp - $pb);
                $t2 .= $st . $s;
                
                $pb = $s->getLP() -1;
            }
            $t2 .= mb_substr($t, $pb);
        }
        else {
            $t2 = $t;
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
    public $lp;

    function __construct($p, $html) {
        $this->p = $p;
        $this->text = $html;
        $this->l = $this->getL();
        $this->lp = $this->getLP();
    }

    function getL() { return mb_strlen(strip_tags($this->text)); }

    function getLP() { return $this->p + $this->getL(); }
    
    function __toString() {
        return $this->text;
    }
}