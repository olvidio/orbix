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

use function src\shared\domain\helpers\input_int;
use function src\shared\domain\helpers\input_string;
use function src\shared\domain\helpers\is_true;

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
        $id_nom = input_int($input, 'id_nom');
        $obj_pau = input_string($input, 'obj_pau');

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
        $edadRaw = input_string($input, 'edad');
        $oPersona->setEdad($edadRaw === '' ? null : (int) $edadRaw);
        $oPersona->setProfesor_stgr(is_true(input_string($input, 'profesor_stgr')));

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
        $oPersona->setDl(input_string($input, 'dl'));
        $oPersona->setSituacion(input_string($input, 'situacion'));
        $oPersona->setIdioma_preferido(input_string($input, 'idioma_preferido'));
        $oPersona->setNivel_stgr(input_int($input, 'nivel_stgr'));
        $oPersona->setTrato(input_string($input, 'trato'));
        $oPersona->setNom(input_string($input, 'nom'));
        $oPersona->setApel_fam(input_string($input, 'apel_fam'));
        $oPersona->setNx1(input_string($input, 'nx1'));
        $oPersona->setApellido1(input_string($input, 'apellido1'));
        $oPersona->setNx2(input_string($input, 'nx2'));
        $oPersona->setApellido2(input_string($input, 'apellido2'));
        $oPersona->setLugar_nacimiento(input_string($input, 'lugar_nacimiento'));

        $f_nacimiento = input_string($input, 'f_nacimiento');
        $rawF_nacimiento = $f_nacimiento === '' ? null : DateTimeLocal::createFromLocal($f_nacimiento);
        $oPersona->setF_nacimiento($rawF_nacimiento instanceof DateTimeLocal ? $rawF_nacimiento : null);

        $f_situacion = input_string($input, 'f_situacion');
        $rawF_situacion = $f_situacion === '' ? null : DateTimeLocal::createFromLocal($f_situacion);
        $oPersona->setF_situacion($rawF_situacion instanceof DateTimeLocal ? $rawF_situacion : null);

        $oPersona->setProfesion(input_string($input, 'profesion'));
        $oPersona->setSacd(is_true(input_string($input, 'sacd')));
        $oPersona->setEap(input_string($input, 'eap'));
        $oPersona->setInc(input_string($input, 'inc'));

        $f_inc = input_string($input, 'f_inc');
        $rawF_inc = $f_inc === '' ? null : DateTimeLocal::createFromLocal($f_inc);
        $oPersona->setF_inc($rawF_inc instanceof DateTimeLocal ? $rawF_inc : null);
        $oPersona->setObserv(input_string($input, 'observ'));
    }

    /**
     * @param array<string,mixed> $input
     */
    private function applyDlFields(PersonaDl $oPersona, array $input): void
    {
        $oPersona->setId_ctr(input_int($input, 'id_ctr') ?: null);
        if (method_exists($oPersona, 'setCe')) {
            $oPersona->setCe(input_int($input, 'ce') ?: null);
        }
        if (method_exists($oPersona, 'setCe_lugar')) {
            $oPersona->setCe_lugar(input_string($input, 'ce_lugar') ?: null);
        }
        if (method_exists($oPersona, 'setCe_ini')) {
            $oPersona->setCe_ini(input_int($input, 'ce_ini') ?: null);
        }
        if (method_exists($oPersona, 'setCe_fin')) {
            $oPersona->setCe_fin(input_int($input, 'ce_fin') ?: null);
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
