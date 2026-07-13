---
id: "usuarios.usuario_grupo_add.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "usuarios"
nombre: "Flujo - Gestionar Usuario Grupo Add"
capacidad: "usuarios.usuario_grupo_add.gestionar"
pantallas_principales: []
fragmentos: ["usuarios.pantalla.usuario_form"]
acciones: ["ejecutar"]
endpoints: ["/src/usuarios/usuario_grupo_add"]
estado_revision: "revisado"
---

# Flujo - Usuario Grupo Add

## Objetivo De Usuario

Asocia grupo permisos a usuario (ctx HashB `usuario_grupo_add`).

## Punto De Entrada

Sin entrada de menú directa; login, preferencias personales o fragmento/modal desde pantalla padre.

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

- `/src/usuarios/usuario_grupo_add`

## Errores Conocidos

- `Operación no autorizada`
- `hay un error, no se ha guardado`

## Ruta de menú

- **Legacy:** sin entrada de menú en el índice
- **Pills2:** sin entrada de menú en el índice
