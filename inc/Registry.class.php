<?php

/**
 * Registry class
 *
 * @author Kevin van der Burgt <info@kevinvdburgt.com>
 * @url https://github.com/kevinvdburgt
 */
final class Registry
{
    /**
     * The singleton instance of this object
     * @var Registry
     */
    private static $_instance;

    /**
     * The storage container
     * @var array
     */
    private static $_storage;

    /**
     * Prevent creating a new instance
     */
    private function __construct() { }

    /**
     * Get a existing instance of the registry object
     * @return Registry A new or existsing instance of the Registry
     */
    public static function getInstance()
    {
        if(!self::$_instance instanceof self)
            self::$_instance = new Registry;

        return self::$_instance;
    }

    /**
     * Puts a object into the storage container
     * @param string $index
     * @param object $value
     */
    public function __set($index, $value)
    {
        self::$_storage[$index] = $value;
    }

    /**
     * Gets a object from the storage container
     * @param  string $index
     * @return object|null
     */
    public function __get($index)
    {
        if(isset(self::$_storage[$index]))
            return self::$_storage[$index];

        return null;
    }
}