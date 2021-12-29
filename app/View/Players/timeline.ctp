<? $this->Html->script('flot/jquery.flot', false); ?>
<?// $this->Html->script('flot/jquery.flot.symbol', false); ?>


<script type="text/javascript">
	var players = false;
	var first = true;

	$(function() {
		loadPlayers();

		$("#character_type").change(function(e) {
			window.location.href = "/players/timeline/" + $(this).val();
			// loadPlayers();
		});
	});

	function loadPlayers() {
		var characterType = $("#character_type").val();
		$.getJSON("/players/getPlayersForTimeline/"+characterType, function(response) {
			players = response.players;

			// console.dir(players);

			rendered = false;

			initTimeline();

			first = false;
		});
	}

	var imagesLoaded = 0;

	function initTimeline() {
		var d1 = [];
		var images = [];

		for(var i = 0; i < players.length; i++) {
			var player = players[i];
			d1.push([player.Season.season_number, player.PlayersSeason.placement]);

			var img = new Image();

			img.src = player.Player.image_url;  

			images.push(img);

			img.onload = function() {
		        // console.log($(this).attr('src') + ' - done!');
		        imagesLoaded++;
		        if(imagesLoaded == players.length) {
		        	t(d1, images);
		        	imagesLoaded = 0;
		        }

		    }
		}

		// setTimeout(function() { t(d1, images); }, 3000);

		if(first) {
			$("#placeholder").bind("plothover", function (event, pos, item) {
		        if(item) {
		            if (previousPoint != item.dataIndex) {
		                previousPoint = item.dataIndex;

		                // console.log(item.dataIndex);

		                $("#tooltip").remove();
		                var y = item.datapoint[1];
		                var x = item.datapoint[0];

		                var player = players[item.dataIndex];

		                var label = "<div style='color: #333; font-weight: bold;'>" + player.Player.fname + " " + player.Player.lname + "</div>" + player.Season.season_name + "<br/>Occupation: <span style='color: #999'>" + player.Player.occupation + "</span><br/>Placement: <span style='color: #999'>" + ordinal_suffix_of(player.PlayersSeason.placement) + "</span>";

		                showTooltip(item.pageX, item.pageY,
		                            label);
		            }
		        }
		        else {
		            $("#tooltip").remove();
		            previousPoint = null;            
		        }
		    });
		}
	}

	function ordinal_suffix_of(i) {
	    var j = i % 10,
	        k = i % 100;
	    if (j == 1 && k != 11) {
	        return i + "st";
	    }
	    if (j == 2 && k != 12) {
	        return i + "nd";
	    }
	    if (j == 3 && k != 13) {
	        return i + "rd";
	    }
	    return i + "th";
	}

	function showTooltip(x, y, contents) {
	    $('<div id="tooltip" style="max-width: 300px;">' + contents + '</div>').css({
	        position: 'absolute',
	        display: 'none',
	        top: y + 5,
	        left: x + 5,
	        border: '1px solid #fdd',
	        padding: '2px',
	        'background-color': '#fee',
	        opacity: 0.80
	    }).appendTo("body").fadeIn(200);
	}

	var previousPoint = -1;
	var index = 0;
	var rendered = false;

	function t(d1, images) {
		var placeholder = $("#placeholder");

		var plot = $.plot("#placeholder", [{ data: d1 }], {
			points: { 
				show: true,
				symbol: function(ctx, x, y, axisx, axisy) {  
					var img = images[index];

					// console.log("image index: " + index);

					if(!rendered) {
						ctx.drawImage(img, x-20, y-20, 40, 40);  	
					}

                    if(index + 1 < images.length) index++;
                    else {
                    	index = 0;
                    	rendered = true;
                    }

                    return true;  
                }  
			},
			grid: { hoverable: true, clickable: true, borderWidth: 1 },
			yaxis: { minTickSize: 1, tickDecimals: 0 },
			xaxis: {
				minTickSize: 1,  tickDecimals: 0
			},
		});

		// var o = plot.pointOffset({ x: 2, y: -1.2});
		// placeholder.append("<div style='position:absolute;left:" + (o.left + 4) + "px;top:" + o.top + "px;color:#666;font-size:smaller'>Warming up</div>");

	}
</script>

<div style="padding: 20px; margin-left: 30px;">
	<div class="row">
		<div class="col-xs-12 col-sm-12">
			<div class="form-group">
				<label class="pull-left" style="font-size: 20px; margin-top: 3px;">Caunce Types Visual Explorer:</label>
				<select name="data[character_type]" id="character_type" class="form-control pull-left" style="width: 200px; margin-left: 10px;">
					<? foreach($character_types as $character_type): ?>
						<? if($character_type_id == $character_type['CharacterType']['id']): ?>
							<option selected value="<?= $character_type['CharacterType']['id'] ?>"><?= $character_type['CharacterType']['character_type'] ?></option>
						<? else: ?>
							<option value="<?= $character_type['CharacterType']['id'] ?>"><?= $character_type['CharacterType']['character_type'] ?></option>
						<? endif; ?>
					<? endforeach; ?>
				</select>
				<div class="clearfix"></div>
			</div>
		</div>
	</div>
	<div class="demo-container" style="width: 1200px; height: 600px; border: 1px solid #ddd; background: #fff;">
		<div id="placeholder" class="demo-placeholder" style="width: 100%; height: 100%;"></div>
	</div>
</div>
