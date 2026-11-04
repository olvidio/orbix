---
id: "actividades.tipo_activ_form_modificar.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "actividades"
nombre: "Flujo - Modificar un tipo de actividad"
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

Modificar el nombre de un tipo de actividad existente.

## Punto De Entrada

Menú **sistema > Configuración > gestión Tipos actividades**.

## Escenarios Inferidos

### Modificar un tipo de actividad

Pasos propuestos:
1. En **gestión Tipos actividades**, pulsar modificar en el tipo.
2. Se abre el formulario con el nombre actual.
3. Cambiarlo y guardar.

Endpoints asociados:
- `/src/actividades/tipo_activ_form_modificar`

## Endpoints Del Flujo

- `/src/actividades/tipo_activ_form_modificar`

## Ruta de menú

- **Legacy:** sistema > Configuración > gestión Tipos actividades.
- **Pills2:** ADMIN LOCAL > Gestión tipos de actividad.
