---
id: "zonassacd.zona_sacd_lista"
tipo: "endpoint"
modulo: "zonassacd"
url: "/src/zonassacd/zona_sacd_lista"
metodos: ["GET", "POST"]
operacion: "consulta"
controller: "src/zonassacd/infrastructure/ui/http/controllers/zona_sacd_lista.php"
entrada: ["post.id_zona:string"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
requiere_hashb: false
frontend_referencias: ["frontend/zonassacd/controller/zona_sacd_lista_ajax.php"]
casos_uso: ["src\\zonassacd\\application\\ZonaSacdLista"]
tags: ["zonassacd", "zona", "sacd", "lista"]
estado_revision: "revisado"
---

# Zona Sacd Lista

Devuelve la estructura de tabla con los **sacd asignados a una zona**, con su flag
`propia` y los dias de atencion semanal (L–D como `x`/`-`).

- `id_zona` numerico: sacds de esa zona, ordenados por apellidos.
- `id_zona = 'no'`: sacds activos de la dl (tablas `n, a, sssc, pa, pn`) **sin ninguna**
  asignacion de zona.
- `id_zona` vacio o invalido: tabla vacia.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Endpoint

- URL: `/src/zonassacd/zona_sacd_lista`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/zonassacd/infrastructure/ui/http/controllers/zona_sacd_lista.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `id_zona` | `string` | controller | No | controller |

El controller pasa `$_POST` completo al caso de uso; la tabla incluye campos inferidos del application layer.

## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- `data`: estructura de tabla para `Lista`: `tipo: "tabla"`, `id_tabla: "zona_sacd_ajax"`,
  `a_cabeceras` (sacd, zona, propia, L, M, X, J, V, S, D), `a_botones`, `con_sel`, `a_valores`.

## Permisos

- Los datos se devuelven a cualquier usuario autenticado; el permiso oficina `des` o
  `vcsd` activa `con_sel` (checkboxes) y el boton `modificar` (modal dias de la semana,
  restaurado jun 2026 tras perderse en la migracion desde `apps/`).

## Casos De Uso

- `src\zonassacd\application\ZonaSacdLista`

## Frontend Relacionado

- `frontend/zonassacd/controller/zona_sacd_lista_ajax.php`

## Revision Manual

- Revisado jun 2026 (lectura de `ZonaSacdLista::execute`).
- Pendiente: ejemplos reales de request/response.