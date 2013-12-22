<?php
/**
 * RBaseApplication class file
 *
 * @author: Raysmond
 */
class RBaseApplication
{

    /**
     * The name of the application
     * @var string
     */
    public $name = "My Application";

    /**
     * The charset of the front-end view
     * @var string
     */
    public $charset = "UTF-8";

    /**
     * The time zone
     * @var string
     */
    public $timeZone = 'PRC';

    /**
     * The absolute base directory of the application class files
     * @var string
     */
    private $_baseDir = '';

    /**
     * The base path of the application class files. $_appPath is generated by program.
     * For example: The application 'index.php' locates at 'http://localhost/example', and the application class
     * files locate at 'http://localhost/example/app', then the $_appPath is '/example/app'
     * @var string
     */
    private $_appPath = '';

    /**
     * The base path of the application 'index.php' file
     * for example: /example  (the / at the beginning stands for the web server host like 'localhost')
     * @var string
     */
    private $_basePath = '';

    /**
     * The base URL of the application
     * @var
     */
    private $_baseUrl;

    /**
     * The database config array
     * for example:
     * 'db' => array(
     *   'host' => '127.0.0.1',
     *   'user' => 'fdugroup',
     *   'password' => 'fdugroup',
     *   'db_name' => 'fdugroup',
     *   'table_prefix' => 'group_',
     *   'charset' => 'utf8',
     *   ),
     * @var array
     */
    private $_db;

    /**
     * The whole config array
     * @var array
     */
    private $_config = array();

    /**
     * Cache config array
     * for example:
     * 'cache' => array(
     *   'cache_dir' => '/cache',
     *   'cache_prefix' => "cache_",
     *   'cache_time' => 1800, //seconds
     *   )
     * @var array
     */
    private $_cache = array();

    /**
     * Exception handling action
     * @var string controller action string representation. like : 'site/exception'
     */
    private $_exceptionAction = "";

    private $debug = true;

    public function __construct($defaultConfig = null, $config = null)
    {
        $this->init($config);

        $_config = new RConfig();
        $_config->setDefault($defaultConfig);
        $_config->load($config);
    }

    /**
     * Initialize the application with configurations
     * @param null $config
     */
    public function init($config = null)
    {
        $this->setConfig($config);
        $config = $this->getConfig();

        if (isset($config['name']))
            $this->name = $config['name'];
        if (isset($config['basePath']))
            $this->_basePath = $config['basePath'];
        if (isset($config['baseUrl']))
            $this->_baseUrl = $config['baseUrl'];
        if (isset($config['charset']))
            $this->charset = $config['charset'];
        if (isset($config['db']))
            $this->_db = $config['db'];
        if (isset($config['timeZone']))
            $this->timeZone = $config['timeZone'];
        if (isset($config['cache'])){
            $this->_cache = $config['cache'];
        }
        if(isset($config['baseDir'])){
            $this->_baseDir = $config['baseDir'];
        }
        if(isset($config['exceptionAction'])){
            $this->setExceptionAction($config['exceptionAction']);
        }
        if(isset($config['debug'])){
            $this->debug = $config['debug'];
        }

        date_default_timezone_set($this->timeZone);

        Rays::import("system.base.RException");
    }

    /**
     * Run the application
     */
    public function run()
    {

    }

    /**
     * End the application
     * @param int $status
     */
    public function end($status = 0)
    {
        exit($status);
    }

    /**
     * Get the base URL of the application site
     * @return string
     */
    public function getBaseUrl()
    {
        if (!isset($this->_baseUrl)) {
            $this->_baseUrl = 'http://' . $_SERVER['SERVER_NAME'].(isset($this->_basePath)?$this->_basePath:"");
        }
        return $this->_baseUrl;
    }

    /**
     * Set the base URL of the application site
     * @param $value
     */
    public function setBaseUrl($value)
    {
        $this->_baseUrl =  $value;
    }

    /**
     * Get application base path
     * @return string
     */
    public function getAppPath()
    {
        if($this->_appPath===''){
            $pos = strpos($this->_baseDir,$this->_basePath);
            $this->_appPath = substr($this->_baseDir,$pos);
        }
        return $this->_appPath;
    }

    /**
     * Get base path of the application. For example: /FUDGroup
     * @return string
     */
    public function getBasePath()
    {
        return $this->_basePath;
    }

    /**
     * Set the base path of the web application
     * @param $path
     */
    public function setBasePath($path)
    {
        $this->_basePath = $path;
    }

    /**
     * Get the base directory of the application
     * @return string
     */
    public function getBaseDir()
    {
        return $this->_baseDir;
    }

    /**
     * Get the name of the application
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set the name of the application
     * @param $_name
     */
    public function setName($_name)
    {
        $this->name = $_name;
    }

    /**
     * Get the database configuration array.
     * @return array
     */
    public function getDbConfig()
    {
        return $this->_db;
    }

    /**
     * Set the database configuration
     *
     * @param array $db
     * For example:
     * <code>
     * array(
     *   'host' => '127.0.0.1',
     *   'user' => 'fdugroup',
     *   'password' => 'fdugroup',
     *   'db_name' => 'fdugroup',
     *   'table_prefix' => '',
     *   'charset' => 'utf8',
     *   ),
     * </code>
     */
    public function setDbConfig($db)
    {
        $this->_db = $db;
    }

    /**
     * Get the whole configuration array of the application
     * @return array
     */
    public function getConfig()
    {
        return $this->_config;
    }

    /**
     * Set the configuration of the application
     * @param $config
     */
    public function setConfig($config)
    {
        if (is_string($config))
            $config = require($config);
        $this->_config = $config;
    }

    /**
     * Set the default time zone of the application
     * @param $timeZone
     */
    public function setTimeZone($timeZone)
    {
        $this->timeZone = $timeZone;
    }

    /**
     * Get the time zone of the application
     * @return string
     */
    public function getTimeZone()
    {
        return $this->timeZone;
    }

    /**
     * Get the database table prefix from database configuration
     * @return string
     */
    public function getDBPrefix()
    {
        return isset($this->_db['table_prefix'])?$this->_db['table_prefix']:"";
    }

    /**
     * Get cache configuration array
     * @return array
     */
    public function getCacheConfig()
    {
        return $this->_cache;
    }

    /**
     * Set the cache configuration for the application
     * @param $config
     */
    public function setCacheConfig($config){
        $this->_cache = $config;
    }

    /**
     * Get Exception action
     * like: "site/exception", the "site" means the controller ID and the "exception" means the action ID in SiteController
     * @return string
     */
    public function getExceptionAction()
    {
        return $this->_exceptionAction;
    }

    /**
     * Set the Exception action
     * @param string $action
     */
    public function setExceptionAction($action="")
    {
        $this->_exceptionAction = $action;
        RExceptionHandler::setExceptionAction($action);
    }

    /**
     * Whether the application is in debug mode.
     * @return bool
     */
    public function isDebug()
    {
        return $this->debug === true? true: false;
    }
}