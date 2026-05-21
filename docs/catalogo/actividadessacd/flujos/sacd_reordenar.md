---
id: "actividadessacd.sacd_reordenar.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "actividadessacd"
nombre: "Flujo - Gestionar Sacd Reordenar"
capacidad: "actividadessacd.sacd_reordenar.gestionar"
pantallas_principales: ["actividadessacd.pantalla.activ_sacd"]
fragmentos: []
acciones: ["ejecutar"]
endpoints: ["/src/actividadessacd/sacd_reordenar"]
estado_revision: "generado"
---

# Flujo - Gestionar Sacd Reordenar

Propuesta generada automaticamente desde la capacidad `actividadessacd.sacd_reordenar.gestionar` y sus pantallas relacionadas.

## Objetivo De Usuario

Gestiona SacdReordenar. Reordena un sacd dentro de una actividad (mas / menos prioridad).

## Punto De Entrada

- `actividadessacd.pantalla.activ_sacd`

## Fragmentos O Pantallas Auxiliares

No se han detectado fragmentos AJAX relacionados.

## Escenarios Inferidos

### Ejecutar

Pasos propuestos:
1. Revisar manualmente los pasos de esta accion.

Endpoints asociados:
- Ninguno inferido para esta accion.

## Campos Y Acciones Detectadas En Pantalla

Campos:
- `form.empiezamax`
- `form.empiezamin`
- `form.periodo`
- `form.tipo`
- `form.year`
- `post.periodo`
- `post.tipo`
- `post.year`

Acciones JavaScript:
- `fnjs_actualizar_activ`
- `fnjs_asignar_sacd`
- `fnjs_cambiar_sacd`
- `fnjs_cerrar`
- `fnjs_construir_celda_sacds`
- `fnjs_construir_leyenda`
- `fnjs_construir_tabla_disponibles`
- `fnjs_construir_tabla_lista`
- `fnjs_construir_tabla_solapes`
- `fnjs_enviar`
- `fnjs_esc`
- `fnjs_left_side_hide`
- `fnjs_nuevo_sacd`
- `fnjs_orden`
- `fnjs_parse_rta`
- `fnjs_ver`

## Endpoints Del Flujo

- `/src/actividadessacd/sacd_reordenar`

## Errores Conocidos

- ``direccion de orden incorrecta (mas / menos)``
- ``faltan parametros id_activ / id_nom``

## Revision Manual

- Confirmar si el flujo debe separarse en varios flujos de usuario.
- Cambiar nombres tecnicos por nombres de usuario.
- Completar precondiciones, permisos, validaciones y errores comunes.
- Redactar los pasos definitivos para el manual de usuario.
