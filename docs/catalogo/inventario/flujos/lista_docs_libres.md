---
id: "inventario.lista_docs_libres.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "inventario"
nombre: "Flujo - Elegir un documento libre"
capacidad: "inventario.lista_docs_libres.gestionar"
pantallas_principales: ["inventario.pantalla.equipajes_docs_libres"]
fragmentos: ["inventario.pantalla.equipajes_docs_libres"]
acciones: ["ejecutar"]
endpoints: ["/src/inventario/lista_docs_libres"]
estado_revision: "revisado"
---

# Flujo - Gestionar Lista Docs Libres

## Objetivo De Usuario

Ver los documentos todavía sin meter en ninguna valija del equipaje, para añadirlos.

## Punto De Entrada

Dentro de **hacer equipajes**, en el formulario **añadir documento**.

## Fragmentos O Pantallas Auxiliares

- `inventario.pantalla.equipajes_docs_libres`

## Escenarios Inferidos

### Elegir un documento libre

Pasos propuestos:
1. En **hacer equipajes**, abrir una valija y pulsar **añadir documento**.
2. Elegir el **tipo de documento** entre los libres.
3. Pulsar **añadir** (o **cancel**).

Endpoints asociados:
- `/src/inventario/lista_docs_libres`

## Campos Y Acciones Detectadas En Pantalla

Campos:
- `html.sel[]`
- `post.id_equipaje`
- `post.id_tipo_doc`

Acciones JavaScript:
- Ninguna detectada.

## Endpoints Del Flujo

- `/src/inventario/lista_docs_libres`

## Errores Conocidos

No se han documentado errores en la capacidad.

## Ruta de menú

- **Legacy:** sin entrada de menú en el índice (fragmento/AJAX/dossier)
- **Pills2:** sin entrada de menú en el índice (fragmento/AJAX/dossier)
