<?php

use src\ubiscamas\application\HabitacionFormData;
use src\shared\web\ContestarJson;

$data = HabitacionFormData::build($_POST);
ContestarJson::enviar('', $data);
