---
id: "actividadestudios.matriculas.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "actividadestudios"
nombre: "Flujo - Gestionar Matriculas"
capacidad: "actividadestudios.matriculas.gestionar"
pantallas_principales: []
fragmentos: ["actividadestudios.pantalla.matriculas_lista"]
acciones: ["listar"]
endpoints: ["/src/actividadestudios/matriculas_lista_data"]
estado_revision: "generado"
---

# Flujo - Gestionar Matriculas

Propuesta generada automaticamente desde la capacidad `actividadestudios.matriculas.gestionar` y sus pantallas relacionadas.

## Objetivo De Usuario

Gestiona Matriculas. Listado de matrículas en un intervalo de fechas (actividades cuyo f_ini cae en el periodo). Usado por matriculas_lista vía PostRequest.

## Punto De Entrada

No se ha detectado pantalla principal. Revisar si el flujo solo aparece como fragmento o desde otra pantalla.

## Fragmentos O Pantallas Auxiliares

- `actividadestudios.pantalla.matriculas_lista`

## Escenarios Inferidos

### Listar

Pasos propuestos:
1. Abrir la pantalla principal del flujo.
2. Rellenar los filtros visibles si los hay.
3. Ejecutar la accion de busqueda/listado.
4. Revisar el listado mostrado en pantalla.

Endpoints asociados:
- `/src/actividadestudios/matriculas_lista_data`

## Campos Y Acciones Detectadas En Pantalla

Campos:
- `form.empiezamax`
- `form.empiezamin`
- `form.iactividad_val`
- `form.iasistentes_val`
- `form.periodo`
- `form.year`
- `html.mod`
- `html.pau`
- `html.refresh`
- `post.empiezamax`
- `post.empiezamin`
- `post.mod`
- `post.periodo`
- `post.stack`
- `post.year`

Acciones JavaScript:
- `fnjs_actualizar`
- `fnjs_borrar`
- `fnjs_buscar`
- `fnjs_enviar_formulario`
- `fnjs_left_side_hide`
- `fnjs_solo_uno`
- `fnjs_update_div`
- `fnjs_ver_ca`

## Endpoints Del Flujo

- `/src/actividadestudios/matriculas_lista_data`

## Errores Conocidos

No se han documentado errores en la capacidad.

## Revision Manual

- Confirmar si el flujo debe separarse en varios flujos de usuario.
- Cambiar nombres tecnicos por nombres de usuario.
- Completar precondiciones, permisos, validaciones y errores comunes.
- Redactar los pasos definitivos para el manual de usuario.
