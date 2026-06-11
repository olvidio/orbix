---
id: "actividades.actividad_ver_datos"
tipo: "endpoint"
modulo: "actividades"
url: "/src/actividades/actividad_ver_datos"
metodos: ["GET", "POST"]
operacion: "consulta"
controller: "src/actividades/infrastructure/ui/http/controllers/actividad_ver_datos.php"
entrada: ["post.Bdl:string", "post.calc_tarifa_inicial:integer", "post.dl_org:string", "post.id_activ:integer", "post.id_repeticion:integer", "post.id_tipo_activ:string", "post.id_ubi:integer", "post.idioma:string", "post.isfsv:integer", "post.lugar_esp:string", "post.nivel_stgr:mixed", "post.tarifa:mixed"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
respuesta_data_schema: "actividades_ActividadVerDatosData"
respuesta_data: ["entidad:object|null", "isfsv:integer", "html_despl_dl_org:string", "html_despl_tarifa:string", "html_despl_nivel_stgr:string", "html_despl_idioma:string", "html_despl_repeticion:string", "nombre_ubi:string", "ssfsv:string", "sasistentes:string", "sactividad:string", "snom_tipo:string", "tarifa_inicial:integer|null"]
requiere_hashb: false
frontend_referencias: ["frontend/actividades/controller/actividad_ver.php", "frontend/actividades/controller/planning_casa_modificar.php", "frontend/actividades/controller/planning_casa_nueva.php"]
casos_uso: ["src\\actividades\\application\\ActividadVerDatos"]
tags: ["actividades", "actividad", "ver", "datos"]
estado_revision: "revisado"
---

# Actividad Ver Datos

Consulta de soporte del formulario "ver/editar actividad": devuelve la entidad
(si procede) y los fragmentos HTML de los desplegables, para que el controller
frontend renderice sin tocar `src/`.

Dos modos segun `id_activ`:

- **`id_activ > 0` (editar)**: carga la actividad y devuelve `entidad` con todos
  sus campos; los desplegables se construyen con los valores reales de la
  actividad (los parametros `dl_org`, `tarifa`, etc. recibidos se ignoran).
  Tambien devuelve los textos del tipo (`ssfsv`, `sasistentes`, `sactividad`,
  `snom_tipo`).
- **`id_activ` ausente o 0 (nuevo / cambiar_tipo)**: `entidad: null`; los
  desplegables se construyen con los valores recibidos.

Campos auxiliares:

- `nombre_ubi`: nombre de la casa (`id_ubi` ≠ 0,1); `lugar_esp` si `id_ubi=1`;
  `"sin determinar"` si no hay lugar; `"ya no existe: cambiarlo"` si la ubi
  referenciada ya no existe.
- `tarifa_inicial` (solo si `calc_tarifa_inicial=1` y hay `id_tipo_activ`):
  primera tarifa asociada al tipo (relacion tarifa-tipo, orden `id_serie`) o null.
- `nivel_stgr` ausente ⇒ se calcula el nivel por defecto del tipo
  (misma regla que `actividad_nivel_stgr_default_datos`).

## Endpoint

- URL: `/src/actividades/actividad_ver_datos`
- Metodos registrados: `GET, POST` (solo lee POST)
- Operacion: `consulta` (sin efectos)
- Controller: `src/actividades/infrastructure/ui/http/controllers/actividad_ver_datos.php`

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Entrada

| Campo | Tipo | Obligatorio | Notas |
|-------|------|-------------|-------|
| `id_activ` | `integer` | No | `> 0` ⇒ modo editar (resto de parametros ignorados). |
| `isfsv` | `integer` | No | Seccion (1=sv, 2=sf); en modo editar se deriva del tipo. |
| `dl_org` | `string` | No | Preseleccion del desplegable organiza. |
| `Bdl` | `string` | No | `'t'` (defecto) / `'f'`: incluir o no la dl propia en el desplegable organiza. |
| `tarifa`, `nivel_stgr`, `idioma`, `id_repeticion` | varios | No | Preselecciones de los desplegables. |
| `id_ubi`, `lugar_esp` | `integer`/`string` | No | Para calcular `nombre_ubi`. |
| `id_tipo_activ` | `string` | No | Para nivel STGR por defecto y `tarifa_inicial`. |
| `calc_tarifa_inicial` | `integer` | No | `1` ⇒ incluir `tarifa_inicial` en la respuesta. |

## Salida

- Helper: `ContestarJson::enviar`
- `data`: `entidad` (objeto con `id_tipo_activ`, `dl_org`, `nom_activ`, `id_ubi`,
  `f_ini`, `h_ini`, `f_fin`, `h_fin`, `precio`, `status`, `observ`, `nivel_stgr`,
  `lugar_esp`, `tarifa`, `id_repeticion`, `publicado`, `plazas`, `idioma`, o null),
  `isfsv`, `html_despl_dl_org`, `html_despl_tarifa`, `html_despl_nivel_stgr`,
  `html_despl_idioma`, `html_despl_repeticion`, `nombre_ubi`; con entidad ademas
  `ssfsv`, `sasistentes`, `sactividad`, `snom_tipo`; con `calc_tarifa_inicial`
  ademas `tarifa_inicial`.

## Permisos

- No valida permisos: devuelve los datos de cualquier `id_activ`. El control de
  acceso a la ficha lo hace el controller frontend (`actividad_ver.php` corta con
  `only_perm('ocupado')` tras consultar `PermisosActividades`).

## Casos De Uso

- `src\actividades\application\ActividadVerDatos`

## Frontend Relacionado

- `frontend/actividades/controller/actividad_ver.php` (hasta 3 llamadas: entidad,
  desplegables modo nuevo, regeneracion del desplegable tarifa con `tarifa_inicial`)
- `frontend/actividades/controller/planning_casa_modificar.php`
- `frontend/actividades/controller/planning_casa_nueva.php`

## Revision Manual

- Revisado jun 2026 (lectura de controller + `ActividadVerDatos`): modos, forma real
  de `data` y semantica de `Bdl`/`calc_tarifa_inicial`/`nombre_ubi` verificadas.
- Pendiente: ejemplos reales de request/response.
