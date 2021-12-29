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
class UsersController extends AppController {

/**
 * This controller does not use a model
 *
 * @var array
 */
	public $uses = array();

    public function beforeFilter() {
        parent::beforeFilter();
        
        $this->Auth->allow('login', 'logout');
    }

    public function login() {
        Security::setHash('md5');

        if ($this->request->is('post')) {
            $this->autoRender = false;
            if ($this->Auth->login()) {
                $retval = array("result" => _SUCCESS);
            }
            else {
                $retval = array("result" => _FAILURE, "message" => "Invalid email and password combination.");
            }

            $this->response->body(json_encode($retval));
            $this->response->type('json');  
        }
        else {
            $this->layout = "ajax";
        }
    }

    public function logout() {
        return $this->redirect($this->Auth->logout());
    }

    public function signUp() {

    }
}
