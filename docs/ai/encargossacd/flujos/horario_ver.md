---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "encargossacd"
titulo: "Ver o crear un horario de encargo"
flujo: "encargossacd.horario_ver.gestionar.flujo"
preguntas: ["Como ver o crear un horario de encargo en Ver o crear un horario de encargo?"]
pantallas_principales: ["encargossacd.pantalla.horario_ver"]
fragmentos: ["encargossacd.pantalla.horario_ver"]
endpoints: ["/src/encargossacd/horario_ver_data"]
source: "docs/catalogo/encargossacd/flujos/horario_ver.md"
estado_revision: "generado"
---

# Ayuda IA - Ver o crear un horario de encargo

Usa este documento para responder preguntas de usuario sobre como trabajar con `Ver o crear un horario de encargo`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como ver o crear un horario de encargo en Ver o crear un horario de encargo?

## Donde Entrar

- Horario Ver (`encargossacd.pantalla.horario_ver`)

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Ver o crear un horario de encargo

1. En la lista de horarios, pulsar **añadir horario** o un horario existente.
2. Indicar **dia**, **hora inicio**, **hora fin**, **nº de sacd** y el periodo (**activo desde** / **hasta**).

Referencias tecnicas para verificar la respuesta:
- `/src/encargossacd/horario_ver_data`

## Pantallas Y Fragmentos Relacionados

- `encargossacd.pantalla.horario_ver`
- `encargossacd.pantalla.horario_ver`

## Objetivo

Ver el formulario de horario de un encargo (días, horas, nº de sacerdotes, periodo de vigencia).

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Si el usuario pregunta por errores concretos, responder que estan pendientes salvo que el catalogo los documente.
