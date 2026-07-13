---
id: "actividades.tipo_activ_form.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "actividades"
nombre: "Flujo - Formulario alta tipo actividad"
capacidad: "actividades.tipo_activ_form.gestionar"
pantallas_principales: ["actividades.pantalla.tipo_activ"]
fragmentos: []
acciones: ["crear"]
endpoints: ["/src/actividades/tipo_activ_form_nuevo"]
estado_revision: "revisado"
---

# Flujo - Formulario alta tipo actividad

Carga del formulario HTML para dar de alta un nuevo tipo (cascada de metadatos).

## Objetivo De Usuario

Pulsar *nuevo* en gestión de tipos y ver el formulario vacío con desplegables.

## Punto De Entrada

Pantalla `tipo_activ` → botón nuevo → `tipo_activ_form_nuevo`.

## Endpoints Del Flujo

- `/src/actividades/tipo_activ_form_nuevo`

## Ruta de menú

- **Legacy:** sistema > Configuración > gestión Tipos actividades.
- **Pills2:** ADMIN LOCAL > Gestión tipos de actividad.
