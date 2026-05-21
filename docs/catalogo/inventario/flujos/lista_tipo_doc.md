---
id: "inventario.lista_tipo_doc.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "inventario"
nombre: "Flujo - Gestionar Lista Tipo Doc"
capacidad: "inventario.lista_tipo_doc.gestionar"
pantallas_principales: []
fragmentos: ["inventario.pantalla.docs_asignar_que", "inventario.pantalla.equipajes_form_add"]
acciones: ["ejecutar"]
endpoints: ["/src/inventario/lista_tipo_doc"]
estado_revision: "generado"
---

# Flujo - Gestionar Lista Tipo Doc

Propuesta generada automaticamente desde la capacidad `inventario.lista_tipo_doc.gestionar` y sus pantallas relacionadas.

## Objetivo De Usuario

Gestiona TipoDocOpciones. Opciones del desplegable de tipos de documento (lista_tipo_doc.php).

## Punto De Entrada

No se ha detectado pantalla principal. Revisar si el flujo solo aparece como fragmento o desde otra pantalla.

## Fragmentos O Pantallas Auxiliares

- `inventario.pantalla.docs_asignar_que`
- `inventario.pantalla.equipajes_form_add`

## Escenarios Inferidos

### Ejecutar

Pasos propuestos:
1. Revisar manualmente los pasos de esta accion.

Endpoints asociados:
- Ninguno inferido para esta accion.

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

## Revision Manual

- Confirmar si el flujo debe separarse en varios flujos de usuario.
- Cambiar nombres tecnicos por nombres de usuario.
- Completar precondiciones, permisos, validaciones y errores comunes.
- Redactar los pasos definitivos para el manual de usuario.
