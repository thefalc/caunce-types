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

class MySimpleHeap extends SplHeap 
{ 
    public function  compare( $value1, $value2 ) { 
        // echo $value1['result']. ' '. $value2['result'].'<br>';

        return ( $value1['result'] > $value2['result'] ); 
    } 
}

/**
 * Static content controller
 *
 * Override this controller by placing a copy in controllers directory of an application
 *
 * @package       app.Controller
 * @link http://book.cakephp.org/2.0/en/controllers/pages-controller.html
 */
class ImportController extends AppController {

/**
 * This controller does not use a model
 *
 * @var array
 */
	public $uses = array();

    private $total_behaviors = 22;
    private $start_behavior_index = 7;

    public function compareWinners() {
        $this->autoRender = false;

        $winner_vectors = $this->readWinnerData();

        for($i = 0; $i < count($winner_vectors); $i++) {
            $best_result = 0; $best_index = 0;
            for($k = 0; $k < count($winner_vectors); $k++) {
                if($i == $k) continue;

                $result = $this->dotProduct($winner_vectors[$k], $winner_vectors[$i], 2, 2);

                // better result, so save it
                if($best_result < $result) {
                    $best_result = $result;
                    $best_index = $k;
                }
            }

            // save the best result
            $best_results[] = array("result" => $best_result, "index" => $best_index, "name" => $winner_vectors[$i][0]);
        }

        foreach($best_results as $result) { 
            $length = strpos($winner_vectors[$result['index']][0], "-");

            echo $result['name']. ' most similar to '.substr($winner_vectors[$result['index']][0], 0, $length).' '.$result['result'].'<br>';
        } 
        echo "<br>";
    }

    public function computeWinner() {
        $this->autoRender = false;

        $winner_vectors = $this->readWinnerData();
        $episode_vectors = $this->createEpisodeVectors();   

        // go episode by episode, player by player, and figure out which player looks 
        // the most like a winner at this moment in time
        for($i = 1; $i <= count($episode_vectors); $i++) {
            $best_results = array();

            // each player
            for($j = 0; $j < count($episode_vectors[$i]); $j++) {
                if($i > $episode_vectors[$i][$j]['num_of_episodes']) continue; 

                $player_behaviors = $episode_vectors[$i][$j]['behaviors'];

                $best_result = 0; $best_index = 0;
                for($k = 0; $k < count($winner_vectors); $k++) {
                    $result = $this->dotProduct($winner_vectors[$k], $player_behaviors);

                    // better result, so save it
                    if($best_result < $result) {
                        $best_result = $result;
                        $best_index = $k;
                    }
                }

                // save the best result
                $best_results[] = array("result" => $best_result, "index" => $best_index, "name" => $episode_vectors[$i][$j]['name']);
            }

            // sort by largest results
            usort($best_results, function ($a, $b) {
                if (floatval($a['result']) == floatval($b['result'])) {
                    return 0;
                }
                return (floatval($a['result']) < floatval($b['result'])) ? 1 : -1;
            });

            echo "Episode ".$i.":<br>";

            // print the top 3 options
            $count = 0;
            foreach($best_results as $result) { 
                $length = strpos($winner_vectors[$result['index']][0], "-");

                echo $result['name']. ' most similar to '.substr($winner_vectors[$result['index']][0], 0, $length).'<br>';
                $count++;
            } 
            echo "<br>";
        }
    }

    private function dotProduct($v1, $v2, $offset1 = 2, $offset2 = 0) {
        $result = 0;
        $length_v1 = 0;
        $length_v2 = 0;

        for($i = 0; $i < count($v2) - $offset2; $i++) {
            $result += $v1[$i + $offset1] * $v2[$i + $offset2];

            $length_v1 += $v1[$i + $offset1] * $v1[$i + $offset1];
            $length_v2 += $v2[$i + $offset2] * $v2[$i + $offset2];
        }

        $length_v1 = sqrt($length_v1);
        $length_v2 = sqrt($length_v2);

        return $result / ($length_v1 * $length_v2);
    }

