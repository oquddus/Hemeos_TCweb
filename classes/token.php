<?
class Token{
	
	public $pocet_mist=10;
	
	public function __construct(){
	
		$this->deleteOldTokens();
		
	}
	
	
	//* =============================================================================
	//	generuje token a zapise k uzivateli do db
	//============================================================================= */
	public function getToken(){
	
		$token="";

		for($i=1;$i<=$this->pocet_mist;$i++){

			//48-57 jsou cisla, 97-122 pismena
			$cislo=rand(48,82);

			if($cislo<=57){
				$token.=chr($cislo);
			}else{
				$token.=chr($cislo-57+97);
			}

		}
		
		mysql_query("INSERT INTO user_token (ID_user, token) VALUES
			('".$_SESSION['usr_ID']."',
			'".mysql_real_escape_string($token)."')");

		return $token;
	
	}
	
	
	//* =============================================================================
	//	pouzit token - overi platnost tokenu pro uzivatele a vymazat z db
	//============================================================================= */
	public function useToken($token){
		
		if(!empty($token)){

			$token_nalezen = mysql_result(mysql_query("SELECT COUNT(*) FROM user_token WHERE ID_user='".$_SESSION['usr_ID']."' AND token='".mysql_real_escape_string($token)."'"), 0);

			if($token_nalezen){
				//odstranit z db
				mysql_query("DELETE FROM user_token WHERE ID_user='".$_SESSION['usr_ID']."' AND token='".mysql_real_escape_string($token)."' LIMIT 1");

				return true;
			}else{
				return false;
			}
			
		}else{
			return false;
		}
		
	}
	
	
	//* =============================================================================
	//	odebrat stare nepouzite tokeny
	//============================================================================= */
	public function deleteOldTokens(){

		mysql_query("DELETE FROM user_token WHERE date_creation<=NOW() - INTERVAL 1 DAY");
		
	}
	
	

}

?>