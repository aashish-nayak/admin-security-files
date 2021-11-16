<?php
define("LOCAL", "http://localhost/tigersafarinow/1/page.php?url=");
define("WEB", "http://www.tigersafarinow.com/");

$phone = "+91-9024483973";
$email  = "info@tigersafarinow.com";
$website = "tigersafarinow.com"; 
$session_id = "HJKaf1_H&56(*&^^&";
$env = LOCAL; //change to WEB if you're live
if ($env == WEB) {
	$servername = "localhost";
	$username = "hinduyuvavahinir_gosafarionlineu";
	$password = "wf)87k6%(%x2";
	$dbname = "hinduyuvavahinir_gosafarionline"; //as in phpmyadmin
	$sitekey = '6LfLo6ocAAAAAIU9QOhT_GE1iStTskWQJP3lpOkG';
	$secretkey = '6LfLo6ocAAAAACJEEDKnHgcM5eNfHn1ulPxtQwGr';
} else {
	$servername = "localhost";
	$username = "root";
	$password = "";
	$dbname = "admin_security"; //as in phpmyadmin
	$sitekey = '6LeUIrcZAAAAAKEfGqiFsksFwZm8LrpZJINmOFK1';
	$secretkey = '6LeUIrcZAAAAAEmvQnvx0Fnoh3zlY8-rk7EfRf6Y';
}
// Create connection
$conn = mysqli_connect($servername, $username, $password, $dbname);
// Check connection
if ($conn) {
	//echo "Connected With DataBase";
} else {
	"Not Connected with Database ";
}
function sessioncheck()
{
	session_start();
	if ($_SESSION['id'] != 'HJKaf1_H&56(*&^^&') { //if this session id is not matched, means user not come from login page
		header("Location: index.php"); //send to login page
	}
}
?>
