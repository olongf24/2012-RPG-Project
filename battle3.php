<!DOCTYPE html>
<html>
<head>
<?PHP include 'layout_if.php'; ?>
<style class="text/css">

input.attack {
color:yellow;
background-color:#330000;
border:3px solid orange;
text-decoration:bold;
text-align:center;
}

input.search {
color:black;
background-color:white;
border:3px solid #330000;
}

table.yourdisplay {
width:300px;
height:110px;
margin-left:10px;
margin-right:100px;
text-align:right;
font-family:Tahoma;
font-size:12px;
color:white;
background-color:#330000;
}
table.yourdisplay th {
height:10px;
text-align:center;
font-family:Tahoma;
font-size:14px;
color:white;
background-color:black;
}

table.foedisplay {
height:110px;
width:300px;
top:100px;
margin-left:320px;
text-align:left;
font-size:12px;
font-family:Tahoma;
color:white;
background-color:#330000;
}
table.foedisplay th {
height:10px;
text-align:center;
font-family:Tahoma;
font-size:14px;
color:white;
background-color:black;
}

div.floatleft {
float:left;
}
div.floatright {
float:right;
}

div.baserpgcontainer {
height:1000px;
}


</style>



<?PHP
db_connect($db_handle);
?>

<div class="baserpgcontainer">
<h1><align=center>Battle</align></h1>

<?PHP

if (isset($_GET['ID'])) {

$_SESSION['opponent_ID'] = $_GET['ID'];
$check = $_GET['ID'];


$stmt = $db_handle->prepare("SELECT * FROM userbox WHERE User_ID=:uID AND Roster=1");
$stmt->bindValue("uID", $check);
$stmt->execute();
$db_field = $stmt->fetch();

if (!isset($db_field['User_ID'])) {
echo "<p>Sorry, this user doesn't exist yet.</p>";
return;
}
if (!isset($_GET['ID'])) {
echo "<p>We found nothing in the textbox.</p>";
return;
}
$_SESSION['opponent_ID'] = $_GET['ID'];

}

if (!isset($_POST['Attack'])) {
if (!isset($_SESSION['MoveCount'])) {
$string1 = "<p>The battle has started.</p>";
}

}


