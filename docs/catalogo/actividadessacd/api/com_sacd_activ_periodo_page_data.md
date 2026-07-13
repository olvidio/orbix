---
id: "actividadessacd.com_sacd_activ_periodo_page_data"
tipo: "endpoint"
modulo: "actividadessacd"
url: "/src/actividadessacd/com_sacd_activ_periodo_page_data"
metodos: ["GET", "POST"]
operacion: "form_data"
controller: "src/actividadessacd/infrastructure/ui/http/controllers/com_sacd_activ_periodo_page_data.php"
entrada: []
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
respuesta_data_schema: "actividadessacd_ComSacdActivPeriodoPageDataData"
respuesta_data: ["perm_mod_txt:boolean"]
requiere_hashb: false
frontend_referencias: ["frontend/actividadessacd/controller/com_sacd_activ_periodo.php"]
casos_uso: ["src\\actividadessacd\\application\\ComSacdActivPeriodoPageData"]
tags: ["actividadessacd", "com", "sacd", "activ", "periodo", "page", "data"]
estado_revision: "revisado"
---

# Com Sacd Activ Periodo Page Data

Devuelve la configuración de la pantalla de comunicación de actividades a los sacd: si el usuario
puede modificar el texto de la comunicación.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

- Lee el usuario de `$_SESSION['session_auth']['id_usuario']` y su rol.
- `perm_mod_txt = true` por defecto; pasa a `false` si el rol del usuario es `p-sacd` (el sacd solo
  ve su comunicación, no edita los textos base).

## Endpoint

- URL: `/src/actividadessacd/com_sacd_activ_periodo_page_data`
- Metodos registrados: `GET, POST`
- Operacion: `form_data`
- Controller: `src/actividadessacd/infrastructure/ui/http/controllers/com_sacd_activ_periodo_page_data.php`

## Entrada

Sin parámetros. El controller invoca `execute()` sin leer `$_POST` (envuelto en `try/catch`: una
excepción se devuelve como `success: false` con el mensaje).

## Salida

- Helper: `ContestarJson::enviar($error, $data)` — `data` es el payload serializado como string JSON;
  el front hace un segundo `JSON.parse`. `$error` sale del `catch` del controller.
- Forma: `standard_envelope_string_data`.
- Payload en `data` (schema `actividadessacd_ComSacdActivPeriodoPageDataData`):
  - `perm_mod_txt` (`boolean`): `true` si el usuario puede modificar los textos de comunicación.

## Permisos

- El propio payload transporta el permiso (`perm_mod_txt`), derivado del rol del usuario
  (`p-sacd` → `false`). No hay más control de acceso en el caso de uso.

## Casos De Uso

- `src\actividadessacd\application\ComSacdActivPeriodoPageData`

## Frontend Relacionado

- `frontend/actividadessacd/controller/com_sacd_activ_periodo.php` (consume el payload vía
  `PostRequest::getDataFromUrl` para decidir si muestra el enlace de edición de textos).
