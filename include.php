<?php

ignore_user_abort(true);

require dirname(__FILE__) . '/core/define.php';
require dirname(__FILE__) . '/core/class_loader.php';
require dirname(__FILE__) . '/core/base.php';
require dirname(__FILE__) . '/core/param.php';
require dirname(__FILE__) . '/core/param_helper.php';

ClassLoader::init();

require dirname(__FILE__) . '/core/options.php';
