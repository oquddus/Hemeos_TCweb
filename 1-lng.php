<?php

//--funkce pro preklad textu
function gtext($gtext,$gt,$vynuceny_jazyk=""){

	//--preklad pro ...
	if(($_SESSION['jazyk']=="_en" && !$vynuceny_jazyk) || $vynuceny_jazyk=="_en"):
	
		switch ($gt):
		
			case "1": $gtext="Project"; break;
			case "2": $gtext="Select project"; break;
			case "3": $gtext="Priority"; break;
			case "4": $gtext="low"; break;
			case "5": $gtext="medium"; break;
			case "6": $gtext="high"; break;
			case "7": $gtext="User"; break;
			case "8": $gtext="Item selection"; break;
			case "9": $gtext="Request subject"; break;
			case "10": $gtext="Request"; break;
			case "11": $gtext="Files"; break;
			case "12": $gtext="Admin comment"; break;
			case "13": $gtext="Technicians"; break;
			case "14": $gtext="Technician feedback"; break;
			case "15": $gtext="Proposed  solution"; break;
			case "16": $gtext="Solution date"; break;
			case "17": $gtext="Status"; break;
			case "18": $gtext="Solution type"; break;
			case "19": $gtext="Solution mark"; break;
			case "20": $gtext="none"; break;
			case "21": $gtext="excellent"; break;
			case "22": $gtext="deficiently"; break;
			case "23": $gtext="to"; break;
			case "24": $gtext="Solved"; break;
			case "25": $gtext=""; break;
			case "26": $gtext="Related requests"; break;
			case "27": $gtext="Generate related request"; break;
			case "28": $gtext="Submit"; break;
			case "29": $gtext="Report was submitted"; break;
			case "30": $gtext="Report was not submitted"; break;
			case "31": $gtext="Report was modified"; break;
			case "32": $gtext="Report was not modified"; break;
			case "33": $gtext="Please, complete the item"; break;
			case "34": $gtext="History of request"; break;
			case "35": $gtext="Forum about request"; break;
			case "36": $gtext="Message"; break;
			case "37": $gtext="Internal"; break;
			case "38": $gtext="Requests in progress"; break;
			case "39": $gtext="Completed  requests"; break;
			case "40": $gtext="Insert request"; break;
			case "41": $gtext="Requests"; break;
			case "42": $gtext="Personal settings"; break;
			case "43": $gtext="User"; break;
			case "44": $gtext="Logout"; break;
			case "45": $gtext="Date of entry from"; break;
			case "46": $gtext="Search"; break;
			case "47": $gtext="Filter by client"; break;
			case "48": $gtext="Filter by technician"; break;
			case "49": $gtext="Added"; break;
			case "50": $gtext="Client"; break;
			case "51": $gtext="Technicians"; break;
			case "52": $gtext="Action"; break;
			case "53": $gtext="Edit"; break;
			case "54": $gtext="Print"; break;
			case "55": $gtext="Request was submitted"; break;
			case "56": $gtext="New request"; break;
			case "57": $gtext="user"; break;
			case "58": $gtext="inserted new request"; break;
			case "59": $gtext="Assigned technician"; break;
			case "60": $gtext="You have been chosen for the solving this request"; break;
			case "61": $gtext="Solving request"; break;
			case "62": $gtext="Request"; break;
			case "63": $gtext="has been solved"; break;
			case "64": $gtext="Request to ammend data"; break;
			case "65": $gtext="technician"; break;
			case "66": $gtext="Return request to the admin"; break;
			case "67": $gtext="was returned to the admin"; break;
			case "68": $gtext="Return request to the technician"; break;
			case "69": $gtext="was returned to the technician"; break;
			case "70": $gtext="Ammendment of request by user"; break;
			case "71": $gtext="was ammended by user"; break;
			case "72": $gtext="Forum about the request"; break;
			case "73": $gtext="A comment was added to request"; break;
			case "74": $gtext="on the forum"; break;
			case "75": $gtext="You can see the whole request here"; break;
			case "76": $gtext="Inserted post to the forum"; break;
			case "77": $gtext="Please enter your login info"; break;
			case "78": $gtext="Your user name or password are not correct"; break;
			case "79": $gtext="Repeated incorrect login. Your account has been blocked"; break;
			case "80": $gtext="You must be logged in for access to this area"; break;
			case "81": $gtext="You don´t have permission for access to this area"; break;
			case "82": $gtext="requests an ammendment of information"; break;
			case "83": $gtext="Dear Mr./Mrs."; break;
			case "84": $gtext="New password"; break;
			case "85": $gtext="Password again"; break;
			case "86": $gtext="Name"; break;
			case "87": $gtext="Surname"; break;
			case "88": $gtext="Firm"; break;
			case "89": $gtext="Phone"; break;
			case "90": $gtext="Sort requests by"; break;
			case "91": $gtext="Logged user"; break;
			case "92": $gtext="Date (default setting)"; break;
			case "93": $gtext="Adapt application size to windows resolution"; break;
			case "94": $gtext="Login"; break;
			case "95": $gtext="Password"; break;
			case "96": $gtext="There are no inserted requests."; break;
			
			case "97": $gtext="Default language"; break;
			case "98": $gtext="czech"; break;
			case "99": $gtext="english"; break;
			
			case "100": $gtext="Technician feedback update"; break;
			case "101": $gtext="was updated in technician feedback"; break;
			
			case "102": $gtext="Technical support"; break;
			case "103": $gtext="All rights reserved"; break;
			case "104": $gtext="Fill your login and password, please"; break;
			case "105": $gtext="Login or password is not correct"; break;
			case "106": $gtext="Your account was blocked due to many incorrect log-in"; break;
			case "107": $gtext="You have to be logged to access this area"; break;
			case "108": $gtext="You are not allowed to access this area"; break;
			
			case "109": $gtext="Version"; break;
			
			case "110": $gtext="Abbr."; break;
			case "111": $gtext="Project"; break;
			case "112": $gtext="Project group"; break;
			case "113": $gtext="Contributions"; break;
			case "114": $gtext="Action"; break;
			case "115": $gtext="Open topic"; break;
			case "116": $gtext="Topic"; break;
			case "117": $gtext="Author"; break;
			case "118": $gtext="Date"; break;
			case "119": $gtext="reply"; break;
			case "120": $gtext="Insert contribution"; break;
			case "121": $gtext="New contribution"; break;
			case "122": $gtext="Name"; break;
			case "123": $gtext="Contrib. name"; break;
			case "124": $gtext="Text"; break;
			case "125": $gtext="Response to"; break;
			
			case "126": $gtext="Title"; break;
			case "127": $gtext="READ"; break;
			case "128": $gtext="Category"; break;
			
			case "129": $gtext="Solving request in a new version"; break;
			case "130": $gtext="will be solved in a new version"; break;
			
			case "131": $gtext="Solving request - need acceptation"; break;
			case "132": $gtext="is solved. Please, let us know your acceptation."; break;
			case "133": $gtext="Solving request - accepted by client"; break;
			case "134": $gtext="is solved and acceptedd by client"; break;
			case "135": $gtext="Solving reques - will be in a new version"; break;
			case "136": $gtext="is solved and will be in a new version"; break;
			
			case "137": $gtext="Period from"; break;
			case "138": $gtext="Filter by project"; break;
			
			case "139": $gtext="Admin comment change"; break;
			case "140": $gtext="has a new admin comment"; break;
			case "141": $gtext=""; break;
			case "142": $gtext=""; break;
			case "143": $gtext=""; break;
			case "144": $gtext=""; break;
			case "145": $gtext=""; break;
			case "146": $gtext=""; break;
			case "147": $gtext=""; break;
			case "148": $gtext=""; break;
			case "149": $gtext=""; break;
			case "150": $gtext=""; break;
			
		endswitch;
	
	endif;


	

	
	return $gtext;
}

?>