    private function normalize($behavior_instance_count, $num_of_codings) {
        $alpha = 1 + $behavior_instance_count;
        $beta = 1 + $num_of_codings - $behavior_instance_count;

        $estimate_of_expected_behavior = $alpha / ($alpha + $beta);
        $variance_of_behavior = ($alpha * $beta) / (pow($alpha + $beta, 2) * ($alpha + $beta + 1));
        $standard_deviation = sqrt($variance_of_behavior);

        $feature_value = $estimate_of_expected_behavior - $standard_deviation;

        $min_conf = $estimate_of_expected_behavior - $standard_deviation;
        $max_conf = $estimate_of_expected_behavior + $standard_deviation;

        if(is_nan($feature_value)) return 0;

        return $feature_value;
    }

    private function createEpisodeVectors() {
        $season_number = 37;
        $total_episodes = 12;
        $independent_codes = 4;

        //2, 3, 5, 13, 14, 17, 18, 20, 22, 24, 26, 27, 29, 30, 33

        $raw_data = $this->readRawData($season_number);

        $players = $this->getPlayers($raw_data);

        $episodes = array();

        $current_episode = 1;

        for($current_episode = 1; $current_episode <= $total_episodes; $current_episode++) {
            $episodes[$current_episode] = $players;

            $current_player_index = 0;
            foreach($players as $player) {
                // player has been booted already, so set their vector to zeros
                if($current_episode > $episodes[$current_episode][$current_player_index]['num_of_episodes']) {
                    for($j = 0; $j < $this->total_behaviors; $j++) {
                        $episodes[$current_episode][$current_player_index]['behavior'][] = 0;
                    }
                }
                else { // compute the behavior vector based on episodes seen so far
                    // echo "Episode ".$current_episode.":<br>";

                    for($i = 0; $i < count($raw_data); $i++) {
                        // we passed the current episode, so break
                        if($raw_data[$i][6] > $current_episode) break;

                        // only care about matching players
                        if($raw_data[$i][5] == $episodes[$current_episode][$current_player_index]['id']) {
                            $found = false;

                            // loop over all behaviors for a single episode for this player
                            for($j = $this->start_behavior_index; $j < $this->total_behaviors + $this->start_behavior_index; $j++) {
                                // echo $raw_data[$i][$j].",";

                                $behavior_index = $j - $this->start_behavior_index;

                                // no behavior value set yet
                                if(!isset($episodes[$current_episode][$current_player_index]['behaviors'][$behavior_index])) {
                                    $episodes[$current_episode][$current_player_index]['behaviors'][$behavior_index] = 0;
                                }

                                // if there was an observed behavior, mark it
                                if($raw_data[$i][$j]) {
                                    $episodes[$current_episode][$current_player_index]['behaviors'][$behavior_index]++;
                                }
                            }

                            // echo "<br>";
                        }
                    }

                    // normalize behaviors by current episode number
                    for($j = 0; $j < count($episodes[$current_episode][$current_player_index]['behaviors']); $j++) {
                        $episodes[$current_episode][$current_player_index]['behaviors'][$j] = $this->normalize($episodes[$current_episode][$current_player_index]['behaviors'][$j], $current_episode * $independent_codes);
                    }

                    echo $current_episode.",".$episodes[$current_episode][$current_player_index]['name'];
                    for($j = 0; $j < count($episodes[$current_episode][$current_player_index]['behaviors']); $j++) {
                        echo ",".$episodes[$current_episode][$current_player_index]['behaviors'][$j];
                    }
                    echo "<br>";
                }

                $current_player_index++;
            }
        }

        // exit;

        return $episodes;
    }

    private function getPlayers($raw_data) {
        $players = array();
        $seen = array();
        for($i = 0; $i < count($raw_data); $i++) {
            if($raw_data[$i][6] == 2) break;

            $episodes = 1;
            $current_episode = 1;

            $episodes = $this->getNumOfEpisodes($raw_data, $raw_data[$i][5]);

            $name = $raw_data[$i][0].' '.$raw_data[$i][1];

            if(isset($seen[$name])) continue;

            $seen[$name] = true;

            $players[] = array("name" => $name, "id" => $raw_data[$i][5],
                "behaviors" => array(), "num_of_episodes" => $episodes);
        }

        return $players;
    }

