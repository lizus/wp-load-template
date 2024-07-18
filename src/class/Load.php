<?php

namespace LizusWPLoad;

/**
 * 仅在wp主题中可以使用
 */

class Load
{
    protected $path_root = 'template/'; //根目录,相对主题文件夹

    private static $_instance = [];
    private function __construct()
    {
    }
    public static function getInstance()
    {
        $name = \get_called_class();
        if (!isset(self::$_instance[$name])) {
            self::$_instance[$name] = new $name();
        }
        return self::$_instance[$name];
    }
    private function __clone()
    {
        trigger_error('Clone is not allow!', E_USER_ERROR);
    }
    /**
     * 根据模板路径和模板名寻找模板文件，如果找到则载入并返回true，否则返回false
     */
    protected function load_template($path, $name, $data = []): bool
    {
        if (!empty($data) && is_array($data)) self::setData($data);
        if (file_exists(get_stylesheet_directory() . '/' . $this->path_root . $path . '/' . $path . '-' . $name . '.php')) {
            \get_template_part($this->path_root . $path . '/' . $path, $name);
            return true;
        }
        return false;
    }

    /**
     * setData
     * 临时数据设置，方便用于在模板间传值，但要求传值为键值对的数组
     * @param  array $data
     * @return void
     */
    protected static function setData($data)
    {
        global $_vitara_tmp_data;
        if (is_array($data)) $_vitara_tmp_data = $data;
    }
    /**
     * getData
     * 获取临时数据，同时清空该全局变量
     */
    public static function getData()
    {
        global $_vitara_tmp_data;
        $tmp = $_vitara_tmp_data;
        $_vitara_tmp_data = [];
        return $tmp;
    }

    public function __call($name, $args)
    {
        /**
         * 调用形如loadHeader($name,$data)的方法的时候，载入$path_root.'/header/header-'.$name.php的模板
         */
        if (\preg_match('/load([A-Z0-9][_a-zA-Z0-9]*)/', $name, $m)) {
            $path = \lcfirst($m[1]);
            $name = $args[0] ?? '';
            $data = (isset($args[1]) && is_array($args[1])) ? $args[1] : [];
            return $this->load_template($path, $name, $data);
        }
        /**
         * 使用getHeader($name,$data)的方法的时候，获取模板的内容可以存储在变量中
         */
        if (\preg_match('/get([A-Z0-9][_a-zA-Z0-9]*)/', $name, $m)) {
            \ob_start();
            $path = \lcfirst($m[1]);
            $name = $args[0] ?? '';
            $data = (isset($args[1]) && is_array($args[1])) ? $args[1] : [];
            $this->load_template($path, $name, $data);
            $rs = \ob_get_contents();
            \ob_end_clean();
            return $rs;
        }
    }
}
