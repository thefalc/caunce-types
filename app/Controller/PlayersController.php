<?php
/**
 * Static content controller.
 *
 * This file will render views from views/pages/
 *
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       app.Controller
 * @since         CakePHP(tm) v 0.2.9
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */

App::uses('AppController', 'Controller');

/**
 * Static content controller
 *
 * Override this controller by placing a copy in controllers directory of an application
 *
 * @package       app.Controller
 * @link http://book.cakephp.org/2.0/en/controllers/pages-controller.html
 */
class PlayersController extends AppController {

/**
 * This controller does not use a model
 *
 * @var array
 */
	public $uses = array();

	public function beforeFilter() {
        $this->Auth->allow('clearModels', 'home', 'view', 'download', 'timeline', 'getPlayersForTimeline');
    }

    public function timeline($character_type_id = 7) {
		$this->set("title_for_layout", "Caunce Types Timeline View");
		$this->layout = "basic";

		$this->set("character_type_id", $character_type_id);
		$this->set("character_types", $this->getCharacterTypes());
    }

    public function getPlayersForTimeline($character_type_id) {
    	$this->autoRender = false;

    	$this->Season = ClassRegistry::init("Season");

        $players = array();
        try {
        	$joins = array(
        		array("table" => "players_seasons", "alias" => "PlayersSeason", "type" => "INNER", "conditions" => array("PlayersSeason.season_id = Season.id")),
        		array("table" => "players", "alias" => "Player", "type" => "INNER", "conditions" => array("Player.id = PlayersSeason.player_id")),
            	array("table" => "character_types", "alias" => "CharacterType", "type" => "INNER", "conditions" => array("CharacterType.id = PlayersSeason.character_type_id")));

        	$fields = array("Player.fname", "Player.lname", "Player.nickname", "Player.twitter", 
        		"Player.occupation", "Player.sex", "Player.image_url", "Player.wikia_url", "Player.location",
        		"Season.id", "Season.season_name", "Season.season_number", 
            			"PlayersSeason.age_show", "PlayersSeason.placement", "PlayersSeason.day_voted_out", "PlayersSeason.votes_against", 
            			"PlayersSeason.starting_tribe", "PlayersSeason.swapped_tribe", "PlayersSeason.med_evac", "PlayersSeason.quit", 
            			"PlayersSeason.tribe_wins", "PlayersSeason.individual_wins",
            			"CharacterType.id", "CharacterType.character_type");

        	$conditions = array("CharacterType.id" => $character_type_id);

            $order = array("Season.season_number");

            $players = $this->Season->find("all", compact('joins', 'fields', 'order', 'conditions'));

            $retval = array("result" => _SUCCESS, "players" => $players);
        } catch(Exception $e) {
        	debug($e);
        }

        $this->response->body(json_encode($retval));
        $this->response->type('json');  
    }

    public function download() {
    	$default = ini_get('max_execution_time');
        set_time_limit(0);
        ini_set("memory_limit", "256M");

    	$this->response->download("SurvivorPlayersCaunceTypes".strtotime(date("Y-m-d H:i:s")).".csv");

    	$players = $this->getAllPlayers();
    	
    	$this->set(compact('players'));
    	$this->layout = 'ajax';

    	return;
    }

    public function add() {
		$this->layout = "ajax";
	}

