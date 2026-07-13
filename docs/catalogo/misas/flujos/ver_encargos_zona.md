---
id: "misas.ver_encargos_zona.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "misas"
nombre: "Flujo - Gestionar Ver Encargos Zona"
capacidad: "misas.ver_encargos_zona.gestionar"
pantallas_principales: []
fragmentos: ["misas.pantalla.ver_encargos_zona"]
acciones: ["obtener_datos"]
endpoints: ["/src/misas/ver_encargos_zona_data"]
estado_revision: "revisado"
---

# Flujo - Ver encargos zona

## Objetivo De Usuario

Devuelve encargos 8100+ de una zona ordenados para SlickGrid y datos del modal de edición.

## Punto De Entrada

Menú Legacy: dre > Misas > Modificar encargos. Pills2: ATENCIÓN SACD > Gestión de misas > Modificar encargos.

## Fragmentos O Pantallas Auxiliares

- `misas.pantalla.ver_encargos_zona`

## Escenarios Inferidos

### Obtener Datos

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

- `/src/misas/ver_encargos_zona_data`

## Errores Conocidos

- _(ninguno documentado)_

## Ruta de menú

- **Legacy:** dre > Misas > Modificar encargos
- **Pills2:** ATENCIÓN SACD > Gestión de misas > Modificar encargos
