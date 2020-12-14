<?php
session_start();
if( isset( $_SESSION["player_id"] ) )
{
require 'connect.php';

$player_id = $_SESSION["player_id"];
$items = array();
echo "<script>items = [];</script>";

$selectgeneralinv = "SELECT * FROM map_item_inventory WHERE player_id = '$player_id'";

$result = $connect->query($selectgeneralinv);

if ($result->num_rows > 0) {
    // output data of each row
    while($row = $result->fetch_assoc()) {
        for ($i=1; $i < 21; $i++) { 
            $slot = "slot".(string)$i;
            if(!empty($row[$slot])) {
                array_push($items,$row[$slot]);
            }
        }
    }

    foreach ($items as $itemID) {
        if($itemID[0] == "w") {
            $IDitem = $itemID[1];
            $selectweapon = "SELECT damage,id,level,material,name,type FROM map_weapons WHERE id = '$IDitem'";

            $weaponresult = $connect->query($selectweapon);
            while($row = $weaponresult->fetch_assoc()) {
                $row["id"] = "w".(string)$row["id"];
                $js_array = json_encode($row);
                echo "<script>
                javascript_array = ". $js_array . ";
                items.push(javascript_array);
                </script>";
            }
        }
        if($itemID[0] == "a") {
            $IDitem = $itemID[1];
            $selectarmor = "SELECT resistance,id,level,material,name,type FROM map_armor WHERE id = '$IDitem'";

            $armorresult = $connect->query($selectarmor);
            while($row = $armorresult->fetch_assoc()) {
                $row["id"] = "a".(string)$row["id"];
                $js_array = json_encode($row);
                echo "<script>
                javascript_array = ". $js_array . ";
                items.push(javascript_array);
                </script>";
            }
        }
    }
}
}
else {
}
?> 