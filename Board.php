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

    static function GetWidth($type, $l = -1) {
        $l = $l === -1 ? self::WIDTH : $l;
        switch($type) {
            case "half":
                $l = $l << 1;
                break;
            case "normal":
                $l = self::WIDTH;
                break;
            case "double":
                $l = $l >> 1;
                break;
        }
        return $l;
    }

    static function GetLastPosition($type) {
        return GetWidth($type) - 1;
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

    private $fps = [];
    private $ls = [];
    private $pos = 0;
    private $type;
    private $width = 0;
    private $closed = false;
    private $html = '';
    private $text = '';
    private $slices = [];
    private $encloser;
    public $href = '';
    private $dump;
    private $dumpmessage;

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
        $row->abs($str, $p);
        return $row;
    }

    function getPos() { return $this->pos; }

    function setPos($pos) {
        if(gettype($pos) === "integer" && $pos >= 0 && $pos <= 88) {
            $l = $pos - $this->pos;
            if($l > 0 && mb_strlen($this->text) < $pos) {
                $this->text .= str_repeat(' ', $l);
            }
            $this->pos = $pos;
            //dump("\n".$this->text."|\n$l\n".mb_strlen($this->text)."\n".$this->pos);
        }
        else {
            throw new Exception("Row: wrong pos.");
        }
    }

    function rel($str, $i = 0, $style = '', $dump = false) {
        $_l = $l = mb_strlen(strip_tags($str));

        if(is_array($i)) {
            $_l = $i[1];
            $fp = $i[0];
            if(isset($_l) && $_l >0 && $_l < $l) throw new Exception("Row: l is too small $_l < $l.");
            $l = $_l;
            $i="[$fp, $_l]";
        }
        else if($i === 'back' || $i < 0) {
            $i = is_string($i) ? 0 : $i;
            $fp = $this->pos + $i - $l;
        } else {
            $fp = $this->pos + $i;
        }
        

        $this->dumpmessage = "REL: p={$this->pos}, i=$i -> fp=$fp, l=$_l\t|$str|";
        

        $this->addSlice($str, $fp, $l, $style, $dump);
    }

    function abs($str, $fp, $style = '', $dump = false) {
        
        
        $_l = $l = mb_strlen(strip_tags($str));
        
        if(is_array($fp)) {
            $_l = $fp[1];
            $fp = $fp[0];
            if($_l >0 && $_l < $l) throw new Exception("Row: l is too small $_l < $l.");
            $l = $_l;
        }
        
        
        if($fp === "lp")    $fp = -$this->width +1;
        else
        if($fp === 0)       $fp = $this->pos;
        else
        if($fp < 0)         $fp = -1 * $fp - $l + 1;
        

        $this->dumpmessage = "abs(|$str|, fp=$fp, l=$_l) $this->pos={$this->pos}";
        

        $this->addSlice($str, $fp, $l, $style, $dump);
    }

    function addSlice($str, $fp, $l, $style = '', $dump = false) {
        switch($style) {
            case '': break;
            case "italic":
            $str = "<span class=\"italic\">$str</span>";
            break;
            default:
            $str = "<span $style>$str</span>";
        }

        $sls = $this->slices;

        $lp = $fp + $l;

        if($dump)    dump("{$fp}, $l");

        $p=0;
        foreach($sls as $sfp => $s) {
            if($fp >= $s[0] && $fp < $s[1]) {
                $this->dump2($str, $fp, $lp, $l, 0);
                throw new Exception("Row: first position occupied: str=$str, {$s[0]} < $fp < {$s[1]}");
            }
            if($lp > $s[0] && $lp < $s[1]) {
                $this->dump2($str, $fp, $lp, $l, 0);
                throw new Exception("Row: last position occupied: {$s[0]} < $l < {$s[1]}");
            }
        }

        $this->slices[$fp] = [
            $fp,
            $lp,
            $l,
            $str,
            $this->dumpmessage
        ];
    }

    function print2($str, $p = 0, $style = '', $dump = false) {
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

        
        //$str = $this->text . str_repeat(' ', $ws) . $str;

        
        $ms = [];
        
        if($dump === true) {
            dump("ws=$ws, tp={$this->pos}, p=$p, fp=$fp, lp=$lp, l=$l\n|$str|");
        }
        
        $this->pos = $lp;

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


            $this->slices[] = new Slice($fp + $mb - $hl, $html);


            // dump(righello()
            // ."\n$str\n"
            // .strip_tags($str)."\n"
            // .pidx($mb-$hl, "mb-hl")."\n"
            // .pidx($p, "p_html")."\n"
            // .pidx($mb, "mb")."\n"
            // ."|$t|\n"
            // ."|$ss|\n"
            // .mb_strlen($ss)."\n"
            // ."$html\n"
            // .mb_strlen($html)."\n"
            // .mb_strlen($t));


            $hl += mb_strlen($html) - mb_strlen($t);
        }

        $t = $this->text;
        $lt = mb_strlen($t);
        $str_nh = strip_tags($str);
        if($fp > $lt)
            $t .= str_repeat(' ', $lp-$lt) . strip_tags($str);
        if($fp < $lt) {
            $t = mb_substr($t, 0, $fp) . $str_nh;
            if($lp < $lt) {
                $t .= mb_substr($t, $lp);
            }
        }

        $this->text = $t;

        return $l;
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

    function dump() {
        foreach($this->slices as $fp => $s) {

            $ws = $s[0] - 1;
            
            if($ws > 0) $t = str_repeat(' ', $ws) . $s[3];
            else $t = $s[3];

            $this->dump .= "$s[4]\n" . righello() . "|{$t}|" . pidx($s[0]) . pidx($s[1]) . pidx($ws) . "\n".$this->dumpSlice($s);
        }
    }

    function dump2($str, $fp, $lp, $l, $po) {
        $d = righello();
        $t='';

        $this->dump();
        
        $ws = $fp - 1;
        if($ws > 0) $t = str_repeat(' ', $ws);

        $t .= $str;
        $this->dump = $this->dumpmessage . righello() . "|{$t}|" . pidx($fp) . pidx($lp) . pidx($ws) . "\n|{$str}|";

        dump($this->dump . '\n|' . $this->__toString());
    }

    function dumpSlice($s) {
        return "$s[4], fp=$s[0], lp=$s[1], l=$s[2], str=|$s[3]|";
    }

    function __toString() {
        ksort($this->slices);

        $sls = $this->slices;

        $this->dump();
        
        $t = '';
        $t_l=0;
        foreach($sls as $fp => $s) {

            $ws = $s[0] - $t_l;

            if($ws >= 0) $t .= str_repeat(' ', $ws);
            else dump("ws=$ws, " . $this->dumpSlice($s));

            $t .= $s[3];

            $t_l = $s[1];
        }
        $t2 = $t;
        if($this->type === "link") $t = "<a href=\"{$this->href}\">{$t}</a>";
        else
        if($this->type) $t = "<span class=\"{$this->type}\">{$t}</span>";
        
        //dump("$this->type\n$t2\n$t", $this);

        return $t."\n";
    }

    function __toString2() {

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
        if($this->type === "link") $t2 = "<a href=\"{$this->href}\">{$t2}</a>";
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