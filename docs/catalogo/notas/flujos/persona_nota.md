---
id: "notas.persona_nota.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "notas"
nombre: "Flujo - Añadir una nota"
capacidad: "notas.persona_nota.gestionar"
pantallas_principales: ["notas.pantalla.form_notas_de_una_persona"]
fragmentos: ["notas.pantalla.form_notas_de_una_persona"]
acciones: ["crear", "eliminar"]
endpoints: ["/src/notas/persona_nota_eliminar", "/src/notas/persona_nota_nueva"]
estado_revision: "revisado"
---

# Flujo - Gestionar Persona Nota

Propuesta generada automaticamente desde la capacidad `notas.persona_nota.gestionar` y sus pantallas relacionadas.

## Objetivo De Usuario

Apuntar a una persona una asignatura aprobada, con su nota y el acta o certificado.

## Punto De Entrada

Menú **vest > buscar persona** (por ejemplo **n r/dl**): buscar a la persona, abrir su ficha y, en «Relación de dossiers», pulsar **Notas del stgr de una persona (tessera)**.

## Fragmentos O Pantallas Auxiliares

- `notas.pantalla.form_notas_de_una_persona`

## Escenarios Inferidos

### Añadir una nota

Pasos propuestos:
1. Abrir el dossier de notas de la persona (se titula «Notas del stgr»; sin notas dice «no hay ninguna fila») y pulsar **añadir nota**.
2. Se abre «asignaturas aprobadas». Elegir si procede de un **acta** o de un **certificado**: delegación (por ejemplo dlb) y número (formato num/aa, por ejemplo 38/26).
3. Elegir la **Asignatura**, escribir la **nota** (y «sobre» cuánto, por defecto 10), la **situación** (por ejemplo «nota numérica») y la **fecha acta**.
4. Marcar **preceptor** si procede, elegir la **época** (sin especificar, ca o semestre invierno), la actividad en **cursada en** (enlace **añadir ca**) y un **detalle** opcional.
5. Pulsar **Guardar** una sola vez (un doble clic no guarda la nota dos veces). Una asignatura que ya tiene nota deja de salir en el desplegable de asignaturas, así que no se puede guardar dos veces. Sale el aviso «Nota guardada en el esquema H-dlbv» (el nombre del esquema es intencionado) y vuelve la tabla con asignatura, nota (por ejemplo «Cum laude (8/10)»), acta, fecha acta, preceptor, época (ca, semestre invierno o sin especificar), detalle y cursada en.
6. Si se guarda dos veces la misma nota, quedan dos filas iguales.

Endpoints asociados:
- `/src/notas/persona_nota_eliminar`
- `/src/notas/persona_nota_nueva`

### Quitar una nota

Pasos propuestos:
1. En la tabla de notas marcar la fila y pulsar **borrar asignatura**.
2. Confirmar «¿Está seguro que desea borrar la nota de esta asignatura?».

Endpoints asociados:
- `/src/notas/persona_nota_eliminar`
- `/src/notas/persona_nota_nueva`

## Campos Y Acciones Detectadas En Pantalla

Campos:
- `form.acta`
- `form.dl_org`
- `form.f_acta_iso`
- `form.id_nom`
- `html.acta`
- `html.detalle`
- `html.epoca`
- `html.f_acta`
- `html.id_asignatura`
- `html.nota_max`
- `html.nota_num`
- `html.preceptor`
- `html.tipo_acta`
- `post.id_asignatura_real`
- `post.id_pau`
- `post.mod`
- `post.obj_pau`
- `post.pau`
- `post.permiso`
- `post.sel`

Acciones JavaScript:
- `fnjs_buscar_acta`
- `fnjs_buscar_ca`
- `fnjs_cerrar`
- `fnjs_cmb_opcional`
- `fnjs_cmb_preceptor`
- `fnjs_comprobar_fecha`
- `fnjs_construir_desplegable`
- `fnjs_guardar`
- `fnjs_modificar`
- `fnjs_nota`
- `fnjs_update_activ`
- `fnjs_update_div`

## Endpoints Del Flujo

- `/src/notas/persona_nota_eliminar`
- `/src/notas/persona_nota_nueva`

## Errores Conocidos

No se han documentado errores en la capacidad.

## Ruta de menú

- **Legacy:** sin entrada de menú en el índice (fragmento/dossier)
- **Pills2:** sin entrada de menú en el índice (fragmento/dossier)
