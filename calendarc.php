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
foreach ($fixs as $f) {
    if($cr++ % 3 === 0 && $cr !== 1) {
        $p=0;
        $board->printRow($row1);
        $board->printRow($row2);
        $board->printRow($row3);
        $board->printRow($row4);
        $board->printRow($row5);
        $board->ww(5);

        $row1 = new Row();
        $row2 = new Row();
        $row3 = new Row();
        $row4 = new Row();
        $row5 = new Row();
        if($cr === 3) break;
    } else {
        $p = (($cr-1)%3) * 30;
        $row1->setPos($p);
        $row2->setPos($p);
        $row3->setPos($p);
        $row4->setPos($p);
        $row5->setPos($p);
    }

    
    $d = new DateTimeImmutable($f['date_tot']);
    $type = $f['type'];
    $hn = $f['hname'];
    $an = $f['aname'];
    // $hn = count($hn) > 20 ? substr($f['hname'], 0, 17).'...' : $hn;
    // $an = count($an) > 20 ? substr($f['hname'], 0, 17).'...' : $an;

    $row1->abs($d->format("d/m, H.i:"), $p, "italic");
    $row1->rel($type, 1, "bold");

    if(strlen($hn) > 15) {
        $lws = strrpos($hn, ' ');
        $row2->rel(substr($hn, 0, $lws));
        $row3->rel(substr($hn, $lws+1));
    } else {
        $row2->rel($hn);
    }
    //$row2->rel($hn, [0, 25]);

    if(strlen($an) > 15) {
        $lws = strrpos($an, ' ');
        $row4->rel(substr($an, 0, $lws));
        $row5->rel(substr($an, $lws+1));
    } else {
        $row5->rel($an);
    }
    //$row3->rel($an, [0, 25]);

    $row2->abs($f['hg'], [$p + 17, -2]);
    $row5->abs($f['ag'], [$p + 17, -2]);

    $w = 20;
    // $row1->abs('.', $p + $w);
    // $row2->abs('.', $p + $w);
    // $row3->abs('.', $p + $w);
    // $row4->abs('.', $p + $w);
    // $row5->abs('.', $p + $w);

    // if($d->format("d") !== $d_old->format("d")) {
    //     $board->printRow($row_dash);
    //     $board->ww(1);
    //     $board->printRow(Row::Fast($d->format("d/m/Y:"), 2));
    //     $board->ww(1);
    // }

    $d_old = $d;
}



?>