<? $totalResults = isset($this->Paginator->params['paging']['Player']['count']) ? $this->Paginator->params['paging']['Player']['count'] : 1; 

	$search_text = Util::getIfSet($this->passedArgs['search_text']);
	$character_type_param = Util::getIfSet($this->passedArgs['character_type']);
	$season_param = Util::getIfSet($this->passedArgs['season']);
	$placement = Util::getIfSet($this->passedArgs['placement']);
	$sex = Util::getIfSet($this->passedArgs['sex']);
	$evac = Util::getIfSet($this->passedArgs['evac']);
	$quit = Util::getIfSet($this->passedArgs['quit']);
	$sort_by = Util::getIfSet($this->passedArgs['sortBy']);
	if(!$sort_by) $sort_by = "name";

	$total_results = isset($this->Paginator->params['paging']['Player']['count']) ? $this->Paginator->params['paging']['Player']['count'] : 1;
?>

<script type="text/javascript">
function blockUI() {
    $.blockUI({ css: { 
        border: 'none', 
        padding: '15px', 
        backgroundColor: '#000', 
        '-webkit-border-radius': '10px', 
        '-moz-border-radius': '10px', 
        opacity: .5, 
        color: '#fff',
    },
    baseZ: 100000,
    message: 'Please wait...' }); 
}

var lastSortBy = "<?= $sort_by ?>";

$(document).ready(function() {	
	$(document).on("click", "#search-button", function(e) {
        e.preventDefault();

        doSearch();
    });

    $(document).on("change", "select", function(e) {
        doSearch();
    });

    $(document).on("click", ".sort-link", function(e) {
    	doSearch($(this).attr("sort-by"));
    });

    $(".search-text").keydown(function(e) {
        if (e.keyCode == 13) {
        	e.preventDefault();
        	doSearch();
        }
    });

    $("#clear-link").click(function(e) {
    	e.preventDefault();

    	doSearch("name", 1);
    });	

    <? if(AuthComponent::user('id')): ?>
    $("a.add-player").click(function(e) {
    	e.preventDefault();

    	addPlayer();
    });
    <? endif; ?>
});

function addPlayer() {
	$.ajax({
       type: "POST",
       url: "/players/add/",
       success: function(html){
            $("#popup #modalContent").html(html);
            
            $("#popup").modal('show');
       }
    });    
}

function doSearch(sortBy, clearFilters) {
	blockUI();

	if(sortBy == undefined) {
		if(lastSortBy == undefined) {
			sortBy = "name";	
		}
		else {
			sortBy = lastSortBy;
		}
	}

	if(clearFilters == undefined) clearFilters = 0;

	if(clearFilters) {
		var searchText = "";
		var characterType = "all";
		var season = "all";
		var placement = "all";
		var sex = "all";
		var evac = "all";
		var quit = "all";
	}
	else {
		var searchText = escape($("#search-text").val());
		var characterType = escape($("#character_type").val());
		var season = escape($("#season").val());
		var placement = escape($("#placement").val());
		var sex = escape($("#sex").val());
		var evac = escape($("#evac").val());
		var quit = escape($("#quit").val());
	}

	window.location.href = "/players/home/search_text:" + searchText + "/character_type:" + characterType + "/season:" + season + "/placement:" + placement + "/sex:" + sex + "/evac:" + evac + "/quit:" + quit + "/sortBy:" + sortBy;

	lastSortBy = sortBy;
}
</script>

