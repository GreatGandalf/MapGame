<?php
session_start();
if( isset($_SESSION["player_id"]) and isset($_POST['monsterid']) and isset($_POST['latit']) and isset($_POST['lonit']) )
{
require 'connect.php';

$player_id = $_SESSION["player_id"];
$monsterid = htmlspecialchars($_POST['monsterid']);

$min_grens_latit = htmlspecialchars($_POST['latit']) - 0.00045; //Latitude moet de helft zo veel speling hebben als longitude
$max_grens_latit = htmlspecialchars($_POST['latit']) + 0.00045;

$min_grens_lonit = htmlspecialchars($_POST['lonit']) - 0.0009;
$max_grens_lonit = htmlspecialchars($_POST['lonit']) + 0.0009;

$selectmonster = "SELECT type, level FROM map_monsters WHERE id = '$monsterid' AND latitude > $min_grens_latit AND latitude < $max_grens_latit AND longitude > $min_grens_lonit AND longitude < $max_grens_lonit";

$selectmonsterresult = $connect->query($selectmonster);

if ($selectmonsterresult->num_rows > 0) {
    // output data of each row
    while($row = $selectmonsterresult->fetch_assoc()) {
    	$type = $row['type'];
    	$level = $row['level'];

    	$get_monster_stats = "SELECT * FROM map_monster_stats WHERE type = '$type'";

    	$get_monster_statsresult = $connect->query($get_monster_stats);

    	if ($get_monster_statsresult->num_rows > 0) {
    		// output data of each row
    		while($row = $get_monster_statsresult->fetch_assoc()) {
    			$monsterhealth = $row['base_hp'] + ($level - 1) * ($row['base_hp'] * ($row['hp_incr_per_level'] / 100));
    			$monsterattack = $row['base_attack'] + ($level - 1) * ($row['base_attack'] * ($row['att_incr_per_level'] / 100));
                $coinreward = $row['base_coin_reward'] + ($level - 1) * ($row['base_coin_reward'] * ($row['coin_reward_incr'] / 100));
                $hppotion_level_reward = floor($monsterhealth / 75);
                $exp_reward = floor(($monsterhealth * $monsterattack) / 10);
                if($hppotion_level_reward > 10) {
                    $hppotion_level_reward = 10;
                }

    			$set_battle = "UPDATE map_current_battle SET in_battle = 1, can_attack = 1, monster_id = '$monsterid', monster_hp = '$monsterhealth', monster_att = '$monsterattack', coin_reward = '$coinreward', hppotion_reward = '$hppotion_level_reward', exp_reward = '$exp_reward' WHERE player_id = '$player_id'";

    			$set_battleresult = $connect->query($set_battle);

    			echo "
    			<script>
    			set_battle('".$type."', ".$level.", ".$monsterhealth.");
    			</script>
    			";
    		}
		}
    }
}
else {
	echo "<script>alert('No monster select result')</script>";
}


}
else {
	echo "<script>alert('Not everything set')</script>";
}
?>