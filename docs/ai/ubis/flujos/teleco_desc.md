---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "ubis"
titulo: "Elegir la descripción de una teleco"
flujo: "ubis.teleco_desc.gestionar.flujo"
preguntas: ["Como elegir la descripción de una teleco en Elegir la descripción de una teleco?"]
pantallas_principales: ["ubis.pantalla.teleco_desc_lista_ajax"]
fragmentos: ["ubis.pantalla.teleco_desc_lista_ajax"]
endpoints: ["/src/ubis/teleco_desc_lista"]
source: "docs/catalogo/ubis/flujos/teleco_desc.md"
estado_revision: "generado"
---

# Ayuda IA - Elegir la descripción de una teleco

Usa este documento para responder preguntas de usuario sobre como trabajar con `Elegir la descripción de una teleco`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como elegir la descripción de una teleco en Elegir la descripción de una teleco?

## Donde Entrar

- Teleco Desc Lista Ajax (`ubis.pantalla.teleco_desc_lista_ajax`)

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Elegir la descripción de una teleco

1. En el formulario de teleco, elegir el tipo.
2. El desplegable de descripción se rellena con las opciones de ese tipo.

Referencias tecnicas para verificar la respuesta:
- `/src/ubis/teleco_desc_lista`

## Pantallas Y Fragmentos Relacionados

- `ubis.pantalla.teleco_desc_lista_ajax`
- `ubis.pantalla.teleco_desc_lista_ajax`

## Objetivo

Ofrecer las descripciones de telecomunicación según el tipo elegido.

## Errores Documentados

- `_(ninguno documentado)_`

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Usar la seccion "Errores Documentados" cuando el usuario reporte un mensaje conocido.
