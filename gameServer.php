<?php
	$code = array();
	$mysqli = new mysqli('localhost', 'root', '', 'mastermind');
	$opr = $_GET['opr'];
	
	header("Access-Control-Allow-Origin: *");
	
	if($opr == "start") 
	{	
		//When starting a new game...
		$user = $_GET['username'];
		//$gid = $_GET['gameID'];
		
		//Initialize game...
		$colours = array();
		$colours[0] = "B";	//Blue
		$colours[1] = "R";	//Red
		$colours[2] = "G";	//Green
			
		/*Generate the code randomly*/
		$code[0] = $colours[rand(0,2)];	
		$code[1] = $colours[rand(0,2)];	
		$code[2] = $colours[rand(0,2)];	
		
		$gid = time();	//unique game ID
		
		//Set COOKIES...
		/*setcookie("user", $user);
		setcookie("gid", $gid);
		setcookie("code", $code[0].$code[1].$code[2]);
		session_start();*/
		
		//file_put_contents($gid.".txt",  $code[0].";".$code[1].";".$code[2]);	//save the code to guess in a file
		
		$sql = "INSERT INTO games VALUES($gid, '$code[0];$code[1];$code[2]', '')";
		$mysqli->query($sql);
		
		echo $gid;
	}
	
	if($opr == "join")
	{
		//When joining an existing game...
		$user = $_GET['username'];
		$gid = $_GET['gid'];
		
		//$c = file_get_contents($gid.".txt");	//read from the file where the code is stored

		$sql = "SELECT code_to_guess FROM games WHERE game_id='$gid'";
		$codeQuery = $mysqli->query($sql);
		$row = $codeQuery->fetch_object();
		$c = $row->code_to_guess;
		
		$code[0] = $c[0];
		$code[1] = $c[2];
		$code[2] = $c[4];
		
		//echo "ok";
		
		//Set COOKIES...
		/*setcookie("user", $user);
		setcookie("gid", $gid);
		setcookie("code", $code[0].$code[1].$code[2]);
		session_start();*/
	}
	
	if($opr == "guess" || $opr == "update")
	{
		//Game is in progress, and the user has a guess...
		$guess = $_GET['g'];
		$user = $_GET['username'];
		$gid = $_GET['gid'];
		
		//Get the values of the COOKIES...
		/*$userCookie = $_COOKIE['user'];
		$gidCookie = $_COOKIE['gid'];
		$codeCookie = $_COOKIE['code'];*/
		
		$sql = "SELECT code_to_guess FROM games WHERE game_id='$gid'";
		$codeQuery = $mysqli->query($sql);
		$row = $codeQuery->fetch_object();
		$c = $row->code_to_guess;
		
		$code[0] = $c[0];
		$code[1] = $c[2];
		$code[2] = $c[4];
		
		$n = 0;	//number of correct guesses
		
		for($i = 0; $i < 3; $i++)
		{
			if($guess[$i] == $code[$i])
			{
				$n++;
			}
		}
		
		//file_put_contents("result".$gidCookie.".txt", "With ".$guess.", ".$userCookie." guessed ".$n." colour(s).\r\n", FILE_APPEND | LOCK_EX);
		//$content = file_get_contents("result".$gidCookie.".txt");
		//echo $content;
		
		if($opr == "guess")
		{
			//Updating the results...
			$sql = "UPDATE games SET results = CONCAT(results,'With $guess[0];$guess[1];$guess[2], $user guessed $n colour(s). ') WHERE game_id='$gid'";
			$mysqli->query($sql);
		}
		
		//Reading the results...
		$sql = "SELECT results FROM games WHERE game_id='$gid'";
		$resultQuery = $mysqli->query($sql);
		$row = $resultQuery->fetch_object();
		$res = $row->results;
		echo $res;
	}
	
	if($opr == "check")
	{
		$gid = $_GET['gid'];
		//Reading the results...
		$sql = "SELECT results FROM games WHERE game_id='$gid'";
		$resultQuery = $mysqli->query($sql);
		$row = $resultQuery->fetch_object();
		$res = $row->results;
		if($res == "")
		{
			echo "";
		}
		else
		{
			echo $res;
		}
	}
?>
