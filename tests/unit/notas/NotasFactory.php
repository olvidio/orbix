<?php

namespace Tests\unit\notas;

use Faker\Factory;
use notas\model\entity\PersonaNotaDB;
use notas\model\PersonaNota;
use web\DateTimeLocal;

class NotasFactory
{
    private int $count = 1;
    private string $dl;

    public function __construct()
    {
    }

    public function create($id_nom,$dl)
    {
        $this->dl = $dl;
        return $this->crear_PersonaNotas($id_nom);
    }

    public function crear_PersonaNotas($id_nom): array
    {

        $faker = Factory::create();


        $count = $this->getCount() ?? 10; // número de notas

        $cPersonaNotas = [];
        $cAsignaturas = $faker->randomElements($this->a_asignaturas, $count);
        $a_id_nivel_ocupado = [];
        $a_id_asignatura_ocupado = [];
        foreach ($cAsignaturas as $Asignatura) {
            $id_asignatura = $Asignatura['id_asignatura'];
            $id_nivel = $Asignatura['id_nivel'];
            if ($id_asignatura > 3000) { // es opcional
                $id_nivel = $faker->randomElement($this->id_nivel_opcionales);
            }
            if (in_array($id_nivel, $a_id_nivel_ocupado)) {
                continue; // no puede haber duplicados
            }
            $a_id_nivel_ocupado[] = $id_nivel;

            if (in_array($id_asignatura, $a_id_asignatura_ocupado)) {
                continue; // no puede haber duplicados
            }
            $a_id_asignatura_ocupado[] = $id_asignatura;

            $f_acta_iso = $faker->dateTimeBetween()->format('Y-m-d'); // a date between -30 years ago, and now
            $oFActa = new DateTimeLocal($f_acta_iso); // a date between -30 years ago, and now

            $year = $oFActa->format('y');
            $num_acta = $faker->numberBetween(1, 150);
            $acta = $this->dl . ' ' . "$num_acta/$year";

            $id_situacion = 10;
            $tipo_acta = 1;
            $preceptor = $faker->boolean();
            $id_preceptor = null;
            if ($preceptor) {
                $id_preceptor = $id_nom + 111;
            }
            $detalle = $faker->sentence(3);
            $epoca = 2;
            $id_activ = 30012345;
            $nota_num = $faker->randomFloat(1, 2, 10);
            $nota_max = 10;

            $oPersonaNota = new PersonaNota();
            $oPersonaNota->setIdNom($id_nom);
            $oPersonaNota->setIdNivel($id_nivel);
            $oPersonaNota->setIdAsignatura($id_asignatura);
            $oPersonaNota->setIdSituacion($id_situacion);
            $oPersonaNota->setActa($acta);
            $oPersonaNota->setFActa($oFActa);
            $oPersonaNota->setTipoActa($tipo_acta);
            $oPersonaNota->setPreceptor($preceptor);
            $oPersonaNota->setIdPreceptor($id_preceptor);
            $oPersonaNota->setDetalle($detalle);
            $oPersonaNota->setEpoca($epoca);
            $oPersonaNota->setIdActiv($id_activ);
            $oPersonaNota->setNotaNum($nota_num);
            $oPersonaNota->setNotaMax($nota_max);

            $cPersonaNotas[] = $oPersonaNota;
        }

        return $cPersonaNotas;
    }


    private $id_nivel_opcionales = [1230, 1231, 1232, 2430, 2431, 2432, 2433];

