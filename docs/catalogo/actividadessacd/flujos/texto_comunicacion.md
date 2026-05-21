---
id: "actividadessacd.texto_comunicacion.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "actividadessacd"
nombre: "Flujo - Gestionar Texto Comunicacion"
capacidad: "actividadessacd.texto_comunicacion.gestionar"
pantallas_principales: []
fragmentos: ["actividadessacd.pantalla.com_sacd_txt"]
acciones: ["guardar", "obtener_datos"]
endpoints: ["/src/actividadessacd/texto_comunicacion_data", "/src/actividadessacd/texto_comunicacion_guardar"]
estado_revision: "generado"
---

# Flujo - Gestionar Texto Comunicacion

Propuesta generada automaticamente desde la capacidad `actividadessacd.texto_comunicacion.gestionar` y sus pantallas relacionadas.

## Objetivo De Usuario

Gestiona TextoComunicacion. Devuelve el texto de comunicacion (clave, idioma). Upsert/delete del texto de comunicacion (clave, idioma, texto).

## Punto De Entrada

No se ha detectado pantalla principal. Revisar si el flujo solo aparece como fragmento o desde otra pantalla.

## Fragmentos O Pantallas Auxiliares

- `actividadessacd.pantalla.com_sacd_txt`

## Escenarios Inferidos

### Guardar

Pasos propuestos:
1. Revisar manualmente los pasos de esta accion.

Endpoints asociados:
- Ninguno inferido para esta accion.

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

- `/src/actividadessacd/texto_comunicacion_data`
- `/src/actividadessacd/texto_comunicacion_guardar`

## Errores Conocidos

- ``faltan parametros clave / idioma``
- ``hay un error, no se ha eliminado el texto``
- ``hay un error, no se ha guardado el texto``

## Revision Manual

- Confirmar si el flujo debe separarse en varios flujos de usuario.
- Cambiar nombres tecnicos por nombres de usuario.
- Completar precondiciones, permisos, validaciones y errores comunes.
- Redactar los pasos definitivos para el manual de usuario.
