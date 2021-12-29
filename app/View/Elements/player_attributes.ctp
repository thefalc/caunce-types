<div class="panel-heading">
	<div class="row">
		<div class="col-sm-6">
			<strong><?= $player['Player']['fname']; ?> <?= $player['Player']['lname']; ?></strong>
		</div>
		<div class="col-sm-6 text-right">
			<a id="edit-player" class="login-link" player_id="<?= $player['Player']['id']; ?>" href="#">Edit</a>
		</div>
	</div>
</div>
<div class="panel-body">
	<div class="row">
		<div class="col-sm-4">
			<img class="img-responsive" src="<?= $player['Player']['image_url']; ?>" />
		</div>
		<div class="col-sm-8">
			<div class="row">
				<div class="col-sm-3">
					Nickname:
				</div>
				<div class="col-sm-9 player-attribute">
					<strong><?= $player['Player']['nickname'] ? $player['Player']['nickname'] : "N/A"; ?></strong>
				</div>
			</div>
			<div class="row vspan1">
				<div class="col-sm-3">
					Location:
				</div>
				<div class="col-sm-9 player-attribute">
					<strong><?= $player['Player']['location']; ?></strong>
				</div>
			</div>
			<div class="row vspan1">
				<div class="col-sm-3">
					Occupation:
				</div>
				<div class="col-sm-9 player-attribute">
					<strong><?= $player['Player']['occupation']; ?></strong>
				</div>
			</div>
			<div class="row vspan1">
				<div class="col-sm-3">
					Sex:
				</div>
				<div class="col-sm-9 player-attribute">
					<? if($player['Player']['sex']): ?>
						<strong><?= $player['Player']['sex'] == "M" ? "Male" : "Female"; ?></strong>
					<? else: ?>
						<strong>N/A</strong>
					<? endif; ?>
				</div>
			</div>
			<div class="row vspan1">
				<div class="col-sm-3">
					Twitter:
				</div>
				<div class="col-sm-9 player-attribute">
					<? if($player['Player']['twitter']): ?>
						<a target="_blank" href="<?= $player['Player']['twitter']; ?>"><strong><?= $player['Player']['twitter']; ?></strong></a>
					<? else: ?>
						<strong>N/A</strong>
					<? endif; ?>
				</div>
			</div>
			<div class="row vspan1">
				<div class="col-sm-3">
					Wikia:
				</div>
				<div class="col-sm-9 player-attribute">
					<? if($player['Player']['wikia_url']): ?>
						<p><a target="_blank" href="<?= $player['Player']['wikia_url']; ?>"><strong><?= $player['Player']['wikia_url']; ?></strong></a></p>
					<? else: ?>
						N/A
					<? endif; ?>
				</div>
			</div>
		</div>
	</div>
</div>