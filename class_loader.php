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
		// 这里使用映射的目的
		// 1.是为了按需加载
		// 2.是为了清楚的指定类对应的文件
		// TODO 优化方案:
		// 1.可以考虑实现一个直接由类名查找文件的方法
		// 2.可以考虑实现"约定大于配置"策略
		$file = self::$class_map[$class_name];
		if (isset($file)) {
			include dirname(__FILE__) . '/../core/' . $file . '.php';
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
		'Important'			=> '../config/important',

		'DB'			    => '../common/db',
		'DBConn'			=> '../common/db_conn',

		'Base'			    => '../common/base',
	];
}
