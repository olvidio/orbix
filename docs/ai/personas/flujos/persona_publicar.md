---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "personas"
titulo: "Publicar persona hacia otra DL"
flujo: "personas.persona_publicar.gestionar.flujo"
preguntas: []
pantallas_principales: []
fragmentos: ["personas.pantalla.persona_publicar_form"]
endpoints: ["/src/personas/persona_publicar_form_data", "/src/personas/persona_publicar"]
source: "docs/catalogo/personas/flujos/persona_publicar.md"
estado_revision: "generado"
---

# Ayuda IA - Publicar persona hacia otra DL

Usa este documento para responder preguntas de usuario sobre como trabajar con `Publicar persona hacia otra DL`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:

## Donde Entrar

- Pantalla pendiente de revisar.

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Pantallas Y Fragmentos Relacionados

- `personas.pantalla.persona_publicar_form`

## Objetivo

Compartir temporalmente una persona con otra delegación sin trasladarla.

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
