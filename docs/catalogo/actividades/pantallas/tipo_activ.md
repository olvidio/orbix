---
id: "actividades.pantalla.tipo_activ"
tipo: "pantalla_frontend"
subtipo: "pantalla_principal"
modulo: "actividades"
nombre: "Gestión de tipos de actividad"
controller: "frontend/actividades/controller/tipo_activ.php"
vistas: ["frontend/actividades/view/tipo_activ.html.twig"]
fragmentos_frontend: []
endpoints: ["/src/actividades/tipo_activ_eliminar", "/src/actividades/tipo_activ_form_modificar", "/src/actividades/tipo_activ_form_nuevo", "/src/actividades/tipo_activ_lista", "/src/actividades/tipo_activ_nuevo", "/src/actividades/tipo_activ_update"]
capacidades: ["actividades.tipo_activ.gestionar", "actividades.tipo_activ_form.gestionar", "actividades.tipo_activ_form_modificar.gestionar"]
campos: ["form.id_tipo_activ"]
acciones: []
estado_revision: "revisado"
---

# Gestión de tipos de actividad

Pantalla de **administración del catálogo de tipos** (código compuesto sf/sv +
asistentes + actividad + nombre). Carga la tabla vía AJAX (`tipo_activ_lista`),
permite alta (`tipo_activ_form_nuevo` → `tipo_activ_nuevo`), edición
(`tipo_activ_form_modificar` → `tipo_activ_update`) y borrado (`tipo_activ_eliminar`
con confirmación). Renderizada con `ViewNewTwig` + `tipo_activ.html.twig`.

## Tipo

- Subtipo: `pantalla_principal`
- Controller: `frontend/actividades/controller/tipo_activ.php`
- Vista: `frontend/actividades/view/tipo_activ.html.twig`

## Endpoints Usados

- `/src/actividades/tipo_activ_lista` — HTML tabla inicial
- `/src/actividades/tipo_activ_form_nuevo` / `tipo_activ_form_modificar` — formularios
- `/src/actividades/tipo_activ_nuevo` / `tipo_activ_update` / `tipo_activ_eliminar` — mutaciones

## Manual De Usuario

Pantalla de configuración local: listar tipos, crear uno nuevo con la cascada de
metadatos, renombrar o eliminar (si no está en uso).

## Ruta de menú

- **Legacy:** sistema > Configuración > gestión Tipos actividades.
- **Pills2:** ADMIN LOCAL > Gestión tipos de actividad; sistema > Configuración >
  gestión Tipos actividades.
