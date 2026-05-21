---
id: "ubis.delegacion_que.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "ubis"
nombre: "Flujo - Gestionar Delegacion Que"
capacidad: "ubis.delegacion_que.gestionar"
pantallas_principales: []
fragmentos: ["ubis.pantalla.delegacion_que"]
acciones: ["obtener_datos"]
endpoints: ["/src/ubis/delegacion_que_data"]
estado_revision: "generado"
---

# Flujo - Gestionar Delegacion Que

Propuesta generada automaticamente desde la capacidad `ubis.delegacion_que.gestionar` y sus pantallas relacionadas.

## Objetivo De Usuario

Gestiona DelegacionQue. Opciones del formulario delegaciones (traslado de ubis).

## Punto De Entrada

No se ha detectado pantalla principal. Revisar si el flujo solo aparece como fragmento o desde otra pantalla.

## Fragmentos O Pantallas Auxiliares

- `ubis.pantalla.delegacion_que`

## Escenarios Inferidos

### Obtener Datos

Pasos propuestos:
1. Revisar manualmente los pasos de esta accion.

Endpoints asociados:
- Ninguno inferido para esta accion.

## Campos Y Acciones Detectadas En Pantalla

Campos:
- Ninguno detectado.

Acciones JavaScript:
- `fnjs_cerrar`
- `fnjs_cmb_id_dl`
- `fnjs_trasladar`

## Endpoints Del Flujo

- `/src/ubis/delegacion_que_data`

## Errores Conocidos

No se han documentado errores en la capacidad.

## Revision Manual

- Confirmar si el flujo debe separarse en varios flujos de usuario.
- Cambiar nombres tecnicos por nombres de usuario.
- Completar precondiciones, permisos, validaciones y errores comunes.
- Redactar los pasos definitivos para el manual de usuario.
