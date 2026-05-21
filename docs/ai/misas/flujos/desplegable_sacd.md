---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "misas"
titulo: "Desplegable Sacd"
flujo: "misas.desplegable_sacd.gestionar.flujo"
preguntas: ["Como ejecutar en Desplegable Sacd?"]
pantallas_principales: []
fragmentos: []
endpoints: ["/src/misas/desplegable_sacd"]
source: "docs/catalogo/misas/flujos/desplegable_sacd.md"
estado_revision: "generado"
---

# Ayuda IA - Desplegable Sacd

Usa este documento para responder preguntas de usuario sobre como trabajar con `Desplegable Sacd`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como ejecutar en Desplegable Sacd?

## Donde Entrar

- Pantalla pendiente de revisar.

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Ejecutar

1. Revisar manualmente los pasos de esta accion.

Referencias tecnicas para verificar la respuesta:
- Ninguna referencia API inferida.

## Pantallas Y Fragmentos Relacionados

- Ninguna pantalla relacionada.

## Objetivo

Gestiona DesplegableSacd. Opciones del desplegable dinámico de SACD en el modal de la cuadrícula de zona. El payload sigue el espíritu del contrato de refactor.md (id, selected, filas ordenadas). rows conserva el orden del HTML legacy: opción actual, opción en blanco si aplica, resto ordenado por clave.

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Si el usuario pregunta por errores concretos, responder que estan pendientes salvo que el catalogo los documente.
