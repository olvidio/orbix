---
id: "notas.acta_ver.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "notas"
nombre: "Flujo - Ver o modificar la cabecera del acta"
capacidad: "notas.acta_ver.gestionar"
pantallas_principales: ["notas.pantalla.acta_ver"]
fragmentos: ["notas.pantalla.acta_ver"]
acciones: ["ver_formulario"]
endpoints: ["/src/notas/acta_ver_form_data", "/src/notas/acta_ver_notas_listado_data", "/src/notas/acta_ver_add_persona_form_data", "/src/notas/acta_ver_add_persona", "/src/notas/acta_nueva", "/src/notas/acta_modificar"]
estado_revision: "revisado"
---

# Flujo - Gestionar Acta Ver

Ver/editar cabecera de acta; en apertura desde listado, ver alumnos del acta y (si hay permiso) añadir nota.

## Objetivo De Usuario

Consultar o modificar la cabecera de un acta (asignatura, fechas, libro, tribunal, PDF) y, cuando se abre desde el listado de actas, ver las notas ya grabadas y añadir un alumno.

## Punto De Entrada

Se abre desde el listado de actas (ESTUDIOS > Actas), y también dentro de una actividad de estudios, desde el acta de notas de una asignatura. Dentro de la actividad no sale el listado de notas ni el alta de alumnos.

## Fragmentos O Pantallas Auxiliares

- `notas.pantalla.acta_ver`

## Escenarios Inferidos

### Ver o modificar la cabecera del acta

Pasos propuestos:
1. Abrir el acta desde el listado o desde la actividad.
2. Se muestra la cabecera con los datos del acta.
3. Para un acta nueva, rellenarla y guardar; para uno existente, corregir lo que haga falta y guardar los cambios.

Endpoints asociados:
- `/src/notas/acta_ver_form_data`
- `/src/notas/acta_ver_notas_listado_data`
- `/src/notas/acta_ver_add_persona_form_data`
- `/src/notas/acta_ver_add_persona`
- `/src/notas/acta_nueva`
- `/src/notas/acta_modificar`

### Ver las notas del acta

Pasos propuestos:
1. Con un acta ya creada y con asignatura, debajo de la cabecera aparece la tabla de alumnos con su nombre, nota y situación.
2. Si de alguna nota no se puede ver el nombre, sale un aviso.

Endpoints asociados:
- `/src/notas/acta_ver_form_data`
- `/src/notas/acta_ver_notas_listado_data`
- `/src/notas/acta_ver_add_persona_form_data`
- `/src/notas/acta_ver_add_persona`
- `/src/notas/acta_nueva`
- `/src/notas/acta_modificar`

### Añadir un alumno al acta

Pasos propuestos:
1. Solo está disponible desde el listado de actas, con permiso de escritura y si el acta no tiene el PDF firmado.
2. Elegir a la persona en el desplegable de candidatos (de la dl o publicados para la dl; no salen quienes están en repaso ni quienes ya tienen nota de esa asignatura).
3. Escribir la nota y la nota máxima y guardar.
4. El listado se recarga con el alumno añadido.

Endpoints asociados:
- `/src/notas/acta_ver_form_data`
- `/src/notas/acta_ver_notas_listado_data`
- `/src/notas/acta_ver_add_persona_form_data`
- `/src/notas/acta_ver_add_persona`
- `/src/notas/acta_nueva`
- `/src/notas/acta_modificar`

## Casos particulares

- Embebido en actividad (`notas`/`Qnotas` no vacío): no listado ni alta.
- Acta firmada (PDF): cabecera readonly; no alta; las notas de esa acta no se pueden modificar.
- Sin PDF: se puede corregir e imprimir de nuevo. Subir el PDF firmado exige haber impreso después del último cambio (si no, mensaje de reimpresión).
- `permiso !== 3` o ámbito rstgr: no formulario de alta.
- Candidatos: DL + publicados para mi DL; excluye Repaso y quien ya tiene nota en la asignatura.

## Endpoints Del Flujo

- `/src/notas/acta_ver_form_data`
- `/src/notas/acta_ver_notas_listado_data`
- `/src/notas/acta_ver_add_persona_form_data`
- `/src/notas/acta_ver_add_persona`
- `/src/notas/acta_nueva`
- `/src/notas/acta_modificar`

## Errores Conocidos

- `Faltan acta o persona`
- `No se encuentra el acta`
- `El acta está firmada y no se puede modificar`
- `El acta no tiene asignatura`
- `Falta el acta`
- Aviso listado: `existe una nota de la que no se tiene acceso al nombre (id_nom = %s)`

## Ruta de menú

- **Legacy:** sin entrada de menú en el índice (fragmento desde listado ESTUDIOS > Actas)
- **Pills2:** sin entrada de menú en el índice (fragmento desde listado ESTUDIOS > Actas)
