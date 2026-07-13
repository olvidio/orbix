---
id: "procesos.usuario_perm_activ.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "procesos"
nombre: "Flujo - Gestionar Usuario Perm Activ"
capacidad: "procesos.usuario_perm_activ.gestionar"
pantallas_principales: []
fragmentos: ["procesos.pantalla.usuario_perm_activ"]
acciones: ["obtener_datos"]
endpoints: ["/src/procesos/usuario_perm_activ_data"]
estado_revision: "revisado"
---

# Flujo - Permisos de actividad de usuario

## Objetivo De Usuario

Carga de la pantalla de alta o edición de permisos de actividad para un usuario: tipo de actividad, filas de ámbitos afectados y desplegables de fase y permisos.

## Punto De Entrada

Sin entrada directa de menú; se abre embebido desde la gestión de usuarios o permisos.

## Fragmentos O Pantallas Auxiliares

- `procesos.pantalla.usuario_perm_activ`

## Escenarios Inferidos

### Obtener Datos

Pasos propuestos:
1. Revisar manualmente los pasos de esta accion.

Endpoints asociados:
- Ninguno inferido para esta accion.

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

- `/src/procesos/usuario_perm_activ_data`

## Errores Conocidos

- _(ninguno documentado)_

## Ruta de menú

- **Legacy:** sin entrada de menú en el índice
- **Pills2:** sin entrada de menú en el índice