if (isset($_POST['Attack'])) {

//---Getting the name of attack.
$atk_name_you = $_POST['SelectAttack'];

if ($atk_name_you == 'move1') {
$atk_name_you = $_SESSION['Move1'];
}
elseif ($atk_name_you == 'move2') {
$atk_name_you = $_SESSION['Move2'];
}
elseif ($atk_name_you == 'move3') {
$atk_name_you = $_SESSION['Move3'];
}
else {
$atk_name_you = $_SESSION['Move4'];
}


$atk_rand = rand(1,4);

switch ($atk_rand) {
case 1:
$atk_name_opponent = 'Tackle';
case 2:
$atk_name_opponent = 'Tackle';
case 3:
$atk_name_opponent = 'Tackle';
case 4:
$atk_name_opponent = 'Tackle';
}

//---Searching database for attack info.
$stmt = $db_handle->prepare("SELECT * FROM `moves` WHERE `Name`=:atk");
$stmt->bindValue("atk", $atk_name_you);
$stmt->setFetchMode(PDO::FETCH_ASSOC);
$stmt->execute();
$db_field = $stmt->fetch();

//---Getting permanent variables.
$basepower_you = $db_field['BasePower'];
$atk_type_you = $db_field['Type'];
$critical_you = ($db_field['CriticalHit'] * 0.18) * 100;
$critical_you = round($critical_you, 0, PHP_ROUND_HALF_DOWN);
$category_you = $db_field['Category'];
$level_you = $_SESSION['level_you'];
$level_opponent = $_SESSION['level_opponent'];

$pkmn_you = $_SESSION['pkmn_you'];
$pkmn_opponent = $_SESSION['pkmn_opponent'];

//---Getting stats of your Pokemon.
$stmt = $db_handle->prepare("SELECT Atk,Def,SpAtk,SpDef,Speed,Type1,Type2 FROM `basestats` WHERE Species=:species");
$stmt->bindValue("species", $pkmn_you);
$stmt->execute();
$db_field = $stmt->fetch();

$atk_you = ($level_you / 40) * $db_field['Atk'];
$atk_you = round($atk_you, 0, PHP_ROUND_HALF_DOWN);
$def_you = ($level_you / 40) * $db_field['Def'];
$def_you = round($def_you, 0, PHP_ROUND_HALF_DOWN);
$spatk_you = ($level_you / 40) * $db_field['SpAtk'];
$spatk_you = round($spatk_you, 0, PHP_ROUND_HALF_DOWN);
$spdef_you = ($level_you / 40) * $db_field['SpDef'];
$spdef_you = round($spdef_you, 0, PHP_ROUND_HALF_DOWN);
$speed_you = ($level_you / 40) * $db_field['Speed'];
$speed_you = round($speed_you, 0, PHP_ROUND_HALF_DOWN);
$type1_you = $db_field['Type1'];
$type2_you = $db_field['Type2'];

//---Getting stats of opponent Pokemon.
$stmt = $db_handle->prepare("SELECT Atk,Def,SpAtk,SpDef,Speed,Type1,Type2,HP FROM `basestats` WHERE Species=:species");
$stmt->bindValue("species", $pkmn_opponent);
$stmt->execute();
$db_field = $stmt->fetch();


$hp_opponent = ($level_opponent / 40) * $db_field['HP'] * 2;
$hp_opponent = round($hp_opponent, 0, PHP_ROUND_HALF_DOWN);
$totalhp_opponent = $hp_opponent;
$atk_opponent = ($level_opponent / 40) * $db_field['Atk'];
$atk_opponent = round($atk_opponent, 0, PHP_ROUND_HALF_DOWN);
$def_opponent = ($level_opponent / 40) * $db_field['Def'];
$def_opponent = round($def_opponent, 0, PHP_ROUND_HALF_DOWN);
$spatk_opponent = ($level_opponent / 40) * $db_field['SpAtk'];
$spatk_opponent = round($spatk_opponent, 0, PHP_ROUND_HALF_DOWN);
$spdef_opponent = ($level_opponent / 40) * $db_field['SpDef'];
$spdef_opponent = round($spdef_opponent, 0, PHP_ROUND_HALF_DOWN);
$speed_opponent = ($level_opponent / 40) * $db_field['Speed'];
$speed_opponent = round($speed_opponent, 0, PHP_ROUND_HALF_DOWN);
$type1_opponent = $db_field['Type1'];
$type2_opponent = $db_field['Type2'];


//---Getting opponent's attack details
$stmt = $db_handle->prepare("SELECT * FROM `moves` WHERE Name=:atk");
$stmt->bindValue("atk", $atk_name_opponent);
$db_field = $stmt->fetch();


$basepower_opponent = $db_field['BasePower'];
$critical_opponent = ($db_field['CriticalHit'] * 0.18) * 100;
$critical_opponent = round($critical_opponent, 0, PHP_ROUND_HALF_DOWN);
$atk_type_opponent = $db_field['Type'];


/*

If the user outspeeds the opponent Pokemon.

*/

if ($speed_you >= $speed_opponent) {

//---Switching a $def_var depending on special or physical attack (YOUR side).
if ($category_you = 'Special') { $def_var = $spdef_opponent; $atk_var = $spatk_you; }
if ($category_you = 'Physical') { $def_var = $def_opponent; $atk_var = $atk_you; }
//---Determining an item boost.
$itemboost = 1;

//---Atk Damage calculation (Part I)
$atk_damage = ((((((($level_you * 2) / 5) * $basepower_you * $atk_var) / 50) / $def_var) * $itemboost) + 2);

$rand = (rand(1,15) / 100);
$critical = rand(1,100);

//---Critical hit calculator
if ($critical >= $critical_you) { $critical_rate = 1.25; $string6 = 'It was a critical hit!<BR>'; }
else { $critical_rate = 1; }

//---STAB calculator
if ($atk_type_you == $type1_you || $atk_type_you == $type2_you) { $STAB = 1.5; }
else { $STAB = 1; }

$stmt = $db_handle->prepare("SELECT :type1 FROM `types` WHERE Type=:atk");
$stmt->bindValue("type1", $type1_opponent);
$stmt->bindValue("atk", $atk_type_you);
$stmt->execute();
$db_field = $stmt->fetch();
$atk_type1 = $db_field[$type1_opponent];

if ($type2_opponent != "") {
$stmt = $db_handle->prepare("SELECT :type2 FROM `types` WHERE Type=:atk");
$stmt->bindValue("type2", $type2_opponent);
$stmt->bindValue("atk", $atk_type_you);
$stmt->execute();
$atk_type2 = $db_field[$type2_opponent];
}
else {
$atk_type2 = 1;
}

$atk_types = $atk_type1 * $atk_type2;


if ($atk_types = 0) { $string3 = '<p>It didn\'t effect the foe.</p><BR>'; }
elseif ($atk_types = 1) { }
elseif ($atk_types < 1) { $string3 = '<p>It wasn\'t very effective.</p><BR>'; }
elseif ($atk_types > 1) { $string3 = '<p>It was super effective!</p><BR>'; }

//---Atk Damage calculation (Part II)
$atk_damage = ((($atk_damage * $critical_rate * (1 - $rand)) / 2) * $STAB * $atk_types);
$atk_damage = round($atk_damage, 0, PHP_ROUND_HALF_UP);

$_SESSION['HP_opponent'] = $_SESSION['HP_opponent'] - $atk_damage;


$string1 = $pkmn_you . " used " . $atk_name_you . "!<BR>";

$ID_2 = $_SESSION['opponent_ID'];

//---Checking for extra strings.
if ($_SESSION['HP_opponent'] <= 0) { 

$stmt = $db_handle->prepare("SELECT BaseEXP FROM `basestats` WHERE `Species`=:species");
$stmt->bindValue("species", $_SESSION['Species_opponent']);
$stmt->setFetchMode(PDO::FETCH_ASSOC);
$stmt->execute(); $db_field = $stmt->fetch();

$expgain = ($db_field['BaseEXP'] * $_SESSION['level_opponent']) / 7;

$stmt = $db_handle->prepare("SELECT Level,EXP FROM `userbox` WHERE `User_ID`=:uID AND `Roster`=:slot");
$stmt->bindValue("uID", $_SESSION['ID']);
$stmt->bindValue("slot", $_SESSION['roster']);
$stmt->setFetchMode(PDO::FETCH_ASSOC);
$stmt->execute(); $db_field = $stmt->fetch();

$i = 0;
while ($i != 1) {
$nextlevel_EXP = $db_field['Level'] * $db_field['Level'] * $db_field['Level'];
$nextlevel = $db_field['Level'] + 1;
$exptotal = $db_field['EXP'] + $expgain;

if ($nextlevel_EXP > $exptotal) { $i = 1; }

if ($exptotal >= $nextlevel_EXP) {
$string10 = $pkmn_you . " grew to level " . $nextlevel ."!";

$stmt = $db_handle->prepare("UPDATE `userbox` SET `EXP`=:EXP , `Level`=:level WHERE User_ID=:uID and Roster=:slot");
$stmt = $db_handle->prepare("UPDATE `userbox` SET `EXP`=:EXP , `Level`=:level WHERE User_ID=:uID and Roster=:slot");
$stmt->bindValue("EXP", $exptotal);
$stmt->bindValue("level", $nextlevel);
$stmt->bindValue("uID", $_SESSION['ID']);
$stmt->bindValue("slot", $_SESSION['roster']);

}
}

$string4 = "<p>Foe " . $pkmn_opponent . " fainted!<BR><B>You won the battle!</B><BR></p>";
$string9 = '<p>The battle has finished.<BR><a href="battle3.php?ID=' . $ID_2 . '&Submit=Battle%21">Restart Battle</a></p>';
$_SESSION['HP_opponent'] = 0;
unset($_SESSION['HP_opponent']);
unset($_SESSION['HP_you']);
unset($_SESSION['MoveCount']); 
unset ($_SESSION['Move1']);
unset ($_SESSION['Move2']);
unset ($_SESSION['Move3']);
unset ($_SESSION['Move4']);
unset ($_SESSION['pkmn_opponent']);
unset ($_SESSION['Next_level_opponent']);
unset ($_SESSION['EXP_opponent']);
unset ($_SESSION['level_opponent']);
$_SESSION['Faint'] = "N";
echo $string9;
$db_handle = null;
}

//---Switching an opponents $def_var depending on special or physical attack (YOUR side).
if ($category_opponent = 'Special') { $def_var = $spdef_you; $atk_var = $spatk_you; }
if ($category_opponent = 'Physical') { $def_var = $def_you; $atk_var = $atk_you; }
//---Determining an opponents item boost.
$itemboost = 1;



//---Attack damage calculator (Part I)
$atk_damage = ((((((($level_opponent * 2) / 5) * $basepower_opponent * $atk_var) / 50) / $def_var) * $itemboost) + 2);

$rand = (rand(1,15) / 100);
$critical = rand(1,100);

//---Critical hit calculator
if ($critical >= $critical_you) { $critical_rate = 1.25; $string6 = 'It was a critical hit!<BR>'; }
else { $critical_rate = 1; }

//---STAB calculator
if ($atk_type_opponent == $type1_opponent || $atk_type_opponent == $type2_opponent) { $STAB = 1.5; }
else { $STAB = 1; }

db_connect($db_handle);

$stmt = $db_handle->prepare("SELECT :type1 FROM types WHERE Type=:atk");
$stmt->bindValue("type1", $type1_you);
$stmt->bindValue("atk", $atk_type_opponent);
$stmt->execute();
$db_field = $stmt->fetch();

$atk_type1 = $db_field[$type1_you];

if ($type2_you != "") {

$stmt = $db_handle->prepare("SELECT :type2 FROM types WHERE Type=:atk");
$stmt->bindValue("type2", $type2_you);
$stmt->bindValue("atk", $atk_type_opponent);
$stmt->execute();
$db_field = $stmt->fetch();

$atk_type2 = $db_field[$type2_you];
}
else {
$atk_type2 = 1;
}

if ($atk_types = 0) { $string7 = '<p>It didn\'t effect the foe.</p>'; }
elseif ($atk_types > 1) { $string7 = '<p>It wasn\'t very effective.</p>'; }
elseif ($atk_types = 1) { }
elseif ($atk_types > 1) { $string7 = '<p>It was super effective!</p>'; }




$atk_damage = ((($atk_damage * $critical_rate * (1 - $rand)) / 2) * $STAB * $atk_types);

$_SESSION['HP_you'] = $_SESSION['HP_you'] - $atk_damage;
$_SESSION['HP_you'] = round($_SESSION['HP_you'], 0, PHP_ROUND_HALF_UP);

$string5 = "Foe " . $pkmn_opponent . " used " . $atk_name_opponent . "!<BR><BR>";

if ($_SESSION['HP_you'] <= 0) { $string8 = "<p>" . $pkmn_you . " fainted!<BR><B>You lost the battle!</B></p>";
$_SESSION['HP_you'] = 0;
unset($_SESSION['HP_opponent']);
unset($_SESSION['HP_you']);
unset($_SESSION['MoveCount']); 
unset ($_SESSION['Move1']);
unset ($_SESSION['Move2']);
unset ($_SESSION['Move3']);
unset ($_SESSION['Move4']);
unset ($_SESSION['pkmn_opponent']);
unset ($_SESSION['Next_level_opponent']);
unset ($_SESSION['EXP_opponent']);
unset ($_SESSION['level_opponent']); 
mysql_close();
}
}
}

