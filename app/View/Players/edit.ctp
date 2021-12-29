<script type="text/javascript">
    $(document).ready(function() {
        $("#save-btn").click(function(e) {
            e.preventDefault();

            $("#editPlayerForm").submit();
        });

        $('#editPlayerForm').ajaxForm({
            beforeSubmit: function() {
                $("#save-btn").button("loading");
            },
            success: showResponse
        });
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
            $('#flash').html("Player Saved!");
            $('#flash').slideDown('slow');
            $('#flash').click(function () { $('#flash').hide(); });

            setTimeout(function() { $('#flash').slideUp(); }, 2000);

            $('#popup').modal('hide');
        }
    }
</script>

<div class="modal-header">
	<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
	<h3>Edit <?= $this->request->data['Player']['fname'] ?> <?= $this->request->data['Player']['lname'] ?></h3>
</div>
<div class="modal-body">
	<?= $this->Form->create('Player', array(
		'id' => 'editPlayerForm', 
		'url' => '/players/save/'.$this->request->data['Player']['id'], 
		'class' => 'errorsRight form-horizontal',
	)); ?>
		<div class="row">
			<div class="col-sm-6">
				<label>First name:</label>
				<?= $this->Form->input("fname", array('id' => 'fname', 'class' => 'required', 'label' => false)); ?>
			</div>
			<div class="col-sm-6">
				<label>Last name:</label>
				<?= $this->Form->input("lname", array('id' => 'lname', 'class' => 'required', 'label' => false)); ?>
			</div>
		</div>
		<div class="row vspan2">
			<div class="col-sm-6">
				<label>Nickname:</label>
				<?= $this->Form->input("nickname", array('id' => 'nickname', 'label' => false)); ?>
			</div>
			<div class="col-sm-6">
				<label>Occupation:</label>
				<?= $this->Form->input("occupation", array('id' => 'occupation', 'label' => false)); ?>
			</div>
		</div>
		<div class="row vspan2">
			<div class="col-sm-6">
				<label>Location:</label>
				<?= $this->Form->input("location", array('id' => 'location', 'label' => false)); ?>
			</div>
			<div class="col-sm-6">
				<label>Sex:</label>
				<?= $this->Form->input("sex", array('id' => 'sex', 'options' => array('M' => 'Male', 'F' => 'Female'), 'label' => false)); ?>
			</div>
		</div>
		<div class="row vspan2">
			<div class="col-sm-12">
				<label>Twitter:</label>
				<?= $this->Form->input("twitter", array('id' => 'twitter', 'placeholder' => 'http://www.twitter.com/handle', 'label' => false)); ?>
			</div>
		</div>
		<div class="row vspan2">
			<div class="col-sm-12">
				<label>Wikia Page:</label>
				<?= $this->Form->input("wikia_url", array('id' => 'wikia_url', 'placeholder' => 'http://survivor.wikia.com/wiki/handle', 'label' => false)); ?>
			</div>
		</div>
	<?= $this->Form->end(); ?>
</div>
<div class="modal-footer">
	<a href="#" id="close-btn" data-dismiss="modal" aria-hidden="true" class="btn btn-default">Close</a>
	<a href="#" id="save-btn" data-loading-text="Saving..." class="btn btn-primary">Save</a>
</div>