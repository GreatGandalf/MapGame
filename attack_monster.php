<?php
session_start();
if( isset($_SESSION["player_id"]) and isset($_POST['skipPlayer']))
{
require 'connect.php';

$player_id = $_SESSION["player_id"];
$skipPlayer = htmlspecialchars($_POST["skipPlayer"]);

$select_battle_status = "SELECT in_battle, can_attack, monster_id FROM map_current_battle WHERE player_id = '$player_id'";

$statusresult = $connect->query($select_battle_status);

if ($statusresult->num_rows > 0) {
    // output data of each row
    while($row = $statusresult->fetch_assoc()) {
    	if($row['in_battle'] == 1 and $row['can_attack'] == 1) {
    		if($skipPlayer == 0) {
    			set_can_attack0();
    		}
    	}
    	elseif($skipPlayer == 1) {
    		monster_attacks();
    	}
    }
}
}
else {
}

function set_can_attack0() {
	$player_id = $GLOBALS['player_id'];
	$connect = $GLOBALS['connect'];

	$update_can_attack = "UPDATE map_current_battle SET can_attack = 0 WHERE player_id = '$player_id'";
	$update_can_attackresult = $connect->query($update_can_attack);

	calculate_player_attack_dmg();
}
// TODO - Base attack strength on monsters resistance, kind of weapon, elemental weapons against certain monsters
function calculate_player_attack_dmg() {
	$player_id = $GLOBALS['player_id'];
	$connect = $GLOBALS['connect'];
	$select_current_weapon = "SELECT weapon1 FROM map_loadouts WHERE player_id = '$player_id'";

	$weaponresult = $connect->query($select_current_weapon);
	if ($weaponresult->num_rows > 0) {
    	// output data of each row
    	while($row = $weaponresult->fetch_assoc()) {
    		$weaponID = $row['weapon1'];
		}
	}

	$select_weapon_dmg = "SELECT * FROM map_weapons WHERE id = '$weaponID'";

	$dmgresult = $connect->query($select_weapon_dmg);
	if ($dmgresult->num_rows > 0) {
    	// output data of each row
    	while($row = $dmgresult->fetch_assoc()) {
    		$dmg = $row['damage'];
		}
	}
	// Weapons deals between 10% less and 10% more damage, making damage a bit random
	$min = floor($dmg*0.9);
	$max = floor($dmg*1.1);
	$dmg = rand($min,$max);
	update_monster_hp($dmg);
}

function update_monster_hp($dmg) {
	$player_id = $GLOBALS['player_id'];
	$connect = $GLOBALS['connect'];
	//Here a static value of 20 is used for attack power of the player. Still need to implement items and calculating attack power based on player equipment.
	$update_monsterhp = "UPDATE map_current_battle SET monster_hp = monster_hp - $dmg WHERE player_id = '$player_id'";
	$update_monsterhpresult = $connect->query($update_monsterhp);

	monster_attacks();
}

function cal_dmg_to_player($attack) {
	$player_id = $GLOBALS['player_id'];
	$connect = $GLOBALS['connect'];

	$select_current_armor = "SELECT armor1, armor2, armor3, armor4 FROM map_loadouts WHERE player_id = '$player_id'";
	$armorresult = $connect->query($select_current_armor);

	if ($armorresult->num_rows > 0) {
    	while($row = $armorresult->fetch_assoc()) {
    		$armor1 = $row['armor1'];
    		$armor2 = $row['armor2'];
    		$armor3 = $row['armor3'];
    		$armor4 = $row['armor4'];
    	}

    	$select_armor_resistance = "SELECT SUM(resistance) AS resistance FROM map_armor WHERE id='$armor1' OR id='$armor2' OR id='$armor3' OR id='$armor1'";
    	$resistanceresult = $connect->query($select_armor_resistance);

    	if ($resistanceresult->num_rows > 0) {
    		while($row = $resistanceresult->fetch_assoc()) {
    			$resistance = $row['resistance'];
    		}

    		//echo "<script>alert('Attack: ".$attack." | Resistance: ".$resistance."')</script>";

    		// Formula for calculating damage done to player
    		$damageMultiplier = $attack / ( $attack + $resistance);
			$totalDamage = $attack * $damageMultiplier;

			//echo "<script>alert('".$totalDamage." = ".$attack." * ".$damageMultiplier."')</script>";

			return $totalDamage;
    	}
	}
}

function monster_attacks() {
	$player_id = $GLOBALS['player_id'];
	$connect = $GLOBALS['connect'];

	$select_monsterhp = "SELECT monster_hp, monster_att FROM map_current_battle WHERE player_id = '$player_id'";

	$monsterhpresult = $connect->query($select_monsterhp);

	if ($monsterhpresult->num_rows > 0) {
    	// output data of each row
    	while($row = $monsterhpresult->fetch_assoc()) {
    		$monsterhp = $row['monster_hp'];
    		if($monsterhp > 0) {
    			$monsteratt = $row['monster_att'];

    			$dmg_to_player = cal_dmg_to_player($monsteratt);

    			$update_playerhp = "UPDATE map_players SET health = health - '$dmg_to_player' WHERE id = '$player_id'";
				$update_playerhpresult = $connect->query($update_playerhp);

				select_playerhp($monsterhp);
    		}
    		else {
    			monster_defeat();
    		}
    	}
	}
}

