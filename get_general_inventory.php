<?php
session_start();
if( isset( $_SESSION["player_id"] ) )
{
require 'connect.php';

$player_id = $_SESSION["player_id"];

$selectgeneralinv = "SELECT * FROM map_general_inventory WHERE player_id = '$player_id'";

$result = $connect->query($selectgeneralinv);

if ($result->num_rows > 0) {
    // output data of each row
    while($row = $result->fetch_assoc()) {
    	echo "
        <script>
        coins = ".$row['coins'].";
        hp_potions_lvl1 = ".$row['hp_potion_lvl1'].";
        hp_potions_lvl2 = ".$row['hp_potion_lvl2'].";
        hp_potions_lvl3 = ".$row['hp_potion_lvl3'].";
        hp_potions_lvl4 = ".$row['hp_potion_lvl4'].";
        hp_potions_lvl5 = ".$row['hp_potion_lvl5'].";
        hp_potions_lvl6 = ".$row['hp_potion_lvl6'].";
        hp_potions_lvl7 = ".$row['hp_potion_lvl7'].";
        hp_potions_lvl8 = ".$row['hp_potion_lvl8'].";
        hp_potions_lvl9 = ".$row['hp_potion_lvl9'].";
        hp_potions_lvl10 = ".$row['hp_potion_lvl10'].";
        mana_potions_lvl1 = ".$row['mana_potion_lvl1'].";
        </script>
        ";
    }
}
}
else {
}
?>