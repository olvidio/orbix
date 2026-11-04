---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "personas"
titulo: "Publicar una persona hacia otra dl"
flujo: "personas.persona_publicar.gestionar.flujo"
preguntas: ["Como publicar una persona hacia otra dl en Publicar una persona hacia otra dl?"]
pantallas_principales: ["personas.pantalla.persona_publicar_form"]
fragmentos: ["personas.pantalla.persona_publicar_form"]
endpoints: ["/src/personas/persona_publicar_form_data", "/src/personas/persona_publicar"]
source: "docs/catalogo/personas/flujos/persona_publicar.md"
estado_revision: "generado"
---

# Ayuda IA - Publicar una persona hacia otra dl

Usa este documento para responder preguntas de usuario sobre como trabajar con `Publicar una persona hacia otra dl`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como publicar una persona hacia otra dl en Publicar una persona hacia otra dl?

## Donde Entrar

- Publicar persona (`personas.pantalla.persona_publicar_form`)

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Publicar una persona hacia otra dl

1. En la lista de personas, seleccionar a la persona y pulsar **publicar**.
2. Se abre un formulario con su nombre y un desplegable de delegaciones; elegir la delegación destino (es obligatorio).
3. Guardar. Sale el aviso «persona publicada» y se vuelve a la lista.

Referencias tecnicas para verificar la respuesta:
- `/src/personas/persona_publicar_form_data`
- `/src/personas/persona_publicar`

## Pantallas Y Fragmentos Relacionados

- `personas.pantalla.persona_publicar_form`
- `personas.pantalla.persona_publicar_form`

## Objetivo

Compartir temporalmente una persona con otra delegación, sin trasladarla.

## Errores Documentados

- `No se encuentra la persona`
- `No se puede determinar el esquema de la persona`
- `Datos de persona no válidos`
- `Debe indicar al menos una delegación destino`
- `No se puede publicar hacia la propia delegación`
- `No se ha podido publicar la persona`
- `FE: Debe elegir una delegación (alert si dl vacío)`

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Usar la seccion "Errores Documentados" cuando el usuario reporte un mensaje conocido.
