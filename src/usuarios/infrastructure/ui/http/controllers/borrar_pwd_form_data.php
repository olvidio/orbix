<?php
use src\shared\security\HashB;
use src\shared\web\ContestarJson;

// Sin identidad de registro que atar: la mutación opera sobre todos los
// usuarios de todos los esquemas de pruebas. El ctx solo prueba que la
// petición viene de esta pantalla (mismo patrón que cabecera_pie_txt_guardar
// / activacion_default_guardar).
$data = ['ctx_guardar' => HashB::sign('borrar_pwd')];

ContestarJson::enviar('', $data);
