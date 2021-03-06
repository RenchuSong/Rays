<?php

define('SYSTEM_PATH', dirname(__FILE__));

define('SYSTEM_CORE_PATH', SYSTEM_PATH . '/base');

define('HELPER_PATH', SYSTEM_PATH . '/helpers');

/**
 * Base framework bootstrap file. It's implemented as a singleton to provide some common functionality
 * for the whole web application, for example, class naming, classes autoload, importing classes,
 * creating and running the application and etc.
 *
 * @author: Raysmond
 */
class RaysFramework
{
    /**
     * The web application
     * @var
     */
    public static $app;

    /**
     * The whole system and application log helper
     * @var
     */
    public static $logger;

    /**
     * Application start time
     * @var
     */
    public static $startTime;

    /**
     * Class to file map. Map key is the class name and value is the full class file path
     * @var array
     */
    public static $classMap = array();

    /**
     * Auto-import files
     * @var array
     */
    public static $imports = array();

    public static $_includePaths = array(
        SYSTEM_PATH,
        SYSTEM_CORE_PATH,
        HELPER_PATH,
    );

    /**
     * Get the application object
     * @return RApplicationBase
     */
    public static function app()
    {
        return self::$app;
    }

    /**
     * Create a new web application
     * @param $config
     * @return RApplicationBase
     */
    public static function newApp($config)
    {
        self::$startTime = microtime(true);
        self::$logger = new RLog();

        $defaultConfig = SYSTEM_PATH."/AppConfig.php";
        return new RApplication($defaultConfig, $config);
    }

    /**
     * Set the current application
     * @param $_app
     */
    public static function setApp($_app)
    {
        if (self::$app === null && $_app != null) {
            self::$app = $_app;
            self::initPath();
        } else {
            die("Application not found!");
        }
    }

    /**
     * Initialize the application files include path
     */
    public static function initPath()
    {
        self::$_includePaths[] = self::app()->getControllerPath();
        self::$_includePaths[] = self::app()->getModelPath();
        self::$_includePaths[] = self::app()->getModulePath();
    }

    /**
     * Import a module
     * @param $moduleId
     * @throws RException
     */
    public static function importModule($moduleId)
    {
        if (!class_exists(RModule::moduleClass($moduleId))) {
            $path = self::app()->getModulePath() . "/$moduleId/$moduleId" . RModule::MODULE_FILE_EXTENSION;
            if (is_file($path) && file_exists($path)) {
                require($path);
            } else
                throw new RException("Module class (" . $moduleId . "_module) file ($path) not exist.");
        }
    }

    /**
     * Import files
     * @param array $imports
     */
    public static function autoImports($imports = array())
    {
        foreach ($imports as $import) {
            self::import($import);
        }
    }

    /**
     * Import custom PHP files
     *
     * @param $files files like: application.extension.file_name or application.extension.ext1.*
     * 1. application.extension.file_name locates to extension/file_name.php
     * 2. application.extension.ext1.* will import
     * - application. locates the application base directory (default)
     * - system. locates the system(framework directory)
     */
    public static function import($files)
    {
        $files = str_replace('.', '/', $files);
        if ($files) {
            $arr = explode('/', $files);
            $baseDir = ($arr[0] == "system") ? SYSTEM_PATH : self::app()->getBaseDir();
            $files = $arr[0] === "system" ? substr($files, 7) : $files;
            $files = $arr[0] === "application" ? substr($files, 12) : $files;

            $fileName = end($arr);
            if ($fileName !== '*') {
                if (!isset(self::$imports[$files])) {
                    $path = $baseDir . '/' . $files . '.php';
                    if (is_file($path)) {
                        self::$imports[$files] = $path;
                        require($path);
                    }
                }
            } else {
                $files = str_replace('/*', '', $files);
                $dir = $baseDir . '/' . $files;
                if (is_dir($dir)) {
                    $dp = dir($dir);
                    while ($file = $dp->read()) {
                        if ($file != "." && $file != ".." && !is_dir($file)) {
                            if (end(explode('.', $file)) === 'php') {
                                $file_key = $files . '/' . $file;
                                $path = $dir . '/' . $file;
                                if (!isset(self::$imports[$file_key])) {
                                    self::$imports[$file_key] = $path;
                                    require($path);
                                }
                            }
                        }
                    }
                    $dp->close();
                }
            }
        }
    }

    /**
     * Class autoload loader
     * This method is invoked within an __antoload() magic method
     * @param string $className class name
     */
    public static function autoload($className)
    {
        if (isset(self::$classMap[$className]))
            require(self::$classMap[$className]);
        else {
            $className = end(explode("\\", $className));
            foreach (self::$_includePaths as $path) {
                $classFile = $path . DIRECTORY_SEPARATOR . $className . '.php';
                if (is_file($classFile)) {
                    require($classFile);
                    break;
                }
            }
        }
    }

    /**
     * Generate a new log
     * @param $message the log message
     * @param string $level the level of the message
     * @param string $category
     */
    public static function log($message, $level = RLog::LEVEL_INFO, $category = 'system')
    {
        self::$logger->log($message, $level, $category);
    }

    /**
     * Get the log object
     * @return RLog
     */
    public static function logger()
    {
        return self::$logger;
    }
}

spl_autoload_register(array('RaysFramework', 'autoload'));

set_exception_handler(array("RExceptionHandler", "handleException"));

require(SYSTEM_PATH."/base/RFunctions.php");

header('Content-Type: text/html; charset=UTF-8');