/*

If the opponent outspeeds you.

*/

elseif ($speed_you < $speed_opponent) {
//---Switching an opponents $def_var depending on special or physical attack (YOUR side).
if ($category_opponent = 'Special') { $def_var = $spdef_you; $atk_var = $spatk_you; }
if ($category_opponent = 'Physical') { $def_var = $def_you; $atk_var = $atk_you; }
//---Determining an opponents item boost.
$itemboost = 1;

//---Attack damage calculator (Part I)
$atk_damage = ((((((($level_opponent * 2) / 5) * $basepower_opponent * $atk_var) / 50) / $def_var) * $itemboost) + 2);

$rand = (rand(1,15) / 100);
$critical = rand(1,100);

//---Critical hit calculator
if ($critical <= $critical_you) { $critical_rate = 1.25; $string6 = 'It was a critical hit!<BR>'; }
else { $critical_rate = 1; }

//---STAB calculator
if ($atk_type_opponent == $type1_opponent || $atk_type_opponent == $type2_opponent) { $STAB = 1.5; }
else { $STAB = 1; }

$stmt = $db_handle->prepare("SELECT :type1 FROM types WHERE Type=:atk");
$stmt->bindValue("type1", $type1_you);
$stmt->bindValue("atk", $atk_type_opponent);
$stmt->execute();
$db_field = $stmt->fetch();

$atk_type1 = $db_field[$type1_you];

if ($type2_you != "") {

$stmt = $db_handle->prepare("SELECT :type2 FROM types WHERE Type=:atk");
$stmt->bindValue("type2", $type2_you);
$stmt->bindValue("atk", $atk_type_opponent);
$stmt->execute();
$db_field = $stmt->fetch();

$atk_type2 = $db_field[$type2_you];
}
else {
$atk_type2 = 1;
}

$atk_types = $atk_type1 * $atk_type2;

if ($atk_types = 0) { $string7 = '<p>It didn\'t effect the foe.</p>'; }
elseif ($atk_types = 1) { }
elseif ($atk_types < 1) { $string7 = '<p>It wasn\'t very effective.</p>'; }
elseif ($atk_types > 1) { $string7 = '<p>It was super effective!</p>'; }


$atk_damage = ((($atk_damage * $critical_rate * (1 - $rand)) / 2) * $STAB * $atk_types);


$_SESSION['HP_you'] = $_SESSION['HP_you'] - $atk_damage;


$string1 = "Foe " . $pkmn_opponent . " used " . $atk_name_opponent . "!<BR>";
if ($_SESSION['HP_you'] <= 0) { $string4 = "<p>" . $pkmn_you . " fainted!<BR><B>You lost the battle!</B></p>";
$_SESSION['HP_you'] = 0;
unset($_SESSION['HP_opponent']);
unset($_SESSION['HP_you']);
unset($_SESSION['MoveCount']); 
unset ($_SESSION['Move1']);
unset ($_SESSION['Move2']);
unset ($_SESSION['Move3']);
unset ($_SESSION['Move4']);
unset ($_SESSION['pkmn_opponent']);
unset ($_SESSION['Next_level_opponent']);
unset ($_SESSION['EXP_opponent']);
unset ($_SESSION['level_opponent']);
$db_handle = null;
}


//---Switching a $def_var depending on special or physical attack (YOUR side).
if ($category_you = 'Special') { $def_var = $spdef_opponent; $atk_var = $spatk_you; }
if ($category_you = 'Physical') { $def_var = $def_opponent; $atk_var = $atk_you; }
//---Determining an item boost.
$item_boost = 1;

//---Atk Damage calculation (Part I)
$atk_damage = ((((((($level_you * 2) / 5) * $basepower_you * $atk_var) / 50) / $def_var) * $item_boost) + 2);

$rand = (rand(1,15) / 100);
$critical = rand(1,100);

//---Critical hit calculator
if ($critical >= $critical_you) { $critical_rate = 1.25; $string6 = '<p>It was a critical hit!</p><BR>'; }
else { $critical_rate = 1; }

//---STAB calculator
if ($atk_type_you == $type1_you || $atk_type_you == $type2_you) { $STAB = 1.5; }
else { $STAB = 1; }

$stmt = $db_handle->prepare("SELECT :type1 FROM `types` WHERE Type=:atk");
$stmt->bindValue("type1", $type1_opponent);
$stmt->bindValue("atk", $atk_type_you);
$stmt->execute();
$db_field = $stmt->fetch();
$atk_type1 = $db_field[$type1_opponent];

if ($type2_opponent != "") {
$stmt = $db_handle->prepare("SELECT :type2 FROM `types` WHERE Type=:atk");
$stmt->bindValue("type2", $type2_opponent);
$stmt->bindValue("atk", $atk_type_you);
$stmt->execute();
$atk_type2 = $db_field[$type2_opponent];
}
else {
$atk_type2 = 1;
}

$atk_types = $atk_type1 * $atk_type2;
if ($atk_types = 0) { $string3 = '<p>It didn\'t effect the foe.</p><BR>'; }
elseif ($atk_types = 1) { }
elseif ($atk_types < 1) { $string3 = '<p>It wasn\'t very effective.</p><BR>'; }
elseif ($atk_types > 1) { $string3 = '<p>It was super effective!</p><BR>'; }

//---Atk Damage calculation (Part II)
$atk_damage = ((($atk_damage * $critical_rate * (1 - $rand)) / 2) * $STAB * $atk_types);
$atk_damage = round($atk_damage, 0, PHP_ROUND_HALF_UP);

if (isset($_SESSION['HP_opponent'])) {
$_SESSION['HP_opponent'] = $_SESSION['HP_opponent'] - $atk_damage;
}
else {
$hp_opponent = $hp_opponent - $atk_damage;
$_SESSION['HP_opponent'] = $hp_opponent;
}

$string5 = "<p>" . $pkmn_you . " used " . $atk_name_you . "!</p><BR>";

$ID_2 = $_SESSION['opponent_ID'];
//---Checking for extra strings.
if ($_SESSION['HP_opponent'] <= 0) {

$stmt = $db_handle->prepare("SELECT BaseEXP FROM `basestats` WHERE `Species`=:species");
$stmt->bindValue("species", $_SESSION['Species_opponent']);
$stmt->setFetchMode(PDO::FETCH_ASSOC);
$stmt->execute(); $db_field = $stmt->fetch();

$expgain = ($db_field['BaseEXP'] * $_SESSION['level_opponent']) / 7;

$stmt = $db_handle->prepare("SELECT Level,EXP FROM `userbox` WHERE `User_ID`=:uID AND `Roster`=:slot");
$stmt->bindValue("uID", $_SESSION['ID']);
$stmt->bindValue("slot", $_SESSION['roster']);
$stmt->setFetchMode(PDO::FETCH_ASSOC);
$stmt->execute; $db_field = $stmt->fetch();

while ($i != 1) {
$nextlevel_EXP = $db_field['Level'] * $db_field['Level'] * $db_field['Level'];
$nextlevel = $db_field['Level'] + 1;
$exptotal = $db_field['EXP'] + $expgain;

if ($nextlevel_EXP > $exptotal) { $i = 1; }

if ($exptotal >= $nextlevel_EXP) {
$string10 = $pkmn_you . " grew to level " . $nextlevel ."!";

$stmt = $db_handle->prepare("UPDATE `userbox` SET `EXP`=:EXP , `Level`=:level WHERE User_ID=:uID and Roster=:slot");
$stmt->bindValue("EXP", $exptotal);
$stmt->bindValue("level", $nextlevel);
$stmt->bindValue("uID", $_SESSION['ID']);
$stmt->bindValue("slot", $_SESSION['roster']);

}
}

$string8 = "<p>Foe " . $pkmn_opponent . " fainted!<BR>" . $pkmn_you . "gained " . $expgain . "EXP!<BR><B>You won the battle!</B><BR>";
$string9 = 'The battle has finished.<BR><a href="battle3.php?ID=' . $ID_2 . '&Submit=Battle%21">Rebattle</a></p>';
$_SESSION['HP_opponent'] = 0;
unset($_SESSION['HP_opponent']);
unset($_SESSION['HP_you']);
unset($_SESSION['MoveCount']); 
unset ($_SESSION['Move1']);
unset ($_SESSION['Move2']);
unset ($_SESSION['Move3']);
unset ($_SESSION['Move4']);
unset ($_SESSION['pkmn_opponent']);
unset ($_SESSION['Next_level_opponent']);
unset ($_SESSION['EXP_opponent']);
unset ($_SESSION['level_opponent']);
$db_handle = null;

}
else {
$db_handle = null;
}
}


