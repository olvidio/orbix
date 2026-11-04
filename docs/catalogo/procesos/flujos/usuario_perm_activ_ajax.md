---
id: "procesos.usuario_perm_activ_ajax.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "procesos"
nombre: "Flujo - Elegir la fase de referencia"
capacidad: "procesos.usuario_perm_activ_ajax.gestionar"
pantallas_principales: ["procesos.pantalla.usuario_perm_activ"]
fragmentos: ["procesos.pantalla.usuario_perm_activ"]
acciones: ["ejecutar"]
endpoints: ["/src/procesos/usuario_perm_activ_ajax"]
estado_revision: "revisado"
---

# Flujo - Fases de referencia en permisos

## Objetivo De Usuario

Actualizar las fases disponibles al cambiar el tipo de actividad o la delegación en el permiso de un usuario.

## Punto De Entrada

Formulario de permiso de actividades del usuario.

## Fragmentos O Pantallas Auxiliares

- `procesos.pantalla.usuario_perm_activ`

## Escenarios Inferidos

### Elegir la fase de referencia

Pasos propuestos:
1. En el permiso, cambiar el tipo de actividad o la delegación.
2. El desplegable de fase de referencia se actualiza con las fases válidas.

Endpoints asociados:
- `/src/procesos/usuario_perm_activ_ajax`

## Campos Y Acciones Detectadas En Pantalla

Campos:
- `form.dl_propia`
- `form.extendida`
- `form.fase_ref`
- `form.iactividad_val`
- `form.iasistentes_val`
- `form.id_tipo_activ`
- `form.inom_tipo_val`
- `form.isfsv_val`
- `form.perm_off`
- `form.perm_on`
- `post.dl_propia`
- `post.id_tipo_activ_txt`
- `post.id_usuario`
- `post.que`
- `post.quien`
- `post.sel`

Acciones JavaScript:
- Ninguna detectada.

## Endpoints Del Flujo

- `/src/procesos/usuario_perm_activ_ajax`

## Errores Conocidos

- _(ninguno documentado)_

## Ruta de menú

- **Legacy:** sin entrada de menú en el índice
- **Pills2:** sin entrada de menú en el índice
