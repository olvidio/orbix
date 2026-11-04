---
id: "personas.persona_publicar.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "personas"
nombre: "Flujo - Publicar una persona hacia otra dl"
capacidad: "personas.persona_publicar.gestionar"
pantallas_principales: ["personas.pantalla.persona_publicar_form"]
fragmentos: ["personas.pantalla.persona_publicar_form"]
acciones: ["ver_formulario", "publicar"]
endpoints: ["/src/personas/persona_publicar_form_data", "/src/personas/persona_publicar"]
estado_revision: "revisado"
---

# Flujo - Publicar persona hacia otra DL

Hace visible una persona de la DL propia en el desplegable de personas de otra DL (caso B), con
caducidad por defecto de un mes.

## Objetivo De Usuario

Compartir temporalmente una persona con otra delegación, sin trasladarla.

## Punto De Entrada

Botón **publicar** de la lista de personas; solo sale con permiso de las oficinas est, sm o agd.

## Escenarios Inferidos

### Publicar una persona hacia otra dl

Pasos propuestos:
1. En la lista de personas, seleccionar a la persona y pulsar **publicar**.
2. Se abre un formulario con su nombre y un desplegable de delegaciones; elegir la delegación destino (es obligatorio).
3. Guardar. Sale el aviso «persona publicada» y se vuelve a la lista.

Endpoints asociados:
- `/src/personas/persona_publicar_form_data`
- `/src/personas/persona_publicar`

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
