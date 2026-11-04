---
id: "usuarios.grupo_info.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "usuarios"
nombre: "Flujo - Abrir un grupo para editarlo"
capacidad: "usuarios.grupo_info.gestionar"
pantallas_principales: ["usuarios.pantalla.grupo_form"]
fragmentos: ["usuarios.pantalla.grupo_form"]
acciones: ["ejecutar"]
endpoints: ["/src/usuarios/grupo_info"]
estado_revision: "revisado"
---

# Flujo - Grupo Info

## Objetivo De Usuario

Cargar el nombre de un grupo en su formulario de edición.

## Punto De Entrada

Pulsar un grupo en la lista de grupos.

## Fragmentos O Pantallas Auxiliares

- `usuarios.pantalla.grupo_form`

## Escenarios Inferidos

### Abrir un grupo para editarlo

Pasos propuestos:
1. En **grupos**, pulsar el grupo.
2. Se carga su nombre en el formulario.
3. Si el grupo ya no existe aparece «Grupo no encontrado».

Endpoints asociados:
- `/src/usuarios/grupo_info`

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
