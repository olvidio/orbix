<?php

use src\shared\web\ContestarJson;
use src\inventario\application\InventarioCssInlineData;

$data = InventarioCssInlineData::build();
ContestarJson::enviar('', $data);
