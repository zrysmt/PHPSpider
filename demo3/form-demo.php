<?php 
	$userName = $_POST['userName'];
	$password = $_POST['password'];

	if($userName==="admin"&&$password==="admin"){
		echo "hello admin";
	}else{
		echo "login error";
	}
 ?>