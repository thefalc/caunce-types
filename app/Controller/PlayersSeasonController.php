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
class PlayersSeasonController extends AppController {

/**
 * This controller does not use a model
 *
 * @var array
 */
	public $uses = array();

	public function add($player_id) {
		$this->layout = "ajax";

		$this->initSeasonForm();

		$this->set("player_id", $player_id);

		$this->render("edit");
	}

	public function edit($id) {
		$this->layout = "ajax";

		$this->request->data = $this->getSeasonAttributes($id);

		$this->initSeasonForm();
	}

	public function deleteSeason($id) {
		$this->autoRender = false;

        $retval = array('result' => _SUCCESS);

    	$this->PlayersSeason = ClassRegistry::init("PlayersSeason");
        if ($this->PlayersSeason->delete($id)) {
            $retval = array('result' => _SUCCESS);
        } 
        else {
            $retval = array('result' => _FAILURE, "message" => "Could not remove the player season.");
        }    
        
        $this->response->body(json_encode($retval));
        $this->response->type('json');  
	}

	public function save($id = 0) {
		$this->autoRender = false;

        $retval = array('result' => _SUCCESS);
        
        if ($this->request->data) {
        	$this->PlayersSeason = ClassRegistry::init("PlayersSeason");
        	if($id) {
        		$this->request->data['PlayersSeason']['id'] = $id;	
        	}
            else {
            	$this->PlayersSeason->create();
            }
        	
            if ($this->PlayersSeason->save($this->request->data)) {
                $retval = array('result' => _SUCCESS);
            } 
            else {
                $retval = array('result' => _FAILURE, "message" => "Could not save the player information.");
            }    
        }
        
        $this->response->body(json_encode($retval));
        $this->response->type('json');  
	}

	public function saveComment($players_season_id) {
		$this->autoRender = false;

		$retval = array('result' => _SUCCESS);

		$this->PlayersSeason = ClassRegistry::init("PlayersSeason");
		$players_season = $this->PlayersSeason->findById($players_season_id, array("id"));

		if($players_season) {
			if(isset($this->data['additional_comments'])) {
				$players_season['PlayersSeason']['additional_comments'] = $this->data['additional_comments'];
			}
			else if(isset($this->data['boot_circumstances'])) {
				$players_season['PlayersSeason']['boot_circumstances'] = $this->data['boot_circumstances'];
			}

			if(!$this->PlayersSeason->save($players_season)) {
				$retval = array('result' => _FAILURE, "message" => "Could not save the player information.");
			}
		}
		else {
			$retval = array('result' => _FAILURE, "message" => "Could not locate the player information.");
		}

		$this->response->body(json_encode($retval));
        $this->response->type('json');  
	}

	public function getPlayersSeason($players_season_id) {
		$this->layout = "ajax";

		$this->set("player_season", $this->getSeasonAttributes($players_season_id));

		$this->render("/Elements/player_season_attributes");
	}

	private function initSeasonForm() {
		$days = array();
		for($i = 1; $i <= 42; $i++) {
			$days[$i] = $i;
		}

		$placement = array();
		for($i = 0; $i <= 20; $i++) {
			$placement[$i] = $i;
		}

		$ages = array();
		for($i = 18; $i <= 100; $i++) {
			$ages[$i] = $i;
		}

		$numbers = array();
		for($i = 0; $i <= 30; $i++) {
			$numbers[$i] = $i;
		}

		$this->set("days", $days);
		$this->set("numbers", $numbers);
		$this->set("placement", $placement);
		$this->set("ages", $ages);
		$this->set("character_types", $this->getCharacterTypes());
		$this->set("seasons", $this->getSeasons());
	}

	private function getCharacterTypes() {
		$this->CharacterType = ClassRegistry::init("CharacterType");
		$character_types = $this->CharacterType->find("list", array("fields" => array("id", "character_type"), "order" => "character_type"));

		$character_type_list["0"] = "None";

		foreach($character_types as $k => $v) {
			$character_type_list[$k] = $v;			
		}

		// debug($character_types);

		return $character_type_list;
	}

	private function getSeasons() {
		$this->Season = ClassRegistry::init("Season");
		return $this->Season->find("list", array("fields" => array("id", "season_name"), "order" => "season_number"));
	}

	private function getSeasonAttributes($player_season_id) {
		$this->PlayersSeason = ClassRegistry::init("PlayersSeason");

		$joins = array(array("table" => "seasons", "alias" => "Season", "type" => "INNER", "conditions" => array("Season.id = PlayersSeason.season_id")),
				array("table" => "players", "alias" => "Player", "type" => "INNER", "conditions" => array("Player.id = PlayersSeason.player_id")),
            	array("table" => "character_types", "alias" => "CharacterType", "type" => "LEFT", "conditions" => array("CharacterType.id = PlayersSeason.character_type_id")));

    	$player_seasons = $this->PlayersSeason->find("first", array("conditions" => array("PlayersSeason.id" => $player_season_id),
            		"joins" => $joins,
            		"fields" => array("Season.id", "Season.season_name", "Player.fname", "Player.lname",
            			"CharacterType.id", "CharacterType.character_type", "PlayersSeason.id", "PlayersSeason.character_type_id", "PlayersSeason.season_id",
            			"PlayersSeason.age_show", "PlayersSeason.placement", "PlayersSeason.day_voted_out",
            			"PlayersSeason.votes_against", "PlayersSeason.starting_tribe", "PlayersSeason.swapped_tribe",
            			"PlayersSeason.med_evac", "PlayersSeason.quit", "PlayersSeason.boot_circumstances",
            			"PlayersSeason.additional_comments", "PlayersSeason.tribe_wins", "PlayersSeason.individual_wins"
            			)
            		)
    		);

    	return $player_seasons;
	}

	public function getPlayer($id) {
		$this->layout = "ajax";
		$this->Player = ClassRegistry::init("Player");

		$player = $this->Player->findById($id);
		$this->set("player", $player);

		$this->render("/Elements/player_attributes");
	}
}
