<?php
session_start();
if( isset( $_POST['latit'] ) and isset( $_POST['lonit'] ) )
{
require 'connect.php';

$player_id = $_SESSION["player_id"];
$latit = $_POST['latit'];
$lonit = $_POST['lonit'];
$my_date = date("Y-m-d H:i:s");

$updatehp = "UPDATE map_player_locations SET latitude = '$latit', longitude = '$lonit', timedate = '$my_date' WHERE player_id = '$player_id'";

$result = $connect->query($updatehp);

}
?>