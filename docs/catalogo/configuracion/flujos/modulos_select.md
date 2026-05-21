---
id: "configuracion.modulos_select.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "configuracion"
nombre: "Flujo - Gestionar Modulos Select"
capacidad: "configuracion.modulos_select.gestionar"
pantallas_principales: []
fragmentos: ["configuracion.pantalla.modulos_select"]
acciones: ["obtener_datos"]
endpoints: ["/src/configuracion/modulos_select_data"]
estado_revision: "generado"
---

# Flujo - Gestionar Modulos Select

Propuesta generada automaticamente desde la capacidad `configuracion.modulos_select.gestionar` y sus pantallas relacionadas.

## Objetivo De Usuario

Gestiona ModulosSelect. JSON para {.

## Punto De Entrada

No se ha detectado pantalla principal. Revisar si el flujo solo aparece como fragmento o desde otra pantalla.

## Fragmentos O Pantallas Auxiliares

- `configuracion.pantalla.modulos_select`

## Escenarios Inferidos

### Obtener Datos

Pasos propuestos:
1. Revisar manualmente los pasos de esta accion.

Endpoints asociados:
- Ninguno inferido para esta accion.

## Campos Y Acciones Detectadas En Pantalla

Campos:
- `html.mod`
- `html.refresh`

Acciones JavaScript:
- `fnjs_actualizar`
- `fnjs_eliminar`
- `fnjs_enviar_formulario`
- `fnjs_modificar`
- `fnjs_nuevo`
- `fnjs_solo_uno`

## Endpoints Del Flujo

- `/src/configuracion/modulos_select_data`

## Errores Conocidos

No se han documentado errores en la capacidad.

## Revision Manual

- Confirmar si el flujo debe separarse en varios flujos de usuario.
- Cambiar nombres tecnicos por nombres de usuario.
- Completar precondiciones, permisos, validaciones y errores comunes.
- Redactar los pasos definitivos para el manual de usuario.
