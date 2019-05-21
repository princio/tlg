<?php

require './dumper/autoload.php';
require_once './Board.php';
require_once 'utils.php'
?>

<?php


$servername = "localhost";
$username = "princio";
$password = "37727";


try {
    $new = new PDO("mysql:host=$servername;dbname=newdb", $username, $password);
    // set the PDO error mode to exception
    $new->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
}
catch(PDOException $e)
{
    echo "Connection failed: " . $e->getMessage();
}




$sql = "SELECT * FROM `calendar` WHERE `season_id` = ? order by `date`";
$fix_sel = $new->prepare($sql);
$fix_sel->execute([$page_id]);
$fixs = $fix_sel->fetchAll(PDO::FETCH_ASSOC);

$d_old = new DateTime($fixs[0]['date_tot']);
$d_old->sub(new DateInterval("P2D"));
$weeks = [];
$days = [];
$day = [];
$ll=0;
foreach ($fixs as $f) {
    $d = new DateTime($f['date_tot']);


    // dump(sprintf("w=%d, wo=%d\nd=%d, do=%d", $d->format("W"), $d_old->format("W"), $d->format("w"), $d_old->format("w")));
    
    // if($d->format("W") !== $d_old->format("W") && count($days) > 0) {
    //     $weeks[$d->format("W")] = $week;
    //     $week = [];
    // }

    // if($d->format("d") !== $d_old->format("d") && count($day) > 0) {
    //     $week[$d->format("w")] = $day;
    //     $day = [];
    // }
    if(!array_key_exists($d->format("W"), $weeks)) {
        $weeks[$d->format("W")] = [];
    } 
    if(!array_key_exists($d->format("w"), $weeks[$d->format("W")])) {
        $weeks[$d->format("W")][$d->format("w")] = [];
    }

    $weeks[$d->format("W")][$d->format("w")][] = $f;

    dump($d->format("W") .','. $d->format("w") .','. $ll);

    $ll++;

    $d_old = $d;
}

$days[$d->format("w")] = $day;
$weeks[$d->format("W")] = $days;

dump("count=".count($fixs).", ll=$ll");

dump($weeks);

$board = new Board();

$d_old = new DateTime($fixs[0]['date_tot']);
$d_old->sub(new DateInterval("P2D"));
$row_dash = Row::Fast(str_repeat('- ', 38).'-', 0);

$row1 = new Row();
$row2 = new Row();
$row3 = new Row();
$row4 = new Row();
$row5 = new Row();
$cr = 0;
$p=0;
$w = 20;
foreach ($weeks as $week) {
    $n = 9;
    $rows = rows_gen($n);
    $p = -$w;
    foreach ($week as $day) {
        $p += $w;
        foreach ($day as $fn => $f) {
            rows_setpos($rows, $p);

            $r0 = &$rows[$fn*3];
            $r1 = &$rows[$fn*3 + 1];
            $r2 = &$rows[$fn*3 + 2];

            $d = new DateTime($f['date_tot']);
            $type = $f['type'];
            $hn = $f['hname'];
            $an = $f['aname'];

            $r0->abs($d->format("H.i,"), $p, "italic");
            $r0->rel($type, 1, "bold");

            $r1->rel(short_name($hn), [0, 6]);
            $r2->rel(short_name($an), [0, 6]);
            
            $r1->rel($f['hg'], [0, -2]);
            $r2->rel($f['ag'], [0, -2]);

        }
    }
    for($i=0; $i < $n; $i++) {
        if($i % 3 === 0 && $i > 0) {
            $board->ww(1);
        }
        $board->printRow($rows[$i]);
    }
    $board->ww(5);
}

?>