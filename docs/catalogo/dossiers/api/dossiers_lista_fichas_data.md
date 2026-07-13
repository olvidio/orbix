---
id: "dossiers.dossiers_lista_fichas_data"
tipo: "endpoint"
modulo: "dossiers"
url: "/src/dossiers/dossiers_lista_fichas_data"
metodos: ["GET", "POST"]
operacion: "lista_data"
controller: "src/dossiers/infrastructure/ui/http/controllers/dossiers_lista_fichas_data.php"
entrada: ["post.id_pau:integer", "post.obj_pau:string", "post.pau:string"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
respuesta_data_schema: "dossiers_DossiersListaFichasDataData"
respuesta_data: ["a_filas:array", "web_icons:string"]
requiere_hashb: false
frontend_referencias: ["frontend/dossiers/helpers/DossiersListaRender.php"]
casos_uso: ["src\\dossiers\\application\\DossiersListaFichasData"]
tags: ["dossiers", "lista", "fichas", "data"]
estado_revision: "revisado"
---

# Dossiers Lista Fichas Data

Filas de la tabla de relación de dossiers (modo lista de `dossiers_ver`).

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

Devuelve las carpetas de dossier disponibles para una entidad (`pau` + `id_pau`): recorre los
`TipoDossier` del ámbito (`tabla_from = pau`), filtra por disponibilidad de `app` (p. ej. los
dossiers de camas CDC solo si `ubiscamas` está instalada) y por capacidad de renderizar ficha, y por
cada uno determina el icono de carpeta abierta/cerrada según exista un `Dossier` activo. Calcula el
permiso efectivo por fila (`PermDossier::permiso`) y emite los link specs para ver/abrir la ficha.

## Endpoint

- URL: `/src/dossiers/dossiers_lista_fichas_data`
- Metodos registrados: `GET, POST`
- Operacion: `lista_data`
- Controller: `src/dossiers/infrastructure/ui/http/controllers/dossiers_lista_fichas_data.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `pau` | `string` | controller | No | Ámbito/tabla origen (`p` persona, `a` actividad, `u` ubi). Se usa como `tabla_from` |
| `id_pau` | `integer` | controller | No | Id de la entidad (persona/actividad/ubi) según `pau` |
| `obj_pau` | `string` | controller | No | Colectivo/clase concreta; se propaga a los link specs |

El controller lee `pau`, `id_pau` y `obj_pau` de `$_POST` y llama a `DossiersListaFichasData::build()`.

## Salida

- Helper: `ContestarJson::enviar` (data serializada como string JSON; el front hace un segundo `JSON.parse`).
- Forma: `standard_envelope_string_data`.
- Payload en `data` (schema `dossiers_DossiersListaFichasDataData`):
  - `a_filas`: lista de filas, cada una con:
    - `imagen` (`string`, icono `folder.open.gif` / `folder.gif`)
    - `clase` (`string`, `imp`/`par` para el zebra)
    - `descripcion` (`string`)
    - `href_ver_link_spec` (link spec hacia `dossiers_ver.php`)
    - `href_abrir_link_spec` (link spec hacia `dossier_abrir.php`)
    - `perm_a` (permiso efectivo calculado por `PermDossier::permiso`)
  - `web_icons` (`string`, base de iconos)
- Los `href_ver` / `href_abrir` NO se firman en el borde HTTP: los link specs se firman en el
  frontend (`DossiersListaSupport::signFilas(..., ['href_ver', 'href_abrir'])`).

## Permisos

- No hay control de acceso propio en el endpoint. La visibilidad de cada tipo de dossier depende de
  las `app` instaladas (`ConfigGlobal::is_app_installed`) y el permiso por fila lo calcula
  `PermDossier::permiso(permiso_lectura, permiso_escritura, depende_modificar, pau, id_pau)`. El
  resto de autorización se resuelve en frontend y en `$_SESSION['oPerm']`.

## Casos De Uso

- `src\dossiers\application\DossiersListaFichasData`

## Frontend Relacionado

- `frontend/dossiers/helpers/DossiersListaRender.php` (invoca el endpoint, firma las filas y pinta
  `lista_dossiers.phtml`). También se usa indirectamente desde `dossiers_ver.php` (modo lista) vía
  `DossiersVerPantallaData`.
