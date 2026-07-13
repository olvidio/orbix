---
id: "procesos.procesos_select_data"
tipo: "endpoint"
modulo: "procesos"
url: "/src/procesos/procesos_select_data"
metodos: ["GET", "POST"]
operacion: "form_data"
controller: "src/procesos/infrastructure/ui/http/controllers/procesos_select_data.php"
entrada: []
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
requiere_hashb: false
errores: []
frontend_referencias: ["frontend/procesos/controller/procesos_select.php"]
casos_uso: ["src\\procesos\\application\\ProcesosSelectData"]
tags: ["procesos", "select", "data"]
estado_revision: "revisado"
---

# Procesos Select Data

Datos iniciales para la pantalla `procesos_select`.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

Devuelve el desplegable de tipos de proceso disponibles para administrar tareas de proceso.

## Endpoint

- URL: `/src/procesos/procesos_select_data`
- Metodos registrados: `GET, POST`
- Operacion: `form_data`
- Controller: `src/procesos/infrastructure/ui/http/controllers/procesos_select_data.php`

## Entrada

Sin parámetros POST; el caso de uso no lee entrada.

## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Claves en `data` (doble `JSON.parse`):
  - `a_tipos_proceso` (`array<int|string, string>`): mapa `id_tipo_proceso` → nombre

## Errores conocidos

- _(ninguno documentado en el caso de uso)_

## Permisos

- Sin control de permisos propio; autorización en `procesos_select.php` y `$_SESSION['oPerm']`.

## Casos De Uso

- `src\procesos\application\ProcesosSelectData`

## Frontend Relacionado

- `frontend/procesos/controller/procesos_select.php` (carga inicial vía `PostRequest::getDataFromUrl`)
