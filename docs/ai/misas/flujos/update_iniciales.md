---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "misas"
titulo: "Update Iniciales"
flujo: "misas.update_iniciales.gestionar.flujo"
preguntas: ["Como ejecutar en Update Iniciales?"]
pantallas_principales: []
fragmentos: ["misas.pantalla.ver_iniciales_zona"]
endpoints: ["/src/misas/update_iniciales"]
source: "docs/catalogo/misas/flujos/update_iniciales.md"
estado_revision: "generado"
---

# Ayuda IA - Update Iniciales

Usa este documento para responder preguntas de usuario sobre como trabajar con `Update Iniciales`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como ejecutar en Update Iniciales?

## Donde Entrar

- Pantalla pendiente de revisar.

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Ejecutar

1. Revisar manualmente los pasos de esta accion.

Referencias tecnicas para verificar la respuesta:
- Ninguna referencia API inferida.

## Pantallas Y Fragmentos Relacionados

- `misas.pantalla.ver_iniciales_zona`

## Objetivo

Inserta o actualiza iniciales y color de un sacerdote en la tabla InicialesSacd.

## Errores Documentados

- `<repositorio getErrorTxt()>`

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Usar la seccion "Errores Documentados" cuando el usuario reporte un mensaje conocido.
