---
id: "usuarios.usuario_grupo_del.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "usuarios"
nombre: "Flujo - Gestionar Usuario Grupo Del"
capacidad: "usuarios.usuario_grupo_del.gestionar"
pantallas_principales: []
fragmentos: ["usuarios.pantalla.usuario_form"]
acciones: ["ejecutar"]
endpoints: ["/src/usuarios/usuario_grupo_del"]
estado_revision: "generado"
---

# Flujo - Gestionar Usuario Grupo Del

Propuesta generada automaticamente desde la capacidad `usuarios.usuario_grupo_del.gestionar` y sus pantallas relacionadas.

## Objetivo De Usuario

Gestiona UsuarioGrupoDel. Descripcion funcional pendiente de revisar.

## Punto De Entrada

No se ha detectado pantalla principal. Revisar si el flujo solo aparece como fragmento o desde otra pantalla.

## Fragmentos O Pantallas Auxiliares

- `usuarios.pantalla.usuario_form`

## Escenarios Inferidos

### Ejecutar

Pasos propuestos:
1. Revisar manualmente los pasos de esta accion.

Endpoints asociados:
- Ninguno inferido para esta accion.

## Campos Y Acciones Detectadas En Pantalla

Campos:
- `form.id_usuario`
- `form.password`
- `form.usuario`
- `html.cambio_password`
- `html.has_2fa`
- `html.password`
- `post.id_usuario`
- `post.que`
- `post.quien`
- `post.refresh`
- `post.scroll_id`
- `post.sel`
- `post.stack`

Acciones JavaScript:
- `fnjs_add_grup`
- `fnjs_chk_passwd`
- `fnjs_del_grup`
- `fnjs_guardar`
- `fnjs_guardar_datos`
- `fnjs_lst_add_grup`
- `fnjs_lst_del_grup`
- `fnjs_mas_casas`

## Endpoints Del Flujo

- `/src/usuarios/usuario_grupo_del`

## Errores Conocidos

No se han documentado errores en la capacidad.

## Revision Manual

- Confirmar si el flujo debe separarse en varios flujos de usuario.
- Cambiar nombres tecnicos por nombres de usuario.
- Completar precondiciones, permisos, validaciones y errores comunes.
- Redactar los pasos definitivos para el manual de usuario.
