---
id: "cartaspresentacion.pantalla.cartas_presentacion_buscar"
tipo: "pantalla_frontend"
subtipo: "fragmento_ajax"
modulo: "cartaspresentacion"
nombre: "Cartas Presentacion Buscar"
controller: "frontend/cartaspresentacion/controller/cartas_presentacion_buscar.php"
vistas: ["frontend/cartaspresentacion/view/cartas_presentacion_buscar.phtml"]
fragmentos_frontend: ["frontend/cartaspresentacion/controller/cartas_presentacion_lista.php"]
endpoints: ["/src/cartaspresentacion/cartas_presentacion_buscar_data"]
capacidades: ["cartaspresentacion.cartas_presentacion_buscar.gestionar"]
campos: ["html.btn_ok", "html.poblacion", "html.region", "html.pais", "html.dl"]
acciones: ["fnjs_buscar", "fnjs_enviar", "fnjs_enviar_formulario"]
estado_revision: "revisado"
---

# Cartas Presentacion Buscar

Formulario de búsqueda de cartas de presentación por población, región, país y delegación.

## Tipo

- Subtipo: `fragmento_ajax`
- Controller: `frontend/cartaspresentacion/controller/cartas_presentacion_buscar.php`

## Vistas Relacionadas

- `frontend/cartaspresentacion/view/cartas_presentacion_buscar.phtml`

## Fragmentos Frontend Relacionados

- `frontend/cartaspresentacion/controller/cartas_presentacion_lista.php` (resultados en `#resultados`)

## Endpoints Usados

- `/src/cartaspresentacion/cartas_presentacion_buscar_data` (bootstrap de desplegables)

## Campos Detectados

- `html.poblacion` — texto libre
- `html.region`, `html.pais`, `html.dl` — desplegables
- `html.btn_ok` — botón buscar

## Acciones Detectadas

- `fnjs_buscar` — envía el formulario a `cartas_presentacion_lista.php` con `que=get`

## Manual De Usuario

1. Rellenar uno o más filtros (población, región, país, delegación H).
2. Pulsar **buscar**: los resultados aparecen debajo en `#resultados` como listado HTML agrupado.

## Ruta de menú

- **Legacy:** scdl > direcciones > cartas presentacion > buscar
- **Pills2:** scdl > direcciones > cartas presentacion > buscar