	public function import() {
		$this->autoRender = false;

		$url = Util::getIfSet($this->request->data['Player']['wikia_url']);

		$this->Player = ClassRegistry::init("Player");
		$this->Season = ClassRegistry::init("Season");
		$this->PlayersSeason = ClassRegistry::init("PlayersSeason");

		try {
			$player = $this->Player->findByWikiaUrl(trim($url), array("id"));

			// player already exists, so attempt to import seasons
			if($player) {
				$html = Util::getResponse($url);
				$start = strpos($html, "class=\"toccolours\"");

				if($start !== false) {
					$html = substr($html, $start);

					$birth_date = $this->getTagValue($html, "<b>Born:</b>", "<span style=\"display:none\">");

					// need to add season information
					$this->importSeasons($html, $player['Player']['id'], $birth_date);
				}
				$retval = array("result" => _SUCCESS, "player_id" => $player['Player']['id']);
			}
			else {
				$html = Util::getResponse($url);

				$start = strpos($html, "portable-infobox pi-background pi-theme-wikia pi-layout-default");
				$player = array();

				if($start !== false) {
					$html = substr($html, $start);
					$name = Util::getValue($html, "<h2 class=\"pi-item pi-item-spacing pi-title\" data-source=\"title\">", "</h2>");

					$image_url = Util::getValue($html, "href=\"", "\"");

					$player['Player']['image_url'] = $image_url;
					$player['Player']['wikia_url'] = $url;

					// name
					$player['Player']['fname'] = Util::firstName($name);
					$player['Player']['lname'] = Util::lastName($name);

					$birth_date = $this->getTagValue($html, "<h3 class=\"pi-data-label pi-secondary-font\">Born:</h3>", "<span style=\"display:none\">");
			
					// get hometown
					$player['Player']['location'] = $this->getTagValue($html, "<h3 class=\"pi-data-label pi-secondary-font\">Hometown:</h3>", "</div>");

					// get occupation
					$player['Player']['occupation'] = $this->getTagValue($html, "<h3 class=\"pi-data-label pi-secondary-font\">Occupation:</h3>", "</div>");

					// create the player
					$this->Player->create();
					if($this->Player->save($player)) {
						// need to add season information
						$this->importSeasons($html, $this->Player->id, $birth_date);

						$retval = array("result" => _SUCCESS, "player_id" => $this->Player->id);
					}
				}
				else {
					$retval = array("result" => _FAILURE, "message" => "The page entered does not appear to contain Survivor player information. Make sure you copied the URL correctly. Please contact <a href=\"mailto:falconer.sean@gmail.com\">falconer.sean@gmail.com</a> if you believe this is an error.");
				}
			}
		} catch(Exception $e) {
			debug($e);
			$retval = array("result" => _FAILURE, "message" => "The page entered does not appear to contain Survivor player information. Make sure you copied the URL correctly. Please contact <a href=\"mailto:falconer.sean@gmail.com\">falconer.sean@gmail.com</a> if you believe this is an error.");
		}

		$this->response->body(json_encode($retval));
        $this->response->type('json');  
	}

