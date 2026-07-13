---
id: "actividadestudios.ca_posibles.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "actividadestudios"
nombre: "Flujo - Gestionar Ca Posibles"
capacidad: "actividadestudios.ca_posibles.gestionar"
pantallas_principales: []
fragmentos: ["actividadestudios.pantalla.ca_posibles"]
acciones: ["obtener_datos"]
endpoints: ["/src/actividadestudios/ca_posibles_data"]
estado_revision: "revisado"
---

# Flujo - Gestionar Ca Posibles

Cálculo y listado de créditos/asignaturas cursables por alumno en cada CA.

## Objetivo De Usuario

Tras elegir centro, periodo y filtros en `ca_posibles_que`, el usuario obtiene el cuadro
de posibles CA por alumno: créditos cursables, asignaturas pendientes y enlaces de detalle.
Misma lógica que `frontend/actividadestudios/controller/ca_posibles.php`; en modo lista,
`pagina_link_spec` lo firma el front.

## Punto De Entrada

Pantalla `ca_posibles` (`frontend/actividadestudios/controller/ca_posibles.php`): al enviar
el formulario de `ca_posibles_que` o al pulsar un enlace de detalle, llama a
`ca_posibles_data` con los filtros de centro, periodo, grupo de estudios y flags
(`ca_estudios`, `ca_repaso`, `ca_todos`).

También accesible desde búsqueda de personas (`personas_select`, `fnjs_posibles_ca`).

## Fragmentos O Pantallas Auxiliares

- `actividadestudios.pantalla.ca_posibles`
- `actividadestudios.pantalla.ca_posibles_que` (formulario de filtros)

## Escenarios Inferidos

### Obtener Datos

Pasos:
1. En `ca_posibles_que`, elegir centro N o AGD, periodo y opciones de filtro.
2. Pulsar **buscar**; el formulario envía a `ca_posibles.php`.
3. El controlador valida que haya centro seleccionado y consulta `ca_posibles_data`.
4. Se muestra el listado o cuadro de posibles CA por alumno.

Endpoints asociados:
- `/src/actividadestudios/ca_posibles_data`

## Campos Y Acciones Detectadas En Pantalla

Campos:
- `html.observ`
- `post.ca_estudios`
- `post.ca_repaso`
- `post.ca_todos`
- `post.empiezamax`
- `post.empiezamin`
- `post.grupo_estudios`
- `post.id_ctr_agd`
- `post.id_ctr_n`
- `post.idca`
- `post.na`
- `post.obj_pau`
- `post.periodo`
- `post.ref`
- `post.sel`
- `post.stack`
- `post.texto`
- `post.year`

Acciones JavaScript:
- `fnjs_update_div`

## Endpoints Del Flujo

- `/src/actividadestudios/ca_posibles_data`

## Errores Conocidos

No se han documentado errores en la capacidad.

## Ruta de menú

Se accede desde la pantalla `ca_posibles_que` o desde búsqueda de personas:

- **Legacy:** vest > posibles ca > posibles ca; vest > buscar persona > n r/dl (botón posibles CA).
- **Pills2:** vest > posibles ca > posibles ca; PERSONAS > Numerarios > Buscar n de la r/dl.