    private $a_asignaturas = [
        ['id_asignatura' => 1101, 'id_nivel' => 1101],
        ['id_asignatura' => 1102, 'id_nivel' => 1102],
        ['id_asignatura' => 1103, 'id_nivel' => 1103],
        ['id_asignatura' => 1104, 'id_nivel' => 1104],
        ['id_asignatura' => 1105, 'id_nivel' => 1105],
        ['id_asignatura' => 1106, 'id_nivel' => 1106],
        ['id_asignatura' => 1107, 'id_nivel' => 1107],
        ['id_asignatura' => 1108, 'id_nivel' => 1108],
        ['id_asignatura' => 1109, 'id_nivel' => 1109],
        ['id_asignatura' => 1110, 'id_nivel' => 1110],
        ['id_asignatura' => 1111, 'id_nivel' => 1111],
        ['id_asignatura' => 1112, 'id_nivel' => 1112],
        ['id_asignatura' => 1201, 'id_nivel' => 1201],
        ['id_asignatura' => 1202, 'id_nivel' => 1202],
        ['id_asignatura' => 1203, 'id_nivel' => 1203],
        ['id_asignatura' => 1204, 'id_nivel' => 1204],
        ['id_asignatura' => 1205, 'id_nivel' => 1205],
        ['id_asignatura' => 1206, 'id_nivel' => 1206],
        ['id_asignatura' => 1207, 'id_nivel' => 1207],
        ['id_asignatura' => 1208, 'id_nivel' => 1208],
        ['id_asignatura' => 1209, 'id_nivel' => 1209],
        ['id_asignatura' => 1210, 'id_nivel' => 1210],
        ['id_asignatura' => 1211, 'id_nivel' => 1211],
        ['id_asignatura' => 1212, 'id_nivel' => 1212],
        ['id_asignatura' => 1213, 'id_nivel' => 1213],
        ['id_asignatura' => 1230, 'id_nivel' => 1230],
        ['id_asignatura' => 1231, 'id_nivel' => 1231],
        ['id_asignatura' => 1232, 'id_nivel' => 1232],
        ['id_asignatura' => 2101, 'id_nivel' => 2101],
        ['id_asignatura' => 2102, 'id_nivel' => 2102],
        ['id_asignatura' => 2103, 'id_nivel' => 2103],
        ['id_asignatura' => 2104, 'id_nivel' => 2104],
        ['id_asignatura' => 2105, 'id_nivel' => 2105],
        ['id_asignatura' => 2106, 'id_nivel' => 2106],
        ['id_asignatura' => 2107, 'id_nivel' => 2107],
        ['id_asignatura' => 2108, 'id_nivel' => 2108],
        ['id_asignatura' => 2109, 'id_nivel' => 2109],
        ['id_asignatura' => 2110, 'id_nivel' => 2110],
        ['id_asignatura' => 2111, 'id_nivel' => 2111],
        ['id_asignatura' => 2112, 'id_nivel' => 2112],
        ['id_asignatura' => 2113, 'id_nivel' => 2113],
        ['id_asignatura' => 2201, 'id_nivel' => 2201],
        ['id_asignatura' => 2202, 'id_nivel' => 2202],
        ['id_asignatura' => 2203, 'id_nivel' => 2203],
        ['id_asignatura' => 2204, 'id_nivel' => 2204],
        ['id_asignatura' => 2205, 'id_nivel' => 2205],
        ['id_asignatura' => 2206, 'id_nivel' => 2206],
        ['id_asignatura' => 2207, 'id_nivel' => 2207],
        ['id_asignatura' => 2208, 'id_nivel' => 2208],
        ['id_asignatura' => 2215, 'id_nivel' => 2209],
        ['id_asignatura' => 2209, 'id_nivel' => 2210],
        ['id_asignatura' => 2210, 'id_nivel' => 2211],
        ['id_asignatura' => 2211, 'id_nivel' => 2212],
        ['id_asignatura' => 2301, 'id_nivel' => 2301],
        ['id_asignatura' => 2302, 'id_nivel' => 2302],
        ['id_asignatura' => 2303, 'id_nivel' => 2303],
        ['id_asignatura' => 2304, 'id_nivel' => 2304],
        ['id_asignatura' => 2308, 'id_nivel' => 2305],
        ['id_asignatura' => 2306, 'id_nivel' => 2306],
        ['id_asignatura' => 2307, 'id_nivel' => 2307],
        ['id_asignatura' => 2309, 'id_nivel' => 2308],
        ['id_asignatura' => 2407, 'id_nivel' => 2309],
        ['id_asignatura' => 2410, 'id_nivel' => 2310],
        ['id_asignatura' => 2315, 'id_nivel' => 2311],
        ['id_asignatura' => 2312, 'id_nivel' => 2312],
        ['id_asignatura' => 2401, 'id_nivel' => 2401],
        ['id_asignatura' => 2402, 'id_nivel' => 2402],
        ['id_asignatura' => 2403, 'id_nivel' => 2403],
        ['id_asignatura' => 2305, 'id_nivel' => 2404],
        ['id_asignatura' => 2404, 'id_nivel' => 2405],
        ['id_asignatura' => 2405, 'id_nivel' => 2406],
        ['id_asignatura' => 2406, 'id_nivel' => 2407],
        ['id_asignatura' => 2408, 'id_nivel' => 2408],
        ['id_asignatura' => 2409, 'id_nivel' => 2409],
        ['id_asignatura' => 2310, 'id_nivel' => 2410],
        ['id_asignatura' => 2411, 'id_nivel' => 2411],
        ['id_asignatura' => 2311, 'id_nivel' => 2412],
        ['id_asignatura' => 2430, 'id_nivel' => 2430],
        ['id_asignatura' => 2431, 'id_nivel' => 2431],
        ['id_asignatura' => 2432, 'id_nivel' => 2432],
        ['id_asignatura' => 2433, 'id_nivel' => 2433],
        ['id_asignatura' => 3107, 'id_nivel' => 3107],
        ['id_asignatura' => 3114, 'id_nivel' => 3114],
        ['id_asignatura' => 3116, 'id_nivel' => 3116],
        ['id_asignatura' => 3117, 'id_nivel' => 3117],
        ['id_asignatura' => 3120, 'id_nivel' => 3120],
        ['id_asignatura' => 3126, 'id_nivel' => 3126],
        ['id_asignatura' => 3128, 'id_nivel' => 3128],
        ['id_asignatura' => 3129, 'id_nivel' => 3129],
        ['id_asignatura' => 3130, 'id_nivel' => 3130],
        ['id_asignatura' => 3131, 'id_nivel' => 3131],
        ['id_asignatura' => 3132, 'id_nivel' => 3132],
        ['id_asignatura' => 3135, 'id_nivel' => 3135],
        ['id_asignatura' => 3136, 'id_nivel' => 3136],
        ['id_asignatura' => 3139, 'id_nivel' => 3139],
        ['id_asignatura' => 3140, 'id_nivel' => 3140],
        ['id_asignatura' => 3141, 'id_nivel' => 3141],
        ['id_asignatura' => 3142, 'id_nivel' => 3142],
        ['id_asignatura' => 3143, 'id_nivel' => 3143],
        ['id_asignatura' => 3144, 'id_nivel' => 3144],
        ['id_asignatura' => 3145, 'id_nivel' => 3145],
        ['id_asignatura' => 3148, 'id_nivel' => 3148],
        ['id_asignatura' => 3156, 'id_nivel' => 3156],
        ['id_asignatura' => 3163, 'id_nivel' => 3163],
        ['id_asignatura' => 3165, 'id_nivel' => 3165],
        ['id_asignatura' => 3166, 'id_nivel' => 3166],
        ['id_asignatura' => 3167, 'id_nivel' => 3167],
        ['id_asignatura' => 3168, 'id_nivel' => 3168],
        ['id_asignatura' => 3200, 'id_nivel' => 3200],
        ['id_asignatura' => 3201, 'id_nivel' => 3201],
        ['id_asignatura' => 3204, 'id_nivel' => 3204],
        ['id_asignatura' => 3208, 'id_nivel' => 3208],
        ['id_asignatura' => 3215, 'id_nivel' => 3215],
        ['id_asignatura' => 3221, 'id_nivel' => 3221],
        ['id_asignatura' => 3226, 'id_nivel' => 3226],
        ['id_asignatura' => 3231, 'id_nivel' => 3231],
        ['id_asignatura' => 3233, 'id_nivel' => 3233],
        ['id_asignatura' => 3239, 'id_nivel' => 3239],
        ['id_asignatura' => 3240, 'id_nivel' => 3240],
        ['id_asignatura' => 3242, 'id_nivel' => 3242],
        ['id_asignatura' => 3244, 'id_nivel' => 3244],
        ['id_asignatura' => 3245, 'id_nivel' => 3245],
        ['id_asignatura' => 3247, 'id_nivel' => 3247],
        ['id_asignatura' => 3252, 'id_nivel' => 3252],
        ['id_asignatura' => 3253, 'id_nivel' => 3253],
        ['id_asignatura' => 3254, 'id_nivel' => 3254],
        ['id_asignatura' => 3258, 'id_nivel' => 3258],
        ['id_asignatura' => 3261, 'id_nivel' => 3261],
        ['id_asignatura' => 3263, 'id_nivel' => 3263],
        ['id_asignatura' => 3264, 'id_nivel' => 3264],
        ['id_asignatura' => 3265, 'id_nivel' => 3265],
        ['id_asignatura' => 3266, 'id_nivel' => 3266],
        ['id_asignatura' => 3268, 'id_nivel' => 3268],
        ['id_asignatura' => 3269, 'id_nivel' => 3269],
        ['id_asignatura' => 3270, 'id_nivel' => 3270],
        ['id_asignatura' => 3271, 'id_nivel' => 3271],
        ['id_asignatura' => 3272, 'id_nivel' => 3272],
        ['id_asignatura' => 3273, 'id_nivel' => 3273],
        ['id_asignatura' => 3281, 'id_nivel' => 3281],
        ['id_asignatura' => 3301, 'id_nivel' => 3301],
        ['id_asignatura' => 3302, 'id_nivel' => 3302],
        ['id_asignatura' => 3305, 'id_nivel' => 3305],
        ['id_asignatura' => 3308, 'id_nivel' => 3308],
        ['id_asignatura' => 3313, 'id_nivel' => 3313],
        ['id_asignatura' => 3316, 'id_nivel' => 3316],
        ['id_asignatura' => 3321, 'id_nivel' => 3321],
        ['id_asignatura' => 3324, 'id_nivel' => 3324],
        ['id_asignatura' => 3325, 'id_nivel' => 3325],
        ['id_asignatura' => 3336, 'id_nivel' => 3336],
        ['id_asignatura' => 3343, 'id_nivel' => 3343],
        ['id_asignatura' => 3344, 'id_nivel' => 3344],
        ['id_asignatura' => 3345, 'id_nivel' => 3345],
        ['id_asignatura' => 3348, 'id_nivel' => 3348],
        ['id_asignatura' => 3349, 'id_nivel' => 3349],
        ['id_asignatura' => 3350, 'id_nivel' => 3350],
        ['id_asignatura' => 3362, 'id_nivel' => 3362],
        ['id_asignatura' => 3363, 'id_nivel' => 3363],
    ];

    public function setCount(int $count)
    {
        $this->count = $count;
    }

    private function getCount()
    {
        return $this->count;
    }

}