//---Updating battle turns.
if (!isset($_SESSION['MoveCount'])) {
$_SESSION['MoveCount'] = 2;
}
else {
$_SESSION['MoveCount'] = $_SESSION['MoveCount'] + 1;
}


?>


<table class="yourdisplay">
<?PHP
db_connect($db_handle);

//---Getting your Pokemon
if (!isset($_POST['MoveCount'])) {
$_SESSION['roster'] = 1;
}

if (isset($_POST['Change'])) {
$_SESSION['roster'] = $_SESSION['roster'] + 1;
}


$roster = $_SESSION['roster'];

$ID = $_SESSION['ID'];
$stmt = $db_handle->prepare("SELECT * FROM `userbox` WHERE `User_ID`=:uID AND `Roster`=:slot");
$stmt->bindValue("uID", $ID);
$stmt->bindValue("slot", $roster);
$stmt->setFetchMode(PDO::FETCH_ASSOC);
$stmt->execute();
$db_field = $stmt->fetch();


$level_you = $db_field['Level'];
$_SESSION['level_you'] = $level_you;
$exp_you = $db_field['EXP'];
$_SESSION['exp_you'] = $exp_you;
$pokemon_ID = $db_field['Pokemon_ID'];
$status = $db_field['Status'];
$_SESSION['pkmn_you'] = $db_field['Species'];
$pkmn_you = $_SESSION['pkmn_you'];
$move1_you = $db_field['Move1'];
$move2_you = $db_field['Move2'];
$move3_you = $db_field['Move3'];
$move4_you = $db_field['Move4'];
$species = $db_field['Species'];
$_SESSION['Species_you'] = $species;

