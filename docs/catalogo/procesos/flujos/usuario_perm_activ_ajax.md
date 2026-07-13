---
id: "procesos.usuario_perm_activ_ajax.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "procesos"
nombre: "Flujo - Gestionar Usuario Perm Activ Ajax"
capacidad: "procesos.usuario_perm_activ_ajax.gestionar"
pantallas_principales: []
fragmentos: ["procesos.pantalla.usuario_perm_activ"]
acciones: ["ejecutar"]
endpoints: ["/src/procesos/usuario_perm_activ_ajax"]
estado_revision: "revisado"
---

# Flujo - Fases de referencia en permisos

## Objetivo De Usuario

Actualizar dinámicamente las opciones del desplegable `fase_ref[]` al cambiar el tipo de actividad o la delegación en la pantalla de permisos de actividad de usuario.

## Punto De Entrada

Sin entrada directa de menú; se invoca desde la pantalla embebida `usuario_perm_activ`.

## Fragmentos O Pantallas Auxiliares

- `procesos.pantalla.usuario_perm_activ`

## Escenarios Inferidos

### Ejecutar

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

- `/src/procesos/usuario_perm_activ_ajax`

## Errores Conocidos

- _(ninguno documentado)_

## Ruta de menú

- **Legacy:** sin entrada de menú en el índice
- **Pills2:** sin entrada de menú en el índice
