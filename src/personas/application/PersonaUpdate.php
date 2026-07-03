<?php

namespace src\personas\application;

use src\personas\application\support\PersonaRepositoryResolver;
use src\personas\domain\contracts\PersonaAgdRepositoryInterface;
use src\personas\domain\contracts\PersonaExRepositoryInterface;
use src\personas\domain\contracts\PersonaNaxRepositoryInterface;
use src\personas\domain\contracts\PersonaNRepositoryInterface;
use src\personas\domain\contracts\PersonaSRepositoryInterface;
use src\personas\domain\contracts\PersonaSSSCRepositoryInterface;
use src\personas\domain\entity\PersonaAgd;
use src\personas\domain\entity\PersonaDl;
use src\personas\domain\entity\PersonaEx;
use src\personas\domain\entity\PersonaGlobal;
use src\personas\domain\entity\PersonaN;
use src\personas\domain\entity\PersonaNax;
use src\personas\domain\entity\PersonaS;
use src\personas\domain\entity\PersonaSSSC;
use src\shared\domain\value_objects\DateTimeLocal;

/**
 * Guarda los datos de una persona (crear o actualizar).
 */
final class PersonaUpdate
{
    public function __construct(
        private PersonaRepositoryResolver $personaRepositoryResolver,
    ) {
    }

    /**
     * @param array<string,mixed> $input typicamente `$_POST`.
     * @return string cadena vacia si ok, mensaje de error si falla.
     */
    public function execute(array $input): string
    {
        $id_nom = \src\shared\domain\helpers\FuncTablasSupport::inputInt($input, 'id_nom');
        $obj_pau = \src\shared\domain\helpers\FuncTablasSupport::inputString($input, 'obj_pau');

        if ($id_nom === 0) {
            return _("No se ha pasado el id_nom");
        }

        try {
            $id_tabla = PersonaRepositoryResolver::idTablaFor($obj_pau);
        } catch (\InvalidArgumentException) {
            return _("No existe la clase de la persona");
        }

        return match ($obj_pau) {
            'PersonaN' => $this->guardarPersonaDl(
                $this->personaRepositoryResolver->personaNRepository(),
                PersonaN::class,
                $id_nom,
                $id_tabla,
                $input,
            ),
            'PersonaAgd' => $this->guardarPersonaDl(
                $this->personaRepositoryResolver->personaAgdRepository(),
                PersonaAgd::class,
                $id_nom,
                $id_tabla,
                $input,
            ),
            'PersonaNax' => $this->guardarPersonaDl(
                $this->personaRepositoryResolver->personaNaxRepository(),
                PersonaNax::class,
                $id_nom,
                $id_tabla,
                $input,
            ),
            'PersonaS' => $this->guardarPersonaDl(
                $this->personaRepositoryResolver->personaSRepository(),
                PersonaS::class,
                $id_nom,
                $id_tabla,
                $input,
            ),
            'PersonaSSSC' => $this->guardarPersonaDl(
                $this->personaRepositoryResolver->personaSSSCRepository(),
                PersonaSSSC::class,
                $id_nom,
                $id_tabla,
                $input,
            ),
            'PersonaEx' => $this->guardarPersonaEx($id_nom, $id_tabla, $input),
            default => _("No existe la clase de la persona"),
        };
    }

    /**
     * @template T of PersonaDl
     * @param PersonaNRepositoryInterface|PersonaAgdRepositoryInterface|PersonaNaxRepositoryInterface|PersonaSRepositoryInterface|PersonaSSSCRepositoryInterface $repo
     * @param class-string<T> $entityClass
     * @param array<string,mixed> $input
     */
    private function guardarPersonaDl(
        PersonaNRepositoryInterface|PersonaAgdRepositoryInterface|PersonaNaxRepositoryInterface|PersonaSRepositoryInterface|PersonaSSSCRepositoryInterface $repo,
        string $entityClass,
        int $id_nom,
        string $id_tabla,
        array $input,
    ): string {
        $oPersona = $repo->findById($id_nom);
        if ($oPersona === null) {
            $oPersona = $this->createPersonaDlEntity($entityClass);
            $oPersona->setId_nom($id_nom);
            $oPersona->setId_tabla($id_tabla);
        }

        $this->applyCommonFields($oPersona, $input);
        $this->applyDlFields($oPersona, $input);

        if ($this->guardarPersonaEntity($repo, $entityClass, $oPersona) === false) {
            $err = _("hay un error, no se ha guardado");
            $detalle = $repo->getErrorTxt();

            return $detalle !== '' ? $err . "\n" . $detalle : $err;
        }

        return '';
    }

