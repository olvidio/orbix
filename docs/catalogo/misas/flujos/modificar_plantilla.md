---
id: "misas.modificar_plantilla.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "misas"
nombre: "Flujo - Modificar la plantilla de una zona"
capacidad: "misas.modificar_plantilla.gestionar"
pantallas_principales: ["misas.pantalla.modificar_plantilla"]
fragmentos: ["misas.pantalla.modificar_plantilla"]
acciones: ["obtener_datos"]
endpoints: ["/src/misas/modificar_plantilla_data"]
estado_revision: "revisado"
---

# Flujo - Modificar plantilla

## Objetivo De Usuario

Editar la plantilla de una zona: las tareas que se repiten cada semana o mes y quién las hace.

## Punto De Entrada

Menú **dre > Misas > Modificar plantilla** (Pills2: ATENCIÓN SACD > Gestión de misas > Modificar plantilla).

## Fragmentos O Pantallas Auxiliares

- `misas.pantalla.modificar_plantilla`

## Escenarios Inferidos

### Modificar la plantilla de una zona

Pasos propuestos:
1. Entrar en **Modificar plantilla**.
2. Elegir la zona en **Plantilla de la zona**, el **Tipo de plantilla** y el **Orden**.
3. Editar la cuadrícula que aparece.

Endpoints asociados:
- `/src/misas/modificar_plantilla_data`

### Importar de otra plantilla

Pasos propuestos:
1. En el desplegable **Importar de la plantilla** elegir el tipo de plantilla origen.
2. Pulsar **importar**. Aviso: «Borrará todos los datos introducidos en la plantilla que ves!!».

Endpoints asociados:
- `/src/misas/modificar_plantilla_data`

## Campos Y Acciones Detectadas En Pantalla

Campos:
- `form.id_zona`
- `form.importar_de_plantilla`
- `form.orden`
- `form.tipo_plantilla`
- `form.tipo_plantilla_destino`
- `form.tipo_plantilla_origen`
- `html.importar`

Acciones JavaScript:
- `button:importar`
- `fnjs_importar_de_plantilla_zona`
- `fnjs_ver_plantilla_zona`

## Endpoints Del Flujo

- `/src/misas/modificar_plantilla_data`

## Errores Conocidos

- `Usuario no encontrado`
- `No tiene permiso para ver esta página`

## Ruta de menú

- **Legacy:** dre > Misas > Modificar plantilla
- **Pills2:** ATENCIÓN SACD > Gestión de misas > Modificar plantilla
