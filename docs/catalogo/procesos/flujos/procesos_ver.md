---
id: "procesos.procesos_ver.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "procesos"
nombre: "Flujo - Gestionar Procesos Ver"
capacidad: "procesos.procesos_ver.gestionar"
pantallas_principales: []
fragmentos: ["procesos.pantalla.procesos_ver"]
acciones: ["obtener_datos"]
endpoints: ["/src/procesos/procesos_ver_data"]
estado_revision: "generado"
---

# Flujo - Gestionar Procesos Ver

Propuesta generada automaticamente desde la capacidad `procesos.procesos_ver.gestionar` y sus pantallas relacionadas.

## Objetivo De Usuario

Gestiona ProcesosVer. Caso de uso: datos para la pantalla procesos_ver (formulario editar / nuevo de una fase dentro de un tipo de proceso). Devuelve todos los arrays necesarios para que el controlador frontend monte los frontend\shared\web\Desplegable (fases, tareas, status, oficinas responsables, fases previas y sus tareas) y el formulario de edicion.

## Punto De Entrada

No se ha detectado pantalla principal. Revisar si el flujo solo aparece como fragmento o desde otra pantalla.

## Fragmentos O Pantallas Auxiliares

- `procesos.pantalla.procesos_ver`

## Escenarios Inferidos

### Obtener Datos

Pasos propuestos:
1. Revisar manualmente los pasos de esta accion.

Endpoints asociados:
- Ninguno inferido para esta accion.

## Campos Y Acciones Detectadas En Pantalla

Campos:
- `form.acc`
- `form.dep_num`
- `form.id_fase`
- `form.id_fase_previa`
- `form.id_of_responsable`
- `form.id_tarea`
- `form.id_tarea_previa`
- `form.mensaje_requisito`
- `form.status`
- `form.valor_depende`
- `post.id_item`
- `post.id_tipo_proceso`
- `post.mod`

Acciones JavaScript:
- `fnjs_get_depende`

## Endpoints Del Flujo

- `/src/procesos/procesos_ver_data`

## Errores Conocidos

No se han documentado errores en la capacidad.

## Revision Manual

- Confirmar si el flujo debe separarse en varios flujos de usuario.
- Cambiar nombres tecnicos por nombres de usuario.
- Completar precondiciones, permisos, validaciones y errores comunes.
- Redactar los pasos definitivos para el manual de usuario.
