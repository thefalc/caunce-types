<div class="panel-heading">
	<div class="row">
		<div class="col-sm-6">
			<strong><?= $season['Season']['season_name']; ?> (Season Number <?= $season['Season']['season_number'] ?>)</strong>
		</div>
		<div class="col-sm-6 text-right">
			<a id="edit-season" class="login-link" season_id="<?= $season['Season']['id']; ?>" href="#">Edit</a>
		</div>
	</div>
</div>

<div class="panel-body">
	<div class="row">
		<div class="col-sm-12">
			<div class="attribute-header">
				Season Attributes
			</div>
			<div class="row vspan1">
				<div class="col-sm-4">
					Premiere Date: <span class="player-attribute"><strong><?= strtotime($season['Season']['premiere_date']) > 0 ? date("F d, Y", strtotime($season['Season']['premiere_date'])) : "N/A"; ?></strong></span>
				</div>
				<div class="col-sm-4">
					Finale: <span class="player-attribute"><strong><?= strtotime($season['Season']['finale_date']) > 0 ? date("F d, Y", strtotime($season['Season']['finale_date'])) : "N/A"; ?></strong></span>
				</div>
			</div>
			<div class="row vspan1">
				<div class="col-sm-4">
					Starting Players: <span class="player-attribute"><strong><?= $season['Season']['starting_players'] ? $season['Season']['starting_players'] : "N/A"; ?></strong></span>
				</div>
				<div class="col-sm-4">
					Starting Tribes: <span class="player-attribute"><strong><?= $season['Season']['starting_tribes'] ? $season['Season']['starting_tribes'] : "N/A"; ?></strong></span>
				</div>
			</div>
			<div class="attribute-header vspan3">
				Swap Tribes
			</div>
			<div class="row vspan1">
				<div class="col-sm-4">
					Swap 1: <span class="player-attribute"><strong><?= $season['Season']['swap_day1'] ? $season['Season']['swap_day1'] : "N/A"; ?></strong></span>
				</div>
				<div class="col-sm-4">
					Swap 2: <span class="player-attribute"><strong><?= $season['Season']['swap_day2'] ? $season['Season']['swap_day2'] : "N/A"; ?></strong></span>
				</div>
			</div>
			<div class="attribute-header vspan3">
				Merge Details
			</div>
			<div class="row vspan1">
				<div class="col-sm-4">
					Merge Tribe: <span class="player-attribute"><strong><?= $season['Season']['merge_tribe'] ? $season['Season']['merge_tribe'] : "N/A"; ?></strong></span>
				</div>
				<div class="col-sm-4">
					Merge Players: <span class="player-attribute"><strong><?= $season['Season']['merge_players'] ? $season['Season']['merge_players'] : "N/A"; ?></strong></span>
				</div>
			</div>
			<div class="attribute-header vspan3">
				Jury & Final Tribal Members
			</div>
			<div class="row vspan1">
				<div class="col-sm-4">
					Final Tribal: <span class="player-attribute"><strong><?= $season['Season']['ftc_count'] ? $season['Season']['ftc_count'] : "N/A"; ?></strong></span>
				</div>
				<div class="col-sm-4">
					Jury Members: <span class="player-attribute"><strong><?= $season['Season']['jury_count'] ? $season['Season']['jury_count'] : "N/A"; ?></strong></span>
				</div>
			</div>
			<div class="attribute-header vspan3">
				Tribe Names
			</div>
			<div class="row vspan1">
				<div class="col-sm-3">
					Tribe 1: <span class="player-attribute"><strong><?= $season['Season']['tribe1'] ? $season['Season']['tribe1'] : "N/A"; ?></strong></span>
				</div>
				<div class="col-sm-3">
					Tribe 2: <span class="player-attribute"><strong><?= $season['Season']['tribe2'] ? $season['Season']['tribe2'] : "N/A"; ?></strong></span>
				</div>
				<div class="col-sm-3">
					Tribe 3: <span class="player-attribute"><strong><?= $season['Season']['tribe3'] ? $season['Season']['tribe3'] : "N/A"; ?></strong></span>
				</div>
				<div class="col-sm-3">
					Tribe 4: <span class="player-attribute"><strong><?= $season['Season']['tribe4'] ? $season['Season']['tribe4'] : "N/A"; ?></strong></span>
				</div>
			</div>

			<div class="row vspan3">
				<div class="col-sm-3">
					Wikia:
				</div>
				<div class="col-sm-9 player-attribute">
					<? if($season['Season']['wikia_url']): ?>
						<p><a target="_blank" href="<?= $season['Season']['wikia_url']; ?>"><strong><?= $season['Season']['wikia_url']; ?></strong></a></p>
					<? else: ?>
						N/A
					<? endif; ?>
				</div>
			</div>
		</div>
	</div>
</div>