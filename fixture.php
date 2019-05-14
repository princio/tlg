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


$sql = "SELECT * FROM fixture_view WHERE id = ?";
$fix_sel = $new->prepare($sql);
$fix_sel->execute([$page_id]);
$fix = $fix_sel->fetch(PDO::FETCH_ASSOC);


$sql = "SELECT * FROM fixture_players WHERE fixture_id = ?";
$app_sel = $new->prepare($sql);
$app_sel->execute([ $page_id ]);
$_apps = $app_sel->fetchAll(PDO::FETCH_ASSOC);

$surname_max = 0;
$name_max = 0;

$ha = [];
$aa = [];
foreach ($_apps as $a) {
    $id = array_shift($a);
    $apps[$id] = $a;
    if($a['team_id'] === $fix['home_team_id']) {
        $ha[$id] = $a;
    } else {
        $aa[$id] = $a;
    }
    $surname_max = max($surname_max, strlen($a['surname']));
    $name_max = max($name_max, strlen($a['name']));
}

$sql = "SELECT `event`.* FROM `event` JOIN `app` ON `app_id` = app.id WHERE app.fixture_id = ? ORDER BY `event`.`minute`, `event`.`id`";
$evt_sel = $new->prepare($sql);
$evt_sel->execute([ $page_id ]);
$evts = $evt_sel->fetchAll(PDO::FETCH_ASSOC);

/*monoopen("double");
emono("%20s", $fix['home'].sprintf("  %2s", $fix['home_goals']), [ "class" => "" ]);
ews(3);
emono("%-20s", sprintf("%2s  ", $fix['away_goals']).'  '.$fix['away'], [ "class" => "bold" ]);
monoclose();*/

$board = new Board();

/*$board->printRow(Row::Fast("{$fix['date']}, {$fix['time']}: {$fix['type']}"));
$board->ww(2);
$row = new Row("double");
$row->print($fix['home'], 0, "bold");
$row->print($fix['home_goals'], -20, "bold");
$row->print(" — ");
$row->print($fix['away_goals'], 23, "bold");
$row->print($fix['away'], -44, "bold");
$board->printRow($row);
$board->ww(2);
*/$row = new Row("double");
$row->print($fix['home'], 0, "bold");
$row->print($fix['away'], -44, "bold");
$board->printRow($row);
$board->ww(2);
$row = new Row("double");
$of = 20;
$row->print($fix['home_goals'], $of, "bold");
$row->print("-", 21, "bold");
$row->print($fix['away_goals'], -43+$of, "bold");
$board->printRow($row);
$board->ww(2);
$board->printRow(Row::Fast(wc('_', 70), 9));


foreach ($evts as $e) {
    $row = new Row();
    $a = $apps[$e['app_id']];
    $isH = $a['team_id'] === $fix['home_team_id'];
    $ee = event_to_html($e, $isH);
    if($isH) {
        $row->print("{$a['name']} {$a['surname']} {$ee}", -40);
    }
    $row->print("  ··  ", 40);
    if(!$isH) {
        $row->print("{$ee} {$a['name']} {$a['surname']}", 46);
    }
    $board->printRow($row);
}
$board->printRow(Row::Fast(wc('‾', 70), 9));

$board->ww(5);

$ha = array_values($ha);
$aa = array_values($aa);

for ($i = 0; $i < max(count($ha), count($aa)); $i++) {
    $row1 = new Row();
    $row2 = new Row();
    if($i < count($ha)) {
        $row1->print($ha[$i]['name']);
        $row2->print($ha[$i]['surname']);
    }
    if($i < count($aa)) {
        $row1->print($aa[$i]['name'], -88);
        $row2->print($aa[$i]['surname'], -88);
    }
    $board->printRow($row1);
    $board->printRow($row2);
    $board->ww();
}

?>