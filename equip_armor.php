<?php
session_start();
if( isset($_SESSION["player_id"]) and isset($_POST['itemid']))
{
require 'connect.php';

$player_id = $_SESSION["player_id"];
$itemid = htmlspecialchars($_POST['itemid']);

if($itemid[0] !== "a") {
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

function equip($spot) {
	$player_id = $GLOBALS['player_id'];
	$connect = $GLOBALS['connect'];
	$itemid = $GLOBALS['itemid'];

	$item = substr($itemid, 1);
	$place = "armor".(string)$spot;

	$equip_weapon = "UPDATE map_loadouts SET ".$place." = '$item' WHERE player_id = '$player_id'";
	$equipresult = $connect->query($equip_weapon);

	handle_javascript();
}

function get_type() {
	$player_id = $GLOBALS['player_id'];
	$connect = $GLOBALS['connect'];
	$itemid = $GLOBALS['itemid'];

	$item = substr($itemid, 1);

	$select_type = "SELECT type FROM map_armor WHERE id = '$item'";
	$typeresult = $connect->query($select_type);

	if ($typeresult->num_rows > 0) {
    	while($row = $typeresult->fetch_assoc()) {
    		$type = $row['type'];
    		if($type==="helmet") {
    			equip(1);
    		} elseif ($type==="chest") {
    			equip(2);
    		} elseif ($type==="pants") {
    			equip(3);
    		} elseif ($type==="boots") {
    			equip(4);
    		} else {
    			die("wrong type");
    		}
    	}
	}
}

$select_item_inventory = "SELECT * FROM map_item_inventory WHERE player_id = '$player_id'";
$itemsresult = $connect->query($select_item_inventory);

if ($itemsresult->num_rows > 0) {
    // output data of each row
    while($row = $itemsresult->fetch_assoc()) {
    	if (in_array($itemid, $row)) {
    		get_type();
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