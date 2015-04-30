// Library of tools without Jquery

/**
 * @author FRO
 * @date 14/09/23
 */
var µ = {};

//CONSTRUCTOR
/**
 * WorkerMessage : Constructeur pour notre obj message
 * 
 * @param {string} cmd
 * @param {json} parameter
 * @returns {WorkerMessage}
 */
function WorkerMessage(cmd, parameter) {
    this.cmd = cmd;
    this.parameter = parameter;
}

//LIB
(function() {
    'use strict';

    var
        /* version */
        VERSION = '0.1'
    ;
  
    // Initialize
    _init();

    ////////////////////////// PRIVATE METHODS ////////////////////////
    /**
     * @name _init
     * @desc Hello
     */
    function _init() {
    };

    ////////////////////////// PUBLIC METHODS /////////////////////////
    //pas de jquery, pas de dom
    µ.functionsOda = {
        callRest: function(p_url, p_tabSetting, p_tabInput) {
            try {
                var jsonAjaxParam = {
                    url : p_url
                    , contentType : 'application/x-www-form-urlencoded; charset=UTF-8'
                    , dataType : 'json'
                    , type : 'GET'
                };

                //création du jeton pour la secu
                var key = µ.functionsOda.getCookie("key");
                if((typeof key == 'undefined')||(key == '')||(key == 'pasDeTableSession')){
                   if(typeof p_tabInput["keyAuthODA"] != 'undefined'){
                       key = p_tabInput["keyAuthODA"];
                       delete p_tabInput["keyAuthODA"];
                   }else{
                       key = "";
                   } 
                }

                p_tabInput.milis = µ.functionsOda.getMilise();
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

                //Utilisé notament pour les workers qui ne peuvent avoir Jquey et donc Ajax
                var xhr_object = new XMLHttpRequest();

                switch (jsonAjaxParam.type){ 
                    case "GET": 
                    case "get": 
                        var url = jsonAjaxParam.url+"?tag=1";

                        for(var key in jsonAjaxParam.data){
                           var param = jsonAjaxParam.data[key].toString();
                           url += "&"+key+"="+(param.replace(new RegExp("&", "g"), '%26'));
                        }

                        xhr_object.open(jsonAjaxParam.type, url, false);
                        xhr_object.send(null);
                        break;  
                    case "POST": 
                    case "post": 
                    default: 
                        var params = "tag=1";
                        for(var key in jsonAjaxParam.data){
                           var param = jsonAjaxParam.data[key].toString();
                            params += "&"+key+"="+(param.replace(new RegExp("&", "g"), '%26'));
                        }

                        xhr_object.open(jsonAjaxParam.type,jsonAjaxParam.url, false);
                        xhr_object.setRequestHeader("Content-type","application/x-www-form-urlencoded");
                        xhr_object.send(params);
                        break; 
                }

                v_retourSync = "Vide";
                switch (jsonAjaxParam.dataType) { 
                    case "json": 
                        if (xhr_object.readyState == 4 && xhr_object.status == 200) {
                            v_retourSync = JSON.parse(xhr_object.responseText);
                        } else {
                            v_retourSync = "ERROR";
                        }
                        break;   
                    case "text": 
                    default: 
                        if (xhr_object.readyState == 4) {
                            v_retourSync = xhr_object.responseText;
                        } else {
                            v_retourSync = "ERROR";
                        }
                    break; 
                }

                delete self.xhr_object;

                return v_retourSync;
            } catch (er) {
                var msg = "ERROR(µ.functionsOda.callRest):" + er.message;
                this.log(0, msg);
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
                this.log(0, "ERROR(µ.functionsOda.getMilise):" + er.message);
                return null;
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
               console.log(p_msg);
               return true;
           } catch (er) {
               throw new Error("ERROR(µ.functionsOda.log):" + er.message);
               return false;
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
               this.log(0, "ERROR(µ.functionsOda.getSQL):" + er.message);
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
                log(0, "ERROR(µ.functionsOda.getStrDate.convertSecondsToTime):" + er.message);
                return null;
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

               if(typeof document != 'undefined'){
                   var search = p_name + "="
                   if (document.cookie.length > 0) {
                       var offset = document.cookie.indexOf(search);
                       // if cookie exists
                       if (offset != -1) { 
                           offset += search.length
                           // set index of beginning of value
                           var end = document.cookie.indexOf(";", offset);
                           // set index of end of cookie value
                           if (end == -1) end = document.cookie.length;
                               returnvalue=unescape(document.cookie.substring(offset, end))
                       }
                   }
               }

               return returnvalue;
           } catch (er) {
               this.log(0, "ERROR(µ.functionsOda.get_cookie):" + er.message);
               return null;
           }
        }
    };
})();