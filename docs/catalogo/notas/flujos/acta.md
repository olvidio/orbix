---
id: "notas.acta.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "notas"
nombre: "Flujo - Crear un acta"
capacidad: "notas.acta.gestionar"
pantallas_principales: ["notas.pantalla.acta_select"]
fragmentos: ["notas.pantalla.acta_select", "notas.pantalla.acta_ver"]
acciones: ["crear", "eliminar"]
endpoints: ["/src/notas/acta_eliminar", "/src/notas/acta_nueva"]
estado_revision: "revisado"
---

# Flujo - Gestionar Acta

Gestión del ciclo de vida de actas: listado, alta, edición, eliminación, PDF e impresión.

## Objetivo De Usuario

Ciclo completo de un acta: listarla, abrirla, crearla, modificarla o eliminarla, con PDF e impresión.

## Punto De Entrada

Menú **vest > actas... > actas**.

## Fragmentos O Pantallas Auxiliares

- `notas.pantalla.acta_select`
- `notas.pantalla.acta_ver`

## Escenarios Inferidos

### Crear un acta

Pasos propuestos:
1. Entrar en **actas** y pulsar nueva acta: se abre **DATOS DEL ACTA**.
2. El **Acta** viene propuesta con el siguiente número de la delegación (por ejemplo «dlb 39/26», con «última = dlb 38/26»); se puede cambiar, con formato num/aa.
3. Rellenar la **Fecha acta** (obligatoria), **Libro**, **Página**, **Línea**, **Lugar** y **Observaciones**.
4. Elegir la **Asignatura** escribiendo parte del nombre y eligiendo de la lista (es obligatoria aunque la pantalla no lo avise; si falta sale un error técnico, D-NOT-2).
5. En **Tribunal**, escribir el **Examinador** (con **Más** se añaden más).
6. Pulsar **Guardar cambios acta**. Después, abriendo el acta, aparece **Alumnos del acta** (al principio «Todavía no hay notas asociadas a este acta») y **Añadir alumno al acta**.

Endpoints asociados:
- `/src/notas/acta_eliminar`
- `/src/notas/acta_nueva`

### Eliminar un acta

Pasos propuestos:
1. En **actas**, marcar el acta y pulsar eliminar; confirmar «esto eliminará los datos del acta, pero no las notas que mantendrán el número de acta». Las notas ya puestas siguen existiendo con ese número de acta.

Endpoints asociados:
- `/src/notas/acta_eliminar`
- `/src/notas/acta_nueva`

## Campos Y Acciones Detectadas En Pantalla

Campos:
- `form.acta`
- `form.acta_pdf`
- `form.mod`
- `form.search`
- `form.sel`
- `html.acta`
- `html.acta_pdf`
- `html.btn_ok`
- `html.examinadores[]`
- `html.id_asignatura`
- `html.mod`
- `html.refresh`
- `post.acta`
- `post.refresh`
- `post.stack`
- `post.titulo`

Acciones JavaScript:
- `fnjs_actualizar`
- `fnjs_add_examinador`
- `fnjs_autocomplete_exam`
- `fnjs_cmb_acta`
- `fnjs_descargar_pdf`
- `fnjs_eliminar`
- `fnjs_eliminar_pdf`
- `fnjs_enviar`
- `fnjs_enviar_formulario`
- `fnjs_guardar_acta`
- `fnjs_imprimir`
- `fnjs_left_side_hide`
- `fnjs_modificar`
- `fnjs_nueva_convocatoria`
- `fnjs_nuevo`
- `fnjs_solo_uno`
- `fnjs_upload_pdf`

## Endpoints Del Flujo

- `/src/notas/acta_select_data`
- `/src/notas/acta_ver_form_data`
- `/src/notas/acta_nueva`
- `/src/notas/acta_modificar`
- `/src/notas/acta_eliminar`

## Errores Conocidos

- ``No se encuentra el acta``

## Ruta de menú

- **Legacy:** sin entrada de menú en el índice (fragmento/dossier)
- **Pills2:** sin entrada de menú en el índice (fragmento/dossier)
