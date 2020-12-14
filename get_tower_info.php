<?php
session_start();
if( isset($_POST['towerid']) )
{
require 'connect.php';

$player_id = $_SESSION["player_id"];
$faction = $_SESSION["faction"];
$towerid = htmlspecialchars($_POST['towerid']);

$getTowerInfo = "SELECT * FROM map_towers WHERE id = '$towerid'";

$TowerInforesult = $connect->query($getTowerInfo);

if ($TowerInforesult->num_rows > 0) {
    // output data of each row
    while($row = $TowerInforesult->fetch_assoc()) {
    	$towerFaction = $row['faction'];
    	$towerStrength = $row['strength'];
    	$towerName = $row['name'];
    }

    echo "
    <script>
    towerFaction = '".$towerFaction."';
    towerStrength = '".$towerStrength."';
    towerName = '".$towerName."';
    towerID = '".$towerid."';
    ";

    if($towerFaction == $faction) {
    	echo "
    	showTowerInfo(true);
    	</script>";
    }
    else {
    	echo "
    	showTowerInfo(false);
    	</script>";
    }

}

}
else {

}
?>