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
    $a['id'] = $id;
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

$board = new Board();

$row = new Row("double");
$row->abs($fix['home'], 0, "bold");
$row->abs($fix['away'], "lp", "bold");
$board->printRow($row);
$board->ww(2);
$row = new Row("double");
$of = 18;
$row->abs($fix['home_goals'], $of, "bold");
$row->abs("-", 21, "bold");
$row->abs($fix['away_goals'], -43+$of, "bold");
$board->printRow($row);
$board->ww(2);
$board->printRow(Row::Fast(wc('_', 70), 9));


foreach ($evts as $e) {
    $row = new Row();
    $a = $apps[$e['app_id']];
    $isH = $a['team_id'] === $fix['home_team_id'];
    $ee = event_to_html($e, $isH);
    $row->setPos(39);

    if($isH) {
        $row->rel(event2text($e, $l), ["back", $l]);
        // $row->rel($ee, [ "back", -2 ]);
        $row->rel("{$a['name']} {$a['surname']}", -1);
    }
    $row->abs("  ··  ", 40);
    if(!$isH) {
        // $row->rel(event_minute($e), [0, -3], "italic");
        $row->rel(event2text($e, $l, false), [0, $l]);

        // $row->rel($ee, [0, 1]);
        $row->rel("{$a['name']} {$a['surname']}", 1);
    }
    $board->printRow($row);

    //$row->dump();

    if(!array_key_exists('evts', $a)) {
        $apps[$e['app_id']]['evts'] = [];
    }
    $apps[$e['app_id']]['evts'][] = $e;
}

$board->printRow(Row::Fast(wc('‾', 70), 9));

$board->ww(5);

$ha = array_values($ha);
$aa = array_values($aa);

$mmmax = max($name_max, $surname_max);

for ($i = 0; $i < max(count($ha), count($aa)); $i++) {
    $row1 = new Row();
    $row2 = new Row();
    $row3 = new Row();
    if($i < count($ha)) {
        $row1->rel($ha[$i]['name']);
        $row2->rel($ha[$i]['surname']);
        $a = $apps[$ha[$i]['id']];
        if(array_key_exists('evts', $a)) {
            $row3->setPos(1);
            foreach ($apps[$ha[$i]['id']]['evts'] as $e) {
                $row3->rel(event2text($e, $l), 1);
                // event_print($row, $e, $null, 1);
                // $row3->rel(event_to_html($e, $null), 1);
                // $row3->rel($e['minute'].'\'', 0, 'italic');
            }
        }
    }
    if($i < count($aa)) {
        $a = $apps[$aa[$i]['id']];
        if(array_key_exists('evts', $a)) {
            $es = $apps[$aa[$i]['id']]['evts'];
            $row3->setPos(86);
            // $row3->setPos(88/* - $mmmax*/ -5 - 5*(count($es)));
            // $j = count($es) - 1;
            // $row3->abs(event_to_html($es[$j], $null));
            for ($j=0; $j < count($es); $j++) {
                $e = $es[$j];

                $row3->rel(event2text($e, $l, false), -1);

                // event_print($row, event2text($es[$j], false), $null, -1);
                // $row3->rel(event_to_html($es[$j], $null), -1, '');
                // $row3->rel(event_minute($e), "back", 'italic');
            }
        }
        $row1->abs($aa[$i]['name'], "lp");
        $row2->abs($aa[$i]['surname'], "lp");
    }
    $board->printRow($row1);
    $board->printRow($row2);
    $board->printRow($row3);
    $board->ww(2);
}

?>