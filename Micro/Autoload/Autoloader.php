<?php

class Autoloader
{
    /**
     * @var array Пространсва имён.
     */
    private $nameSpace = [];

    /**
     * Ключ - имя класса в глобальном пространстве имён
     * Значение - ссылка на $classFile[]
     *
     * @var array
     */
    private $className = [];

    /**
     * @var array Содержит полные пути файлов для $className
     */
    private $classFile = [];

    /**
     * Содержит ссылку на элемент $nameSpace,
     * в который была произведена последняя запись пути
     * для данного пространства имён
     *
     * @var array
     */
    private $lastArray;

    /**
     * @var string Корневая директория приложения
     */
    private $baseDir;

    /**
     * @var integer Счётчик подключеных файлов
     */
    public $counter;

    /**
     * Регистрация автозагрузчика,
     * определение корневой директории приложения
     *
     * @var string $baseDir     Корневая директория приложения
     */
    public function __construct($baseDir)
    {
        $this->counter = &$GLOBALS['MICRO_LOADER'];
        spl_autoload_register(array($this, 'microLoader'), false, true);
        $this->baseDir = $baseDir;
    }

    /**
     * Регистрирует пространство имён PSR-4
     *
     * Можно связать глобальное пространство с определённой папкой,
     * если $name = '', $path = путь к папке (имена файлов
     * должны совпадать с именами классов)
     *
     * @param  string       $name    пространство имён
     * @param  string|array $path    путь
     * @return this
     */
    public function psr4($name, $path)
    {
        $arrayLevel = &$this->nameSpace;
        $nameParts = explode('\\', $name);

        foreach ($nameParts as $namePartsValue) {
            $arrayLevel = &$arrayLevel[$namePartsValue];
        }
        $arrayLevel[0] = [];

        if (is_array($path)) {
            foreach ($path as $pathValue) {
                $arrayLevel[0][] .= $pathValue;
            }
        } else {
            $arrayLevel[0][] .= $path;
        }
        $arrayLevel[1] = strlen($name);
        $this->lastArray = &$arrayLevel;

        return $this;
    }

    /**
     * Подключает файлы
     *
     * При отсутствии флага расширенного поиска ($arrayLevel[2]),
     * файл будет подключен сразу. При наличии флага - будет произведена
     * проверка на существование файла (нужна для цепочки лоадеров).
     *
     * @param array $arrayLevel содержит префикспространства имён
     * @param string $spaceEnd  подпространство + имя класса
     * @return void
     */
    private function includeFile(&$arrayLevel, $spaceEnd)
    {
        for ($i = 0; $i < count($arrayLevel[0]); ++$i) {
            $file = $this->baseDir.$arrayLevel[0][$i].$spaceEnd.'.php';

            if (($i == (count($arrayLevel[0]) - 1)) && !isset($arrayLevel[2])) {
                include $file;
                ++$this->counter;
                return;

            } elseif (file_exists($file)) {
                include $file;
                ++$this->counter;
                return;
            }
        }
    }

    /**
     * Регистрирует классы в глобальном пространстве имён.
     * В одном файле может быть несколько классов
     *
     * @param  string $file     полный путь с расширением файла
     * @param  array $class     имена классов
     * @return this
     */
    public function globalClass($file, array $class)
    {
        $this->classFile[$file] = $file;
        foreach ($class as $classValue) {
            $this->className[$classValue] = &$this->classFile[$file];
        }
        return $this;
    }

    /**
     * Устанавливает флаг расширенного поиска
     *
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
        $arrayLevel = &$this->nameSpace;

        foreach ($classNameParts as $classNamePartsValue) {
            if (!isset($arrayLevel[$classNamePartsValue])) break;

            $arrayLevel = &$arrayLevel[$classNamePartsValue];
            if (isset($arrayLevel[0])) {
                $spaceEnd = str_replace('\\', '/', substr($nameSpace, $arrayLevel[1]));
                $this->includeFile($arrayLevel, $spaceEnd);
            }
        }
    }

    /**
     * Подключает глобальный класс.
     *
     * @param  string $class  имя класса без пространства имён
     * @return void
     */
    private function includeGlobalClass($class)
    {
        if (isset($this->className[$class])) {
            include $this->baseDir.$this->className[$class];
            ++$this->counter;
            return;
        }
        elseif (isset($this->nameSpace[''])) {
            $this->includeFile($this->nameSpace[''], '/'.$class);
        }
    }
}
