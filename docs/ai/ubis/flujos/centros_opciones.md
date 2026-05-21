---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "ubis"
titulo: "Centros Opciones"
flujo: "ubis.centros_opciones.gestionar.flujo"
preguntas: ["Como obtener datos en Centros Opciones?"]
pantallas_principales: []
fragmentos: []
endpoints: ["/src/ubis/centros_opciones_data"]
source: "docs/catalogo/ubis/flujos/centros_opciones.md"
estado_revision: "generado"
---

# Ayuda IA - Centros Opciones

Usa este documento para responder preguntas de usuario sobre como trabajar con `Centros Opciones`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como obtener datos en Centros Opciones?

## Donde Entrar

- Pantalla pendiente de revisar.

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Obtener datos

1. Revisar manualmente los pasos de esta accion.

Referencias tecnicas para verificar la respuesta:
- Ninguna referencia API inferida.

## Pantallas Y Fragmentos Relacionados

- Ninguna pantalla relacionada.

## Objetivo

Gestiona CentrosOpciones. Devuelve el payload (solo datos) para poblar el <select> de centros en frontend\shared\web\CentrosQue. Sustituye el acceso directo desde CentrosQue al repositorio CentroDlRepositoryInterface (separación frontend ↔ backend, ver refactor.md).

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Si el usuario pregunta por errores concretos, responder que estan pendientes salvo que el catalogo los documente.
