//------------------------------------------------------------------------------
//  ANGULARJS
//------------------------------------------------------------------------------
/**
* Angular : pour le web dynamique
*/
var app = angular.module('odaApp',[]);

app.directive('ngTips', function(){
    return {
        template: function(elem){
            elem.bind('focus', function(){
                $("#tips_"+elem.context.name).html(elem.context.getAttribute("ng-tips"));
            });
            elem.bind('blur', function(){
                $("#tips_"+elem.context.name).html('&nbsp;');
            });
        }
    };
});