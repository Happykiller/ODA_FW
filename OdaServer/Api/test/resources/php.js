var tabSetting = { };
var tabInput = { };
var retour = $.functionsLib.callRest(domaine+"API/test/test.php", tabSetting, tabInput);

test( "[PHP]Test", function() {
    var details = retour["data"]["details"];
    for(var indice in details){
        for(var indiceRetour in details[indice]["retour"]){
            equal(details[indice]["retour"][indiceRetour]["statut"], "OK", "Test - " + details[indice]["name"] + " : " + details[indice]["retour"][indiceRetour]["message"] );
        }
    }
});