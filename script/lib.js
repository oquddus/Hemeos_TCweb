$(document ).ready(function() {
    $("#doDelete").click(function() {
        return potvrd('Do you really want to remove this item?');
    });
    $("#doDelete").click(function() {
        return potvrd('Do you really want to remove this item?');
    });
    $("#doDelete").click(function() {
        return potvrd('Do you really want to remove this item?');
    });
    $("#log-out").click(function() {
        return potvrd('Do you really want to logout?')
    });

    $("#B1").click(function(e) {
        set_temp_form(0);
    });
    $("#B2").click(function(e) {
        set_temp_form(1);
    });

    function set_temp_form(prom){
        $('#temp_data').val(prom);
    }



    function minimize(idcko)
    {

        co=document.getElementById(idcko);

        if(co.style.display!="none"){
            co.style.display="none";
        }else{
            co.style.display="block";
        }

    }



});

