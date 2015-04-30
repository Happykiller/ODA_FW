///////////////////
//BLOCK VARIABLE GLOBAL
///////////////////
var colorCard = {
    Commune : "#848484",
    Commune_inv : "#B0B0B0",
    Rare : "#2E2EFE",
    Rare_inv : "#5A5AFF",
    Épique : "#8904B1",
    Épique_inv : "#AA61C1",
    Légendaire : "#FF8000",
    Légendaire_inv : "#FFB163"
};
var colorClasse = {
    Death_Knight : "#C41F3B",
    Druide : "#FF7D0A",
    Chasseur : "#ABD473",
    Mage : "#69CCF0",
    Moine : "#00FF96",
    Paladin : "#F58CBA",
    Prêtre : "#FFFFFF",
    Voleur : "#FFF569",
    Chaman : "#0070DE",
    Démoniste : "#9482C9",
    Guerrier : "#C79C6E"
};
var chosenClass = "";

var tab_sets = ['Tous', 'Expert' , 'La Malédiction de Naxxramas', 'Gobelins et Gnomes', 'Mont Rochenoire'];

/**
 * @name afficherMeta
 * @desc Affiche la répartition de la meta
 * @param {type} p_div
 * @param {type} p_dbDatas
 * @returns {undefined}
 */
function afficherMeta(p_titre, p_div, p_json_retour){
    try {
        var pieDbDatas = p_json_retour["data"]["resultats"]["data"];
        var pieDatas = new Array();
        for (var indice in pieDbDatas) {
            pieDatas[pieDatas.length] = {
                name : pieDbDatas[indice]["classe_adversaire"],
                color : colorClasse[pieDbDatas[indice]["classe_adversaire"]],
                y : parseInt(pieDbDatas[indice]["nb"])
            };
        }

        // Create the chart
        $(p_div).highcharts({
            chart: {
                type: 'pie',
                backgroundColor:'rgba(255, 255, 255, 0.1)'
            },
            exporting: {
                enabled: false
            },
            title: {
                text: p_titre
            },
            plotOptions: {
                pie: {
                    shadow: true,
                    center: ['50%', '50%']
                }
            },
            tooltip: {
                formatter: function() {
                    var s;
                    s = this.point.name +' : '+$.functionsLib.arrondir(this.point.percentage,2)+"% ("+this.y+")";
                    return s;
                }
            },
            series: [{
                name: 'Classe',
                data: pieDatas,
                dataLabels: {
                    formatter: function() {
                        var s;
                        if (this.point.percentage > 2) { // the pie chart
                            s = this.point.name +':'+$.functionsLib.arrondir(this.point.percentage,2)+"%";
                        }
                        return s;
                    }
                }
            }]
        });
        var nb = parseInt(p_json_retour["data"]["resultat"]["nb"]);
        $(p_div).after('<label style="font-size:70%;color:#BDBDBD;">From:'+nb+'</label>');
    } catch (er) {
        $.functionsLib.log(0, "ERROR(afficherMeta):" + er.message);
    }
}

/**
 * getListElements
 * @param {string} p_table
 * @param {string} p_col
 * @returns {Array}
 */
function getListElements(p_table, p_col) {
    try {
        var list = new Array();
        
        var tabInput = { table : p_table, col : p_col };
        var json_retour = $.functionsLib.callRest(domaine+"phpsql/getListElements.php", {}, tabInput);
        if(json_retour["strErreur"] == ""){
            for (var indice in json_retour["data"]["liste"]["data"]) {
                list[list.length] = json_retour["data"]["liste"]["data"][indice]["element"];
            }
        }
        
        return list;
    } catch (er) {
        $.functionsLib.log(0, "ERROR(getListElements):" + er.message);
        return null;
    }
}
    
/**
 * setClasse
 */
function setClasse(p_classe, p_div){
    try {
        chargerClasse(p_div);
        chosenClass = p_classe;     
        $('#img_'+p_classe).css("border-color", "red");
    } catch (er) {
        $.functionsLib.log(0, "ERROR(setClasse):" + er.message);
    }
}

/**
 * chargerClasse
 */