if ($status == 'Normal') { $status = ''; }

$stmt = $db_handle->prepare("SELECT Normal,Shiny,HP FROM `basestats` WHERE `Species`=:species");
$stmt->bindValue("species", $species);
$stmt->setFetchMode(PDO::FETCH_ASSOC);
$stmt->execute();
$db_field = $stmt->fetch();

if ($status == '') { $display_image = $db_field['Normal']; }
elseif ($status == 'Shiny') { $display_image = $db_field['Shiny']; }

$hp_you = ($level_you / 40) * $db_field['HP'] * 2;
$hp_you = round($hp_you, 0, PHP_ROUND_HALF_UP);

$totalhp_you = $hp_you;

$next_level = ($level_you + 1);
$next_level = ($next_level * $next_level * $next_level) - $exp_you;
$_SESSION['Next_level'] = $next_level;

if (!isset($_SESSION['HP_you'])) {
$_SESSION['HP_you'] = $hp_you;
$_SESSION['HP_you'] = round($_SESSION['HP_you'], 0, PHP_ROUND_HALF_UP);
}
else {
$_SESSION['HP_you'] = round($_SESSION['HP_you'], 0, PHP_ROUND_HALF_UP);
}

unset($_POST['Change']);
?>

<tr><th>Your <?PHP echo $status . $species; ?></th></tr>
<tr><td class="foe"><div class="floatright"><?PHP echo $display_image; ?></div>
Level <?PHP echo $level_you; ?><BR>
HP: <?PHP echo $_SESSION['HP_you'] . "/" . $totalhp_you; ?><BR>
Experience: <?PHP echo $exp_you; ?><BR>
To next level: <?PHP echo $next_level ?></td></tr>
</table>

