---
id: "misas.guardar_encargo_zona.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "misas"
nombre: "Flujo - Gestionar Guardar Encargo Zona"
capacidad: "misas.guardar_encargo_zona.gestionar"
pantallas_principales: []
fragmentos: ["misas.pantalla.ver_encargos_zona"]
acciones: ["ejecutar"]
endpoints: ["/src/misas/guardar_encargo_zona"]
estado_revision: "revisado"
---

# Flujo - Guardar encargo zona

## Objetivo De Usuario

Crea o actualiza un Encargo del grupo ZONAS_MISAS (id_enc=0 → alta) y devuelve id y nombre del centro.

## Punto De Entrada

Menú Legacy: dre > Misas > Modificar encargos. Pills2: ATENCIÓN SACD > Gestión de misas > Modificar encargos.

## Fragmentos O Pantallas Auxiliares

- `misas.pantalla.ver_encargos_zona`

## Escenarios Inferidos

### Ejecutar

Pasos propuestos:
1. Revisar manualmente los pasos de esta accion.

Endpoints asociados:
- Ninguno inferido para esta accion.

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
