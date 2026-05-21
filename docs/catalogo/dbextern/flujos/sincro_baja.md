---
id: "dbextern.sincro_baja.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "dbextern"
nombre: "Flujo - Gestionar Sincro Baja"
capacidad: "dbextern.sincro_baja.gestionar"
pantallas_principales: []
fragmentos: ["dbextern.pantalla.ver_desaparecidos_de_listas"]
acciones: ["ejecutar"]
endpoints: ["/src/dbextern/sincro_baja"]
estado_revision: "generado"
---

# Flujo - Gestionar Sincro Baja

Propuesta generada automaticamente desde la capacidad `dbextern.sincro_baja.gestionar` y sus pantallas relacionadas.

## Objetivo De Usuario

Gestiona BajaPersonaUseCase. Da de baja a una persona (fallecido o traslado a otra región).

## Punto De Entrada

No se ha detectado pantalla principal. Revisar si el flujo solo aparece como fragmento o desde otra pantalla.

## Fragmentos O Pantallas Auxiliares

- `dbextern.pantalla.ver_desaparecidos_de_listas`

## Escenarios Inferidos

### Ejecutar

Pasos propuestos:
1. Revisar manualmente los pasos de esta accion.

Endpoints asociados:
- Ninguno inferido para esta accion.

## Campos Y Acciones Detectadas En Pantalla

Campos:
- `form.id_nom_orbix`
- `form.tipo_persona`
- `post.ids_desaparecidos_de_listas`
- `post.tipo_persona`

Acciones JavaScript:
- `fnjs_baja`
- `fnjs_traslado`

## Endpoints Del Flujo

- `/src/dbextern/sincro_baja`

## Errores Conocidos

- ``OJO: Debería cambiar el campo situación. No se ha hecho ningún cambio.``

## Revision Manual

- Confirmar si el flujo debe separarse en varios flujos de usuario.
- Cambiar nombres tecnicos por nombres de usuario.
- Completar precondiciones, permisos, validaciones y errores comunes.
- Redactar los pasos definitivos para el manual de usuario.
