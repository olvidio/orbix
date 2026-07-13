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
estado_revision: "revisado"
---

# Flujo - Gestionar Matriculas

Listado de matrículas realizadas en un periodo.

## Objetivo De Usuario

El usuario elige un periodo y pulsa **buscar**: el sistema muestra la tabla de matrículas
de actividades cuyo `f_ini` cae en ese intervalo, con alumno, centro, actividad, asignatura,
preceptor y nota.

## Punto De Entrada

Pantalla `matriculas_lista` (`frontend/actividadestudios/controller/matriculas_lista.php`):
al cargar o al pulsar **buscar** (`fnjs_buscar` en `matriculas.phtml`) llama a
`matriculas_lista_data` con `inicioIso` y `finIso` calculados del periodo.

## Fragmentos O Pantallas Auxiliares

- `actividadestudios.pantalla.matriculas_lista`
- `frontend/dossiers/controller/dossiers_ver.php` (destino de **ver asignaturas ca**)

## Escenarios Inferidos

### Listar

Pasos:
1. Abrir **Matrículas** desde el menú.
2. Elegir año y periodo (por defecto `curso_ca`) y pulsar **buscar**.
3. El sistema consulta `matriculas_lista_data` y muestra la tabla paginada.
4. Opcionalmente, seleccionar filas para ver dossier CA o borrar matrículas.

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

## Ruta de menú

- **Legacy:** vest > actas... > Matrículas.
- **Pills2:** ESTUDIOS > Actas y certificados > Matrículas realizadas; ESTUDIOS > Preparación
  planes estudio > Matrículas realizadas; vest > actas... > Matrículas.
