/* Author: Ismael Serrano 2011 */
var BrowserDetect = {
	init: function () {
		this.browser = this.searchString(this.dataBrowser) || "An unknown browser";
		this.version = this.searchVersion(navigator.userAgent)
			|| this.searchVersion(navigator.appVersion)
			|| "an unknown version";
		this.OS = this.searchString(this.dataOS) || "an unknown OS";
	},
	searchString: function (data) {
		for (var i=0;i<data.length;i++)	{
			var dataString = data[i].string;
			var dataProp = data[i].prop;
			this.versionSearchString = data[i].versionSearch || data[i].identity;
			if (dataString) {
				if (dataString.indexOf(data[i].subString) != -1)
					return data[i].identity;
			}
			else if (dataProp)
				return data[i].identity;
		}
	},
	searchVersion: function (dataString) {
		var index = dataString.indexOf(this.versionSearchString);
		if (index == -1) return;
		//return parseFloat(dataString.substring(index+this.versionSearchString.length+1));
		return dataString.substring(index+this.versionSearchString.length+1);
	},
	dataBrowser: [
		{
			string: navigator.userAgent,
			subString: "Chrome",
			identity: "Chrome"
		},
		{ 	string: navigator.userAgent,
			subString: "OmniWeb",
			versionSearch: "OmniWeb/",
			identity: "OmniWeb"
		},
		{
			string: navigator.vendor,
			subString: "Apple",
			identity: "Safari"
		},
		{
			prop: window.opera,
			identity: "Opera"
		},
		{
			string: navigator.vendor,
			subString: "iCab",
			identity: "iCab"
		},
		{
			string: navigator.vendor,
			subString: "KDE",
			identity: "Konqueror"
		},
		{
			string: navigator.userAgent,
			subString: "Firefox",
			identity: "Firefox"
		},
		{
			string: navigator.vendor,
			subString: "Camino",
			identity: "Camino"
		},
		{		// for newer Netscapes (6+)
			string: navigator.userAgent,
			subString: "Netscape",
			identity: "Netscape"
		},
		{
			string: navigator.userAgent,
			subString: "MSIE",
			identity: "Explorer",
			versionSearch: "MSIE"
		},
		{
			string: navigator.userAgent,
			subString: "Gecko",
			identity: "Mozilla",
			versionSearch: "rv"
		},
		{ 		// for older Netscapes (4-)
			string: navigator.userAgent,
			subString: "Mozilla",
			identity: "Netscape",
			versionSearch: "Mozilla"
		}
	],
	dataOS : [
		{
			string: navigator.platform,
			subString: "Win",
			identity: "Windows"
		},
		{
			string: navigator.platform,
			subString: "Mac",
			identity: "Mac"
		},
		{
			string: navigator.platform,
			subString: "Linux",
			identity: "Linux"
		}
	]

};
BrowserDetect.init();

//Detectamos el navegador y OS para los pixeles en el eje X de los avisos
var navegador = BrowserDetect.browser;
var SO = BrowserDetect.OS;
var versionNav = BrowserDetect.version;
var versionFloat = parseFloat(versionNav);


$(document).ready(function(){
   
   //var code = $.getUrlVar('code');
   
   $('a#more-results').click(function(){
        paginate($(this).attr('id'));
        return false;
   });
   
   $('ul.instagram-placeholder li').tipsy({gravity:'s', title: function() { return $(this).find('img').attr('alt'); } });
   
   showCaption();
   
   //Add 1 pixel margin top in Webkit based browsers
   if (navegador=='Chrome' || navegador=='Safari'){
        $('nav').css('margin-top', '104px');
   }

});


