<?
header('Content-type: text/html; charset=windows-1250');

//--vytvoøení session
session_start();

require "opendb.php";



//--Ajaxem zaslane post promenne jsou v utf8 (nebere to vnucene win kodovani), proto prevod
foreach ($_POST as $key=>$value):
    $_POST[$key]=iconv('UTF-8', 'windows-1250',$value);
endforeach;

?>