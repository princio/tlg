
<?php
ini_set('display_startup_errors', 1);
ini_set('display_errors', 1);
error_reporting(E_ALL);

require './dumper/autoload.php';

$servername = "localhost";
$username = "princio";
$password = "pomo";

try {
    $old = new PDO("mysql:host=$servername;dbname=doricocup", $username, $password);
    // set the PDO error mode to exception
    $old->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "Connected successfully";
}
catch(PDOException $e)
{
    echo "Connection failed: " . $e->getMessage();
}


try {
    $new = new PDO("mysql:host=$servername;dbname=newdb", $username, $password);
    // set the PDO error mode to exception
    $new->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "Connected successfully";
}
catch(PDOException $e)
{
    echo "Connection failed: " . $e->getMessage();
}


// $sql = "SELECT * FROM app a JOIN fixture f ON a.fixture_id = f.id ORDER BY f.data ASC ";
// $stmt= $new->prepare($sql);





// $sql = "TRUNCATE TABLE `app`; TRUNCATE TABLE `fixture`; TRUNCATE TABLE `event`;";
// $stmt= $new->prepare($sql);
// $stmt->execute();

// $sql = "SELECT * FROM fixture ORDER BY `date` ASC ";
// $stmto= $old->prepare($sql);
// $stmto->execute();

// $sql = "INSERT INTO `fixture`(`id`, `s_league_id`, `date`, `type`, `home_s_l_club_id`, `away_s_l_club_id`, `home_goals`, `away_goals`) VALUES (null, ?, ?, ?, ?, ?, ?, ?)";
// $stmt= $new->prepare($sql);

// $sql = "SELECT * FROM `app2` WHERE `fixture_id` = ?";
// $app_sel= $new->prepare($sql);

// $sql = "INSERT INTO `app`(`id`, `fixture_id`, `s_player_id`, `yellow_card`, `red_card`, `first_yellow_card_minute`, `second_yellow_card_minute`, `penalty_kick`, `match_bans`, `other_information`, `is_present`) 
//         VALUES (null, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
// $app_ins= $new->prepare($sql);



// $sql = "SELECT * FROM `event2` WHERE `app_id` = ?";
// $evt_sel= $new->prepare($sql);

// $sql = "INSERT INTO `event`(`id`, `app_id`, `minute`, `type`, `subtype`)
//         VALUES (null, ?, ?, ?, ?)";
// $evt_ins= $new->prepare($sql);


// $ids = [];
// while($res = $stmto->fetch(PDO::FETCH_NUM)) {
//     $f_oldid = array_shift($res);
//     if(!$stmt->execute($res)) {
//         dump($stmt->errorInfo);
//     }
//     $f_newid = $new->lastInsertId();

//     $app_sel->execute([$f_oldid]);
//     while($app = $app_sel->fetch(PDO::FETCH_NUM)) {
//         $app_oldid = array_shift($app);
//         $app[0] = $f_newid;
//         $app_ins->execute($app);
//         $app_newid = $new->lastInsertId();

//         $evt_sel->execute([$app_oldid]);
//         while($evt = $evt_sel->fetch(PDO::FETCH_NUM)) {
//             array_shift($evt);
//             $evt[0] = $app_newid;
//             $evt_ins->execute($evt);
//         }
//     }
// }

// try {
// $stmto = $old->prepare("SELECT * FROM app WHERE yellow_card > 0 OR red_card > 0 ORDER BY app.id");

// $stmto->execute();

// var_dump($stmto->rowCount());

// $sql = "INSERT INTO `event2`(`id`, `app_id`, `minute`, `type`, `subtype`) VALUES (NULL, ?, ?, ?, ?)";
// $stmt= $new->prepare($sql);

// while($res = $stmto->fetch(PDO::FETCH_ASSOC)) {
//     $app = intval( $res['id']);
//     var_dump($app);

//     $yc = $res['yellow_card'];
//     $rc = $res['red_card'];
    
//     if($yc >= 1) {
//         $stmt->execute([$app, NULL, 'booking', 'yc' ]);
//     }
//     if($yc == 2) {
//         $stmt->execute([$app, NULL, 'booking', 'yc' ]);
//     }
//     if($rc == 1) {
//         $stmt->execute([$app, NULL, 'booking', 'rc' ]);
//     }
// }
// }
// catch(PDOException $e)
// {
//     echo "Connection failed: " . $e->getMessage();
// }