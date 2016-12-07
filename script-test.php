<?php
//----------cast nastaveni
ini_set('session.use_only_cookies', 1); //pouzivat pouze cookies
ini_set('session.use_trans_sid', 0); //automaticky nedoplnovat PHPSESSID


//--vytvoøení session
session_start(); 

echo "Login: ".$_SESSION['usr_login'];

?>