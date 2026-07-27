---
id: "notas.acta_ver_notas_listado_data"
tipo: "endpoint"
modulo: "notas"
url: "/src/notas/acta_ver_notas_listado_data"
metodos: ["GET", "POST"]
operacion: "lista_data"
controller: "src/notas/infrastructure/ui/http/controllers/acta_ver_notas_listado_data.php"
entrada: ["post.acta:string"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
requiere_hashb: false
errores: []
frontend_referencias: ["frontend/notas/controller/acta_ver.php"]
casos_uso: ["src\\notas\\application\\ActaVerNotasListadoData"]
tags: ["notas", "acta", "ver", "listado", "notas"]
estado_revision: "revisado"
---

# Acta Ver Notas Listado Data

Listado solo lectura de alumnos y notas de un acta en la pantalla `acta_ver` (contexto standalone, no embebido en actividad).

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Endpoint

- URL: `/src/notas/acta_ver_notas_listado_data`
- Metodos registrados: `GET, POST`
- Operacion: `lista_data`
- Controller: `src/notas/infrastructure/ui/http/controllers/acta_ver_notas_listado_data.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `acta` | `string` | application | No | Si vacío → `filas`/`avisos` vacíos (sin error) |

El controller pasa `$_POST` completo al caso de uso.

## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data` (payload en `data` como string JSON; doble `JSON.parse` en cliente)
- Payload:

| Clave | Tipo | Descripción |
|-------|------|-------------|
| `filas` | `list` | `{id_nom, nombre, nota, situacion}` ordenadas por nombre sin acentos |
| `avisos` | `list<string>` | Notas cuyo `id_nom` no es accesible en global |

## Objetivo funcional

Devuelve las `PersonaNota` del acta con `tipo_acta = FORMATO_ACTA`, resolviendo el nombre vía `PersonaFinderService::findPersonaEnGlobal`.

## Casos particulares

- Si `acta` vacío → `{filas: [], avisos: []}` (no error).
- Si `id_nom < 1` en una nota → se omite la fila.
- Si la persona no se encuentra en global → se omite la fila y se añade aviso gettext `existe una nota de la que no se tiene acceso al nombre (id_nom = %s)`.
- Situación: texto desde `NotaSituacion::getArraySituacionTxt()`; id desconocido → string vacío.
- Orden: `uksort` con `FuncTablasSupport::strsinacentocmp` sobre el nombre.

## Permisos

- Sin control propio en el caso de uso. El frontend solo llama este endpoint en contexto standalone (`$notas` y `$Qnotas` vacíos), acta existente, no modo nueva, y con asignatura válida. Autorización de pantalla: `have_perm_oficina('est')` / `rstgr` solo lectura.

## Errores conocidos

- Ningún error en envelope; avisos suaves en `data.avisos`.

## Casos De Uso

- `src\notas\application\ActaVerNotasListadoData`

## Frontend Relacionado

- `frontend/notas/controller/acta_ver.php` vía `PostRequest::getDataFromUrl` cuando `$mostrar_notas_listado`.
