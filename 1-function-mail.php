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
	$headers.="\nReturn-Path: info@steiner.cz";
	
	

	$title_email="TCweb";

	
	//$predmet=iconv("windows-1250","utf-8",$predmet);
	if($predmet):
		$predmet='=?UTF-8?B?'.base64_encode($predmet).'?=';
	endif;
	
	$obsah_cisty_text=strip_tags(str_replace("</p>","\n\r",$obsah));

	//////zasl�n� mailu u�ivateli
		$hlavicka="<!DOCTYPE HTML>
			<html lang=\"cs\"><head>".PHP_EOL."
			<meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\">".PHP_EOL."
			<title>".htmlspecialchars($title_email)."</title>".PHP_EOL."
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

		
		$zapati.="<br><br><br>STEINER, s.r.o.<br>
		D��insk� 477, 109 00  Praha 10<br>
		I�: 26488931, DI�: CZ26488931
		<br>".PHP_EOL."
		
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

function sendEmailPhpMailer(){
$msg = '';
if (array_key_exists('userfile', $_FILES)) {
	// First handle the upload
	// Don't trust provided filename - same goes for MIME types
	// See http://php.net/manual/en/features.file-upload.php#114004 for more thorough upload validation

    //Look at this again - AF
	$uploadfile1 = tempnam(sys_get_temp_dir(), sha1($_FILES['f1']['name']));
	$uploadfile2 = tempnam(sys_get_temp_dir(), sha1($_FILES['f2']['name']));
	if ((move_uploaded_file($_FILES['f1']['tmp_name'], $uploadfile1))||(move_uploaded_file($_FILES['f2']['tmp_name'],
			$uploadfile2))) {
		// Upload handled successfully
		// Now create a message
		// This should be somewhere in your include_path
		require './phpmailer/PHPMailerAutoload.php';
		$mail = new PHPMailer();
		$mail->setFrom('sosnovich@steiner.cz', 'First Last');
		$mail->addAddress('baros@steiner.cz', 'John Doe');
		$mail->Subject = 'PHPMailer file sender';
		$mail->msgHTML("My message body");
		// Attach the uploaded file
		if(isset($_FILES['f1']))
		$mail->addAttachment($uploadfile1, 'My uploaded file');
		if(isset($_FILES['f2']))
		$mail->addAttachment($uploadfile2, 'My uploaded file');

		if (!$mail->send()) {
			$msg .= "Mailer Error: " . $mail->ErrorInfo;
		} else {
			$msg .= "Message sent!";
		}
	} else {
		$msg .= 'Failed to move file to ' . $uploadfile1;
		$msg .= 'Failed to move file to ' . $uploadfile2;
	}
}

}




?>