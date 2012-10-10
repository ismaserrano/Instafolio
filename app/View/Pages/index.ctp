<article>
        <header>
		<h2 style="position:relative;float:left;margin-bottom:20px !important;">Popular media</h2>
		<div id="actions" style="margin-top:50px !important;">
			<p><form method="get" action="<?= $this->Html->url("/"); ?>search" id="search">
				<input name="data[Search][text]" id="search-text" type="text" size="40" placeholder="Search..." />
			</form></p>
		</div>
		<div id="placeholder">&nbsp;</div>
	</header>
	<h3>Coming soon with social, metrics...</h3>
	<p>Now you can connect Instagram with Instafolio and show your photos with cool designs.</p>
</article>
<script type="text/javascript">
	//Get recent popular results
	getResults("https://api.instagram.com/v1/media/popular?client_id=c0eb2bfc20474995bfcb3efa9a40e263");
	
	$('form#search').submit(function(){
		var sendedData = $('#search-text').val().replace('#', '');
		getResults("https://api.instagram.com/v1/tags/"+sendedData+"/media/recent?client_id=c0eb2bfc20474995bfcb3efa9a40e263");
		return false;
	});
	function getResults(urlArg){
		$('#placeholder').html('<img src="<?= $this->Html->url("/"); ?>img/loader.gif" />');
		$.ajax({
			type: "GET",
			dataType: "jsonp",
			cache: false,
			url: urlArg,
			success: function(data) {
				if (data.data.length>0){
					$('#placeholder').html('');
					for (var i = 0; i < data.data.length; i++) {
					    $('#placeholder').append("<ul class='instagram-placeholder'><li><a target='_blank' href='" + data.data[i].link +"'><img class='instagram-image' src='" + data.data[i].images.thumbnail.url +"' alt='" + data.data[i].user.username +"' title='" + data.data[i].user.username +"' /></a></li></ul>");
					}
				} else {
					$('#placeholder').html('<p>No results found</p>');
				}
			}
		});
	}
</script>
<?
//Realizamos logout con Instagram
if ($logout){
?>
<iframe src="https://instagram.com/accounts/logout/" width="0" height="0" frameborder="no" scrolling="no" style="display:none !important;"></iframe>
<?
}
?>