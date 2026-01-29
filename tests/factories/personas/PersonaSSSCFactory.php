<?php

namespace Tests\factories\personas;

use Faker\Factory;
use src\actividades\domain\value_objects\NivelStgrId;
use src\personas\domain\entity\PersonaSSSC;
use src\personas\domain\value_objects\ApelFamText;
use src\personas\domain\value_objects\EapText;
use src\personas\domain\value_objects\IncCode;
use src\personas\domain\value_objects\LugarNacimientoText;
use src\personas\domain\value_objects\ObservText;
use src\personas\domain\value_objects\PersonaApellido1Text;
use src\personas\domain\value_objects\PersonaApellido2Text;
use src\personas\domain\value_objects\PersonaNombreText;
use src\personas\domain\value_objects\PersonaNx1Text;
use src\personas\domain\value_objects\PersonaNx2Text;
use src\personas\domain\value_objects\PersonaTablaCode;
use src\personas\domain\value_objects\PersonaTratoCode;
use src\personas\domain\value_objects\ProfesionText;
use src\personas\domain\value_objects\SituacionCode;
use src\shared\domain\value_objects\DateTimeLocal;
use src\shared\domain\value_objects\LocaleCode;
use src\ubis\domain\value_objects\DelegacionCode;

/**
 * Factory para crear instancias de PersonaSSSC para tests
 * Generado automáticamente - puede ser modificado según necesidades
 */
class PersonaSSSCFactory
{
    private int $count = 1;

    public function setCount(int $count): void
    {
        $this->count = $count;
    }

    public function getCount(): int
    {
        return $this->count;
    }

    /**
     * Crea una instancia simple de PersonaSSSC con datos mínimos
     * Útil para tests que no requieren datos complejos
     */
    public function createSimple(?int $id = null): PersonaSSSC
    {
        $id = $id ?? (9900000 + random_int(1000, 9999));
        $oPersonaSSSC = new PersonaSSSC();
        $oPersonaSSSC->setId_auto($id);

        $oPersonaSSSC->setId_nom(2001);
        $oPersonaSSSC->setIdTablaVo(new PersonaTablaCode('sssc'));
        $oPersonaSSSC->setApellido1Vo(new PersonaApellido1Text('test_apellido1vo'));
        $oPersonaSSSC->setSituacionVo(new SituacionCode('A'));

        return $oPersonaSSSC;
    }

    /**
     * Crea una instancia de PersonaSSSC con datos realistas usando Faker
     * @param int|null $id ID específico o null para generar uno aleatorio
     * @return PersonaSSSC
     */
    public function create(?int $id = null): PersonaSSSC
    {
        $faker = Factory::create('es_ES');
        $id = $id ?? (9900000 + random_int(1000, 9999));

        $oPersonaSSSC = new PersonaSSSC();
        $oPersonaSSSC->setId_auto($id);

        $oPersonaSSSC->setId_nom($faker->numberBetween(2001, 20000));
        $oPersonaSSSC->setIdTablaVo(new PersonaTablaCode('sssc'));
        $oPersonaSSSC->setDlVo(new DelegacionCode(substr($faker->word, 0, 8)));
        $oPersonaSSSC->setSacd($faker->boolean);
        $oPersonaSSSC->setTratoVo(new PersonaTratoCode(substr($faker->word, 0, 5)));
        $oPersonaSSSC->setNomVo(new PersonaNombreText($faker->firstNameMale()));
        $oPersonaSSSC->setNx1Vo(new PersonaNx1Text(substr($faker->word, 0, 7)));
        $oPersonaSSSC->setApellido1Vo(new PersonaApellido1Text($faker->lastName));
        $oPersonaSSSC->setNx2Vo(new PersonaNx2Text(substr($faker->word, 0, 7)));
        $oPersonaSSSC->setApellido2Vo(new PersonaApellido2Text($faker->lastName));
        $oPersonaSSSC->setF_nacimiento(new DateTimeLocal($faker->date()));
        $oPersonaSSSC->setIdiomaPreferidoVo(new LocaleCode($faker->locale() . ".UTF-8"));
        $oPersonaSSSC->setSituacionVo(new SituacionCode(strtoupper($faker->randomLetter())));
        $oPersonaSSSC->setF_situacion(new DateTimeLocal($faker->date()));
        $oPersonaSSSC->setApelFamVo(new ApelFamText($faker->word));
        $oPersonaSSSC->setIncVo(new IncCode(substr($faker->word, 0, 2)));
        $oPersonaSSSC->setF_inc(new DateTimeLocal($faker->date()));
        $oPersonaSSSC->setNivelStgrVo(new NivelStgrId($faker->randomElement(array_keys(NivelStgrId::getArrayNivelStgr()))));
        $oPersonaSSSC->setProfesionVo(new ProfesionText($faker->realText(255)));
        $oPersonaSSSC->setEapVo(new EapText($faker->word));
        $oPersonaSSSC->setObservVo(new ObservText($faker->realText()));
        $oPersonaSSSC->setId_ctr($faker->numberBetween(10011, 50000));
        $oPersonaSSSC->setLugarNacimientoVo(new LugarNacimientoText($faker->realText(255)));
        $oPersonaSSSC->setEs_publico($faker->boolean);

        return $oPersonaSSSC;
    }

    /**
     * Crea múltiples instancias de PersonaSSSC
     * @param int $count Número de instancias a crear
     * @param int|null $startId ID inicial (se incrementará)
     * @return array
     */
    public function createMany(int $count, ?int $startId = null): array
    {
        $startId = $startId ?? (9900000 + random_int(1000, 9999));
        $instances = [];

        for ($i = 0; $i < $count; $i++) {
            $instances[] = $this->create($startId + $i);
        }

        return $instances;
    }
}
