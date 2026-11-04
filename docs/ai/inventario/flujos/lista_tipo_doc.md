---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "inventario"
titulo: "Elegir el tipo de documento"
flujo: "inventario.lista_tipo_doc.gestionar.flujo"
preguntas: ["Como elegir el tipo de documento en Elegir el tipo de documento?"]
pantallas_principales: ["inventario.pantalla.docs_asignar_que"]
fragmentos: ["inventario.pantalla.docs_asignar_que", "inventario.pantalla.equipajes_form_add"]
endpoints: ["/src/inventario/lista_tipo_doc"]
source: "docs/catalogo/inventario/flujos/lista_tipo_doc.md"
estado_revision: "generado"
---

# Ayuda IA - Elegir el tipo de documento

Usa este documento para responder preguntas de usuario sobre como trabajar con `Elegir el tipo de documento`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como elegir el tipo de documento en Elegir el tipo de documento?

## Donde Entrar

- Asignar documentos — selector (`inventario.pantalla.docs_asignar_que`)

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Elegir el tipo de documento

1. Abrir **asignar documento** desde el menú.
2. Elegir el documento en el desplegable **seleccione un tipo de documento**.
3. Pulsar **lista de asignados** para ver los centros que ya lo tienen, **lista de NO asignados** para ver los que no, o uno de los dos botones de asignación (**Asignar a centros (sin especificar lugar)** / **Asignar a dlb/casa (especificar lugar)**).

Referencias tecnicas para verificar la respuesta:
- `/src/inventario/lista_tipo_doc`

## Pantallas Y Fragmentos Relacionados

- `inventario.pantalla.docs_asignar_que`
- `inventario.pantalla.docs_asignar_que`
- `inventario.pantalla.equipajes_form_add`

## Objetivo

Elegir el tipo de documento con el que se va a trabajar antes de asignarlo o de consultar a quién se ha asignado.

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Si el usuario pregunta por errores concretos, responder que estan pendientes salvo que el catalogo los documente.
