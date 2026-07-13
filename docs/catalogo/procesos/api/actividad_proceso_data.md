---
id: "procesos.actividad_proceso_data"
tipo: "endpoint"
modulo: "procesos"
url: "/src/procesos/actividad_proceso_data"
metodos: ["GET", "POST"]
operacion: "form_data"
controller: "src/procesos/infrastructure/ui/http/controllers/actividad_proceso_data.php"
entrada: ["post.id_activ:integer"]
entrada_obligatoria: ["id_activ"]
respuesta: "standard_envelope_string_data"
requiere_hashb: false
errores: []
frontend_referencias: ["frontend/procesos/controller/actividad_proceso.php"]
casos_uso: ["src\\procesos\\application\\ActividadProcesoData"]
tags: ["procesos", "actividad", "proceso", "data"]
estado_revision: "revisado"
---

# Actividad Proceso Data

Datos iniciales para la pantalla `actividad_proceso` (nombre de la actividad seleccionada).

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

Devuelve el identificador y el nombre de la actividad cuyo proceso se va a consultar o editar.
El `id_activ` suele llegar desde la navegación `sel` de `actividad_proceso.php`.

## Endpoint

- URL: `/src/procesos/actividad_proceso_data`
- Metodos registrados: `GET, POST`
- Operacion: `form_data`
- Controller: `src/procesos/infrastructure/ui/http/controllers/actividad_proceso_data.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `id_activ` | `integer` | controller | Si | Extraído de `$_POST`; identifica la actividad |

## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Claves en `data` (doble `JSON.parse`):
  - `id_activ` (`int`)
  - `nom_activ` (`string`; vacío si no existe la actividad)

## Errores conocidos

- _(ninguno; no valida existencia de la actividad)_

## Permisos

- Sin control de permisos propio en el caso de uso; la autorización se resuelve en el
  frontend (`permiso_calendario` vía `ProcesosPayload`) y en `$_SESSION['oPerm']`.

## Casos De Uso

- `src\procesos\application\ActividadProcesoData`

## Frontend Relacionado

- `frontend/procesos/controller/actividad_proceso.php` (carga inicial vía `PostRequest::getDataFromUrl`)
