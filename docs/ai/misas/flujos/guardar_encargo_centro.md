---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "misas"
titulo: "Guardar un encargo visible para un centro"
flujo: "misas.guardar_encargo_centro.gestionar.flujo"
preguntas: ["Como guardar un encargo visible para un centro en Guardar un encargo visible para un centro?"]
pantallas_principales: ["misas.pantalla.ver_encargos_centros"]
fragmentos: ["misas.pantalla.ver_encargos_centros"]
endpoints: ["/src/misas/guardar_encargo_centro"]
source: "docs/catalogo/misas/flujos/guardar_encargo_centro.md"
estado_revision: "generado"
---

# Ayuda IA - Guardar un encargo visible para un centro

Usa este documento para responder preguntas de usuario sobre como trabajar con `Guardar un encargo visible para un centro`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como guardar un encargo visible para un centro en Guardar un encargo visible para un centro?

## Donde Entrar

- Ver Encargos Centros (`misas.pantalla.ver_encargos_centros`)

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Guardar un encargo visible para un centro

1. Elegir zona, encargo y centro.
2. Pulsar **Grabar**.

Referencias tecnicas para verificar la respuesta:
- `/src/misas/guardar_encargo_centro`

## Pantallas Y Fragmentos Relacionados

- `misas.pantalla.ver_encargos_centros`
- `misas.pantalla.ver_encargos_centros`

## Objetivo

Hacer visible un encargo de zona en un centro, o cambiarlo.

## Errores Documentados

- `No se encuentra el encargo-centro %s`
- `<repositorio getErrorTxt()>`

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Usar la seccion "Errores Documentados" cuando el usuario reporte un mensaje conocido.
