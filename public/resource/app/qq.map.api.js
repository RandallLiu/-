window.qq = window.qq || {};
qq.maps = qq.maps || {};
window.soso || (window.soso = qq);
soso.maps || (soso.maps = qq.maps);
(function () {
    function getScript(src) {
        document.write('<' + 'script src="' + src + '"' +' type="text/javascript"><' + '/script>');
    }
    qq.maps.__load = function (apiLoad) {
        delete qq.maps.__load;
        apiLoad([["2.4.35","",0],["https://open.map.qq.com/","apifiles/2/4/35/mods/","https://open.map.qq.com/apifiles/2/4/35/theme/",true],[1,18,34.519469,104.461761,4],[1479117595565,"https://pr.map.qq.com/pingd","https://pr.map.qq.com/pingd"],["https://apis.map.qq.com/jsapi","https://apikey.map.qq.com/mkey/index.php/mkey/check","https://sv.map.qq.com/xf","https://sv.map.qq.com/boundinfo","https://sv.map.qq.com/rarp","https://search.map.qq.com/","https://routes.map.qq.com/","https://confinfo.map.qq.com/confinfo"],[[null,["https://rt0.map.gtimg.com/tile","https://rt1.map.gtimg.com/tile","https://rt2.map.gtimg.com/tile","https://rt3.map.gtimg.com/tile"],"png",[256,256],1,19,"114",true,false],[null,["https://m0.map.gtimg.com/hwap","https://m1.map.gtimg.com/hwap","https://m2.map.gtimg.com/hwap","https://m3.map.gtimg.com/hwap"],"png",[128,128],4,18,"110",false,false],[null,["https://p0.map.gtimg.com/sateTiles","https://p1.map.gtimg.com/sateTiles","https://p2.map.gtimg.com/sateTiles","https://p3.map.gtimg.com/sateTiles"],"jpg",[256,256],1,19,"101",false,false],[null,["https://rt0.map.gtimg.com/tile","https://rt1.map.gtimg.com/tile","https://rt2.map.gtimg.com/tile","https://rt3.map.gtimg.com/tile"],"png",[256,256],1,19,"",false,false],[null,["https://sv0.map.qq.com/hlrender/","https://sv1.map.qq.com/hlrender/","https://sv2.map.qq.com/hlrender/","https://sv3.map.qq.com/hlrender/"],"png",[256,256],1,19,"",false,false],[null,["https://rtt2.map.qq.com/rtt/","https://rtt2a.map.qq.com/rtt/","https://rtt2b.map.qq.com/rtt/","https://rtt2c.map.qq.com/rtt/"],"png",[256,256],1,19,"",false,false],null,[["https://rt0.map.gtimg.com/vector/","https://rt1.map.gtimg.com/vector/","https://rt2.map.gtimg.com/vector/","https://rt3.map.gtimg.com/vector/"],[256,256],4,18,"114",["https://rt0.map.gtimg.com/icons/","https://rt1.map.gtimg.com/icons/","https://rt2.map.gtimg.com/icons/","https://rt3.map.gtimg.com/icons/"]],null],["https://s.map.qq.com/TPano/v1.1.1/TPano.js","https://map.qq.com/",""]],loadScriptTime);
    };
    var loadScriptTime = (new Date).getTime();
    getScript("https://open.map.qq.com/apifiles/2/4/35/main.js");
})();