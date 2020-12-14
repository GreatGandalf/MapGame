<?php
session_start();

$logged_in = $_SESSION["logged_in"];
$username = $_SESSION["username"];
$player_id = $_SESSION["player_id"];
$exp = $_SESSION["exp"];
$level = $_SESSION["level"];
$hp = $_SESSION["health"];
$max_hp = $_SESSION["max_health"];
$faction = $_SESSION["faction"];

if (empty($logged_in)) {
  header ('Location: login.php?FROMtest_map=1&emptyLoggedIN');
  die();
} elseif (empty($username)) {
  header ('Location: login.php?FROMtest_map=1&emptyUsername');
  die();
} elseif (empty($player_id)) {
  header ('Location: login.php?FROMtest_map=1&emptyPlayer_id');
  die();
}

require 'connect.php';

$selecthp = "SELECT health, max_health FROM map_players WHERE id = '$player_id'";

$result = $connect->query($selecthp);

if ($result->num_rows > 0) {
    // output data of each row
    while($row = $result->fetch_assoc()) {
        $hp = $row['health'];
        $max_hp = $row['max_health'];
    }
}

echo "<script>PlayerFaction = '".$faction."';</script>";

?>
<!DOCTYPE html>
<html>
  <head>
    <title>Mapgame</title>
    <meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=0">
    <meta charset="utf-8">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <link rel="stylesheet" href="https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/themes/smoothness/jquery-ui.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>
    <link rel="stylesheet" type="text/css" href="./css/style.css">
    <script src="./scripts/fullscreen.js"></script>
  </head>
  <body>
    <script async defer
    src="https://maps.googleapis.com/maps/api/js?key=AIzaSyALJZuq_Fj4K-NYoFqE5tOhW4vPiY5nHr8&callback=initMap">
    </script>
    <div id="testabove" style="z-index: 999; position: fixed; background-color: white; left: 150px;"></div>
    <div id="invtest" style="z-index: 999; position: fixed; background-color: white; left: 150px; top: 50px;"></div>
    <div id="hptest" style="z-index: 999; position: fixed; background-color: white; left: 150px; top: 100px;"></div>
    <div id="potiontest" style="z-index: 999; position: fixed; background-color: white; left: 150px; top: 0px;"></div>
    <div id="battletest" style="z-index: 999; position: fixed; background-color: white; left: 150px; top: 50px;"></div>
    <div id="attacktest" style="z-index: 999; position: fixed; background-color: white; left: 150px; top: 100px;"></div>
    <div id="towertest" style="z-index: 999; position: fixed; background-color: white; left: 150px; top: 0px;"></div>
    <div id="iteminvtest" style="z-index: 999; position: fixed; background-color: white; left: 150px; top: 50px;"></div>
    <div id="loadouttest" style="z-index: 999; position: fixed; background-color: white; left: 150px; top: 100px;"></div>
    <div id="towerhelp" style="z-index: 999; position: fixed; background-color: white; left: 150px; top: 150px;"></div>
    <div id="equiphelp" style="z-index: 999; position: fixed; background-color: white; left: 150px; top: 0px;"></div>
    <div id="map"></div>
    <script>
      // Note: This example requires that you consent to location sharing when
      // prompted by your browser. If you see the error "The Geolocation service
      // failed.", it means you probably did not give permission for the browser to
      // locate you.
      var map, infoWindow;
      function initMap() {
        map = new google.maps.Map(document.getElementById('map'), {
          center: {lat: -34.397, lng: 150.644},
          zoom: 16,
          mapTypeControl: false,
          scrollwheel: false,
          navigationControl: false,
          scaleControl: false,
          draggable: false,
          zoomControl: false,
          streetViewControl: false
        });
        infoWindow = new google.maps.InfoWindow;
        
        var styles = {
        default: null,
        hide: [
  {
    "elementType": "labels",
    "stylers": [
      {
        "visibility": "off"
      }
    ]
  },
  {
    "featureType": "administrative.land_parcel",
    "stylers": [
      {
        "visibility": "off"
      }
    ]
  },
  {
    "featureType": "administrative.neighborhood",
    "stylers": [
      {
        "visibility": "off"
      }
    ]
  }
]
      };
      map.setOptions({styles: styles['hide']});
    

      BlueUserImage = {
        url: "./img/pixel_knight_blue.png", // url
        scaledSize: new google.maps.Size(34, 44), // scaled size
      };

      RedUserImage = {
        url: "./img/pixel_knight_red.png", // url
        scaledSize: new google.maps.Size(34, 44), // scaled size
      };

      OtherPlayerImage = {
        url: "./img/pixel_knight_grey.png", // url
        scaledSize: new google.maps.Size(17, 22), // scaled size
      };

      towerImage = {
        url: "./img/tower.png", // url
        scaledSize: new google.maps.Size(50, 50), // scaled size
      };

      bluetowerImage = {
        url: "./img/bluetower.png", // url
        scaledSize: new google.maps.Size(50, 50), // scaled size
      };

      redtowerImage = {
        url: "./img/redtower.png", // url
        scaledSize: new google.maps.Size(50, 50), // scaled size
      };

      batImage = {
        url: "./img/bat.gif", // url
        scaledSize: new google.maps.Size(42, 42), // scaled size
        labelOrigin: new google.maps.Point(20, 7)
      };

      slimeImage = {
        url: "./img/slime.png", // url
        scaledSize: new google.maps.Size(28, 28), // scaled size
      };

      dragonImage = {
        url: "./img/dragon.png", // url
        scaledSize: new google.maps.Size(42, 44), // scaled size
        labelOrigin: new google.maps.Point(20, -4)
      };

      blackdragonImage = {
        url: "./img/blackdragon.png", // url
        scaledSize: new google.maps.Size(50, 50), // scaled size
      };
    }


        pos = {
            lat: 0,
            lng: 0
        };

        markers = [];
        markerids = [];
        markerids2 = [];

        scans = 0;

        isInBattle = false;
        wasInBattle = false;
        getPointsRunning = 0;

        var watchID;
        var geoLoc;
         
        function showLocation(position) {
          pos = {
            lat: position.coords.latitude,
            lng: position.coords.longitude
          };
          window.latitude = position.coords.latitude;
          window.longitude = position.coords.longitude;
          console.log("Latitude : " + latitude + " Longitude: " + longitude);
        }

        function errorHandler(err) {
          if(err.code == 1) {
             alert("Error: Access is denied!");
          } else if( err.code == 2) {
             alert("Error: Position is unavailable!");
          }
        }

        function getLocationUpdate(){
            
          if(navigator.geolocation){
             
             // timeout at 60000 milliseconds (60 seconds)
             var options = {maximumAge:0, timeout:5000, enableHighAccuracy: true};
             geoLoc = navigator.geolocation;
             watchID = geoLoc.watchPosition(showLocation, errorHandler, options);
          } else {
             alert("Sorry, browser does not support geolocation!");
          }
        }

        // Try HTML5 geolocation.
        function SetPlayerLocation() {
          if(isInBattle === false){
            map.setCenter(pos);

            if(typeof marker === 'undefined') {
              if(PlayerFaction === "blue") {
                marker = new google.maps.Marker({
                    position: pos,
                    icon: BlueUserImage,
                });
              } else if(PlayerFaction === "red") {
                marker = new google.maps.Marker({
                    position: pos,
                    icon: RedUserImage,
                });
              }

                marker.setMap(map);
                console.log("marker made");
            }
            marker.setPosition(pos);

        }
        else {
          wasInBattle = true;
        }
        setTimeout(SetPlayerLocation, 500);
        }

        function GetPoints() {
          if(isInBattle === false) {
            getPointsRunning = 1;
            if ($("#endless").is(":checked")) {
                endlessrange = 1;
            }
            else {
                endlessrange = 0;
            }
            for (var i = 0; i < markers.length; i++) {
              markers[i].setTitle("delete");
            }
        $.ajax({
                type: 'post',
                url: 'get_map_points.php',
                data: {
                    latit:window.latitude,
                    lonit:window.longitude,
                    endlrange:endlessrange,
                },
                success: function (data) {
                    HandlePoints(data);
                }
            });

        }
        }

        function HandlePoints(resp) {
            $( '#testabove' ).html(resp);
            for (var i = 0; i < markers.length; i++) {
              if(markers[i].getTitle() != "delete") {
                markers[i].setMap(map);
              }
              if(i == markers.length - 1) {setTimeout(RemoveOldPoints, 500);}
            }
        }

        function RemoveOldPoints() {
          for (var i = 0; i < markers.length; i++) {
              if(markers[i].getTitle() == "delete") {
                markers[i].setMap(null);
                markers[i] = null;
                markers.splice(i, 1);
              }
            }
            setTimeout(GetPoints, 500);
        }

        function updatePlayerLocationInDB() {
          $.ajax({
                type: 'post',
                url: 'update_player_location.php',
                data: {
                    latit:window.latitude,
                    lonit:window.longitude,
                },
                success: function (data) {
                  setTimeout(updatePlayerLocationInDB, 500);
                }
            });
        }

        $(document).ready(function(){
        getLocationUpdate();
        setTimeout(SetPlayerLocation, 2000);
        setTimeout(GetPoints, 2000);
        setTimeout(updatePlayerLocationInDB, 2000);
        });

      function handleLocationError(browserHasGeolocation) {
        if(browserHasGeolocation==false) {

        }

      }

    </script>
    <div id="battle_shadow"></div>
    <div id="battle_box">
      <div id="monster_image"></div>
      <div id="player_image"><img src="./img/knight_grey_back.png"></div>
      <div id="dmg_box"></div>
      <p>Type: <span id="monster_name"></span></p>
      <p>Level: <span id="monster_level"></span></p>
      <p>Monster HP:</p>
      <div id="MonsterHpBarOuter">
        <div id="MonsterHpBarInner"></div>
      </div>
      <p id="monster_hp"></p>
      <p>Player HP:</p>
      <div id="PlayerHpBarOuter">
        <div id="PlayerHpBarInner"></div>
      </div>
      <div id="dmg_box2"></div>
      <p id="player_hp"></p>
      <div id="attackButton" onclick="DB_battleAttack()"><p>Attack!</p></div>
      <div id="useItemButton" onclick="useItem()"><p>Use item</p></div>
      <div id="runAwayButton" onclick="hideBattle()"><p>Run away</p></div>
    </div>

    <div id="monster_defeated">
      <center>
      <p id="victory">Victory</p>
      <p id="coinreward">Coins: <span id="coin_amount"></span></p>
      <p id="potionreward">Potion: <span id="potion_level"></span></p>
      <p id="expreward">Exp: <span id="exp_amount"></span></p>
      </center>
    </div>
    <div id="player_defeated">
      <center>
      <p id="victory">Defeat</p>
      </center>
    </div>

    <div id="tower_shadow" onclick="hideTower()"></div>
    <div id="tower_box">
      <p>Name: <span id="tower_name"></span></p>
      <p>Faction: <span id="tower_faction"></span></p>
      <p>Strength: <span id="tower_strength"></span></p>
      <div id="attack_tower" style="display: none;"><p>Attack this tower</p></div>
      <br><br>
      <input type="number" name="shabd" id="HelpAmount">
      <br>
      <div id="give_tower_strength" style="display: none;" onclick="HelpTower()"><p>Help this tower</p></div>
    </div>

    <script>
      function getPlayerHealth() {
        $.ajax({
            type: 'post',
            url: 'get_player_health.php',
            data: {
            },
            success: function (data) {
                $( '#hptest' ).html(data);
                showBattle();
            }
        });
      }

      function useItem() {
        if(canAttack === true) {
          $("#items_container").empty();
          GetGeneralInv();
        }
      }

      function getImage(type) {
        if(type == "slime") {
        return "./img/slime.png";
        } else if(type == "dragon") {
          return "./img/dragon.png";
        } else if(type == "blackdragon") {
          return "./img/blackdragon.png";
        } else if(type == "bat") {
          return "./img/bat.gif";
        }
      }

      tombstoneImage = "http://pixelartmaker.com/art/c916ac6a2625651.png";

      //Script for battle system on click
      function DB_battle_monster(id) {
        $("#battle_shadow").fadeIn(500);
        $.ajax({
            type: 'post',
            url: 'battle_monster.php',
            data: {
              monsterid:id,
              latit:window.latitude,
              lonit:window.longitude,
            },
            success: function (data) {
              $('#battletest').html(data);
            }
        });
      }

      function set_battle(type, level, monsterhp) {
        isInBattle = true;
        $("#monster_name").html(type);
        $("#monster_level").html(level);
        ImageUrl = type + "ImageUrl";
        $("#monster_image").html("<img src=\""+getImage(type)+"\"/>");
        healthString = monsterhp.toString();
        $("#monster_hp").html(healthString+" / "+healthString);
        MonsterType = type;
        MonsterLevel = level;
        MonsterHealth = monsterhp;
        MonsterMaxHealth = monsterhp;
        getPlayerHealth();
      }

      function showBattle() {
        $("#battle_shadow").fadeOut(500);
        $("#dmg_box").hide()
        $("#player_hp").html(PlayerHealth.toString()+" / "+PlayerMaxHealth.toString());
        $("#MonsterHpBarInner").css("width", "100%");
        var percentagePlayerHealth = (PlayerHealth / PlayerMaxHealth) * 100;
        $("#PlayerHpBarInner").css("width", percentagePlayerHealth.toString()+"%");
        $("#battle_box").css("display", "block");
        canAttack = true;
        $("#attackButton").css("background-color", "red");
        $("#useItemButton").css("background-color", "lightblue")
        $("#runAwayButton").css("background-color", "#3C4A59");
      }

      function hideBattle() {
        $.ajax({
            type: 'post',
            url: 'run_from_battle.php',
            data: {
              test:"testy"
            },
            success: function (data) {
              $('#attacktest').html(data);
            }
        });
        $("#battle_shadow").fadeIn(500);
        $("#battle_shadow").css("display", "none");
        $("#battle_box").css("display", "none");
        isInBattle = false;
        GetPoints();
      }

      function DB_battleAttack() {
        //canAttack = false;
        $("#attackButton").css("background-color", "grey");
        $("#useItemButton").css("background-color", "grey");
        $("#runAwayButton").css("background-color", "grey");
        $.ajax({
            type: 'post',
            url: 'attack_monster.php',
            data: {
              skipPlayer:0
            },
            success: function (data) {
              $('#attacktest').html(data);
            }
        });
      }

      function attackWithoutPlayer() {
        $.ajax({
            type: 'post',
            url: 'attack_monster.php',
            data: {
              skipPlayer:1
            },
            success: function (data) {
              $('#attacktest').html(data);
            }
        });
      }

      function showBattleAttack() {
          damage = PrevMonsterHealth - MonsterHealth;
          $('#dmg_box').html("<p>"+damage+"</p>");
          $("#dmg_box").fadeIn(200);
          $("#dmg_box").delay(700).fadeOut(500);
          if(MonsterHealth < 0) {MonsterHealth = 0;}
          $("#monster_hp").html(MonsterHealth.toString()+" / "+MonsterMaxHealth.toString());
          var percentageMonsterHealth = (MonsterHealth / MonsterMaxHealth) * 100;
          $("#MonsterHpBarInner").animate({width: percentageMonsterHealth.toString()+"%"}, 600, 'easeInOutQuint');
          setTimeout(showMonsterAttack, 500);
      }

      function showMonsterAttack() {
          $("#monster_image").effect( "shake", {times:3}, 400 );
          showPlayerHealthChange();
      }

      function monsterDefeated() {
          $("#monster_hp").html(MonsterHealth.toString()+" / "+MonsterMaxHealth.toString());
          var percentageMonsterHealth = (MonsterHealth / MonsterMaxHealth) * 100;
          $("#MonsterHpBarInner").animate({width: percentageMonsterHealth.toString()+"%"}, 600, 'easeInOutQuint');
          $("#monster_image").html("<img src=\""+tombstoneImage+"\"/>");
          $("#coin_amount").html(Rewarded_coins);
          $("#potion_level").html(Rewarded_potion);
          $("#exp_amount").html(Rewarded_exp);
          setTimeout(showVictory, 2000);
      }

      function removeBattle() {
        $("#battle_shadow").css("display", "none");
        $("#battle_box").css("display", "none");
      }

      function playerDefeated() {
        $("#side_hp").html("Health: "+PlayerHealth.toString()+" / "+PlayerMaxHealth.toString());
        $("#player_hp").html(PlayerHealth.toString()+" / "+PlayerMaxHealth.toString());
        $("#PlayerHpBarInner").animate({width: "0%"}, 600, 'easeInOutQuint');
        $("#player_defeated").delay(1200).fadeIn(500);
        $("#player_defeated").delay(4000).fadeOut(1000);
        removeBattle();
        isInBattle = false;
        GetPoints();
      }

      function partC() {
          canAttack = true;
          $("#attackButton").css("background-color", "red");
          $("#useItemButton").css("background-color", "lightblue")
      }

      function showPlayerHealthChange() {
          damage = PrevPlayerHealth - PlayerHealth;
          $('#dmg_box2').html("<p>"+damage+"</p>");
          $("#dmg_box2").fadeIn(200);
          $("#dmg_box2").delay(700).fadeOut(500);
          if(PlayerHealth < 0) {PlayerHealth = 0;}
          $("#side_hp").html("Health: "+PlayerHealth.toString()+" / "+PlayerMaxHealth.toString());
          $("#player_hp").html(PlayerHealth.toString()+" / "+PlayerMaxHealth.toString());
          var percentagePlayerHealth = (PlayerHealth / PlayerMaxHealth) * 100;
          $("#PlayerHpBarInner").animate({width: percentagePlayerHealth.toString()+"%"}, 600, 'easeInOutQuint');
          setTimeout(partC, 500);
      }

      function showVictory() {
        $("#monster_defeated").fadeIn(500);
        $("#monster_defeated").delay(4000).fadeOut(1000);
        removeBattle();
        isInBattle = false;
        GetPoints();
      }
    </script>

    <script type="text/javascript">function fullscreen() {$("#fullscreen").toggle(); openFullscreen();}</script>
    <!-- <div id="fullscreen" onclick="fullscreen()"></div> -->

    <p id="hide_options" style="position: fixed; background-color: white; top: -10px; left: 0px;"><b>Hide</b></p>
    <script type="text/javascript">
        $( "#hide_options" ).click(function() {
            $("#options").toggle();
            $("#userdata").toggle();
        });
    </script>
    <div id="options" style="z-index: 8; position: fixed; background-color: white; top: 30px; left: 0px; min-width: 100px;">
      <p>Heel ver scannen <input type="checkbox" id="endless"/></p>
        <p id='seconds-counter'> </p>
        <p id='scans-counter'> </p>
        <a href="logout.php"><p>Logout</p></a>
            <script type="text/javascript">
        var seconds = 0;