function showCaption(){
        //On mouse over those thumbnail
        $('.instagram-placeholder li').hover(function() {
             //Display the caption
             $(this).find('div.caption').stop(false,true).fadeIn(200);
        },
        function() {
             //Hide the caption
             $(this).find('div.caption').stop(false,true).fadeOut(200);
        });
        $('ul.instagram-placeholder li').tipsy({gravity:'s', title: function() { return $(this).find('img').attr('alt'); } });
        
        //$('#modal-window').dialog({
        //        autoOpen: false,
        //        width: 400,
        //        maxHeight: 100,
        //        //modal: true,
        //        resizable: false,
        //        buttons: {
        //                "Ok": function() { 
        //                        $(this).dialog("close"); 
        //                }
        //        }
        //});
        $('.likes_button').click(function(){
                //var thisId = $(this).attr('id');
                //$('#modal-window').dialog({ title: 'Likes' });
                //$('#modal-window').html($('#likes_'+thisId).html());
                //$('#modal-window').dialog('open', {title: 'Likes'});
                return false;
        });
        $('.comments_button').click(function(){
                //var thisId = $(this).attr('id');
                //$('#modal-window').dialog({ title: 'Comments' });
                //$('#modal-window').html($('#comments_'+thisId).html());
                //$('#modal-window').dialog('open');
                return false;
        });
        
        $('.likes_button').each(function(){
                var thisId = $(this).attr('id');
                var thisTitle = $(this).attr('title');
                $(this).qtip(
                {
                   content: {
                      // Set the text to an image HTML string with the correct src URL to the loading image you want to use
                      text: $('#likes_'+thisId).html(),
                      //ajax: {
                      //   url: $('#likes_'+$(this).attr('id')).html() // Use the rel attribute of each element for the url to load
                      //},
                      title: {
                         text: thisTitle, // Give the tooltip a title using each elements text
                         button: true
                      }
                   },
                   position: {
                      at: 'bottom center', // Position the tooltip above the link
                      my: 'top center',
                      viewport: $(window), // Keep the tooltip on-screen at all times
                      effect: false // Disable positioning animation
                   },
                   show: {
                      event: 'click',
                      solo: true // Only show one tooltip at a time
                   },
                   hide: 'unfocus',
                   style: {
                      classes: 'ui-tooltip-wiki ui-tooltip-light ui-tooltip-shadow'
                   }
                });
        });
}

function paginate(url){
        $('#loader').html('<img src="img/loader.gif" />');
        var nextUrl = $('#next-url').html();
        if (nextUrl!=undefined){
                $.ajax({
                    type: "GET",
                    dataType: "jsonp",
                    cache: false,
                    url: nextUrl,
                    success: function(data) {
                        $('#loader').html('');
                        for (var i = 0; i < data.data.length; i++) {
                                var captionText = '* Untitled *';
                                if (data.data[i].caption!=undefined){
                                        captionText = data.data[i].caption.text;
                                }
                                htmlDoc = "<ul class='instagram-placeholder'>";
                                htmlDoc += "<li><img class='instagram-image' src='"+data.data[i].images.thumbnail.url+"' alt='"+captionText+"' title='"+captionText+"' />";
                                htmlDoc += "<div class='caption'><p><a href='#' id='"+data.data[i].id+"' class='likes_button'><img src='image/thumb/img/heartShape.png/12/12/1/0' alt='likes' title='likes' /></a> "+data.data[i].likes.count+"</p>";
                                htmlDoc += "<p><a href='#' id='"+data.data[i].id+"' class='comments_button'><img src='image/thumb/img/speechBubble.png/12/12/1/0' alt='comments' title='comments' /></a> "+data.data[i].comments.count+"</p>";
                                htmlDoc += "<p>Filter: "+data.data[i].filter+"</p>";
                                htmlDoc += "</div></li></ul>";
                                htmlDoc += "<div id='likes_"+data.data[i].id+"' class='dialog-window'>";
                                for (var j = 0; j < data.data[i].likes.count; j++){
                                        htmlDoc += "<p><img src='image/thumb/"+data.data[i].likes.data[j].profile_picture+"/50/50/1/1' alt='"+data.data[i].likes.data[j].username+"' title='"+data.data[i].likes.data[j].username+"' /> "+data.data[i].likes.data[j].username+"</p>";
                                }
                                htmlDoc += "</div>";
                                htmlDoc += "<div id='comments_"+data.data[i].id+"' class='dialog-window'>";
                                for (var j = 0; j < data.data[i].comments.count; j++){
                                        htmlDoc += "<p><img src='image/thumb/"+data.data[i].comments.data[j].from.profile_picture+"/50/50/1/1' alt='"+data.data[i].comments.data[j].from.username+"' title='"+data.data[i].comments.data[j].from.username+"' /></p><p><strong>"+data.data[i].comments.data[j].from.username+" said:</strong> "+data.data[i].comments.data[j].text+"</p>";
                                        htmlDoc += "<p>&nbsp;</p>";
                                }
                                htmlDoc += "</div>";
                                $('#placeholder').append(htmlDoc);
                        }
                        if (data.pagination.next_url!=undefined){
                                $('#next-url').html(data.pagination.next_url);
                        } else {
                                $('#next-url').remove();
                                $('#more-prg').html('&nbsp;');
                        }
                        showCaption();
                    }
                });
        }
}

$.extend({
    getUrlVars: function(){
        var vars = [], hash;
        var hashes = window.location.hash.slice(window.location.hash.indexOf('#') + 1).split('&');
        for(var i = 0; i < hashes.length; i++)
        {
            hash = hashes[i].split('=');
            vars.push(hash[0]);
            vars[hash[0]] = hash[1];
        }
        return vars;
        },
    getUrlVar: function(name){
        return $.getUrlVars()[name];
    }
});
