---
id: "actividades.tipo_activ_metadata.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "actividades"
nombre: "Flujo - Metadatos cascada tipo actividad"
capacidad: "actividades.tipo_activ_metadata.gestionar"
pantallas_principales: ["actividades.pantalla.tipo_activ"]
fragmentos: []
acciones: ["ejecutar"]
endpoints: ["/src/actividades/tipo_activ_metadata"]
estado_revision: "revisado"
---

# Flujo - Metadatos cascada tipo actividad

Opciones AJAX para los desplegables del formulario de alta/edición de tipos (sf/sv,
asistentes, actividad, nombre).

## Objetivo De Usuario

Al cambiar un nivel en el formulario de tipos, actualizar los siguientes desplegables.

## Punto De Entrada

JS en `tipo_activ.html.twig` al editar/crear tipos.

## Endpoints Del Flujo

- `/src/actividades/tipo_activ_metadata`

## Ruta de menú

- **Legacy:** sistema > Configuración > gestión Tipos actividades.
- **Pills2:** ADMIN LOCAL > Gestión tipos de actividad.
