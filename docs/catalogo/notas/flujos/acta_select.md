---
id: "notas.acta_select.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "notas"
nombre: "Flujo - Buscar un acta"
capacidad: "notas.acta_select.gestionar"
pantallas_principales: ["notas.pantalla.acta_select"]
fragmentos: ["notas.pantalla.acta_select"]
acciones: ["obtener_datos"]
endpoints: ["/src/notas/acta_select_data"]
estado_revision: "revisado"
---

# Flujo - Gestionar Acta Select

Propuesta generada automaticamente desde la capacidad `notas.acta_select.gestionar` y sus pantallas relacionadas.

## Objetivo De Usuario

Buscar actas del curso y abrir, modificar, imprimir o descargar su PDF.

## Punto De Entrada

Menú **vest > actas... > actas** (Pills2: ESTUDIOS > Actas y certificados > Actas).

## Fragmentos O Pantallas Auxiliares

- `notas.pantalla.acta_select`

## Escenarios Inferidos

### Buscar un acta

Pasos propuestos:
1. Entrar en **actas**.
2. Escribir en el buscador el número de acta con formato nn/aa (por ejemplo 12/21). Se pueden usar comodines: un punto (.) es cualquier carácter y .* cualquier cantidad de caracteres (por ejemplo «dlm. 2/19» devuelve dlmE 2/19, dlmO 2/19…).
3. En una delegación solo se buscan las actas de la propia delegación o región; una región con delegaciones busca en todas.
4. Pulsar el acta de la tabla para abrirla.

Endpoints asociados:
- `/src/notas/acta_select_data`

### Imprimir o descargar un acta

Pasos propuestos:
1. En la tabla, marcar el acta.
2. Elegir imprimir o descargar el PDF.

Endpoints asociados:
- `/src/notas/acta_select_data`

## Campos Y Acciones Detectadas En Pantalla

Campos:
- `form.acta`
- `form.mod`
- `form.sel`
- `html.acta`
- `html.btn_ok`
- `html.mod`
- `html.refresh`
- `post.acta`
- `post.refresh`
- `post.stack`
- `post.titulo`

Acciones JavaScript:
- `fnjs_actualizar`
- `fnjs_descargar_pdf`
- `fnjs_eliminar`
- `fnjs_enviar`
- `fnjs_enviar_formulario`
- `fnjs_imprimir`
- `fnjs_left_side_hide`
- `fnjs_modificar`
- `fnjs_nuevo`
- `fnjs_solo_uno`

## Endpoints Del Flujo

- `/src/notas/acta_select_data`

## Errores Conocidos

No se han documentado errores en la capacidad.

## Ruta de menú

- **Legacy:** vest > actas... > actas; stgr > actas > actas
- **Pills2:** ESTUDIOS > Actas y certificados > Actas; vest > actas... > actas; stgr > actas > actas
