</div>
		</div>
	</div></div>
<div class="stin-dole" style="width:<? echo $width5; ?>px"></div>
</div>



</div>
</div>

<?
//* =============================================================================
//	AJAX multibox obsluha
//============================================================================= */
if($cil_text):

	echo "<form onsubmit=\"return false;\" action=\"\" name=\"ajax_formular\">
		<div class=\"selectbox2\" id=\"ajax_div\" style=\"display:none;\">
			<table id=\"table-ajax\" cellspacing=\"0\" width=\"386\">
				<thead class=\"cursor-move\">
				  <tr>
					<td class=\"td1\"></td>
					<td class=\"td2\" width=\"340\" style=\"text-align:left;\">".gtext('V�b�r polo�ek',8)."</td>
					<td class=\"td3\"><div class=\"td-min\"><a href=\"javascript:minimize('ajax_div'); hide_it('meziprostor');"; if($ID_stranky==333 || $ID_stranky==444): echo " document.formular_hledej.submit();"; endif; echo "\"><img src=\"img/table/close.gif\" border=\"0\" width=\"21\" height=\"28\" alt=\"\"></a></div></td>
				  </tr>
				</thead>
			</table>
			<div class=\"selectbox3\"><div class=\"selectbox4\">
				<div style=\"float:left; text-align:left;\" id=\"ajax_form\"></div>
				<div class=\"selectbox5\"><input type=\"submit\" class=\"form-send\" onclick=\"minimize('ajax_div'); hide_it('meziprostor');"; if($ID_stranky==333 || $ID_stranky==444): echo " document.formular_hledej.submit();"; endif; echo "\" name=\"B1\" value=\"\"></div>
			</div></div>

		</div>

	</form>";



	echo "<SCRIPT language=\"JavaScript\" type=\"text/javascript\">

		//pomocna promenna vyuzita v pripade, kdyz chci zjistit, s cim neco delam
		var cil_id_pomocny;

				//funkce vytvori seznam id, zapise a vola fci pro ziskani textovych hodnot
				function ajax_idcka(id_act, id_checkboxu){

					var cil_id=document.ajax_formular.cil_id.value;
					var cil_form=document.ajax_formular.cil_form.value;

					var cil_text=document.ajax_formular.cil_text.value;
					var zpusob=document.ajax_formular.zpusob.value;
					var tab=document.ajax_formular.tab.value;
					var sl=document.ajax_formular.sl.value;
					var podrazeny_form=document.ajax_formular.podrazeny_form.value;

					var seznam_id=document.forms[cil_form].elements[cil_id].value;


					if(document.getElementById(id_checkboxu).type == \"checkbox\"){
						if(document.getElementById(id_checkboxu).checked){
							var novy_seznam_id=seznam_id+\"|\"+id_act;
							document.forms[cil_form].elements[cil_id].value=novy_seznam_id;

							ajax_hodnoty(cil_form, cil_id, cil_text, tab, sl, zpusob, podrazeny_form);

						}else{
							delAjItem(id_act, cil_form, cil_id, cil_text, tab, sl, zpusob, podrazeny_form);
						}
					}else{
							var novy_seznam_id=id_act;
							document.forms[cil_form].elements[cil_id].value=novy_seznam_id;

							ajax_hodnoty(cil_form, cil_id, cil_text, tab, sl, zpusob, podrazeny_form);
					}

				}


				//funkce pro ziskani textovych hodnot dle seznamu id
				function ajax_hodnoty(cil_form, cil_id, cil_text, tab, sl, zpusob, podrazeny_form){

					var idcka=document.forms[cil_form].elements[cil_id].value;

					//do pomocne globalni promenne ulozim info o tom, s jakym prvkem co delam
					cil_id_pomocny=cil_id;

					showHint2('1-add2.php?tab='+tab+'&sl='+sl+'&zpusob='+zpusob+'&idcka='+idcka+'&cil_form='+cil_form+'&cil_id='+cil_id+'&cil_text='+cil_text+'&podrazeny_form='+podrazeny_form, cil_text, zpusob, 'akce1');

				}



				function delAjItem(id_pryc, cil_form, cil_id, cil_text, tab, sl, zpusob, podrazeny_form){

					var novy_seznam=\"\";
					var obsah=document.forms[cil_form].elements[cil_id].value;

					var brokenstring=obsah.split(\"|\");

						for(var i=0; i<brokenstring.length;i++ ){
							if(brokenstring[i]!=id_pryc){
								novy_seznam=novy_seznam+\"|\"+brokenstring[i];
							}
						}

					document.forms[cil_form].elements[cil_id].value=novy_seznam;


					if(podrazeny_form!=\"\"){

						var brokenstring=podrazeny_form.split(\", \");

						for(var i=0; i<brokenstring.length;i++ ){
							var ajax_text_div=cil_form+brokenstring[i];
							document.forms[cil_form].elements[brokenstring[i]].value=\"\";
							document.getElementById(ajax_text_div).innerHTML=\"\";
						}

					}

					ajax_hodnoty(cil_form, cil_id, cil_text, tab, sl, zpusob, podrazeny_form);

				}



				function ajax_obarvit(id_checkboxu, id_radku, barva){
					if(document.getElementById(id_checkboxu).type == \"checkbox\"){
						if(document.getElementById(id_checkboxu).checked){
							document.getElementById(id_radku).className='barva3';
						}else{
							document.getElementById(id_radku).className=barva;
						}
					}else{
						//radek_on(id_radku,barva);
					}
				}

			</SCRIPT>";

endif;
//--konec AJAX multibox obsluhy
////////////////////////////////////////////////
?>
<?
//* =============================================================================
//	JS obsluha kalendare
//============================================================================= */
		$mesic=Date("n");
		$rok=Date("Y");

		//--JS pro kalendar
		echo "<SCRIPT language=\"JavaScript\" type=\"text/javascript\">

			function get_calendar(nazev_inputu,form_name){
				minimize('iddivu_kalendar');
				showHint('1-kalendar0.php?mesic=$mesic&rok=$rok&nazev_inputu='+nazev_inputu+'&form_name='+form_name,'crm_kalendar','1');
			}

			function crm_kalendar(datum,nazev_inputu,form_name){
				minimize('iddivu_kalendar');
				minimize('meziprostor_kalendar');
				document.forms[form_name].elements[nazev_inputu].value=datum;

					//po zadani data lze volat doplnujici fci,pokud existuje
					if(typeof next_fce_kalendar=='function'){
						next_fce_kalendar(nazev_inputu);
					}
			}
		</SCRIPT>";

?>

<div class="patka"><div class="patka-svetlo">
	<div class="patka-vnitrek text4">
		<div class="patka-text1"><span class="text3 bold">Hemeos, LLC</span><br><br>Tel.: +1 8559436704<br>email: <a href="mailto:registration@hemeos.com" class="odkaz1">registration@hemeos.com</a></div>
		<div class="patka-text2"><span class="text3 bold"></div>
		<div class="patka-text3">TC Portal<br><br>Beta Version 1.0<br>www.hemeos.com</div>
	</div>
</div></div>


<script type="text/javascript" src="script/lib.js"></script>


<!--Start of Tawk.to Script-->
<script type="text/javascript">
var Tawk_API=Tawk_API||{}, Tawk_LoadStart=new Date();
(function(){
var s1=document.createElement("script"),s0=document.getElementsByTagName("script")[0];
s1.async=true;
s1.src='https://embed.tawk.to/586d0e588f538671f4627811/default';
s1.charset='UTF-8';
s1.setAttribute('crossorigin','*');
s0.parentNode.insertBefore(s1,s0);
})();
</script>
<!--End of Tawk.to Script-->
</BODY>
</HTML>
