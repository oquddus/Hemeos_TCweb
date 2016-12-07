<?php

//////////////////////////////Pro notifikace je to i v SOAP

//---zaslani emailu
function zasli_email($od, $komu, $predmet, $obsah){

	if(!$od):
		$od="info@steiner.cz";
	endif;
	if(!$komu): 
		$komu="info@steiner.cz";
	endif;

	
	$headers = "From: TCweb <".$od.">";
		
	$headers.="\nReply-To: ".$od;
	//$headers.="\nReturn-Path: info@steiner.cz";
	
	

	$title_email="TCweb";

	
	//$predmet=iconv("windows-1250","utf-8",$predmet);
	if($predmet):
		$predmet='=?UTF-8?B?'.base64_encode($predmet).'?=';
	endif;
	
	$obsah_cisty_text=strip_tags(str_replace("</p>","\n\r",$obsah));

	//////zaslání mailu uživateli
		$hlavicka="<!DOCTYPE HTML>
			<html lang=\"cs\"><head>".PHP_EOL."
			<meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\">".PHP_EOL."
			<title>$title_email</title>".PHP_EOL."
            <style type=\"text/css\">".PHP_EOL."
			body {
				margin:0;  background-color: #FFFFFF;
			}".PHP_EOL."

			.text {
				color: #262626;
				FONT-SIZE: 13px;
				text-align: justify;
				FONT-FAMILY: Verdana;
			}".PHP_EOL."
			
			.text-b{
				color: #262626;
				FONT-SIZE: 14px;
				text-align: justify;
				FONT-FAMILY: Verdana;
			}".PHP_EOL."
			
			.text-grey{
				color: #4a4949;
				FONT-SIZE: 11px;
				text-align: justify;
				FONT-FAMILY: Verdana;
			}".PHP_EOL."
			
			.text-grey2{
				color: #8f8f8f;
				FONT-SIZE: 13px;
				text-align: justify;
				FONT-FAMILY: Verdana;
			}".PHP_EOL."

			a:link {
				color: #262626;
				FONT-SIZE: 12px;
				FONT-FAMILY: Verdana;
				TEXT-DECORATION: underline;
			}".PHP_EOL."

			a:visited {
				color: #262626;
				FONT-SIZE: 12px;
				FONT-FAMILY: Verdana;
				TEXT-DECORATION: underline;
			}".PHP_EOL."

			a:hover  {
				color: #262626;
				FONT-SIZE: 12px;
				FONT-FAMILY: Verdana;
				TEXT-DECORATION: none;
			}".PHP_EOL."

			a:active  {
				color: #262626;
				FONT-SIZE: 12px;
				FONT-FAMILY: Verdana;
				TEXT-DECORATION: none;
			}".PHP_EOL."
			
			
			a.odkaz:link {
				color: #2995b0;
				FONT-SIZE: 12px;
				FONT-FAMILY: Arial;
				TEXT-DECORATION: none;
			}".PHP_EOL."

			a.odkaz:visited {
				color: #2995b0;
				FONT-SIZE: 12px;
				FONT-FAMILY: Arial;
				TEXT-DECORATION: none;
			}".PHP_EOL."

			a.odkaz:hover  {
				color: #2995b0;
				FONT-SIZE: 12px;
				FONT-FAMILY: Arial;
				TEXT-DECORATION: underline;
			}".PHP_EOL."

			a.odkaz:active  {
				color: #2995b0;
				FONT-SIZE: 12px;
				FONT-FAMILY: Arial;
				TEXT-DECORATION: underline;
			}".PHP_EOL."
   			</style>
			</head>
		
			<body>
  			<center>".PHP_EOL."
				<table border=\"0\" cellpadding=\"0\" width=\"98%\" cellspacing=\"0\" class=\"text\">".PHP_EOL."
				  <tr>
					<td width=\"100%\" bgcolor=\"#ffffff\"><br>".PHP_EOL;


		$zapati="";

		
		$zapati.="<br><br>------------------------------------------------------<br>This is an automated notification, please do not respond directly to this email.".PHP_EOL."
		
		<br><br>
					</td>
				  </tr>
				</table>".PHP_EOL."
  			</center>
			</body>

			</html>";

		//$obsah_cisty_text=iconv("windows-1250","utf-8",$obsah_cisty_text);
		$txt = $hlavicka.$obsah.$zapati;
		//$txt=iconv("windows-1250","utf-8",$txt);
		
		
		// boundary 
		$semi_rand = md5(time()); 
		$mime_boundary = "==Multipart_Boundary_x{$semi_rand}x"; 
	 
		// headers for attachment 
		$headers .= "\nMIME-Version: 1.0\n" . "Content-Type: multipart/alternative;\n" . " boundary=\"{$mime_boundary}\""; 
	 
		// multipart boundary 
		$message = "--{$mime_boundary}\n" . "Content-Type: text/plain; charset=\"utf-8\"\n" .
		"Content-Transfer-Encoding: 7bit\n\n" . $obsah_cisty_text . "\n\n"; 
		
		$message .= "--{$mime_boundary}\n" . "Content-Type: text/html; charset=\"utf-8\"\n" .
		"Content-Transfer-Encoding: 7bit\n\n" . $txt . "\n\n"; 

		
		$message .= "--{$mime_boundary}--";
		//$returnpath = "-f" . $od;
		
		
		//echo $od."<br>";	
		//echo $predmet."<br>";	
		//echo $komu."<br>";	
		//echo $txt;
		
		//$komu_utf=iconv("windows-1250","utf-8",$komu);
		
		$navrat = mail($komu, $predmet, $message, $headers); 
		//$navrat = mail($komu, $predmet, $message, $headers, $returnpath); 

		
		if($_SERVER['HTTP_HOST']=="127.0.0.1"):
			$navrat=1;
		endif;
		
		//ulozit adresu kam bylo odeslano
		if($navrat):
			mysql_query("INSERT INTO emaily_log (email, text, datum) VALUES
			('".mysql_real_escape_string($komu)."',
			'".mysql_real_escape_string($obsah)."',
			CURRENT_TIMESTAMP)");
		endif;
		
		
	return $navrat;

}






?>