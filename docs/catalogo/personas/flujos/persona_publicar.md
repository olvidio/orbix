---
id: "personas.persona_publicar.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "personas"
nombre: "Flujo - Publicar persona hacia otra DL"
capacidad: "personas.persona_publicar.gestionar"
pantallas_principales: []
fragmentos: ["personas.pantalla.persona_publicar_form"]
acciones: ["ver_formulario", "publicar"]
endpoints: ["/src/personas/persona_publicar_form_data", "/src/personas/persona_publicar"]
estado_revision: "revisado"
---

# Flujo - Publicar persona hacia otra DL

Hace visible una persona de la DL propia en el desplegable de personas de otra DL (caso B), con
caducidad por defecto de un mes.

## Objetivo De Usuario

Compartir temporalmente una persona con otra delegación sin trasladarla.

## Punto De Entrada

- Listado `personas_select`: botón «publicar» (`fnjs_publicar`) si hay permiso oficina
  `est`, `sm` o `agd`.

## Escenarios

### Ver formulario

1. Seleccionar persona en el listado (`sel=id_nom#id_tabla`).
2. Pulsar «publicar» → `persona_publicar_form.php`.
3. Carga `persona_publicar_form_data` y muestra nombre + desplegable de DL.

### Publicar

1. Elegir DL destino (obligatorio en FE).
2. Guardar → `persona_publicar` con `id_nom`, `id_schema`, `dl`.
3. Éxito: alerta «persona publicada» y navegación atrás.

## Endpoints Del Flujo

- `/src/personas/persona_publicar_form_data`
- `/src/personas/persona_publicar`

## Errores Conocidos

- `No se encuentra la persona`
- `No se puede determinar el esquema de la persona`
- `Datos de persona no válidos`
- `Debe indicar al menos una delegación destino`
- `No se puede publicar hacia la propia delegación`
- `No se ha podido publicar la persona`
- FE: `Debe elegir una delegación` (alert si `dl` vacío)

## Ruta de menú

- sin entrada de menú en el índice.
