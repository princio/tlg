<?php

require './dumper/autoload.php';
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

echo '<div class="board">';

/*monoopen("double");
emono("%20s", $fix['home'].sprintf("  %2s", $fix['home_goals']), [ "class" => "" ]);
ews(3);
emono("%-20s", sprintf("%2s  ", $fix['away_goals']).'  '.$fix['away'], [ "class" => "bold" ]);
monoclose();*/
monoopen();
echo "{$fix['date']}, {$fix['time']}: {$fix['type']}";
eww(2);
monoclose();
monoopen("double");
emono("%20s", $fix['home'], [ "class" => "bold" ]);
emono("%s", "   ");
emono("%-20s", $fix['away'], [ "class" => "bold" ]);
eww();
emono("%20s", $fix['home_goals'], [ "class" => "bold" ]);
emono("%s", " — ");
emono("%-20s", $fix['away_goals'], [ "class" => "bold" ]);
monoclose();
eww();
ews(9); ewc('_', 70); eww(); ews(42); echo '··'; ews(42);

foreach ($evts as $e) {
    monoopen();
    $a = $apps[$e['app_id']];
    $isH = $a['team_id'] === $fix['home_team_id'];
    $ee = event_to_html($e, $isH);
    if($isH) {
        emono("%40s  ··", $a['name'] . ' ' . $a['surname'] . '  ' . $ee);
    } else {
        ews(40);
        emono("%s", "  ··  ");
        
        emono("%-40s", $ee . '  ' . $a['name'] . ' ' . $a['surname']);
    }
    eww();
    monoclose();
}
ews(9); ewc('_', 70);
eww(7);

$ha = array_values($ha);
$aa = array_values($aa);

for ($i = 0; $i < max(count($ha), count($aa)); $i++) {
    monoopen();
    if($i < count($ha)) {
        emono("%-{$name_max}s", $ha[$i]['name']);
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
    monoclose();
}

?>