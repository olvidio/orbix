<?php

spl_autoload_register(function ($class) {
    $file = __DIR__ . '/' . str_replace('\\', '/', $class) . '.php';
    if (file_exists($file)) {
        require_once $file;
    }
});

use src\encargossacd\domain\value_objects\EncargoModHorarioId;

try {
    $vo1 = new EncargoModHorarioId(EncargoModHorarioId::HORARIO_OPCIONAL);
    echo "Valor 1 ok: " . $vo1->value() . "\n";
    
    $vo2 = new EncargoModHorarioId(EncargoModHorarioId::HORARIO_POR_MODULOS);
    echo "Valor 2 ok: " . $vo2->value() . "\n";
    
    $vo3 = new EncargoModHorarioId(EncargoModHorarioId::HORARIO_POR_HORAS);
    echo "Valor 3 ok: " . $vo3->value() . "\n";
    
    echo "Intentando valor inválido (4)...\n";
    new EncargoModHorarioId(4);
} catch (\InvalidArgumentException $e) {
    echo "Excepción capturada correctamente: " . $e->getMessage() . "\n";
}
