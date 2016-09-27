<?php

/**
 * 处理请求输入输出
 */
class Base
{
	public static function login($user_id)
	{
		$_SESSION['user_id'] = $user_id;
	}

	public static function getRequestJson()
	{
		session_start();
		if ($_SERVER['REQUEST_METHOD'] === 'POST' ) {
			$post = $_POST['data'];
			$json = json_decode($post, true);
			if (json_last_error() !== JSON_ERROR_NONE) {
				self::dieWithError(ERROR_INVALID_REQUEST);
			}
		}else{//GET
			$json = json_decode(json_encode($_GET), true);
			if (json_last_error() !== JSON_ERROR_NONE) {
				self::dieWithError(ERROR_INVALID_REQUEST);
			}
		}
		$json['user_id'] = $_SESSION['user_id'];
		return $json;
	}

	public static function dieWithError($err, $errmsg = null)
	{
		$json = json_encode(array_merge([ 'status' => $err ], ($errmsg) ? [ 'err' => $errmsg ] : []));
		die($json);
	}

	public static function dieWithResponse($obj = null)
	{
		$json = json_encode(array_merge([ 'status' => ERROR_SUCCESS ], ($obj !== null) ? [ 'data' => $obj ] : []));
		echo $json;
		die();
	}
}