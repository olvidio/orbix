---
id: "procesos.procesos_depende.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "procesos"
nombre: "Flujo - Gestionar Procesos Depende"
capacidad: "procesos.procesos_depende.gestionar"
pantallas_principales: []
fragmentos: ["procesos.pantalla.procesos_ver"]
acciones: ["ejecutar"]
endpoints: ["/src/procesos/procesos_depende"]
estado_revision: "generado"
---

# Flujo - Gestionar Procesos Depende

Propuesta generada automaticamente desde la capacidad `procesos.procesos_depende.gestionar` y sus pantallas relacionadas.

## Objetivo De Usuario

Gestiona ProcesosDepende. Caso de uso: devuelve las opciones disponibles para el desplegable de tareas dependientes de la fase indicada (usado al cambiar de fase o fase_previa en el formulario procesos_ver). Respuesta JSON con opciones (value => label). El frontend inyecta los <option> en el <select> destino indicado por acc.

## Punto De Entrada

No se ha detectado pantalla principal. Revisar si el flujo solo aparece como fragmento o desde otra pantalla.

## Fragmentos O Pantallas Auxiliares

- `procesos.pantalla.procesos_ver`

## Escenarios Inferidos

### Ejecutar

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

- `/src/procesos/procesos_depende`

## Errores Conocidos

No se han documentado errores en la capacidad.

## Revision Manual

- Confirmar si el flujo debe separarse en varios flujos de usuario.
- Cambiar nombres tecnicos por nombres de usuario.
- Completar precondiciones, permisos, validaciones y errores comunes.
- Redactar los pasos definitivos para el manual de usuario.