    private function getNumOfEpisodes($raw_data, $player_id) {
        $current_episode = 0;
        $episodes = 0;

        // try to figure out how many episodes this person was in
        for($j = 0; $j < count($raw_data); $j++) {
            if($raw_data[$j][5] == $player_id) {
                $current_episode++;
                $found = false;
                for($k = $this->start_behavior_index; $k < $this->total_behaviors + $this->start_behavior_index; $k++) {
                    if($raw_data[$j][$k]) {
                        $found = true;
                        break;
                    }
                }

                // found an entry, set number of episodes as current one
                if($found) {
                    $episodes = $raw_data[$j][6];
                }
            }
        }

        return $episodes;
    }

    private function readRawData($season_number) {
        $this->Behavior = ClassRegistry::init("Behavior");

        $query = "select p.fname, p.lname, s.season_number, br.* from behavior_records br, players p, players_seasons ps, seasons s where br.player_id = p.id and ps.season_id = br.season_id
            and ps.player_id = br.player_id and s.season_number = ".$season_number." and s.id = ps.season_id order by episode, player_id";

        $results = $this->Behavior->query($query);

        $clean_results = array();
        foreach($results as $result) {
            $item = array($result['p']['fname'], $result['p']['lname'], $result['s']['season_number']);
            foreach($result['br'] as $k => $v) {
                $item[] = $v;
            }

            $clean_results[] = $item;
        }

        return $clean_results;
    }

    private function readWinnerData() {
        $file = WWW_ROOT."/files/winning_player_vectors_10_seasons.csv";
        // $file = WWW_ROOT."/files/all_season_vectors.in";
        $result = file_get_contents($file);
        $data = preg_split("/\r\n|\n|\r/", $result);

        $winners = array();

        for($i = 0; $i < count($data)-1; $i++) {
            if($i == 0) continue; 

            $values = str_getcsv($data[$i], ",", '"');

            $winners[] = $values;
        }

        return $winners;
    }

    private function getBehaviorData($character_type_id) {
        $this->Behavior = ClassRegistry::init("Behavior");

        $query = "select p.fname, p.lname, s.season_number, br.* from behavior_records br, players p, players_seasons ps, seasons s where br.player_id = p.id and ps.season_id = br.season_id
            and ps.player_id = br.player_id and ps.placement = 1 and s.id = ps.season_id order by player_id, episode";

        $results = $this->Behavior->query($query);

        $clean_results = array();
        foreach($results as $result) {
            $item = array($result['p']['fname'], $result['p']['lname'], $result['s']['season_number']);
            foreach($result['br'] as $k => $v) {
                $item[] = $v;
            }

            $clean_results[] = $item;
        }

        return $clean_results;
    }

    private function getSeasonData($season_number) {
        $this->Behavior = ClassRegistry::init("Behavior");

        if(is_array($season_number)) $season_number = implode(",", $season_number);

        $query = "select p.fname, p.lname, s.season_number, br.*, ps.placement, s.id, ct.character_type from behavior_records br, players p, players_seasons ps, seasons s, character_types ct where br.player_id = p.id and ps.season_id = br.season_id and ct.id = ps.character_type_id
            and ps.player_id = br.player_id and s.season_number in (".$season_number.") and s.id = ps.season_id order by s.season_number, player_id, episode";

            ini_set("memory_limit", "512M");

  
        $results = $this->Behavior->query($query);

        $clean_results = array();
        foreach($results as $result) {
            $item = array($result['p']['fname'], $result['p']['lname'], $result['s']['season_number']);
            foreach($result['br'] as $k => $v) {
                $item[] = $v;
            }

            $item[] = $result['ps']['placement'];
            $item[] = $result['s']['id'];
            $item[] = $result['ct']['character_type'];

            $clean_results[] = $item;
        }

        return $clean_results;
    }

