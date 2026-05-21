---
id: "dbextern.ver_traslados.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "dbextern"
nombre: "Flujo - Gestionar Ver Traslados"
capacidad: "dbextern.ver_traslados.gestionar"
pantallas_principales: []
fragmentos: ["dbextern.pantalla.ver_traslados"]
acciones: ["obtener_datos"]
endpoints: ["/src/dbextern/ver_traslados_datos"]
estado_revision: "generado"
---

# Flujo - Gestionar Ver Traslados

Propuesta generada automaticamente desde la capacidad `dbextern.ver_traslados.gestionar` y sus pantallas relacionadas.

## Objetivo De Usuario

Gestiona VerTraslados. Obtiene datos de personas a trasladar desde otras DL.

## Punto De Entrada

No se ha detectado pantalla principal. Revisar si el flujo solo aparece como fragmento o desde otra pantalla.

## Fragmentos O Pantallas Auxiliares

- `dbextern.pantalla.ver_traslados`

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
- `post.ids_traslados`
- `post.tipo_persona`

Acciones JavaScript:
- `fnjs_trasladar`

## Endpoints Del Flujo

- `/src/dbextern/ver_traslados_datos`

## Errores Conocidos

No se han documentado errores en la capacidad.

## Revision Manual

- Confirmar si el flujo debe separarse en varios flujos de usuario.
- Cambiar nombres tecnicos por nombres de usuario.
- Completar precondiciones, permisos, validaciones y errores comunes.
- Redactar los pasos definitivos para el manual de usuario.
