---
id: "actividadessacd.lista_actividades_sacd.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "actividadessacd"
nombre: "Flujo - Gestionar Lista Actividades Sacd"
capacidad: "actividadessacd.lista_actividades_sacd.gestionar"
pantallas_principales: ["actividadessacd.pantalla.activ_sacd"]
fragmentos: []
acciones: ["obtener_datos"]
endpoints: ["/src/actividadessacd/lista_actividades_sacd_data"]
estado_revision: "generado"
---

# Flujo - Gestionar Lista Actividades Sacd

Propuesta generada automaticamente desde la capacidad `actividadessacd.lista_actividades_sacd.gestionar` y sus pantallas relacionadas.

## Objetivo De Usuario

Gestiona ListaActividadesSacd. Devuelve el listado de actividades del tipo + periodo elegidos junto con los sacd encargados y los flags de permiso.

## Punto De Entrada

- `actividadessacd.pantalla.activ_sacd`

## Fragmentos O Pantallas Auxiliares

No se han detectado fragmentos AJAX relacionados.

## Escenarios Inferidos

### Obtener Datos

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

- `/src/actividadessacd/lista_actividades_sacd_data`

## Errores Conocidos

No se han documentado errores en la capacidad.

## Revision Manual

- Confirmar si el flujo debe separarse en varios flujos de usuario.
- Cambiar nombres tecnicos por nombres de usuario.
- Completar precondiciones, permisos, validaciones y errores comunes.
- Redactar los pasos definitivos para el manual de usuario.
