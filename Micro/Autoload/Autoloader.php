<?php

class Autoloader
{
    /**
     * Карта пространсв имён
     * 
     * @var array
     */
    private $spaceMap = [];

    /**
     * Карта отдельных классов в глобальном пространстве имён
     * 
     * @var array
     */
    private $classMap = [];

    /**
     * Содержит ссылку на карту ($lastMap | $spaceMap),
     * в которую производилась запись в последний раз
     * 
     * @var array
     */
    private $lastMap;

    /**
     * Первый элемент пространства имён, разбитого по '/',
     * или имя класса в глобальном пространстве имён
     * 
     * @var string
     */
    private $space;

    /**
     * Корневая директория приложения
     * 
     * @var string
     */
    private $baseDir;

    public $al;

    /**
     * Регистрирует функцию автозагрузчика и
     * и корневую директорию
     */
    public function __construct($baseDir)
    {
        $this->al = &$GLOBALS['MICRO_LOADER'];
        spl_autoload_register(array($this, 'microLoader'), false, true);
        $this->baseDir = $baseDir;
    }

    /**
     * Добавляет в массив $spaceMap пространство имён и путь
     * 
     * @param string $name  пространство имён
     * @param string $path  путь   
     * @return object($this)
     */
    public function space($name, $path)
    {
        $this->lastMap = &$this->spaceMap;
        $this->spaceMap['lastName'] = $name;
        $this->arrayExists($name);
        if (is_array($path)) {
            foreach ($path as $value) {
                $this->spaceMap[$name]['path'][] .= $value;
            }
        } else {
            $this->spaceMap[$name]['path'][] .= $path;
        }

        return $this;
    }

    /**
     * Добавляет в массив $classMap имя глобального класса
     * и  путь к содержащей папке
     * 
     * @param string $path  путь содержащий имя класса(файла без расширения)
     * @return object($this)
     */
    public function globalClass($path)
    {
        $this->lastMap = &$this->classMap;
        if (is_array($path)) {
            foreach ($path as $value) {
                $this->classArray($value);
            }
        } else {
            $this->classArray($path);
        }

        return $this;
    }

    /**
     * Добавляет в массив $classMap имя глобального класса
     * и  путь к содержащей папке
     * Вынесен повторяющийся код из globalClass()
     * 
     * @param string $path  путь содержащий имя класса(файла без расширения) 
     * @return void
     */
    private function classArray($path) {
        $arr = explode('/', $path);
        $name = array_pop($arr);
        $this->classMap['lastName'] = $name;
        $this->arrayExists($name);
        $this->classMap[$name]['path'][] .= implode('/', $arr);
    }

    /**
     * Устанавливает флаг ограничения поиска
     * Поиск будет производится только в тех 
     * директориях, которые содежатся в подмассиве 'path'
     * массива данного пространства имён
     * 
     * @return object($this)
     */
    public function strict()
    {
        $this->lastMap[$this->lastMap['lastName']]['strict'] = true;

        return $this;
    }

    /**
     * Инициализирует подмассивы пространств имен | классов
     * и подмассивы 'path' в них
     *   
     * @param string $name  пространство имён или имя класса
     * @return void
     */
    private function arrayExists($name)
    {
        array_key_exists($name, $this->lastMap)
        ?:
        $this->lastMap[$name] = [];

        array_key_exists('path', $this->lastMap[$name])
        ?:
        $this->lastMap[$name]['path'] = [];
    } 

    /**
     * Зарегистрированная функция автозагрузчика
     * Реализует логику автозагрузки
     * 
     * @param string $className пространство имён
     * @return void
     */
    private function microLoader($className)
    {
        $explName = explode('\\', $className);
        $this->space = array_shift($explName);
        if (empty($explName)) {
            if (array_key_exists($this->space, $this->classMap)) {
                $this->includeFile($this->space, $this->classMap, true);
            } else {
                $this->globalName();
            }
        } elseif (array_key_exists($this->space, $this->spaceMap)) {
            $this->includeFile(implode('/', $explName), $this->spaceMap);
        }
    }

    /**
     * Подключает файлы
     * Если присутствует флаг 'strict', проверка на наличие файла будет произведена
     * по всем записям, присутствующим в массиве 'path', кроме последней,
     * a последняя запись будет использована для подключения файла
     * без проверки на его наличие
     * При отсутствии флага 'strict', будет произведена проверка 
     * на наличие файла для всех записей в массиве 'path'
     * и файл не будет подключен если не был найден
     *  
     * @param string $class         имя класса
     * @param array $map            $lastMap | $spaceMap            
     * @param bool $global          флаг глобального пространства
     * @return void
     */
    private function includeFile($class, $map, $global = false)
    {
        $count = count($map[$this->space]['path']) - 1;
        for ($i = 0; $i <= $count; ++$i) {
            $path = $this->baseDir.$map[$this->space]['path'][$i].'/'.$class.'.php';
            if (($i == $count) && array_key_exists('strict', $map[$this->space])) {
                ++$this->al;
                include $path;
                return;
            }
            else {
                ++$this->al;
                if (file_exists($path)) {
                    include $path;
                    return;
                }
            }
        }
        !$global ?: $this->globalName();
    }

    /**
     * Подключение глобального класса из папки глобального пространства имён,
     * если она определена в $spaceMap по ключу ''
     *   
     * @return void
     */
    private function globalName()
    {
        if (array_key_exists('', $this->spaceMap)) {
            $class = $this->space;
            $this->space = '';
            $this->includeFile($class, $this->spaceMap);
        }
    }
}
