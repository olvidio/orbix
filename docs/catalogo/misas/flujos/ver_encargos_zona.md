---
id: "misas.ver_encargos_zona.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "misas"
nombre: "Flujo - Gestionar los encargos de una zona"
capacidad: "misas.ver_encargos_zona.gestionar"
pantallas_principales: ["misas.pantalla.ver_encargos_zona"]
fragmentos: ["misas.pantalla.ver_encargos_zona"]
acciones: ["obtener_datos"]
endpoints: ["/src/misas/ver_encargos_zona_data"]
estado_revision: "revisado"
---

# Flujo - Ver encargos zona

## Objetivo De Usuario

Ver, crear, modificar y eliminar los encargos de una zona.

## Punto De Entrada

Menú **dre > Misas > Modificar encargos** (Pills2: ATENCIÓN SACD > Gestión de misas > Modificar encargos).

## Fragmentos O Pantallas Auxiliares

- `misas.pantalla.ver_encargos_zona`

## Escenarios Inferidos

### Gestionar los encargos de una zona

Pasos propuestos:
1. En **Modificar encargos**, elegir la zona.
2. Para crear, pulsar **nuevo** y rellenar el formulario: elegir el **Tipo de encargo** (por ejemplo Misa a 1ª hora, Misa no a 1ª hora, rtm con Misa a 1ª hora, rtm sin Misa, bendición) y el **Lugar**; el botón **generar** escribe el nombre del **Encargo** como «tipo (lugar)». Completar **Descripción lugar**, **Orden**, **Prioridad**, **Observaciones** e **Idioma** y pulsar **Grabar**.
3. Para modificar, pulsar el encargo, cambiar y **Grabar**.
4. Para quitar, abrirlo y pulsar **Eliminar encargo**.

Endpoints asociados:
- `/src/misas/ver_encargos_zona_data`

## Campos Y Acciones Detectadas En Pantalla

Campos:
- `form.descripcion_lugar`
- `form.encargo`
- `form.id_enc`
- `form.id_tipo_enc`
- `form.id_ubi`
- `form.id_zona`
- `form.idioma_enc`
- `form.observ`
- `form.orden`
- `form.prioridad`
- `html.nuevo`
- `post.id_zona`
- `post.orden`

Acciones JavaScript:
- `fnjs_generarNomEnc`
- `fnjs_nuevo`
- `fnjs_refresh_grid`

## Endpoints Del Flujo

- `/src/misas/ver_encargos_zona_data`

## Errores Conocidos

- _(ninguno documentado)_

## Ruta de menú

- **Legacy:** dre > Misas > Modificar encargos
- **Pills2:** ATENCIÓN SACD > Gestión de misas > Modificar encargos
