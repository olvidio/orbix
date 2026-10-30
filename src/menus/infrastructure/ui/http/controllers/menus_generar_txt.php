<?php
use src\shared\infrastructure\DependencyResolver;

/**
 * Esta página genera un fichero con todos los textos de los menús que hay en la base de datos,
 * para poder traducirlos por gettex
 *
 *
 * @package    delegacion
 * @subpackage    fichas
 * @author    Dani Serrabou
 * @since        15/5/02.
 *
 */

use src\shared\config\ServerConf;
use src\actividades\domain\contracts\RepeticionRepositoryInterface;
use src\menus\domain\contracts\MenuDbRepositoryInterface;
use src\menus\domain\criteria\MenuDbField;
use src\shared\domain\criteria\Criteria;
use src\shared\domain\criteria\Filter;

/** @var MenuDbRepositoryInterface $MenusRepository */
$MenusRepository = DependencyResolver::get(MenuDbRepositoryInterface::class);
$cMenus = $MenusRepository->matching(
    Criteria::create()
        ->where(Filter::eq(MenuDbField::Ok, true))
        ->orderBy(MenuDbField::IdGrupmenu)
        ->addOrderBy(MenuDbField::Orden)
);

$texto = "<?php\n //Menus interiores\n";

foreach ($cMenus as $oMenuDb) {
    $menu = $oMenuDb->getMenu();
    if (!empty($menu)) {
        $texto .= " _(\"$menu\"); \n";
    }
}

// Añadir los tipos de repetición
/** @var RepeticionRepositoryInterface $RepeticionRepository */
$RepeticionRepository = DependencyResolver::get(RepeticionRepositoryInterface::class);
$cRepeticion = $RepeticionRepository->matching(Criteria::create());

$texto .= "//tipos de repetición actividades\n";
foreach ($cRepeticion as $oRepeticion) {
    $repe = $oRepeticion->getRepeticionVo()->value();
    if (!empty($repe)) {
        $texto .= " _(\"$repe\"); \n";
    }
}

//echo $texto;
//$dir_base = "/var/www/orbix";
$dir_base = ServerConf::DIR;
$filename = "$dir_base/frontend/menus/view/traducir_menu.phtml";
$somecontent = $texto;

// Let's make sure the file exists and is writable first.
if (is_writable($filename)) {

    // In our example we're opening $filename in append mode.
    // The file pointer is at the bottom of the file hence 
    // that's where $somecontent will go when we fwrite() it.
    if (!$handle = fopen($filename, 'w+')) {
        print "No puedo abrir el fichero ($filename)";
        exit;
    }

    // Write $somecontent to our opened file.
    if (!fwrite($handle, $somecontent)) {
        print "Cannot write to file ($filename)";
        exit;
    }
    fclose($handle);

} else {
    print "The file $filename is not writable";
}