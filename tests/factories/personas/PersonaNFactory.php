<?php

namespace Tests\factories\personas;

use Faker\Factory;
use src\actividades\domain\value_objects\NivelStgrId;
use src\personas\domain\entity\PersonaN;
use src\personas\domain\value_objects\ApelFamText;
use src\personas\domain\value_objects\CeCurso;
use src\personas\domain\value_objects\CeLugarText;
use src\personas\domain\value_objects\CeNumber;
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
 * Factory para crear instancias de PersonaN para tests
 * Generado automáticamente - puede ser modificado según necesidades
 */
class PersonaNFactory
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
     * Crea una instancia simple de PersonaN con datos mínimos
     * Útil para tests que no requieren datos complejos
     */
    public function createSimple(?int $id = null): PersonaN
    {
        $id = $id ?? (9900000 + random_int(1000, 9999));
        $oPersonaN = new PersonaN();
        $oPersonaN->setId_auto($id);

        $oPersonaN->setId_nom(2001);
        $oPersonaN->setIdTablaVo(new PersonaTablaCode('n'));
        $oPersonaN->setApellido1Vo(new PersonaApellido1Text('test_apellido1vo'));
        $oPersonaN->setSituacionVo(new SituacionCode('A'));

        return $oPersonaN;
    }

    /**
     * Crea una instancia de PersonaN con datos realistas usando Faker
     * @param int|null $id ID específico o null para generar uno aleatorio
     * @return PersonaN
     */
    public function create(?int $id = null): PersonaN
    {
        $faker = Factory::create('es_ES');
        $id = $id ?? (9900000 + random_int(1000, 9999));

        $oPersonaN = new PersonaN();
        $oPersonaN->setId_auto($id);

        $oPersonaN->setId_nom($faker->numberBetween(2001, 20000));
        $oPersonaN->setIdTablaVo(new PersonaTablaCode('n'));
        $oPersonaN->setDlVo(new DelegacionCode(substr($faker->word, 0, 8)));
        $oPersonaN->setSacd($faker->boolean);
        $oPersonaN->setTratoVo(new PersonaTratoCode(substr($faker->word, 0, 5)));
        $oPersonaN->setNomVo(new PersonaNombreText($faker->firstNameMale()));
        $oPersonaN->setNx1Vo(new PersonaNx1Text(substr($faker->word, 0, 7)));
        $oPersonaN->setApellido1Vo(new PersonaApellido1Text($faker->lastName));
        $oPersonaN->setNx2Vo(new PersonaNx2Text(substr($faker->word, 0, 7)));
        $oPersonaN->setApellido2Vo(new PersonaApellido2Text($faker->lastName));
        $oPersonaN->setF_nacimiento(new DateTimeLocal($faker->date()));
        $oPersonaN->setIdiomaPreferidoVo(new LocaleCode($faker->locale() . ".UTF-8"));
        $oPersonaN->setSituacionVo(new SituacionCode(strtoupper($faker->randomLetter())));
        $oPersonaN->setF_situacion(new DateTimeLocal($faker->date()));
        $oPersonaN->setApelFamVo(new ApelFamText($faker->word));
        $oPersonaN->setIncVo(new IncCode(substr($faker->word, 0, 2)));
        $oPersonaN->setF_inc(new DateTimeLocal($faker->date()));
        $oPersonaN->setNivelStgrVo(new NivelStgrId($faker->randomElement(array_keys(NivelStgrId::getArrayNivelStgr()))));
        $oPersonaN->setProfesionVo(new ProfesionText($faker->realText(255)));
        $oPersonaN->setEapVo(new EapText($faker->word));
        $oPersonaN->setObservVo(new ObservText($faker->realText()));
        $oPersonaN->setId_ctr($faker->numberBetween(10011, 50000));
        $oPersonaN->setLugarNacimientoVo(new LugarNacimientoText($faker->realText(255)));
        $oPersonaN->setEs_publico($faker->boolean);
        $oPersonaN->setCeVo(new CeCurso($faker->numberBetween(1, 10)));
        $oPersonaN->setCeIniVo(new CeNumber($faker->numberBetween(1, 10)));
        $oPersonaN->setCeFinVo(new CeNumber($faker->numberBetween(1, 10)));
        $oPersonaN->setCeLugarVo(new CeLugarText($faker->word));

        return $oPersonaN;
    }

    /**
     * Crea múltiples instancias de PersonaN
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
