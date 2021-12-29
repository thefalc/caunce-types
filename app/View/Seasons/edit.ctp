<?
	$premiere_date = date('m/d/Y', strtotime($this->request->data['Season']['premiere_date']));
	$finale_date = date('m/d/Y', strtotime($this->request->data['Season']['finale_date']));
?>

<script type="text/javascript">
	var selectedPremiereDate = "<?= $premiere_date; ?>";
	var selectedFinaleDate = "<?= $finale_date; ?>";

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

        $("#premiere_date").datepicker();
        $("#finale_date").datepicker();

	    if(selectedPremiereDate) {
	        $("#premiere_date").datepicker( "setDate", selectedPremiereDate );    
	    }

	    if(selectedFinaleDate) {
	        $("#finale_date").datepicker( "setDate", selectedFinaleDate );    
	    }

	    $("#premiere_date").datepicker("option", "dateFormat", "DD, d MM, yy");
        $("#finale_date").datepicker("option", "dateFormat", "DD, d MM, yy");
    });

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
            $('#flash').html("Season Saved!");
            $('#flash').slideDown('slow');
            $('#flash').click(function () { $('#flash').hide(); });

            setTimeout(function() { $('#flash').slideUp(); }, 2000);

            $('#popup').modal('hide');
        }
    }
</script>

<div class="modal-header">
	<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
	<h3>Edit <?= $this->request->data['Season']['season_name'] ?></h3>
</div>
<div class="modal-body" id="season-form">
	<?= $this->Form->create('Season', array(
		'id' => 'editSeasonForm', 
		'url' => '/seasons/save/'.$this->request->data['Season']['id'], 
		'class' => 'errorsRight form-horizontal',
	)); ?>
		<div class="row">
			<div class="col-sm-6">
				<label>Season Name:</label>
				<?= $this->Form->input("season_name", array('id' => 'season_name', 'class' => 'required', 'label' => false)); ?>
			</div>
		</div>
		<div class="row vspan2">
			<div class="col-sm-6">
				<label>Premiere Date:</label>
				<input type="text" id="premiere_date" name="data[Season][premiere_date]" />
			</div>
			<div class="col-sm-6">
				<label>Finale:</label>
				<input type="text" id="finale_date" name="data[Season][finale_date]" />
			</div>
		</div>
		
		<div class="attribute-header vspan3">
			Season Numeric Attributes
		</div>
		<div class="row vspan2">
			<div class="col-sm-4">
				<label>Season Number:</label>
				<?= $this->Form->input("season_number", array('id' => 'season_number', 'options' => $numbers, 'class' => 'required', 'label' => false, 'data-toggle' => 'tooltip', 'data-placement' => 'top',  'title' => 'The season number in terms of its\' aired order')); ?>
			</div>
			<div class="col-sm-4">
				<label>Starting Players:</label>
				<?= $this->Form->input("starting_players", array('id' => 'starting_players', 'options' => $starting_player_numbers, 'label' => false, 'data-toggle' => 'tooltip', 'data-placement' => 'top',  'title' => '# of players at the beginning of the game')); ?>
			</div>
			<div class="col-sm-4">
				<label>Starting Tribes:</label>
				<?= $this->Form->input("starting_tribes", array('id' => 'starting_tribes', 'options' => $tribal_numbers, 'label' => false, 'data-toggle' => 'tooltip', 'data-placement' => 'top',  'title' => '# of tribes at the beginning of the game')); ?>
			</div>
		</div>
		
		<div class="row vspan2">
			<div class="col-sm-4">
				<label>Merge:</label>
				<?= $this->Form->input("merge_players", array('id' => 'merge_players', 'options' => $player_numbers, 'label' => false, 'data-toggle' => 'tooltip', 'data-placement' => 'top',  'title' => '# of players that made it to the merge')); ?>
			</div>
			<div class="col-sm-4">
				<label>Final Tribal:</label>
				<?= $this->Form->input("ftc_count", array('id' => 'ftc_count', 'options' => $player_numbers, 'label' => false, 'data-toggle' => 'tooltip', 'data-placement' => 'top',  'title' => '# of players in the final tribal counsel')); ?>
			</div>
			<div class="col-sm-4">
				<label>Jury:</label>
				<?= $this->Form->input("jury_count", array('id' => 'jury_count', 'options' => $player_numbers, 'label' => false, 'data-toggle' => 'tooltip', 'data-placement' => 'top',  'title' => '# of players on the jury')); ?>
			</div>
		</div>

		<div class="row vspan2">
			<div class="col-sm-4">
				<label>Swap Day 1:</label>
				<?= $this->Form->input("swap_day1", array('id' => 'swap_day1', 'options' => $days, 'label' => false, 'data-toggle' => 'tooltip', 'data-placement' => 'top',  'title' => 'The day the first swap in the game took place (0 if no swap)')); ?>
			</div>
			<div class="col-sm-4">
				<label>Swap Day 2:</label>
				<?= $this->Form->input("swap_day2", array('id' => 'swap_day2', 'options' => $days, 'label' => false, 'data-toggle' => 'tooltip', 'data-placement' => 'top',  'title' => 'The day the second swap in the game took place (0 if no second swap)')); ?>
			</div>
		</div>

		<div class="attribute-header vspan3">
			Tribe Names
		</div>
		<div class="row vspan2">
			<div class="col-sm-6">
				<label>Merge Tribe:</label>
				<?= $this->Form->input("merge_tribe", array('id' => 'merge_tribe', 'label' => false)); ?>
			</div>
		</div>
		<div class="row vspan2">
			<div class="col-sm-6">
				<label>Tribe 1:</label>
				<?= $this->Form->input("tribe1", array('id' => 'tribe1', 'label' => false)); ?>
			</div>
			<div class="col-sm-6">
				<label>Tribe 2:</label>
				<?= $this->Form->input("tribe2", array('id' => 'tribe2', 'label' => false)); ?>
			</div>
		</div>
		<div class="row vspan2">
			<div class="col-sm-6">
				<label>Tribe 3:</label>
				<?= $this->Form->input("tribe3", array('id' => 'tribe3', 'label' => false)); ?>
			</div>
			<div class="col-sm-6">
				<label>Tribe 4:</label>
				<?= $this->Form->input("tribe4", array('id' => 'tribe4', 'label' => false)); ?>
			</div>
		</div>
	<?= $this->Form->end(); ?>
</div>
<div class="modal-footer">
	<a href="#" id="close-btn" data-dismiss="modal" aria-hidden="true" class="btn btn-default">Close</a>
	<a href="#" id="save-btn" data-loading-text="Saving..." class="btn btn-primary">Save</a>
</div>