// Library of tools

/**
 * @author FRO
 * @date 15/03/09
 */

//------------------------------------------------------------------------------
//  WORKER
//------------------------------------------------------------------------------
/**
* WorkerMessage : Constructeur pour notre obj message
* @param {string} cmd
* @param {json} parameter
* @returns {WorkerMessage}
*/
function WorkerMessage(cmd, parameter) {
    this.cmd = cmd;
    this.parameter = parameter;
};

//------------------------------------------------------------------------------
//  LIB
//------------------------------------------------------------------------------
/**
* Lib : pour les outils
*/
(function() {
    'use strict';
     
    //first event before show page
    //Execute en 2
    $(document).ready(function() {
        try {
            _trace("document.ready");
            _documentReady = true;
            _allReadyAndLoad();
        } catch (er) {
            $.functionsLib.log(0, "ERROR(document.ready):" + er.message);
        }
    });

    //second event when show page
    //Execute en 3
    $(window).load(function() {
        try {
            _trace("window.load");
            _windowLoad = true;
            _allReadyAndLoad();
        } catch (er) {
            $.functionsLib.log(0, "ERROR(window.load):" + er.message);
        }
    });

    var
        /* version */
        VERSION = '0.1',

        //-----------------
        //$.functionsLib
        //-----------------
        _oda_msg_color = {
            INFO : "#5882FA",
            WARNING : "#f7931e",
            ERROR : "#B9121B",
            SUCCES : "#AEEE00"
        },
        
        _oda_notAuthTest = [
            "",
            "index.html",
            "api_page_accesperdu.html",
            "api_page_contact_restreint.html",
            "api_page_creercompte.html",
            "api_page_maintenance.html",
            "api_page_sortie.html",
            "api_page_error.html"
        ],
        
        _inte_notif = 0,
        
        _userInfo = null,
        
        _dependecies = null,
        
        _dependeciesFeedback = null,
        
        _i8n = [],
        
        _documentReady = false,
        
        _windowLoad = false,
        
        _odaInit = false,
        
        _debug = false,
        
        //-----------------
        //$.functionsMobile
        //-----------------
        /* return elet*/
        _funcReturnGPSPosition = null,
        
        _funcReturnCaptureImg = null,
        
        /* var intern */
        _networkState = null,

        _positionGps = {
            coords : {
                latitude : 0,
                longitude : 0,
                altitude : 0,
                accuracy : 0,
                altitudeAccuracy : 0,
                heading : 0,
                speed : 0
            },
            timestamp : 0,
            statut : ''
        },
        
        _pictureSource = null,
        
        _destinationType = null,
        
        _onMobile = false,
        
        _onMobileTest = false,
        
        //-----------------
        //$.functionsStorage
        //-----------------
        _etatRobot = false,
        
        _frequencyRobot = 1000,
        
        _keyTll = 'fw-ttl_'
    ;


    ////////////////////////// PRIVATE METHODS ////////////////////////
    /**
     * @name UserException
     * @param {string} message
     * @returns {_L8.UserException}
     */
    function UserException(message) {
        this.message = message;
        this.name = "UserException";
    };

    /**
     * @name _init
     * @desc Hello
     */
    function _init() {
        //for testU
        if(typeof customWindowODA != 'undefined'){
            $.functionsLib.currentWindow = customWindowODA;
        }
        
        //depdends
        $.functionsLib.loadDepends([
            {"name" : "lib" , ordered : true, "list" : [
                { "elt" : "include/config.js", "type" : "script" }
                , { "elt" : "js/fonctions.js", "type" : "script" }
            ]}
            , {"name" : "style" , ordered : false, "list" : [
                , { "elt" : "API/css/mycss.css", "type" : "css" }
                , { "elt" : "css/mycss.css", "type" : "css" }
            ]}
            , {"name" : "datas" , ordered : false, "list" : [
                { "elt" : "API/json/i8n.json", "type" : "json", "target" : function(p_json){_i8n = _i8n.concat(p_json);}}
                , { "elt" : "json/i8n.json", "type" : "json", "target" : function(p_json){_i8n = _i8n.concat(p_json);}}
            ]}
        ],_loaded);
    };
    
    /**
     * @name _loaded
     */
    function _loaded() {
        //Warning
        if (!document.querySelector || !document.addEventListener) {
            $.functionsLib.notification("Pour profiter d'une expérience optimum, merci d'utiliser un navigateur récent.", $.functionsLib.oda_msg_color.WARNING);
        }
        
        //mobile
        document.addEventListener("deviceready", _onDeviceReady, false);
        
        //Pour localstorage
        $.functionsStorage.storageKey = "ODA__"+domaine+"__";
        _startRobot();
        
        //maintenance
        _checkMaintenance();
        
        //secu
        _checkAuthentification();

        //Themitisation
        var theme = $.functionsLib.getParameter("theme_defaut");

        if((!$.functionsLib.isInArray($.functionsLib.pageName,_oda_notAuthTest))&&(_userInfo != null)){
            //Ajoute titre page
            var description = $.functionsLib.getter("api_tab_menu",'{"champ":"Description","type":"PARAM_STR"}','{"champ":"id","valeur":"'+id_page+'","type":"PARAM_INT"}');
            document.title = $.functionsLib.getParameter("nom_site") + " - " + description;
            $("#id_titre").text(description);
            
            //Thematisation
            var themePerso = $.functionsLib.getter("api_tab_utilisateurs",'{"champ":"theme","type":"PARAM_STR"}','{"champ":"code_user","valeur":"'+_userInfo.code_user+'","type":"PARAM_STR"}');

            if((typeof themePerso != 'undefined')&&(themePerso != '')&&(themePerso != 'notAvailable')&&(themePerso != null)&&(themePerso != "default")){
                $('head').append('<link rel="stylesheet" href="css/themes/'+themePerso+'/jquery.mobile.theme.min.css" />');
                $('head').append('<link rel="stylesheet" href="css/themes/'+themePerso+'/jquery.mobile.icons.min.css" />');
            }else if((typeof theme != 'undefined')&&(theme != '')){
                $('head').append('<link rel="stylesheet" href="css/themes/'+theme+'/jquery.mobile.theme.min.css" />');
                $('head').append('<link rel="stylesheet" href="css/themes/'+theme+'/jquery.mobile.icons.min.css" />');
            }

            //Le menu
            var html = _getHtmlMenu(_userInfo.profile, $.functionsLib.pageName);
            $("#mypanel").append(html).trigger('create');

            //L'utilisateur en bas de page
            var strHml = _userInfo.nom + " " + _userInfo.prenom + " (" + _userInfo.labelle + ")";
            $("#id_affichageUser").html(strHml);

            //L'aide ihm
            if(_userInfo.montrer_aide_ihm == "1"){
                $.functionsLib.afficheAideIhm(_userInfo.code_user);
            }
        }else{
            if((typeof theme != 'undefined')&&(theme != '')){
                $('head').append('<link rel="stylesheet" href="css/themes/'+theme+'/jquery.mobile.theme.min.css" />');
                $('head').append('<link rel="stylesheet" href="css/themes/'+theme+'/jquery.mobile.icons.min.css" />');
            }
        }
        

        if($.functionsLib.pageName == "page_home.html"){
            _messagesShow();
        }
        
        _trace("odaInit");
        _odaInit = true;
        _allReadyAndLoad();
    };
    
    /**
    * Hidden notification
    * 
    * @param {type} p_div
    */
    function _notification_fin(p_div) {
       try {
           $( "#"+p_div ).remove();
        } catch (er) {
           $.functionsLib.log(0, "ERROR(_notification_fin):" + er.message);
        }
    };
    
    /**
    * _checkParams
    * @param {json} p_params
    * @param {json} p_def ex : {attr1 : null, attr2 : "truc"}
    */
    function _checkParams(p_params, p_def) {
        try {
            var params = $.functionsLib.clone(p_params);
            
            var param_return = {};
            
            for (var key in p_def) {
                if(p_def[key] == null){
                    if(typeof params[key] == "undefined"){
                        var myUserException = new UserException("Param : "+key+" missing");
                        throw myUserException;
                    }else{
                        param_return[key] = params[key];
                    }
                }else{
                    if(typeof params[key] == "undefined"){
                        param_return[key] = p_def[key];
                    }else{
                        param_return[key] = params[key];
                    }
                }
                delete params[key];
            }
            
            for (var key in params) {
                param_return[key] = params[key];
            }
            
            return param_return;
        } catch (er) {
            $.functionsLib.log(0, "ERROR(_checkParams):" + er.message);
            return null;
        }
    };
    
    /**
     * @name _getUserInfo
     * @param {string} p_auth
     * @returns {json}
     */
    function _getUserInfo(code_user) {
        try {
            var userInfo = null;
           
            var tabSetting = { };
            var tabInput = { 
                code_user : code_user
            };
            var retour = $.functionsLib.callRest(domaine+"API/phpsql/getAuthInfo.php", tabSetting, tabInput);
            
            if(retour.strErreur == ""){
                userInfo = retour.data.resultat;
            }
           
           return userInfo;
        } catch (er) {
            $.functionsLib.log(0, "ERROR(_checkParams):" + er.message);
            return null;
        }
    };
    
    /**
     * @name checkMaintenance
     */
    function _checkMaintenance() {
        try {
           var intMaintenance = $.functionsLib.getParameter("maintenance");
           $.functionsLib.pageName = $.functionsLib.currentWindow.location.pathname.substring($.functionsLib.currentWindow.location.pathname.lastIndexOf("/") + 1);

           if (intMaintenance != 0) {
                if ($.functionsLib.pageName != "api_page_maintenance.html") {
                    $.functionsLib.currentWindow.location = "api_page_maintenance.html";
                    return false;
                }
           } else {
                if ($.functionsLib.pageName == "api_page_maintenance.html") {
                    $.functionsLib.currentWindow.location = "index.html";
                    return false;
                }
           }

           return true;
        } catch (er) {
            $.functionsLib.log(0, "ERROR(checkMaintenance):" + er.message);
            return false;
        }
    };
    
    /**
     * @name checkAuthentification
     */
    function _checkAuthentification(){
        try {
            var session = $.functionsStorage.get("ODA-SESSION");
            if(session != null){
                //session exist
                var tabSetting = { };
                var tabInput = { 
                    "code_user" : session.code_user
                    , "key" : session.key
                };
                var retour = $.functionsLib.callRest(domaine+"API/phpsql/checkSession.php", tabSetting, tabInput); 
                if(retour.data){
                    //session ok
                    if(($.functionsLib.pageName == "index.html") || ($.functionsLib.pageName == "")){
                        $.functionsLib.currentWindow.location = "./page_home.html?mili="+$.functionsLib.getMilise();
                        return true;
                    }else{
                        _userInfo = _getUserInfo(session.code_user);
                        _userInfo.locale = "fr";

                        if(_userInfo == null){
                            $.functionsLib.logout();
                            $.functionsLib.log(0, "ERROR(_checkAuthentification): ko, info invalide for "+session.code_user);
                            $.functionsLib.currentWindow.location = "index.html?mili="+$.functionsLib.getMilise();
                            return false;
                        }
                        return true;
                    }
                }else{
                    //session ko
                    if(!$.functionsLib.isInArray($.functionsLib.pageName,_oda_notAuthTest)){
                        $.functionsLib.logout();
                        $.functionsLib.log(0, "ERROR(_checkAuthentification): ko, for "+session.code_user);
                        $.functionsLib.currentWindow.location = "index.html?mili="+$.functionsLib.getMilise();
                        return false;
                    }
                    return true;
                }
            }else{
                //session not exist
                //si page à test
                if(!$.functionsLib.isInArray($.functionsLib.pageName,_oda_notAuthTest)){
                    //check if log by url
                    var get_usr = $.functionsLib.getParamGet("getUser");
                    var get_pass = $.functionsLib.getParamGet("getPass");
                    if((get_usr != null)&&(get_pass != null)){
                        var retour_login = $.functionsLib.login(get_usr, get_pass, $.functionsLib.pageName);
                        if(retour_login.statut == "ko"){
                            $.functionsLib.log(0, "ERROR(_checkAuthentification): ko, wrong auth by url ("+get_usr+")");
                            $.functionsLib.currentWindow.location = "index.html?mili="+$.functionsLib.getMilise();
                            return false;
                        }else{
                            _userInfo = _getUserInfo(get_usr);
                            return true;
                        }
                    }else{
                        //pas de log by url
                        $.functionsLib.log(0, "ERROR(_checkAuthentification): ko, for anonymous");
                        $.functionsLib.currentWindow.location = "index.html?mili="+$.functionsLib.getMilise();
                        return false;
                    }
                }
            }
            return true;
        } catch (er) {
            $.functionsLib.log(0, "ERROR(_checkAuthentification) ko, " + er.message);
            return false;
        }
    };
    
    /**
     * @name _getHtmlMenu
     * @param {type} p_rang
     * @returns {String}
     */
    function _getHtmlMenu(p_rang, p_page_name) {
        try {
            var strHTML = ""; 

            var listPage = [];

            var tabInput = { rang : p_rang, id_page : id_page };
            var retour = $.functionsLib.callRest(domaine+"API/phpsql/getMenu.php", {}, tabInput);

            if(retour["strErreur"] == ""){
                var datas = retour["data"]["resultat"]["data"];

                var cate = "";

                strHTML += "<div data-role=\"collapsible-set\" data-collapsed-icon=\"arrow-r\" data-expanded-icon=\"arrow-d\" data-corners=\"false\" id=\"leMenu\" data-mini=\"true\">";
                for (var indice in datas) {
                    listPage.push(datas[indice]["Lien"]);
                    if(datas[indice]["id_categorie"] != "98"){
                        if(datas[indice]["id_categorie"] != cate){
                            cate = datas[indice]["id_categorie"];
                            if(indice != "0"){
                                strHTML += "</ul>";
                                strHTML += "</div>";
                            }

                            var strExpand = "";
                            if(datas[indice]["ouvert"] == "1"){
                                strExpand = "data-collapsed=\"false\"";
                            }

                            strHTML += "<div data-role=\"collapsible\" "+strExpand+" data-mini=\"true\">";
                            strHTML += "<h4>"+datas[indice]["Description_cate"]+"</h4>";
                            strHTML += "<ul data-role=\"listview\">";
                        }
                        if(cate != "99"){
                            if(datas[indice]["selected"] == "1"){
                                strHTML += "<li data-mini=\"true\" data-theme=\"b\"><a href=\"javascript:window.location = ('"+datas[indice]["Lien"]+"?mili="+$.functionsLib.getMilise()+"');\">"+datas[indice]["Description_courte"]+"</a></li>";
                            }else{
                                strHTML += "<li data-mini=\"true\"><a href=\"javascript:window.location = ('"+datas[indice]["Lien"]+"?mili="+$.functionsLib.getMilise()+"');\">"+datas[indice]["Description_courte"]+"</a></li>";
                            }
                        }else{
                            strHTML += "<li><a href=\""+datas[indice]["Lien"]+"\" target=\"_new\">"+datas[indice]["Description_courte"]+"</a></li>";
                        }
                    }
                }
                strHTML += "</ul>";
                strHTML += "</div>";
                strHTML += "</div>";
            }
            
            //Check if the user have right to see
            if(!$.functionsLib.isInArray(p_page_name,listPage)){
                $.functionsLib.log(0, "ERROR(_getHtmlMenu) : Illegal demande on page : "+ p_page_name + " by : " + $.functionsLib.getUserInfo().code_user);
                $.functionsLib.logout();
                $.functionsLib.currentWindow.location = "api_page_error.html?mili="+$.functionsLib.getMilise()+"&msg=Illigal demande for the page.";
            }

            return strHTML;
        } catch (er) {
           $.functionsLib.log(0, "ERROR(_getHtmlMenu):" + er.message);
        }
    };
    
    /**
     * @name : _messagesShow
     */
    function _messagesShow() {
        try {
            var tabInput = { code_user : $.functionsLib.getUserInfo().code_user };
            var json_retour = $.functionsLib.callRest(domaine+"API/phpsql/getMessagesToDisplay.php", {}, tabInput);
            if(json_retour["strErreur"] == ""){
                if(parseInt(json_retour["data"]["messages"]["nombre"]) > 0){
                    var strHTML = "";
                    strHTML = '<div id="div_show_messages" style="z-index: 999;position:fixed;top:50px;width:80%;left:50%;margin-left:-40%;vertical-align:middle;line-height:20px;text-shadow:none;border-width:1px;border-style:solid;background-color:#5882FA;">';
                    strHTML += '</div>';
                    $("#main_content").parent().before(strHTML);

                    var strHTML = "";
                    strHTML += '<div style="font-weight: bold;text-align: center;margin:0">Messages</div>';
                    strHTML += '<ul style="line-height: 20px;margin:0;font-weight: bold;color:#E6E6E6">';

                    for (var indice in json_retour["data"]["messages"]["data"]) {
                        strHTML += '<li>'+json_retour["data"]["messages"]["data"][indice]["message"]+'</li>';  
                    }

                    strHTML += '</ul>';
                    strHTML += '<div id="div_bt_messages_lus" style="text-align: right;"><a id="bt_messages_lus" onclick="$.functionsLib.messagesLus();" href="#" class="ui-btn ui-icon-check ui-btn-icon-left ui-shadow ui-corner-all ui-btn-inline ui-mini ui-disabled" title="Marquer comme lu(s)">Marquer comme lu(s)</a></div>';

                    $("#div_show_messages").html(strHTML).trigger('create');

                    var t = setTimeout(function () {_messagesBtOn();},3000);
                }
            } else{
                $.functionsLib.notification(json_retour["strErreur"],$.functionsLib.oda_msg_color.WARNING);
            }
        } catch (er) {
            $.functionsLib.log(0, "ERROR($.functionsLib._messagesShow):" + er.message);
        }
    };
    
    /**
     * messagesBtOn
     */
    function _messagesBtOn() {
        try {
           $('#bt_messages_lus').removeClass('ui-disabled');
        } catch (er) {
            $.functionsLib.log(0, "ERROR($.functionsLib._messagesBtOn):" + er.message);
        }
    };
    
    function _onDeviceReady() {
        _onMobile = true;
        $.functionsMobile.initModuleMobile();
        if(_onMobileTest){
            $('#content_testMobile').css('display', 'inline-block');
            $.functionsMobile.testConnection();
        }
    }
    
    function _allReadyAndLoad(){
        try {
            if(_documentReady && _windowLoad && _odaInit){
                $.functionsLib.ready();
            }
        } catch (er) {
            $.functionsLib.log(0, "ERROR(_allReadyAndLoad):" + er.message);
        }
    }
    
    /**
    * @name getGPSPosition
    * @desc getGPSPosition
    */
    function _onSuccessGPSPosition(p_position) {
        try {
            _positionGps.coords = p_position.coords;
            _positionGps.timestamp = p_position.timestamp;
            _positionGps.statut = "OK";
            _funcReturnGPSPosition(_positionGps);
        } catch (er) {
            log(0, "ERROR($.functionsMobile._onSuccessGPSPosition):" + er.message);
        }
    };

    /**
     * @name getGPSPosition
     * @desc getGPSPosition
     */
     function _onErrorGPSPosition(p_error) {
        try {
            _positionGps.statut = "KO : code=>"+ p_error.code+", message=>"+ p_error.message;
            _funcReturnGPSPosition(_positionGps);
        } catch (er) {
            log(0, "ERROR($.functionsMobile._onErrorGPSPosition):" + er.message);
        }
    };
    
    function _onPhotoSuccess(p_imageData) {
        try {
            var imgSrc = "data:image/jpeg;base64,"+p_imageData;
            
            _funcReturnCaptureImg(imgSrc);
        } catch (er) {
            log(0, "ERROR($.functionsMobile._onPhotoSuccess):" + er.message);
        }
    };
    
    function _onPhotoURISuccess(p_imageURI) {
        try {
            var imgSrc = p_imageURI;
            
            _funcReturnCaptureImg(imgSrc);
        } catch (er) {
            log(0, "ERROR($.functionsMobile._onPhotoURISuccess):" + er.message);
        }
    };

    function _onPhotoFail(p_message) {
        try {
            alert('Failed because: ' + p_message);
        } catch (er) {
            log(0, "ERROR($.functionsMobile._onPhotoFail):" + er.message);
        }
    };
    
    function _startRobot() {
        if(_etatRobot){
            var myTimer=setTimeout(function(){_executeRobot();},_frequencyRobot);
        }
    };
    
    function _trace(p_msg) {
        if(_debug){
            console.log(p_msg);
        }
    };
    
    function _terminateRobot() {
        _etatRobot = false;
    };
    
    function _executeRobot() {
        if(_etatRobot){
            var myTimer=setTimeout(function(){_executeRobot();},_frequencyRobot);
        }
    };
        
    function _loadDepend(p_elt, p_mode) {
        try {
            var retour = true;

            _trace("Loading : "+p_elt.elt);
            
            switch(p_elt.type) {
                case "css":
                    $('<link/>', {
                        rel: 'stylesheet',
                        type: 'text/css',
                        href: p_elt.elt
                    }).appendTo('head');
                    p_elt.status = "done";
                    _trace( "Sucess for : "+p_elt.elt );
                    
                    if(p_mode == "serie"){
                        _loadListDependsOrdoned();
                    }
                    _allDependsLoaded();
                    break;
                case "script":
                    $.ajax({
                    url: p_elt.elt,
                    dataType: "script",
                    context : {"lib" : p_elt.elt},
                    success: function( script, textStatus, jqxhr) {
                        _trace( "Sucess for : "+$(this)[0].lib+" ("+textStatus+")" );
                        p_elt.status = "done";
                    
                        if(p_mode == "serie"){
                            _loadListDependsOrdoned();
                        }
                        _allDependsLoaded();
                    },
                    error : function( jqxhr, settings, exception ) {
                        _trace( "Triggered ajaxError handler for : "+$(this)[0].lib+"." );
                        p_elt.status = "fail";
                    
                        if(p_mode == "serie"){
                            _loadListDependsOrdoned();
                        }
                        _allDependsLoaded();
                    }
                });
                    break;
                case "json":
                    $.ajax({
                    dataType: "json",
                    url: p_elt.elt,
                    context : {"lib" : p_elt.elt},
                    success: function( json, textStatus, jqxhr) {
                        p_elt.target(json);
                        _trace( "Sucess for : "+$(this)[0].lib+" ("+textStatus+")" );
                        p_elt.status = "done";
                        
                        if(p_mode == "serie"){
                            _loadListDependsOrdoned();
                        }
                        _allDependsLoaded();
                    },
                    error : function( jqxhr, settings, exception ) {
                        _trace( "Triggered ajaxError handler for : "+$(this)[0].lib+"." );
                        p_elt.status = "fail";
                        
                        if(p_mode == "serie"){
                            _loadListDependsOrdoned();
                        }
                        _allDependsLoaded();
                    }
                    });
                    break;
                default:
                    _trace( "Type unknown for : "+p_elt.elt+"." );
            }

            return retour;
        } catch (er) {
            $.functionsLib.log(er.message);
            return null;
        }
    }
    
    function _loadListDependsOrdoned() {
        try {
            var retour = true;

            for (var indice in _dependecies) {
                if((!$.functionsLib.isUndefined(_dependecies[indice].status)) && (_dependecies[indice].status == "doing")){
                    var gardian = true;
                    for (var indiceList in _dependecies[indice].list) {
                        var elt = _dependecies[indice].list[indiceList];
                        if(($.functionsLib.isUndefined(elt.status)) || (elt.status == "doing")){
                            elt.status = "doing";
                            _loadDepend(elt,"serie");
                            gardian = false;
                            break;
                        }
                    }
                    if(gardian){
                        _dependecies[indice].status = "done";
                        $.functionsLib.loadDepends();
                    }
                }
            }

            return retour;
        } catch (er) {
            $.functionsLib.log(er.message);
            return null;
        }
    }
    
    function _allDependsLoaded() {
        try {
            var retour = true;
            
            if(_dependecies != null){
                for (var indice in _dependecies) {
                    if(($.functionsLib.isUndefined(_dependecies[indice].status)) || (_dependecies[indice].status == "doing")){
                        retour = false;
                        break;
                    }else{
                        for (var indiceList in _dependecies[indice].list) {
                            var elt = _dependecies[indice].list[indiceList];
                            if(($.functionsLib.isUndefined(elt.status)) || (elt.status == "doing")){
                                retour = false;
                                break;
                            }
                        }
                    }
                }
            }

            if((retour)&&(_dependecies!=null)){
                _dependeciesFeedback();
                _dependecies = null;
            }

            return retour;
        } catch (er) {
            $.functionsLib.log(er.message);
            return null;
        }
    }

    ////////////////////////// PUBLIC METHODS /////////////////////////

    $.functionsLib = {
        /* Version number */
        version : VERSION,
        oda_msg_color : _oda_msg_color,
        currentWindow : window,
        pageName : null,
        
        
        /**
        * addUtilisateur
        * @test addUtilisateur('nom', 'prenom', 'email', 'mdp', 'NOM');
        * @param {String} p_nom
        * @param {String} p_prenom
        * @param {String} p_email
        * @param {String} p_motDePasse
        * @param {String} p_codeUtilisateur
        * @returns {Array}
        */
        addUtilisateur : function(p_nom, p_prenom, p_email, p_motDePasse, p_codeUtilisateur) {
            try {
                var tabInput = { nom : p_nom, prenom : p_prenom, email : p_email, motDePasse : p_motDePasse, codeUtilisateur : p_codeUtilisateur };
                var retour = this.callRest(domaine+"API/phpsql/addUtilisateur.php", {}, tabInput);

                return retour;
            } catch (er) {
                this.log(0, "ERROR(addUtilisateur):" + er.message);
                return null;
            }
        },

        /**
         * @name callRest
         * @desc Hello
         * @param{string} p_url
         * @param{json} p_tabSetting
         * @param{json} p_tabInput
         * @returns {json}
         */
        callRest: function(p_url, p_tabSetting, p_tabInput) {
            try {
                var jsonAjaxParam = {
                    url : p_url
                    , contentType : 'application/x-www-form-urlencoded; charset=UTF-8'
                    , dataType : 'json'
                    , type : 'GET'
                };
                
                //création du jeton pour la secu
                var session = $.functionsStorage.get("ODA-SESSION");
                var key = null;
                if(session != null){
                    key = session.key;
                } else{
                    key = p_tabInput["keyAuthODA"];
                    delete p_tabInput["keyAuthODA"];
                }

                p_tabInput.milis = $.functionsLib.getMilise();
                p_tabInput.ctrl = "OK";
                p_tabInput.keyAuthODA = key;

                jsonAjaxParam.data = p_tabInput;

                //traitement determinant async ou pas
                var async = true;
                if(p_tabSetting.functionRetour == null){
                    async = false;
                    jsonAjaxParam.async = false;        
                }

                for(var indice in p_tabSetting){
                    jsonAjaxParam[indice] = p_tabSetting[indice];
                }

                //si retour synchron init retour
                var v_retourSync = null;

                jsonAjaxParam.success = function(p_retour, p_statut){
                    try {
                        if(typeof p_retour == 'object'){
                            //object
                            var returns = p_retour;
                            
                            if((returns.strErreur == "key auth expiree.")||(returns.strErreur == "key auth invalide.")){
                                $.functionsLib.logout();
                                $.functionsLib.currentWindow.location = "api_page_error.html?mili="+$.functionsLib.getMilise()+"&msg="+encodeURIComponent(returns.strErreur);
                            }
                        }else{
                            var returns = p_retour;
                        }

                        if(async){
                            p_tabSetting.functionRetour(p_retour);
                        }else{
                            v_retourSync = p_retour;
                        }
                    } catch (er) {
                        var msg = "ERROR($.functionsLib.callRest.success):" + er.message;
                        this.log(0, msg);
                    }
                };

                jsonAjaxParam.error = function(p_resultat, p_statut, p_erreur){
                    var msg = p_resultat.responseText + " - " + p_statut + " - " + p_erreur.message;
                    if(async){
                        p_tabSetting.functionRetour(msg);
                    }else{
                        v_retourSync = msg;
                    }
                };

                var ajax = $.ajax(jsonAjaxParam);

                return v_retourSync;
            } catch (er) {
                var msg = "ERROR($.functionsLib.callRest):" + er.message;
                this.log(0, msg);
                return null;
            } 
        },
        
        /**
         * @name clone
         * @desc Clone an object JS
         * @param{object} p_params
         * @returns {object}
         */
        clone : function(p_params) {
            if (null == p_params || "object" != typeof p_params) return p_params;
            var copy = p_params.constructor();
            for (var attr in p_params) {
                if (p_params.hasOwnProperty(attr)) copy[attr] = p_params[attr];
            }
            return copy;
        },
        
        /**
         * @name odaUploadFile
         * @desc Hello
         * @param{json} p_params
         * @returns {boolean}
         */
        uploadFile : function(p_params) {
            try {
                var params_attempt = {
                    idInput : ""
                    , folder : ""
                    , name : ""
                };
                
                // Merge params_attempt into object1
                var params = $.extend( params_attempt, p_params );
                
                if(params.idInput != ""){
                    var retour = {};
                    var inputElement = $("#"+params.idInput);

                    if(inputElement[0].files.length > 0){
                        var data = new FormData();
                        $.each(inputElement[0].files, function(i, file) {
                            data.append('file-'+i, file);
                        });

                        retour = this.sendFile(data, params.folder, params.name);

                        if(retour.code = "ok"){
                            this.notification("Upload réussi.", this.oda_msg_color.SUCCES);
                        }else{
                            this.notification("Erreur upload : "+retour.message, this.oda_msg_color.ERROR);
                        }
                    }else{
                        this.notification("Erreur pas de fichier sélectionné", this.oda_msg_color.WARNING);
                    } 
                }else{
                    this.notification("Erreur pas de fichier sélectionné", this.oda_msg_color.WARNING);
                }
                
                return true;
            } catch (er) {
                this.log(0, "ERROR($.functionsLib.odaUploadFile) : " + er.message);
                return false;
            }
        },
        
        /**
        * messagesLus
        */
        messagesLus : function() {
            try {
                var tabInput = { code_user : this.getUserInfo().code_user };
                var retour = $.functionsLib.callRest(domaine+"API/phpsql/setMessagesLus.php", {}, tabInput);
                if(retour["strErreur"] == ""){
                    this.notification("Merci",$.functionsLib.oda_msg_color.SUCCES);
                } else{
                    this.notification(retour["strErreur"],$.functionsLib.oda_msg_color.WARNING);
                }
                $('#div_show_messages').remove();
            } catch (er) {
                this.log(0, "ERROR($.functionsLib.messagesLus):" + er.message);
            }
        },
        
        /**
        * notification
        * @Desc Show notification
        * @param {string} p_message
        * @param {string} p_color
        * @returns {boolean}
        */
        notification : function(p_message, p_color) {
           try {
               $( "#div_notifcation_"+_inte_notif ).remove();

               _inte_notif += 1;

               var div_notif = "div_notifcation_"+_inte_notif;

              var strHTML = "";
              strHTML = '<div id="'+div_notif+'" style="font-weight: bold;color:#151515;background-color:'+p_color+';position:fixed;bottom:50px;width:80%;left:50%;margin-left:-40%;text-align:center;vertical-align:middle;line-height:normal;line-height:30px;height:30px;text-shadow:none;border-width:1px;border-style:solid;z-index:99999999999999">';
              strHTML += p_message;
              strHTML += '</div>';
              $("#main_content").parent().after(strHTML);

              var t = setTimeout(function () {_notification_fin(div_notif);},5000);
              
              return true;
           } catch (er) {
               this.log(0, "ERROR($.functionsLib.notification) :" + er.message);
               return false;
           }
        },
        
        /**
        * log
        * @param {int} p_type
        * @param {string} p_msg
        * @returns {boolean}
        */
        log : function(p_type, p_msg) {
           try {
               _trace(p_msg);
               this.notification(p_msg,this.oda_msg_color.ERROR);
               var tabSetting = { };
               var tabInput = { 
                   type : p_type
                   , msg : p_msg 
               };
               var retour = this.callRest(domaine+"API/phpsql/insertLog.php", tabSetting, tabInput);
               
               return true;
           } catch (er) {
               throw new Error("ERROR($.functionsLib.log):" + er.message);
               return false;
           }
        },
        
        /**
        * serviceMailDiff
        * @ex $.functionsLib.serviceMailDiff({object:'hello',content:'mycontent',id_serviceMail:1,autretruc:'opt'});
        * @param {string} p_params
        * @returns {boolean}
        */
        serviceMailDiff : function(p_params) {
           try {
               var params_attempt = {
                    sujet : null
                    , message_html : null
                    , id_serviceMail : null
                };
                
                var params = _checkParams(p_params, params_attempt);
                if(params == null){
                    return false;
                }
                
                var paramsMail = {
                    email_mails_dest : ""
                    , message_html : ""
                    , sujet : ""
                };
                
                var sql = "SELECT mail FROM `"+prefixTable+"api_tab_utilisateurs` a, api_api_tab_service_mail_dest b WHERE 1=1 AND a.code_user = b.code_user AND b.id_type_mail = "+params.id_serviceMail+" AND nivo = 1";
                var returnSql = $.functionsLib.getSQL(sql);
                for (var indice in returnSql["data"]["resultat"]["data"]) {
                    paramsMail.email_mails_dest += returnSql["data"]["resultat"]["data"][indice]["mail"]+",";
                }
                
                var sql = "SELECT mail FROM `"+prefixTable+"api_tab_utilisateurs` a, api_api_tab_service_mail_dest b WHERE 1=1 AND a.code_user = b.code_user AND b.id_type_mail = "+params.id_serviceMail+" AND nivo = 2";
                var returnSql = $.functionsLib.getSQL(sql);
                for (var indice in returnSql["data"]["resultat"]["data"]) {
                    paramsMail.email_mails_copy += returnSql["data"]["resultat"]["data"][indice]["mail"]+",";
                }

                var sql = "SELECT mail FROM `"+prefixTable+"api_tab_utilisateurs` a, api_api_tab_service_mail_dest b WHERE 1=1 AND a.code_user = b.code_user AND b.id_type_mail = "+params.id_serviceMail+" AND nivo = 3";
                var returnSql = $.functionsLib.getSQL(sql);
                for (var indice in returnSql["data"]["resultat"]["data"]) {
                    paramsMail.email_mails_cache += returnSql["data"]["resultat"]["data"][indice]["mail"]+",";
                }
                
                paramsMail.message_html = params.message_html;
                
                paramsMail.sujet = params.sujet;
                
                var retour = this.sendMail(paramsMail);
               
                return retour;
           } catch (er) {
               this.log(0, "ERROR($.functionsLib.serviceMailDiff) :" + er.message);
               return null;
           }
        },
        
        /**
         * saisirContact
         * 
         * @param {type} p_codeDate
         * @param {type} p_reponse
         * @param {type} p_message
         * @returns {undefined}
         */
        saisirContact : function(p_codeDate, p_reponse, p_message) {
            try {
                var strDate = this.getStrDate.getStrDateFR();
                if((p_codeDate != "")&&(p_reponse != "")&&(p_message != "")){
                    if(strDate == p_codeDate){
                        var contact_mail_administrateur = this.getParameter("contact_mail_administrateur");
                        if(contact_mail_administrateur != ""){
                            var auteur = "Inconnu";
                            if(this.getUserInfo() != null){
                                auteur = this.getUserInfo().code_user;
                            }
                            var result = this.insertContact(p_reponse, p_message, auteur);
                            if(result["strErreur"] == ""){
                                var message_html = "";
                                var sujet = "";

                                message_html = "";
                                message_html += "<html><head></head><body>";
                                message_html += "De : <pre>"+auteur+"</pre>";
                                message_html += "</br></br>";
                                message_html += "Contact : <pre>"+p_reponse+"</pre>";
                                message_html += "</br></br>";
                                message_html += "Message : <pre>"+p_message+"</pre>";
                                message_html += "</body></html>";

                                sujet = "[ODA-"+this.getParameter("nom_site")+"]Nouveau message du "+strDate+".";

                                var paramsMail = {
                                    email_mail_ori : contact_mail_administrateur
                                    , email_labelle_ori : "Service Mail ODA"
                                    , email_mail_reply : contact_mail_administrateur
                                    , email_labelle_reply : "Service Mail ODA"
                                    , email_mails_dest : contact_mail_administrateur
                                    , message_html : message_html
                                    , sujet : sujet
                                };

                                var retour = this.sendMail(paramsMail);

                                $("#codeDate").val("");
                                $("#howtocontact").val("");
                                $("#textarea").val("");

                                if(retour){
                                    this.notification("Demande bien soummise sous l'identifiant n&ordm;"+result["data"]["resultat"]+".",this.oda_msg_color.SUCCES);
                                }
                            }else{
                                this.notification(result["strErreur"],this.oda_msg_color.ERROR);
                            }
                        }else{
                            this.notification("Mail du service non définie.",this.oda_msg_color.ERROR);
                        }
                    }else{
                        this.notification("Code de s&eacute;curit&eacute; incorrect.",this.oda_msg_color.WARNING);
                    }
                }else{
                    this.notification("Merci de saisir chaque champs.",this.oda_msg_color.WARNING);
                }
            } catch (er) {
                this.log(0, "ERROR(saisirContact):" + er.message);
            }
        },
        
        /**
        * sendMail
        * @ex $.functionsLib.sendMail({email_mails_dest:'fabrice.rosito@gmail.com',message_html:'HelloContent',sujet:'HelloSujet'});
        * @param {json} p_params
        * @returns {boolean}
        */
        sendMail : function(p_params) {
           try {
                var params_attempt = {
                     email_mails_dest : null
                     , message_html : null
                     , sujet : null
                 };

                var params = _checkParams(p_params, params_attempt);
                if(params == null){
                    return false;
                }

                var returns = this.callRest(domaine+"API/scriptphp/send_mail.php", {type : 'POST'}, params);

                return returns;
           } catch (er) {
               this.log(0, "ERROR($.functionsLib.sendMail) :" + er.message);
               return null;
           }
        },
        
        /**
        * getParameter
        * #ex $.functionsLib.getParameter("contact_mail_administrateur");
        * @param {string} p_param_name
        * @returns { int|varchar }
        */
        getParameter : function(p_param_name) {
           try {
                var strResponse;

                var tabInput = { param_name : p_param_name };
                var json_retour = this.callRest(domaine+"API/phpsql/getParam.php", {type : 'POST'}, tabInput);   
                if(json_retour["strErreur"] == ""){
                    var type = json_retour["data"]["leParametre"]["param_type"];
                    var value = json_retour["data"]["leParametre"]["param_value"];
                    switch (type) {
                        case "int":
                            strResponse = parseInt(value);
                            break;
                        case "float":
                            strResponse = this.arrondir(parseFloat(value),2);
                            break;
                        case "varchar":
                            strResponse =  value;
                            break;
                        default:
                            strResponse =  value;
                            break;
                    }
                } 

               return strResponse;
           } catch (er) {
               this.log(0, "ERROR($.functionsLib.getParameter):" + er.message);
               return null;
           }
        },
        
        /**
        * setParameter
        * #ex $.functionsLib.setParameter("contact_mail_administrateur", "hello@gmail.com");
        * @param {string} p_param_name
        * @param {string} p_param_name
        * @returns { int|varchar }
        */
        setParameter : function(p_param_name, p_param_value) {
           try {
               var strResponse;

               var tabInput = { 
                   param_name : p_param_name,
                   param_value : p_param_value
               };
               var json_retour = this.callRest(domaine+"API/phpsql/setParam.php", {}, tabInput);  

               return strResponse;
           } catch (er) {
               this.log(0, "ERROR($.functionsLib.setParameter):" + er.message);
               return null;
           }
        },
        
        /**
         * @name getUserInfo
         * @example $.functionsLib.getUserInfo();
         * @returns {json}
         */
        getUserInfo : function() {
            try {
               return _userInfo;
           } catch (er) {
               this.log(0, "ERROR($.functionsLib.getUserInfo):" + er.message);
               return null;
           }
        },
        
        /**
        * getParamGet
        * @param {String} name
        * @returns {String}
        */
        getParamGet : function(name) {
            try {
                var strUrl = this.currentWindow.location.href;
                strUrl = decodeURIComponent(strUrl);
                var start = strUrl.indexOf("?" + name + "=");
                if (start < 0) start = strUrl.indexOf("&" + name + "=");
                if (start < 0) return null;
                start += name.length + 2;
                var end = strUrl.indexOf("&", start) - 1;
                if (end < 0) end = strUrl.length;
                var result = '';
                for (var i = start; i <= end; i++) {
                    var c = strUrl.charAt(i);
                    result = result + (c == '+' ? ' ' : c);
                }
                return unescape(result);
            } catch (er) {
                this.log(0, "ERROR($.functionsLib.getParamGet):" + er.message);
                return null;
            }
        },
        
        /**
         * @name addStat
         * @test addStat('ADMI', 'page_home.html', 'checkAuth : ok');
         * @param {String} p_user
         * @param {String} p_page
         * @param {String} p_action
         */
        addStat : function(p_user, p_page, p_action) {
           try {
                var tabSetting = { };
                var tabInput = { 
                    user : p_user,
                    page : p_page,
                    action : p_action
                };
                var retour = $.functionsLib.callRest(domaine+"API/phpsql/addStat.php", tabSetting, tabInput);
           } catch (er) {
               this.log(0, "ERROR($.functionsLib.addStat):" + er.message);
           }
        },
        
        /**
         * setCookie
         * @param {string} p_name
         * @param {string} p_value
        */
        setCookie : function(p_name,p_value){
            try {
                var retour = $.functionsStorage.set(p_name, p_value, 43200);
            } catch (er) {
                this.log(0, "ERROR($.functionsLib.setCookie):" + er.message);
           }
        },
        
        /**
         * @name getCookie
         * @param {string} p_name
         * @returns {String}
        */
        getCookie: function(p_name) {
           try {
               var returnvalue = "";
               
               returnvalue = $.functionsStorage.get(p_name, "");

               return returnvalue;
           } catch (er) {
               this.log(0, "ERROR($.functionsLib.get_cookie):" + er.message);
               return null;
           }
        },
        
        /**
         * @name getI8n
         * @param {string} p_group
         * @param {string} p_tag
         * @returns {String}
        */
        getI8n: function(p_group, p_tag) {
            try {
                var returnvalue = "Not define";
                
                for (var grpId in _i8n) {
                    var grp = _i8n[grpId];
                    if(grp.groupName == p_group){
                        var trad = grp[_userInfo.locale][p_tag];
                        if(!$.functionsLib.isUndefined(trad)){
                            returnvalue = trad;
                        }
                        break;
                    }
                }

                return returnvalue;
            } catch (er) {
                this.log(0, "ERROR($.functionsLib.getI8n):" + er.message);
                return null;
            }
        },
        
        /**
         * @name delCookie
         * @param {string} p_name
         */
        delCookie : function(p_name) {
            $.functionsStorage.remove(p_name);
        },
        
        /**
         * getter
         * @param {string} p_table
         * @param {string} p_get
         * @param {string} p_filtre
         * @returns {json}
         */
        getter :  function(p_table, p_get, p_filtre) {
            try {
                var tabInput = { table : p_table, get : p_get, filtre : p_filtre };
                var json_retour = this.callRest(domaine+"API/phpsql/getter.php", {type : 'POST'}, tabInput);

                var valeur = null;

                if(json_retour["strErreur"] == ""){

                    switch (json_retour["data"]["resultat"]["type"]) {
                        case "PARAM_STR":
                            valeur = json_retour["data"]["resultat"]["champ"];
                            break;
                        case "PARAM_INT":
                            valeur = parseInt(json_retour["data"]["resultat"]["champ"]);
                            break;
                       case "PARAM_FLOAT":
                            valeur = parseFloat(json_retour["data"]["resultat"]["champ"]);
                            break;
                       default:
                            valeur = json_retour["data"]["resultat"]["champ"];
                            break;
                    }
                }else{
                    this.notification(json_retour["strErreur"],this.oda_msg_color.ERROR);
                }

                return valeur;
            } catch (er) {
              this.log(0, "ERROR($.functionsLib.getter):" + er.message);
              return null;
            }
        },
        
        /**
         * geRangs
         * @returns {json}
         */
        geRangs :  function() {
            try {
                var valeur = $.functionsStorage.get("ODA_rangs");
                if(valeur == null){
                
                    var tabInput = { "sql" : "Select `labelle`,`indice` FROM `api_tab_rangs` ORDER BY `indice` desc" };
                    var json_retour = this.callRest(domaine+"API/phpsql/getSQL.php", {type : 'POST'}, tabInput);

                    if(json_retour["strErreur"] == ""){
                        valeur = json_retour.data.resultat.data;
                    }else{
                        this.notification(json_retour["strErreur"],this.oda_msg_color.ERROR);
                    }
                
                    $.functionsStorage.set("ODA_rangs",valeur,$.functionsStorage.ttl_default);
                }

                return valeur;
            } catch (er) {
              this.log(0, "ERROR($.functionsLib.geRangs):" + er.message);
              return null;
            }
        },
        
        /**
        * @name getSQL
        * @param {String} p_sql
        * @returns {Array}
        */
        getSQL : function(p_sql) {
           try {
               var tabInput = { sql : p_sql };
               var retour = this.callRest(domaine+"API/phpsql/getSQL.php", {}, tabInput);

               return retour;
           } catch (er) {
               this.log(0, "ERROR($.functionsLib.getSQL):" + er.message);
               return null;
           } 
        },
        
        /**
        * insertSQL
        * @param {String} p_sql
        * @returns {String|Array}
        */
        insertSQL : function(p_sql) {
            try {
                var tabInput = { sql : p_sql };
                var retour = this.callRest(domaine+"API/phpsql/insertSQL.php", {}, tabInput);

                return retour;
            } catch (er) {
                this.log(0, "ERROR($.functionsLib.insertContact):" + er.message);
                return null;
            } 
        },
        
        /**
        * insertContact
        * @param {String} p_reponse
        * @param {String} p_message
        * @param {String} p_code_user
        * @returns {Array}
        */
        insertContact : function(p_reponse, p_message, p_code_user) {
            try {
                var tabSetting = { type : "post" };
                var tabInput = { reponse : p_reponse, message : p_message, code_user : p_code_user };
                var retour = this.callRest(domaine+"API/phpsql/insertContact.php", tabSetting, tabInput);

                return retour;
            } catch (er) {
                this.log(0, "ERROR($.functionsLib.insertContact):" + er.message);
                return null;
            } 
        },
        
        /**
        * @name deleteSQL
        * @param {String} p_sql
        * @returns {String|Array}
        */
        deleteSQL : function(p_sql) {
            try {
                var tabInput = { sql : p_sql };
                var retour = this.callRest(domaine+"API/phpsql/deleteSQL.php", {}, tabInput);

                return retour;
            } catch (er) {
                this.log(0, "ERROR($.functionsLib.deleteSQL):" + er.message);
                return null;
            } 
        },
        
        /**
        * @name displayCaptcha
        * @param {object} p_params
        * @ex : $.functionsLib.displayCaptcha({'div':'divCatchar', 'functionRetour':function(data){_trace(data);}});
        * @returns {String|Array}
        */
        displayCaptcha : function(p_params) {
            try {
                var params_attempt = {
                    div : null
                    , functionRetour : null
                };
                
                var params = _checkParams(p_params, params_attempt);
                if(params == null){
                    return false;
                }
                
                $.getScript("https://www.google.com/recaptcha/api.js?onload=onloadCallbackRecaptcha&render=explicit")
                    .done(function( script, textStatus ) {
                    })
                    .fail(function( jqxhr, settings, exception ) {
                        $.functionsLib.log(0, "recaptcha.js : Triggered ajaxError handler.(code:"+jqxhr.status+')');
                    })
                ;

                window.captchaContainer = null;
                window.onloadCallbackRecaptcha = function() {
                    captchaContainer = grecaptcha.render(params.div, 
                    {
                        'sitekey' : '6LcHdgATAAAAAKNPrvikd5ySmIbGS73-SmqjPJ72',
                        'callback' : function(response) {
                            var tabSetting = {'type':'GET', 'crossDomain' : true };
                            var tabInput = { 
                                'secret' : '6LcHdgATAAAAALSoX4WwOq9oOaQZPxksmTuU6nkU'
                                , 'response' : response
                            };
                            var retour = $.functionsLib.callRest("https://www.google.com/recaptcha/api/siteverify", tabSetting, tabInput);
                            if(retour.success){
                                params.functionRetour(true);
                            }else{
                                params.functionRetour(false);
                            }
                        }
                    });
                };
                
                return true;
            } catch (er) {
                this.log(0, "ERROR($.functionsLib.displayCaptcha):" + er.message);
                return null;
            } 
        },
        
        /**
         * @name cacherAide
         * @param {String} p_user
         */
        cacherAide : function(p_user) {
            try {
                var tabInput = { code_user : p_user, valeur : 0};
                var retour = this.callRest(domaine+"API/phpsql/setMontrer_aide_ihm.php", {}, tabInput);
                $("#div_aide_ihm").hide();
            } catch (er) {
                this.log(0, "ERROR($.functionsLib.cacherAide):" + er.message);
            }
        },
        
        /**
        * objDataTableFromJsonArray
        * 
        * @param {object} p_JsonArray
        * @returns {object}
        */
        objDataTableFromJsonArray : function(p_JsonArray){
            try {
                var objRetour = { statut : "ok"};

                var arrayEntete = {};
                var i = 0;
                for(var key in p_JsonArray[0]){
                    arrayEntete[key] = i;
                    i++;
                }
                objRetour.entete = arrayEntete;

                var arrayData = new Array();
                for(var indice in p_JsonArray){
                    var subArrayData = new Array();
                    for(var key in p_JsonArray[indice]){
                        subArrayData[subArrayData.length] = p_JsonArray[indice][key];
                    } 
                    arrayData[arrayData.length] = subArrayData;
                }

                objRetour.data = arrayData;

                return objRetour;
            } catch (er) {
                this.log(0, "ERROR($.functionsLib.objDataTableFromJsonArray):" + er.message);
                var objRetour = { statut : "ko"};
                return objRetour;
            }
        },
        
        /**
        * affichePopUp
        * @param {type} p_label
        * @param {type} p_details
        */
        affichePopUp : function(p_label, p_details){
            try {
                $('#label_popup').html("<b>"+p_label+"</b>").trigger('create');
                $('#div_popup').html(p_details).trigger('create');
                $('#popup').popup('open', {transition : 'flip'});
            } catch (er) {
                this.log(0, "ERROR($.functionsLib.affichePopUp):" + er.message);
            }
        },
        
        /**
        * arrondir
        * @param {float|int} p_value
        * @param {int} p_precision
        * @returns {float|int}
        */
        arrondir : function(p_value, p_precision){
            try {
                var retour = 0;
                var coef = Math.pow(10, p_precision);

                if(coef != 0){
                    retour = Math.round(p_value*coef)/coef;
                }else{
                    retour = Math.round(p_value);
                }

                return retour;
            } catch (er) {
                this.log(0, "ERROR($.functionsLib.arrondir):" + er.message);
                return null;
            }
        },
        
        /**
        * isInArray
        * @param {string} p_value
        * @param {array} p_array
        * @returns {Boolean}
        */
        isInArray :  function(p_value, p_array) {
            try {
                var boolRetour = false;

                for(var indice in p_array){
                    if(p_value == p_array[indice]){
                        boolRetour = true;
                        break;
                    }
                }

                return boolRetour;
            } catch (er) {
                this.log(0, "ERROR($.functionsLib.isInArray):" + er.message);
                return null;
            }
        },
        
        /**
        * @name getMilise
        * @returns {string}
        */
        getMilise : function() {
            try {
                var d = new Date();
                return d.getTime();
            } catch (er) {
                this.log(0, "ERROR($.functionsLib.getMilise):" + er.message);
                return null;
            }
        },
        
        /**
         * getRecupUtilisateur
         * @param {String} p_nom
         * @param {String} p_prenom
         * @param {String} p_email
         * @param {String} p_codeUtilisateur
         * @returns {Array}
         */
        getRecupUtilisateur : function(p_identifiant, p_email) {
            try {
                var tabInput = { identifiant : p_identifiant, email : p_email};
                var retour = this.callRest(domaine+"API/phpsql/getRecupUtilisateur.php", {}, tabInput);

                return retour;
            } catch (er) {
                this.log(0, "ERROR($.functionsLib.getRecupUtilisateur):" + er.message);
                return null;
            } 
        },
        
        /**
        * @name : getStrDate
        */
        getStrDate : {
            getStrDateFR : function(){
                try {
                    var currentTime = new Date();
                    var annee = currentTime.getFullYear();
                    var mois = $.functionsLib.pad2(currentTime.getMonth()+1);
                    var jour = $.functionsLib.pad2(currentTime.getDate());
                    var strDate = jour + "/" + mois + "/" + annee;
                    return strDate;
                } catch (er) {
                    this.log(0, "ERROR($.functionsLib.getStrDate.getStrDateFR()):" + er.message);
                    return null;
                }
            },
            
            /**
            * getStrDateTimeFrFromUs
            * @param {String} p_strDateTime
            * @returns {String}
            */
            getStrDateTimeFrFromUs : function(p_strDateTime) {
                try {
                    var strDate = "";

                    strDate = p_strDateTime.substr(8,2) + "/" + p_strDateTime.substr(5,2) + "/" + p_strDateTime.substr(0,4) + " " + p_strDateTime.substr(10,(p_strDateTime.length - 10)); 

                    return strDate;
                } catch (er) {
                    this.log(0, "ERROR($.functionsLib.getStrDate.getStrDateTimeFrFromUs):" + er.message);
                    return null;
                }
            },
            
            /**
            * getStrDateFrFromUs
            * @param {String} p_strDate
            * @returns {String}
            */
            getStrDateFrFromUs : function(p_strDate) {
                try {
                    var strDate = "";

                    strDate = p_strDate.substr(8,2) + "/" + p_strDate.substr(5,2) + "/" + p_strDate.substr(0,4); 

                    return strDate;
                } catch (er) {
                    this.log(0, "ERROR($.functionsLib.getStrDate.getStrDateFrFromUs):" + er.message);
                    return null;
                }
            },
            
            /**
            * @name convertSecondsToTime
            * @desc Seconds to hh:mm:ss
            * @param {int} p_second
            * @returns {String}
            */
            convertSecondsToTime : function(p_second) {
                try {
                    var sec_num = p_second;
                    var hours   = Math.floor(sec_num / 3600);
                    var minutes = Math.floor((sec_num - (hours * 3600)) / 60);
                    var seconds = sec_num - (hours * 3600) - (minutes * 60);

                    if (hours   < 10) {hours   = "0"+hours;}
                    if (minutes < 10) {minutes = "0"+minutes;}
                    if (seconds < 10) {seconds = "0"+seconds;}
                    var time    = hours+':'+minutes+':'+seconds;
                    return time;
                } catch (er) {
                    this.log(0, "ERROR($.functionsLib.getStrDate.convertSecondsToTime):" + er.message);
                    return null;
                }
            },
            
            /**
            * @name getStrDateTime
            * @returns {String}
            */
            getStrDateTime : function() {
                try {
                    var currentTime = new Date();
                    var hours = $.functionsLib.pad2(currentTime.getHours());
                    var minutes = $.functionsLib.pad2(currentTime.getMinutes());
                    var annee = currentTime.getFullYear();
                    var mois = $.functionsLib.pad2(currentTime.getMonth()+1);
                    var jour = $.functionsLib.pad2(currentTime.getDate());
                    var secondes = $.functionsLib.pad2(currentTime.getSeconds());
                    var strDateTime = annee + "/" + mois + "/" + jour + " " + hours + ":" + minutes + ":" + secondes;
                    return strDateTime;
                } catch (er) {
                    this.log(0, "ERROR($.functionsLib.getStrDate.getStrDateTime):" + er.message);
                    return null;
                }
            }
        },
        
        /**
        * pad2
        * @param {String} number
        * @returns {String}
        */
        pad2 : function(number) {
            try {
                return (number < 10 ? '0' : '') + number;
            } catch (er) {
                this.log(0, "ERROR($.functionsLib.pad2):" + er.message);
                return null;
            }
        },
        
        /**
        * ready
        */
        ready : function() {
            try {
                return true;
            } catch (er) {
                this.log(0, "ERROR($.functionsLib.ready):" + er.message);
                return null;
            }
        },
        
        /**
         * @name afficheAideIhm
         * @param {String} p_user
         * @returns {String}
         */
        afficheAideIhm : function(p_user) {
            try {
                var strHTML = "";

                strHTML += "<div data-role=\"collapsible\" data-collapsed=\"false\" id=\"div_aide_ihm\">";
                strHTML += "    <h4>Aide &agrave; la navigation</h4>";
                strHTML += "    <div class=\"ui-grid-a ui-responsive\">";
                strHTML += "        <div class=\"ui-block-a\">";
                strHTML += "            <img src=\"API/img/ihm1.png\" alt=\"IHM\" border=\"1\">";
                strHTML += "        </div>";
                strHTML += "        <div class=\"ui-block-b\">";
                strHTML += "            <table data-role=\"table\" id=\"movie-table\" data-mode=\"reflow\" class=\"ui-responsive table-stroke ui-table ui-table-reflow\">";
                strHTML += "              <thead>";
                strHTML += "                <tr>";
                strHTML += "                  <th data-priority=\"1\">Icons</th>";
                strHTML += "                  <th data-priority=\"2\">Actions</th>";
                strHTML += "                </tr>";
                strHTML += "              </thead>";
                strHTML += "              <tbody>";
                strHTML += "                <tr>";
                strHTML += "                  <td><img src=\"API/img/bt_home.png\" alt=\"Menu\" border=\"0\"></td>";
                strHTML += "                  <td>Permet d'afficher le menu</td>";
                strHTML += "                </tr>";
                strHTML += "                <tr>";
                strHTML += "                  <td><img src=\"API/img/bt_deplier.png\" alt=\"Plier/D&eacute;plier\" border=\"0\"></td>";
                strHTML += "                  <td>Permet de plier ou d&eacute;plier un encart</td>";
                strHTML += "                </tr>";
                strHTML += "                <tr>";
                strHTML += "                  <td><img src=\"API/img/bt_info.png\" alt=\"Contact\" border=\"0\"></td>";
                strHTML += "                  <td>Permet d'afficher la partie Contact</td>";
                strHTML += "                </tr>";
                strHTML += "                <tr>";
                strHTML += "                  <td><img src=\"API/img/bt_sortie.png\" alt=\"Sortie\" border=\"0\"></td>";
                strHTML += "                  <td>Permet de sortir de l'application</td>";
                strHTML += "                </tr>";
                strHTML += "                <tr>";
                strHTML += "                  <td><img src=\"API/img/bt_faq.png\" alt=\"FAQ\" border=\"0\"></td>";
                strHTML += "                  <td>Permet d'afficher la foire aux questions</td>";
                strHTML += "                </tr>";
                strHTML += "            </table>";
                strHTML += "        </div>";
                strHTML += "    </div>";    
                strHTML += "    <p>Astuce : Dans les pages longues pour retrouver &agrave; tout moment l'interface, un clic ou un pression dans la page le rappel.</p>";    
                if(p_user != ''){
                    strHTML += "    <a href=\"\" onClick=\"$.functionsLib.cacherAide('"+p_user+"');\" data-role=\"button\" data-inline=\"true\" data-icon=\"arrow-r\" data-mini=\"true\">Ne plus revoir l'aide</a>";    
                }
                strHTML += "</div>";        

                if(p_user != ''){
                    $("#main_content").prepend(strHTML).trigger('create');
                }else{
                    return strHTML;
                }
            } catch (er) {
                this.log(0, "ERROR($.functionsLib.afficheAideIhm):" + er.message);
            }
        },
        
        /**
        * @name : loadDepends
        */
        loadDepends : function(p_depends, p_functionFeedback){
            try {
                if(_dependecies == null){
                    _dependecies = p_depends;
                }
                
                if(_dependeciesFeedback == null){
                    _dependeciesFeedback = p_functionFeedback;
                }
                
                var retour = true;

                for (var indice in _dependecies) {
                    if((this.isUndefined(_dependecies[indice].status)) || (_dependecies[indice].status != "done")){
                        _trace("Loading list of dependecies : "+_dependecies[indice].name+".");
                        _dependecies[indice].status = "doing";
                        if(_dependecies[indice].ordered){
                            _loadListDependsOrdoned();
                            retour = false;
                            break;
                        }else{
                            for (var indiceList in _dependecies[indice].list) {
                                var elt = _dependecies[indice].list[indiceList];
                                _loadDepend(elt,"paral");
                            }
                            _dependecies[indice].status = "done";
                        }
                    }
                }

                return retour;
            } catch (er) {
               this.log(0, "ERROR($.functionsLib.loadDepends):" + er.message);
            }
        },
        
        /**
        * @name : logout
        */
        logout : function(){
           try {
                var session = $.functionsStorage.get("ODA-SESSION");
                var tabInput = { 
                    "key" : session.key
                };
                var retour = $.functionsLib.callRest(domaine+"API/phpsql/deleteSession.php", {}, tabInput); 
                $.functionsStorage.remove("ODA-SESSION");
           } catch (er) {
               this.log(0, "ERROR($.functionsLib.logout):" + er.message);
           }
        },
    
        /**
         * login
         * @param {string} p_log
         * @param {string} p_pass
         * @param {string} p_page
         * @returns {object}
         */
        login : function(p_log, p_pass, p_page){
           try {
               var retour = { statut : "ok", message : ""};

               if((p_log == null)||(p_log == "")){
                   retour.message += "Pas d'identifiant.";
                   retour.statut = "ko";
               }

               if((p_pass == null)||(p_pass == "")){
                   retour.message += "Pas de mot de passe.";
                   retour.statut = "ko";
               }

               if(retour.statut == "ok"){
                    var tabInput = { login : p_log, mdp : p_pass };
                    var returns = $.functionsLib.callRest(domaine+"API/phpsql/getAuth.php", {}, tabInput); 
                    if(returns["strErreur"] == ""){
                        var code_user = returns["data"]["resultat"]["code_user"].toUpperCase();
                        var key = returns["data"]["resultat"]["keyAuthODA"];

                        var session = {
                            "code_user" : code_user
                            , "key" : key
                        };

                        $.functionsStorage.set("ODA-SESSION",session,43200);

                        $.functionsLib.addStat(p_log,p_page,"checkAuth : ok");
                        retour.message = p_log.toUpperCase();
                    }else {
                       $.functionsLib.addStat(p_log,p_page,"checkAuth : ko("+p_pass+")");
                       retour.message = returns["strErreur"];
                       retour.statut = "ko";
                    }
               }
               return retour;
           } catch (er) {
               this.log(0, "ERROR($.functionsLib.login):" + er.message);
               return null;
           }
        },
        
        /**
         * @name : fermerPopUp
         */
        fermerPopUp : function(){
            try {
                $('#label_popup').html('');
                $('#div_popup').html('');
                $('#popup').popup('close', {transition : 'flip'});
            } catch (er) {
                this.log(0, "ERROR($.functionsLib.fermerPopUp):" + er.message);
            }
        },
        
        /**
        * getListValeurPourAttribut
        * @param {json} p_obj
        * @param {string} p_attribut
        * @returns {Array}
        */
        getListValeurPourAttribut : function(p_obj, p_attribut, p_type) {
            try {
                var retour = new Array();

                for (var indice in p_obj) {
                    for (var key in p_obj[indice]) {
                        if(key == p_attribut){
                            if(typeof p_type !== "undefined"){
                                if(p_type == "int"){
                                    var valeur = parseInt(p_obj[indice][key]);
                                }else if(p_type == "float"){
                                    var valeur = parseFloat(p_obj[indice][key]);
                                }else{
                                    var valeur = new p_type(p_obj[indice][key]);
                                }
                                retour[retour.length] = valeur;
                            }else{
                                retour[retour.length] = p_obj[indice][key];
                            }
                        }
                    }
                }

                return retour;
            } catch (er) {
                this.log(0, "ERROR($.functionsLib.getListValeurPourAttribut):" + er.message);
                var retour = new Array();
                return retour;
            }
        },
        
        /**
        * @name testMaintenance
        * @return {boolean} description
        */
        testMaintenance : function() {
            return _checkMaintenance();
        },
        
        /**
        * @name testEvents
        * @return {boolean} description
        */
        testEvents : function() {
            var source = new EventSource(domaine+"API/phpsql/events.php");
            
            source.addEventListener('time', function(e) {
                var data = JSON.parse(e.data);
                _trace('Server is now ' + data.time);
              }, false);
            
            return true;
        },
        
        /**
        * @name testAuthentification
        * @return {boolean} description
        */
        testAuthentification : function() {
            return _checkAuthentification();
        },
        
        /**
        * @name whatIsIt
        * @param {type} object
        * @returns {String}
        */
        whatIsIt : function(object) {
           var stringConstructor = "test".constructor;
           var arrayConstructor = [].constructor;
           var objectConstructor = {}.constructor;

           if (object === null) {
               return "null";
           }
           else if (object === undefined) {
               return "undefined";
           }
           else if (object.constructor === stringConstructor) {
               return "String";
           }
           else if (object.constructor === arrayConstructor) {
               return "Array";
           }
           else if (object.constructor === objectConstructor) {
               return "Object";
           }
           else {
               return "don't know";
           }
       },
       
       /**
        * sleep
        * @Desc Pour patienter
        * @param {int} milliseconds
        */
        sleep : function(milliseconds) {
          var start = new Date().getTime();
          for (var i = 0; i < 1e7; i++) {
            if ((new Date().getTime() - start) > milliseconds){
              break;
            }
          }
        },
        
        /**
        * sendFile
        * @param {type} p_fichiers
        * @param {type} p_dossier
        * @param {type} p_nom
        */
        sendFile : function(p_fichier, p_dossier, p_nom) {
            try {
                var retour = { appel : "ko", code : "ko", message : "init" };

                var strUrl = domaine+'API/scriptphp/uploadFile.php?dossier='+p_dossier+'&nom='+p_nom;

                var ajax = $.ajax({
                    url: strUrl,
                    data: p_fichier,
                    cache: false,
                    contentType: false,
                    processData: false,
                    type: 'POST',
                    async:false,
                    success : function(p_resultat, p_statut){
                        var json_retour = JSON.parse(p_resultat);
                        retour.appel = "ok";
                        retour.code = json_retour["code"];
                        retour.message = json_retour["message"];
                    },
                    error : function(p_resultat, p_statut, p_erreur){
                        var json_retour = JSON.parse(p_resultat);
                        retour.appel = "ko";
                        retour.code = json_retour["code"];
                        retour.message = json_retour["message"];
                    }
                });   

                return retour;
            } catch (er) {
                this.log(0, "ERROR($.functionsLib.sendFile):" + er.message);
                return null;
            }
        },
        
        /**
        * @name isUndefined
        * @desc is ndefined N
        * @param {object} p_object
        * @returns {Worker}
        */
        isUndefined : function(p_object) {
            try {
                var boolReturn = true;
                
                if(typeof p_object != "undefined"){
                    boolReturn = false;
                }

                return boolReturn;
            } catch (er) {
                this.log(0, "ERROR($.functionsLib.isUndefined):" + er.message);
                return null;
            }
        },
        
        /**
        * @name initWorker
        * @desc pour initialiser un worker 
        * @param {string} p_nameWorker
        * @param {string} p_fonctionRetour
        * @returns {Worker}
        */
        initWorker : function(p_nameWorker, p_fonctionRetour) {
            try {
                var blob = new Blob([document.querySelector('#'+p_nameWorker).textContent], {type: 'text/javascript'});

                var monWorker = new Worker(window.URL.createObjectURL(blob));

                monWorker.addEventListener("message", function (event) {
                    p_fonctionRetour(event.data);
                }, false);

                // On démarre le worker en lui envoyant la commande 'init'
                monWorker.postMessage(new WorkerMessage('init', null));

                return monWorker;
            } catch (er) {
                this.log(0, "ERROR($.functionsLib.initWorker):" + er.message);
            }
        },
        
        /**
        * @name terminateWorker
        * @desc pour finir le worker
        * @param {type} p_worker
        * @returns {undefined}
        */
        terminateWorker : function(p_worker) {
            try {
                // On aurait pu créer une commande 'stop' qui aurait été traitée
                // au sein du worker qui se serait fait hara-kiri via .close()
                p_worker.terminate();
            } catch (er) {
                this.log(0, "ERROR($.functionsLib.terminateWorker):" + er.message);
            }
        }
    };
    
    $.functionsMobile = {
        /* Version number */
        version: VERSION,
        
        initModuleMobile : function(){
            try {
                var boolRetour = true;

                _networkState = navigator.connection.type;
                _pictureSource = navigator.camera.PictureSourceType;
                _destinationType = navigator.camera.DestinationType;

                return boolRetour;
            } catch (er) {
                log(0, "ERROR($.functionsMobile.initModuleMobile):" + er.message);
                return null;
            }
        },
        
        ///////////////// PART NETWORK
        
        /**
         * 
         * @returns {Boolean}
         */
        getConnectionString : function(p_networkState){
            try {
                var retour = "";

                var states = {};
                states[Connection.UNKNOWN]  = 'Unknown connection';
                states[Connection.ETHERNET] = 'Ethernet connection';
                states[Connection.WIFI]     = 'WiFi connection';
                states[Connection.CELL_2G]  = 'Cell 2G connection';
                states[Connection.CELL_3G]  = 'Cell 3G connection';
                states[Connection.CELL_4G]  = 'Cell 4G connection';
                states[Connection.NONE]     = 'No network connection';

                retour = 'Connection type: ' + states[p_networkState];

                return retour;
            } catch (er) {
                log(0, "ERROR($.functionsMobile.getConnectionString):" + er.message);
                return null;
            }
        },

        /**
         * 
         * @returns {Boolean}
         */
        testConnection : function(){
            try {
                var boolRetour = true;

                alert(this.getConnectionString(_networkState));

                return boolRetour;
            } catch (er) {
                log(0, "ERROR($.functionsMobile.testConnection):" + er.message);
                return null;
            }
        },
        
        ///////////////// PART GPS
        
        getGpsPosition: function(p_onReturn){
            try {
                var boolRetour = true;
                
                _funcReturnGPSPosition = p_onReturn;

                navigator.geolocation.getCurrentPosition(_onSuccessGPSPosition, _onErrorGPSPosition);

                return boolRetour;
            } catch (er) {
                log(0, "ERROR($.functionsMobile.getGpsPosition):" + er.message);
                return null;
            }
        },
        
        getGpsPositionString: function(p_position){
            try {
                var retour = "";
                
                retour += 'Latitude: '      + p_position.coords.latitude            + '\n' +
                'Longitude: '               + p_position.coords.longitude           + '\n' +
                'Altitude: '                + p_position.coords.altitude            + '\n' +
                'Accuracy: '                + p_position.coords.accuracy            + '\n' +
                'Altitude Accuracy: '       + p_position.coords.altitudeAccuracy    + '\n' +
                'Heading: '                 + p_position.coords.heading             + '\n' +
                'Speed: '                   + p_position.coords.speed               + '\n' +
                'Timestamp: '               + p_position.timestamp                  + '\n' +
                'statut: '                  + p_position.statut                     + '\n';

                return retour;
            } catch (er) {
                log(0, "ERROR($.functionsMobile.getGpsPositionString):" + er.message);
                return null;
            }
        },
        
        ///////////////// PART CAMERA
        getPhotoFromCamera: function(p_retourCapture){
            try {
                var boolRetour = true;
                
                _funcReturnCaptureImg = p_retourCapture;

                navigator.camera.getPicture(_onPhotoSuccess, _onPhotoFail, { quality: 50, destinationType: _destinationType.DATA_URL });

                return boolRetour;
            } catch (er) {
                log(0, "ERROR($.functionsMobile.getPhotoFromCamera):" + er.message);
                return null;
            }
        },
        
        
        getPhotoFromLibrary: function(p_retourCapture){
            try {
                var boolRetour = true;
                
                _funcReturnCaptureImg = p_retourCapture;

                navigator.camera.getPicture(_onPhotoURISuccess, _onPhotoFail, { quality: 50, destinationType: _destinationType.FILE_URI, sourceType: _pictureSource.PHOTOLIBRARY });

                return boolRetour;
            } catch (er) {
                log(0, "ERROR($.functionsMobile.getPhotoFromLibrary):" + er.message);
                return null;
            }
        }
        
    };
    
    $.functionsStorage = {
        /* Version number */
        version : VERSION,
        ttl_default : 86400, //24H
        storageKey : "",

        /**
         * 
         * @returns {Boolean}
         */
        isStorageAvaible: function(){
            try {
                var boolRetour = false;

                if (localStorage) {
                    boolRetour = true;
                } 

                return boolRetour;
            } catch (er) {
                $.functionsLib.log(0, "ERROR(functionsStorage.isStorageAvaible):" + er.message);
                return null;
            }
        },

        /**
         * 
         * @param {string} p_key
         * @param {json} p_value
         * @param {integer} p_ttl in seconde
         * @returns {Boolean}
         */
        set: function(p_key, p_value, p_ttl) {
            try {
                var boolRetour = true;
                
                var d = new Date();
                var date = d.getTime();
                
                var ttl = 0;
                if(typeof(p_ttl) != 'undefined'){
                    ttl = p_ttl;
                }
                
                var storage = {
                    "value" : p_value
                    , "recordDate" : date
                    , "ttl" : ttl
                };
                
                if(boolRetour){
                    var json_text = JSON.stringify(storage);

                    localStorage.setItem(this.storageKey+p_key, json_text);
                }

                return boolRetour;
            } catch (er) {
                $.functionsLib.log(0, "ERROR($.functionsStorage.set):" + er.message);
                return null;
            }
        },

        get: function(p_key, p_default) {
            try {
                var myStorage = JSON.parse(localStorage.getItem(this.storageKey+p_key));
                var myValue = null;
                if(myStorage != null){
                    myValue = myStorage.value;

                    if((myStorage.value != null)&&(myStorage.ttl != 0)){
                        var d = new Date();
                        var date = d.getTime();

                        var dateTimeOut = myStorage.recordDate + (myStorage.ttl*1000);

                        if(date > dateTimeOut){
                            this.remove(p_key);
                            myValue = null;
                        }
                    }

                    if((myValue == null)&&(typeof(p_default) != 'undefined')){
                        this.set(p_key, p_default);
                        myValue = p_default;
                    }
                }else{
                    if(typeof(p_default) != 'undefined'){
                        this.set(p_key, p_default);
                        myValue = p_default;
                    }
                }

                return myValue;
            } catch (er) {
                $.functionsLib.log(0, "ERROR(functionsStorage.get):" + er.message);
                return null;
            }
        },

        stopRobot: function() {
            try {
                _terminateRobot();

                return true;
            } catch (er) {
                $.functionsLib.log(0, "ERROR(functionsStorage.stopRobot):" + er.message);
                return null;
            }
        },

        setTtl: function(p_key, p_ttl) {
            try {
                var myValue = this.get(p_key);
                
                var d = new Date();
                var date = d.getTime();
                
                var storage = {
                    "value" : myValue
                    , recordDate : date
                    , ttl : p_ttl
                };
                
                var myReturn = this.set(this.storageKey+p_key, storage);

                return myReturn;
            } catch (er) {
                $.functionsLib.log(0, "ERROR(functionsStorage.setTtl):" + er.message);
                return null;
            }
        },

        getTtl: function(p_key) {
            try {
                var myReturn = 0;
                
                var myStorage = this.get(this.storageKey+p_key);
                
                if(myStorage != null){
                
                    var d = new Date();
                    var date = d.getTime();

                    var dateTimeOut = myValue.recordDate + (myValue.ttl*1000);

                    myReturn = dateTimeOut - date;

                    if(myReturn < 0){
                        myReturn = 0;
                    }
                }

                return myReturn;
            } catch (er) {
                $.functionsLib.log(0, "ERROR(functionsStorage.getTtl):" + er.message);
                return null;
            }
        },

        remove: function(p_key) {
            try {
                var myReturn = true;
                
                localStorage.removeItem(this.storageKey+p_key);

                return myReturn;
            } catch (er) {
                $.functionsLib.log(0, "ERROR(functionsStorage.setTtl):" + er.message);
                return null;
            }
        },

        reset: function() {
            try {
                var myReturn = true;
                
                for (var indice in localStorage) {
                    localStorage.removeItem(indice);
                }

                return myReturn;
            } catch (er) {
                $.functionsLib.log(0, "ERROR(functionsStorage.reset):" + er.message);
                return null;
            }
        },

        getIndex: function(p_filtre) {
            try {
                var myReturn = [];
                
                var patt = new RegExp("ODA_"+p_filtre);
                
                for (var indice in localStorage) {
                    var res = patt.test(indice);
                    if(res){
                        myReturn.push(indice);
                    }
                }

                return myReturn;
            } catch (er) {
                $.functionsLib.log(0, "ERROR(functionsStorage.getIndex):" + er.message);
                return null;
            }
        }
    };

    // Initialize
    _init();

})();