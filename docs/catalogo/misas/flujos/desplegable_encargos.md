---
id: "misas.desplegable_encargos.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "misas"
nombre: "Flujo - Elegir encargo de la zona"
capacidad: "misas.desplegable_encargos.gestionar"
pantallas_principales: ["misas.pantalla.ver_encargos_centros"]
fragmentos: ["misas.pantalla.ver_encargos_centros"]
acciones: ["ejecutar"]
endpoints: ["/src/misas/desplegable_encargos"]
estado_revision: "revisado"
---

# Flujo - Desplegable encargos

## Objetivo De Usuario

Ofrecer los encargos de la zona elegida en la ventana de encargo-centro.

## Punto De Entrada

Ventana **nuevo encargo visible para un centro**.

## Fragmentos O Pantallas Auxiliares

- `misas.pantalla.ver_encargos_centros`

## Escenarios Inferidos

### Elegir encargo de la zona

Pasos propuestos:
1. En la ventana de encargo visible para un centro, elegir la zona.
2. El desplegable **Encargo** se rellena con los encargos de esa zona.

Endpoints asociados:
- `/src/misas/desplegable_encargos`

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

- `/src/misas/desplegable_encargos`

## Errores Conocidos

- _(ninguno documentado)_

## Ruta de menú

- **Legacy:** dre > Misas > Encargos centro
- **Pills2:** ATENCIÓN SACD > Gestión de misas > Encargos ctr
