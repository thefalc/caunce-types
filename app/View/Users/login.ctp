<script type="text/javascript">
    $(document).ready(function() {
        $("#login-btn").click(function(e) {
            e.preventDefault();

            $("#userForm").submit();
        });

        $('#userForm').validate({
            submitHandler: function() {
                $("#login-btn").button("loading");

                $('#userForm').ajaxSubmit(showResponse);
            }
        });

        $("input").keypress(function(e) {
            if(e.keyCode == 13) {
                e.preventDefault();
                
                $('#userForm').submit();
            }
        });
    });

    function showResponse(responseObject) {
        if(typeof responseObject != 'object') {
            responseObject = $.parseJSON(responseObject);    
        }

        if (responseObject.result == "error") {
            $("#login-btn").button("reset");

            bootbox.alert(responseObject.message);
        }
        else {
            $('body').prepend('<div id="flash"></div>');
            $('#flash').html("Login Successful");
            $('#flash').slideDown('slow');
            $('#flash').click(function () { $('#flash').hide(); });

            setTimeout(function() { $('#flash').slideUp(); window.location.reload(); }, 1000);
        }
    }
</script>

<div class="modal-body">
	<h3 class="text-center">Login to Caunce Types</h3>
	<?= $this->Form->create('User', array(
		'id' => 'userForm', 
		'url' => '/users/login/', 
		'class' => 'errorsRight form-horizontal',
	)); ?>
		<div class="row">
			<div class="col-sm-8 col-sm-offset-2">
				<div class="well vspan3" style="background: #fff;">
					<div><label>Email:</label></div>
					<div><?= $this->Form->input("username", array('id' => 'username', 'style' => 'width: 100%', 'class' => 'required', 'label' => false)); ?></div>

					<div class="vspan2">
						<label>Password:</label>
						<?= $this->Form->input("password", array('id' => 'password', 'style' => 'width: 100%', 'class' => 'required', 'label' => false)); ?>
					</div>
				</div>
				
			</div>
		</div>
	<?= $this->Form->end(); ?>
</div>
<div class="modal-footer">
	<a href="#" id="cancel-btn" data-dismiss="modal" aria-hidden="true" class="btn btn-default">Cancel</a>
	<a href="#" id="login-btn" data-loading-text="Submitting..." class="btn btn-primary">Login</a>
</div>