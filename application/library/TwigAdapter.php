<?php
use \Yaf\View_Interface;
use \Yaf\Registry;

// Twig 自带了自动加载器，所以需要开启yaf.use_spl_autoload
require APPLICATION_PATH . '/application/library/Twig/Autoloader.php';
Twig_Autoloader::register();

/**
 * Yaf Twig 视图引擎
 **/
class TwigAdapter implements View_Interface{
    /**
     * 模板位置 
     *
     * @var string
     **/
    protected $_path;

    /**
     * 模板变量集合
     *
     * @var array
     **/
    protected $_tplVars;

    /**
     * undocumented function
     *
     * @return void
     * @author hwz
     **/
    public function __construct () {
        $this->loader = new Twig_Loader_Filesystem(APPLICATION_PATH.'/application/views');
        $this->twig = new Twig_Environment($this->loader, array(
            'cache' => Registry::get('config')->twig->cache_dir,
            'debug' => Registry::get('config')->twig->debug,
        ));
        $this->_tplVars = array();
        $this->registerFunc();
    }

    /**
     * 添加一个模块变量  
     *
     * @return void
     * @author hwz
     **/
    public function assign($spec,$value = null) {
        $this->_tplVars[$spec] = $value;
    }

    /**
     * 渲染视图，直接输出
     *
     * @return void
     * @author hwz
     **/
    public function display($name,$value=null) {
        echo $this->render($name,$value);
    }

    /**
     * 默认调用函数
     *
     * @return string output
     * @author hwz
     **/
    public function render($name,$value=array()) {
        if (!empty($value)) {
            $this->_tplVars = array_merge($value,$this->_tplVars);
        }
        return $this->twig->render($name,$this->_tplVars);
    }

    /**
     * undocumented function
     *
     * @return void
     * @author hwz
     **/
    public function setScriptPath($path) {
        if (is_readable($path)) {
            $this->_path = $path;
            $this->loader->addPath($path);
            return ;
        }
        throw new Exception('script path not readable!');
    }

    /**
     * undocumented function
     *
     * @return void
     * @author hwz
     **/
    public function getScriptPath() {
        return $this->_path;
    }


    private function registerFunc() {
        $this->twig->addExtension(new \Twig_Extension_Debug());
    }
}

// Twig 扩展
class WCT_Twig_Extension extends \Twig_Extension
{
    public function getName(){
        return 'piecepage';
    }

    public function getFunctions(){
        $addFuncs[] = new Twig_SimpleFunction('site_url','site_url');
        return $addFuncs;
    }
}

function site_url($path = '/') {
    return $path;
}
//End of file 
