---
id: "actividades.lista_actividades_sg_datos"
tipo: "endpoint"
modulo: "actividades"
url: "/src/actividades/lista_actividades_sg_datos"
metodos: ["GET", "POST"]
operacion: "lista_data"
controller: "src/actividades/infrastructure/ui/http/controllers/lista_actividades_sg_datos.php"
entrada: ["post.continuar:string", "post.status:integer", "post.tipo_activ_sg:string", "post.id_ubi:integer", "post.periodo:string", "post.year:string", "post.dl_org:string", "post.empiezamin:string", "post.empiezamax:string", "post.sel:array", "post.scroll_id:string", "post.stack_go:integer"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
requiere_hashb: false
errores: []
frontend_referencias: ["frontend/actividades/controller/lista_actividades_sg.php"]
casos_uso: ["src\\actividades\\application\\ListaActividadesSgListado"]
tags: ["actividades", "lista", "sg", "datos"]
estado_revision: "revisado"
---

# Lista Actividades Sg Datos

Devuelve los datos del listado de actividades sf/sg (crt, cv) para la pantalla `lista_actividades_sg`.
La tabla HTML y la advertencia firmada se arman en el controlador frontend.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

Filtra las actividades por status (por defecto `StatusId::ACTUAL`), tipo (`crt` → `1[45]1`, `cv` →
`1[45]3`), ubicación, periodo y `dl_org`. Para cada actividad calcula sus permisos por actividad
(`$_SESSION['oPermActividades']` si `procesos` está instalado; si no, permiso permisivo) y:

- Omite las actividades sin permiso `ocupado` (cuenta en "sin permiso").
- Muestra "ocupado …" para las que no tienen permiso `ver`.
- Enumera centros encargados (si `actividadescentro`) y SACD (si `actividadessacd` y permiso `sacd:ver`).

Si el nº de actividades supera 200 y no llega `continuar`, devuelve `advertencia_demasiadas` (specs de
enlaces continuar/volver) en lugar de las filas.

## Endpoint

- URL: `/src/actividades/lista_actividades_sg_datos`
- Metodos registrados: `GET, POST`
- Operacion: `lista_data`
- Controller: `src/actividades/infrastructure/ui/http/controllers/lista_actividades_sg_datos.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `continuar` | `string` | controller | No | `si` fuerza mostrar >200 actividades |
| `status` | `integer` | controller | No | Por defecto `StatusId::ACTUAL`; `5` = sin filtro |
| `tipo_activ_sg` | `string` | controller | No | `crt` (defecto) o `cv` |
| `id_ubi` | `integer` | controller | No | Ubicación |
| `periodo` / `year` | `string` | controller | No | Periodo; `desdeHoy` filtra por `f_fin` |
| `dl_org` | `string` | controller | No | Delegación organizadora |
| `empiezamin` / `empiezamax` | `string` | controller | No | Rango de fechas |
| `sel` | `array` | controller | No | Selección previa a reponer en la tabla |
| `scroll_id` | `string` | controller | No | Restaura scroll |
| `stack_go` | `integer` | controller | No | Índice de pila para los enlaces de la advertencia |

## Salida

- Helper: `ContestarJson::enviar` (data serializada como string JSON; el front hace segundo `JSON.parse`).
- Forma: `standard_envelope_string_data`.
- `data` con claves: `result_busqueda` (texto "X actividades encontradas (Y sin permiso)"),
  `id_tipo_activ` (filtro efectivo), `html_advertencia` (vacío), `a_cabeceras`, `a_botones`
  (cargos/asistentes/lista/ctrs org), `a_valores` (filas; puede incluir claves `select`/`scroll_id`).
- Cuando hay demasiadas actividades: `data` trae `advertencia_demasiadas` (num + specs de enlaces) y
  `a_valores` vacío.

## Permisos

- No hay control de permisos que bloquee el endpoint. La visibilidad de cada actividad depende de
  `$_SESSION['oPermActividades']` (permisos por actividad, módulo `procesos`) y de `$_SESSION['oPerm']`
  (p. ej. permiso `des` para ver nombres de otro sf/sv). No inferir permisos concretos aquí.

## Casos De Uso

- `src\actividades\application\ListaActividadesSgListado`

## Frontend Relacionado

- `frontend/actividades/controller/lista_actividades_sg.php` (firma advertencia y `link_spec`, pinta la tabla).
