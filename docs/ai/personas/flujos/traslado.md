---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "personas"
titulo: "Traslado"
flujo: "personas.traslado.gestionar.flujo"
preguntas: ["Como crear o modificar en Traslado?", "Como abrir el formulario en Traslado?"]
pantallas_principales: []
fragmentos: ["personas.pantalla.traslado_form"]
endpoints: ["/src/personas/traslado_form_data", "/src/personas/traslado_update"]
source: "docs/catalogo/personas/flujos/traslado.md"
estado_revision: "generado"
---

# Ayuda IA - Traslado

Usa este documento para responder preguntas de usuario sobre como trabajar con `Traslado`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como crear o modificar en Traslado?
- Como abrir el formulario en Traslado?

## Donde Entrar

- Pantalla pendiente de revisar.

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Crear o modificar

1. Abrir el formulario de alta o modificacion.
2. Rellenar o corregir los campos requeridos.
3. Guardar los cambios.
4. Comprobar que la pantalla vuelve al listado y refleja el cambio.

Referencias tecnicas para verificar la respuesta:
- `/src/personas/traslado_update`

## Abrir el formulario

1. Desde el listado, elegir crear un nuevo registro o modificar uno existente.
2. Abrir el formulario asociado.
3. Comprobar que los campos cargados corresponden al registro o contexto seleccionado.

Referencias tecnicas para verificar la respuesta:
- `/src/personas/traslado_form_data`

## Pantallas Y Fragmentos Relacionados

- `personas.pantalla.traslado_form`

## Objetivo

Gestiona Traslado. Endpoint JSON: aplica un traslado de centro y/o delegacion. Endpoint JSON: datos para el formulario traslado_form.phtml.

## Errores Documentados

- `Faltan id_pau u obj_pau`
- `No existe la clase de la persona`
- `No se encuentra la persona`

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Usar la seccion "Errores Documentados" cuando el usuario reporte un mensaje conocido.
