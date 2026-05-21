---
id: "notas.asig_faltan_select.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "notas"
nombre: "Flujo - Gestionar Asig Faltan Select"
capacidad: "notas.asig_faltan_select.gestionar"
pantallas_principales: []
fragmentos: ["notas.pantalla.asig_faltan_select"]
acciones: ["obtener_datos"]
endpoints: ["/src/notas/asig_faltan_select_data"]
estado_revision: "generado"
---

# Flujo - Gestionar Asig Faltan Select

Propuesta generada automaticamente desde la capacidad `notas.asig_faltan_select.gestionar` y sus pantallas relacionadas.

## Objetivo De Usuario

Gestiona AsigFaltanSelectTabla. Tabla de asig_faltan_select (asignaturas pendientes por persona).

## Punto De Entrada

No se ha detectado pantalla principal. Revisar si el flujo solo aparece como fragmento o desde otra pantalla.

## Fragmentos O Pantallas Auxiliares

- `notas.pantalla.asig_faltan_select`

## Escenarios Inferidos

### Obtener Datos

Pasos propuestos:
1. Revisar manualmente los pasos de esta accion.

Endpoints asociados:
- Ninguno inferido para esta accion.

## Campos Y Acciones Detectadas En Pantalla

Campos:
- `form.sel`
- `post.b_c`
- `post.c1`
- `post.c2`
- `post.lista`
- `post.numero`
- `post.personas_agd`
- `post.personas_n`
- `post.stack`

Acciones JavaScript:
- `fnjs_enviar_formulario`
- `fnjs_modificar`
- `fnjs_solo_uno`
- `fnjs_tesera`

## Endpoints Del Flujo

- `/src/notas/asig_faltan_select_data`

## Errores Conocidos

No se han documentado errores en la capacidad.

## Revision Manual

- Confirmar si el flujo debe separarse en varios flujos de usuario.
- Cambiar nombres tecnicos por nombres de usuario.
- Completar precondiciones, permisos, validaciones y errores comunes.
- Redactar los pasos definitivos para el manual de usuario.
