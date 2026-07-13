---
id: "planning.planning_casa_que_data"
tipo: "endpoint"
modulo: "planning"
url: "/src/planning/planning_casa_que_data"
metodos: ["GET", "POST"]
operacion: "form_data"
controller: "src/planning/infrastructure/ui/http/controllers/planning_casa_que_data.php"
entrada: []
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
requiere_hashb: false
errores: ["No se encuentra el usuario"]
frontend_referencias: ["frontend/planning/controller/planning_casa_que.php"]
casos_uso: ["src\\planning\\application\\PlanningCasaQueFormData"]
tags: ["planning", "casa", "que", "data"]
estado_revision: "revisado"
---

# Planning Casa Que Data

Filtro y modo del selector `CasasQue` para `planning_casa_que`, según rol y permisos del usuario
(sin depender de `Role`/`PauType` en el frontend).

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

Calcula qué casas puede ver el usuario en el formulario de planning por casas:

- **Rol casa (`PAU_CDC`)**: restringe a las ubis del usuario (`filtro.id_ubi_in`) y `modo_casas: casa`.
- **Permiso `des` o `vcsd`**: todas las casas activas (`modo_casas: all`).
- **`mi_sfsv` = 1 o 2**: filtra por `sv` o `sf` respectivamente.
- **Resto**: todas las casas activas.

No recibe parámetros POST; lee `$_SESSION['session_auth']` y `$_SESSION['oPerm']`.

## Endpoint

- URL: `/src/planning/planning_casa_que_data`
- Metodos registrados: `GET, POST`
- Operacion: `form_data`
- Controller: `src/planning/infrastructure/ui/http/controllers/planning_casa_que_data.php`

## Entrada

Sin parámetros POST. El controller invoca el caso de uso sin argumentos.

## Salida

- Helper: `ContestarJson::enviar` (doble `JSON.parse` en el front).
- Forma: `standard_envelope_string_data`.
- `data` contiene:
  - `filtro` (`array`): condiciones para `CasasQue` (`active`, opcionalmente `id_ubi_in`, `sv` o `sf`).
  - `modo_casas` (`string`): `casa` | `all` | `sv` | `sf`.
- En error: `success: false` con el mensaje en el envelope.

## Permisos

- La lógica de alcance está en el caso de uso: rol `PAU_CDC`, `have_perm_oficina('des'|'vcsd')` y
  `ConfigGlobal::mi_sfsv()`. El frontend (`planning_casa_que.php`) solo consume el payload.

## Errores conocidos

- `No se encuentra el usuario` — sesión sin `MiUsuario` válido.

## Casos De Uso

- `src\planning\application\PlanningCasaQueFormData`

## Frontend Relacionado

- `frontend/planning/controller/planning_casa_que.php` (vía `PostRequest::getDataFromUrl`)
