---
id: "actividades.tipo_activ_form_modificar.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "actividades"
nombre: "Flujo - Formulario editar tipo actividad"
capacidad: "actividades.tipo_activ_form_modificar.gestionar"
pantallas_principales: ["actividades.pantalla.tipo_activ"]
fragmentos: []
acciones: ["ejecutar"]
endpoints: ["/src/actividades/tipo_activ_form_modificar"]
estado_revision: "revisado"
---

# Flujo - Formulario editar tipo actividad

Carga del formulario de edición para un `id_tipo_activ` seleccionado en la tabla.

## Objetivo De Usuario

Elegir tipo en la lista y abrir formulario con nombre actual para modificar.

## Punto De Entrada

Pantalla `tipo_activ` → enlace modificar → `tipo_activ_form_modificar`.

## Endpoints Del Flujo

- `/src/actividades/tipo_activ_form_modificar`

## Ruta de menú

- **Legacy:** sistema > Configuración > gestión Tipos actividades.
- **Pills2:** ADMIN LOCAL > Gestión tipos de actividad.
