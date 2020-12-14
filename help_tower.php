<?php
session_start();
if( isset($_SESSION["player_id"]) and isset($_SESSION["faction"]) and isset($_POST['towerid']) and isset($_POST['amount']) )
{
require 'connect.php';

$player_id = $_SESSION["player_id"];
$faction = $_SESSION["faction"];
$towerid = htmlspecialchars($_POST['towerid']);
$amount = htmlspecialchars($_POST['amount']);

function checkAmount() {
	$amount = $GLOBALS['amount'];
	$player_id = $GLOBALS['player_id'];
	$towerid = $GLOBALS['towerid'];
	$connect = $GLOBALS['connect'];

	$checkplayeramount = "SELECT coins FROM map_general_inventory WHERE player_id = '$player_id'";

	$checkplayeramountresult = $connect->query($checkplayeramount);

	if ($checkplayeramountresult->num_rows > 0) {
    	while($row = $checkplayeramountresult->fetch_assoc()) {
    		$coins = $row['coins'];
    		if($coins >= $amount) {
    			// If the player has enough coins
    			$update_playercoins = "UPDATE map_general_inventory SET coins = coins - '$amount' WHERE player_id = '$player_id'";
				$connect->query($update_playercoins);

				$update_towerstrength = "UPDATE map_towers SET strength = strength + '$amount' WHERE id = '$towerid'";
				$connect->query($update_towerstrength);

				echo "<script>hideTower();</script>";
    		}
    		else {
    			// If the player doesn't have enough coins
    			echo "<script>alert('You dont have enough coins for that');</script>";
    		}
    	}
	}
}

$checktower = "SELECT * FROM map_towers WHERE id = '$towerid'";
$checktowerresult = $connect->query($checktower);

if ($checktowerresult->num_rows > 0) {
    while($row = $checktowerresult->fetch_assoc()) {
    	$towerFaction = $row['faction'];
    }

    if ($towerFaction == $faction) {
    	checkAmount();
    }
}

}
else {
}
?>