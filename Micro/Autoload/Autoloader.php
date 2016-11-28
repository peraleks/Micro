<?php

class Autoloader
{
    /**
     * Пространсва имён. 
     * 
     * @var array
     */
    private $nameSpaceArray = [];

    /**
     * Ключ - имя класса в глобальном пространстве имён
     * Значение - ссылка на $classFile
     * 
     * @var array
     */
    private $className = [];

    /**
     * Содержит полные пути файлов для $className
     *
     * @var array
     */
    private $classFile = [];

    /**
     * Содержит ссылку на элемент $nameSpaceArray,
     * в который была произведена последняя запись пути
     * для данного пространства имён
     * 
     * @var array
     */
    private $lastArray;

    /**
     * Корневая директория приложения
     * 
     * @var string
     */
    private $baseDir;

    /**
     * Счётчик подключеных файлов
     * 
     * @var integer $al
     */
    public $al;

    /**
     * Регистрация автозагрузчика,
     * определение корневой директории приложения
     * 
     * @var string $baseDir     Корневая директория приложения
     */
    public function __construct($baseDir)
    {
        $this->al = &$GLOBALS['MICRO_LOADER'];
        spl_autoload_register(array($this, 'microLoader'), false, true);
        $this->baseDir = $baseDir;
    }

    /**
     * Добавляет в массив $nameSpaceArray пространство имён и путь
     *
     * @param  string       $name    пространство имён
     * @param  string|array $path    путь
     * @return this
     */
    public function space($name, $path)
    {
        $nameParts = explode('\\', $name);

        $arrayLevel = &$this->nameSpaceArray;

        foreach ($nameParts as $NamePartsValue) {

            if (!array_key_exists($NamePartsValue, $arrayLevel)) {
                $arrayLevel[$NamePartsValue] = [];
            }
            $arrayLevel = &$arrayLevel[$NamePartsValue];
        }
        $arrayLevel[0] = [];

        if (is_array($path)) {
            foreach ($path as $PathValue) {

                $arrayLevel[0][] .= $PathValue;
            }
        }
        else {
            $arrayLevel[0][] .= $path;
        }

        $arrayLevel[1] = $name;

        $this->lastArray = &$arrayLevel;

        return $this;
    }

    /**
     * Подключает файлы
     *
     * При отсутствии ключа '2' в $arrayLevel, файл будет подключен
     * без предварительной проверки на наличие такового.
     * Иначе если ключ '2' есть - будет произведена проверка
     * на наличие файла для всех путей в $arrayLevel[0].
     * Вслучае неудачи, поиск будет произведён
     * по цепочке лоадеров, если таковые зарегистрированы
     *
     * @param array $arrayLevel содержит префикспространства имён
     * @param string $spaceEnd  подпространство + имя класса
     * @return void
     */
    private function includeFile(&$arrayLevel, $spaceEnd)
    {
        for ($i = 0; $i < count($arrayLevel[0]); ++$i) {

            $file = $this->baseDir.$arrayLevel[0][$i].$spaceEnd.'.php';

            if (($i == (count($arrayLevel[0]) - 1)) && !array_key_exists(2, $arrayLevel)) {
                include $file;
                ++$this->al;
                return;
            }
            else {
                if (file_exists($file)) {
                    include $file;
                    ++$this->al;
                    return;
                }
            }
        }
    }

    /**
     * Добавляет в массив $className имя глобального класса
     * и в $classFile путь к файлу
     *
     * @param  string $file      полный путь с расширением файла
     * @param  string $class     имя класса
     * @return this
     */
    public function globalClass($file, $class)
    {
        $this->classFile[$file] = $file;

        foreach ($class as $ClassValue) {

            $this->className[$ClassValue] = &$this->classFile[$file];
        }

        return $this;
    }

    /**
     * Устанавливает флаг расширенного поиска
     * Вслучае неудачного поиска в директориях,
     * которые определены в элементе массива [0]
     * данного пространства имён, поиск будет произведён
     * по цепочке лоадеров, если таковые зарегистрированы
     *
     * @return this
     */
    public function next()
    {
        $this->lastArray[2] = '';

        return $this;
    }

    /**
     * Лоадер
     *
     * @param  string $nameSpace    полное имя класса
     * @return void
     */
    private function microLoader($nameSpace)
    {
        $classNameParts = explode('\\', $nameSpace);

        if (count($classNameParts) == 1) {
            $this->includeGlobalClass($classNameParts[0]);
            return;
        }

        $arrayLevel = &$this->nameSpaceArray;

        foreach ($classNameParts as $classNamePartsValue) {

            if (!array_key_exists($classNamePartsValue, $arrayLevel)) {
                break;
            }
            else {
                $arrayLevel = &$arrayLevel[$classNamePartsValue];

                if (array_key_exists(0, $arrayLevel)) {

                    $spaceEnd
                    =
                    str_replace('\\', '/', str_replace($arrayLevel[1], '', $nameSpace));

                    $this->includeFile($arrayLevel, $spaceEnd);
                }
            }
        }
    }

    /**
     * Подключение глобального класса.
     * 
     * @param  string $className  имя класса без пространства имён  
     * @return void
     */
    private function includeGlobalClass($className)
    {
        if (array_key_exists($className, $this->className)) {
            ++$this->al;
            include $this->baseDir.$this->className[$className];
            return;
        }
        elseif (array_key_exists('', $this->nameSpaceArray)) {
            $this->includeFile($this->nameSpaceArray[''], $className);
        }
    }
}