<table class="foedisplay">
<?PHP


$ID_2 = $_SESSION['opponent_ID'];
$roster = 1;

$stmt = $db_handle->prepare("SELECT * FROM `userbox` WHERE `User_ID`=:oID AND `Roster`=:slot");
$stmt->bindValue("oID", $ID_2);
$stmt->bindValue("slot", $roster);
$stmt->execute();
$db_field = $stmt->fetch();

$exp_opponent = $db_field['EXP'];
$_SESSION['exp_opponent'] = $exp_opponent;
$level_opponent = $db_field['Level'];
$_SESSION['level_opponent'] = $level_opponent;
$exp_opponent = $db_field['EXP'];
$_SESSION['exp_opponent'] = $exp_opponent;
$pokemon_ID = $db_field['Pokemon_ID'];
$status = $db_field['Status'];
$species = $db_field['Species'];
$_SESSION['Species_opponent'] = $species;

if ($status == 'Normal') { $status = ''; }

$stmt = $db_handle->prepare("SELECT Normal,Shiny,HP FROM `basestats` WHERE `Species`=:species");
$stmt->bindValue("species", $species);
$stmt->setFetchMode(PDO::FETCH_ASSOC);
$stmt->execute();
$db_field = $stmt->fetch();

if ($status == '') { $display_image = $db_field['Normal']; }
elseif ($status == 'Shiny') { $display_image = $db_field['Shiny']; }

