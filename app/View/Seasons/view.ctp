<? $this->Html->script('jquery-ui-1.10.4.custom.min', false); ?>
<? $this->Html->css('smoothness/jquery-ui-1.10.4.custom.min', null, array("inline"=>false)); ?>

<? if(AuthComponent::user('id')): ?>
	<script type="text/javascript">
		$(document).ready(function() {
			$(document).on("click", "#edit-season", function(e) {
		    	e.preventDefault();

				editSeason($(this).attr("season_id"));
		    });
		});

		function editSeason(seasonId) {
		    resetForms();

		    $.ajax({
		       type: "POST",
		       url: "/seasons/edit/"+ seasonId,
		       success: function(html){
		            $("#popup #modalContent").html(html);
		            
		            $("#popup").modal('show');

		            $('#popup').on('hidden.bs.modal', function () {   
		                updateDynamicDiv("#season-attributes", "/seasons/getSeason/"+seasonId);
					});
		       }
		    });    
		}

		function resetForms() {
			$("#popup #modalContent").html("");
		}
	</script>
<? endif; ?>

<div id="popup" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content" id="modalContent"></div>
    </div>
</div> 

<div id="player-profile">
	<div class="container" style="padding-bottom: 60px; margin-top: 20px;">
        <div class="row">
        	<div class="col-md-8 col-md-offset-2 col-sm-8 col-sm-offset-2">
        		<a href="/seasons/home">← Back to all seasons</a>
        	</div>
        </div>

        <div class="col-md-8 col-md-offset-2 col-sm-8 col-sm-offset-2">
			<div class="panel panel-default" id="season-attributes" style="margin-top: 20px;">
				<?= $this->element("season_attributes"); ?>
			</div>

			<div class="panel panel-default">
				<? if($players): ?>
					<div class="panel-heading">
						<div class="row">
							<div class="col-sm-6">
								<strong>Players in order of finish</strong>
							</div>
						</div>
					</div>
					<div class="panel-body">
						<div class="row">
							<div class="col-sm-12">
								<div class="row">
									<? $i = 1;
										foreach($players as $player): ?>
											<div class="col-sm-6 vspan1">
												<? if($player['PlayersSeason']['placement']): ?>
													<?= Util::addOrdinalNumberSuffix($player['PlayersSeason']['placement']); ?>.
												<? else: ?>
													N/A.
												<? endif; ?>
												 <a href="/players/view/<?= $player['Player']['id']; ?>"><?= $player['Player']['fname']; ?> <?= $player['Player']['lname']; ?></a></div>
									<? $i++; endforeach; ?>
								</div>
							</div>
						</div>
					</div>
				<? endif; ?>	
			</div>
		</div>
	</div>
</div>