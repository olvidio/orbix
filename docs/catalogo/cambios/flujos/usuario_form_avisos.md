---
id: "cambios.usuario_form_avisos.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "cambios"
nombre: "Flujo - Gestionar Usuario Form Avisos"
capacidad: "cambios.usuario_form_avisos.gestionar"
pantallas_principales: []
fragmentos: ["cambios.pantalla.usuario_form_avisos"]
acciones: ["obtener_datos"]
endpoints: ["/src/cambios/usuario_form_avisos_data"]
estado_revision: "generado"
---

# Flujo - Gestionar Usuario Form Avisos

Propuesta generada automaticamente desde la capacidad `cambios.usuario_form_avisos.gestionar` y sus pantallas relacionadas.

## Objetivo De Usuario

Gestiona UsuarioFormAvisos. Datos para el listado de avisos de un usuario.

## Punto De Entrada

No se ha detectado pantalla principal. Revisar si el flujo solo aparece como fragmento o desde otra pantalla.

## Fragmentos O Pantallas Auxiliares

- `cambios.pantalla.usuario_form_avisos`

## Escenarios Inferidos

### Obtener Datos

Pasos propuestos:
1. Revisar manualmente los pasos de esta accion.

Endpoints asociados:
- Ninguno inferido para esta accion.

## Campos Y Acciones Detectadas En Pantalla

Campos:
- `post.id_usuario`
- `post.quien`

Acciones JavaScript:
- `fnjs_add_cambio`
- `fnjs_del_cambio`
- `fnjs_enviar_formulario`
- `fnjs_mod_cambio`
- `fnjs_solo_uno`

## Endpoints Del Flujo

- `/src/cambios/usuario_form_avisos_data`

## Errores Conocidos

No se han documentado errores en la capacidad.

## Revision Manual

- Confirmar si el flujo debe separarse en varios flujos de usuario.
- Cambiar nombres tecnicos por nombres de usuario.
- Completar precondiciones, permisos, validaciones y errores comunes.
- Redactar los pasos definitivos para el manual de usuario.
