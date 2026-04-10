<?php
// INICIO Cabecera global de URL de controlador *********************************
use src\shared\infrastructure\ProvidesRepositories;
use web\ContestarJson;


require_once("apps/core/global_header.inc");
// Archivos requeridos por esta url **********************************************

// Crea los objetos de uso global **********************************************
require_once("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************


$Qid_nom = (integer)filter_input(INPUT_POST, 'id_nom');
$Qid_tabla = (string)filter_input(INPUT_POST, 'id_tabla');
$Qnivel_stgr = (string)filter_input(INPUT_POST, 'nivel_stgr');

$entityTypeByIdTabla = [
    'n' => 'PersonaN',
    'x' => 'PersonaNax',
    'a' => 'PersonaAgd',
    's' => 'PersonaS',
    'sssc' => 'PersonaSSSC',
    'cp_sss' => 'PersonaSSSC',
    'pn' => 'PersonaEx',
    'pa' => 'PersonaEx',
];

if (!isset($entityTypeByIdTabla[$Qid_tabla])) {
    $error_txt = "No existe la clase de la persona";
    ContestarJson::enviar($error_txt);
    exit();
}


$repositoryProvider = new class {
    use ProvidesRepositories;

    public function get(string $entityType): object
    {
        return $this->getRepository($entityType);
    }
};

try {
    $repository = $repositoryProvider->get($entityTypeByIdTabla[$Qid_tabla]);
} catch (\InvalidArgumentException) {
    $error_txt = "No existe la clase de la persona";
    ContestarJson::enviar($error_txt);
    exit();
}


$oPersona = $repository->findById($Qid_nom);

$oPersona->setNivel_stgr($Qnivel_stgr);
$error_txt = '';
if ($repository->Guardar($oPersona) === false) {
    $error_txt = _("hay un error, no se ha guardado");
    $error_txt .= "\n" . $oPersona->getErrorTxt();
}

ContestarJson::enviar($error_txt, 'ok');

