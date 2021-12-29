<? if(AuthComponent::user('id')): ?>
	<script type="text/javascript">
		$(document).ready(function() {
			$(document).on("click", "#edit-player", function(e) {
		    	e.preventDefault();

				editPlayer($(this).attr("player_id"));
		    });

		    $(document).on("click", "a.edit-season", function(e) {
		    	e.preventDefault();

				editSeason($(this).attr("player_season_id"));
		    });

		    $(document).on("click", "a.add-comment", function(e) {
		    	e.preventDefault();

		    	editComment(this);
		    });

		    $(document).on("click", "a.cancel-btn", function(e) {
		    	e.preventDefault();

		    	uneditComment(this);
		    });

		    $(document).on("click", "a.save-btn", function(e) {
		    	e.preventDefault();

		    	saveComment(this);
		    });

		    $(document).on("click", "a.add-season", function(e) {
		    	e.preventDefault();

		    	addSeason($(this).attr("player_id"));
		    });
		});

		function saveComment(object) {
			var commentObject = $(object).parent().parent().find(".additional-comments"); 
			var id = $(commentObject).attr("players_season_id");

			$.post("/players_season/saveComment/" + id, $(commentObject).serialize(), function(response) {
		    	if(response.result == "success") {
		    		var tmp = $(object).parent().parent().parent().find(".edit-comments-container");
		    		tmp.html("<div class=\"col-sm-10\">"+$(commentObject).val()+"</div><div class=\"col-sm-2 text-right\">" +
						"<a class=\"add-comment\" href=\"#\">Edit</a>" +
						"</div>");

		    		uneditComment(object);
		    	}
		    });
		}

		function editComment(object) {
			$(object).parent().parent().parent().find(".additional-comments-form").show();
			$(object).parent().parent().parent().find(".edit-comments-container").hide();
		}

		function uneditComment(object) {
			$(object).parent().parent().parent().find(".additional-comments-form").hide();
			$(object).parent().parent().parent().find(".edit-comments-container").show();
		}

		function editPlayer(playerId) {
		    resetForms();

		    $.ajax({
		       type: "POST",
		       url: "/players/edit/"+ playerId,
		       success: function(html){
		            $("#popup #modalContent").html(html);
		            
		            $("#popup").modal('show');

		            $('#popup').on('hidden.bs.modal', function () {   
		                updateDynamicDiv("#player-attributes", "/players/getPlayer/"+playerId);
					});
		       }
		    });    
		}

		function editSeason(playerSeasonId) {
		    resetForms();

		    $.ajax({
		       type: "POST",
		       url: "/players_season/edit/"+ playerSeasonId,
		       success: function(html){
		            $("#popup #modalContent").html(html);
		            
		            $("#popup").modal('show');

		            $('#popup').on('hidden.bs.modal', function () {   
		                updateDynamicDiv("#"+playerSeasonId, "/players_season/getPlayersSeason/"+playerSeasonId);
					});
		       }
		    });    
		}

		function addSeason(playerId) {
			resetForms();

		    $.ajax({
		       type: "POST",
		       url: "/players_season/add/" + playerId,
		       success: function(html){
		            $("#popup #modalContent").html(html);
		            
		            $("#popup").modal('show');
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
        		<a href="/players/home">← Back to all players</a>
        	</div>
        </div>

        <div class="col-md-8 col-md-offset-2 col-sm-8 col-sm-offset-2">
			<div class="panel panel-default" id="player-attributes" style="margin-top: 20px;">
				<?= $this->element("player_attributes"); ?>
			</div>

			<? if($player_seasons): ?>
				<? foreach($player_seasons as $player_season): ?>
					<div id="<?= $player_season['PlayersSeason']['id']; ?>" class="panel panel-default" style="margin-top: 20px;">
						<?= $this->element("player_season_attributes", array("player_season" => $player_season)); ?>
					</div>
				<? endforeach; ?>
			<? endif; ?>	

			<div class="row vspan4">
				<div class="col-sm-10">
					<a player_id="<?= $player['Player']['id']; ?>" class="login-link add-season" href="#">+ Add season for this player</a>
				</div>
			</div>
		</div>
	</div>
</div>