<!DOCTYPE html>
<html>
<head>
<?PHP
include 'layout_if.php';
?>
<script class="text/javascript">
function showResult(str) {
if (str.length==0)
  { 
  document.getElementById("livesearch").innerHTML="Nothing found in the box.";
  document.getElementById("livesearch").style.border="0px";
  return;
  }
  
  if (window.XMLHttpRequest)
  {// code for IE7+, Firefox, Chrome, Opera, Safari
  xmlhttp=new XMLHttpRequest();
  }
else
  {// code for IE6, IE5
  xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
  }
xmlhttp.onreadystatechange=function()
  {
  if (xmlhttp.readyState==4 && xmlhttp.status==200)
    {
    document.getElementById("livesearch").innerHTML=xmlhttp.responseText;
    document.getElementById("livesearch").style.border="3px solid #DD0000";
    }
  }
xmlhttp.open("GET","db_livesearch.php?str="+str,true);
xmlhttp.send();
}
</script>



</script>
<style class="text/css">

p.line {
color:#FFFFFF;
}

input.textbox {
background-color:#5E2605;
color:yellow;
font-size:12px;
font-family:Tahoma;
width:100px;
height:15px;
border:1px solid #DD0000;
}
input.button {
background-color:#330000;
color:white;
font-size:14px;
width:100px;
height:33px;
border:1px solid #DD0000;
}

table#livesearch {
margin-left:150px;
width:400px;
font-family:Tahoma;
height:20px;
color:white;
background-color:#5E2605;
border:5px solid #DD0000;
cellpadding:2px;
}
table.class_matches {
margin-left:150px;
width:400px;
font-family:Tahoma;
height:20px;
color:white;
background-color:#5E2605;
border:5px solid #DD0000;
cellpadding:2px;
}

a.

</style>
</head>
<body>
<div class="baserpgcontainer">
<h1>Search a Trainer</h1>

<p>This page allows you to search for a trainer and view their profile.<BR>
Searches must contain > 3 characters.<BR>
The first 10 results will be displayed.</p>

<FORM name="profilesearch" method="POST" action="search.php">
<INPUT type="TEXT" class="textbox" name="String" value="" onkeyup="showResult(this.value)"><BR>
<INPUT type="SUBMIT" class="button" name="Search" value="Search"><BR><BR>
<p class="line">------------------------<p>
<table id="livesearch"></table>
</FORM>
<?PHP
if (isset($_POST['Search'])) {
echo '<p class="line">------------------------<p>';

//---Getting info about string input
$string = $_POST['String'];
$check = strlen($string);

//---If it's less than 4 characters
if ($check == '') {
echo '<p>Nothing was found in the search box.</p>';
}
elseif ($check < 4) {
echo '<p>Sorry, searches must contain over 4 characters.</p>';
return;
}

echo '<p>';
//---Searching database for placed string;
db_connect($db_handle);

echo '<table class="list_matches">';

$SQL = "SELECT User_ID,Username FROM userdetails WHERE Username LIKE '%$string%';";
$result = mysql_query($SQL);
$db_field = mysql_fetch_assoc($result);
$db_count = 1;

echo '<tr><td>' . $db_field['User_ID'] . '</td><td>' . $db_field['Username'] . '</td><td> <a href="profile.php?ID=' . $db_field['User_ID'] . '">View Profile</a></td></tr>';

while ($db_field = mysql_fetch_assoc($result)) {

echo "<tr><td>";
echo $db_field['User_ID'];
echo "</td><td>";
echo $db_field['Username'];
echo "</td><td>";
echo '<a href="profile.php?ID=' . $db_field['User_ID'] . '">View Profile</a>';
echo "</td></tr>";
$db_count = $db_count + 1;

}


echo '</table>';
echo "<p>Didnt get what you were looking for? Try using longer searches.</p>";
}

?>

</div>
<?PHP
$db_handle = null;
?>
</body>
</html>

