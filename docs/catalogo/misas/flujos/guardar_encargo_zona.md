---
id: "misas.guardar_encargo_zona.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "misas"
nombre: "Flujo - Guardar un encargo de zona"
capacidad: "misas.guardar_encargo_zona.gestionar"
pantallas_principales: ["misas.pantalla.ver_encargos_zona"]
fragmentos: ["misas.pantalla.ver_encargos_zona"]
acciones: ["ejecutar"]
endpoints: ["/src/misas/guardar_encargo_zona"]
estado_revision: "revisado"
---

# Flujo - Guardar encargo zona

## Objetivo De Usuario

Crear o actualizar un encargo de zona.

## Punto De Entrada

Menú **dre > Misas > Modificar encargos** (Pills2: ATENCIÓN SACD > Gestión de misas > Modificar encargos).

## Fragmentos O Pantallas Auxiliares

- `misas.pantalla.ver_encargos_zona`

## Escenarios Inferidos

### Guardar un encargo de zona

Pasos propuestos:
1. Rellenar el formulario del encargo (el botón **generar** propone el nombre «tipo (lugar)») y pulsar **Grabar**.
2. Si el encargo ya no existe aparece «No se encuentra el encargo».

Endpoints asociados:
- `/src/misas/guardar_encargo_zona`

## Campos Y Acciones Detectadas En Pantalla

Campos:
- `form.descripcion_lugar`
- `form.encargo`
- `form.id_enc`
- `form.id_tipo_enc`
- `form.id_ubi`
- `form.id_zona`
- `form.idioma_enc`
- `form.observ`
- `form.orden`
- `form.prioridad`
- `html.nuevo`
- `post.id_zona`
- `post.orden`

Acciones JavaScript:
- `fnjs_generarNomEnc`
- `fnjs_nuevo`
- `fnjs_refresh_grid`

## Endpoints Del Flujo

- `/src/misas/guardar_encargo_zona`

## Errores Conocidos

- `No se encuentra el encargo %d`
- `<repositorio getErrorTxt()>`

## Ruta de menú

- **Legacy:** dre > Misas > Modificar encargos
- **Pills2:** ATENCIÓN SACD > Gestión de misas > Modificar encargos
