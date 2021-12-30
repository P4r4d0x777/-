<?php

/**
 * Автозагрузка файлов библиотеки
 * 
 * 
 * @param $__className - имя класса для загрузки
 */
function autoload($__className)
{
    $__className = preg_replace('/^[A-Z]+\\\/', '', $__className);
	$fileName = __DIR__.DIRECTORY_SEPARATOR.$__className.'.php';
	if (is_readable($fileName)) {
		require $fileName;
	}
}

spl_autoload_register('autoload', true, true);