var el = document.getElementById('seconds-counter');

function incrementSeconds() {
    seconds += 1;
    el.innerText = "Seconden: " + seconds;
}

var cancel = setInterval(incrementSeconds, 1000);
    </script>
    </div>
    <div id="userdata">
      <?php
      echo "<p>Username: " . $username . "</p>";
      echo "<p>Player_id: " . $player_id . "</p>";
      echo "<p>Exp: " . $exp . "</p>";
      echo "<p>Level: " . $level . "</p>";
      echo "<p id=\"side_hp\">Health: " . $hp . " / " . $max_hp . "</p>";
      echo "<p>Faction: " . $faction . "</p>";
      ?>
    </div>
    <div id="see_inventory" style="z-index: 8; position: fixed; background-color: white; bottom: 0px; left: 0px; min-width: 100px;">
      <p>See inventory</p>
    </div>
    <div id="see_loadout" style="z-index: 8; position: fixed; background-color: white; bottom: 0px; right: 0px; min-width: 100px;">
      <p>See loadout</p>
    </div>
    <div id="inventory">
      <div id="inventory_coins"><p id="coins_amount"></p></div>
      <div id="inventory_close"><p>X</p></div>
      <center>
      <div id="potions_container"></div>
      <div id="items_container"></div>
      </center>
    </div>
    <div id="loadout">
      <div id="loadout_close"><p>X</p></div>
      <div id="loadout_container">
      </div>
    </div>
    <div id="itemDetails">
      <div id="itemDetails_close"><p>X</p></div>
      <div id="itemDetails_container_background"></div>
      <div id="itemDetails_container"></div>
    </div>
    <div id="itemCompare">
      <div id="item1">
      </div>
      <div id="item2">
      </div>
      <div id="cancel" onclick="hideCompare()"><p>Cancel</p></div>
      <div id="equip"><p>Equip</p></div>
    </div>
    <script type="text/javascript">
      function seeItemDetails(itemID) {
        $("#itemDetails_container").empty();
        for (i = 0; i < items.length; i++) {
          if(items[i]["id"] == itemID) {
            name = items[i]["name"];
            type = items[i]["type"];
            mater = items[i]["material"];
            img = "./img/"+type+"_"+mater+".png";
            $("#itemDetails_container").append("<p class=\"name\">"+name+"</p><img src=\""+img+"\"><p>Type: "+type+"</p><p>Material: "+mater+"</p>");
            if(itemID[0] == "w") {
              damage = items[i]["damage"];
              $("#itemDetails_container").append("<p>Damage: "+damage+"</p>");

              if(loadout[0]["id"] == itemID[1] || loadout[1]["id"] == itemID[1]) {
                $("#itemDetails_container").append("<div id=\"equiped\"><p>Equiped</p></div>");
              }
              else {
                $("#itemDetails_container").append("<div id=\"equipButton\" onclick=\"compareItem(items["+i+"],'d')\"><p>Equip</p></div>");
              }
            }
            else if(itemID[0] == "a") {
              resistance = items[i]["resistance"];
              $("#itemDetails_container").append("<p>Resistance: "+resistance+"</p>");

              if(loadout[2]["id"] == itemID[1] || loadout[3]["id"] == itemID[1] || loadout[4]["id"] == itemID[1] || loadout[5]["id"] == itemID[1]) {
                $("#itemDetails_container").append("<div id=\"equiped\"><p>Equiped</p></div>");
              }
              else {
                $("#itemDetails_container").append("<div id=\"equipButton\" onclick=\"compareItem(items["+i+"],'r')\"><p>Equip</p></div>");
              }
            }

            $("#itemDetails").toggle();
          }
        }
      }

      $( "#itemDetails_close" ).click(function() {
        $("#itemDetails").toggle();
      });

      function GetGeneralInv() {
        $.ajax({
                type: 'post',
                url: 'get_general_inventory.php',
                data: {
                    test:'testy'
                },
                success: function (data) {
                    $('#invtest').html(data);

                    $('#coins_amount').html(coins);
                    $("#potions_container").empty();
                    for (i = 1; i < 11; i++) {
                      potion = window["hp_potions_lvl"+i.toString()];
                      if(potion > 0) {
                        $("#potions_container").append("<div id=\"inventory_box\" onclick=\"useHpPotion("+i+")\"><p id=\"box_title\">Lvl "+i+"</p><img src=\"img/hp_potion.png\" id=\"box_img\"/><p id=\"box_amount\">"+potion+"</p>");
                      }
                    }

                    $("#inventory").toggle();
                }
            });

        }

        function GetItemInv() {
          $.ajax({
                type: 'post',
                url: 'get_loadout.php',
                data: {
                    test:'testy'
                },
                success: function (data) {
                  $('#loadouttest').html(data);
                }
          });
          $.ajax({
                type: 'post',
                url: 'get_item_inventory.php',
                data: {
                    test:'testy'
                },
                success: function (data) {
                    $("#items_container").empty();
                    $('#iteminvtest').html(data);
                    for (i = 0; i < items.length; i++) {
                      itemid = items[i]["id"];
                      level = items[i]["level"];
                      name = items[i]["name"];
                      if(name.length > 14) {
                        name = name.substr(0, 13) + "..";
                      }
                      type = items[i]["type"];
                      mater = items[i]["material"];
                      img = "./img/"+type+"_"+mater+".png";
                      $("#items_container").append("<div id=\"inventory_item_box\" onclick=\"seeItemDetails('"+itemid+"')\"><p>"+name+"</p><p>Level: "+level+"</p><img src=\""+img+"\"></div>");
                    }
                }
            });
        }

        $( "#see_inventory" ).click(function() {
            GetItemInv();;
            GetGeneralInv();
        });

        $( "#inventory_close" ).click(function() {
            $("#inventory").toggle();
        });

        function GetLoadout() {
          $.ajax({
                type: 'post',
                url: 'get_loadout.php',
                data: {
                    test:'testy'
                },
                success: function (data) {
                    $("#loadout_container").empty();
                    $('#loadouttest').html(data);
                    for (i = 0; i < loadout.length; i++) {
                      if(loadout[i] == "empty") {
                        img = "./img/"+i+"_empty.png";
                        $("#loadout_container").append("<div id=\"loadout_box\"><p>None</p><img src=\""+img+"\"></div>");
                      }
                      else {
                        level = loadout[i]["level"];
                        name = loadout[i]["name"];
                        type = loadout[i]["type"];
                        mater = loadout[i]["material"];
                        img = "./img/"+type+"_"+mater+".png";
                        $("#loadout_container").append("<div id=\"loadout_box\"><p>"+name+"</p><p>Level: "+level+"</p><img src=\""+img+"\"></div>");
                      }
                    }
                    $("#loadout").toggle();
                }
            });
        }

        $( "#see_loadout" ).click(function() {
            GetLoadout();
        });

        $( "#loadout_close" ).click(function() {
            $("#loadout").toggle();
        });

      function useHpPotion(lvl) {
        if(isInBattle === true) {
          canAttack = false;
          $("#attackButton").css("background-color", "grey");
          $("#useItemButton").css("background-color", "grey");
        }
        $('#inventory').toggle();
        $.ajax({
                type: 'post',
                url: 'use_hp_potion.php',
                data: {
                    level:lvl
                },
                success: function (data) {
                  $('#potiontest').html(data);
                  $('#side_hp').html('Health: '+currentHP.toString()+' / '+PlayerMaxHealth.toString());
                  $("#player_hp").html(currentHP.toString()+" / "+PlayerMaxHealth.toString());
                  var percentagePlayerHealth = (currentHP / PlayerMaxHealth) * 100;
                  $("#PlayerHpBarInner").animate({width: percentagePlayerHealth.toString()+"%"}, 600, 'easeInOutQuint');
                }
            });
      }

      function GetTowerInfo(id) {
        $.ajax({
                type: 'post',
                url: 'get_tower_info.php',
                data: {
                    towerid:id
                },
                success: function (data) {
                  $('#towertest').html(data);
                }
            });
      }

      function HelpTower() {
        aamount = $("#HelpAmount").val();
        $.ajax({
                type: 'post',
                url: 'help_tower.php',
                data: {
                    towerid:towerID,
                    amount:aamount
                },
                success: function (data) {
                  $('#towerhelp').html(data);
                }
        });
      }

      function showTowerInfo(towerFriendly) {
        $("#tower_name").html(towerName);
        $("#tower_faction").html(towerFaction);
        $("#tower_strength").html(towerStrength);

        $("#tower_shadow").css("display", "block");
        $("#tower_box").css("display", "block");

        if(towerFriendly == true) {
          $("#give_tower_strength").css("display", "block");
        }
        else {
          $("#attack_tower").css("display", "block");
        }

      }

      function hideTower() {
        $("#tower_shadow").css("display", "none");
        $("#tower_box").css("display", "none");
        $("#attack_tower").css("display", "none");
        $("#give_tower_strength").css("display", "none");
        $("#HelpAmount").val("");
      }

      function compareItem(item,kind) {
        $("#item1").empty();
        $("#item2").empty();

        name = item["name"];
        type = item["type"];
        mater = item["material"];
        img = "./img/"+type+"_"+mater+".png";
        if(kind == "d") {
          amount = item["damage"];
          text = "damage";

          currentItem = loadout[0];
          name2 = currentItem["name"];
          type2 = currentItem["type"];
          mater2 = currentItem["material"];
          amount2 = currentItem["damage"];
          img2 = "./img/"+type2+"_"+mater2+".png";

          $('#equip').attr('onClick', "equipWeapon(\""+item["id"]+"\")");

        } else if(kind == "r") {
          amount = item["resistance"];
          text = "resistance";

          switch(type) {
            case "helmet":
              currentItem = loadout[2];
              place = 2;
              break;
            case "chest":
              currentItem = loadout[3];
              place = 3;
              break;
            case "pants":
              currentItem = loadout[4];
              place = 4;
              break;
            case "boots":
              currentItem = loadout[5];
              place = 5;
              break;
          }

          name2 = currentItem["name"];
          type2 = currentItem["type"];
          mater2 = currentItem["material"];
          amount2 = currentItem["resistance"];
          img2 = "./img/"+type2+"_"+mater2+".png";

          $('#equip').attr('onClick', "equipArmor(\""+item["id"]+"\")");
        }

        $("#item1").append("<img id=\"itemImage\" src=\""+img2+"\" />");
        $("#item1").append("<p>"+amount2+"</p>");
        $("#item1").append("<p>"+mater2+"</p>");

        $("#item2").append("<img id=\"itemImage\" src=\""+img+"\" />");
        $("#item2").append("<p>"+amount+"</p>");
        $("#item2").append("<p>"+mater+"</p>");

        $("#itemCompare").toggle();
      }

      function hideCompare() {
        $("#itemCompare").toggle();
        $("#item1").empty();
        $("#item2").empty();
      }

      function equipWeapon(item) {
        alert(item);
        $.ajax({
                type: 'post',
                url: 'equip_weapon.php',
                data: {
                    itemid:item
                },
                success: function (data) {
                  $('#equiphelp').html(data);
                }
        });
      }

      function equipArmor(item) {
        alert(item);
        $.ajax({
                type: 'post',
                url: 'equip_armor.php',
                data: {
                    itemid:item,
                },
                success: function (data) {
                  $('#equiphelp').html(data);
                }
        });
      }

    </script>
  </body>
</html>