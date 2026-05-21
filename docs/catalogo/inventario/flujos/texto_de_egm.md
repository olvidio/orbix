---
id: "inventario.texto_de_egm.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "inventario"
nombre: "Flujo - Gestionar Texto De Egm"
capacidad: "inventario.texto_de_egm.gestionar"
pantallas_principales: []
fragmentos: ["inventario.pantalla.equipajes_form_texto_listado"]
acciones: ["ejecutar"]
endpoints: ["/src/inventario/texto_de_egm"]
estado_revision: "generado"
---

# Flujo - Gestionar Texto De Egm

Propuesta generada automaticamente desde la capacidad `inventario.texto_de_egm.gestionar` y sus pantallas relacionadas.

## Objetivo De Usuario

Gestiona TextoDeEgm. Descripcion funcional pendiente de revisar.

## Punto De Entrada

No se ha detectado pantalla principal. Revisar si el flujo solo aparece como fragmento o desde otra pantalla.

## Fragmentos O Pantallas Auxiliares

- `inventario.pantalla.equipajes_form_texto_listado`

## Escenarios Inferidos

### Ejecutar

Pasos propuestos:
1. Revisar manualmente los pasos de esta accion.

Endpoints asociados:
- Ninguno inferido para esta accion.

## Campos Y Acciones Detectadas En Pantalla

Campos:
- `form.texto`
- `html.texto`
- `post.id_equipaje`
- `post.loc`
- `post.texto`

Acciones JavaScript:
- `fnjs_cerrar`
- `fnjs_guardar_listado`

## Endpoints Del Flujo

- `/src/inventario/texto_de_egm`

## Errores Conocidos

No se han documentado errores en la capacidad.

## Revision Manual

- Confirmar si el flujo debe separarse en varios flujos de usuario.
- Cambiar nombres tecnicos por nombres de usuario.
- Completar precondiciones, permisos, validaciones y errores comunes.
- Redactar los pasos definitivos para el manual de usuario.