$hp_opponent = ($level_opponent / 40) * $db_field['HP'] * 2;
$hp_opponent = round($hp_opponent, 0, PHP_ROUND_HALF_UP);
$totalhp_opponent = $hp_opponent;

$next_level = ($level_opponent + 1);
$next_level = ($next_level * $next_level * $next_level) - $exp_opponent;
$_SESSION['Next_level_opponent'] = $next_level;

$_SESSION['pkmn_opponent'] = $species;

if (!isset($_SESSION['HP_opponent'])) {
$_SESSION['HP_opponent'] = $hp_opponent;
$_SESSION['HP_opponent'] = round($_SESSION['HP_opponent'], 0, PHP_ROUND_HALF_UP);
}
?>

<tr><th>Foe's <?PHP echo $status . $species; ?></th></tr>
<tr><td><div class="floatleft"><?PHP echo $display_image; ?></div>
Level <?PHP echo $level_opponent; ?><BR>
HP: <?PHP echo $_SESSION['HP_opponent'] . "/" . $totalhp_opponent; ?> <BR>
Experience: <?PHP echo $exp_opponent; ?> <BR>
To next level: <?PHP echo $next_level; ?></td></tr>
</table>


</head>
<body>

<p>
<?PHP

db_connect($db_handle);

$roster = 1;
$ID = $_SESSION['ID'];

