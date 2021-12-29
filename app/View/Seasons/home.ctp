<? 
	$search_text = Util::getIfSet($this->passedArgs['search_text']);

	$ftc_count = Util::getIfSet($this->passedArgs['ftc_count']);
	$jury_count = Util::getIfSet($this->passedArgs['jury_count']);
	$starting_player_count = Util::getIfSet($this->passedArgs['start']);
	$evac = Util::getIfSet($this->passedArgs['evac']);
	$quit = Util::getIfSet($this->passedArgs['quit']);
	$sort_by = Util::getIfSet($this->passedArgs['sortBy']);
	if(!$sort_by) $sort_by = "number";

	$total_results = isset($this->Paginator->params['paging']['Season']['count']) ? $this->Paginator->params['paging']['Season']['count'] : 1;
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

    	doSearch("date", 1);
    });	
});

function doSearch(sortBy, clearFilters) {
	blockUI();

	if(sortBy == undefined) {
		if(lastSortBy == undefined) {
			sortBy = "date";	
		}
		else {
			sortBy = lastSortBy;
		}
	}

	if(clearFilters == undefined) clearFilters = 0;

	if(clearFilters) {
		var searchText = "";
		var evac = "all";
		var quit = "all";
		var start = "all";
		var ftc_count = "all";
		var jury_count = "all";
	}
	else {
		var searchText = escape($("#search-text").val());
		var evac = escape($("#evac").val());
		var quit = escape($("#quit").val());
		var start = escape($("#starting_player_count").val());
		var ftc_count = escape($("#ftc_count").val());
		var jury_count = escape($("#jury_count").val());
	}
	
	
	window.location.href = "/seasons/home/search_text:" + searchText + "/ftc_count:" + ftc_count + "/jury_count:" + jury_count + "/start:" + start + "/evac:" + evac + "/quit:" + quit + "/sortBy:" + sortBy;

	lastSortBy = sortBy;
}
</script>