function chargerClasse(p_div){
    try {
        var listClasse = getListElements("tab_inventaire", "classe");
        var strHtml = "";
        for (var indice in listClasse) {
            var classe = listClasse[indice];
            if(classe != "Neutre"){
                if(classe == "Démoniste"){classe = "Demoniste";};
                if(classe == "Prêtre"){classe = "Pretre";};
                strHtml += "<img src='./img/icon_"+classe+".png' id='img_"+listClasse[indice]+"' style='border-width:3px;border-style:solid;border-color:transparent;' onClick=\"setClasse('"+listClasse[indice]+"', '"+p_div+"');\">";
            }
        }
        $("#"+p_div).html(strHtml).trigger('create');
    } catch (er) {
        $.functionsLib.log(0, "ERROR(chargerClasse):" + er.message);
    }
}


/**
 * Ajouter les params par defaut
 * @param {type} p_input
 * @returns {buildInput.input_defaut}
 */ 
function buildInput(p_input) {
    try {
        var input_defaut = {
            code_user : $.functionsLib.getUserInfo().code_user, 
            div_graph : "", 
            titre : "", 
            legend : "true", 
            dateDebut : "", 
            type : "", 
            classeAdv : "", 
            regrp : "classe", 
            classe : "", 
            deck : "",
            filtre_nonClasse : "false",
            filtre_classe : "false",
            filtre_arene : "false"
        };

        for(var indice in p_input){
            input_defaut[indice] = p_input[indice];
        }

        return input_defaut;
    } catch (er) {
        $.functionsLib.log(0, "ERROR(buildInput):" + er.message);
    }
}
      
/**
 * chargerMetricsGenerique
 */
function chargerMetricsGenerique(p_tabInput){
    try {
        var tabSetting = { functionRetour : retourMetricsMatchsGenerique};
        var tabInput = buildInput(p_tabInput);
        $.functionsLib.callRest(domaine+"phpsql/getMetricsMatchs.php", tabSetting, tabInput);           
    } catch (er) {
        $.functionsLib.log(0, "ERROR(chargerMetricsGenerique):" + er.message);
    }
}    

///////////////////
//BLOCK FONCTIONS AFFICHAGE
///////////////////
/**
 * retourMetricsMatchsGenerique
 * 
 * @param {array} p_retour
 */
