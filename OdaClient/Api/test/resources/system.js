test( "[WEBAPP]checkMaintenance", function() {
    //Test si maintenance (pas maintenance)
    var customWindow = {
        location : {
            pathname : "/page_home.html",
            href : "http://localhost/page_home.html"
        }
    };
    $.functionsLib.currentWindow = customWindow;
    var testCheckMaintenanceOK = function()
    {
        var boolRetour = $.functionsLib.testMaintenance();
        if(boolRetour == true)
        {
            return true;
        }else{
            return false;
        }
    };
    equal(testCheckMaintenanceOK(), true, "Test OK : Passed! (avec la base présente et maintenance à 0" );
    
    //Test si maintenance (maintenance)
    $.functionsLib.setParameter("maintenance", 1);
    var customWindow = {
        location : {
            pathname : "/page_home.html",
            href : "http://localhost/page_home.html"
        }
    };
    $.functionsLib.currentWindow = customWindow;
    var testCheckMaintenanceKO = function()
    {
        var boolRetour = $.functionsLib.testMaintenance();
        if(boolRetour == false)
        {
            var strLocation = $.functionsLib.currentWindow.location;
            if(strLocation == "api_page_maintenance.html"){
                return true;
            } else {
                return false;
            }
        }else{
            return false;
        }
    };
    equal(testCheckMaintenanceKO(), true, "Test KO : Passed! (avec la base absente ou maintenance à 1" );
    $.functionsLib.setParameter("maintenance", 0);
});

test( "[WEBAPP]log", function() {
    var retour = $.functionsLib.log(0,"Test function log JS");
    ok(retour, true, "Test OK : Passed! (0 et salut)" );
});

test( "[REST]exemple", function() {
    var tabSetting = { };
    var tabInput = { test : 'ok' };
    var retour = $.functionsLib.callRest(domaine+"API/phpsql/exemple.php", tabSetting, tabInput); 
    
    ok(($.functionsLib.whatIsIt(retour) == "Object"), "Test OK : Passed!" );
});

test( "[REST]test_secu", function() {
    $.functionsLib.logout();
    var customWindow = {
        location : {
            pathname : "/page_home.html",
            href : "http://localhost/page_home.html"
        }
    };
    $.functionsLib.currentWindow = customWindow;
    $.functionsLib.pageName = "page_home.html";
    
    var tabSetting = { };
    var tabInput = { keyAuthODA : "42c643cc44c593c5c2b4c5f6d40489dd" };
    var retour = $.functionsLib.callRest(domaine+"API/phpsql/tests/test_secu.php", tabSetting, tabInput);
    equal(retour["data"]["resultat"]["param_type"], "int", "Test OK : Passed! (avec key)" );
    
    var tabSetting = { };
    var tabInput = { keyAuthODA : 'badkey' };
    var retour = $.functionsLib.callRest(domaine+"API/phpsql/tests/test_secu.php", tabSetting, tabInput);
    equal(retour["strErreur"], "Key auth invalid.", "Test KO : Passed! (key : badkey)" );
    
    var tabSetting = { };
    var tabInput = { keyAuthODA : '' };
    var retour = $.functionsLib.callRest(domaine+"API/phpsql/tests/test_secu.php", tabSetting, tabInput);
    equal(retour["strErreur"], "Key auth empty.", "Test KO : Passed! (key : vide)" );
});

test( "[WEBAPP]sendMail", function() {
    var retour = $.functionsLib.sendMail({email_mails_dest:'fabrice.rosito@gmail.com',message_html:'HelloContent',sujet:'HelloSujet'});
    
    ok(($.functionsLib.whatIsIt(retour) == "Object"), "Test OK : Passed!" );
});

test( "[WEBAPP]uploadFile", function() {
    var formData = new FormData();
    var content = '<a id="a"><b id="b">hey!</b></a>';
    var blob = new Blob([content], { type: "text/xml"});
    formData.append("webmasterfile", blob, "blob.txt");

    var retour = $.functionsLib.sendFile(formData, "", "truc.txt");
    
    ok(($.functionsLib.whatIsIt(retour) == "Object"), "Test OK : Passed!" );
});