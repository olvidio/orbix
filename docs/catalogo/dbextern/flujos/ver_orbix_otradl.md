---
id: "dbextern.ver_orbix_otradl.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "dbextern"
nombre: "Flujo - Gestionar Ver Orbix Otradl"
capacidad: "dbextern.ver_orbix_otradl.gestionar"
pantallas_principales: []
fragmentos: ["dbextern.pantalla.ver_orbix_otradl"]
acciones: ["obtener_datos"]
endpoints: ["/src/dbextern/ver_orbix_otradl_datos"]
estado_revision: "generado"
---

# Flujo - Gestionar Ver Orbix Otradl

Propuesta generada automaticamente desde la capacidad `dbextern.ver_orbix_otradl.gestionar` y sus pantallas relacionadas.

## Objetivo De Usuario

Gestiona VerOrbixOtraDl. Obtiene datos de personas BDU que están en otra DL en Orbix.

## Punto De Entrada

No se ha detectado pantalla principal. Revisar si el flujo solo aparece como fragmento o desde otra pantalla.

## Fragmentos O Pantallas Auxiliares

- `dbextern.pantalla.ver_orbix_otradl`

## Escenarios Inferidos

### Obtener Datos

Pasos propuestos:
1. Revisar manualmente los pasos de esta accion.

Endpoints asociados:
- Ninguno inferido para esta accion.

## Campos Y Acciones Detectadas En Pantalla

Campos:
- `form.dl`
- `form.id_nom_orbix`
- `form.tipo_persona`
- `post.ids_traslados_A`
- `post.tipo_persona`

Acciones JavaScript:
- `fnjs_trasladar`

## Endpoints Del Flujo

- `/src/dbextern/ver_orbix_otradl_datos`

## Errores Conocidos

No se han documentado errores en la capacidad.

## Revision Manual

- Confirmar si el flujo debe separarse en varios flujos de usuario.
- Cambiar nombres tecnicos por nombres de usuario.
- Completar precondiciones, permisos, validaciones y errores comunes.
- Redactar los pasos definitivos para el manual de usuario.