function select_playerhp($monsterhp) {
	$player_id = $GLOBALS['player_id'];
	$connect = $GLOBALS['connect'];
	$monsterhp = $monsterhp;

	$select_playerhp = "SELECT health FROM map_players WHERE id = '$player_id'";

	$playerhpresult = $connect->query($select_playerhp);

	if ($playerhpresult->num_rows > 0) {
    	// output data of each row
    	while($row = $playerhpresult->fetch_assoc()) {
    		$playerhp = $row['health'];

    		if($playerhp > 0) {
    			$update_can_attack1 = "UPDATE map_current_battle SET can_attack = 1 WHERE player_id = '$player_id'";
				$update_can_attackresult1 = $connect->query($update_can_attack1);
    			show_actions($monsterhp, $playerhp);
    		}
    		else {
    			if($playerhp < 0) {
    				$reset_playerhp = "UPDATE map_players SET health = 0 WHERE id = '$player_id'";
					$reset_playerhpresult = $connect->query($reset_playerhp);
    			}
    			player_defeat();
    		}
    	}
	}
}

function monster_defeat() {
	$player_id = $GLOBALS['player_id'];
	$connect = $GLOBALS['connect'];
	$player_won = "player_won";

	$select_monsterid = "SELECT monster_id FROM map_current_battle WHERE player_id = '$player_id'";
	$select_monsteridresult = $connect->query($select_monsterid);
	if ($select_monsteridresult->num_rows > 0) {
    	// output data of each row
    	while($row = $select_monsteridresult->fetch_assoc()) {
    		$monster_id = $row['monster_id'];
    	}
	}

	$insert_past_battle = "INSERT INTO map_past_battles (player_id, monster_id, result) VALUES ('$player_id', '$monster_id', '$player_won');";
	$insert_past_battleresult = $connect->query($insert_past_battle);

	$select_coin_reward = "SELECT coin_reward, hppotion_reward, exp_reward FROM map_current_battle WHERE player_id = '$player_id'";
	$coinresult = $connect->query($select_coin_reward);

	if ($coinresult->num_rows > 0) {
    	// output data of each row
    	while($row = $coinresult->fetch_assoc()) {
    		$coinreward = $row['coin_reward'];
    		$hppotion_reward = "hp_potion_lvl".(string)$row['hppotion_reward'];
    		$exp_reward = $row['exp_reward'];
    		$update_inv_with_reward = "UPDATE map_general_inventory SET coins = coins + '$coinreward', `$hppotion_reward` = `$hppotion_reward` + 1 WHERE player_id = '$player_id'";
    		$rewardupdateresult = $connect->query($update_inv_with_reward);

    		$update_xp_reward = "UPDATE map_players SET exp = exp + '$exp_reward' WHERE id = '$player_id'";
    		$testresult = $connect->query($update_xp_reward);
    	}
	}

	echo "
	<script>
	MonsterHealth = 0;
	Rewarded_coins = ".$coinreward.";
	Rewarded_potion = '".$hppotion_reward."';
	Rewarded_exp = ".$exp_reward.";
	monsterDefeated();
	</script>
	";

	$set_battle_over = "UPDATE map_current_battle SET in_battle = 0, can_attack = 0, monster_id = 0, monster_hp = 0, monster_att = 0, coin_reward = 0, hppotion_reward = 0, exp_reward = 0 WHERE player_id = '$player_id'";
	$set_battle_overresult = $connect->query($set_battle_over);
}

function player_defeat() {
	$player_id = $GLOBALS['player_id'];
	$connect = $GLOBALS['connect'];
	$player_lost = "player_lost";

	$select_monsterid = "SELECT monster_id FROM map_current_battle WHERE player_id = '$player_id'";
	$select_monsteridresult = $connect->query($select_monsterid);
	if ($select_monsteridresult->num_rows > 0) {
    	// output data of each row
    	while($row = $select_monsteridresult->fetch_assoc()) {
    		$monster_id = $row['monster_id'];
    	}
	}

	$insert_past_battle = "INSERT INTO map_past_battles (player_id, monster_id, result) VALUES ('$player_id', '$monster_id', '$player_lost');";
	$insert_past_battleresult = $connect->query($insert_past_battle);

	echo "
	<script>
	PlayerHealth = 0;
	playerDefeated();
	</script>
	";

	$set_battle_over = "UPDATE map_current_battle SET in_battle = 0, can_attack = 0, monster_id = 0, monster_hp = 0, monster_att = 0, coin_reward = 0 WHERE player_id = '$player_id'";
	$set_battle_overresult = $connect->query($set_battle_over);
}

function show_actions($monsterhp, $playerhp) {
	echo "
	<script>
	PrevMonsterHealth = MonsterHealth;
	PrevPlayerHealth = PlayerHealth;
	MonsterHealth = ".$monsterhp.";
	PlayerHealth = ".$playerhp.";
	showBattleAttack();
	</script>
	";
}
?>