	private function importSeasons($html, $player_id, $birth_date) {
		$start = strpos($html, "<h2 class=\"pi-item pi-header pi-secondary-font pi-item-spacing pi-secondary-background\"><i>");

		$start = strpos($html, "<h2 class=\"pi-item pi-header pi-secondary-font pi-item-spacing pi-secondary-background\"><i>", $start + 20);

		while($start !== false) {
			$html = substr($html, $start + 6);

			$season_url = Util::getValue($html, "href=\"", "\"");

			if($season_url) {
				$season_url = trim("https://survivor.fandom.com".$season_url);
				$season = $this->Season->findByWikiaUrl($season_url, array("id", "premiere_date"));

				// Can't find season, try the old URL
				if(!$season) {
					$season_url = str_replace("https://survivor.fandom.com", "http://survivor.wikia.com", $season_url);
					$season = $this->Season->findByWikiaUrl($season_url, array("id", "premiere_date"));	
				}
	
				if($season) { // season exists
					// mapping between player and season does not exist
					if(!$this->PlayersSeason->find("count", array("conditions" => array("player_id" => $player_id, "season_id" => $season['Season']['id'])))) {
						$player_season = array();

						if($birth_date) {
							$d1 = new DateTime($birth_date);
							$d2 = new DateTime($season['Season']['premiere_date']);

							$diff = $d2->diff($d1);

							if($diff) {
								$player_season['PlayersSeason']['age_show'] = $diff->y;
							}
							else {
								$player_season['PlayersSeason']['age_show'] = 0;
							}
						}

						$player_season['PlayersSeason']['season_id'] = $season['Season']['id'];
						$player_season['PlayersSeason']['player_id'] = $player_id;

						$player_season['PlayersSeason']['placement'] = $this->getTagValue($html, "<h3 class=\"pi-data-label pi-secondary-font\">Finish:</h3>");

						if(strstr($player_season['PlayersSeason']['placement'], "Winner")) {
							$player_season['PlayersSeason']['placement'] = "1";
						}
						else if(strstr($player_season['PlayersSeason']['placement'], "2nd Runner-Up")) {
							$player_season['PlayersSeason']['placement'] = "3";
						}
						else if(strstr($player_season['PlayersSeason']['placement'], "Runner-Up")) {
							$player_season['PlayersSeason']['placement'] = "2";
						}
						else {
							$end = strpos($player_season['PlayersSeason']['placement'], "/");
							if($end !== false) {
								$player_season['PlayersSeason']['placement'] = Util::removeNonNumeric(substr($player_season['PlayersSeason']['placement'], 0, $end));
							}
						}

						$player_season['PlayersSeason']['votes_against'] = $this->getTagValue($html, "<h3 class=\"pi-data-label pi-secondary-font\">Votes Against:</h3>", "</div>");
						$player_season['PlayersSeason']['day_voted_out'] = $this->getTagValue($html, "<h3 class=\"pi-data-label pi-secondary-font\">Days Lasted:</h3>", "</div>");

						// kill any null values
						foreach($player_season['PlayersSeason'] as $k => $v) {
							if(is_null($player_season['PlayersSeason'][$k])) {
								$player_season['PlayersSeason'][$k] = "";
							}
						}

						$this->PlayersSeason->create();
						$this->PlayersSeason->save($player_season);
					}
				}
			}

			$start = strpos($html, "<h2 class=\"pi-item pi-header pi-secondary-font pi-item-spacing pi-secondary-background\">");
		}
	}

	private function getTagValue($html, $tag, $end_tag = "</td>") {
		$start = strpos($html, $tag);
		if($start !== false) {
			$html = substr($html, $start + strlen($tag));

			$value = Util::getValue($html, "<div class=\"pi-data-value pi-font\">", $end_tag);

			if(is_null($value)) $value = "";

			return $value;
		}
		return "";
	}

	private function getHometown($html) {
		$start = strpos($html, "<b>Hometown</b>");
		if($start !== false) {
			$html = substr($html, $start + 15);

			return Util::getValue($html, "<small>", "</small>");
		}
	}

	private function getOccupation($html) {
		$start = strpos($html, "<b>Occupation</b>");
		if($start !== false) {
			$html = substr($html, $start + 15);

			return Util::getValue($html, "<small>", "</small>");
		}
	}

	public function edit($id) {
		$this->layout = "ajax";

		$this->Player = ClassRegistry::init("Player");
		$this->request->data = $this->Player->findById($id);
	}

	function clearModels() {
        $this->autoRender = false;
        Cache::clear(false, '_cake_model_');
    }

	public function save($id) {
		$this->autoRender = false;

        $retval = array('result' => _SUCCESS);
        
        if ($this->request->data) {
            $this->request->data['Player']['id'] = $id;

            if($this->request->data['Player']['fname'] && $this->request->data['Player']['lname']) {
            	$this->Player = ClassRegistry::init("Player");
	            if ($this->Player->save($this->request->data)) {
	                $retval = array('result' => _SUCCESS);
	            } 
	            else {
	                $retval = array('result' => _FAILURE, "message" => "Could not save the player information.");
	            }    
            }
            else {
                $retval = array('result' => _FAILURE, "message" => "You are missing required fields.");
            }  
        }
        
        $this->response->body(json_encode($retval));
        $this->response->type('json');  
	}

	public function getPlayer($id) {
		$this->layout = "ajax";
		$this->Player = ClassRegistry::init("Player");

		$player = $this->Player->findById($id);
		$this->set("player", $player);

		$this->render("/Elements/player_attributes");
	}

