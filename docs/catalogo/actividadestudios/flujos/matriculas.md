---
id: "actividadestudios.matriculas.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "actividadestudios"
nombre: "Flujo - Listar las matrículas de un periodo"
capacidad: "actividadestudios.matriculas.gestionar"
pantallas_principales: ["actividadestudios.pantalla.matriculas_lista"]
fragmentos: ["actividadestudios.pantalla.matriculas_lista"]
acciones: ["listar"]
endpoints: ["/src/actividadestudios/matriculas_lista_data"]
estado_revision: "revisado"
---

# Flujo - Gestionar Matriculas

Listado de matrículas realizadas en un periodo.

## Objetivo De Usuario

Ver las matrículas de las actividades de un periodo.

## Punto De Entrada

Menú **vest > actas... > Matrículas** (Pills2: ESTUDIOS > Actas y certificados > Matrículas realizadas).

## Fragmentos O Pantallas Auxiliares

- `actividadestudios.pantalla.matriculas_lista`
- `frontend/dossiers/controller/dossiers_ver.php` (destino de **ver asignaturas ca**)

## Escenarios Inferidos

### Listar las matrículas de un periodo

Pasos propuestos:
1. Entrar en **Matrículas**: «PERIODO DE SELECCIÓN DE ACTIVIDADES».
2. Elegir todo el año, un trimestre, el curso ca u **otro** (con fechas) y el año.
3. Pulsar **buscar** para ver la lista de matrículas.

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
