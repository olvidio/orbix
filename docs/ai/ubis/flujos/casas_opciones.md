---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "ubis"
titulo: "Casas Opciones"
flujo: "ubis.casas_opciones.gestionar.flujo"
preguntas: ["Como obtener datos en Casas Opciones?"]
pantallas_principales: []
fragmentos: []
endpoints: ["/src/ubis/casas_opciones_data"]
source: "docs/catalogo/ubis/flujos/casas_opciones.md"
estado_revision: "generado"
---

# Ayuda IA - Casas Opciones

Usa este documento para responder preguntas de usuario sobre como trabajar con `Casas Opciones`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como obtener datos en Casas Opciones?

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

Gestiona CasasOpciones. Devuelve el payload (solo datos) para poblar el <select> de casas en frontend\shared\web\CasasQue. La vista/componente frontend es quien construye el HTML del desplegable; aquí solo se exponen las opciones. Sustituye el acceso directo desde CasasQue al repositorio CasaDlRepositoryInterface (separación frontend ↔ backend, ver refactor.md).

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Si el usuario pregunta por errores concretos, responder que estan pendientes salvo que el catalogo los documente.
