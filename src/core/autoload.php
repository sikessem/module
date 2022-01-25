<?php namespace Organizer;
/*
 +----------------------------------------------+
 |            THE ORGANIZER AUTOLOAD            |
 +----------------------------------------------+
 | Author  : SIGUI Kessé Emmanuel               |
 | Website : https://pkg.sikessem.com/organizer |
 | License : Apache 2.0                         |
 +----------------------------------------------+
 */
return function(string $name): void {
    $namespace = __NAMESPACE__;
    $directory = __DIR__ . DIRECTORY_SEPARATOR;
    $extension = '.' . pathinfo(__FILE__, PATHINFO_EXTENSION);
    if(preg_match("/^$namespace\\\(?P<name>.*)$/", $name, $matches))
        if (is_file($file = $directory . str_replace('\\', DIRECTORY_SEPARATOR, $matches['name']) . $extension))
            if (is_readable($file))
                require_once $file;
};