<div id="home-page">
	<div class="container" style="padding-bottom: 60px;">
        <div class="row">
            <div class="text-center col-sm-10 col-md-10 col-lg-8 col-lg-offset-2 col-md-offset-1 col-sm-offset-1" style="padding:0px;">
              <h1>Survivor Players</h1>
            </div>
        </div>

        <div class="col-md-8 col-md-push-3 col-sm-8 col-sm-push-4">
			<div class="panel panel-default" style="margin-top: 20px;">
				<div class="panel-heading">
					<div class="row">
						<div class="col-xs-7 col-sm-8">
							<div style="display: inline-block; margin-right: 5px;">
								<input type="text" class="form-control search-text" value="<?= isset($search_text) ? $search_text : ''; ?>" name="data[search]" placeholder="Find Player" id="search-text"/>
							</div>
							
							<a href="#" style="display: inline-block;" title="Search candidates" class="btn btn-primary" role="button" id="search-button">SEARCH</a>
						</div>

						<div class="col-sm-4 col-xs-4 pull-right text-right result-count">
							<ul class="sort-items list-inline">
								<li style="line-height:34px;">
									<a class="sort-link <?= $sort_by == "name" ? "active" : ""; ?>" sort-by="name" href="#" title="Name">NAME</a>
								</li>
								<li style="line-height:34px;">|</li>
								<li style="line-height:34px;">
									<a class="sort-link <?= $sort_by == "season" ? "active" : ""; ?>" sort-by="season" href="#" title="Season">SEASON</a>
								</li>
							</ul>
						</div>
					</div>
				</div>
				<? if($this->passedArgs): ?>
		    		<? foreach($this->passedArgs as $k => $v): ?>
		    			<? if($k != "sortBy" && $this->passedArgs[$k] && $this->passedArgs[$k] != "all"): ?>
		    				<div class="bg-success" style="padding: 10px; font-weight: normal;">
		    					<div class="pull-left">Results are filtered (<a href="#" id="clear-link">clear</a>)</div>
		    					<div class="pull-right">
		    						<? if($total_results == 1): ?>
										<?= $total_results; ?> player found
									<? else: ?>
										<?= $total_results; ?> players found
									<? endif; ?>
		    					</div>
		    					<div class="clearfix"></div>
		    				</div>
		    			<? break; endif; ?>
		    		<? endforeach; ?>
				<? endif; ?>
				<div class="panel-body">
				    <div class="search-results-container">	
				    	<? foreach($players as $player): 
				                $print_name = Util::capitalizeNames($player['Player']['fname'] . ' '.$player['Player']['lname']);
				        ?>
					    	<div class="candidate-result">
					    		<div class="row">
					    			<div class="col-sm-3">
					    				<img class="img-responsive" src="<?= $player['Player']['image_url']; ?>" />
					    			</div>
					    			<div class="col-sm-9">
					    				<h2><a href="/players/view/<?= $player['Player']['id']; ?>"><?= $print_name ?></a></h2>

					    				<? if($player['SeasonDetails']): ?>
						    				<p><a class="character-type" character_type="<?= $player['SeasonDetails']['CharacterType']['id']; ?>" href="/players/home/character_type:<?= $player['SeasonDetails']['CharacterType']['id']; ?>"><?= $player['SeasonDetails']['CharacterType']['character_type']; ?></a></p>
						    				<p>Last Appearance: <a class="season" season="<?= $player['SeasonDetails']['Season']['id']; ?>" href="/seasons/view/<?= $player['SeasonDetails']['Season']['id']; ?>"><?= $player['SeasonDetails']['Season']['season_name']; ?></a></p>
						    				<p>Best finish: <?= $player['0']['best_finish'] ? Util::addOrdinalNumberSuffix($player['0']['best_finish']) : "N/A"; ?></p>
						    			<? endif; ?>
					    			</div>
					    		</div>	
				            </div>
				            <hr style="margin:0" />
				        <? endforeach; ?>
				    </div>

				     <? if($players): ?>
				        <div class="text-center"><?= $this->element("paging_template", array("width" => "auto")); ?></div>
				    <? else: ?>
				       <p class="no-results">Sorry, no results match your search criteria.</p>
				    <? endif; ?>
				</div>
			</div>
		</div>

		<div class="col-md-3 col-md-pull-8 col-sm-4 col-sm-pull-8 filter-side">
			<div class="panel panel-default" style="margin-top: 20px;">
				 <div class="filters">
                    <div class="filter-container">
                        <div class="header">
                            <h3>Filters</h3>
                        </div>
                        <div class="filter-options">
                            <div class="row">
								<div class="col-xs-12 col-sm-12">
									<div class="form-group">
										<label>Character Type:</label>
										<select name="data[character_type]" id="character_type" class="form-control">
											<option value="all">All Types</option>
											
											<? foreach($character_types as $character_type): ?>
												<? if($character_type['CharacterType']['id'] == $character_type_param): ?>
													<option selected value="<?= $character_type['CharacterType']['id'] ?>"><?= $character_type['CharacterType']['character_type'] ?></option>
												<? else: ?>
													<option value="<?= $character_type['CharacterType']['id'] ?>"><?= $character_type['CharacterType']['character_type'] ?></option>
												<? endif; ?>
											<? endforeach; ?>
										</select>
									</div>
									<div class="form-group">
										<label>Season:</label>
										<select name="data[season]" id="season" class="form-control">
											<option value="all">All Seasons</option>
											<? foreach($seasons as $season): ?>
												<? if($season['Season']['id'] == $season_param): ?>
													<option selected value="<?= $season['Season']['id'] ?>"><?= $season['Season']['season_name'] ?></option>
												<? else: ?>
													<option value="<?= $season['Season']['id'] ?>"><?= $season['Season']['season_name'] ?></option>
												<? endif; ?>
											<? endforeach; ?>
										</select>
									</div>
									<div class="form-group">
										<label>Placement:</label>
										<select name="data[placement]" id="placement" class="form-control">
											<option value="all">Any</option>
											<? for($i = 1; $i <= 20; $i++): ?>
												<? if($i == $placement): ?>
													<option selected value="<?= $i ?>"><?= Util::addOrdinalNumberSuffix($i); ?></option>
												<? else: ?>
													<option value="<?= $i ?>"><?= Util::addOrdinalNumberSuffix($i); ?></option>
												<? endif; ?>
											<? endfor; ?>
										</select>
									</div>
									<div class="form-group">
										<label>Sex:</label>
										<select name="data[sex]" id="sex" class="form-control">
											<option value="all">Any</option>
											<option <?= $sex == "M" ? "selected" : ""; ?> value="M">Male</option>
											<option <?= $sex == "F" ? "selected" : ""; ?> value="F">Female</option>
										</select>
									</div>
									<div class="form-group">
										<label>Med Evac:</label>
										<select name="data[evac]" id="evac" class="form-control">
											<option value="all">Any</option>
											<option <?= $evac == "0" ? "selected" : ""; ?> value="0">No</option>
											<option <?= $evac == "1" ? "selected" : ""; ?> value="1">Yes</option>
										</select>
									</div>
									<div class="form-group">
										<label>Quit:</label>
										<select name="data[quit]" id="quit" class="form-control">
											<option value="all">Any</option>
											<option <?= $quit == "0" ? "selected" : ""; ?> value="0">No</option>
											<option <?= $quit == "1" ? "selected" : ""; ?> value="1">Yes</option>
										</select>
									</div>
									<div class="form-group text-left vspan3">
										<a href="/players/download" style="background: url(/img/basic2-263.png) no-repeat; padding-left: 25px;">Download Results as CSV</a>
									</div>
									<div class="form-group text-left vspan2">
										<a href="#" class="login-link add-player" style="background: url(/img/basic1-072.png) no-repeat; padding-left: 25px;">Add new Survivor player</a>
									</div>
								</div>
							</div>
                        </div>
                    </div>
               </div>
			</div>
		</div>
    </div>
</div>

<div id="popup" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content" id="modalContent"></div>
    </div>
</div> 