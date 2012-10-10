<article>
	<?
	if ($media){
	?>
        <header id="profile">
                <h2 style="position:relative;float:left;margin-bottom:20px !important;width:600px !important;"><?= $user->username; ?>, activity in your network</h2>
		<div id="actions" style="margin-top:50px !important;">
			<p><form method="get" action="<?= $this->Html->url("/"); ?>search" id="search">
				<input name="data[Search][text]" id="search-text" type="text" size="40" placeholder="Search..." />
			</form></p>
		</div>
		<div id="placeholder">
			<?
			foreach ($media->data as $key=>$value){
				$captionText = '* Untitled *';
				if ($value->caption){
					$captionText = $value->caption->text;
				}
				$photo_id = array();
				$photo_id = explode("_", $value->id);
			?>
			<ul class="instagram-placeholder">
				<li><img class="instagram-image" src="<?= $value->images->thumbnail->url; ?>" alt="<?= $captionText; ?>" title="<?= $captionText; ?>">
				<div class="caption">
					<p><a href="#" id="likes_<?= $photo_id[0]; ?>" title="Likes" class="likes_button"><img src="<?= $this->Html->url("/image/thumb/"); ?>img/heartShape.png/12/12/1/0" alt="likes" title="likes" /></a> <?= $value->likes->count; ?></p>
					<p><a href="#" id="comments_<?= $photo_id[0]; ?>" title="Comments" class="comments_button"><img src="<?= $this->Html->url("/image/thumb/"); ?>img/speechBubble.png/12/12/1/0" alt="comments" title="comments" /></a> <?= $value->comments->count; ?></p>
					<p>Filter: <?= $value->filter; ?></p>
					<p>By: <a href="/users/profile/<?= $value->user->username; ?>"><?= $value->user->username; ?></a></p>
				</div>
				</li>
			</ul>
			<?
			}
			?>
		</div>
        </header>
	<p id="loader">&nbsp;</p>
        <p id="more-prg" style="float:right;"><a href="<?= $this->Html->url("/"); ?>" id="more-results" class="rounded-corners cufon">more photos</a></p>
	<p>&nbsp;</p>
	<?
	} else {
	?>
	<h2>Is not possible to connect Instagram.</h2>
	<?
	}
	?>
</article>
<div id="next-url" style="display:none;"><?= $media->pagination->next_url; ?></div>
<script type="application/x-javascript">
	$('form#search').submit(function(){
		var action = $(this).attr('action');
		var sendedData = $(this).serialize();
		$('#loader').html('<img src="<?= $this->Html->url("/"); ?>img/loader.gif" />');
		$('#placeholder').html('');
		$.ajax({
			type: "POST",
			cache: false,
			dataType: "json",
			data: sendedData,
			url: action,
			success: function(data){
				$('#loader').html('');
				for (var i = 0; i < data.data.length; i++) {
					var captionText = '* Untitled *';
					if (data.data[i].caption!=undefined){
						captionText = data.data[i].caption.text;
					}
					htmlDoc = "<ul class='instagram-placeholder'>";
					htmlDoc += "<li><img class='instagram-image' src='"+data.data[i].images.thumbnail.url+"' alt='"+captionText+"' title='"+captionText+"' />";
					htmlDoc += "<div class='caption'><p><a href='#' id='"+data.data[i].id+"' title='Likes' class='likes_button'><img src='<?= $this->Html->url("/"); ?>image/thumb/img/heartShape.png/12/12/1/0' alt='likes' title='likes' /></a> "+data.data[i].likes.count+"</p>";
					htmlDoc += "<p><a href='#' id='"+data.data[i].id+"' title='Comments' class='comments_button'><img src='<?= $this->Html->url("/"); ?>image/thumb/img/speechBubble.png/12/12/1/0' alt='comments' title='comments' /></a> "+data.data[i].comments.count+"</p>";
					htmlDoc += "<p>Filter: "+data.data[i].filter+"</p>";
					htmlDoc += "<p>By: <a href='/users/profile/"+data.data[i].user.username+"'>"+data.data[i].user.username+"</a></p>";
					htmlDoc += "</div></li></ul>";
					$('#placeholder').append(htmlDoc);
				}
				if (data.pagination.next_url!=undefined){
					$('#more-prg').css('display', 'block');
					$('#next-url').html(data.pagination.next_url);
				} else {
					$('#next-url').remove();
					$('#more-prg').html('&nbsp;');
				}
				showCaption();
			}
		});
		return false;
	});
</script>