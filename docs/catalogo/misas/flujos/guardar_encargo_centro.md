---
id: "misas.guardar_encargo_centro.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "misas"
nombre: "Flujo - Guardar un encargo visible para un centro"
capacidad: "misas.guardar_encargo_centro.gestionar"
pantallas_principales: ["misas.pantalla.ver_encargos_centros"]
fragmentos: ["misas.pantalla.ver_encargos_centros"]
acciones: ["ejecutar"]
endpoints: ["/src/misas/guardar_encargo_centro"]
estado_revision: "revisado"
---

# Flujo - Guardar encargo centro

## Objetivo De Usuario

Hacer visible un encargo de zona en un centro, o cambiarlo.

## Punto De Entrada

Menú **dre > Misas > Encargos centro** (Pills2: ATENCIÓN SACD > Gestión de misas > Encargos ctr).

## Fragmentos O Pantallas Auxiliares

- `misas.pantalla.ver_encargos_centros`

## Escenarios Inferidos

### Guardar un encargo visible para un centro

Pasos propuestos:
1. Elegir zona, encargo y centro.
2. Pulsar **Grabar**.

Endpoints asociados:
- `/src/misas/guardar_encargo_centro`

## Campos Y Acciones Detectadas En Pantalla

Campos:
- `form.id_ctr`
- `form.id_enc`
- `form.id_item`
- `form.id_zona`
- `html.nuevo`
- `post.id_zona`

Acciones JavaScript:
- `fnjs_construir_desplegable`
- `fnjs_nuevo`
- `fnjs_prepara_select_encargo`
- `fnjs_refresh_grid`

## Endpoints Del Flujo

- `/src/misas/guardar_encargo_centro`

## Errores Conocidos

- `No se encuentra el encargo-centro %s`
- `<repositorio getErrorTxt()>`

## Ruta de menú

- **Legacy:** dre > Misas > Encargos centro
- **Pills2:** ATENCIÓN SACD > Gestión de misas > Encargos ctr
