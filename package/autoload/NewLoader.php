<?php

class NewLoader
{
    protected static $basedir = '';

    /**
     * An associative array where the key is a namespace prefix and the value
     * is an array of base directories for classes in that namespace.
     *
     * @var array
     */
    private static $prefixes = array();

    /**
     * Save loaded classes.
     *
     * @var array
     */
    private static $loader = array();

    /**
     * Register loader with SPL autoloader stack.
     *
     * @return void
     */
    public static function register($basedir = '')
    {
        self::$basedir = $basedir;

        // load the config file of base directorys to namespace prefixs
//        $mapping = file_exists(BASEDIR . '/Config/autoload.json') ? json_decode(file_get_contents(BASEDIR . '/Config/autoload.json'),
//            true) : '';
//
//        foreach ($mapping as $prefix => $path) {
//            self::addNamespace($prefix, $path);
//        }

        spl_autoload_register(array(new static(), 'loadClass'));
    }

    /**
     * Adds a base directory for a namespace prefix.
     *
     * @param string $prefix   The namespace prefix.
     * @param string $base_dir A base directory for class files in the
     *                         namespace.
     * @param bool   $prepend  If true, prepend the base directory to the stack
     *                         instead of appending it; this causes it to be searched first rather
     *                         than last.
     * @return void
     */
    public static function addNamespace($prefix, $base_dir, $prepend = false)
    {
        // normalize namespace prefix
        $prefix = trim($prefix, '\\') . '\\';

        // normalize the base directory with a trailing separator
        $base_dir = rtrim($base_dir, '/') . DIRECTORY_SEPARATOR;
        $base_dir = rtrim($base_dir, DIRECTORY_SEPARATOR) . '/';

        // initialize the namespace prefix array
        if (isset(self::$prefixes[$prefix]) === false) {
            self::$prefixes[$prefix] = array();
        }

        // retain the base directory for the namespace prefix
        if ($prepend) {
            array_unshift(self::$prefixes[$prefix], $base_dir);
        } else {
            array_push(self::$prefixes[$prefix], $base_dir);
        }
    }

    /**
     * Loads the class file for a given class name.
     *
     * @param string $class The fully-qualified class name.
     * @return mixed The mapped file name on success, or boolean false on
     *                      failure.
     */
    public function loadClass($class)
    {
        // the current namespace prefix
        $prefix = $class;

        // work backwards through the namespace names of the fully-qualified
        // class name to find a mapped file name
        while (false !== $pos = strrpos($prefix, '\\')) {

            // retain the trailing namespace separator in the prefix
            $prefix = substr($class, 0, $pos + 1);

            // the rest is the relative class name
            $relative_class = substr($class, $pos + 1);

            // try to load a mapped file for the prefix and relative class
            $mapped_file = $this->loadMappedFile($prefix, $relative_class);
            if ($mapped_file) {
                return $mapped_file;
            }

            // remove the trailing namespace separator for the next iteration
            // of strrpos()
            $prefix = rtrim($prefix, '\\');
        }

        // find in loacl path
        if ($this->load_from_file($class)) {
            return true;
        }

        // never found
        return false;
    }

    /**
     * Load the mapped file for a namespace prefix and relative class.
     *
     * @param string $prefix         The namespace prefix.
     * @param string $relative_class The relative class name.
     * @return mixed Boolean false if no mapped file can be loaded, or the
     *                               name of the mapped file that was loaded.
     */
    protected function loadMappedFile($prefix, $relative_class)
    {
        // are there any base directories for this namespace prefix?
        if (isset(self::$prefixes[$prefix]) === false) {
            return false;
        }

        // look through base directories for this namespace prefix
        foreach (self::$prefixes[$prefix] as $base_dir) {

            // replace the namespace prefix with the base directory,
            // replace namespace separators with directory separators
            // in the relative class name, append with .php
            $file = $base_dir
                . str_replace('\\', DIRECTORY_SEPARATOR, $relative_class)
                . '.php';

            // if the mapped file exists, require it
            if ($this->requireFile($file)) {
                // yes, we're done
                return $file;
            }
        }

        // never found it
        return false;
    }

    /**
     * If a file exists, require it from the file system.
     *
     * @param string $file The file to require.
     * @return bool True if the file exists, false if not.
     */
    protected function requireFile($file)
    {
        if (file_exists($file)) {
            require_once $file;
            return $file;
        }
        return false;
    }

    protected function save($class_name, $class)
    {
        self::$loader[$class_name] = $class;
    }

    protected function load_from_file($full_className)
    {
        // replace slashes in namespace
        $file_path = rtrim(self::$basedir,'/') . DIRECTORY_SEPARATOR . preg_replace("/\\\\/", "/", strtolower($full_className)) . '.php';

        if ($class = $this->requireFile($file_path)) {
            printf("Autoloading form {$file_path}." . PHP_EOL);
            $this->save($full_className, $class);
            return true;
        }
        return false;
    }

}