---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "misas"
titulo: "Elegir encargo de la zona"
flujo: "misas.desplegable_encargos.gestionar.flujo"
preguntas: ["Como elegir encargo de la zona en Elegir encargo de la zona?"]
pantallas_principales: ["misas.pantalla.ver_encargos_centros"]
fragmentos: ["misas.pantalla.ver_encargos_centros"]
endpoints: ["/src/misas/desplegable_encargos"]
source: "docs/catalogo/misas/flujos/desplegable_encargos.md"
estado_revision: "generado"
---

# Ayuda IA - Elegir encargo de la zona

Usa este documento para responder preguntas de usuario sobre como trabajar con `Elegir encargo de la zona`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como elegir encargo de la zona en Elegir encargo de la zona?

## Donde Entrar

- Ver Encargos Centros (`misas.pantalla.ver_encargos_centros`)

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Elegir encargo de la zona

1. En la ventana de encargo visible para un centro, elegir la zona.
2. El desplegable **Encargo** se rellena con los encargos de esa zona.

Referencias tecnicas para verificar la respuesta:
- `/src/misas/desplegable_encargos`

## Pantallas Y Fragmentos Relacionados

- `misas.pantalla.ver_encargos_centros`
- `misas.pantalla.ver_encargos_centros`

## Objetivo

Ofrecer los encargos de la zona elegida en la ventana de encargo-centro.

## Errores Documentados

- `_(ninguno documentado)_`

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Usar la seccion "Errores Documentados" cuando el usuario reporte un mensaje conocido.
