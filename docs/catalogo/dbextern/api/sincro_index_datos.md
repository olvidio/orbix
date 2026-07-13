---
id: "dbextern.sincro_index_datos"
tipo: "endpoint"
modulo: "dbextern"
url: "/src/dbextern/sincro_index_datos"
metodos: ["GET", "POST"]
operacion: "form_data"
controller: "src/dbextern/infrastructure/ui/http/controllers/sincro_index_datos.php"
entrada: ["post.tipo:string"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
requiere_hashb: false
errores: ["No se encontró la delegación en listas", "no tiene permisos", "No existe la clase de la persona"]
frontend_referencias: ["frontend/dbextern/controller/sincro_index.php"]
casos_uso: ["src\\dbextern\\application\\SincroIndexData"]
tags: ["dbextern", "sincro", "index", "datos"]
estado_revision: "revisado"
---

# Sincro Index Datos

Bootstrap del dashboard de sincronización BDU↔Aquinate: calcula los contadores de las 9 situaciones
(puntos 1–4 en BDU, 7–9 en Aquinate) y devuelve los `link_spec_*` para abrir cada subpantalla.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

Recibe el colectivo (`tipo`: `n`/`a`/`s`/`sssc`), valida permiso de oficina y clase de persona, consulta
`tmp_bdu` y cruza con `id_match` + repositorio Orbix. Devuelve contadores, arrays de IDs para las
sublistas y especificaciones de enlace firmadas que el frontend convierte con `DbexternPayload::signedLink`.

## Endpoint

- URL: `/src/dbextern/sincro_index_datos`
- Metodos registrados: `GET, POST`
- Operacion: `form_data`
- Controller: `src/dbextern/infrastructure/ui/http/controllers/sincro_index_datos.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `tipo` | `string` | controller | Sí | Colectivo: `n`, `a`, `s`, `sssc` (alias de `tipo_persona` en menú) |

## Salida

- Helper: `ContestarJson::enviar` (doble `JSON.parse` en front vía `PostRequest::getDataFromUrl`).
- Éxito: `success: true`, `data` con claves `fecha_actualizacion`, `region`, `dl_listas`, `tipo_persona`,
  contadores `p1_unidas_dl` … `p910_orbix_no_unidas`, arrays `ids_traslados`, `ids_desaparecidos_de_orbix`,
  `ids_traslados_A`, `ids_desaparecidos_de_listas` y `link_spec_ver_*` / `link_spec_self` (`path` + `query`).
- Error: `success: false`, mensaje en envelope; el payload puede traer `error` que el controller extrae.

## Errores conocidos

- `No se encontró la delegación en listas`
- `no tiene permisos` (sin permiso de oficina para el `tipo`)
- `No existe la clase de la persona`

## Permisos

- `tipo=n` → `have_perm_oficina('sm')`
- `tipo=a` → `have_perm_oficina('agd')`
- `tipo=s` → `have_perm_oficina('sg')`
- `tipo=sssc` → `have_perm_oficina('des')`
- Vía `$_SESSION['oPerm']` (`XPermisos`).

## Casos De Uso

- `src\dbextern\application\SincroIndexData`

## Frontend Relacionado

- `frontend/dbextern/controller/sincro_index.php` (carga inicial vía `PostRequest::getDataFromUrl`; si hay `error` hace `exit`)