    /**
     * @param array<string,mixed> $input
     */
    private function guardarPersonaEx(int $id_nom, string $id_tabla, array $input): string
    {
        $repo = $this->personaRepositoryResolver->personaExRepository();

        $oPersona = $repo->findById($id_nom);
        if ($oPersona === null) {
            $oPersona = new PersonaEx();
            $oPersona->setId_nom($id_nom);
            $oPersona->setId_tabla($id_tabla);
        }

        $this->applyCommonFields($oPersona, $input);
        $edadRaw = \src\shared\domain\helpers\FuncTablasSupport::inputString($input, 'edad');
        $oPersona->setEdad($edadRaw === '' ? null : (int) $edadRaw);
        $oPersona->setProfesor_stgr(\src\shared\domain\helpers\FuncTablasSupport::isTrue(\src\shared\domain\helpers\FuncTablasSupport::inputString($input, 'profesor_stgr')));

        if ($repo->Guardar($oPersona) === false) {
            $err = _("hay un error, no se ha guardado");
            $detalle = $repo->getErrorTxt();

            return $detalle !== '' ? $err . "\n" . $detalle : $err;
        }

        return '';
    }

    /**
     * @param array<string,mixed> $input
     */
    private function applyCommonFields(PersonaGlobal|PersonaEx $oPersona, array $input): void
    {
        $oPersona->setDl(\src\shared\domain\helpers\FuncTablasSupport::inputString($input, 'dl'));
        $oPersona->setSituacion(\src\shared\domain\helpers\FuncTablasSupport::inputString($input, 'situacion'));
        $oPersona->setIdioma_preferido(\src\shared\domain\helpers\FuncTablasSupport::inputString($input, 'idioma_preferido'));
        $oPersona->setNivel_stgr(\src\shared\domain\helpers\FuncTablasSupport::inputInt($input, 'nivel_stgr'));
        $oPersona->setTrato(\src\shared\domain\helpers\FuncTablasSupport::inputString($input, 'trato'));
        $oPersona->setNom(\src\shared\domain\helpers\FuncTablasSupport::inputString($input, 'nom'));
        $oPersona->setApel_fam(\src\shared\domain\helpers\FuncTablasSupport::inputString($input, 'apel_fam'));
        $oPersona->setNx1(\src\shared\domain\helpers\FuncTablasSupport::inputString($input, 'nx1'));
        $oPersona->setApellido1(\src\shared\domain\helpers\FuncTablasSupport::inputString($input, 'apellido1'));
        $oPersona->setNx2(\src\shared\domain\helpers\FuncTablasSupport::inputString($input, 'nx2'));
        $oPersona->setApellido2(\src\shared\domain\helpers\FuncTablasSupport::inputString($input, 'apellido2'));
        $oPersona->setLugar_nacimiento(\src\shared\domain\helpers\FuncTablasSupport::inputString($input, 'lugar_nacimiento'));

        $f_nacimiento = \src\shared\domain\helpers\FuncTablasSupport::inputString($input, 'f_nacimiento');
        $rawF_nacimiento = $f_nacimiento === '' ? null : DateTimeLocal::createFromLocal($f_nacimiento);
        $oPersona->setF_nacimiento($rawF_nacimiento instanceof DateTimeLocal ? $rawF_nacimiento : null);

        $f_situacion = \src\shared\domain\helpers\FuncTablasSupport::inputString($input, 'f_situacion');
        $rawF_situacion = $f_situacion === '' ? null : DateTimeLocal::createFromLocal($f_situacion);
        $oPersona->setF_situacion($rawF_situacion instanceof DateTimeLocal ? $rawF_situacion : null);

        $oPersona->setProfesion(\src\shared\domain\helpers\FuncTablasSupport::inputString($input, 'profesion'));
        $oPersona->setSacd(\src\shared\domain\helpers\FuncTablasSupport::isTrue(\src\shared\domain\helpers\FuncTablasSupport::inputString($input, 'sacd')));
        $oPersona->setEap(\src\shared\domain\helpers\FuncTablasSupport::inputString($input, 'eap'));
        $oPersona->setInc(\src\shared\domain\helpers\FuncTablasSupport::inputString($input, 'inc'));

        $f_inc = \src\shared\domain\helpers\FuncTablasSupport::inputString($input, 'f_inc');
        $rawF_inc = $f_inc === '' ? null : DateTimeLocal::createFromLocal($f_inc);
        $oPersona->setF_inc($rawF_inc instanceof DateTimeLocal ? $rawF_inc : null);
        $oPersona->setObserv(\src\shared\domain\helpers\FuncTablasSupport::inputString($input, 'observ'));
    }

