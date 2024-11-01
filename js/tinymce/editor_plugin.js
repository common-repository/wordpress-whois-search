(function() {
	tinymce.PluginManager.requireLangPack("WHOIS");

    tinymce.create('tinymce.plugins.WHOIS', {
        init : function(ed, url) {
            ed.addButton('WHOIS', {
                title : 'WHOIS.title',
                image : url + '/whois.png',
                onclick : function() {
                    ed.execCommand('mceInsertContent', false, '[whois]');
                }
            });
        },
        createControl : function(n, cm) {
            return null;
        },
        getInfo : function() {
            return {
                longname : "WHOIS",
                author : 'Tribulant Software',
                authorurl : 'http://tribulant.com',
                infourl : 'http://tribulant.com',
                version : "1.0"
            };
        }
    });
    
    tinymce.PluginManager.add('WHOIS', tinymce.plugins.WHOIS);
})();