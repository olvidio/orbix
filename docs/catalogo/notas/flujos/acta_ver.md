---
id: "notas.acta_ver.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "notas"
nombre: "Flujo - Gestionar Acta Ver"
capacidad: "notas.acta_ver.gestionar"
pantallas_principales: []
fragmentos: ["notas.pantalla.acta_ver"]
acciones: ["ver_formulario"]
endpoints: ["/src/notas/acta_ver_form_data", "/src/notas/acta_ver_notas_listado_data", "/src/notas/acta_ver_add_persona_form_data", "/src/notas/acta_ver_add_persona", "/src/notas/acta_nueva", "/src/notas/acta_modificar"]
estado_revision: "revisado"
---

# Flujo - Gestionar Acta Ver

Ver/editar cabecera de acta; en apertura desde listado, ver alumnos del acta y (si hay permiso) añadir nota.

## Objetivo De Usuario

Consultar o modificar la cabecera del acta (asignatura, fechas, libro, tribunal, PDF) y, cuando se abre desde el listado de actas, ver las notas ya grabadas y añadir un alumno.

## Punto De Entrada

- Desde `acta_select` (listado) → fragmento `acta_ver` **standalone**.
- Desde actividad de estudios (`actividadestudios/acta_notas`) → mismo fragmento **embebido** (sin listado/alta de alumno en esta pantalla).

## Fragmentos O Pantallas Auxiliares

- `notas.pantalla.acta_ver`

## Escenarios Inferidos

### Ver / editar cabecera

1. Abrir acta desde el listado o desde la actividad.
2. Cargar cabecera (`acta_ver_form_data`).
3. Guardar alta (`acta_nueva`) o cambios (`acta_modificar`).

### Listado de notas (solo standalone)

1. Con acta existente (no modo nueva) y asignatura válida, la UI pide `acta_ver_notas_listado_data`.
2. Se muestra tabla id/nombre/nota/situación; avisos si hay notas sin acceso al nombre.

### Añadir alumno (solo standalone + permiso escritura + sin PDF)

1. Pedir `acta_ver_add_persona_form_data` (desplegable de candidatos).
2. Elegir persona, nota y máximo; enviar `acta_ver_add_persona`.
3. Recargar listado / mensaje de esquema de escritura.

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
