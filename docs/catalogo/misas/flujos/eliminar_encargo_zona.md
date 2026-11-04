---
id: "misas.eliminar_encargo_zona.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "misas"
nombre: "Flujo - Eliminar un encargo de zona"
capacidad: "misas.eliminar_encargo_zona.gestionar"
pantallas_principales: ["misas.pantalla.ver_encargos_zona"]
fragmentos: ["misas.pantalla.ver_encargos_zona"]
acciones: ["ejecutar"]
endpoints: ["/src/misas/eliminar_encargo_zona"]
estado_revision: "revisado"
---

# Flujo - Eliminar encargo zona

## Objetivo De Usuario

Eliminar un encargo de zona.

## Punto De Entrada

Menú **dre > Misas > Modificar encargos** (Pills2: ATENCIÓN SACD > Gestión de misas > Modificar encargos).

## Fragmentos O Pantallas Auxiliares

- `misas.pantalla.ver_encargos_zona`

## Escenarios Inferidos

### Eliminar un encargo de zona

Pasos propuestos:
1. Abrir el encargo y pulsar **Eliminar encargo**.
2. Confirmar.

Endpoints asociados:
- `/src/misas/eliminar_encargo_zona`

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

- `/src/misas/eliminar_encargo_zona`

## Errores Conocidos

- `No se encuentra el encargo %d`
- `<repositorio getErrorTxt()>`

## Ruta de menú

- **Legacy:** dre > Misas > Modificar encargos
- **Pills2:** ATENCIÓN SACD > Gestión de misas > Modificar encargos
