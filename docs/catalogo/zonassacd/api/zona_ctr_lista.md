---
id: "zonassacd.zona_ctr_lista"
tipo: "endpoint"
modulo: "zonassacd"
url: "/src/zonassacd/zona_ctr_lista"
metodos: ["GET", "POST"]
operacion: "consulta"
controller: "src/zonassacd/infrastructure/ui/http/controllers/zona_ctr_lista.php"
entrada: ["post.id_zona:string"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
requiere_hashb: false
frontend_referencias: ["frontend/zonassacd/controller/zona_ctr_lista_ajax.php"]
casos_uso: ["src\\zonassacd\\application\\ZonaCtrLista"]
tags: ["zonassacd", "zona", "ctr", "lista"]
estado_revision: "revisado"
---

# Zona Ctr Lista

Devuelve la estructura de tabla con los **centros activos de una zona** (columnas
centro y zona).

- `id_zona` numerico: centros dl + sf de esa zona, ordenados por nombre.
- `id_zona = 'no'`: centros dl activos sin zona (`id_zona IS NULL`).
- `id_zona = 'no_sf'`: centros sf/ellas activos sin zona.

Los centros sf (con `id_ubi` que empieza por `2`) solo se incluyen si el usuario
tiene permiso (`des`/`vcsd`) y se marcan con clase `tono2`.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Endpoint

- URL: `/src/zonassacd/zona_ctr_lista`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/zonassacd/infrastructure/ui/http/controllers/zona_ctr_lista.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `id_zona` | `string` | controller | No | controller |

El controller pasa `$_POST` completo al caso de uso; la tabla incluye campos inferidos del application layer.

## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- `data`: estructura de tabla para `Lista`: `tipo: "tabla"`, `id_tabla: "zona_ctr_ajax"`,
  `a_cabeceras` (centro, zona), `con_sel`, `a_valores`.

## Permisos

- Permiso oficina `des` o `vcsd`: activa `con_sel` y la inclusion de centros sf.

## Casos De Uso

- `src\zonassacd\application\ZonaCtrLista`

## Frontend Relacionado

- `frontend/zonassacd/controller/zona_ctr_lista_ajax.php`

## Revision Manual

- Revisado jun 2026 (lectura de `ZonaCtrLista::execute`).
- Pendiente: ejemplos reales de request/response.