---
id: "inventario.lista_docs_de_egm.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "inventario"
nombre: "Flujo - Gestionar Lista Docs De Egm"
capacidad: "inventario.lista_docs_de_egm.gestionar"
pantallas_principales: []
fragmentos: ["inventario.pantalla.equipajes_form_del", "inventario.pantalla.equipajes_lista_docs"]
acciones: ["ejecutar"]
endpoints: ["/src/inventario/lista_docs_de_egm"]
estado_revision: "generado"
---

# Flujo - Gestionar Lista Docs De Egm

Propuesta generada automaticamente desde la capacidad `inventario.lista_docs_de_egm.gestionar` y sus pantallas relacionadas.

## Objetivo De Usuario

Gestiona ListaDocsDeEgm. Descripcion funcional pendiente de revisar.

## Punto De Entrada

No se ha detectado pantalla principal. Revisar si el flujo solo aparece como fragmento o desde otra pantalla.

## Fragmentos O Pantallas Auxiliares

- `inventario.pantalla.equipajes_form_del`
- `inventario.pantalla.equipajes_lista_docs`

## Escenarios Inferidos

### Ejecutar

Pasos propuestos:
1. Revisar manualmente los pasos de esta accion.

Endpoints asociados:
- Ninguno inferido para esta accion.

## Campos Y Acciones Detectadas En Pantalla

Campos:
- `form.sel`
- `post.id_equipaje`
- `post.id_grupo`
- `post.id_item_egm`
- `post.id_lugar`

Acciones JavaScript:
- `fnjs_cerrar`
- `fnjs_del_doc`
- `fnjs_eliminar_grupo`
- `fnjs_modificar_form_add`
- `fnjs_modificar_form_del`

## Endpoints Del Flujo

- `/src/inventario/lista_docs_de_egm`

## Errores Conocidos

No se han documentado errores en la capacidad.

## Revision Manual

- Confirmar si el flujo debe separarse en varios flujos de usuario.
- Cambiar nombres tecnicos por nombres de usuario.
- Completar precondiciones, permisos, validaciones y errores comunes.
- Redactar los pasos definitivos para el manual de usuario.
