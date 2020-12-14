<?php
session_start();
if( isset( $_SESSION["player_id"] ) )
{
require 'connect.php';

$player_id = $_SESSION["player_id"];

$selecthp = "SELECT health, max_health FROM map_players WHERE id = '$player_id'";

$result = $connect->query($selecthp);

if ($result->num_rows > 0) {
    // output data of each row
    while($row = $result->fetch_assoc()) {
    	echo "
        <script>
        PlayerHealth = ".$row['health'].";
        PlayerMaxHealth = ".$row['max_health'].";
        </script>
        ";
    }
}
}
else {
}
?>