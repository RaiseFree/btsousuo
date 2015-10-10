<?php

class IndexController extends \Yaf\Controller_Abstract {

    public function init() {
        //$this->setViewPath(APPLICATION_PATH.'/application/views/'.$this->_request->module);
    }

    /* default action */
    public function indexAction() {
        //$router = \Yaf\Dispatcher::getInstance()->getRouter();
        // $search = new Search();
        // $search->setQ('大圣归来');
        // $res = $search->query();
        // $id  = $res['matches'][0]['id'];

        // $conn = new MyPDO();
        // $films = $conn->table('film')->select('*')->where('id = ?', array($id))->execute();

        // $config = \Yaf\Application::app()->getConfig();
        //$this->_view->films = $films;
    }
}
