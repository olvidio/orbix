---
id: "inventario.lista_lugares_de_ubi.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "inventario"
nombre: "Flujo - Elegir el lugar dentro del centro"
capacidad: "inventario.lista_lugares_de_ubi.gestionar"
pantallas_principales: ["inventario.pantalla.traslado_doc_que"]
fragmentos: ["inventario.pantalla.traslado_doc_que"]
acciones: ["ejecutar"]
endpoints: ["/src/inventario/lista_lugares_de_ubi"]
estado_revision: "revisado"
---

# Flujo - Gestionar Lista Lugares De Ubi

## Objetivo De Usuario

Cargar los lugares (estancias) de un centro cuando se elige como origen o destino de un traslado.

## Punto De Entrada

Dentro de **traslado de doc**: se carga sola al elegir un centro.

## Fragmentos O Pantallas Auxiliares

- `inventario.pantalla.traslado_doc_que`

## Escenarios Inferidos

### Elegir el lugar dentro del centro

Pasos propuestos:
1. En **traslado de doc**, elegir un centro (origen o destino).
2. Aparece el desplegable **seleccione un lugar** con los lugares de ese centro.
3. Si falla la carga se muestra «Error al cargar lugares de origen/destino»; volver a elegir el centro.

Endpoints asociados:
- `/src/inventario/lista_lugares_de_ubi`

## Campos Y Acciones Detectadas En Pantalla

Campos:
- `form.id_ubi`
- `form.id_ubi_new`
- `form.sel`
- `html.ok`

Acciones JavaScript:
- `fnjs_busca_docs`
- `fnjs_busca_lugares`
- `fnjs_busca_lugares_destino`
- `fnjs_busca_lugares_origen`
- `fnjs_crearSelectDesdeJson`
- `fnjs_guardar`
- `fnjs_put_desplegable_lugares`

## Endpoints Del Flujo

- `/src/inventario/lista_lugares_de_ubi`

## Errores Conocidos

No se han documentado errores en la capacidad.

## Ruta de menú

- **Legacy:** scdl > Inventario > inventarios > traslado de doc
- **Pills2:** sin entrada de menú en el índice
