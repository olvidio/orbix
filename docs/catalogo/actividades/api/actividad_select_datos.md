---
id: "actividades.actividad_select_datos"
tipo: "endpoint"
modulo: "actividades"
url: "/src/actividades/actividad_select_datos"
metodos: ["GET", "POST"]
operacion: "lista_data"
controller: "src/actividades/infrastructure/ui/http/controllers/actividad_select_datos.php"
entrada: ["post.continuar:string", "post.modo:string", "post.status:integer", "post.id_tipo_activ:string", "post.filtro_lugar:string", "post.id_ubi:integer", "post.nom_activ:string", "post.periodo:string", "post.year:string", "post.dl_org:string", "post.empiezamin:string", "post.empiezamax:string", "post.fases_on:array", "post.fases_off:array", "post.publicado:integer", "post.ssfsv:string", "post.sasistentes:string", "post.sactividad:string", "post.sactividad2:string", "post.sel:array", "post.scroll_id:string", "post.stack_go:integer"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
requiere_hashb: false
errores: []
frontend_referencias: ["frontend/actividades/controller/actividad_select.php"]
casos_uso: ["src\\actividades\\application\\ActividadSelectListado"]
tags: ["actividades", "actividad", "select", "datos"]
estado_revision: "revisado"
---

# Actividad Select Datos

Devuelve los datos del listado de selección de actividades (`actividad_select`) a partir de los filtros
POST. La tabla HTML, los `link_spec` y la advertencia se firman/renderizan en el controlador frontend.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

Filtra actividades por status, tipo (compuesto o `id_tipo_activ`), lugar, nombre, periodo/fechas,
`dl_org`, fases activas/inactivas (`fases_on`/`fases_off`) y estado de publicación, y construye la tabla
de selección. El contenido de columnas y acciones depende del `modo` (`buscar`, `importar`, `publicar`…),
del rol PAU del usuario y de la preferencia de presentación (`html` → enlaces a dossiers; si no, script
`jsForm.mandar`). Cada fila marca coincidencias de fechas (`*`). Si superan 200 y no llega `continuar`,
devuelve `advertencia_demasiadas` en vez de filas.

## Endpoint

- URL: `/src/actividades/actividad_select_datos`
- Metodos registrados: `GET, POST`
- Operacion: `lista_data`
- Controller: `src/actividades/infrastructure/ui/http/controllers/actividad_select_datos.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `continuar` | `string` | controller | No | `si` fuerza mostrar >200 actividades |
| `modo` | `string` | controller | No | `buscar` (defecto), `importar`, `publicar`… |
| `status` | `integer` | controller | No | Status a filtrar |
| `id_tipo_activ` | `string` | controller | No | Filtro directo por tipo |
| `filtro_lugar` / `id_ubi` | `string`/`integer` | controller | No | Lugar |
| `nom_activ` | `string` | controller | No | Búsqueda por nombre |
| `periodo` / `year` | `string` | controller | No | Periodo; `desdeHoy` filtra por `f_fin` |
| `dl_org` | `string` | controller | No | Delegación organizadora |
| `empiezamin` / `empiezamax` | `string` | controller | No | Rango de fechas |
| `fases_on` / `fases_off` | `array` | controller | No | Filtro por fases del proceso |
| `publicado` | `integer` | controller | No | Filtra por estado de publicación |
| `ssfsv` / `sasistentes` / `sactividad` / `sactividad2` | `string` | controller | No | Componen el tipo (`sactividad2` en modo extendido) |
| `sel` | `array` | controller | No | Selección previa a reponer |
| `scroll_id` | `string` | controller | No | Restaura scroll |
| `stack_go` | `integer` | controller | No | Índice de pila para enlaces de la advertencia |

## Salida

- Helper: `ContestarJson::enviar` (data serializada como string JSON; el front hace segundo `JSON.parse`).
- Forma: `standard_envelope_string_data`.
- `data` con claves: `resultado` (texto resumen con nº y rango de fechas), `perm_nueva`, `mod`,
  `obj_pau`, `aTiposActiv` (opciones para crear nueva actividad), `html_advertencia` (vacío),
  `extendida`, `id_tipo_activ_efectivo`, `aRolesPau`, `id_role`, `a_cabeceras`, `a_botones`,
  `a_valores` (filas; celdas con `link_spec`/`script`; puede incluir `select`/`scroll_id`).
- Con demasiadas actividades: `data` trae `advertencia_demasiadas` (num + `*_link_spec`) y `a_valores` vacío.

## Permisos

- Sin control que bloquee el endpoint. Lee `$_SESSION['oPerm']`/rol PAU para decidir columnas y si se
  ofrece "nueva actividad"; la visibilidad por actividad usa `PermisosActividades`. No inferir permisos
  concretos aquí.

## Casos De Uso

- `src\actividades\application\ActividadSelectListado`

## Frontend Relacionado

- `frontend/actividades/controller/actividad_select.php` (firma advertencia y `link_spec`, pinta la tabla).
