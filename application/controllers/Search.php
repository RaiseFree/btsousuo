<?php

class SearchController extends \Yaf\Controller_Abstract {
   /* default action */
   public function indexAction() {
       $key     = $this->getRequest()->get('kw');
       $orderby = $this->getRequest()->get('od');
       $page    = $this->getRequest()->get('page', 1);

       // $router = \Yaf\Dispatcher::getInstance()->getRouter();
       // $config = \Yaf\Application::app()->getConfig();
       $search = new Search();
       $search->setQ($key);
       $search->setPage($page);
       if (!empty($orderby)) {
           $search->setSortby($orderby);
       }
       $res    = $search->query();
       $pages  = 1;
       $films  = null;
       if ($res) {
           $pages = empty($res['pages']) ? 1 : $res['pages'];
           foreach ($res['matches'] as $match) {
               $ids[] = $match['id'];
           }
           $conn = new MyPDO();
           $films = $conn->table('film')->select('*')->where('id in ('.implode(',', $ids).')')->orderBy('order by find_in_set (id, \''.implode(',', $ids).'\')')->execute();
       }


       $this->_view->assign('films', $films);
       $this->_view->assign('kw', $key);
       $this->_view->assign('orderby', $orderby);
       $this->_view->assign('page', $page);
       $this->_view->assign('pages', $pages);
   }
}
