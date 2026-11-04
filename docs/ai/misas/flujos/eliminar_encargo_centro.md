---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "misas"
titulo: "Quitar un encargo visible para un centro"
flujo: "misas.eliminar_encargo_centro.gestionar.flujo"
preguntas: ["Como quitar un encargo visible para un centro en Quitar un encargo visible para un centro?"]
pantallas_principales: ["misas.pantalla.ver_encargos_centros"]
fragmentos: ["misas.pantalla.ver_encargos_centros"]
endpoints: ["/src/misas/eliminar_encargo_centro"]
source: "docs/catalogo/misas/flujos/eliminar_encargo_centro.md"
estado_revision: "generado"
---

# Ayuda IA - Quitar un encargo visible para un centro

Usa este documento para responder preguntas de usuario sobre como trabajar con `Quitar un encargo visible para un centro`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como quitar un encargo visible para un centro en Quitar un encargo visible para un centro?

## Donde Entrar

- Ver Encargos Centros (`misas.pantalla.ver_encargos_centros`)

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Quitar un encargo visible para un centro

1. Abrir el encargo-centro y pulsar **Eliminar encargo**.
2. Confirmar.

Referencias tecnicas para verificar la respuesta:
- `/src/misas/eliminar_encargo_centro`

## Pantallas Y Fragmentos Relacionados

- `misas.pantalla.ver_encargos_centros`
- `misas.pantalla.ver_encargos_centros`

## Objetivo

Quitar un encargo de la vista de un centro.

## Errores Documentados

- `Falta el identificador del encargo-centro a eliminar`
- `No se encuentra el encargo-centro %s`
- `<repositorio getErrorTxt()>`

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Usar la seccion "Errores Documentados" cuando el usuario reporte un mensaje conocido.