    /**
    *   Generates the feature vectors for players from seasons specified.
    *   This is used to train the model.
    */
    public function processBehaviors() {
        $this->autoRender = false;

        $default = ini_get('max_execution_time');
        set_time_limit(0);

        $this->Behavior = ClassRegistry::init("Behavior");
        $behaviors = $this->Behavior->find("all");

        echo "name,class";
        foreach($behaviors as $behavior) {
            echo ",".$behavior['Behavior']['behavior'];
        }
        echo "<br>";
        $data = $this->getSeasonData(array(1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 20, 21, 22, 23, 24, 25, 26, 27, 28, 29, 30, 31, 32, 33, 35));

        $player_table = array();
        $current_player_id = 0;
        for($i = 0; $i < count($data); $i++) {
            if($i == 0) continue; 

            $values = $data[$i];

            $current_player_id = $values[5];
            $player_name = $values[0]. " ".$values[1]. "-".$values[2];
            
            $character_type = $values[count($values)-1];
            $season_id = $values[count($values)-2];
            $placement = $values[count($values)-3];
            $player_table[$player_name] = array();

            // loop over all behavior entries for this contestant
            for($i = $i; $i < count($data); $i++) {
                $values = $data[$i];

                if($current_player_id != $values[5]) {
                    break;
                }

                $found = false;
                
                // loop over all behaviors for a single episode for this player
                for($j = $this->start_behavior_index; $j < $this->total_behaviors + $this->start_behavior_index; $j++) {
                    $behavior_index = $j - $this->start_behavior_index;

                    // no behavior value set yet
                    if(!isset($player_table[$player_name][$behavior_index])) {
                        $player_table[$player_name][$behavior_index] = 0;
                    }

                    // if there was an observed behavior, mark it
                    if($values[$j]) {
                        $player_table[$player_name][$behavior_index]++;
                    }
                }
            }

            $unique_coders = $this->Behavior->query("select count(distinct fan_name) as count from behavior_records where season_id = ".$season_id);
            $unique_coders = $unique_coders[0][0]['count'];

            $num_of_episodes = $this->getNumOfEpisodes($data, $current_player_id);

            echo "\"".$player_name."\"";
            echo ",".($placement <= 3 ? "1" : "0");

            for($j = 0; $j < count($player_table[$player_name]); $j++) {
                //echo "behaviors: ".$player_table[$player_name][$j]."<br>";
               // echo "total entries: ".$total_entries."<br>";

                $player_table[$player_name][$j] = $this->normalize($player_table[$player_name][$j], $num_of_episodes * $unique_coders);

                echo ",".$player_table[$player_name][$j];
            }

            echo "<br>";

            $i--;
        }

        echo "<br>";
        
    }

    /** goes over all episode CSVs and imports into DB */
    private function processRecords($dir) {
        flush();

        $parts = explode("_-_", $dir);
        $parts2 = explode("_", $parts[0]);

        $season_num = $parts2[1];

        $parts2 = explode("_", $parts[2]);

        $fan_name = $parts2[0]." ".$parts2[1];

        $this->BehaviorRecord = ClassRegistry::init("BehaviorRecord");
        $this->Season = ClassRegistry::init("Season");
        $this->Player = ClassRegistry::init("Player");

        $season = $this->Season->findBySeasonNumber($season_num);
        if(!$season) {
            echo "could not locate season: ".$season_num."<br>";
            exit;
        }

        $dir = $dir."/*";
        foreach(glob($dir) as $file) {
            $filename = basename($file);
            if(strstr($filename, "Ep.")) {
                $result = file_get_contents($file);
                $data = preg_split("/\r\n|\n|\r/", $result);

                $episode = str_replace("Ep. ", "", $filename);
                $episode = str_replace(".csv", "", $episode);

                echo $filename."<br>";
                flush();

                $first = true;

                $players = array();

                // invert rows and cols
                $arr = array();
                for($i = 1; $i < count($data); $i++) {
                    $values = str_getcsv($data[$i], ",", '"');

                    $arr[] = $values;
                }

                $existing = $this->BehaviorRecord->find("count", array("conditions" =>
                    array("season_id" => $season['Season']['id'], "fan_name" => $fan_name, "episode" => $episode)));

                // delete existing records
                if($existing) {
                    $this->BehaviorRecord->deleteAll(array("season_id" => $season['Season']['id'], "fan_name" => $fan_name, "episode" => $episode));
                }

                // debug($arr);
                // exit;

                for($i = 1; $i < count($arr[0]); $i++) {
                    $record = array("season_id" => $season['Season']['id'], "fan_name" => $fan_name, "episode" => $episode);

                    // debug($record);
                    // exit;

                    for($j = 0; $j < count($arr); $j++) {
                        if(!isset($arr[$j][$i])) continue;

                        $value = trim($arr[$j][$i]);

                        // echo $value.",";
                        if($j == 0 && $i != 0 && $value) { // player name
                            // cleaning names for matching purposes
                            $name_changes = array("Jessica \"Figgy\"" => "Figgy", "MiYeson" => "Mixon", "Justin \"Jay\"" => "Jay",
                                "Taylor Lee" => "Taylor", "Ciandre \"CeCe\"" => "CeCe", "Brendan    Shapiro" => "Brendan Shapiro", "Daniel Rengering" => "Dan Rengering", "Gabriela Pascuzzi" => "Gabby Pascuzzi", "Michael White" => "Mike White", "Natalia Azoqu" => "Natalia Azoqa");

                            foreach($name_changes as $key => $name_change) {
                                $value = str_replace($key, $name_change, $value);
                            }

                            $value = preg_replace('!\s+!', ' ', $value);

                            $end = strpos($value, " ");
                            $first_name = substr($value, 0, $end);
                            $last_name = str_replace($first_name." ", "", $value);

                            $joins = array(array("table" => "players_seasons", "alias" => "PlayersSeason", "type" => "INNER", "conditions" => array("Player.id = PlayersSeason.player_id")));

                            $player = $this->Player->find("first", array(
                                "joins" => $joins,
                                "conditions" => array("fname" => $first_name, "lname" => $last_name, "season_id" => $season['Season']['id']),
                            ));
                           
                            if(!$player) {
                                echo "could not find player ".$value;
                                exit;
                            }

                            $record['player_id'] = $player['Player']['id'];
                            // echo $value.",";
                        }
                        else if(trim($arr[$j][$i])) {
                            // TODO: Fix import for all!!!!
                            if(strtolower($arr[$j][0]) == "egotisical") {
                                $arr[$j][0] = "egotistical";
                            }
                            
                            $record[strtolower($arr[$j][0])] = 1;
                            // echo $arr[$j][0].",";
                            // echo $value.",";
                        }
                        else {
                            // echo ",";
                        }                        
                    }   

                    $this->BehaviorRecord->create();
                    $this->BehaviorRecord->save($record);
                }
            }
        }
    }

