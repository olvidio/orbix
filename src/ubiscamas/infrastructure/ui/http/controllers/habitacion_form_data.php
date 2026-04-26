<?php

use src\ubiscamas\application\HabitacionFormData;
use frontend\shared\web\ContestarJson;

$data = HabitacionFormData::build($_POST);
ContestarJson::enviar('', $data);
