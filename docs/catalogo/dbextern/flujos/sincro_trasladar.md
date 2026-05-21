---
id: "dbextern.sincro_trasladar.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "dbextern"
nombre: "Flujo - Gestionar Sincro Trasladar"
capacidad: "dbextern.sincro_trasladar.gestionar"
pantallas_principales: []
fragmentos: ["dbextern.pantalla.ver_traslados"]
acciones: ["ejecutar"]
endpoints: ["/src/dbextern/sincro_trasladar"]
estado_revision: "generado"
---

# Flujo - Gestionar Sincro Trasladar

Propuesta generada automaticamente desde la capacidad `dbextern.sincro_trasladar.gestionar` y sus pantallas relacionadas.

## Objetivo De Usuario

Gestiona TrasladarPersonaUseCase. Trasladar persona desde otra DL a la DL actual.

## Punto De Entrada

No se ha detectado pantalla principal. Revisar si el flujo solo aparece como fragmento o desde otra pantalla.

## Fragmentos O Pantallas Auxiliares

- `dbextern.pantalla.ver_traslados`

## Escenarios Inferidos

### Ejecutar

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

- `/src/dbextern/sincro_trasladar`

## Errores Conocidos

No se han documentado errores en la capacidad.

## Revision Manual

- Confirmar si el flujo debe separarse en varios flujos de usuario.
- Cambiar nombres tecnicos por nombres de usuario.
- Completar precondiciones, permisos, validaciones y errores comunes.
- Redactar los pasos definitivos para el manual de usuario.
