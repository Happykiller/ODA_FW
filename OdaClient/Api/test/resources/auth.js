test( "[WEBAPP]login", function() {
    $.functionsLib.logout();
    
    deepEqual( $.functionsLib.login("VIS","VIS","index.html"), { statut : "ok", message : "VIS"}, "Test OK : Passed! (Bon code user)" );
    $.functionsLib.logout();
    
    deepEqual( $.functionsLib.login("VIdfsdfS","sdfsdf","index.html"), { statut : "ko", message : "Auth impossible."}, "Test KO : Passed! (Mauvais code user)" );
    $.functionsLib.logout();
});

test( "[WEBAPP]checkAuth", function() {
    id_page = 0;
    
    //Test 1
    $.functionsLib.logout();
    var customWindow = {
        location : {
            pathname : "/page_home.html",
            href : "http://localhost/page_home.html"
        }
    };
    $.functionsLib.currentWindow = customWindow;
    $.functionsLib.pageName = "page_home.html";
    var testCheckAuthKO = function()
    {
        var strRetour = $.functionsLib.testAuthentification();
        if(!strRetour) {
            var strLocation = $.functionsLib.currentWindow.location;
            if(strLocation.indexOf("index.html") >= 0){
                return true;
            } else {
                return false;
            }
        }else{
            return false;
        }
    };
    equal(testCheckAuthKO(), true, "Test KO : Passed! (Pas de cookie, pas de var dans l'url)");
    $.functionsLib.logout();
    
    //Test 2
    $.functionsLib.logout();
    var customWindow = {
        location : {
            pathname : "/page_home.html",
            href : "http://localhost/page_home.html"
        }
    };
    $.functionsLib.currentWindow = customWindow;
    $.functionsLib.pageName = "page_home.html";
    $.functionsLib.setCookie("auth","VIS");
    $.functionsLib.setCookie("key","42c643cc44c593c5c2b4c5f6d40489dd");
    equal( $.functionsLib.testAuthentification(), true, "Test OK : Passed! (Avec un bon cookie)" );
    $.functionsLib.logout();
    
    //Test 3
    $.functionsLib.logout();
    var customWindow = {
        location : {
            pathname : "/page_home.html",
            href : "http://localhost/page_home.html?getUser=VIS&getPass=VIS"
        }
    };
    $.functionsLib.currentWindow = customWindow;
    $.functionsLib.pageName = "page_home.html";
    equal( $.functionsLib.testAuthentification(), true, "Test OK : Passed! (Avec bon param dans l'url)" ); 
    $.functionsLib.logout();
    
    //Test 4
    $.functionsLib.logout();
    var customWindow = {
        location : {
            pathname : "/page_home.html",
            href : "http://localhost/page_home.html?getUser=NO&getPass=NO"
        }
    };
    $.functionsLib.currentWindow = customWindow;
    $.functionsLib.pageName = "page_home.html";
    var testCheckAuthKOGET = function()
    {
        var strRetour = $.functionsLib.testAuthentification();
        if(!strRetour) {
            var strLocation = $.functionsLib.currentWindow.location;
            if(strLocation.indexOf("index.html") >= 0){
                return true;
            } else {
                return false;
            }
        }else{
            return false;
        }
    };
    equal( testCheckAuthKOGET(), true, "Test KO : Passed! (Avec mauvais param dans l'url)" ); 
    $.functionsLib.logout();
    
    delete id_page;
});

test( "[REST]getAuth", function() {
    var tabSetting = { };
    var tabInput = { login : "VIS", mdp : "VIS" };
    var retour = $.functionsLib.callRest(domaine+"API/phpsql/getAuth.php", tabSetting, tabInput);
    deepEqual( retour["data"]["resultat"]["profile"], "99", "Test OK : Passed! (Avec bon log, pass)" ); 
    
    var tabSetting = { };
    var tabInput = { login : "badLog", mdp : "badPass" };
    var retour = $.functionsLib.callRest(domaine+"API/phpsql/getAuth.php", tabSetting, tabInput);
    equal( retour["strErreur"], "Auth impossible.", "Test KO : Passed! (Avec mauvais log, pass)" );
});

test( "[REST]getAuthInfo", function() {
    var tabSetting = { };
    var tabInput = { code_user : "VIS" };
    var retour = $.functionsLib.callRest(domaine+"API/phpsql/getAuthInfo.php", tabSetting, tabInput);
    deepEqual( retour["data"]["resultat"]["profile"], "99", "Test OK : Passed! (Avec code user existant)" ); 
    
    var tabSetting = { };
    var tabInput = { code_user : "badLog" };
    var retour = $.functionsLib.callRest(domaine+"API/phpsql/getAuthInfo.php", tabSetting, tabInput);
    equal( retour["data"]["resultat"], false, "Test KO : Passed! (Avec code user inconnu)" );
});