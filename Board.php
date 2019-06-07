<?php

class Board {

    const WIDTH = 88;

    const SW = 10;
    private $curP = 0;
    private $cr = null;
    private $rown = 0;
    private $rows = [];

    function __constructor() {}

    function printRow($row) {

        $this->rows[] = $row;

        return;

        $rown = ++$this->rown;

        if(!$row->isClosed())
            $row->close();
        
        $row->top = $rown * 20;
        echo $row;
        
        // echo "<div class=\"\" style=\"z-index: 5; position: absolute; left: 1200px;\">";
        // echo "<button onclick=\"$('#row-$rown').toggle()\" class=\"b-dump\">d</button>";
        // echo "<div id=\"row-$rown\" style=\"display: none; position: relative; left: -900px; width: 880px;\">";
        // echo $row->dump();
        // echo "</div></div>\n";
    }

    function print() {
        echo '<div class="board">';
        foreach ($this->rows as $i => $row) {
            // if(!$row->isClosed())
            // $row->close();

            echo $row;
        }
        echo '</div>';
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
    private $dumped;
    private $dumpmessage;
    public $top = 25;

    function __construct($type = "normal") {
        $this->type = $type;
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

    function abs($str, $p, $w = null, $style = '', $dump = false) {
        $pp = $p;
        if($p === "lp") $pp = 0;

        $this->addSlice($str, $p, $pp, $w, $style);

        $this->pos = $p + $w ?? 0;
    }

    function rel($str, $p = 0, $w = null, $style = '', $dump = false) {
        if($p < 0) {
            $pp = -1*$this->pos;
            $w = abs($p);
            $p = 0;
        } else {
            $pp = $this->pos + $p;
        }
        $this->addSlice($str, $p, $pp, $w, $style);
        $a = $this->pos;
        $this->pos += abs($p) + $w ?? 0;
//dump("$a -> " .$this->pos . ": $str");
}

    function addSlice($str, $po, $p, $w, $style = '', $dump = false) {
        switch($style) {
            case '': break;
            case "italic":
            $str = "<span class=\"italic\">$str</span>";
            break;
            case "bold":
            $str = "<span class=\"bold\">$str</span>";
            break;
            default:
            $str = "<span $style>$str</span>";
        }

        $sls = $this->slices;

        $this->slices[] = [
            'html' => $str,
            'po' => $po,
            'p' => $p,
            'w' => $w,
            'style' => $style
        ];
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
        $t = "<div class=\"row {$this->type}\" ";
        switch($this->type) {
            case "link":
            $t = "<a href=\"{$this->href}\"";
            break;
            case "double":
            $this->top = 50;
            break;
            default:
            break;
        }

        $t .= " style=\"height: {$this->top}px;\">";


        foreach($this->slices as $fp => $s) {
            $style = $s['style'];
            $p = $s['p'];
            $po = $s['po'];
            if($po === "lp") {
                $p = "text-align: right; right: {$p}px;";
            } else {
                $p = $p > 0 ? "left: {$p}px;" : "text-align: right; left: " . ($p*-1) . "px;";
            }
            $w = $s['w'];
            $w = $w ? "width: {$w}px;" : "";
            $html = $s['html'];

            $t .= "<span class=\"{$style}\" style=\"position: absolute; $w $p\">$html</span>";
        }
        $t2 = $t;
        if($this->type === "link") $t .= "</a>";
        else $t .= "</div>";
        
        return $t;
    }
}