	public function view($id) {
		$this->Player = ClassRegistry::init("Player");

		$player = $this->Player->findById($id);

		$this->set("title_for_layout", "Viewing ".$player['Player']['fname']." ".$player['Player']['lname']);
		$this->set("player", $player);
		$this->set("character_types", $this->getCharacterTypes());
		$this->set("seasons", $this->getSeasons());
		$this->set("player_seasons", $this->getPlayerSeasons($player['Player']['id']));
	}

	public function home() {
		$this->set("title_for_layout", "Welcome to the Caunce Types");

		if($this->passedArgs) {
			$this->Session->write("passed_args", $this->passedArgs);
		}
		else if($this->Session->check("passed_args")) {
			$this->passedArgs = $this->Session->read("passed_args");
		}

		$this->set("players", $this->getPlayers());
		$this->set("character_types", $this->getCharacterTypes());
		$this->set("seasons", $this->getSeasons());
	}

	private function getPlayerSeasons($player_id) {
		$this->PlayersSeason = ClassRegistry::init("PlayersSeason");

		$joins = array(array("table" => "seasons", "alias" => "Season", "type" => "INNER", "conditions" => array("Season.id = PlayersSeason.season_id")),
            	array("table" => "character_types", "alias" => "CharacterType", "type" => "LEFT", "conditions" => array("CharacterType.id = PlayersSeason.character_type_id")));

    	$player_seasons = $this->PlayersSeason->find("all", array("conditions" => array("PlayersSeason.player_id" => $player_id),
            		"order" => array("Season.season_number DESC"), "joins" => $joins,
            		"fields" => array("Season.id", "Season.season_name", 
            			"CharacterType.id", "CharacterType.character_type", "PlayersSeason.id",
            			"PlayersSeason.age_show", "PlayersSeason.placement", "PlayersSeason.day_voted_out",
            			"PlayersSeason.votes_against", "PlayersSeason.starting_tribe", "PlayersSeason.swapped_tribe",
            			"PlayersSeason.med_evac", "PlayersSeason.quit", "PlayersSeason.boot_circumstances",
            			"PlayersSeason.additional_comments", "PlayersSeason.tribe_wins", "PlayersSeason.individual_wins"
            			)
            		)
    		);

    	return $player_seasons;
	}

	private function getCharacterTypes() {
		$this->CharacterType = ClassRegistry::init("CharacterType");
		$items =  $this->CharacterType->find("all", array("order" => "character_type"));

		return $items;
	}

	private function getSeasons() {
		$this->Season = ClassRegistry::init("Season");
		return $this->Season->find("all", array("order" => "season_number"));
	}

	private function getAllPlayers() {
		$this->Player = ClassRegistry::init("Player");
		$this->PlayersSeason = ClassRegistry::init("PlayersSeason");

        $limit = 1000;
        
        $players = array();
        try {
        	$joins = array(
        		array("table" => "players_seasons", "alias" => "PlayersSeason", "type" => "INNER", "conditions" => array("PlayersSeason.player_id = Player.id")),
        		array("table" => "seasons", "alias" => "Season", "type" => "INNER", "conditions" => array("Season.id = PlayersSeason.season_id")),
            	array("table" => "character_types", "alias" => "CharacterType", "type" => "LEFT", "conditions" => array("CharacterType.id = PlayersSeason.character_type_id")));


        	$fields = array("Player.fname", "Player.lname", "Player.nickname", "Player.twitter", 
        		"Player.occupation", "Player.sex", "Player.image_url", "Player.wikia_url", "Player.location",
        		"Season.id", "Season.season_name", "Season.season_number", 
            			"PlayersSeason.age_show", "PlayersSeason.placement", "PlayersSeason.day_voted_out", "PlayersSeason.votes_against", 
            			"PlayersSeason.starting_tribe", "PlayersSeason.swapped_tribe", "PlayersSeason.med_evac", "PlayersSeason.quit", 
            			"PlayersSeason.tribe_wins", "PlayersSeason.individual_wins",
            			"CharacterType.id", "CharacterType.character_type");

            $params = $this->getPlayerSearchParams($fields, $limit, $joins);

            $players = $this->Player->find("all", $params);

        } catch(Exception $e) {
        	debug($e);
        }

        return $players;
	}

