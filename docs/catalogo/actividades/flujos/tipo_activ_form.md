---
id: "actividades.tipo_activ_form.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "actividades"
nombre: "Flujo - Crear un tipo de actividad"
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

Crear un tipo de actividad nuevo.

## Punto De Entrada

Menú **sistema > Configuración > gestión Tipos actividades** (Pills2: ADMIN LOCAL > Gestión tipos de actividad).

## Escenarios Inferidos

### Crear un tipo de actividad

Pasos propuestos:
1. Entrar en **gestión Tipos actividades** y pulsar **nuevo**.
2. Se abre el formulario vacío con los desplegables por niveles.
3. Rellenar el nombre y los niveles y guardar.

Endpoints asociados:
- `/src/actividades/tipo_activ_form_nuevo`

## Endpoints Del Flujo

- `/src/actividades/tipo_activ_form_nuevo`

## Ruta de menú

- **Legacy:** sistema > Configuración > gestión Tipos actividades.
- **Pills2:** ADMIN LOCAL > Gestión tipos de actividad.
