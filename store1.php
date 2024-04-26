<!DOCTYPE html>
<html>
<title>The Copper Clamperls</title>
<head>
<?PHP
include 'layout_if.php';

db_connect($db_handle);
$page = 'store1.php';
include 'log_update.php';
$db_handle = null;
?>
<style class="text/css">
table#storelist {
font-size:14px;
left:10px;
width:500px;
font-family:tahoma;
color:white;
background-color:#330000;
position:absolute;
}

table#storelist td.left {
text-align:left;
border:3px solid #EE7600;
background-color:#990000;
width:250px;
}
table#storelist td.left img {
float:left;
}

table#storelist td.right {
width:250px;
text-align:right;
background-color:#990000;
border:3px solid #EE7600;
}
table#storelist td.right img {
float:right;
}

table#promobox {
position:absolute;
right:10px;
float:right;
font-family:tahoma;
font-size:14px;
width:170px;
}
table#promobox th {
border-radius:20px;
background-color:#DD0000;
color:white;
}
table#promobox td {
border-radius:20px;
text-align:center;
background-color:yellow;
border:2px solid #EE7600;
}
table#promobox td img {
float:center;
}

input.buybutton {
border:2px solid yellow;
background-color:#330000;
color:yellow;
}


.baserpgcontainer {
height:600px;
}
</style>

<div class="baserpgcontainer">
</head>
<body>
<?PHP db_connect($db_handle);


/*

If user attempts to buy a Pokemon

*/

