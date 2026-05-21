---
id: "dbextern.sincro_desunir.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "dbextern"
nombre: "Flujo - Gestionar Sincro Desunir"
capacidad: "dbextern.sincro_desunir.gestionar"
pantallas_principales: []
fragmentos: ["dbextern.pantalla.ver_desaparecidos_de_orbix"]
acciones: ["ejecutar"]
endpoints: ["/src/dbextern/sincro_desunir"]
estado_revision: "generado"
---

# Flujo - Gestionar Sincro Desunir

Propuesta generada automaticamente desde la capacidad `dbextern.sincro_desunir.gestionar` y sus pantallas relacionadas.

## Objetivo De Usuario

Gestiona DesunirPersonaUseCase. Descripcion funcional pendiente de revisar.

## Punto De Entrada

No se ha detectado pantalla principal. Revisar si el flujo solo aparece como fragmento o desde otra pantalla.

## Fragmentos O Pantallas Auxiliares

- `dbextern.pantalla.ver_desaparecidos_de_orbix`

## Escenarios Inferidos

### Ejecutar

Pasos propuestos:
1. Revisar manualmente los pasos de esta accion.

Endpoints asociados:
- Ninguno inferido para esta accion.

## Campos Y Acciones Detectadas En Pantalla

Campos:
- `form.id_nom_listas`
- `form.tipo_persona`
- `post.ids_desaparecidos_de_orbix`
- `post.tipo_persona`

Acciones JavaScript:
- `fnjs_desunir`

## Endpoints Del Flujo

- `/src/dbextern/sincro_desunir`

## Errores Conocidos

- ``hay un error, no se ha eliminado``
- ``no se encontró el registro a desunir``

## Revision Manual

- Confirmar si el flujo debe separarse en varios flujos de usuario.
- Cambiar nombres tecnicos por nombres de usuario.
- Completar precondiciones, permisos, validaciones y errores comunes.
- Redactar los pasos definitivos para el manual de usuario.
