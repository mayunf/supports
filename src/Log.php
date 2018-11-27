<?php
namespace Mayunfeng\Supports;

use Monolog\Formatter\LineFormatter;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Psr\Log\LoggerInterface;
/**
 * @method static void emergency($message, array $context = array())
 * @method static void alert($message, array $context = array())
 * @method static void critical($message, array $context = array())
 * @method static void error($message, array $context = array())
 * @method static void warning($message, array $context = array())
 * @method static void notice($message, array $context = array())
 * @method static void info($message, array $context = array())
 * @method static void debug($message, array $context = array())
 * @method static void log($message, array $context = array())
 */
class Log
{
    /**
     * Logger instance.
     *
     * @var LoggerInterface
     */
    protected static $logger;
    /**
     * Forward call.
     * @param string $method
     * @param array  $args
     * @throws \Exception
     * @return mixed
     */
    public static function __callStatic($method, $args)
    {
        return forward_static_call_array([self::getLogger(), $method], $args);
    }
    /**
     * Forward call.
     * @param string $method
     * @param array  $args
     * @throws \Exception
     * @return mixed
     */
    public function __call($method, $args)
    {
        return call_user_func_array([self::getLogger(), $method], $args);
    }
    /**
     * Return the logger instance.
     *
     * @throws \Exception
     *
     * @return LoggerInterface
     */
    public static function getLogger()
    {
        return self::$logger ?: self::$logger = self::createDefaultLogger();
    }
    /**
     * Set logger.
     *
     * @param LoggerInterface $logger
     */
    public static function setLogger(LoggerInterface $logger)
    {
        self::$logger = $logger;
    }
    /**
     * Tests if logger exists.
     *
     * @author yansongda <me@yansongda.cn>
     *
     * @return bool
     */
    public static function hasLogger()
    {
        return self::$logger ? true : false;
    }
    /**
     * Make a default log instance.
     *
     * @param string $file
     * @param string $identify
     * @param int    $level
     * @param string $type
     * @param int    $max_files
     *
     * @return \Monolog\Logger
     */
    public static function createDefaultLogger($file = null, $identify = null, $level = Logger::DEBUG, $type = 'daily', $max_files = 30)
    {
        $file = is_null($file) ? sys_get_temp_dir().'/logs/mayunfeng.supports.log' : $file;
        $handler = $type === 'single' ? new StreamHandler($file, $level) : new RotatingFileHandler($file, $max_files, $level);
        $handler->setFormatter(
            new LineFormatter("%datetime% %level_name% %message% %context% %extra%\n", null, false, true)
        );
        $logger = new Logger(is_null($identify) ? 'mayunfeng.supports' : $identify);
        $logger->pushHandler($handler);
        return $logger;
    }
}
