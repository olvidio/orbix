---
id: "actividadplazas.pantalla.gestion_plazas"
tipo: "pantalla_frontend"
subtipo: "fragmento_ajax"
modulo: "actividadplazas"
nombre: "Gestion Plazas"
controller: "frontend/actividadplazas/controller/gestion_plazas.php"
vistas: ["frontend/actividadplazas/view/gestion_plazas.phtml"]
fragmentos_frontend: ["frontend/actividadplazas/controller/gestion_plazas.php"]
endpoints: ["/src/actividadplazas/gestion_plazas_data", "/src/actividadplazas/gestion_plazas_update"]
capacidades: ["actividadplazas.gestion_plazas.gestionar"]
campos: ["form.colName", "form.data", "html.refresh", "post.empiezamax", "post.empiezamin", "post.id_tipo_activ", "post.periodo", "post.refresh", "post.sactividad", "post.sactividad2", "post.sasistentes", "post.year"]
acciones: ["fnjs_buscar", "fnjs_enviar_formulario", "fnjs_left_side_hide"]
estado_revision: "revisado"
---

# Gestion Plazas

Pantalla principal del módulo `actividadplazas`: cuadro de distribución de plazas por delegación del
grupo de estudios (totales, concedidas y pedidas) para un periodo y tipo de actividad, con edición
inline por celda.

## Tipo

- Subtipo: `fragmento_ajax`
- Controller: `frontend/actividadplazas/controller/gestion_plazas.php`

## Vistas Relacionadas

- `frontend/actividadplazas/view/gestion_plazas.phtml`

## Fragmentos Frontend Relacionados

- `frontend/actividadplazas/controller/gestion_plazas.php`

## Endpoints Usados

- `/src/actividadplazas/gestion_plazas_data`
- `/src/actividadplazas/gestion_plazas_update`

## Capacidades Relacionadas

- `actividadplazas.gestion_plazas.gestionar`

## Campos Detectados

- `form.colName`
- `form.data`
- `html.refresh`
- `post.empiezamax`
- `post.empiezamin`
- `post.id_tipo_activ`
- `post.periodo`
- `post.refresh`
- `post.sactividad`
- `post.sactividad2`
- `post.sasistentes`
- `post.year`

## Acciones Detectadas

- `fnjs_buscar`
- `fnjs_enviar_formulario`
- `fnjs_left_side_hide`

## Manual De Usuario

Muestra una tabla editable (`TablaEditable`) con las actividades del periodo por filas y, por cada
delegación del grupo de estudios, las columnas de plazas concedidas (`-c`) y pedidas (`-p`), más las
plazas totales de la actividad. Pasos habituales:

1. Ajustar el periodo (año + periodo, o rango de fechas) y pulsar **Buscar** (`fnjs_buscar`) para
   recargar el cuadro.
2. Editar una celda con doble clic; solo son editables las celdas de mi delegación según quién
   organiza la actividad.
3. El cambio se guarda al instante contra `gestion_plazas_update`.

Si la actividad aún no tiene plazas en el calendario común, al editar concedidas/pedidas aparece el
aviso explicando que primero hay que dar de alta plazas en el calendario de la actividad.

## Ruta de menú

- **Legacy:** vsm > ca > Gestión de plazas (y variantes por perfil/tipo: dagd, vsg, vest…)
- **Pills2:** ACTIVIDADES > Gestión de plazas y peticiones > Distribución plazas ca n entre r/dl (y variantes por tipo/colectivo)
