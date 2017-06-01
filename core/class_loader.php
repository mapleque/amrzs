<?php

/**
 * 自动加载类
 */
class ClassLoader
{
	/**
	 * 系统初始化时候需要调用该方法
	 *
	 * @return void
	 */
	public static function init()
	{
		spl_autoload_register([ __CLASS__, 'classLoaderCallback' ]);
	}

	/**
	 * 补充额外定义的映射表
	 *
	 * @param array $additional_map 而外的映射表
	 *
	 * return void
	 */
	public static function appendMap(array $additional_map)
	{
		self::$class_map = array_merge(self::$class_map, $additional_map);
	}

	/**
	 * class loader 回调函数
	 *
	 * @param string $class_name 类名 
	 *
	 * @return boolean
	 */
	public static function classLoaderCallback($class_name)
	{
		$file = self::$class_map[$class_name];
		if (isset($file)) {
			include dirname(__FILE__) . '/../../../core/' . $file . '.php';
		} else {
			return false;
		}
	}

	/**
	 * class文件映射表
	 *
	 * @var array
	 */
	private static $class_map = [
		'Important'			=> '../important',

		'DB'			    => '../vendor/amrzs/core/db',
		'DBConn'			=> '../vendor/amrzs/core/db_conn',
        'DBHelper'          => '../vendor/amrzs/core/db_helper',

		'Base'			    => '../vendor/amrzs/core/base',
	];
}
