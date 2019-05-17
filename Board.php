<?php 

class Board {

    private $rowtype = "normal";
    private $row_length = "normal";
    public $classes = "";

    function setRowType($rowtype) {
        $this->rowtype = $this->rowtype;
        return $this;
    }

    public function row($rowtype = "normal", $classes = '') {
        $this->row_length = 0;
        $this->rowtype = $this->rowtype;
        $this->classes = $classes;
        echo "<span class=\"{$this->rowtype} {$classes}\">";
    }

    public function closerow() {
        echo "</span>";
    }

    public function ws($n = 1) {
        return str_repeat(' ', $n);
    }

    public function ews($n = 1) {
        echo ws($n);
    }

    public function wc($c, $n = 1) {
        return str_repeat($c, $n);
    }

    public function ewc($c, $n = 1) {
        echo wc($c, $n);
    }

    function ww($n = 1) {
        return str_repeat("\n", $n);
    }

    public function eww($n = 1) {
        $this->closerow();
        $this->row($this->rowtype, $this->classes);
        echo ww($n);
    }

    public function adjustLength(&$ll) {
        $l = abs($ll);
        $this->row_length += abs($l);
        if($this->row_length > 88) {
            $this->row_length = 88 - $this->row_length;
            eww();
        }
        switch($this->rowtype) {
            case "half":
            $l = $l >> 1;
            break;
            case "normal":
            break;
            case "double":
            $l = $l << 1;
            break;
        }
        $this->row_length += $l;
        return $l;
    }

    public function pf($l, $str, $classes = '') {
        $this->adjustLength($l);
        return '<span class="'.$classes.'">'.$str.'</span>';
    }

    public function epf($f, $str, $classes = '') {
        echo $this->pf($f, $str, $classes);
    }

    public function p($str, $classes = '') {
        $l = strlen($str);
        $this->adjustLength($l);
        return '<span class="'.$classes.'">'.$str.'</span>';
    }

    public function ep($str, $classes = '') {
        echo $this->p($str, $classes);
    }

    function format($f, $str, $classes = '') {
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
            $str = '<span class="'.$classes.'">'.$str.'</span>';
        }
        return $str;
    }


}