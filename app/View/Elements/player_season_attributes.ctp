<? if($player_season): ?>
	<div class="panel-heading">
		<div class="row">
			<div class="col-sm-6">
				<a href="/seasons/view/<?= $player_season['Season']['id']; ?>"><strong><?= $player_season['Season']['season_name']; ?></strong></a>
			</div>
			<div class="col-sm-6 text-right">
				<a class="edit-season login-link" player_season_id="<?= $player_season['PlayersSeason']['id']; ?>" href="#">Edit</a>
			</div>
		</div>
	</div>
	<div class="panel-body">
		<div class="attribute-header">
			Season Player Attributes
		</div>
		<div class="row">
			<div class="col-sm-3">
				Age: <span class="player-attribute"><strong><?= $player_season['PlayersSeason']['age_show']; ?></strong></span>
			</div>
			<div class="col-sm-3">
				Placement: <span class="player-attribute"><strong>
					<?= $player_season['PlayersSeason']['placement'] ? Util::addOrdinalNumberSuffix($player_season['PlayersSeason']['placement']) : "N/A"; ?>
				</strong></span>
			</div>
			<div class="col-sm-3">
				Days Lasted: <span class="player-attribute"><strong><?= $player_season['PlayersSeason']['day_voted_out']; ?></strong></span>
			</div>
			<div class="col-sm-3">
				Votes Against: <span class="player-attribute"><strong><?= $player_season['PlayersSeason']['votes_against']; ?></strong></span>
			</div>
		</div>
		<hr />

		<div class="row">
			<div class="col-sm-3">
				Tribe Wins: <span class="player-attribute"><strong><?= $player_season['PlayersSeason']['tribe_wins']; ?></strong></span>
			</div>
			<div class="col-sm-3">
				Individual Wins: <span class="player-attribute"><strong><?= $player_season['PlayersSeason']['individual_wins']; ?></strong></span>
			</div>

			<div class="col-sm-3">
				Evac: <span class="player-attribute"><strong><?= $player_season['PlayersSeason']['med_evac'] == 0 ? "No" : "Yes"; ?></strong></span>
			</div>
			<div class="col-sm-3">
				Quit: <span class="player-attribute"><strong><?= $player_season['PlayersSeason']['quit'] == 0 ? "No" : "Yes"; ?></strong></span>
			</div>
		</div>
		<hr />

		<div class="attribute-header vspan3">
			Character Type
		</div>
		<div class="row">
			<div class="col-sm-5 player-attribute">
				<p><strong><?= $player_season['CharacterType']['character_type'] ? $player_season['CharacterType']['character_type'] : "N/A"; ?></strong></p>
			</div>
		</div>

		<div class="attribute-header vspan3">
			Starting Tribe
		</div>
		<div class="row">
			<div class="col-sm-5 player-attribute">
				<p><strong><?= $player_season['PlayersSeason']['starting_tribe'] ? $player_season['PlayersSeason']['starting_tribe'] : "N/A"; ?></strong></p>
			</div>
		</div>

		<div class="attribute-header vspan3">
			Swapped Tribe
		</div>
		<div class="row">
			<div class="col-sm-5 player-attribute">
				<p><strong><?= $player_season['PlayersSeason']['swapped_tribe'] ? $player_season['PlayersSeason']['swapped_tribe'] : "N/A"; ?></strong></p>
			</div>
		</div>

		<div class="attribute-header vspan3">
			Boot Cirumstances
		</div>
		<div class="row">
			<div class="edit-comments-container">
				<? if($player_season['PlayersSeason']['boot_circumstances']): ?>
					<div class="col-sm-10">
						<?= $player_season['PlayersSeason']['boot_circumstances']; ?>
					</div>
					
					<div class="col-sm-2 text-right">
						<a class="add-comment login-link" href="#">Edit</a>
					</div>
				<? else: ?>
					<div class="col-sm-10">
						<a class="add-comment login-link" href="#">+ Add information about the player's boot cirumstances</a>
					</div>
				<? endif; ?>
			</div>

			<div class="additional-comments-form" style="display: none;">
				<div class="col-sm-12">
					<textarea class="additional-comments" name="data[boot_circumstances]" players_season_id="<?= $player_season['PlayersSeason']['id']; ?>" placeholder="Write the boot circumstances for this player" style="width: 100%; height: 100px;"><?= $player_season['PlayersSeason']['boot_circumstances']; ?></textarea>
				</div>
				<div class="col-sm-12 text-right">
					<a href="#" class="btn btn-default cancel-btn">Cancel</a> <a href="#" class="btn btn-primary save-btn">Save</a>
				</div>
			</div>
		</div>	

		<div class="attribute-header vspan3">
			Additional Comments
		</div>
		<div class="row">
			<div class="edit-comments-container">
				<? if($player_season['PlayersSeason']['additional_comments']): ?>
					<div class="col-sm-10">
						<?= $player_season['PlayersSeason']['additional_comments']; ?>
					</div>
					
					<div class="col-sm-2 text-right">
						<a class="add-comment login-link" href="#">Edit</a>
					</div>
				<? else: ?>
					<div class="col-sm-10">
						<a class="add-comment login-link" href="#">+ Add information comments about this player</a>
					</div>
				<? endif; ?>
			</div>

			<div class="additional-comments-form" style="display: none;">
				<div class="col-sm-12">
					<textarea class="additional-comments" name="data[additional_comments]" players_season_id="<?= $player_season['PlayersSeason']['id']; ?>" placeholder="Write additional comments about this player" style="width: 100%; height: 100px;"><?= $player_season['PlayersSeason']['additional_comments']; ?></textarea>
				</div>
				<div class="col-sm-12 text-right">
					<a href="#" class="btn btn-default cancel-btn">Cancel</a> <a href="#" class="btn btn-primary save-btn">Save</a>
				</div>
			</div>
		</div>	
	</div>	
<? endif; ?>
