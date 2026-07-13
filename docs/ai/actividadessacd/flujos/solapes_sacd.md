---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "actividadessacd"
titulo: "Solapes Sacd"
flujo: "actividadessacd.solapes_sacd.gestionar.flujo"
preguntas: ["Como obtener datos en Solapes Sacd?"]
pantallas_principales: ["actividadessacd.pantalla.activ_sacd"]
fragmentos: []
endpoints: ["/src/actividadessacd/solapes_sacd_data"]
source: "docs/catalogo/actividadessacd/flujos/solapes_sacd.md"
estado_revision: "generado"
---

# Ayuda IA - Solapes Sacd

Usa este documento para responder preguntas de usuario sobre como trabajar con `Solapes Sacd`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como obtener datos en Solapes Sacd?

## Donde Entrar

- Activ Sacd (`actividadessacd.pantalla.activ_sacd`)

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Obtener datos

1. Entrar desde el menú con tipo `solape`.
2. Elegir periodo y pulsar **buscar**.
3. El sistema construye la tabla de sacd con sus actividades incompatibles y la leyenda de colores.

Referencias tecnicas para verificar la respuesta:
- `/src/actividadessacd/solapes_sacd_data`

## Pantallas Y Fragmentos Relacionados

- `actividadessacd.pantalla.activ_sacd`

## Objetivo

Con el tipo de menú `solape`, el usuario elige un periodo y pulsa **buscar**: el sistema muestra los sacd que tienen actividades incompatibles (solapes horarios) y, para cada uno, las actividades afectadas.

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Si el usuario pregunta por errores concretos, responder que estan pendientes salvo que el catalogo los documente.
