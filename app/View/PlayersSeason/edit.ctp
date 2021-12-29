<?
	$id = Util::getIfSet($this->request->data['PlayersSeason']['id'], true);
?>

<script type="text/javascript">
    $(document).ready(function() {
    	$('[data-toggle="tooltip"]').tooltip();

        $("#save-btn").click(function(e) {
            e.preventDefault();

            $("#editSeasonForm").submit();
        });

        $('#editSeasonForm').ajaxForm({
            beforeSubmit: function() {
                $("#save-btn").button("loading");
            },
            success: showResponse
        });

        $("#delete-btn").click(function(e) {
        	e.preventDefault();

        	deleteSeason($(this).attr("players_season_id"));
        });
    });

    function deleteSeason(id) {
    	bootbox.confirm({
	        message: 'Are you sure you want to delete this season from this player\'s history?',
	        buttons: {
	            'cancel': {
	                label: 'Cancel',
	                className: 'btn-default'
	            },
	            'confirm': {
	                label: 'Delete Season',
	                className: 'btn-danger'
	            }
	        },
	        callback: function(result) {
	            if(result) {
	                $("#delete-btn").button("loading");
	                
	                $.getJSON('/players_season/deleteSeason/' + id, function(data) {
	                    if(data.result == "erro") {
	                        $("#delete-btn").button("reset");
	                        
	                        bootbox.alert(data.message);
	                    }
	                    else {
	                       $('body').prepend('<div id="flash"></div>');
				            $('#flash').html("Player Season Removed!");
				            $('#flash').slideDown('slow');
				            $('#flash').click(function () { $('#flash').hide(); });

				            $("#"+id).remove();

				            setTimeout(function() { $('#flash').slideUp(); window.location.reload(); }, 1000);
	                    }
	                });
	            }
	        }
	    });
    }

    function showResponse(responseObject) {
        if(typeof responseObject != 'object') {
            responseObject = $.parseJSON(responseObject);    
        }

        if (responseObject.result == "error") {
            $("#save-btn").button("reset");

            bootbox.alert(responseObject.message);
        }
        else {
        	$('body').prepend('<div id="flash"></div>');
            $('#flash').html("Player Season Saved!");
            $('#flash').slideDown('slow');
            $('#flash').click(function () { $('#flash').hide(); });

            setTimeout(function() { $('#flash').slideUp(); }, 1000);

            $('#popup').modal('hide');
        }
    }
</script>

<div class="modal-header">
	<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
	<? if($this->request->data): ?>
		<h3>Editing <?= $this->request->data['Season']['season_name'] ?></h3>
	<? else: ?>
		<h3>Adding Season</h3>
	<? endif; ?>
</div>
<div class="modal-body">
	<?= $this->Form->create('PlayersSeason', array(
		'id' => 'editSeasonForm', 
		'url' => '/players_season/save/'.$id, 
		'class' => 'errorsRight form-horizontal',
	)); ?>
		<? if(isset($player_id)): ?>
			<?= $this->Form->input("player_id", array('id' => 'player_id', 'type' => 'hidden', 'value' => $player_id, 'class' => 'required', 'label' => false)); ?>
		<? endif; ?>
		<div class="row">
			<div class="col-sm-6">
				<label>Season:</label>
				<?= $this->Form->input("season_id", array('id' => 'season_id', 'options' => $seasons, 'class' => 'required', 'label' => false)); ?>
			</div>
			<div class="col-sm-6">
				<label>Character Type:</label>
				<?= $this->Form->input("character_type_id", array('id' => 'character_type_id', 'options' => $character_types, 'class' => 'required', 'label' => false)); ?>
			</div>
		</div>
		<div class="row vspan2">
			<div class="col-sm-6">
				<label>Starting Tribe:</label>
				<?= $this->Form->input("starting_tribe", array('id' => 'starting_tribe', 'label' => false)); ?>
			</div>
			<div class="col-sm-6">
				<label>Swapped Tribe:</label>
				<?= $this->Form->input("swapped_tribe", array('id' => 'swapped_tribe', 'label' => false)); ?>
			</div>
		</div>

		<hr />

		<div class="row vspan2">
			<div class="col-sm-3">
				<label>Age:</label>
				<?= $this->Form->input("age_show", array('id' => 'age_show', 'options' => $ages, 'label' => false, 'data-toggle' => 'tooltip', 'data-placement' => 'top',  'title' => 'The player\'s age when the show aired')); ?>
			</div>
			<div class="col-sm-3">
				<label>Placement:</label>
				<?= $this->Form->input("placement", array('id' => 'placement', 'options' => $placement, 'label' => false, 'data-toggle' => 'tooltip', 'data-placement' => 'top',  'title' => 'The final placing for this player during this season')); ?>
			</div>
			<div class="col-sm-3">
				<label>Days Lasted:</label>
				<?= $this->Form->input("day_voted_out", array('id' => 'day_voted_out', 'options' => $days, 'label' => false, 'data-toggle' => 'tooltip', 'data-placement' => 'top',  'title' => '# of days lasted in the game')); ?>
			</div>
			<div class="col-sm-3">
				<label>Votes Against:</label>
				<?= $this->Form->input("votes_against", array('id' => 'votes_against', 'options' => $numbers, 'label' => false, 'data-toggle' => 'tooltip', 'data-placement' => 'top',  'title' => '# of votes against this player throughout the game')); ?>
			</div>
		</div>

		<div class="row vspan2">
			<div class="col-sm-3">
				<label>Tribe Wins:</label>
				<?= $this->Form->input("tribe_wins", array('id' => 'tribe_wins', 'options' => $numbers, 'label' => false, 'data-toggle' => 'tooltip', 'data-placement' => 'top', 'title' => 'Total # of wins by a tribe this player was a member of while in the game')); ?>
			</div>
			<div class="col-sm-3">
				<label>Individual Wins:</label>
				<?= $this->Form->input("individual_wins", array('id' => 'individual_wins', 'options' => $numbers, 'label' => false, 'data-toggle' => 'tooltip', 'data-placement' => 'top',  'title' => 'Total # of individual wins post merge')); ?>
			</div>
			<div class="col-sm-3">
				<label>Med Evac:</label>
				<?= $this->Form->input("med_evac", array('id' => 'med_evac', 'options' => array('0' => 'No', '1' => 'Yes'), 'label' => false, 'data-toggle' => 'tooltip', 'data-placement' => 'top',  'title' => 'Yes, if medically evacuated from the game')); ?>
			</div>
			<div class="col-sm-3">
				<label>Quit:</label>
				<?= $this->Form->input("quit", array('id' => 'quit', 'options' => array('0' => 'No', '1' => 'Yes'), 'label' => false, 'data-toggle' => 'tooltip', 'data-placement' => 'top',  'title' => 'Yes, if this player quit the game during this season')); ?>
			</div>
		</div>
		
	<?= $this->Form->end(); ?>
</div>
<div class="modal-footer">
	<? if($this->request->data): ?>
		<a href="#" id="delete-btn" data-loading-text="Removing..." players_season_id="<?= $this->request->data['PlayersSeason']['id']; ?>" data-dismiss="modal" style="color: #d9534f;" aria-hidden="true" class="pull-left">Delete Entry</a>
	<? endif; ?>
	<a href="#" id="close-btn" data-dismiss="modal" aria-hidden="true" class="btn btn-default">Close</a>
	<a href="#" id="save-btn" data-loading-text="Saving..." class="btn btn-primary">Save</a>
</div>