	private function getPlayers() {
		$this->Player = ClassRegistry::init("Player");
		$this->PlayersSeason = ClassRegistry::init("PlayersSeason");

		$page = isset($this->request->params['named']['page']) ? $this->request->params['named']['page'] : 1;

        $players = array();
        try {
            $this->paginate = $this->getPlayerSearchParams();

            $players = $this->paginate('Player');

            $joins = array(array("table" => "seasons", "alias" => "Season", "type" => "LEFT", "conditions" => array("Season.id = PlayersSeason.season_id")),
            	array("table" => "character_types", "alias" => "CharacterType", "type" => "LEFT", "conditions" => array("CharacterType.id = PlayersSeason.character_type_id")));

            foreach($players as &$player) {
            	$details = $this->PlayersSeason->find("first", array("conditions" => array("PlayersSeason.player_id" => $player['Player']['id']),
            		"order" => array("Season.season_number DESC"), "limit" => 1, "joins" => $joins,
            		"fields" => array("Season.id", "Season.season_name", "CharacterType.id", "CharacterType.character_type")));

            	$player['SeasonDetails'] = $details;
            }
        } catch(Exception $e) {
        	debug($e);
        }

        return $players;
	}

	private function getPlayerSearchParams($fields = array(), $limit = 10, $joins = array()) {
		$search_text = Util::getIfSet($this->passedArgs['search_text']);
		$character_type = Util::getIfSet($this->passedArgs['character_type']);
		$season = Util::getIfSet($this->passedArgs['season']);
		$placement = Util::getIfSet($this->passedArgs['placement']);
		$sex = Util::getIfSet($this->passedArgs['sex']);
		$evac = Util::getIfSet($this->passedArgs['evac']);
		$quit = Util::getIfSet($this->passedArgs['quit']);
		$sort_by = Util::getIfSet($this->passedArgs['sortBy']);

		if($character_type && $character_type != "all") {
			$conditions[] = array("(select count(*) from players_seasons ps where ps.player_id = Player.id 
				and ps.character_type_id = ".$character_type.") > 0");
		}
		if($season && $season != "all") {
			$conditions[] = array("(select count(*) from players_seasons ps where ps.player_id = Player.id 
				and ps.season_id = ".$season.") > 0");
		}
		if($placement && $placement != "all") {
			$conditions[] = array("(select count(*) from players_seasons ps where ps.player_id = Player.id 
				and ps.placement = ".$placement.") > 0");
		}
		if($evac && $evac != "all") {
			$conditions[] = array("(select count(*) from players_seasons ps where ps.player_id = Player.id 
				and ps.med_evac = ".$evac.") > 0");
		}
		if($quit && $quit != "all") {
			$conditions[] = array("(select count(*) from players_seasons ps where ps.player_id = Player.id 
				and ps.quit = ".$quit.") > 0");
		}
		if($sex && $sex != "all") {
			$conditions["Player.sex"] = $sex;
		}

		if(!$sort_by || $sort_by == "name") {
			$order = array("Player.lname", "Player.fname");
		}
		else {
			$order = "(select s.season_number from players_seasons ps, seasons s where s.id = ps.season_id and ps.player_id = Player.id order by s.season_number desc limit 1)";
		}
		
        $q = addslashes(strtolower($search_text));
        if($q) {
        	$conditions[] = array("concat_ws(' ', Player.fname, Player.lname) like" => '%'. $q . '%');	
		}
        
       	if(!$fields) {
       		$fields = array(
	        	'Player.id', 'Player.fname', 'Player.lname', 'Player.image_url', 'Player.location',
	        	'(select ps.placement from players_seasons ps where ps.player_id = Player.id order by ps.placement limit 1) as best_finish'
	        );
       	}
        
      
        return compact('order', 'conditions', 'fields', 'limit', 'joins', 'group');
	}
}
