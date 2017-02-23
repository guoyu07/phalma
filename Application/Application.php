<?php
namespace Application;

use ManaPHP\Di\FactoryDefault;
use Phalcon\Loader;

/**
 * Class Application\Application
 *
 * @package Application
 *
 * @property \Application\Configure     $configure
 * @property \ManaPHP\DebuggerInterface $debugger
 */
class Application extends \Phalcon\Mvc\Application
{
    /**
     * @var string
     */
    public $appPath = __DIR__;

    /** @noinspection MagicMethodsValidityInspection */
    /**
     * Application constructor.
     *
     * @param \Phalcon\Loader      $loader
     * @param \Phalcon\DiInterface $dependencyInjector
     */
    public function __construct($loader = null, $dependencyInjector = null)
    {
        if (!$loader) {
            $loader = new Loader();
        }
        $loader->registerNamespaces([basename($this->appPath) => $this->appPath, 'ManaPHP' => ROOT_PATH . '/ManaPHP'])->register();

        $this->_dependencyInjector = $dependencyInjector ?: new FactoryDefault();
        $this->_dependencyInjector->setShared('loader', $loader);
        $this->_dependencyInjector->setShared('application', $this);
    }

    /**
     * @return array
     */
    public function getModules()
    {
        return array_keys($this->configure->modules);
    }

    public function main()
    {
        $this->registerServices();

        $this->debugger->start();

        echo $this->handle()->getContent();
    }

    public function registerServices()
    {
        $rootNamespace = basename($this->appPath);

        $this->_dependencyInjector->setShared('configure', "$rootNamespace\\Configure");

        $configure = $this->configure;

        if (isset($configure->redis)) {
            $c = (array)$configure->redis;
            foreach (isset($c['host']) ? ['redis' => $c] : $c as $service => $config) {
                if (!is_array($config)) {
                    $config = (array)$config;
                }

                $c += ['port' => 6379, 'timeout' => 0.0];
                $this->_dependencyInjector->setShared($service, function () use ($config) {
                    $redis = new \Redis();
                    $redis->connect($config['host'], $config['port'], $config['timeout']);

                    return $redis;
                });
            }
        }

        if (isset($configure->db)) {
            $c = (array)$configure->db;
            foreach (isset($c['host']) ? ['db' => $c] : $c as $service => $config) {
                if (!is_array($config)) {
                    $config = (array)$config;
                }

                $adapter = isset($config['adapter']) ? $config['adapter'] : 'Phalcon\Db\Adapter\Pdo\Mysql';
                unset($config['adapter']);
                $this->_dependencyInjector->setShared($service, function () use ($adapter, $config) {
                    return new $adapter($config);
                });
            }
        }

        foreach (isset($configure->modules) ? $configure->modules : ['Home' => '/', 'Api' => '/api'] as $module => $path) {
            $className = "$rootNamespace\\$module\\RouteGroup";
            if (!class_exists($className)) {
                $className = 'ManaPHP\Mvc\Router\Group';
            }

            $this->registerModules([$module => ['className' => "$rootNamespace\\$module\\Module", 'path' => $this->appPath . "/$module/Module.php"]], true);
            $this->router->mount(new $className($path));
        }
    }
}
