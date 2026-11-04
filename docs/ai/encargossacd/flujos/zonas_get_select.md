---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "encargossacd"
titulo: "Elegir la zona de un encargo"
flujo: "encargossacd.zonas_get_select.gestionar.flujo"
preguntas: ["Como elegir la zona de un encargo en Elegir la zona de un encargo?"]
pantallas_principales: ["encargossacd.pantalla.encargo_ver"]
fragmentos: ["encargossacd.pantalla.encargo_ver"]
endpoints: ["/src/encargossacd/zonas_get_select_data"]
source: "docs/catalogo/encargossacd/flujos/zonas_get_select.md"
estado_revision: "generado"
---

# Ayuda IA - Elegir la zona de un encargo

Usa este documento para responder preguntas de usuario sobre como trabajar con `Elegir la zona de un encargo`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como elegir la zona de un encargo en Elegir la zona de un encargo?

## Donde Entrar

- Encargo Ver (`encargossacd.pantalla.encargo_ver`)

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Elegir la zona de un encargo

1. En el formulario de encargo, abrir **zona**.
2. Elegir una de las zonas de misas.

Referencias tecnicas para verificar la respuesta:
- `/src/encargossacd/zonas_get_select_data`

## Pantallas Y Fragmentos Relacionados

- `encargossacd.pantalla.encargo_ver`
- `encargossacd.pantalla.encargo_ver`

## Objetivo

Ofrecer las zonas de misas al crear o editar un encargo.

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Si el usuario pregunta por errores concretos, responder que estan pendientes salvo que el catalogo los documente.
