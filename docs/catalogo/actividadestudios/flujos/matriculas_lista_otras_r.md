---
id: "actividadestudios.matriculas_lista_otras_r.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "actividadestudios"
nombre: "Flujo - Consultar alumnos de otras regiones"
capacidad: "actividadestudios.matriculas_lista_otras_r.gestionar"
pantallas_principales: ["actividadestudios.pantalla.matriculas_lista_otras_r"]
fragmentos: ["actividadestudios.pantalla.matriculas_lista_otras_r"]
acciones: ["obtener_datos"]
endpoints: ["/src/actividadestudios/matriculas_lista_otras_r_data"]
estado_revision: "revisado"
---

# Flujo - Gestionar Matriculas Lista Otras R

Listado de alumnos de otras regiones STGR para certificados.

## Objetivo De Usuario

Buscar por apellido a alumnos de otras regiones para ver sus asignaturas matriculadas y emitir certificados E43. Solo está disponible para las regiones del stgr.

## Punto De Entrada

Menú **Envío información a otras r** (solo regiones del stgr).

## Fragmentos O Pantallas Auxiliares

- `actividadestudios.pantalla.matriculas_lista_otras_r`

## Escenarios Inferidos

### Consultar alumnos de otras regiones

Pasos propuestos:
1. Abrir **Envío información a otras r**.
2. Si se quiere, escribir un apellido y pulsar **buscar**.
3. Aparecen los alumnos con sus avisos y asignaturas.
4. Seleccionar un alumno y pulsar **imprimir certificado** para emitir su E43.

Endpoints asociados:
- `/src/actividadestudios/matriculas_lista_otras_r_data`

## Campos Y Acciones Detectadas En Pantalla

Campos:
- `form.apellido1`
- `html.apellido1`
- `html.btn`
- `html.mod`
- `html.pau`
- `html.refresh`
- `post.apellido1`
- `post.mod`
- `post.stack`

Acciones JavaScript:
- `fnjs_buscar`
- `fnjs_buscar_por_apellidos`
- `fnjs_enviar_formulario`
- `fnjs_imp_certificado`
- `fnjs_left_side_hide`
- `fnjs_solo_uno`

## Endpoints Del Flujo

- `/src/actividadestudios/matriculas_lista_otras_r_data`

## Errores Conocidos

No se han documentado errores en la capacidad.

## Ruta de menú

- **Legacy:** — (sin entrada en el índice Legacy).
- **Pills2:** ESTUDIOS > Actas y certificados > Envío información a otras r.
