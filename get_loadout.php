<?php
session_start();
if( isset( $_SESSION["player_id"] ) )
{
require 'connect.php';

$player_id = $_SESSION["player_id"];
$loadout = array();
echo "<script>loadout = [];</script>";

$selectloadout = "SELECT * FROM map_loadouts WHERE player_id = '$player_id'";

$result = $connect->query($selectloadout);

if ($result->num_rows > 0) {
    // output data of each row
    while($row = $result->fetch_assoc()) {
        $weapon1 = $row["weapon1"];
        $weapon2 = $row["weapon2"];
        $amor1 = $row["amor1"];
        $amor2 = $row["amor2"];
        $amor3 = $row["amor3"];
        $amor4 = $row["amor4"];
        $spell1 = $row["spell1"];
    }

    function put_in_array($thing) {
        if ($thing->num_rows > 0) {
            while($row = $thing->fetch_assoc()) {
                $js_array = json_encode($row);
                echo "
                <script>
                javascript_array = ". $js_array . ";
                loadout.push(javascript_array);
                </script>";
            }
        }
        else {
            echo "
            <script>
            javascript_array = 'empty'; 
            loadout.push(javascript_array);
            </script>";
        }
    }

    $selectweapon1 = "
    SELECT map_weapons.damage,map_weapons.id,map_weapons.level,map_weapons.material,map_weapons.name,map_weapons.type
    FROM map_weapons
    INNER JOIN map_loadouts ON map_weapons.id=map_loadouts.weapon1
    WHERE map_loadouts.player_id = '$player_id'";

    $weapon1result = $connect->query($selectweapon1);
    put_in_array($weapon1result);

    $selectweapon2 = "
    SELECT map_weapons.damage,map_weapons.id,map_weapons.level,map_weapons.material,map_weapons.name,map_weapons.type
    FROM map_weapons
    INNER JOIN map_loadouts ON map_weapons.id=map_loadouts.weapon2
    WHERE map_loadouts.player_id = '$player_id'";
    
    $weapon2result = $connect->query($selectweapon2);
    put_in_array($weapon2result);

    for ($i=1; $i < 5; $i++) {
        $armm = "map_loadouts.armor".$i;
        $selectarmor = "
        SELECT map_armor.resistance,map_armor.id,map_armor.level,map_armor.material,map_armor.name,map_armor.type
        FROM map_armor
        INNER JOIN map_loadouts ON map_armor.id=".$armm."
        WHERE map_loadouts.player_id = '$player_id'";

        $armorresult = $connect->query($selectarmor);
        put_in_array($armorresult);
    }
}
}
else {
}
?> 