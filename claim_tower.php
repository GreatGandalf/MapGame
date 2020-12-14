<?php
session_start();
if( isset($_SESSION["player_id"]) and isset($_SESSION["faction"]) and isset($_POST['towerid']) )
{
require 'connect.php';

$player_id = $_SESSION["player_id"];
$faction = $_SESSION["faction"];
$towerid = $_POST['towerid'];

$claimtower = "UPDATE map_towers SET faction = '$faction' WHERE id = '$towerid'";

$claimtowerresult = $connect->query($claimtower);

}
else {
}
?>