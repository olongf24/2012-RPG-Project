<!DOCTYPE html>
<html>
<head>

<title>Claim a Pokemon</title>

<style type="text/css">
                 

body {
background-color:#DD0000;
}

h1 {
font-size:20px;
color:#FFFFFF;
text-decoration:underline;
text-decoration:bold;
}

h2 {
font-size:14px;
color:#FFFFFF;
}

h3 {
font-size:14px;
color:yellow;
}

h4 {
font-size:12px;
color:cream;
}

</style>



<?PHP

$clicked = $_POST['SubmitClaim'];


if (isset($_POST['SubmitClaim'])) {

	

					$guess = $_POST['Guess'];

					if ($guess == "") {
					$guess = "undefine";
					echo "<h4>Nothing found in the textbox!</h4>";
					}
					

	if ($guess == "Miltank") {
					echo "<h4>You have gained a Miltank!</h4>";
				   }


			else {
				echo "<h4>Wrong Pokemon! Too bad.</h4>";
				$clicked = "undefine";
			     }

}




?>



</head>

<body>





<h1>Click this to claim a Pokemon!</h1>

<h2>The current Pokemon is:</h2>

<IMG SRC =Images/241.png>

<h3>Guess the Pokemon's Name to win the prize.</h3>




<FORM NAME ="GuesstoClaim" METHOD ="POST" ACTION = "Claimamiltank.php">

<INPUT TYPE = "TEXT" VALUE ="" NAME = "Guess">
<INPUT TYPE = "Submit" Name = "SubmitClaim" VALUE = "Claim">
</FORM>

</body>
</html>