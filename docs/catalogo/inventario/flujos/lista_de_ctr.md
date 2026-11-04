---
id: "inventario.lista_de_ctr.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "inventario"
nombre: "Flujo - Elegir el centro de origen y de destino"
capacidad: "inventario.lista_de_ctr.gestionar"
pantallas_principales: ["inventario.pantalla.traslado_doc_que"]
fragmentos: ["inventario.pantalla.traslado_doc_que"]
acciones: ["ejecutar"]
endpoints: ["/src/inventario/lista_de_ctr"]
estado_revision: "revisado"
---

# Flujo - Gestionar Lista De Ctr

## Objetivo De Usuario

Cargar la lista de centros disponibles como origen y destino en el traslado de documentos.

## Punto De Entrada

Dentro de **Inventario > inventarios > traslado de doc**: se carga sola al abrir la pantalla.

## Fragmentos O Pantallas Auxiliares

- `inventario.pantalla.traslado_doc_que`

## Escenarios Inferidos

### Elegir el centro de origen y de destino

Pasos propuestos:
1. Abrir **traslado de doc**.
2. Elegir el centro de origen en su desplegable.
3. Elegir el centro de destino en el segundo desplegable.

Endpoints asociados:
- `/src/inventario/lista_de_ctr`

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

- `/src/inventario/lista_de_ctr`

## Errores Conocidos

No se han documentado errores en la capacidad.

## Ruta de menú

- **Legacy:** scdl > Inventario > inventarios > traslado de doc
- **Pills2:** sin entrada de menú en el índice
