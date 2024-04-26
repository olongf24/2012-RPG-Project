<!DOCTYPE hmtl>
<html>
<head>


<?PHP
include 'layout_loggedout.php';
?>

<style class="text/css">

h4 {
color:#330000;
}

h5 {
font-family:Tahoma;
font-size:12px;
color:#FFFFFF;
}

</style>
<BR>
<div class="baserpgcontainer">

<?PHP

//---Defining this function for future use.
function db_connect(&$db_handle) {

$user_name = "root";
$password = "";
$database = "users";
$server = "127.0.0.1";


try {

 $db_handle = new PDO("mysql:host=$server;dbname=$database", $user_name, $password); 
 $db_handle->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
 //---Setting an error attribute.
 $db_handle->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );  
 }  
catch(PDOException $e) {  
	//---Record the error.
    echo "Sorry, I can't do that.";
	file_put_contents('PDOErrors.txt', $e->getMessage(), FILE_APPEND);  

}
return $db_handle;

}

//---Checking if button was submitted.
if (!isset($_POST['Submit']) || isset($_SESSION['Username'])) {
echo "<h1>Error Detected. Please go back.</h1>";
return;
}
if ($_POST['starter'] == "noselect") {
echo "<h1>You didn't select your starter. Please go back.</h1>";
return;
}
$starter = $_POST['starter'];

$s_username = htmlentities(strip_tags($_POST['username']));
$s_password = htmlentities(strip_tags($_POST['password']));

//---Checking for blank values.
if (empty($s_username)  | empty($s_password)) {
echo "<h1>No username or password submitted. Please go back.</h1>";
return;
}
else {
echo "<h4>Processing username/password request.</h4>";
}

if (empty($_POST['email'])) {
echo "<h1>No email submitted. Please go back.</h1>";
return;
}

//---Trimming + converting password, and adding slashes (security).
$s_password = addslashes(trim($s_password));
$s_password = addslashes(hash('sha512', $s_password));
$s_username = addslashes($s_username);
$checka = '%^&*~\/|<>?@#$!';
$checkb = '1234567890';

//---Checking for "bad characters" (prevent attacks)
$check_username1 = similar_text($s_username, $checka, $check1);
$check_username2 = similar_text($checkb, $s_username, $check2);
$check_password3 = similar_text($s_password, $checka, $check3);

if ($check1 > 0) {
echo "<h4>Bad characters detected in the username or password.</h4>";
echo "<h4>Please use letters and numbers <B>only.</B></h4>";
return;
}
elseif ($check2 == 10 ) {
echo "<h4>The username <u>must</u> contain letters.</h4>";
return;
}
elseif ($check3 > 0) {
echo "<h4>Bad characters detected in the username or password.</h4>";
echo "<h4>Please use letters and numbers <B>only</B></h4>";
return;
}

//---Trimming any spaces and checking username charlength.
$s_username = trim($_POST['username']);
$c_username = strlen($s_username);

if ($c_username >= 16) {
echo "<h2>Your username exceeds the 15-character limit.</h2>";
return;
}
if ($c_username <= 3) {
echo "<h2>Your username must be at least 4 characters.</h2>";
return;
}

//---Checking for email @ symbol.
$s_email = trim($_POST['email']);
$checkc = '@';
$check_username1 = similar_text($s_email, $checkc, $check3);

if ($check3 == 0) {
echo "<h4>Invalid email address submitted.</h4><BR>";
return;
}

//---Salting the password. :D
$characterList = "abcdef012345678901234567890123456789abcdef";
        $i = 0;
        $max = 30;
        $salt_beg = "";
		$salt_end = "";
		//---Getting the starter salt.
        while ($i < $max) {
            $salt_beg .= $characterList{mt_rand(0, (strlen($characterList) - 1))};
            $i++;
         }
		 //---Getting the finishing salt.
		         $i = 0;
        while ($i < $max) {
	$salt_end .= $characterList{mt_rand(0, (strlen($characterList) - 1))};
	$i++;
        }
		
		$s_password = $salt_beg . $s_password . $salt_end;

//----Checking if the username submitted matches another already ingame.

db_connect($db_handle);