    public function importBehaviors() {
        $this->autoRender = false;

        $file = WWW_ROOT."/files/One World Test - Behaviours Refined.csv";

        $this->Behavior = ClassRegistry::init("Behavior");

        $result = file_get_contents($file);
        $data = preg_split("/\r\n|\n|\r/", $result);

        $this->Behavior->query("delete from behaviors");

        $first = true;

        foreach($data as $line) {
            if($first) {
                $first = false;
                continue;
            }

            $values = str_getcsv($line, ",", '"');

            $behavior = trim($values[0]);
            $explanation = trim($values[1]);
            $example = trim($values[2]);

            $this->Behavior->create();
            $this->Behavior->save(array("behavior" => $behavior, "explanation" => $explanation, 
                "iconic_example" => $example, "created_date" => date("Y-m-d H:i:s")));
        }
    }

    public function generateSpreadsheet() {
        $this->autoRender = false;

        $this->Player = ClassRegistry::init("Player");
        $this->Behavior = ClassRegistry::init("Behavior");
        $this->Season = ClassRegistry::init("Season");

        for($season_number = 32; $season_number <= 33; $season_number++) {
            $joins = array(array("table" => "players_seasons", "alias" => "PlayersSeason", "type" => "INNER", "conditions" => array("PlayersSeason.player_id = Player.id")),
                array("table" => "seasons", "alias" => "Season", "type" => "INNER", "conditions" => array("Season.id = PlayersSeason.season_id")));

            $players = $this->Player->find("all", array("joins" => $joins, 
                "conditions" => array("Season.season_number" => $season_number),
                "fields" => array("Player.fname", "Player.lname")));

            $behaviors = $this->Behavior->find("all");

            $season = $this->Season->findBySeasonNumber($season_number);

            $csv = "";
            for($i = 1; $i < 13; $i++) {
                $csv = "Episode ".$i."\n";

                $csv .= ",";
                foreach($players as $player) {
                    $csv .= ",".$player['Player']['fname']." ".$player['Player']['lname'];
                }
                $csv .= "\n";

                // print behaviors
                $category = "";
                $sub_category = "";
                // echo ",";
                foreach($behaviors as $behavior) {
                    $csv .= $behavior['Behavior']['behavior'].",\"".$behavior['Behavior']['explanation']."\",";
                    $csv .= "\n";
                }
                $csv .= "\n";

                file_put_contents(WWW_ROOT."/files/csvs/season".$season_number.".csv", $csv);

                break;
            }
        }
    }

