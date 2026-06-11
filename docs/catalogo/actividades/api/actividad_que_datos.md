---
id: "actividades.actividad_que_datos"
tipo: "endpoint"
modulo: "actividades"
url: "/src/actividades/actividad_que_datos"
metodos: ["GET", "POST"]
operacion: "consulta"
controller: "src/actividades/infrastructure/ui/http/controllers/actividad_que_datos.php"
entrada: ["post.extendida:mixed", "post.id_tipo_activ:mixed", "post.para:string", "post.perm_jefe:mixed", "post.que:string", "post.sactividad:string", "post.sactividad2:string", "post.sasistentes:string", "post.sfsv:string", "post.sfsv_all:mixed", "post.snom_tipo:string"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
respuesta_data_schema: "actividades_ActividadQueDatosData"
respuesta_data: ["actividad_tipo_html:string"]
requiere_hashb: false
frontend_referencias: ["frontend/actividades/controller/actividad_que.php", "frontend/actividades/controller/actividad_ver.php", "frontend/actividades/controller/planning_casa_modificar.php", "frontend/actividades/controller/planning_casa_nueva.php", "frontend/actividadtarifas/controller/tarifa_tipo_actividad_form.php"]
casos_uso: ["src\\actividades\\application\\ActividadQueDatos"]
tags: ["actividades", "actividad", "que", "datos"]
estado_revision: "revisado"
---

# Actividad Que Datos

Devuelve en `data.actividad_tipo_html` el bloque HTML del **selector de tipo de
actividad** (cascada sfsv → asistentes → actividad → nom_tipo) ya renderizado
via Twig (`frontend\actividades\helpers\ActividadTipo::getHtml`). Lo consumen
los controllers frontend que pintan ese widget (busqueda, ficha, planning,
tarifas).

Semantica de los parametros:

- `id_tipo_activ`: tipo (total o parcial, los niveles sin concretar van con `.`)
  con el que preseleccionar la cascada; numerico o cadena tipo `2[789]...`.
- `extendida` = `'t'`: variante de 2 digitos para actividad/nom_tipo (cursos sr/sg);
  usa `sactividad2` en lugar de `sactividad`.
- `perm_jefe` = `'t'`: el desplegable de asistentes muestra todos los posibles
  (igual que `que='buscar'` o jefe de calendario); si no, se intersecta con los
  permisos de oficina del usuario (est, sm, nax, agd, sg, des, sr, calendario).
- `sfsv`, `sasistentes`, `sactividad`/`sactividad2`, `snom_tipo`: textos
  preseleccionados de cada nivel.
- `que`: `'buscar'` ⇒ el onchange final es `fnjs_id_activ()`; otro valor ⇒
  `fnjs_act_id_activ()` (recarga de ficha).
- `para`: plantilla destino (`actividades` por defecto, `gestion`,
  `tipoactiv-tarifas`, `procesos`, `cambios`).
- `sfsv_all` = `'t'` (defecto si no llega): añade opcion en blanco (`.`) al
  desplegable sf/sv.

## Endpoint

- URL: `/src/actividades/actividad_que_datos`
- Metodos registrados: `GET, POST` (solo lee POST)
- Operacion: `consulta` (sin efectos; usa permisos de sesion para filtrar opciones)
- Controller: `src/actividades/infrastructure/ui/http/controllers/actividad_que_datos.php`

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Salida

- Helper: `ContestarJson::enviar`
- `data`: `{ "actividad_tipo_html": "<table>…</table><script>…</script>" }`
  (incluye el JS de la cascada, que llama a `/src/actividades/actividad_tipo_get`).

## Permisos

- No exige permiso para llamar; los permisos de oficina de la sesion
  (`$_SESSION['oPerm']`, jefe calendario) determinan **que opciones** se ofrecen
  en el desplegable de asistentes.

## Casos De Uso

- `src\actividades\application\ActividadQueDatos` (delega el render en
  `frontend\actividades\helpers\ActividadTipo`)

## Frontend Relacionado

- `frontend/actividades/controller/actividad_que.php`
- `frontend/actividades/controller/actividad_ver.php`
- `frontend/actividades/controller/planning_casa_modificar.php`
- `frontend/actividades/controller/planning_casa_nueva.php`
- `frontend/actividadtarifas/controller/tarifa_tipo_actividad_form.php`

## Revision Manual

- Revisado jun 2026 (lectura de controller + `ActividadQueDatos` + helper `ActividadTipo`):
  semantica de `extendida`/`perm_jefe`/`para`/`sfsv_all` y forma de `data` verificadas.
- Pendiente: ejemplos reales de request/response.
