---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "encargossacd"
titulo: "Elegir el tipo de encargo"
flujo: "encargossacd.encargo_lst_tipo_enc.gestionar.flujo"
preguntas: ["Como elegir el tipo de encargo en Elegir el tipo de encargo?"]
pantallas_principales: ["encargossacd.pantalla.encargo_ver"]
fragmentos: ["encargossacd.pantalla.encargo_ver"]
endpoints: ["/src/encargossacd/encargo_lst_tipo_enc_data"]
source: "docs/catalogo/encargossacd/flujos/encargo_lst_tipo_enc.md"
estado_revision: "generado"
---

# Ayuda IA - Elegir el tipo de encargo

Usa este documento para responder preguntas de usuario sobre como trabajar con `Elegir el tipo de encargo`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como elegir el tipo de encargo en Elegir el tipo de encargo?

## Donde Entrar

- Encargo Ver (`encargossacd.pantalla.encargo_ver`)

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Elegir el tipo de encargo

1. En el formulario de encargo, elegir el **grupo**.
2. El desplegable **tipo encargo** muestra solo los tipos de ese grupo.

Referencias tecnicas para verificar la respuesta:
- `/src/encargossacd/encargo_lst_tipo_enc_data`

## Pantallas Y Fragmentos Relacionados

- `encargossacd.pantalla.encargo_ver`
- `encargossacd.pantalla.encargo_ver`

## Objetivo

Ofrecer los tipos de encargo que corresponden al grupo elegido.

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Si el usuario pregunta por errores concretos, responder que estan pendientes salvo que el catalogo los documente.
