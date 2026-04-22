<?php

namespace src\personas\application;

use src\personas\application\support\PersonaRepositoryResolver;
use src\shared\domain\value_objects\DateTimeLocal;

use function core\is_true;

/**
 * Guarda los datos de una persona (crear o actualizar).
 *
 * Migrado desde la rama "guardar" de `apps/personas/controller/personas_update.php`
 * (slice 2 de la migracion del modulo `personas`).
 */
final class PersonaUpdate
{
    /**
     * @param array<string,mixed> $input typicamente `$_POST`.
     * @return string cadena vacia si ok, mensaje de error si falla.
     */
    public static function execute(array $input): string
    {
        $id_nom = (int)($input['id_nom'] ?? 0);
        $obj_pau = (string)($input['obj_pau'] ?? '');

        if (empty($id_nom)) {
            return _("No se ha pasado el id_nom");
        }

        try {
            $id_tabla = PersonaRepositoryResolver::idTablaFor($obj_pau);
        } catch (\InvalidArgumentException) {
            return _("No existe la clase de la persona");
        }

        $resolver = new PersonaRepositoryResolver();
        try {
            $repoPersona = $resolver->repositorio($obj_pau);
        } catch (\InvalidArgumentException) {
            return _("No existe la clase de la persona");
        }

        $oPersona = $repoPersona->findById($id_nom);
        if ($oPersona === null) { // registro nuevo
            $entityClass = 'src\\personas\\domain\\entity\\' . $obj_pau;
            if (!class_exists($entityClass)) {
                return _("No existe la clase de la persona");
            }
            $oPersona = new $entityClass();
            $oPersona->setId_nom($id_nom);
            $oPersona->setId_tabla($id_tabla);
        }

        $oPersona->setDl((string)($input['dl'] ?? ''));
        $oPersona->setId_ctr((int)($input['id_ctr'] ?? 0));
        $oPersona->setSituacion((string)($input['situacion'] ?? ''));
        $oPersona->setIdioma_preferido((string)($input['idioma_preferido'] ?? ''));
        $oPersona->setNivel_stgr((int)($input['nivel_stgr'] ?? 0));
        $oPersona->setTrato((string)($input['trato'] ?? ''));
        $oPersona->setNom((string)($input['nom'] ?? ''));
        $oPersona->setApel_fam((string)($input['apel_fam'] ?? ''));
        $oPersona->setNx1((string)($input['nx1'] ?? ''));
        $oPersona->setApellido1((string)($input['apellido1'] ?? ''));
        $oPersona->setNx2((string)($input['nx2'] ?? ''));
        $oPersona->setApellido2((string)($input['apellido2'] ?? ''));
        $oPersona->setLugar_nacimiento((string)($input['lugar_nacimiento'] ?? ''));

        $f_nacimiento = (string)($input['f_nacimiento'] ?? '');
        $oPersona->setF_nacimiento(empty($f_nacimiento) ? null : DateTimeLocal::createFromLocal($f_nacimiento));

        $f_situacion = (string)($input['f_situacion'] ?? '');
        $oPersona->setF_situacion(empty($f_situacion) ? null : DateTimeLocal::createFromLocal($f_situacion));

        $oPersona->setProfesion((string)($input['profesion'] ?? ''));
        $oPersona->setSacd(is_true((string)($input['sacd'] ?? '')));
        $oPersona->setEap((string)($input['eap'] ?? ''));
        $oPersona->setInc((string)($input['inc'] ?? ''));

        $f_inc = (string)($input['f_inc'] ?? '');
        $oPersona->setF_inc(empty($f_inc) ? null : DateTimeLocal::createFromLocal($f_inc));

        $oPersona->setCe((int)($input['ce'] ?? 0));
        $oPersona->setCe_lugar((string)($input['ce_lugar'] ?? ''));
        $oPersona->setCe_ini((int)($input['ce_ini'] ?? 0));
        $oPersona->setCe_fin((int)($input['ce_fin'] ?? 0));
        $oPersona->setObserv((string)($input['observ'] ?? ''));

        if ($repoPersona->Guardar($oPersona) === false) {
            $err = _("hay un error, no se ha guardado");
            $detalle = $repoPersona->getErrorTxt();
            return $detalle ? $err . "\n" . $detalle : $err;
        }

        return '';
    }
}
