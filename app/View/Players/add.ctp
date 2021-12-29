<script type="text/javascript">
    $(document).ready(function() {
        $("#submit-btn").click(function(e) {
            e.preventDefault();

            $("#playerFrom").submit();
        });

        $('#playerFrom').ajaxForm({
            beforeSubmit: function() {
                $("#submit-btn").button("loading");
            },
            success: showResponse
        });
    });

    function showResponse(responseObject) {
        if(typeof responseObject != 'object') {
            responseObject = $.parseJSON(responseObject);    
        }

        if (responseObject.result == "error") {
            $("#submit-btn").button("reset");

            bootbox.alert(responseObject.message, function() {
            	 if(responseObject.player_id != undefined) {
	            	var playerId = responseObject.player_id;
	            	window.location.href = "/players/view/" + playerId;
	            }
            });
        }
        else {
        	$('body').prepend('<div id="flash"></div>');
            $('#flash').html("Player Saved!");
            $('#flash').slideDown('slow');
            $('#flash').click(function () { $('#flash').hide(); });

            var playerId = responseObject.player_id;

            setTimeout(function() { $('#flash').slideUp(); window.location.href = "/players/view/" + playerId }, 1000);

            $('#popup').modal('hide');
        }
    }
</script>

<div class="modal-body">
	<h3 class="text-center">Add Survivor Player</h3>
	<?= $this->Form->create('Player', array(
		'id' => 'playerFrom', 
		'url' => '/players/import/', 
		'class' => 'errorsRight form-horizontal',
	)); ?>
		<div class="row">
			<div class="col-sm-10 col-sm-offset-1">
				<div class="well vspan3" style="background: #fff;">
					<p>
						Input the Wikia URL for the player. The information on Wikia will be automatically imported and create a new player if the player does not already exist.
					</p>
					<div><label>Wikia URL:</label></div>
					<div><?= $this->Form->input("wikia_url", array('id' => 'wikia_url', 'placeholder' => 'http://survivor.wikia.com/wiki/...', 'style' => 'width: 100%', 'class' => 'required', 'label' => false)); ?></div>

				</div>
				
			</div>
		</div>
	<?= $this->Form->end(); ?>
</div>
<div class="modal-footer">
	<a href="#" id="cancel-btn" data-dismiss="modal" aria-hidden="true" class="btn btn-default">Cancel</a>
	<a href="#" id="submit-btn" data-loading-text="Submitting..." class="btn btn-primary">Import</a>
</div>