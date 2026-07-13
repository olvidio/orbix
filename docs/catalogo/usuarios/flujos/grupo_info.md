---
id: "usuarios.grupo_info.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "usuarios"
nombre: "Flujo - Gestionar Grupo Info"
capacidad: "usuarios.grupo_info.gestionar"
pantallas_principales: []
fragmentos: ["usuarios.pantalla.grupo_form"]
acciones: ["ejecutar"]
endpoints: ["/src/usuarios/grupo_info"]
estado_revision: "revisado"
---

# Flujo - Grupo Info

## Objetivo De Usuario

Devuelve el nombre de un grupo para el formulario de edición.

## Punto De Entrada

Sin entrada de menú directa; login, preferencias personales o fragmento/modal desde pantalla padre.

## Fragmentos O Pantallas Auxiliares

- `usuarios.pantalla.grupo_form`

## Escenarios Inferidos

### Ejecutar

Pasos propuestos:
1. Revisar manualmente los pasos de esta accion.

Endpoints asociados:
- Ninguno inferido para esta accion.

## Campos Y Acciones Detectadas En Pantalla

Campos:
- `form.que`
- `form.sel`
- `form.usuario`
- `html.que`
- `html.refresh`
- `post.id_usuario`
- `post.que`
- `post.refresh`
- `post.scroll_id`
- `post.sel`
- `post.stack`

Acciones JavaScript:
- `fnjs_actualizar`
- `fnjs_add_perm_menu`
- `fnjs_del_perm_menu`
- `fnjs_enviar_formulario`
- `fnjs_guardar`
- `fnjs_solo_uno`

## Endpoints Del Flujo

- `/src/usuarios/grupo_info`

## Errores Conocidos

- `Grupo no encontrado`

## Ruta de menú

- **Legacy:** sin entrada de menú en el índice
- **Pills2:** sin entrada de menú en el índice