<div id="home-page">
	<div class="container" style="padding-bottom: 60px;">
        <div class="row">
            <div class="text-center col-sm-10 col-md-10 col-lg-8 col-lg-offset-2 col-md-offset-1 col-sm-offset-1" style="padding:0px;">
              <h1>Survivor Seasons</h1>
            </div>
        </div>

        <div class="col-md-8 col-md-push-3 col-sm-8 col-sm-push-4">
			<div class="panel panel-default" style="margin-top: 20px;">
				<div class="panel-heading">
					<div class="row">
						<div class="col-xs-7 col-sm-8">
							<div style="display: inline-block; margin-right: 5px;">
								<input type="text" class="form-control search-text" value="<?= isset($search_text) ? $search_text : ''; ?>" name="data[search]" placeholder="Find Season" id="search-text"/>
							</div>
							
							<a href="#" style="display: inline-block;" title="Search candidates" class="btn btn-primary" role="button" id="search-button">SEARCH</a>
						</div>

						<div class="col-sm-4 col-xs-4 pull-right text-right result-count">
							<ul class="sort-items list-inline">
								<li style="line-height:34px;">
									<a class="sort-link <?= $sort_by == "number" ? "active" : ""; ?>" sort-by="number" href="#" title="Name">AIR DATE</a>
								</li>
								<li style="line-height:34px;">|</li>
								<li style="line-height:34px;">
									<a class="sort-link <?= $sort_by == "name" ? "active" : ""; ?>" sort-by="name" href="#" title="Season">NAME</a>
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
										<?= $total_results; ?> season found
									<? else: ?>
										<?= $total_results; ?> seasons found
									<? endif; ?>
		    					</div>
		    					<div class="clearfix"></div>
		    				</div>
		    			<? break; endif; ?>
		    		<? endforeach; ?>
				<? endif; ?>
						
				<div class="panel-body">
				    <div class="search-results-container">	
				    	
				    	<? foreach($seasons as $season): 
				    			if($season['PlayerDetails']) {
				    				$print_name = Util::capitalizeNames($season['PlayerDetails']['Player']['fname'] . ' '.$season['PlayerDetails']['Player']['lname']);
				    			}
				                
				        ?>
					    	<div class="candidate-result">
					    		<div class="row">
					    			<div class="col-sm-9">
					    				<h2><a href="/seasons/view/<?= $season['Season']['id']; ?>"><?= $season['Season']['season_name'] ?></a></h2>

					    				<? if(strtotime($season['Season']['premiere_date']) > 0): ?>
						    				<p>Aired: <?= date("F d, Y", strtotime($season['Season']['premiere_date'])); ?></p>
						    			<? endif; ?>
					    				<? if(strtotime($season['Season']['finale_date']) > 0): ?>
						    				<p>Finale: <?= date("F d, Y", strtotime($season['Season']['finale_date'])); ?></p>
						    			<? endif; ?>
					    				<p><?= $season['Season']['starting_players']; ?> players</p>
					    				<p><?= $season['Season']['jury_count']; ?> jury members</p>
					    			</div>
					    			<? if($season['PlayerDetails']): ?>
						    			<div class="col-sm-3">
						    				<p style="text-align: center; margin-bottom: 0px;">Winner</p>
						    				<img class="img-responsive" src="<?= $season['PlayerDetails']['Player']['image_url']; ?>" />
						    				<p style="text-align: center;"><a href="/players/view/<?= $season['PlayerDetails']['Player']['id'] ?>"><?= $print_name ?></a></p>
					    				</div>	
					    			<? endif; ?>
					    		</div>	
				            </div>
				            <hr style="margin:0" />
				        <? endforeach; ?>
				    </div>

				     <? if($seasons): ?>
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
										<label>Starting Players:</label>
										<select name="data[starting_players]" id="starting_player_count" class="form-control">
											<option value="all">Any</option>
											<? foreach($starting_players as $start): ?>
												<option <?= $starting_player_count == $start['Season']['starting_players'] ? "selected" : ""; ?> value="<?= $start['Season']['starting_players'] ?>"><?= $start['Season']['starting_players'] ?> Players</option>
											<? endforeach; ?>
										</select>
									</div>
									<div class="form-group">
										<label>Jury Members:</label>
										<select name="data[jury_count]" id="jury_count" class="form-control">
											<option value="all">Any</option>
											<? foreach($jury_counts as $option): ?>
												<option <?= $jury_count == $option['Season']['jury_count'] ? "selected" : ""; ?> value="<?= $option['Season']['jury_count'] ?>"><?= $option['Season']['jury_count'] ?> Players</option>
											<? endforeach; ?>
										</select>
									</div>
									<div class="form-group">
										<label>Final Tribal:</label>
										<select name="data[ftc_count]" id="ftc_count" class="form-control">
											<option value="all">Any</option>
											<? foreach($ftc_counts as $option): ?>
												<option <?= $ftc_count == $option['Season']['ftc_count'] ? "selected" : ""; ?> value="<?= $option['Season']['ftc_count'] ?>"><?= $option['Season']['ftc_count'] ?> Players</option>
											<? endforeach; ?>
										</select>
									</div>
									<div class="form-group">
										<label>Had a Med Evac:</label>
										<select name="data[evac]" id="evac" class="form-control">
											<option value="all">Any</option>
											<option <?= $evac == "0" ? "selected" : ""; ?> value="0">No</option>
											<option <?= $evac == "1" ? "selected" : ""; ?> value="1">Yes</option>
										</select>
									</div>
									<div class="form-group">
										<label>Had a Quit:</label>
										<select name="data[quit]" id="quit" class="form-control">
											<option value="all">Any</option>
											<option <?= $quit == "0" ? "selected" : ""; ?> value="0">No</option>
											<option <?= $quit == "1" ? "selected" : ""; ?> value="1">Yes</option>
										</select>
									</div>

									<div class="form-group text-center vspan3">
										<a href="/seasons/download" style="background: url(/img/basic2-263-gray.png) no-repeat; padding-left: 25px;">Download Results as CSV</a>
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