    public function importCharacterTypeMapping() {
        $this->autoRender = false;

        $file = WWW_ROOT."/files/character_types.csv";

        $this->CharacterType = ClassRegistry::init("CharacterType");
        $this->Player = ClassRegistry::init("Player");
        $this->Season = ClassRegistry::init("Season");
        $this->PlayersSeason = ClassRegistry::init("PlayersSeason");

        $result = file_get_contents($file);
        $data = preg_split("/\r\n|\n|\r/", $result);
        $first = true;

        foreach($data as $line) {
            $values = str_getcsv($line, ",", '"');

            if($first) {
                $first = false;
                continue;
            }

            $character_type_name = trim($values[0]);

            // echo $character_type_name."<br>";

            $character_type = $this->CharacterType->findByCharacterType($character_type_name);
            if($character_type) {
                for($i = 1; $i < 36; $i++) {
                    if(!isset($values[$i]) || !$values[$i]) continue;

                    $pos = strpos($values[$i], " - ");
                    if($pos === false) $pos = strlen($values[$i]);

                    $name = substr($values[$i], 0, $pos);

                    $fname = Util::firstName($name);
                    $lname = Util::lastName($name);

                    $player = $this->Player->find("first", array("conditions" => array("fname" => $fname, "lname" => $lname)));

                    if($player) {
                        $season = $this->Season->findBySeasonNumber(36 - $i);
                        $season_id = $season['Season']['id'];

                        if($season) {
                            $player_season = $this->PlayersSeason->find("first", array("conditions" => array("player_id" => $player['Player']['id'], "season_id" => $season_id)));

                            // echo $fname." " .$lname."<br>";

                            if($player_season) {
                                if($player_season['PlayersSeason']['character_type_id'] != $character_type['CharacterType']['id']) {

                                    $player_season['PlayersSeason']['character_type_id'] = $character_type['CharacterType']['id'];

                                    echo $fname. " - ".$lname. " not matching ".$character_type_name."<br>";
                                    // $this->PlayersSeason->save($player_season);
                                }
                            }
                            else {
                                echo "could not find ".$fname. " - ".$lname." - ".$season_id."<br>";
                                exit;
                            }
                            
                        }
                        else {
                            echo "could not find season ".$i;
                            exit;
                        }
                    }
                    else {
                        echo $fname. " - ".$lname." - ".$character_type_name."<br>";
                    }
                }
            }        
        }
    }

	public function importCharacterTypes() {
		$this->autoRender = false;

		$file = WWW_ROOT."/files/Survivor Database - Data Entry - Character_Types_Grid.csv";

		$this->CharacterType = ClassRegistry::init("CharacterType");
		$this->CharacterType->query("delete from character_types");

		$result = file_get_contents($file);
    	$data = preg_split("/\r\n|\n|\r/", $result);
    	$first = true;

    	foreach($data as $line) {
        	$values = str_getcsv($line, ",", '"');

        	if($first) {
        		$first = false;
        		continue;
        	}

        	$values[1] = trim($values[1]);
        	$values[2] = trim($values[2]);

        	if($values[2] == "Male") $values[2] = "M";
        	else $values[2] = "F";

        	$this->CharacterType->create();
        	$this->CharacterType->save(array("order" => $values[0], "character_type" => $values[1], "sex" => $values[2], "created_date" => date("Y-m-d H:i:s")));

        }
	}

