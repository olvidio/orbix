---
id: "actividades.tipo_activ_metadata.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "actividades"
nombre: "Flujo - Elegir los niveles de un tipo"
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

Al cambiar un nivel en el formulario de tipos, actualizar los desplegables siguientes.

## Punto De Entrada

Formulario de tipos de actividad.

## Escenarios Inferidos

### Elegir los niveles de un tipo

Pasos propuestos:
1. En el formulario de tipos, elegir el primer nivel.
2. Los desplegables siguientes se actualizan con las opciones que corresponden.

Endpoints asociados:
- `/src/actividades/tipo_activ_metadata`

## Endpoints Del Flujo

- `/src/actividades/tipo_activ_metadata`

## Ruta de menú

- **Legacy:** sistema > Configuración > gestión Tipos actividades.
- **Pills2:** ADMIN LOCAL > Gestión tipos de actividad.
