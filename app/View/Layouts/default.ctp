<!doctype html>
<!--[if lt IE 7]> <html class="no-js ie6 oldie" lang="en"> <![endif]-->
<!--[if IE 7]>    <html class="no-js ie7 oldie" lang="en"> <![endif]-->
<!--[if IE 8]>    <html class="no-js ie8 oldie" lang="en"> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js" lang="en"> <!--<![endif]-->
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">

	<title>Instafolio</title>
	<meta name="description" content="Instafolio allow Instagram users customize his photo portfolio.">
	<meta name="author" content="Instafolio">
        <meta property="og:title" content="Instafolio" />
	<meta property="og:type" content="website" />
	<meta property="og:url" content="<?= $this->Html->url("/", true); ?>" />
	<meta property="og:description" content="Instafolio allow Instagram users customize his photo portfolio." />
	<meta property="og:image" content="<?= $this->Html->url("/", true); ?>img/logo_share.jpg" />
	<meta property="og:image:type" content="image/jpeg" />
	<meta property="og:image:width" content="400" />
	<meta property="og:image:height" content="350" />
	<meta name="viewport" content="width=device-width,initial-scale=0">

	<link rel="stylesheet" href="<?= $this->Html->url("/"); ?>css/style.css">
	<link rel="stylesheet" type="text/css" href="<?= $this->Html->url("/"); ?>js/fancybox/jquery.fancybox-1.3.4.css" media="screen" />
	<link rel="stylesheet" type="text/css" href="<?= $this->Html->url("/"); ?>css/tipsy.css" media="screen" />
	<link rel="stylesheet" type="text/css" href="<?= $this->Html->url("/"); ?>css/smoothness/jquery-ui-1.8.16.custom.css" media="screen" />
	<link rel="stylesheet" type="text/css" href="<?= $this->Html->url("/"); ?>css/jquery.qtip.min.css" media="screen" />

	<script type="text/javascript" src="<?= $this->Html->url("/"); ?>js/libs/modernizr-2.0.min.js"></script>
	<script type="text/javascript" src="<?= $this->Html->url("/"); ?>js/libs/respond.min.js"></script>
        
        <script type="text/javascript" src="<?= $this->Html->url("/"); ?>js/libs/jquery-1.6.2.min.js"></script>
	<script type="text/javascript" src="<?= $this->Html->url("/"); ?>js/libs/jquery-ui-1.8.16.custom.min.js"></script>
	<script type="text/javascript" src="<?= $this->Html->url("/"); ?>js/jquery.qtip.min.js"></script>
	<script type="text/javascript" src="<?= $this->Html->url("/"); ?>js/fancybox/jquery.fancybox-1.3.4.pack.js"></script>
	<script type="text/javascript" src="<?= $this->Html->url("/"); ?>js/jquery.tipsy.js"></script>
        <script type="text/javascript" src="<?= $this->Html->url("/"); ?>js/cufon-yui.js" type="text/javascript"></script>
        <script type="text/javascript" src="<?= $this->Html->url("/"); ?>js/CA_BND_Web_Bold_700.font.js" type="text/javascript"></script>
        <script type="text/javascript">
                Cufon.replace('h1,h2,h3,nav,.cufon');
        </script>
	<script type="text/javascript" src="<?= $this->Html->url("/"); ?>js/script.js"></script>
</head>
<body>
	<?= $this->Element('header'); ?>
	<div id="main" class="wrapper">
		<?= $content_for_layout; ?>
	</div>
	<?= $this->Element('footer'); ?>
<script type="text/javascript">
	var _gaq=[['_setAccount','UA-7794422-10'],['_trackPageview']];
	(function(d,t){var g=d.createElement(t),s=d.getElementsByTagName(t)[0];g.async=1;
	g.src=('https:'==location.protocol?'//ssl':'//www')+'.google-analytics.com/ga.js';
	s.parentNode.insertBefore(g,s)}(document,'script'));
</script>

<!--[if lt IE 7 ]>
	<script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/chrome-frame/1.0.2/CFInstall.min.js"></script>
	<script>window.attachEvent("onload",function(){CFInstall.check({mode:"overlay"})})</script>
<![endif]-->
</body>
</html>
