<?
mysql_connect("localhost","prometheus","98sj7dnjsy3hLwP6ns@Ds*");
mysql_select_db("476_prometheus");

mysql_query("SET @ID_upravil_usr = ".$_SESSION['usr_ID']);

mysql_query("SET character_set_client=cp1250");
mysql_query("SET character_set_connection=utf8");
mysql_query("SET character_set_results=cp1250");


//Vytvoreni mysqli objeku
$mysqli = new mysqli("localhost", "prometheus","98sj7dnjsy3hLwP6ns@Ds*","476_prometheus");
$mysqli->set_charset("utf8");
?>
