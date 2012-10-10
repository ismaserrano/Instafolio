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

var fullUrl = document.location.href;
var urlAbsoluta = '/';
if (fullUrl.indexOf('localhost')!=-1){
   urlAbsoluta = '/instafolio/';
}


$(document).ready(function(){
   
   //var code = $.getUrlVar('code');
   
   $('a#more-results').click(function(){
        paginate($(this).attr('href'));
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
        
        $('.likes_button').click(function(){
                return false;
        });
        $('.comments_button').click(function(){
                return false;
        });
        
        //Action for profile type
        $('.profile').change(function(){
            tinyVal('', $(this).val(), 'public', $(this).attr('id'));
        });
        
        $('.likes_button, .comments_button').each(function(){
                var thisTitle = String($(this).attr('title'));
                var divId = thisTitle.toLowerCase();
                var thisId = $(this).attr('id').replace(divId+'_', '');
                var button = '';
                switch (divId){
                        case "likes":
                                button = '<a href="#" id="likes_'+thisId+'" class="likes" onclick="likes(1, '+thisId+'); return false;"><img src="'+urlAbsoluta+'image/thumb/img/heartShape.png/12/12/1/0" alt="Like this one" title="Like this one" /></a>';
                                break;
                        case "comments":
                                button = '<a href="#" id="comments_'+thisId+'" class="comments" onclick="comments(1, '+thisId+'); return false;"><img src="'+urlAbsoluta+'image/thumb/img/speechBubble.png/12/12/1/0" alt="Comment this one" title="Comment this one" /></a>';
                                break;
                }
                $(this).qtip(
                {
                    content: {
                        // Set the text to an image HTML string with the correct src URL to the loading image you want to use
                        text: '<img src="'+urlAbsoluta+'img/loader.gif" />',
                        ajax: {
                            url: urlAbsoluta+'users/'+divId+'/'+thisId
                        },
                        title: {
                            text: button, // Give the tooltip a title using each elements text
                            button: true
                        }
                    },
                    //overwrite: false,
                    position: {
                        at: 'bottom center', // Position the tooltip above the link
                        my: 'top center',
                        //viewport: $(window), // Keep the tooltip on-screen at all times
                        adjust: {
                                container: true,
                                screen: true,
                                //y: 0,
                                mouse: false,
                                scroll: true,
                                resize: true
                        }
                    //effect: false // Disable positioning animation
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
                $(this).click(function(){
                   event.preventDefault();     
                });
        });
}

function paginate(urlArg){
        $('#loader').html('<img src="'+urlAbsoluta+'img/loader.gif" />');
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
                                idParts = new Array();
                                idParts = String(data.data[i].id).split("_");
                                htmlDoc = "<ul class='instagram-placeholder'>";
                                htmlDoc += "<li><img class='instagram-image' src='"+data.data[i].images.thumbnail.url+"' alt='"+captionText+"' title='"+captionText+"' />";
                                htmlDoc += "<div class='caption'><p><a href='#' id='"+idParts[0]+"' title='Likes' class='likes_button'><img src='/image/thumb/img/heartShape.png/12/12/1/0' alt='likes' title='likes' /></a> "+data.data[i].likes.count+"</p>";
                                htmlDoc += "<p><a href='#' id='"+idParts[0]+"' title='Comments' class='comments_button'><img src='/image/thumb/img/speechBubble.png/12/12/1/0' alt='comments' title='comments' /></a> "+data.data[i].comments.count+"</p>";
                                htmlDoc += "<p class='active_"+idParts[0]+"'></p>";
                                htmlDoc += "<p>Filter: "+data.data[i].filter+"</p>";
                                htmlDoc += "<p>By: <a href='/users/profile/"+data.data[i].user.username+"'>"+data.data[i].user.username+"</a></p>";
                                htmlDoc += "</div></li></ul>";
                                $('#placeholder').append(htmlDoc);
                                //tinyVal(idParts[0]);
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


function photoActiveTiny(){
    $('.active_button').click(function(event){
        var $target = $(event.target).parent();
        var togg = $target.attr( 'id' );
        if( $(this).attr( 'id' ) == togg ){
            tinyVal('', $(this).attr('val'), 'active', $(this).parent().attr('class'));
        }
        return false;
    });
}


function tinyVal(opt, val, field, from){
    var added = '';
    
    if (from==undefined){
        from = '';
    }
    
    if (from.indexOf('container_')!=-1){
        added = '/profile';
        var photoId = from.replace('container_', '');
    }
    
    if (opt!=''){
        added = '/'+opt;
        from = 'active_'+opt;
    }
    if (from.indexOf('active_')!=-1){
        var photoId = from.replace('active_', '');
    }
    
    $.ajax({
        type: "POST",
        cache: false,
        data: 'data[val]='+val+'&data[field]='+field+'&data[id]='+photoId,
        url: urlAbsoluta+'tinyVal'+added,
        success: function(data){
            if (from.indexOf('active_')!=-1){
                $('.'+from).html(data);
                photoActiveTiny();
            }
        }
    });
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


function likes(action, media){
        var mediaId = media;
        switch (action){
                case 1:
                        accion = "post";
                        break;
                case 2:
                        accion = "remove";
                        break;
        }
        $.ajax({
                type: "POST",
                cache: false,
                data: 'data[mediaId]='+mediaId,
                url: urlAbsoluta+'users/likeMedia/'+accion,
                success: function(data){
                        if (data.meta.code=='200'){
                            $('#likes_'+mediaId).find('img').attr('src', '/image/thumb/img/heartShape-red.png/12/12/1/0');
                        } else {
                            alert('ERROR');
                        }
                }
        });
}
