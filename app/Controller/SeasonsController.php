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
class SeasonsController extends AppController {

/**
 * This controller does not use a model
 *
 * @var array
 */
	public $uses = array();

	public function download() {
    	$default = ini_get('max_execution_time');
        set_time_limit(0);
        ini_set("memory_limit", "256M");

    	$this->response->download("SurvivorSeasonsCaunceTypes".strtotime(date("Y-m-d H:i:s")).".csv");

    	$seasons = $this->getAllSeasons();
    	
    	$this->set(compact('seasons'));
    	$this->layout = 'ajax';

    	return;
    }

	public function home() {
		$this->set("title_for_layout", "Survivor Seasons");

		if($this->passedArgs) {
			$this->Session->write("season_passed_args", $this->passedArgs);
		}
		else if($this->Session->check("season_passed_args")) {
			$this->passedArgs = $this->Session->read("season_passed_args");
		}

		$starting_players = $this->Season->find("all", array("fields" => array("Season.starting_players"), "group" => array("Season.starting_players"), "order" => array("Season.starting_players")));
		$ftc_counts = $this->Season->find("all", array("fields" => array("Season.ftc_count"), "group" => array("Season.ftc_count"), "order" => array("Season.ftc_count")));
		$jury_counts = $this->Season->find("all", array("fields" => array("Season.jury_count"), "group" => array("Season.jury_count"), "order" => array("Season.jury_count")));

		$this->set(compact('starting_players', 'ftc_counts', 'jury_counts'));
		$this->set("seasons", $this->getSeasons());
	}

	public function edit($id) {
		$this->layout = "ajax";

		$this->Season = ClassRegistry::init("Season");
		$this->request->data = $this->Season->findById($id);

		$numbers = array();
		for($i = 1; $i <= 50; $i++) {
			$numbers[$i] = $i;
		}

		$starting_player_numbers = array();
		for($i = 16; $i <= 24; $i++) {
			$starting_player_numbers[$i] = $i;
		}

		$tribal_numbers = array();
		for($i = 1; $i <= 4; $i++) {
			$tribal_numbers[$i] = $i;
		}

		$days = array();
		for($i = 0; $i <= 42; $i++) {
			$days[$i] = $i;
		}

		$player_numbers = array();
		for($i = 0; $i <= 20; $i++) {
			$player_numbers[$i] = $i;
		}

		$this->set(compact('numbers', 'starting_player_numbers', 'tribal_numbers', 'days', 'player_numbers'));
	}

	public function save($id) {
		$this->autoRender = false;

        $retval = array('result' => _SUCCESS);
        
        if ($this->request->data) {
            $this->request->data['Season']['id'] = $id;

            if($this->request->data['Season']['season_name'] && $this->request->data['Season']['season_number']) {
            	$this->Season = ClassRegistry::init("Season");

            	$this->request->data['Season']['premiere_date'] = date("Y-m-d", strtotime($this->request->data['Season']['premiere_date']));
            	$this->request->data['Season']['finale_date'] = date("Y-m-d", strtotime($this->request->data['Season']['finale_date']));
	            if ($this->Season->save($this->request->data)) {
	                $retval = array('result' => _SUCCESS);
	            } 
	            else {
	                $retval = array('result' => _FAILURE, "message" => "Could not save the season information.");
	            }    
            }
            else {
                $retval = array('result' => _FAILURE, "message" => "You are missing required fields.");
            }  
        }
        
        $this->response->body(json_encode($retval));
        $this->response->type('json');  
	}

	public function getSeason($id) {
		$this->layout = "ajax";
		$this->Season = ClassRegistry::init("Season");

		$season = $this->Season->findById($id);
		$this->set("season", $season);

		$this->render("/Elements/season_attributes");
	}

	public function view($id) {
		$this->Season = ClassRegistry::init("Season");

		$season = $this->Season->findById($id);

		$this->set("title_for_layout", "Viewing ".$season['Season']['season_name']);
		$this->set("season", $season);
		$this->set("players", $this->getPlayers($season['Season']['id']));
	}