    /**
     * @param array<string,mixed> $input
     */
    private function applyDlFields(PersonaDl $oPersona, array $input): void
    {
        $oPersona->setId_ctr(\src\shared\domain\helpers\FuncTablasSupport::inputInt($input, 'id_ctr') ?: null);
        if (method_exists($oPersona, 'setCe')) {
            $oPersona->setCe(\src\shared\domain\helpers\FuncTablasSupport::inputInt($input, 'ce') ?: null);
        }
        if (method_exists($oPersona, 'setCe_lugar')) {
            $oPersona->setCe_lugar(\src\shared\domain\helpers\FuncTablasSupport::inputString($input, 'ce_lugar') ?: null);
        }
        if (method_exists($oPersona, 'setCe_ini')) {
            $oPersona->setCe_ini(\src\shared\domain\helpers\FuncTablasSupport::inputInt($input, 'ce_ini') ?: null);
        }
        if (method_exists($oPersona, 'setCe_fin')) {
            $oPersona->setCe_fin(\src\shared\domain\helpers\FuncTablasSupport::inputInt($input, 'ce_fin') ?: null);
        }
    }

    /**
     * @param class-string<PersonaDl> $entityClass
     */
    private function createPersonaDlEntity(string $entityClass): PersonaDl
    {
        return match ($entityClass) {
            PersonaN::class => new PersonaN(),
            PersonaAgd::class => new PersonaAgd(),
            PersonaNax::class => new PersonaNax(),
            PersonaS::class => new PersonaS(),
            PersonaSSSC::class => new PersonaSSSC(),
            default => throw new \InvalidArgumentException(sprintf('Entidad persona no soportada: %s', $entityClass)),
        };
    }

    /**
     * @param PersonaNRepositoryInterface|PersonaAgdRepositoryInterface|PersonaNaxRepositoryInterface|PersonaSRepositoryInterface|PersonaSSSCRepositoryInterface $repo
     * @param class-string<PersonaDl> $entityClass
     */
    private function guardarPersonaEntity(
        PersonaNRepositoryInterface|PersonaAgdRepositoryInterface|PersonaNaxRepositoryInterface|PersonaSRepositoryInterface|PersonaSSSCRepositoryInterface $repo,
        string $entityClass,
        PersonaDl $oPersona,
    ): bool {
        return match ($entityClass) {
            PersonaN::class => $repo instanceof PersonaNRepositoryInterface && $oPersona instanceof PersonaN
                ? $repo->Guardar($oPersona) : false,
            PersonaAgd::class => $repo instanceof PersonaAgdRepositoryInterface && $oPersona instanceof PersonaAgd
                ? $repo->Guardar($oPersona) : false,
            PersonaNax::class => $repo instanceof PersonaNaxRepositoryInterface && $oPersona instanceof PersonaNax
                ? $repo->Guardar($oPersona) : false,
            PersonaS::class => $repo instanceof PersonaSRepositoryInterface && $oPersona instanceof PersonaS
                ? $repo->Guardar($oPersona) : false,
            PersonaSSSC::class => $repo instanceof PersonaSSSCRepositoryInterface && $oPersona instanceof PersonaSSSC
                ? $repo->Guardar($oPersona) : false,
            default => false,
        };
    }
}
