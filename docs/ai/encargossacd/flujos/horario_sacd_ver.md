---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "encargossacd"
titulo: "Crear o modificar el horario de un sacerdote"
flujo: "encargossacd.horario_sacd_ver.gestionar.flujo"
preguntas: ["Como crear o modificar el horario de un sacerdote en Crear o modificar el horario de un sacerdote?"]
pantallas_principales: ["encargossacd.pantalla.horario_sacd_ver"]
fragmentos: ["encargossacd.pantalla.horario_sacd_ver"]
endpoints: ["/src/encargossacd/horario_sacd_ver_data"]
source: "docs/catalogo/encargossacd/flujos/horario_sacd_ver.md"
estado_revision: "generado"
---

# Ayuda IA - Crear o modificar el horario de un sacerdote

Usa este documento para responder preguntas de usuario sobre como trabajar con `Crear o modificar el horario de un sacerdote`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como crear o modificar el horario de un sacerdote en Crear o modificar el horario de un sacerdote?

## Donde Entrar

- Horario Sacd Ver (`encargossacd.pantalla.horario_sacd_ver`)

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Crear o modificar el horario de un sacerdote

1. En la ficha, pulsar **crear horario**.
2. Indicar **dia**, **hora inicio**, **hora fin** y el periodo (**activo desde** / **hasta**).
3. Se pueden **añadir horario** y **generar excepciones** (antes del / después del).
4. Pulsar **guardar horario**; **eliminar horario** pide confirmación.

Referencias tecnicas para verificar la respuesta:
- `/src/encargossacd/horario_sacd_ver_data`

## Pantallas Y Fragmentos Relacionados

- `encargossacd.pantalla.horario_sacd_ver`
- `encargossacd.pantalla.horario_sacd_ver`

## Objetivo

Ver el formulario de horario de un sacerdote en un encargo, con excepciones antes o después de una fecha.

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Si el usuario pregunta por errores concretos, responder que estan pendientes salvo que el catalogo los documente.
