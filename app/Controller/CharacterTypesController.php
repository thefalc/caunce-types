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
class CharacterTypesController extends AppController {

/**
 * This controller does not use a model
 *
 * @var array
 */
	public $uses = array();

	public function beforeFilter() {
        $this->Auth->allow('home');
    }

	public function home() {
		$this->set("title_for_layout", "The Caunce Character Types");

		if($this->passedArgs) {
			$this->Session->write("passed_args", $this->passedArgs);
		}
		else if($this->Session->check("passed_args")) {
			$this->passedArgs = $this->Session->read("passed_args");
		}

		$this->CharacterType = ClassRegistry::init("CharacterType");
		$male_character_types =  $this->CharacterType->find("all", array("conditions" => array("sex" => "m"), "order" => "order"));
		$female_character_types =  $this->CharacterType->find("all", array("conditions" => array("sex" => "f"), "order" => "order"));

		$this->set("female_character_types", $female_character_types);
		$this->set("male_character_types", $male_character_types);
	}
}