$stmt = $db_handle->prepare("SELECT Move1,Move2,Move3,Move4 FROM userbox WHERE User_ID=:ID AND Roster=:slot");
$stmt->bindValue("ID", $ID);
$stmt->bindValue("slot", $roster);
$stmt->execute();
$db_field = $stmt->fetch();

$move1 = $db_field['Move1'];
$move2 = $db_field['Move2'];
$move3 = $db_field['Move3'];
$move4 = $db_field['Move4'];
$_SESSION['Move1'] = $move1;
$_SESSION['Move2'] = $move2;
$_SESSION['Move3'] = $move3;
$_SESSION['Move4'] = $move4;


if (!isset($_SESSION['MoveCount'])) {
$_SESSION['MoveCount'] = 1;
}

if (isset($_POST['SelectAttack'])) { echo $string1; }
if (isset($string2)) { echo $string2; }
if (isset($string3)) { echo $string3; }
if (isset($string4)) { echo $string4;
return; }
if (isset($_POST['SelectAttack'])) { echo $string5; }
if (isset($string6)) { echo $string6; }
if (isset($string7)) { echo $string7; }
if (isset($string8)) { echo $string8; }
if (isset($string9)) { echo $string9; }
if (isset($string9)) { echo $string10; return; }
echo "<BR><BR>Turn " . $_SESSION['MoveCount'];

$db_handle = null;
?>

<FORM name="attack" method="POST" action="battle3.php"><p>
<Input type ='Radio' Name ='SelectAttack' value= 'move1'> <?PHP echo $move1; ?> <BR>
<Input type ='Radio' Name ='SelectAttack' value= 'move2'> <?PHP echo $move2; ?> <BR>
<Input type ='Radio' Name ='SelectAttack' value= 'move3'> <?PHP echo $move3; ?> <BR>
<Input type ='Radio' Name ='SelectAttack' value= 'move4'> <?PHP echo $move4 . "</p>"; ?> <BR>
<Input type='Submit' Name ='Attack' value= 'Attack!'>
<Input type='Submit' Name ='Change' value= 'or change to next Pokemon'>
</p>
</FORM>
</div>
</body>
</html>


