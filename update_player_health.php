<?php
session_start();
if( isset($_SESSION["player_id"]) and isset($_POST['amount']) and isset($_POST['plusminus']))
{
require 'connect.php';

$player_id = $_SESSION["player_id"];
$change_player_hp = $_POST['amount'];
$plusminus = $_POST['plusminus'];

if($plusminus === "plus") {
	$updatehp = "UPDATE map_players SET health = health + '$change_player_hp' WHERE id = '$player_id'";
} elseif($plusminus === "minus") {
	$updatehp = "UPDATE map_players SET health = health - '$change_player_hp' WHERE id = '$player_id'";
}

$result = $connect->query($updatehp);


$selecthp = "SELECT health, max_health FROM map_players WHERE id = '$player_id'";

$result = $connect->query($selecthp);

if ($result->num_rows > 0) {
    // output data of each row
    while($row = $result->fetch_assoc()) {
    	if($row['health'] <= 0) {
    		$insideupdatehp = "UPDATE map_players SET health = 0 WHERE id = '$player_id'";

			$insideresult = $connect->query($insideupdatehp);

			echo "
        	<script>
        	PlayerHealth = 0;
        	PlayerMaxHealth = ".$row['max_health'].";
        	</script>
        	";
    	} elseif($row['health'] > $row['max_health']) {
    		$insideupdatehp = "UPDATE map_players SET health = max_health WHERE id = '$player_id'";

			$insideresult = $connect->query($insideupdatehp);

			echo "
        	<script>
        	PlayerHealth = ".$row['max_health'].";
        	PlayerMaxHealth = ".$row['max_health'].";
        	</script>
        	";
    	} else {
    	echo "
        <script>
        PlayerHealth = ".$row['health'].";
        PlayerMaxHealth = ".$row['max_health'].";
        </script>
        ";
    	}
    }
}
}
else {
	echo "
    <script>
    alert('isset didnt go');
    </script>
    ";
}
?>