<?php
session_start();
require 'connect.php';
if( isset($_SESSION["player_id"]) and isset($_POST['level']) )
{

$player_id = $_SESSION["player_id"];
$level = htmlspecialchars($_POST['level']);
$lvlpotion = "hp_potion_lvl" . (string)$level;
$inbattle = 0;

if($level < 1 or $level > 10) { //if a potion level is send that doesn't exist, just do nothing.
	die("Does not exist");
}

$select_battle_status = "SELECT in_battle, can_attack, monster_id FROM map_current_battle WHERE player_id = '$player_id'";

$statusresult = $connect->query($select_battle_status);

if ($statusresult->num_rows > 0) {
    // output data of each row
    while($row = $statusresult->fetch_assoc()) {
        if($row['in_battle'] == 1 and $row['can_attack'] == 1) {
            $inbattle = 1;
            set_can_attack0();
        }
        elseif($row['in_battle'] == 0) {
            use_potion();
        }
    }
}
else {
}

}

function set_can_attack0() {
    $player_id = $GLOBALS['player_id'];
    $connect = $GLOBALS['connect'];

    $update_can_attack = "UPDATE map_current_battle SET can_attack = 0 WHERE player_id = '$player_id';";
    $update_can_attackresult = $connect->query($update_can_attack);

    use_potion();
}

function use_potion() {
$player_id = $GLOBALS['player_id'];
$connect = $GLOBALS['connect'];
$lvlpotion = $GLOBALS['lvlpotion'];
$level = $GLOBALS['level'];
$inbattle = $GLOBALS['inbattle'];


$selectAmountOfPotions = "SELECT * FROM map_general_inventory WHERE player_id = '$player_id';";

$AmountPotionsresult = $connect->query($selectAmountOfPotions); //select the amount of potions of this level the player has

if ($AmountPotionsresult->num_rows > 0) {
    // output data of each row
    while($row = $AmountPotionsresult->fetch_assoc()) {
    	if($row[''.$lvlpotion] > 0) { //if the player has at least one potion of this level

    		$subtractPotion = "UPDATE map_general_inventory SET ".$lvlpotion." = ".$lvlpotion." - 1 WHERE id = '$player_id'";

    		$subtractresult = $connect->query($subtractPotion); //remove one potion of this level from the players inventory

    		$healing = $level * 10; //the amount of healing is the level of the potion * 10

    		$selecthp = "SELECT health, max_health FROM map_players WHERE id = '$player_id'";

    		$selecthpresult = $connect->query($selecthp); //get the current amount of health and the max_health the player has

    		if ($selecthpresult->num_rows > 0) {
    		// output data of each row
    			while($row = $selecthpresult->fetch_assoc()) {
                    $max_healthy = $row['max_health'];
                    $new_health = $row['health'] + $healing;
    				if($new_health < $max_healthy) { //if healing the player would not bring their health higher than the max_health
    					$increasehp = "UPDATE map_players SET health = '$new_health' WHERE id = '$player_id'";

    					$increasehpresult = $connect->query($increasehp); //heal the player by the calculated healing

    					$currentHealth = $row['health'] + $healing; //set currentHealth to the players old health + the healing
    				}
    				else { //so if the healing would bring their health higher than the max
    					$increasehp = "UPDATE map_players SET health = max_health WHERE id = '$player_id'";

    					$increasehpresult = $connect->query($increasehp); //set the players health to their max_health

    					$currentHealth = $row['max_health']; //set currentHealth to the players max_health
    				}
    			}
			}

    		echo"<script>
    		currentHP = ".$currentHealth.";
            PlayerHealth = ".$currentHealth.";
            PlayerMaxHealth = ".$max_healthy.";
    		</script>"; //bring the current health to JS and update the side text

            if($inbattle == 1) {
                echo"<script>setTimeout(attackWithoutPlayer, 700)</script>";
            }
    	}
    	else { //so if the player has no potions of this level
    		echo"<script>alert('You have none of those')</script>";
    	}
    }
}
}
?>