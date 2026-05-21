---
id: "actividadessacd.pantalla.activ_sacd"
tipo: "pantalla_frontend"
subtipo: "pantalla"
modulo: "actividadessacd"
nombre: "Activ Sacd"
controller: "frontend/actividadessacd/controller/activ_sacd.php"
vistas: ["frontend/actividadessacd/view/activ_sacd.phtml"]
fragmentos_frontend: []
endpoints: ["/src/actividadessacd/", "/src/actividadessacd/lista_actividades_sacd_data", "/src/actividadessacd/sacd_asignar", "/src/actividadessacd/sacd_eliminar", "/src/actividadessacd/sacd_reordenar", "/src/actividadessacd/sacds_disponibles_data", "/src/actividadessacd/sacds_encargados_data", "/src/actividadessacd/solapes_sacd_data"]
capacidades: ["actividadessacd.lista_actividades_sacd.gestionar", "actividadessacd.sacd.gestionar", "actividadessacd.sacd_asignar.gestionar", "actividadessacd.sacd_reordenar.gestionar", "actividadessacd.sacds_disponibles.gestionar", "actividadessacd.sacds_encargados.gestionar", "actividadessacd.solapes_sacd.gestionar"]
campos: ["form.empiezamax", "form.empiezamin", "form.periodo", "form.tipo", "form.year", "post.periodo", "post.tipo", "post.year"]
acciones: ["fnjs_actualizar_activ", "fnjs_asignar_sacd", "fnjs_cambiar_sacd", "fnjs_cerrar", "fnjs_construir_celda_sacds", "fnjs_construir_leyenda", "fnjs_construir_tabla_disponibles", "fnjs_construir_tabla_lista", "fnjs_construir_tabla_solapes", "fnjs_enviar", "fnjs_esc", "fnjs_left_side_hide", "fnjs_nuevo_sacd", "fnjs_orden", "fnjs_parse_rta", "fnjs_ver"]
estado_revision: "generado"
---

# Activ Sacd

Pantalla principal del modulo `actividadessacd`.

## Tipo

- Subtipo: `pantalla`
- Controller: `frontend/actividadessacd/controller/activ_sacd.php`

## Vistas Relacionadas

- `frontend/actividadessacd/view/activ_sacd.phtml`

## Fragmentos Frontend Relacionados

No se han detectado controladores frontend relacionados.

## Endpoints Usados

- `/src/actividadessacd/`
- `/src/actividadessacd/lista_actividades_sacd_data`
- `/src/actividadessacd/sacd_asignar`
- `/src/actividadessacd/sacd_eliminar`
- `/src/actividadessacd/sacd_reordenar`
- `/src/actividadessacd/sacds_disponibles_data`
- `/src/actividadessacd/sacds_encargados_data`
- `/src/actividadessacd/solapes_sacd_data`

## Capacidades Relacionadas

- `actividadessacd.lista_actividades_sacd.gestionar`
- `actividadessacd.sacd.gestionar`
- `actividadessacd.sacd_asignar.gestionar`
- `actividadessacd.sacd_reordenar.gestionar`
- `actividadessacd.sacds_disponibles.gestionar`
- `actividadessacd.sacds_encargados.gestionar`
- `actividadessacd.solapes_sacd.gestionar`

## Campos Detectados

- `form.empiezamax`
- `form.empiezamin`
- `form.periodo`
- `form.tipo`
- `form.year`
- `post.periodo`
- `post.tipo`
- `post.year`

## Acciones Detectadas

- `fnjs_actualizar_activ`
- `fnjs_asignar_sacd`
- `fnjs_cambiar_sacd`
- `fnjs_cerrar`
- `fnjs_construir_celda_sacds`
- `fnjs_construir_leyenda`
- `fnjs_construir_tabla_disponibles`
- `fnjs_construir_tabla_lista`
- `fnjs_construir_tabla_solapes`
- `fnjs_enviar`
- `fnjs_esc`
- `fnjs_left_side_hide`
- `fnjs_nuevo_sacd`
- `fnjs_orden`
- `fnjs_parse_rta`
- `fnjs_ver`

## Manual De Usuario

Pendiente de redactar: objetivo de la pantalla, pasos habituales, validaciones y errores comunes.

## Revision Manual

- Confirmar si es pantalla principal o fragmento AJAX.
- Completar nombre funcional orientado a usuario.
- Revisar campos obligatorios y significado de cada accion.
- Confirmar si las capacidades relacionadas son correctas.
