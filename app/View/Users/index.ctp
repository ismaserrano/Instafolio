<article>
	<?
	if ($media && $user && $media->data){
	?>
        <header id="profile">
                <h2>Hello <?= $user->username; ?>!</h2>
                <p><img src="<?= $this->Html->url("/image/thumb/").$user->profile_picture; ?>/50/50/1/1" alt="<?= $user->username; ?>" title="<?= $user->username; ?>"/></p>
		<div id="info">
			<p id="full-name"><?= $user->full_name; ?></p>
			<p>Photos: <?= $user->counts->media; ?></p>
			<p>Follow by: <?= $user->counts->followed_by; ?></p>
			<p>Follows: <?= $user->counts->follows; ?></p>
		</div>
		<div id="actions">
			<p><form method="post" action="<?= $this->Html->url("/"); ?>search" id="search">
				<select id="container_<?= $user->id; ?>" name="data[User][public]" style="margin-left:10px;" class="profile">
					<option value="0"<? if($userDDBB['User']['public']==0){ ?> selected="selected"<? } ?>>Private profile</option>
					<option value="1"<? if($userDDBB['User']['public']==1){ ?> selected="selected"<? } ?>>Public profile</option>
				</select>
				<select id="template" name="data[User][template]" style="margin-left:10px;">
					<option value="1" selected="selected">Basic template</option>
					<!--<option value="2">Premium template</option>-->
					<!--<option value="3">Advanced template</option>-->
				</select>
			</form></p>
			<p>Your public URL profile is: <a href="<?= $this->Html->url("/", true); ?><?= $user->username; ?>" style="font-weight:bold;"><?= $this->Html->url("/", true); ?><?= $user->username; ?></a></p>
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
				<li><img class="instagram-image" src="<?= $value->images->thumbnail->url; ?>" alt="<?= $captionText; ?>" title="<?= $captionText; ?>" />
				<div class="caption">
					<p><a href="#" id="likes_<?= $photo_id[0]; ?>" title="Likes" class="likes_button"><img src="<?= $this->Html->url("/image/thumb/"); ?>img/heartShape.png/12/12/1/0" alt="likes" title="likes" /></a> <?= $value->likes->count; ?></p>
					<p><a href="#" id="comments_<?= $photo_id[0]; ?>" title="Comments" class="comments_button"><img src="<?= $this->Html->url("/image/thumb/"); ?>img/speechBubble.png/12/12/1/0" alt="comments" title="comments" /></a> <?= $value->comments->count; ?></p>
					<p class="active_<?= $photo_id[0]; ?>"></p>
					<p>Filter: <?= $value->filter; ?></p>
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
	<p style="height:200px;">&nbsp;</p>
	<?
	}
	?>
</article>
<div id="next-url" style="display:none;"><?= $media->pagination->next_url; ?></div>