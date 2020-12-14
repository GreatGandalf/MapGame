<?php
session_start();
if( isset($_SESSION["player_id"]) and isset($_POST['itemid']))
{
require 'connect.php';

$player_id = $_SESSION["player_id"];
$itemid = htmlspecialchars($_POST['itemid']);

if($itemid[0] !== "w") {
	die("Not right");
}

function handle_javascript() {
	echo "<script>
	GetLoadout();
	hideCompare();
	$(\"#itemDetails\").toggle();
	$(\"#inventory\").toggle();
	</script>";
}

function equip() {
	$player_id = $GLOBALS['player_id'];
	$connect = $GLOBALS['connect'];
	$itemid = $GLOBALS['itemid'];

	$item = substr($itemid, 1);

	$equip_weapon = "UPDATE map_loadouts SET weapon1 = '$item' WHERE player_id = '$player_id'";
	$equipresult = $connect->query($equip_weapon);

	handle_javascript();
}

$select_item_inventory = "SELECT * FROM map_item_inventory WHERE player_id = '$player_id'";
$itemsresult = $connect->query($select_item_inventory);

if ($itemsresult->num_rows > 0) {
    // output data of each row
    while($row = $itemsresult->fetch_assoc()) {
    	if (in_array($itemid, $row)) {
    		equip();
		} else {
			echo "<script>alert('You dont have that item');</script>";
		}
    }
}

}
else {
	die("Not set");
}
?>