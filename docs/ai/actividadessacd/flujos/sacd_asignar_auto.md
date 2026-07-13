---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "actividadessacd"
titulo: "Sacd Asignar Auto"
flujo: "actividadessacd.sacd_asignar_auto.gestionar.flujo"
preguntas: ["Como ejecutar en Sacd Asignar Auto?"]
pantallas_principales: ["actividadessacd.pantalla.asignar_sacd_auto"]
fragmentos: []
endpoints: ["/src/actividadessacd/sacd_asignar_auto"]
source: "docs/catalogo/actividadessacd/flujos/sacd_asignar_auto.md"
estado_revision: "generado"
---

# Ayuda IA - Sacd Asignar Auto

Usa este documento para responder preguntas de usuario sobre como trabajar con `Sacd Asignar Auto`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como ejecutar en Sacd Asignar Auto?

## Donde Entrar

- Asignar Sacd Auto (`actividadessacd.pantalla.asignar_sacd_auto`)

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Ejecutar

1. Leer el texto que describe el criterio de asignación automática.
2. Pulsar **continuar**.
3. El sistema procesa y muestra el resultado (`asignadas`, `sin_asignar`) sin recargar la página.

Referencias tecnicas para verificar la respuesta:
- `/src/actividadessacd/sacd_asignar_auto`

## Pantallas Y Fragmentos Relacionados

- `actividadessacd.pantalla.asignar_sacd_auto`

## Objetivo

El usuario confirma la asignación automática: el sistema asigna el sacd titular del centro encargado a las actividades sr/sg actuales posteriores al inicio de curso des que aún no tienen sacd. Devuelve cuántas se han asignado y cuántas quedan sin asignar; las asignadas quedan con observaciones `auto`.

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Si el usuario pregunta por errores concretos, responder que estan pendientes salvo que el catalogo los documente.
