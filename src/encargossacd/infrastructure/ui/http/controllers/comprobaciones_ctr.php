<?php

use src\encargossacd\application\EncargoComprobacionesCtr;
use frontend\shared\web\ContestarJson;

ContestarJson::enviar('', EncargoComprobacionesCtr::ejecutar());