	public function importSeasons() {
		$this->autoRender = false;

		$file = WWW_ROOT."/files/Survivor Database - Data Entry - Seasons.csv";

		$this->Season = ClassRegistry::init("Season");
		$this->Season->query("delete from seasons");

		$result = file_get_contents($file);
    	$data = preg_split("/\r\n|\n|\r/", $result);
    	$first = true;

    	foreach($data as $line) {
        	$values = str_getcsv($line, ",", '"');

        	if($first) {
        		$first = false;
        		continue;
        	}

        	$season_number = $values[0];
        	$season_name = $values[1];
        	$premiere_date = $values[2];
        	$air_day = $values[3];
        	$finale_date = $values[4];

        	$starting_players = $values[6];
        	$starting_tribes = $values[7];
        	$tribe1 = $values[8];
        	$tribe2 = $values[9];
        	$tribe3 = $values[10];
        	$tribe4 = $values[11];
        	$swap_day1 = $values[12];
        	$swap_day2 = $values[13];
        	$merge_tribe = $values[14];
        	$merge_players = $values[15];
        	$ftc_count = $values[16];
        	$jury_count = $values[17];

        	$this->Season->create();
        	$this->Season->save(array("season_number" => $season_number, "season_name" => $season_name,
        		"premiere_date" => date("Y-m-d", strtotime($premiere_date)), "air_day" => $air_day, "finale_date" => date("Y-m-d", strtotime($finale_date)),
        		"starting_players" => $starting_players, "starting_tribes" => $starting_tribes, 
        		"tribe1" => $tribe1, "tribe2" => $tribe2, "tribe3" => $tribe3, "tribe4" => $tribe4,
        		"swap_day1" => $swap_day1, "swap_day2" => $swap_day2, "merge_tribe" => $merge_tribe,
        		"merge_players" => $merge_players, "ftc_count" => $ftc_count, "jury_count" => $jury_count));
        }
	}

	public function importPlayers() {
		$this->autoRender = false;

		$file = WWW_ROOT."/files/realitytv_players.csv";

		$this->PlayersSeason = ClassRegistry::init("PlayersSeason");
		$this->Player = ClassRegistry::init("Player");
		$this->Season = ClassRegistry::init("Season");
		$this->Player->query("delete from players");
		$this->PlayersSeason->query("delete from players_seasons");

		$result = file_get_contents($file);
    	$data = preg_split("/\r\n|\n|\r/", $result);
    	$first = true;

    	foreach($data as $line) {
        	$values = str_getcsv($line, ",", '"');

        	if($first) {
        		$first = false;
        		continue;
        	}

        	debug($values);
        	// exit;

        	$image_url = $values[0];

        	$start = strpos($values[1], " ");
        	$fname = trim(substr($values[1], 0, $start));
        	$lname = trim(substr($values[1], $start));
        	$wikia_url = $values[2];
        	$occupation = $values[3];
        	$age_show = $values[5];
        	$location = $values[6];
        	$season = trim(str_replace("Survivor: ", "", $values[7]));
        	$placement = $values[8];
        	$tribe_wins = $values[9];
        	$individual_wins = $values[10];
        	$day_voted_out = $values[11];
        	$votes_against = $values[12];
        	$sex = strtoupper($values[14]);

        	// hack to map seasons properly
        	if($season == "Panama") $season = "Panama: Exile Island";
        	else if($season == "Cagayan") $season = "Cagayan: BBB1";
        	else if($season == "Caramoan") $season = "Caramoan: Fans vs Favourites 2";
        	else if($season == "Micronesia") $season = "Micronesia: Fans vs Favourites";

        	$season_rec = $this->Season->findBySeasonName($season);

        	// found the season
        	if($season_rec) {
        		$player = $this->Player->findByWikiaUrl($wikia_url);
        		// cannot find the player, create it
        		if(!$player) {
        			$this->Player->create();
	        		$this->Player->save(array("fname" => $fname, "lname" => $lname, "occupation" => $occupation,
	        			"location" => $location, "sex" => $sex, "image_url" => $image_url, "wikia_url" => $wikia_url,
	        			"created_date" => date("Y-m-d H:i:s")));

	        		$player_id = $this->Player->id;
        		}
        		else {
        			$player_id = $player['Player']['id'];
        		}

        		$this->PlayersSeason->create();
        		$this->PlayersSeason->save(array("player_id" => $player_id, "season_id" => $season_rec['Season']['id'],
        			"age_show" => $age_show, "placement" => $placement, "day_voted_out" => $day_voted_out, 
        			"votes_against" => $votes_against, "tribe_wins" => $tribe_wins, "individual_wins" => $individual_wins,
        			"created_date" => date("Y-m-d H:i:s")));
        	}
        	else {
        		echo "could not find ".$season;
        		exit;
        	}
        }
	}
}
