---
id: "actividadessacd.locales_desplegable.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "actividadessacd"
nombre: "Flujo - Gestionar Locales Desplegable"
capacidad: "actividadessacd.locales_desplegable.gestionar"
pantallas_principales: []
fragmentos: ["actividadessacd.pantalla.com_sacd_txt"]
acciones: ["obtener_datos"]
endpoints: ["/src/actividadessacd/locales_desplegable_data"]
estado_revision: "generado"
---

# Flujo - Gestionar Locales Desplegable

Propuesta generada automaticamente desde la capacidad `actividadessacd.locales_desplegable.gestionar` y sus pantallas relacionadas.

## Objetivo De Usuario

Gestiona LocalesDesplegable. Descripcion funcional pendiente de revisar.

## Punto De Entrada

No se ha detectado pantalla principal. Revisar si el flujo solo aparece como fragmento o desde otra pantalla.

## Fragmentos O Pantallas Auxiliares

- `actividadessacd.pantalla.com_sacd_txt`

## Escenarios Inferidos

### Obtener Datos

Pasos propuestos:
1. Revisar manualmente los pasos de esta accion.

Endpoints asociados:
- Ninguno inferido para esta accion.

## Campos Y Acciones Detectadas En Pantalla

Campos:
- `html.comunicacion`

Acciones JavaScript:
- `fnjs_cancelar`
- `fnjs_get_texto`
- `fnjs_guardar`
- `fnjs_parse_rta_txt`

## Endpoints Del Flujo

- `/src/actividadessacd/locales_desplegable_data`

## Errores Conocidos

No se han documentado errores en la capacidad.

## Revision Manual

- Confirmar si el flujo debe separarse en varios flujos de usuario.
- Cambiar nombres tecnicos por nombres de usuario.
- Completar precondiciones, permisos, validaciones y errores comunes.
- Redactar los pasos definitivos para el manual de usuario.
