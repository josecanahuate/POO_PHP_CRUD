<?php
#echo __DIR__; #C:\xampp\htdocs\curso-PHP\POO_GOKU_CRUD
spl_autoload_register(function ($clase) {
    $archivo = __DIR__ . '/' . str_replace('\\', '/', $clase) . '.php';

    if (is_file($archivo)) {
        require_once $archivo;
    }
});