	private function getPlayers($season_id) {
		$this->PlayersSeason = ClassRegistry::init("PlayersSeason");

		$joins = array(array("table" => "players", "alias" => "Player", "type" => "INNER", "conditions" => array("Player.id = PlayersSeason.player_id")));


		return $this->PlayersSeason->find("all", array("conditions" => array("PlayersSeason.season_id" => $season_id),
				"joins" => $joins, "order" => array("PlayersSeason.placement"), 
				"fields" => array("Player.id", "Player.fname", "Player.lname", "PlayersSeason.placement")
			));
	}

	private function getAllSeasons() {
		$this->Season = ClassRegistry::init("Season");

        try {
        	$fields = array("Season.*");
        	$limit = 100;
            $this->paginate = $this->getSeasonSearchParams($fields, $limit);

            $seasons = $this->paginate('Season');
        } catch(Exception $e) {
        	debug($e);
        }

        return $seasons;
	}

	private function getSeasons() {
		$this->Season = ClassRegistry::init("Season");
		$this->Player = ClassRegistry::init("Player");

		$page = isset($this->request->params['named']['page']) ? $this->request->params['named']['page'] : 1;
        $limit = 10;
        
        $players = array();
        try {
            $this->paginate = $this->getSeasonSearchParams();

            $seasons = $this->paginate('Season');

            $joins = array(array("table" => "players_seasons", "alias" => "PlayersSeason", "type" => "INNER", "conditions" => array("PlayersSeason.player_id = Player.id")),
            	array("table" => "character_types", "alias" => "CharacterType", "type" => "LEFT", "conditions" => array("CharacterType.id = PlayersSeason.character_type_id")));

            foreach($seasons as &$season) {
            	$details = $this->Player->find("first", array("conditions" => array("PlayersSeason.season_id" => $season['Season']['id'], "PlayersSeason.placement" => 1),
            		"order" => array("PlayersSeason.placement"), "limit" => 1, "joins" => $joins,
            		"fields" => array("Player.id", "Player.image_url", "Player.fname", "Player.lname", "CharacterType.id", "CharacterType.character_type")));

            	$season['PlayerDetails'] = $details;
            }
        } catch(Exception $e) {
        	debug($e);
        }

        return $seasons;
	}

	private function getSeasonSearchParams($fields = array(), $limit = 10) {
		$search_text = Util::getIfSet($this->passedArgs['search_text']);

		$evac = Util::getIfSet($this->passedArgs['evac']);
		$quit = Util::getIfSet($this->passedArgs['quit']);
		$start = Util::getIfSet($this->passedArgs['start']);
		$ftc_count = Util::getIfSet($this->passedArgs['ftc_count']);
		$jury_count = Util::getIfSet($this->passedArgs['jury_count']);
		$sort_by = Util::getIfSet($this->passedArgs['sortBy']);

		if($evac && $evac != "all") {
			$conditions[] = array("(select count(*) from players_seasons ps where ps.season_id = Season.id 
				and ps.med_evac = ".$evac.") > 0");
		}
		if($quit && $quit != "all") {
			$conditions[] = array("(select count(*) from players_seasons ps where ps.season_id = Season.id 
				and ps.quit = ".$quit.") > 0");
		}
		if($start && $start != "all") {
			$conditions["Season.starting_players"] = $start;
		}
		if($jury_count && $jury_count != "all") {
			$conditions["Season.jury_count"] = $jury_count;
		}
		if($ftc_count && $ftc_count != "all") {
			$conditions["Season.ftc_count"] = $ftc_count;
		}

		if(!$sort_by || $sort_by == "number") {
			$order = array("Season.season_number");
		}
		else {
			$order = array("Season.season_name");
		}

        $q = addslashes(strtolower($search_text));
        if($q) {
        	$conditions[] = array("Season.season_numbe like" => '%'. $q . '%');	
		}
        
        if(!$fields) {
        	$fields = array(
	        	'Season.id', 'Season.season_name', 'Season.premiere_date', 'Season.finale_date', 'Season.starting_players', 'Season.jury_count'
	        );
        }
      
        return compact('order', 'conditions', 'fields', 'limit', 'joins', 'group');
	}
}
