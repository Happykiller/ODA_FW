//# sourceURL=OdaLibGoogle.js

/**
 * @author FRO
 * @date 01/05/2015
 */

(function () {
    'use strict';

    var
        _debug = true,
        _gapiLoaded = false,
        _clientId = "249758124548-fgt33dblm1r8jm0nh9snn53pkghpjtsu.apps.googleusercontent.com",
        _apiKey = "PgCsKeWAsVGdOj3KjPn-JPS3",
        _scopes = 'https://www.googleapis.com/auth/userinfo.profile https://www.googleapis.com/auth/userinfo.email',
        _methodeSessionAuthKo = null,
        _methodeSessionAuthOk = null,
        _sessionInfo = null
    ;
    
    ////////////////////////// PRIVATE METHODS ////////////////////////

    /**
     * @name _init
     * @desc Initialize
     */
    function _init() {
        $.functionsGoogle.init();
    }

    ////////////////////////// PUBLIC METHODS /////////////////////////
    $.functionsGoogle = {
        init : function () {
            try {
                $.getScript("https://apis.google.com/js/client.js?onload=handleClientLoad",$.functionsGoogle.handleClientLoad);
                console.log("$.functionsGoogle.init finish.");
            } catch (er) {
                console.log(0, "ERROR - $.functionsGoogle.init :" + er.message);
            }
        },
        handleClientLoad : function() {
            try {
                if(null == gapi.client) {
                    window.setTimeout($.functionsGoogle.handleClientLoad,1000);
                    return;
                }else{
                    _gapiLoaded = true;
                }
                console.log("$.functionsGoogle.handleClientLoad finish.");
            } catch (er) {
                console.log(0, "ERROR - $.functionsGoogle.handleClientLoad :" + er.message);
            }
        },
        startSessionAuth : function(methodOk, methodKo){
            try {
                if(!$.functionsLib.isUndefined(methodOk)){
                    _methodeSessionAuthOk = methodOk;
                }
                if(!$.functionsLib.isUndefined(methodKo)){
                    _methodeSessionAuthKo = methodKo;
                }
                if(_gapiLoaded!=true) {
                    window.setTimeout($.functionsGoogle.startSessionAuth,1000);
                    return;
                }else{
                    $.functionsGoogle.loadGapis([{"api":"oauth2","version":"v2"}], $.functionsGoogle.callbackAuthSession);
                }
                console.log("$.functionsGoogle.startSessionAuth finish.");
            } catch (er) {
                console.log(0, "ERROR - $.functionsGoogle.startSessionAuth :" + er.message);
            }
        },
        loadGapis : function(tabApi, callbackFunction) {
            try {
                if(tabApi.length>0){
                    gapi.client.load(tabApi[0].api, tabApi[0].version,function(resp){
                        if(typeof resp == "undefined"){
                            console.log('Chargement ok pour : '+tabApi[0].api + " en "+tabApi[0].version);
                            tabApi.splice(0,1);
                            $.functionsGoogle.loadGapis(tabApi,callbackFunction);
                        }else{
                            console.log('Chargement ko pour : '+tabApi[0].api + " en "+tabApi[0].version + "("+resp.error.message+")");
                        }
                    });
                }else{
                    callbackFunction();
                }
                console.log("$.functionsGoogle.loadGapis finish.");
            } catch (er) {
                console.log(0, "ERROR - $.functionsGoogle.loadGapis :" + er.message);
            }
        },
        callbackAuthSession : function(){
            try {
                gapi.client.setApiKey(_apiKey);
                gapi.auth.authorize({client_id: _clientId, scope: _scopes, immediate: true}, $.functionsGoogle.handleAuthResult);
                console.log("$.functionsGoogle.callbackLaodGapis finish.");
            } catch (er) {
                console.log(0, "ERROR - $.functionsGoogle.callbackLaodGapis :" + er.message);
            }
        },
        handleAuthResult : function(authResult) {
            try {
                if ((authResult) && (!authResult.error) && (authResult.access_token != undefined)) {
                    _sessionInfo = authResult;
                    _methodeSessionAuthOk();
                } else {
                    _methodeSessionAuthKo();
                }
                console.log("$.functionsGoogle.handleAuthResult finish.");
            } catch (er) {
                console.log(0, "ERROR - $.functionsGoogle.handleAuthResult :" + er.message);
            }
        },
        callServiceGoogleAuth : function(callMethodeOk) {
            try {
                _methodeSessionAuthOk = callMethodeOk;
                gapi.auth.authorize({client_id: _clientId, scope: _scopes, immediate: false}, $.functionsGoogle.handleAuthResult);
                console.log("$.functionsGoogle.callServiceGoogleAuth finish.");
            } catch (er) {
                console.log(0, "ERROR - $.functionsGoogle.callServiceGoogleAuth :" + er.message);
            }
        }
    };

    // Initialize
    _init();

})();