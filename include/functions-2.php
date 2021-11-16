<?php

function getIpAddr()
{
	if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
		$ipAddr = $_SERVER['HTTP_CLIENT_IP'];
	} elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
		$ipAddr = $_SERVER['HTTP_X_FORWARDED_FOR'];
	} else {
		$ipAddr = $_SERVER['REMOTE_ADDR'];
	}
	return $ipAddr;
}

function getTry($ip, $conn)
{
	$sql = "SELECT try FROM logintry WHERE ip='$ip'";
	$result = mysqli_query($conn, $sql); //fire query to the mysql DB
	$row = mysqli_fetch_assoc($result);
	return $row['try'];
}


function timediffmiuntes($ip, $ctime, $conn)
{
	$sql = "SELECT ltime FROM logintry WHERE ip='$ip'";
	$result = mysqli_query($conn, $sql); //fire query to the mysql DB
	$row = mysqli_fetch_assoc($result);
	$oldtime =  $row['ltime'];
	$tdiff = $ctime - $oldtime;
	$minutes = $tdiff / 60 % 60;
	return $minutes;
}

function generateStrongPassword($length = 9, $add_dashes = false, $available_sets = 'luds')
{
	$sets = array();
	if (strpos($available_sets, 'l') !== false)
		$sets[] = 'abcdefghjkmnpqrstuvwxyz';
	if (strpos($available_sets, 'u') !== false)
		$sets[] = 'ABCDEFGHJKMNPQRSTUVWXYZ';
	if (strpos($available_sets, 'd') !== false)
		$sets[] = '23456789';
	if (strpos($available_sets, 's') !== false)
		$sets[] = '!@#$%&*?';

	$all = '';
	$password = '';
	foreach ($sets as $set) {
		$password .= $set[array_rand(str_split($set))];
		$all .= $set;
	}

	$all = str_split($all);
	for ($i = 0; $i < $length - count($sets); $i++)
		$password .= $all[array_rand($all)];

	$password = str_shuffle($password);

	if (!$add_dashes)
		return $password;

	$dash_len = floor(sqrt($length));
	$dash_str = '';
	while (strlen($password) > $dash_len) {
		$dash_str .= substr($password, 0, $dash_len) . '-';
		$password = substr($password, $dash_len);
	}
	$dash_str .= $password;
	return $dash_str;
}


function findip($ip, $conn)
{
	$sql = "SELECT * FROM logintry WHERE ip='$ip'";
	$result = mysqli_query($conn, $sql); //fire query to the mysql DB
	$count = mysqli_num_rows($result);
	return $count;
}

function ipexist($ip, $conn)
{
	$sql = "SELECT ip FROM logintry WHERE ip='$ip'";
	$result = mysqli_query($conn, $sql); //fire query to the mysql DB
	$count = mysqli_num_rows($result);
	return $count;
}

//logout if user is inactive for a time period
function set_timeout()
{
	$expiretime = 30; // Session expire if inactive user
	$_SESSION['last_activity'] = time(); //your last activity was now, having logged in.
	$_SESSION['expire_time'] = $expiretime * 60; //expire time in minutes: three hours 3*60*60 (you must change this)
}

function session_timeout()
{
	if ($_SESSION['last_activity'] < time() - $_SESSION['expire_time']) { //have we expired?
		//redirect to logout.php
		header('Location: logout.php'); //change yoursite.com to the name of you site!!
	} else { //if we haven't expired:
		$_SESSION['last_activity'] = time(); //this was the moment of last activity.
	}
}