try {
//---See if the username is already in the database
$stmt = $db_handle->prepare("SELECT `Username` FROM `userdetails` WHERE Username=:name");
$stmt->bindValue("name", $s_username);
$stmt->setFetchMode(PDO::FETCH_ASSOC);
$stmt->execute();
$db_field = $stmt->fetch();
}
catch (PDOException $e) {
//---Record the error.
$dateoferror = "\n". date('y-m-d H:i:s') . "\n";
echo "<h4>There has been an error connecting to the database. We will fix this. Sorry!</h4>";
file_put_contents('PDOErrors.txt', $e->getMessage(), FILE_APPEND);
file_put_contents('PDOErrors.txt', $dateoferror, FILE_APPEND);
return;
}

$check = $db_field['Username'];

//---Checking if username is already there.
if ($check == $s_username) {
echo "<h1>This username has already been taken.<br>Please go back.</h1>";
return;
}

try {
//---Get the latest ID from the database
$stmt = $db_handle->query("SELECT User_ID FROM `userdetails` WHERE `User_ID` = (SELECT MAX(User_ID) from userdetails)");
$stmt->setFetchMode(PDO::FETCH_ASSOC);
$stmt->execute();
$db_field = $stmt->fetch();
}
catch (PDOException $e) {
//---Record the error.
$dateoferror = "\n". date('y-m-d H:i:s') . "\n";
echo "<h4>There has been an error connecting to the database. We will fix this. Sorry!</h4>";
file_put_contents('PDOErrors.txt', $e->getMessage(), FILE_APPEND);
file_put_contents('PDOErrors.txt', $dateoferror, FILE_APPEND);
return;
}

//---Getting new ID, date and IP of join.
$new_ID = $db_field['User_ID'] + 1;
$date = date('d-m-y H:i:s');
$IP_join = $_SERVER['REMOTE_ADDR'];

//---Inserting the new user into the database.
$stmt = $db_handle->prepare("INSERT INTO `userdetails`
(`User_ID`,`Username`,`Password`,`Email`,`Money_hand`,`Money_bank`,`Last_online`,`Dungeon_EXP`,`Wild_EXP`,`Trainer_EXP`,`Pokemon_Total`,`IP_lastused`,`New`)
VALUES
(:newID,:name,:pass,:email,3000,0,:date,0,0,0,1,:IPjoin,'Y')");
$stmt->bindValue("newID", $new_ID);
$stmt->bindValue("name", $s_username);
$stmt->bindValue("pass", $s_password);
$stmt->bindValue("email", $s_email);
$stmt->bindValue("date", $date);
$stmt->bindValue("IPjoin", $IP_join);
$stmt->execute();

$stmt = $db_handle->prepare("INSERT INTO `usersettings`
VALUES (:newID,'N','Normal','N',1,1,1,1,'N');");
$stmt->bindValue("newID", $new_ID);
$stmt->execute();

$stmt = $db_handle->prepare("INSERT INTO `log_ip`
VALUES (:newID,:IP,:date,'join')");
$stmt->bindValue("newID", $new_ID);
$stmt->bindValue("IP", $IP_join);
$stmt->bindValue("date", $date);
$stmt->execute();

//---Getting gender.
$rand = rand(1,100);
$rand2 = rand(1,10000);

//---Getting actual gender value.
if ($rand <= 87) { $gender = 1; }
else { $gender = 2; }

//---Gender is (?)?
if ($rand2 == 4) { $gender = 4; }

//---Putting ghost Pokemon into db
$stmt = $db_handle->prepare("INSERT INTO `start_ghosts`
VALUES (:starter,:newID,:gender)");
$stmt->bindValue("starter", $starter);
$stmt->bindValue("newID", $new_ID);
$stmt->bindValue("gender", $gender);
$stmt->execute();

//---Posting new user information on-screen.
echo "<h4>Your Trainer ID is: " . $new_ID . "</h4><BR>";
echo "<h4>Username: " . $s_username .'<BR>';
echo "<h4>Email: " . $s_email . "<BR><BR>You have successfully registered.</h4><BR><BR>";
echo "<h3>You can log in now.</h3>";

?>
</div>