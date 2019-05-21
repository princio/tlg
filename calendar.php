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
foreach ($fixs as $f) {

    $d = new DateTimeImmutable($f['date_tot']);
    $type = $f['type'];
    $hn = $f['hname'];
    $an = $f['aname'];
    $hn = count($hn) > 20 ? substr($f['hname'], 0, 17).'...' : $hn;
    $an = count($an) > 20 ? substr($f['hname'], 0, 17).'...' : $an;
    $row1 = new Row("link");
    $row1->href = "/fixture/{$f['id']}";

    $row1->abs($d->format("H:i"), 0);

    $row1->rel($type, 1);

    $row1->rel($hn, [2, 25]);
    $row1->rel($an, [2, 25]);

    $row1->rel($f['hg'], [0, 2]);
    $row1->rel(' - ');
    $row1->rel($f['ag'], [0, 2]);
    if($d->format("d") !== $d_old->format("d")) {
        $board->printRow($row_dash);
        $board->ww(1);
        $board->printRow(Row::Fast($d->format("d/m/Y:"), 2));
        $board->ww(1);
    }
    $board->printRow($row1);
    $board->ww(1);

    $d_old = $d;
}



?>