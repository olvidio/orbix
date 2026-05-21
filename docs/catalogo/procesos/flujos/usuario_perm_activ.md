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
estado_revision: "generado"
---

# Flujo - Gestionar Usuario Perm Activ

Propuesta generada automaticamente desde la capacidad `procesos.usuario_perm_activ.gestionar` y sus pantallas relacionadas.

## Objetivo De Usuario

Gestiona UsuarioPermActiv. Caso de uso: datos para la pantalla usuario_perm_activ (alta/edicion de permisos de actividad para un usuario). Agrupa la resolucion de repositorios para que el controlador frontend no acceda directamente al contenedor ni a use src\.... El frontend recibe arrays serializables y construye los frontend\shared\web\Desplegable.

## Punto De Entrada

No se ha detectado pantalla principal. Revisar si el flujo solo aparece como fragmento o desde otra pantalla.

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

No se han documentado errores en la capacidad.

## Revision Manual

- Confirmar si el flujo debe separarse en varios flujos de usuario.
- Cambiar nombres tecnicos por nombres de usuario.
- Completar precondiciones, permisos, validaciones y errores comunes.
- Redactar los pasos definitivos para el manual de usuario.
