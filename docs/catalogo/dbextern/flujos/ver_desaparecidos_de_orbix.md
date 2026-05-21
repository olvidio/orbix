---
id: "dbextern.ver_desaparecidos_de_orbix.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "dbextern"
nombre: "Flujo - Gestionar Ver Desaparecidos De Orbix"
capacidad: "dbextern.ver_desaparecidos_de_orbix.gestionar"
pantallas_principales: []
fragmentos: ["dbextern.pantalla.ver_desaparecidos_de_orbix"]
acciones: ["obtener_datos"]
endpoints: ["/src/dbextern/ver_desaparecidos_de_orbix_datos"]
estado_revision: "generado"
---

# Flujo - Gestionar Ver Desaparecidos De Orbix

Propuesta generada automaticamente desde la capacidad `dbextern.ver_desaparecidos_de_orbix.gestionar` y sus pantallas relacionadas.

## Objetivo De Usuario

Gestiona VerDesaparecidosDeOrbix. Obtiene datos de personas BDU desaparecidas de Orbix.

## Punto De Entrada

No se ha detectado pantalla principal. Revisar si el flujo solo aparece como fragmento o desde otra pantalla.

## Fragmentos O Pantallas Auxiliares

- `dbextern.pantalla.ver_desaparecidos_de_orbix`

## Escenarios Inferidos

### Obtener Datos

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

- `/src/dbextern/ver_desaparecidos_de_orbix_datos`

## Errores Conocidos

No se han documentado errores en la capacidad.

## Revision Manual

- Confirmar si el flujo debe separarse en varios flujos de usuario.
- Cambiar nombres tecnicos por nombres de usuario.
- Completar precondiciones, permisos, validaciones y errores comunes.
- Redactar los pasos definitivos para el manual de usuario.
