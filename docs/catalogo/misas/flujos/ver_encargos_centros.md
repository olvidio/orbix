---
id: "misas.ver_encargos_centros.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "misas"
nombre: "Flujo - Hacer visible un encargo para un centro"
capacidad: "misas.ver_encargos_centros.gestionar"
pantallas_principales: ["misas.pantalla.ver_encargos_centros"]
fragmentos: ["misas.pantalla.ver_encargos_centros"]
acciones: ["obtener_datos"]
endpoints: ["/src/misas/ver_encargos_centros_data"]
estado_revision: "revisado"
---

# Flujo - Ver encargos centros

## Objetivo De Usuario

Ver y modificar qué encargos de la zona aparecen en el plan de cada centro.

## Punto De Entrada

Menú **dre > Misas > Encargos centro** (Pills2: ATENCIÓN SACD > Gestión de misas > Encargos ctr).

## Fragmentos O Pantallas Auxiliares

- `misas.pantalla.ver_encargos_centros`

## Escenarios Inferidos

### Hacer visible un encargo para un centro

Pasos propuestos:
1. En **Encargos centro**, elegir la zona.
2. Pulsar **nuevo**; en **Zona del encargo**, **Encargo** y **Centro** elegir los valores y pulsar **Grabar**.
3. Para modificarlo, pulsarlo y **Grabar**; para quitarlo, **Eliminar encargo**.
4. Si falta encargo o centro aparece «Falta encargo o centro».

Endpoints asociados:
- `/src/misas/ver_encargos_centros_data`

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

- `/src/misas/ver_encargos_centros_data`

## Errores Conocidos

- _(ninguno documentado)_

## Ruta de menú

- **Legacy:** dre > Misas > Encargos centro
- **Pills2:** ATENCIÓN SACD > Gestión de misas > Encargos ctr
