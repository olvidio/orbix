---
id: "inventario.lista_tipo_doc.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "inventario"
nombre: "Flujo - Elegir el tipo de documento"
capacidad: "inventario.lista_tipo_doc.gestionar"
pantallas_principales: ["inventario.pantalla.docs_asignar_que"]
fragmentos: ["inventario.pantalla.docs_asignar_que", "inventario.pantalla.equipajes_form_add"]
acciones: ["ejecutar"]
endpoints: ["/src/inventario/lista_tipo_doc"]
estado_revision: "revisado"
---

# Flujo - Gestionar Lista Tipo Doc

## Objetivo De Usuario

Elegir el tipo de documento con el que se va a trabajar antes de asignarlo o de consultar a quién se ha asignado.

## Punto De Entrada

Menú **Inventario > documentos > asignar documento**. El desplegable de tipos de documento se carga solo al abrir la pantalla.

## Fragmentos O Pantallas Auxiliares

- `inventario.pantalla.docs_asignar_que`
- `inventario.pantalla.equipajes_form_add`

## Escenarios Inferidos

### Elegir el tipo de documento

Pasos propuestos:
1. Abrir **asignar documento** desde el menú.
2. Elegir el documento en el desplegable **seleccione un tipo de documento**.
3. Pulsar **lista de asignados** para ver los centros que ya lo tienen, **lista de NO asignados** para ver los que no, o uno de los dos botones de asignación (**Asignar a centros (sin especificar lugar)** / **Asignar a dlb/casa (especificar lugar)**).

Endpoints asociados:
- `/src/inventario/lista_tipo_doc`

## Campos Y Acciones Detectadas En Pantalla

Campos:
- `form.id_tipo_doc`
- `form.sel`
- `html.okay`
- `html.okay2`
- `html.okay3`
- `html.okay4`
- `post.id_equipaje`
- `post.id_grupo`
- `post.id_item_egm`
- `post.id_tipo_doc`
- `post.inventario`

Acciones JavaScript:
- `fnjs_add_doc`
- `fnjs_cerrar`
- `fnjs_docs_libres`
- `fnjs_enviar_formulario`
- `fnjs_go`
- `fnjs_left_side_hide`

## Endpoints Del Flujo

- `/src/inventario/lista_tipo_doc`

## Errores Conocidos

No se han documentado errores en la capacidad.

## Ruta de menú

- **Legacy:** scdl > Inventario > documentos > asignar documento
- **Pills2:** sin entrada de menú en el índice
