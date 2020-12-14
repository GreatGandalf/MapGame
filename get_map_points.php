<?php
session_start();
if( isset( $_POST['latit'] ) and isset( $_POST['lonit'] ) )
{
require 'connect.php';

$player_id = $_SESSION["player_id"];
$latit = htmlspecialchars($_POST['latit']);
$lonit = htmlspecialchars($_POST['lonit']);

$min_grens_latit = $latit - 0.0015; //Latitude moet de helft zo veel speling hebben als longitude
$max_grens_latit = $latit + 0.0015;

$min_grens_lonit = $lonit - 0.003;
$max_grens_lonit = $lonit + 0.003;

$min_klik_grens_latit = $latit - 0.00045; //Latitude moet de helft zo veel speling hebben als longitude
$max_klik_grens_latit = $latit + 0.00045;

$min_klik_grens_lonit = $lonit - 0.0009;
$max_klik_grens_lonit = $lonit + 0.0009;

if (htmlspecialchars($_POST['endlrange']) == 1) {

    $min_grens_latit = $_POST['latit'] - 0.02; //Latitude moet de helft zo veel speling hebben als longitude
    $max_grens_latit = $_POST['latit'] + 0.02;

    $min_grens_lonit = $_POST['lonit'] - 0.04;
    $max_grens_lonit = $_POST['lonit'] + 0.04;

	$selectdata = "SELECT id, latitude, longitude, type, level FROM map_monsters WHERE latitude > $min_grens_latit AND latitude < $max_grens_latit AND longitude > $min_grens_lonit AND longitude < $max_grens_lonit"; //Speciale query voor heel ver scannen
}
else {
    $selectdata = "SELECT id, latitude, longitude, type, level FROM map_monsters WHERE latitude > $min_grens_latit AND latitude < $max_grens_latit AND longitude > $min_grens_lonit AND longitude < $max_grens_lonit";
}

$result = $connect->query($selectdata);

$monsters_fought = array();

$select_monsters_fought = "SELECT monster_id FROM map_past_battles WHERE player_id = '$player_id'";
$monstersresult = $connect->query($select_monsters_fought);

if ($monstersresult->num_rows > 0) {
    // output data of each row
    while($row = $monstersresult->fetch_assoc()) {
        array_push($monsters_fought, $row['monster_id']);
    }
}

if ($result->num_rows > 0) {
    // output data of each row
    while($row = $result->fetch_assoc()) {
        if (in_array($row['id'], $monsters_fought)) {
            //If the player has already fought this monster do nothing
        }
        else {
    	// Als de speler dichtbij genoeg is wordt een andere versie getoont met het level erbij en je kunt er op klikken
		if($row['latitude'] > $min_klik_grens_latit and $row['latitude'] < $max_klik_grens_latit and $row['longitude'] > $min_klik_grens_lonit and $row['longitude'] < $max_klik_grens_lonit or $_POST['endlrange'] == 1) {
        echo "
        <script>
        myLatLng = {lat: ".$row['latitude'].", lng: ".$row['longitude']."};
        marker".$row['id']." = new google.maps.Marker({
                position: myLatLng,
                icon: ".$row['type']."Image,
                zIndex: 100,
                label: {
    				text: '".$row['level']."',
    				color: '#9f3cd8',
    				fontSize: '16px',
    				fontWeight: 'bold',
  				}
        });
        markers.push(marker".$row['id'].");
        markerids.push(".$row['id'].");
        google.maps.event.addDomListener(marker".$row['id'].", 'click', function() {DB_battle_monster('".$row['id']."');});
        </script>";
    	}
    	else { //Als de speler niet dichtbij genoeg is wordt een beperkte versie getoont
    	echo "
        <script>
        myLatLng = {lat: ".$row['latitude'].", lng: ".$row['longitude']."};
        marker".$row['id']." = new google.maps.Marker({
                position: myLatLng,
                icon: ".$row['type']."Image,
        });
        markers.push(marker".$row['id'].");
        markerids.push(".$row['id'].");
        </script>";
    	}
        }
    }
}

//Locaties van andere spelers ophalen en als markers toevoegen

$selectOtherPlayerLocationData = "SELECT player_id, latitude, longitude FROM map_player_locations WHERE player_id != '$player_id'";

$OtherPlayersresult = $connect->query($selectOtherPlayerLocationData);

if ($OtherPlayersresult->num_rows > 0) {
    // output data of each row
    while($row = $OtherPlayersresult->fetch_assoc()) {
        echo "
        <script>
        myLatLng = {lat: ".$row['latitude'].", lng: ".$row['longitude']."};
        playermarker".$row['player_id']." = new google.maps.Marker({
                position: myLatLng,
                icon: OtherPlayerImage,
        });
        markers.push(playermarker".$row['player_id'].");
        </script>";
    }
}


//Get towers and place them
$selectTowerData = "SELECT * FROM map_towers";

$Towerresult = $connect->query($selectTowerData);

if ($Towerresult->num_rows > 0) {
    // output data of each row
    while($row = $Towerresult->fetch_assoc()) {

        $faction = $row['faction'];
        if($faction == "blue") {
            $towerImg = "bluetowerImage";
        } elseif($faction == "red") {
            $towerImg = "redtowerImage";
        } else {
            $towerImg = "towerImage";
        }

        if($row['latitude'] > $min_klik_grens_latit and $row['latitude'] < $max_klik_grens_latit and $row['longitude'] > $min_klik_grens_lonit and $row['longitude'] < $max_klik_grens_lonit or $_POST['endlrange'] == 1) {
        echo "
        <script>
        myLatLng = {lat: ".$row['latitude'].", lng: ".$row['longitude']."};
        towermarker".$row['id']." = new google.maps.Marker({
                position: myLatLng,
                icon: ".$towerImg.",
                label: {
                    text: 'Click',
                    color: '#FFFFFF',
                    fontSize: '13px',
                    fontWeight: 'bold',
                }
        });
        markers.push(towermarker".$row['id'].");
        google.maps.event.addDomListener(towermarker".$row['id'].", 'click', function() {GetTowerInfo(".$row['id'].");});
        </script>";
        }
        else {
        echo "
        <script>
        myLatLng = {lat: ".$row['latitude'].", lng: ".$row['longitude']."};
        towermarker".$row['id']." = new google.maps.Marker({
                position: myLatLng,
                icon: ".$towerImg.",
        });
        markers.push(towermarker".$row['id'].");
        </script>";
        }

    }
} else {
    echo"<script>console.log('no towers');</script>";
}

}
else {
    echo "<script>RemoveOldPoints();</script>";
}
echo "<script>scans++; document.getElementById('scans-counter').innerText = 'Scans: ' + scans;</script>";
?>