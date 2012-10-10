<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html>
    <head>
        <title><?= $user->username; ?> photo portfolio</title>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
        <meta name="description" content="<?= $user->username; ?> photo portfolio" />
        <meta name="keywords" content="instafolio, instagram, photo, portfolio, <?= $user->username; ?>"/>
	<meta name="author" content="Instafolio">
        <meta property="og:title" content="<?= $user->username; ?> photo portfolio" />
	<meta property="og:type" content="website" />
	<meta property="og:url" content="<?= $this->Html->url("/", true).$user->username; ?>" />
	<meta property="og:description" content="<?= $user->username; ?> photo portfolio with selected photos" />
	<meta property="og:image" content="<?= $user->profile_picture; ?>" />
	<meta property="og:image:type" content="image/jpeg" />
	<meta property="og:image:width" content="150" />
	<meta property="og:image:height" content="150" />
        <link rel="stylesheet" href="<?= $this->Html->url("/"); ?>templates/slidingpanel/css/style.css" type="text/css" media="screen"/>
	<script type="text/javascript" src="<?= $this->Html->url("/"); ?>js/libs/jquery-1.6.2.min.js"></script>
    </head>
    <style>
        a{
            color:#fff;
            text-decoration:none;
        }
        a:hover{
            text-decoration:underline;
        }
        span.reference{
            position:fixed;
            left:30px;
            bottom:0px;
            font-size:9px;
        }
        span.reference a{
            color:#aaa;
	    text-transform: none !important;
        }
        span.reference a:hover{
            color:#ddd;
            text-decoration:none;
        }

    </style>
    <body>
        <div class="infobar">
            <span id="description"></span>
            <span id="loading">Loading Image</span>
            <span class="reference">
                <a href="<?= $this->Html->url("/"); ?>">Support by Instafolio</a>
            </span>
        </div>
        <div id="thumbsWrapper">
            <div id="content">
		<div id="center-content">
			<?
			foreach ($media->data as $key=>$value){
				$captionText = '* Untitled *';
				if ($value->caption){
					$captionText = $value->caption->text;
				}
				$photo_id = array();
				$photo_id = explode("_", $value->id);
			?>
			<img src="<?= $value->images->thumbnail->url; ?>" alt="<?= $value->images->standard_resolution->url; ?>" title="<?= $captionText; ?>" rel="<?= $value->link; ?>" />
			<?
			}
			?>
		</div>
                <div class="placeholder"></div>
            </div>
        </div>
	<div id="next-url" style="display:none;"><?= $media->pagination->next_url; ?></div>
        <div id="panel">
            <div id="wrapper">
                <a id="prev"></a>
                <a id="next"></a>
            </div>
        </div>
        <!-- The JavaScript -->
        <script type="text/javascript">
            $(function() {
                /* this is the index of the last clicked picture */
                var current = -1;
                /* number of pictures */
                var totalpictures = $('#center-content img').size();
                /* speed to animate the panel and the thumbs wrapper */
                var speed 	= 500;
		
		
		function init(){
			/* show the content */
			$('#content').show();
					
			/*
			when the user resizes the browser window,
			the size of the picture being viewed is recalculated;
			 */
			$(window).bind('resize', function() {
			    var $picture = $('#wrapper').find('img');
			    resize($picture);
			});
		       
			/*
			when hovering a thumb, animate it's opacity
			for a cool effect;
			when clicking on it, we load the corresponding large image;
			the source of the large image is stored as 
			the "alt" attribute of the thumb image
			 */
			$('#center-content > img').hover(function () {
			    var $this   = $(this);
			    $this.stop().animate({'opacity':'1.0'},200);
			},function () {
			    var $this   = $(this);
			    $this.stop().animate({'opacity':'0.4'},200);
			}).bind('click',function(){
			    var $this   = $(this);
			    
			    /* shows the loading icon */
			    $('#loading').show();
			    $('#description').hide();
			    
			    $('<img/>').load(function(){
				$('#loading').hide();
				
				if($('#wrapper').find('img').length) return;
				current 	= $this.index();
				var $theImage   = $(this);
				/*
				After it's loaded we hide the loading icon
				and resize the image, given the window size;
				then we append the image to the wrapper
				*/
				
				//Location hash change
				var hashArray = new Array();
				hashArray = String($this.attr('rel')).split("/");
				var finalHash = hashArray[hashArray.length-2];
				location.hash = finalHash;
				
				resize($theImage);
	
				$('#wrapper').append($theImage);
				/* make its opacity animate */
				$theImage.fadeIn(800);
				
				/* and finally slide up the panel */
				$('#panel').animate({'height':'100%'},speed,function(){
				    /*
				    if the picture has a description,
				    it's stored in the title attribute of the thumb;
				    show it if it's not empty
				     */
				    var title = $this.attr('title')+" - <a href='"+$this.attr('rel')+"' target='_blank'>Open in Instagram</a>";
				    if(title != '') 
					$('#description').html(title).show();
				    else 
					$('#description').empty().hide();
				    
				    /*
				    if our picture is the first one,
				    don't show the "previous button"
				    for the slideshow navigation;
				    if our picture is the last one,
				    don't show the "next button"
				    for the slideshow navigation
				     */
				    if(current==0)
					$('#prev').hide();
				    else
					$('#prev').fadeIn();
				    if(current==parseInt(totalpictures-1))
					$('#next').hide();
				    else
					$('#next').fadeIn();
				    /*
				    we set the z-index and height of the thumbs wrapper 
				    to 0, because we want to slide it up afterwards,
				    when the user clicks the large image
				     */
				    $('#thumbsWrapper').css({'z-index':'0','height':'0px'});
				});
			    }).attr('src', $this.attr('alt'));
			});
	
			/*
			when hovering a large image,
			we want to slide up the thumbs wrapper again,
			and reset the panel (like it was initially);
			this includes removing the large image element
			 */
			$('#wrapper > img').live('click',function(){
			    $this = $(this);
			    $('#description').empty().hide();
			    
			    $('#thumbsWrapper').css('z-index','10')
			    .stop()
			    .animate({'height':'100%'},speed,function(){
				var $theWrapper = $(this);
				$('#panel').css('height','0px');
				$theWrapper.css('z-index','0');
				
				//Refresh hash to empty
				location.hash = '';
				
				/* 
				remove the large image element
				and the navigation buttons
				 */
				$this.remove();
				$('#prev').hide();
				$('#next').hide();
			    });
			});
		}

                /*
                when we are viewing a large image,
                if we navigate to the right/left we need to know
                which image is the corresponding neighbour.
                we know the index of the current picture (current),
                so we can easily get to the neighbour:
                 */
                $('#next').bind('click',function(){
                    var $this           = $(this);
                    var $nextimage 		= $('#center-content img:nth-child('+parseInt(current+2)+')');
                    navigate($nextimage,'right');
                });
                $('#prev').bind('click',function(){
                    var $this           = $(this);
                    var $previmage 		= $('#center-content img:nth-child('+parseInt(current)+')');
                    navigate($previmage,'left');
                });

                /*
                given the next or previous image to show,
                and the direction, it loads a new image in the panel.
                 */
                function navigate($nextimage,dir){
                    /*
                    if we are at the end/beginning
                    then there's no next/previous
                     */
                    if(dir=='left' && current==0)
                        return;
                    if(dir=='right' && current==parseInt(totalpictures-1))
                        return;
                    $('#loading').show();
		    $('#description').hide();
                    $('<img/>').load(function(){
                        var $theImage = $(this);
                        $('#loading').hide();
                        $('#description').empty().fadeOut();
                         
                        $('#wrapper img').stop().fadeOut(500,function(){
                            var $this = $(this);
							
                            $this.remove();
                            resize($theImage);
			    
			    //Location hash change
			    var hashArray = new Array();
			    hashArray = String($nextimage.attr('rel')).split("/");
			    var finalHash = hashArray[hashArray.length-2];
			    location.hash = finalHash;
                            
                            $('#wrapper').append($theImage.show());
                            $theImage.stop().fadeIn(800);

                            var title = $nextimage.attr('title')+" - <a href='"+$nextimage.attr('rel')+"' target='_blank'>Open in Instagram</a>";
                            if(title != ''){
                                $('#description').html(title).show();
                            }
                            else
                                $('#description').empty().hide();

                            if(current==0)
                                $('#prev').hide();
                            else
                                $('#prev').show();
                            if(current==parseInt(totalpictures-1))
                                $('#next').hide();
                            else
                                $('#next').show();
                        });
                        /*
                        increase or decrease the current variable
                         */
                        if(dir=='right')
                            ++current;
                        else if(dir=='left')
                            --current;
                    }).attr('src', $nextimage.attr('alt'));
                }

                /*
                resizes an image given the window size,
                considering the margin values
                 */
                function resize($image){
                    var windowH      = $(window).height()-100;
                    var windowW      = $(window).width()-80;
                    var theImage     = new Image();
                    theImage.src     = $image.attr("src");
                    var imgwidth     = theImage.width;
                    var imgheight    = theImage.height;

                    if((imgwidth > windowW)||(imgheight > windowH)){
                        if(imgwidth > imgheight){
                            var newwidth = windowW;
                            var ratio = imgwidth / windowW;
                            var newheight = imgheight / ratio;
                            theImage.height = newheight;
                            theImage.width= newwidth;
                            if(newheight>windowH){
                                var newnewheight = windowH;
                                var newratio = newheight/windowH;
                                var newnewwidth =newwidth/newratio;
                                theImage.width = newnewwidth;
                                theImage.height= newnewheight;
                            }
                        }
                        else{
                            var newheight = windowH;
                            var ratio = imgheight / windowH;
                            var newwidth = imgwidth / ratio;
                            theImage.height = newheight;
                            theImage.width= newwidth;
                            if(newwidth>windowW){
                                var newnewwidth = windowW;
                                var newratio = newwidth/windowW;
                                var newnewheight =newheight/newratio;
                                theImage.height = newnewheight;
                                theImage.width= newnewwidth;
                            }
                        }
                    }
                    $image.css({'width':theImage.width+'px','height':theImage.height+'px'});
                }
		
		//Check if have hash to load direct image
		//TO DO: change AJAX calling if image is not load
		var hashUrl = location.hash;
		hashTreat = hashUrl.split('#');
		if (hashTreat[1]!=''){
		    $('#center-content').find('img').each(function(){
			if (String($(this).attr('rel')).indexOf(hashTreat[1])!=-1){
			    var imageLink = $(this).attr('alt');
			    var imageTitle = $(this).attr('title');
			    //Change FB sharing image and title for the specific photo
			    $('meta').each(function(){
				if($(this).attr('property')=='og:image'){
				    $(this).attr('content', imageLink);
				}
				if($(this).attr('property')=='og:title'){
				    $(this).attr('content', imageTitle);
				}
				if($(this).attr('property')=='og:description'){
				    $(this).attr('content', imageTitle);
				}
				if($(this).attr('property')=='og:url'){
				    $(this).attr('content', '<?= $this->Html->url("/", true).$user->username; ?>#'+hashTreat[1]);
				}
			    });
			    $(this).click();
			}
		    });
		}
		
		init();
		
		function loadImages(urlArg) {
			$('#loading').show();
			$('#loading').html('Loading more images');
			$.ajax({
				type: "GET",
				dataType: "jsonp",
				cache: false,
				url: urlArg,
				success: function(data) {
					if (data.pagination.next_url!=undefined){
						$('#next-url').html(data.pagination.next_url);
					} else {
						$('#next-url').remove();
						$('#more-prg').html('');
					}
					for (var i = 0; i < data.data.length; i++) {
						var captionText = '* Untitled *';
						if (data.data[i].caption!=undefined){
							captionText = data.data[i].caption.text;
						}
						$("#center-content").append('<img src="'+data.data[i].images.thumbnail.url+'" alt="'+data.data[i].images.standard_resolution.url+'" title="'+captionText+'" rel="'+data.data[i].link+'" />');
					}
					$('#loading').html('Loading Image');
					$('#loading').hide();
					init();
				}
			});
		};
		
		// Append a scroll event handler to the container
		$("#content").scroll(function() {
			// We check if we're at the bottom of the scrollcontainer
			if ($(this)[0].scrollHeight - $(this).scrollTop() == $(this).outerHeight()) {
				var nextUrl = $('#next-url').html();
				if (nextUrl){
					loadImages(nextUrl);
				}
			}
		});
		
		loadImages($('#next-url').html());
		
            });	    
        </script>
	<script type="text/javascript">
		var _gaq=[['_setAccount','UA-7794422-10'],['_trackPageview']];
		(function(d,t){var g=d.createElement(t),s=d.getElementsByTagName(t)[0];g.async=1;
		g.src=('https:'==location.protocol?'//ssl':'//www')+'.google-analytics.com/ga.js';
		s.parentNode.insertBefore(g,s)}(document,'script'));
	</script>
    </body>
</html>