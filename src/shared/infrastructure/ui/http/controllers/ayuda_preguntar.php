<?php

/**
 * Endpoint JSON: buscar en la documentación local (docs/guias + docs/ai + docs/manual) sin LLM.
 *
 * Entrada: `pregunta`, `limite` (opcional) y `pantalla` (opcional: pantalla desde la que se abrió
 * la ayuda). Cada pregunta queda anotada sin el usuario ({@see AyudaConsultaRegistrar}) y la
 * respuesta lleva `ctx_valorar`, la cápsula con la que quien preguntó puede decir si le sirvió
 * (`/src/shared/ayuda_consulta_valorar`). Sin pregunta no se anota nada.
 */

use src\shared\application\AyudaConsultaRegistrar;
use src\shared\application\AyudaPreguntar;
use src\shared\config\ConfigGlobal;
use src\shared\domain\helpers\FilterPostGet;
use src\shared\infrastructure\DependencyResolver;
use src\shared\security\HashB;
use src\shared\web\ContestarJson;

$useCase = new AyudaPreguntar();
$data = $useCase->execute($_POST);

$data['ctx_valorar'] = '';
if ($data['pregunta'] !== '') {
    $uuid = DependencyResolver::get(AyudaConsultaRegistrar::class)->execute(
        $data,
        ConfigGlobal::mi_region_dl(),
        (string) FilterPostGet::post('pantalla')
    );
    if ($uuid !== '') {
        // Quien pregunta puede tardar en leer los pasos antes de valorar: cuatro horas.
        $data['ctx_valorar'] = HashB::sign('ayuda_consulta_valorar', ['uuid_item' => $uuid], 4 * 3600);
    }
}

ContestarJson::enviar('', $data);
