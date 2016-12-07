$(document).ready(function(){


	$('#persistTable').dragtable({ dragHandle:'.posuvnik', persistState: function(table) {
	     table.el.find('th').each(function(i) {
	       
			if(this.id != '') {
			  table.sortOrder[this.id]=i;
			}
		});

		$.ajax({url: 'ajax-sloupce.php?action=zapis', data: table.sortOrder}); 
	     }
	}); 


	
	$( "#ajax_div" ).draggable({ opacity: 0.5, handle: 'thead'});


	$("#more_firmy").click(function(){
	    $("#filtr_firmy").slideToggle(100);
	});


	$("#more_resitele").click(function(){
	    $("#filtr_resitele").slideToggle(100);
	});
	
	
	
	
	$("#column_select").click(function(){
	    $("#div_column_select").toggle();
	});

	
	
	$(".filter_column").click(function(){
		
		$idcko=$(this).attr('id');
		$(".div_column_filter").not("#filter_"+$idcko).hide();	//vsechny nejdriv zavrit
	    $("#filter_"+$idcko).toggle();
	});
	
	$(".filter_close").click(function(){
	    $(".div_column_filter").hide();
	});
	
    
});
