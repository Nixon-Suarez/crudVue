<?php
    // Cargar autoload de Composer
    require_once __DIR__ . "/vendor/autoload.php";

    // Cargar configuración de base de datos (Eloquent)
    require_once __DIR__ . "/config/database.php";

    spl_autoload_register(function($clase){
        $archivo = __DIR__ . "/" . str_replace("\\", "/", $clase) . ".php";
        if(is_file($archivo)){
            require_once $archivo;
        }
    });