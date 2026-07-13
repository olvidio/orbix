---
id: "actividades.calendario_listas_datos"
tipo: "endpoint"
modulo: "actividades"
url: "/src/actividades/calendario_listas_datos"
metodos: ["GET", "POST"]
operacion: "lista_data"
controller: "src/actividades/infrastructure/ui/http/controllers/calendario_listas_datos.php"
entrada: ["post.que:string", "post.ver_ctr:string", "post.periodo:string", "post.year:string", "post.yeardefault:string", "post.empiezamin:string", "post.empiezamax:string", "post.id_cdc:array"]
entrada_obligatoria: ["que"]
respuesta: "standard_envelope_string_data"
requiere_hashb: false
errores: ["opción no definida en switch en <file>, linea <n>"]
frontend_referencias: ["frontend/actividades/controller/calendario_listas.php"]
casos_uso: ["src\\actividades\\application\\CalendarioListasDatos"]
tags: ["actividades", "calendario", "listas", "datos"]
estado_revision: "revisado"
---

# Calendario Listas Datos

Compone el calendario de actividades por casas o por oficinas en un periodo dado y devuelve el HTML
listo para inyectar en el DOM.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

Según `que`, agrupa por casas (`lista_cdc`, `c_comunes*`, `c_todas*`) o por oficinas (`o_actual`,
`o_todas`), calcula el rango del periodo y lista las actividades con status `< 4`. Para cada actividad
resuelve lugar, tipo, nº de asistentes previstos, tarifa y (si `ver_ctr=si`) los centros encargados,
aplicando los permisos por actividad (`$_SESSION['oPermActividades']` con `procesos`, o permisivo). Las
actividades sin lugar generan avisos que preceden a la tabla paginada. Con un `que` desconocido devuelve
un mensaje de error dentro de `data.html`.

## Endpoint

- URL: `/src/actividades/calendario_listas_datos`
- Metodos registrados: `GET, POST`
- Operacion: `lista_data`
- Controller: `src/actividades/infrastructure/ui/http/controllers/calendario_listas_datos.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `que` | `string` | controller | Sí | Modo: `lista_cdc`, `c_comunes`, `c_comunes_sf/sv`, `c_todas`, `c_todas_sf/sv`, `o_actual`, `o_todas` |
| `ver_ctr` | `string` | controller | No | `si` añade la columna de centros encargados |
| `periodo` | `string` | controller | No | `desdeHoy` filtra por `f_fin` |
| `year` | `string` | controller | No | Año/curso |
| `yeardefault` | `string` | controller | No | Año por defecto (`next` si vacío) |
| `empiezamin` / `empiezamax` | `string` | controller | No | Rango de fechas |
| `id_cdc` | `array` | controller | No | Casas concretas en modo `lista_cdc` |

## Salida

- Helper: `ContestarJson::enviar` (data serializada como string JSON; el front hace segundo `JSON.parse`).
- Forma: `standard_envelope_string_data`.
- `data` es un objeto con una única clave `html` (avisos de actividades sin lugar + tabla paginada, o
  el mensaje de error si `que` no es válido).

## Errores conocidos

- `opción no definida en switch en <file>, linea <n>` (cuando `que` no coincide con ningún modo;
  devuelto dentro de `data.html`)

## Permisos

- Sin control que bloquee el endpoint. En modo `o_actual` filtra los grupos de oficina con
  `PermisoDossier::have_perm_oficina`; la visibilidad de cada actividad depende de
  `$_SESSION['oPermActividades']`. No inferir permisos concretos aquí.

## Casos De Uso

- `src\actividades\application\CalendarioListasDatos`

## Frontend Relacionado

- `frontend/actividades/controller/calendario_listas.php`
