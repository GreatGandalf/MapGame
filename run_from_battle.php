<?php
session_start();
if( isset($_SESSION["player_id"]) )
{
require 'connect.php';

$player_id = $_SESSION["player_id"];

function player_runs() {
	$player_id = $GLOBALS['player_id'];
	$connect = $GLOBALS['connect'];
	$player_ran = "player_ran";

	$select_monsterid = "SELECT monster_id FROM map_current_battle WHERE player_id = '$player_id'";
	$select_monsteridresult = $connect->query($select_monsterid);
	if ($select_monsteridresult->num_rows > 0) {
    	// output data of each row
    	while($row = $select_monsteridresult->fetch_assoc()) {
    		$monster_id = $row['monster_id'];
    	}
	}

	$insert_past_battle = "INSERT INTO map_past_battles (player_id, monster_id, result) VALUES ('$player_id', '$monster_id', '$player_ran');";
	$insert_past_battleresult = $connect->query($insert_past_battle);

	$set_battle_over = "UPDATE map_current_battle SET in_battle = 0, can_attack = 0, monster_id = 0, monster_hp = 0, monster_att = 0, coin_reward = 0 WHERE player_id = '$player_id'";
	$set_battle_overresult = $connect->query($set_battle_over);

	echo"<script>
	for (var i = 0; i < markers.length; i++) {
        markers[i].setTitle(\"delete\");
    }
	</script>";
}

$select_battle_status = "SELECT in_battle, can_attack FROM map_current_battle WHERE player_id = '$player_id'";
$statusresult = $connect->query($select_battle_status);

if ($statusresult->num_rows > 0) {
    // output data of each row
    while($row = $statusresult->fetch_assoc()) {
    	if($row['in_battle'] == 1 and $row['can_attack'] == 1) {
    		player_runs();
    	}
    }
}

}
else {
	// If not set
}
?>