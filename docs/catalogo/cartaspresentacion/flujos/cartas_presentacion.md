---
id: "cartaspresentacion.cartas_presentacion.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "cartaspresentacion"
nombre: "Flujo - Gestionar Cartas Presentacion"
capacidad: "cartaspresentacion.cartas_presentacion.gestionar"
pantallas_principales: []
fragmentos: ["cartaspresentacion.pantalla.cartas_presentacion_lista"]
acciones: ["listar"]
endpoints: ["/src/cartaspresentacion/cartas_presentacion_lista_data"]
estado_revision: "revisado"
---

# Flujo - Consultar listado de cartas de presentación

Visualización del listado agrupado de cartas (solo lectura) desde el menú o desde buscar.

## Objetivo De Usuario

Consultar todas las cartas de presentación organizadas por tipo de labor, delegación y población.

## Punto De Entrada

- Menú **lista dl** o **lista todo** → `cartas_presentacion_lista.php` con `que=lista_dl` o
  `que=lista_todo`.
- Pantalla **buscar** → mismo fragmento con `que=get` y filtros.

## Escenarios

### Listar (menú)

1. Abrir lista dl (solo mi delegación) o lista todo (todas las delegaciones).
2. El sistema muestra tablas HTML agrupadas con datos de contacto.

### Listar (buscar)

1. Desde buscar, rellenar filtros y pulsar **buscar**.
2. Los resultados se cargan en `#resultados`.

## Endpoints Del Flujo

- `/src/cartaspresentacion/cartas_presentacion_lista_data`

## Errores Conocidos

- Centros con `tipo_labor` mal configurado aparecen en aviso al pie (`html_errores`), no como error AJAX.

## Ruta de menú

- **Legacy:** scdl > direcciones > cartas presentacion > lista dl / lista todo
- **Pills2:** scdl > direcciones > cartas presentacion > lista dl / lista todo
