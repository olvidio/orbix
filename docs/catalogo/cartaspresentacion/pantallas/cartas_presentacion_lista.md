---
id: "cartaspresentacion.pantalla.cartas_presentacion_lista"
tipo: "pantalla_frontend"
subtipo: "fragmento_ajax"
modulo: "cartaspresentacion"
nombre: "Cartas Presentacion Lista"
controller: "frontend/cartaspresentacion/controller/cartas_presentacion_lista.php"
vistas: []
fragmentos_frontend: []
endpoints: ["/src/cartaspresentacion/cartas_presentacion_lista_data"]
capacidades: ["cartaspresentacion.cartas_presentacion.gestionar"]
campos: ["post.dl", "post.pais", "post.poblacion", "post.que", "post.region"]
acciones: []
estado_revision: "revisado"
---

# Cartas Presentacion Lista

Fragmento AJAX que imprime el listado agrupado de cartas de presentación en HTML.

## Tipo

- Subtipo: `fragmento_ajax`
- Controller: `frontend/cartaspresentacion/controller/cartas_presentacion_lista.php`

## Vistas Relacionadas

No tiene vista PHTML propia; devuelve HTML directamente vía `AjaxJsonSupport::html`.

## Endpoints Usados

- `/src/cartaspresentacion/cartas_presentacion_lista_data`

## Capacidades Relacionadas

- `cartaspresentacion.cartas_presentacion.gestionar`

## Campos Detectados

- `post.que` — `lista_dl`, `lista_todo` o `get`
- `post.poblacion`, `post.pais`, `post.region`, `post.dl` — filtros en modo `get`

## Manual De Usuario

Se abre desde el menú (lista dl / lista todo) o como destino del formulario buscar. Muestra tablas
agrupadas por tipo de labor, delegación y población con los datos de contacto de cada carta. Si hay
centros con `tipo_labor` mal configurado, aparece un aviso al final.

## Ruta de menú

Dos entradas de menú según `que`:

- **Legacy:** scdl > direcciones > cartas presentacion > lista dl (`que=lista_dl`)
- **Pills2:** scdl > direcciones > cartas presentacion > lista dl

- **Legacy:** scdl > direcciones > cartas presentacion > lista todo (`que=lista_todo`)
- **Pills2:** scdl > direcciones > cartas presentacion > lista todo

Desde buscar se invoca con `que=get` (sin entrada de menú propia).
