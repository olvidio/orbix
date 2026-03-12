<?php
// INICIO Cabecera global de URL de controlador *********************************
use src\shared\infrastructure\ProvidesRepositories;

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
    echo "No existe la clase de la persona";
    die();
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
    echo "No existe la clase de la persona";
    die();
}

$oPersona = $repository->findById($Qid_nom);

$oPersona->setNivel_stgr($Qnivel_stgr);
if ($repository->Guardar($oPersona) === false) {
    echo _("hay un error, no se ha guardado");
    echo "\n" . $oPersona->getErrorTxt();
}

echo $oPosicion->go_atras(1);