if (isset($_POST['ID'])) { 

//---Fetching submitted buy.
$ID = explode(" ", $_POST['ID']);
$info['Species'] = $ID[1];

//---Getting details from database
$stmt1 = $db_handle->prepare("SELECT * FROM `store1` WHERE `Species`=:spec");
$stmt1->bindValue("spec", $info['Species']);
$stmt1->execute();
$db_field = $stmt1->fetch();


//---Storing the details in array
$info['uID'] = $_SESSION['ID'];
$info['Price'] = $db_field['Price'];
$info['Gender'] = $db_field['Gender'];
$info['Level'] = $db_field['Level'];
$info['Status'] = $db_field['Status'];

//---Checking to see if the user has enough money
$stmt2 = $db_handle->prepare("SELECT Money_hand FROM `userdetails` WHERE `User_ID`=:uID");
$stmt2->bindValue("uID", $info['uID']);
$stmt2->execute();
$db_field = $stmt2->fetch();

if ($db_field['Money_hand'] < $info['Price']) {
echo '<p><font color="#DD0000"><B>You do not have enough money to buy this.</B></font></p>';
$db_handle = null;
return;
}
else {
$info['uMoney'] = $db_field['Money_hand'];
}

//---Getting the latest pokemon ID
$stmt3 = $db_handle->query("SELECT MAX(Pokemon_ID) AS `Pokemon_ID` FROM `userbox`");
$stmt3->execute();
$db_field = $stmt3->fetch();

$info['pkmnID'] = $db_field['Pokemon_ID'] + 1;
$info['EXP'] = $info['Level'] * $info['Level'] * $info['Level'];

//---Setting the gender
if ($info['Gender'] == 5050) {
$info['Gender'] = rand(1,2);
}
else if ($info['Gender'] == 8713) {
$gender = rand(1,100);
		if ($gender < 87) { $info['Gender'] = 1; }
		if ($gender >= 87) { $info['Gender'] = 2; }
}
else if ($info['Gender'] == 1387) {
$gender = rand(1,100);	
		if ($gender > 87) { $info['Gender'] = 2; }
		if ($gender >= 87) { $info['Gender'] = 1; }
}
else {
//---If species is only 1-gendered (eg Genderless)
$info['Gender'] = $info['Gender'];
}

//---Adding Pokemon into userbox table
$stmt4 = $db_handle->prepare("INSERT INTO `userbox` (`Pokemon_ID`,`User_ID`,`Status`,`Species`,`Gender`,`Level`,`EXP`,`Roster`)
VALUES (:pkmnID,:uID,:status,:species,:gender,:level,:exp,'0')");
$stmt4->bindValue("pkmnID", $info['pkmnID']);
$stmt4->bindValue("uID", $info['uID']);
$stmt4->bindValue("status", $info['Status']);
$stmt4->bindValue("species", $info['Species']);
$stmt4->bindValue("gender", $info['Gender']);
$stmt4->bindValue("level", $info['Level']);
$stmt4->bindValue("exp", $info['EXP']);
$stmt4->execute();

if ($info['Status'] == 'Normal') { $poststatus = ''; }

//---Echoing message onscreen
echo '<p><font color="green"><B>Thank you for purchasing ' . $poststatus . $info['Species'] . '. Come back soon!</B></font></p>';


//---Deducting cash from urmom

$cashleft = $info["uMoney"] - $info['Price'];

$stmt5 = $db_handle->prepare("UPDATE `userdetails` SET `Money_hand`=:money WHERE `User_ID`=:uID");
$stmt5->bindValue("money", $cashleft);
$stmt5->bindValue("uID", $info['uID']);
$stmt5->execute();

unset($info);
}


/*

If user claims the promo

*/

if (isset($_POST['PromoClaim'])) {

if ($money_hand < $price) {
echo '<p><font color="#DD0000">Sorry, you do not have enough money on-hand to buy this.</font></p>';
return;
}

$status = 'Normal';
$species = 'Spearow';
$price = '2000';

//---Getting new Pokemon ID
$stmt = $db_handle->query("SELECT MAX(Pokemon_ID) AS Pokemon_ID FROM `userbox`");
$stmt->execute();
$db_field = $stmt->fetch();

$gender = rand(1,2);
$pokemon_ID = $db_field['Pokemon_ID'] + 1;


//---Inserting promo Pokemon into database
$stmt = $db_handle->prepare("INSERT INTO `userbox` (`Pokemon_ID`,`User_ID`,`Status`,`Species`,`Gender`,`Level`,`EXP`,`Roster`)
VALUES (:pkmnID, :uID, :status, :species, :gender, 5, 125, 0)");
$stmt->bindValue("pkmnID", $pokemon_ID);
$stmt->bindValue("uID", $user_ID);
$stmt->bindValue("status", $status);
$stmt->bindValue("species", $species);
$stmt->bindValue("gender", $gender);
$stmt->execute();

echo '<p><font color="green"><B>You successfully purchased the promo!</B></font></p>';

$stmt = $db_handle->prepare("UPDATE `usersettings` SET Promo='Y' WHERE User_ID=:uID");
$stmt->bindValue("uID", $user_ID);
$stmt->execute();

$IP_used = $_SESSION['IP'];
$pokemon = $status . $species;
$cur_date = date('F d, Y H:i:s');

//---Taking the log of the user.
$stmt = $db_handle->prepare("INSERT INTO `log_promo` VALUES (:uID, :pkmnID, :name, :date, :IP)");
$stmt->bindValue("uID", $user_ID);
$stmt->bindValue("pkmnID", $pokemon_ID);
$stmt->bindValue("name", $pokemon);
$stmt->bindValue("date", $cur_date);
$stmt->bindValue("IP", $IP_used);
$stmt->execute();

//---Deducting the cash from the user.
$stmt = $db_handle->prepare("SELECT Money_hand FROM `userdetails` WHERE User_ID=:uID");
$stmt->bindValue("uID", $user_ID);
$stmt->execute();
$db_field = $stmt->fetch();

$money_hand = $db_field['Money_hand'] - $price;

$stmt = $db_handle->prepare("UPDATE `userdetails` SET `Money_hand`=:money WHERE User_ID=:uID");
$stmt->bindValue("money", $money_hand);
$stmt->bindValue("uID", $user_ID);
$stmt->execute();

}



//---Getting current money on-hand
$user_ID = $_SESSION['ID'];
$stmt = $db_handle->prepare("SELECT Money_hand,Trainer_EXP FROM `userdetails` WHERE `User_ID`=:uID");
$stmt->bindValue("uID", $user_ID);
$stmt->setFetchMode(PDO::FETCH_ASSOC);
$stmt->execute();
$db_field = $stmt->fetch();

$money_hand = $db_field['Money_hand'];
$t_exp = $db_field['Trainer_EXP'];


?>
<h1>The Copper Clamperls</h1>

<p><B>Welcome!</B></p>
<p><B>Money:</B> <?PHP echo $money_hand; ?></p>

<table id="storelist">
<?PHP

$stmt = $db_handle->prepare("SELECT * FROM `store1` WHERE Rota='1' AND Promo='N'");
$stmt->setFetchMode(PDO::FETCH_ASSOC);
$stmt->execute();

$col = 0;
$ID = 1;

while ($db_field = $stmt->fetch()) {

$col = $col + 1;

$species = $db_field['Species'];
$status = $db_field['Status'];
$level = $db_field['Level'];
$gender = $db_field['Gender'];
$price = $db_field['Price'];


if ($col == 1) {
echo '<td class="left">';
}
else {
echo '<td class="right">';
}
//---Getting the image!

switch ($status) {
case 'Normal': $stmt1 = $db_handle->prepare("SELECT Normal FROM `basestats` WHERE `Species`=:species"); break;
case 'Shiny': $stmt1 = $db_handle->prepare("SELECT Shiny FROM `basestats` WHERE `Species`=:species"); break;
case 'Retro': $stmt1 = $db_handle->prepare("SELECT Shiny FROM `basestats` WHERE `Species`=:species"); break;
}
$stmt1->bindValue("species", $species);
$stmt1->setFetchMode(PDO::FETCH_ASSOC);
$stmt1->execute();
$display_image1 = $stmt1->fetch();
$display_image = $display_image1[$status];

echo $display_image;
if ($status == 'Normal') { $status = ''; }
echo '<BR><B>'. $status . $species .'</B><BR>';
echo 'Level ' . $level . '<BR>';

//---If the user hasn't got enough money to buy.
if ($money_hand < $price) {
echo '<p><font color="white">You haven\'t got enough money.</font></p>';
if ($col == 2) {
$col = 0;
echo '</tr>';
}
continue;
}

echo 'Price: ' . $price . '<BR>';
echo '<FORM NAME = "PurchasePokemon" METHOD="POST" ACTION="store1.php"><BR>
<INPUT class="buybutton" TYPE ="Submit" Name="ID" VALUE="Buy '. $species. '">
</FORM>';
echo '</td>';

if ($col == 2) {
$col = 0;
echo '</tr>';
}

}
?>
</table>
<table id="promobox">
<tr><th>Special Offer</th></tr>
<tr><td><?PHP
$stmt2 = $db_handle->query("SELECT * FROM `store1` WHERE `Promo`='Y'");
$stmt2->setFetchMode(PDO::FETCH_ASSOC);
$stmt2->execute();
$db_field2 = $stmt2->fetch();

$stmt3 = $db_handle->prepare("SELECT * FROM `basestats` WHERE `Species`=:species");
$stmt3->bindValue("species", $db_field2['Species']);
$stmt3->setFetchMode(PDO::FETCH_ASSOC);
$stmt3->execute();
$db_field3 = $stmt3->fetch();


echo '<p><B>This is the current promo.</B></p>';

echo $db_field3[$db_field2['Status']] . '<BR>';
if ($db_field2['Status'] == 'Normal') { $db_field2['Status'] = ''; }
echo '<B>' . $db_field2['Status'] . $db_field2['Species'] . '</B><BR>'; 
echo 'Price: ' . $db_field2['Price'];


$stmt4 = $db_handle->prepare("SELECT Promo FROM `usersettings` WHERE `User_ID`=:uID");
$stmt4->bindValue("uID", $user_ID);
$stmt4->setFetchMode(PDO::FETCH_ASSOC);
$stmt4->execute();
$db_field4 = $stmt4->fetch();

//---If user's already claimed this promo
if ($db_field4['Promo'] == 'Y') {
echo '<p><font color="#DD0000"><B>You have already claimed this promo.</B></font></p>';
}
else {
//---If the user hasn't got enough cash
if ($money_hand < $db_field2['Price']) {
$diff = $db_field2['Price'] - $money_hand;
echo '<p><font color="#DD0000">You need a further <BR><B>' . $diff . '</B> on-hand to buy this.</font></p>';
return;
}

echo '<FORM NAME ="GetPromo" METHOD ="POST" ACTION = "store1.php"><BR>
<INPUT class = "buybutton" TYPE = "Submit" Name = "PromoClaim" VALUE = "Purchase"></FORM>';

}

$npc = 'Peter';

$stmt5 = $db_handle->prepare("SELECT * FROM `npctalk` WHERE Name=:NPC AND ID='1'");
$stmt5->bindValue("NPC", $npc);
$stmt5->execute();
$db_field5 = $stmt5->fetch();

echo $db_field5['Talk'];

?>

</td></tr>
</table>
</div>
</body>
</html>