function retourMetricsMatchsGenerique(json_retour){
    try {
        if(json_retour["strErreur"] == ""){
            var div_graph = json_retour["data"]["div_graph"];
            var legend = (json_retour["data"]["legend"] === 'true');
            var titre = json_retour["data"]["titre"];
            var regrp = json_retour["data"]["regrp"];

            if((json_retour["data"]["metricsMatchsWin"]["nombre"] == "0")&&(json_retour["data"]["metricsMatchsLoss"]["nombre"] == "0")){
                $('#from'+div_graph).remove();
                $('#'+div_graph).html(json_retour["data"]["titre"]+"<br>Pas de match.");
            }else if(json_retour["data"]["metricsMatchsWin"]["nombre"] == "0"){
                $('#from'+div_graph).remove();
                $('#'+div_graph).html(json_retour["data"]["titre"]+"<br>"+json_retour["data"]["metricsMatchsLoss"]["nombre"]+" perdu(s)");
            }else if(json_retour["data"]["metricsMatchsLoss"]["nombre"] == "0"){
                $('#from'+div_graph).remove();
                $('#'+div_graph).html(json_retour["data"]["titre"]+"<br>"+json_retour["data"]["metricsMatchsWin"]["nombre"]+" gagn&eacute;(s)");
            }else{
                var victoire = json_retour["data"]["metricsMatchsWin"]["data"];
                var countVictoire = parseInt(victoire[0]["countReussite"]);
                var categoriesVictoire = $.functionsLib.getListValeurPourAttribut(victoire,"regrp");
                var classesVictoire = $.functionsLib.getListValeurPourAttribut(victoire,"classe");
                var dataVictoire = $.functionsLib.getListValeurPourAttribut(victoire,"nb",Number);
                var colorsDataVictoire = new Array();
                for (var indice in categoriesVictoire) {
                    switch (regrp) {
                        case 'classe_adversaire':
                        case "classe":
                            colorsDataVictoire[colorsDataVictoire.length] = colorClasse[categoriesVictoire[indice]];
                            break;
                        case "nom_deck":
                            colorsDataVictoire[colorsDataVictoire.length] = colorClasse[classesVictoire[indice]];
                            break;
                        default:
                            colorsDataVictoire[colorsDataVictoire.length] = colorClasse[classesVictoire[indice]];
                            break;
                    }
                }

                var defaite = json_retour["data"]["metricsMatchsLoss"]["data"];
                var countDefaite = parseInt(defaite[0]["countReussite"]);
                var categoriesDefaite = $.functionsLib.getListValeurPourAttribut(defaite,"regrp");
                var classesDefaite = $.functionsLib.getListValeurPourAttribut(defaite,"classe");
                var dataDefaite = $.functionsLib.getListValeurPourAttribut(defaite,"nb",Number);
                var colorsDataDefaite  = new Array();
                for (var indice in categoriesDefaite) {
                    switch (regrp) {
                        case 'classe_adversaire':
                        case "classe":
                            colorsDataDefaite[colorsDataDefaite.length] = colorClasse[categoriesDefaite[indice]];
                            break;
                        case "nom_deck":
                            colorsDataDefaite[colorsDataDefaite.length] = colorClasse[classesDefaite[indice]];
                            break;
                        default:
                            colorsDataDefaite[colorsDataDefaite.length] = colorClasse[classesDefaite[indice]];
                            break;
                    }
                }

                var colors = Highcharts.getOptions().colors,
                    categories = ['Victoires', 'Defaites'],
                    name = 'Réussite',
                    data = [{
                            y: countVictoire,
                            color: colors[0],
                            drilldown: {
                                name: 'Victoire',
                                categories: categoriesVictoire,
                                data: dataVictoire,
                                color: colorsDataVictoire
                            }
                        }, {
                            y: countDefaite,
                            color: colors[1],
                            drilldown: {
                                name: 'Defaite',
                                categories: categoriesDefaite,
                                data: dataDefaite,
                                color: colorsDataDefaite
                            }
                        }];


                // Build the data arrays
                var browserData = [];
                var versionsData = [];
                for (var i = 0; i < data.length; i++) {

                    // add browser data
                    browserData.push({
                        name: categories[i],
                        y: data[i].y,
                        color: data[i].color
                    });

                    // add version data
                    for (var j = 0; j < data[i].drilldown.data.length; j++) {
                        var brightness = 0.2 - (j / data[i].drilldown.data.length) / 5 ;
                        versionsData.push({
                            name: data[i].drilldown.categories[j],
                            y: data[i].drilldown.data[j],
                            color: data[i].drilldown.color[j]
                        });
                    }
                }

                // Create the chart
                $('#'+div_graph).highcharts({
                    chart: {
                        type: 'pie',
                        backgroundColor:'rgba(255, 255, 255, 0.1)'
                    },
                    exporting: {
                        enabled: false
                    },
                    title: {
                        text: titre
                    },
                    yAxis: {
                        title: {
                            text: 'Répartition'
                        }
                    },
                    plotOptions: {
                        pie: {
                            shadow: true,
                            center: ['50%', '50%']
                        }
                    },
                    tooltip: {
                        formatter: function() {
                            var s;
                            if(legend){
                                s = this.point.name +' : '+$.functionsLib.arrondir(this.point.percentage,2)+"% ("+this.y+")";
                            }else{
                                s = this.point.name.substring(0,2) +':'+$.functionsLib.arrondir(this.point.percentage,2)+"%("+this.y+")";
                            }
                            return s;
                        }
                    },
                    series: [{
                        name: 'Réussite',
                        data: browserData,
                        size: '63%',
                        dataLabels: {
                            formatter: function() {
                                return this.y > 5 ? this.point.name : null;
                            },
                            color: 'white',
                            distance: -60,
                            enabled : legend
                        }
                    }, {
                        name: 'Deck',
                        data: versionsData,
                        size: '90%',
                        innerSize: '70%',
                        dataLabels: {
                            formatter: function() {
                                var s;
                                if (this.point.percentage > 2) { // the pie chart
                                    s = this.point.name;
                                }
                                return s;
                            },
                            enabled : legend
                        }
                    }]
                });
                var nb = parseInt(json_retour["data"]["metricsMatchsWin"]["data"][0]["countReussite"])+parseInt(json_retour["data"]["metricsMatchsLoss"]["data"][0]["countReussite"]);
                $('#from'+div_graph).remove();
                $('#'+div_graph).after('<div id="from'+div_graph+'" style="text-align:right;"><label style="font-size:70%;color:#BDBDBD;">From:'+nb+'</label><div>');
            }
           
        } else{
            $.functionsLib.notification(json_retour["strErreur"],$.functionsLib.oda_msg_color.ERROR);
        }
    } catch (er) {
        $.functionsLib.log(0, "ERROR(retourMetricsMatchsGenerique):" + er.message);
    }
}