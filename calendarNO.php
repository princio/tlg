<?php

require './dumper/autoload.php';
require_once './Board.php';
require_once 'utils.php'
?>

<?php


$servername = "localhost";
$username = "princio";
$password = "pomo";


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
$row_dash = Row::Fast(str_repeat('- ', 32).'-', 0);
foreach ($fixs as $f) {
    $d = new DateTimeImmutable($f['date_tot']);
    $row1 = new Row();
    $row2 = new Row();
    
    $row1->print($d->format("H:i"), 15);

    $row1->print($f['hname'], 25);
    $row2->print($f['aname'], 25);

    $row1->print($f['hg'], 60);
    $row2->print($f['ag'], 60);

    if($d->format("d") !== $d_old->format("d")) {
        $board->printRow($row_dash);
        $board->printRow(Row::Fast($d->format("d/m/Y:"), 2));
    }
    $board->printRow($row1);
    $board->printRow($row2);
    $board->ww();
    $d_old = $d;
}



?>