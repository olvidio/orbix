---
id: "inventario.doc_asignar_ctr.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "inventario"
nombre: "Flujo - Gestionar Doc Asignar Ctr"
capacidad: "inventario.doc_asignar_ctr.gestionar"
pantallas_principales: []
fragmentos: ["inventario.pantalla.doc_asignar_ctr"]
acciones: ["guardar"]
endpoints: ["/src/inventario/doc_asignar_ctr_guardar"]
estado_revision: "generado"
---

# Flujo - Gestionar Doc Asignar Ctr

Propuesta generada automaticamente desde la capacidad `inventario.doc_asignar_ctr.gestionar` y sus pantallas relacionadas.

## Objetivo De Usuario

Gestiona DocAsignarCtr. Descripcion funcional pendiente de revisar.

## Punto De Entrada

No se ha detectado pantalla principal. Revisar si el flujo solo aparece como fragmento o desde otra pantalla.

## Fragmentos O Pantallas Auxiliares

- `inventario.pantalla.doc_asignar_ctr`

## Escenarios Inferidos

### Guardar

Pasos propuestos:
1. Revisar manualmente los pasos de esta accion.

Endpoints asociados:
- Ninguno inferido para esta accion.

## Campos Y Acciones Detectadas En Pantalla

Campos:
- `html.f_asignado`
- `html.f_recibido`
- `html.okay`
- `post.id_tipo_doc`
- `post.sel`

Acciones JavaScript:
- `fnjs_guardar`

## Endpoints Del Flujo

- `/src/inventario/doc_asignar_ctr_guardar`

## Errores Conocidos

No se han documentado errores en la capacidad.

## Revision Manual

- Confirmar si el flujo debe separarse en varios flujos de usuario.
- Cambiar nombres tecnicos por nombres de usuario.
- Completar precondiciones, permisos, validaciones y errores comunes.
- Redactar los pasos definitivos para el manual de usuario.
