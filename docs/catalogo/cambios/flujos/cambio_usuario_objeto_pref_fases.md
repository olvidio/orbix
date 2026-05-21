---
id: "cambios.cambio_usuario_objeto_pref_fases.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "cambios"
nombre: "Flujo - Gestionar Cambio Usuario Objeto Pref Fases"
capacidad: "cambios.cambio_usuario_objeto_pref_fases.gestionar"
pantallas_principales: []
fragmentos: ["cambios.pantalla.usuario_avisos_pref_fases"]
acciones: ["obtener_datos"]
endpoints: ["/src/cambios/cambio_usuario_objeto_pref_fases_data"]
estado_revision: "generado"
---

# Flujo - Gestionar Cambio Usuario Objeto Pref Fases

Propuesta generada automaticamente desde la capacidad `cambios.cambio_usuario_objeto_pref_fases.gestionar` y sus pantallas relacionadas.

## Objetivo De Usuario

Gestiona CambioUsuarioObjetoPrefFases. Endpoint JSON: lista de fases para el tipo de actividad indicado.

## Punto De Entrada

No se ha detectado pantalla principal. Revisar si el flujo solo aparece como fragmento o desde otra pantalla.

## Fragmentos O Pantallas Auxiliares

- `cambios.pantalla.usuario_avisos_pref_fases`

## Escenarios Inferidos

### Obtener Datos

Pasos propuestos:
1. Revisar manualmente los pasos de esta accion.

Endpoints asociados:
- Ninguno inferido para esta accion.

## Campos Y Acciones Detectadas En Pantalla

Campos:
- `post.dl_propia`
- `post.id_tipo_activ`
- `post.objeto`

Acciones JavaScript:
- Ninguna detectada.

## Endpoints Del Flujo

- `/src/cambios/cambio_usuario_objeto_pref_fases_data`

## Errores Conocidos

No se han documentado errores en la capacidad.

## Revision Manual

- Confirmar si el flujo debe separarse en varios flujos de usuario.
- Cambiar nombres tecnicos por nombres de usuario.
- Completar precondiciones, permisos, validaciones y errores comunes.
- Redactar los pasos definitivos para el manual de usuario.
