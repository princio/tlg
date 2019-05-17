<?php

require './dumper/autoload.php';
require_once 'utils.php';
require_once 'Board.php';
?>

<?php


$servername = "localhost";
$username = "root";
$password = "37727";

$bb = new Board();

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

echo '<div class="board">';

/*monoopen("double");
emono("%20s", $fix['home'].sprintf("  %2s", $fix['home_goals']), [ "class" => "" ]);
ews(3);
emono("%-20s", sprintf("%2s  ", $fix['away_goals']).'  '.$fix['away'], [ "class" => "bold" ]);
monoclose();*/
$bb->ep("{$fix['date']}, {$fix['time']}: {$fix['type']}");
eww(2);
$bb->setRowType("double");
$bb->epf(20, $fix['home'], "bold");
$bb->ews(3);
$bb->epf(-20, $fix['home'], "bold");
$bb->eww();
$bb->epf(20, $fix['home_goals'], "bold");
echo " — ";
$bb->epf(-20, $fix['away_goals'], "bold");
$bb->eww();

$bb->ews(9);
$bb->ewc('_', 70);   
$bb->eww();
foreach ($evts as $e) {
    $a = $apps[$e['app_id']];
    $isH = $a['team_id'] === $fix['home_team_id'];
    $ee = event_to_html($e, $isH);
    if($isH) {
        $bb->epf(40, "{$a['name']} {$a['surname']} $ee");
    } else {
        $bb->ews(40);
        $bb->ep("  ··  ");
        
        $bb->epf(-40, "$ee {$a['name']} {$a['surname']}");
    }
    $bb->ww();
}
$bb->ews(9);
$bb->ewc('‾', 70);
$bb->eww(7);

/*$ha = array_values($ha);
$aa = array_values($aa);

for ($i = 0; $i < max(count($ha), count($aa)); $i++) {
    if($i < count($ha)) {
        $bb->epf("%-{$name_max}s", $ha[$i]['name']);
        ews();
        emono("%-{$surname_max}s", $ha[$i]['surname']);
        ews(43 - $name_max - $surname_max);
    }
    else {
        ews(44);
    }
    if($i < count($aa)) {
        ews(43 - $name_max - $surname_max);
        emono("%{$surname_max}s", $aa[$i]['surname']);
        ews();
        emono("%{$name_max}s", $aa[$i]['name']);
    }
    eww();
    eww();
    eww();
    monoclose();
}*/

$ha = array_values($ha);
$aa = array_values($aa);

$bb->eww();
for ($i = 0; $i < max(count($ha), count($aa)); $i++) {
    $hname = $ha[$i]['name'] ?? '';
    $hsurn = $ha[$i]['name'] ?? '';
    $aname = $aa[$i]['surname'] ?? '';
    $asurn = $aa[$i]['surname'] ?? '';
    $bb->setRowType('half');
    $bb->epf(-44, $hname);
    $bb->epf(44, $aname);
    $bb->eww();
    $bb->epf(-44, $hsurn);
    $bb->epf(44, $asurn);
    $bb->eww();
    $bb->eww();
    $bb->eww();
}

?>