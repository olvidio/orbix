---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "actividades"
titulo: "Tipo Activ Form"
flujo: "actividades.tipo_activ_form.gestionar.flujo"
preguntas: ["Como crear en Tipo Activ Form?"]
pantallas_principales: ["actividades.pantalla.tipo_activ"]
fragmentos: []
endpoints: ["/src/actividades/tipo_activ_form_nuevo"]
source: "docs/catalogo/actividades/flujos/tipo_activ_form.md"
estado_revision: "generado"
---

# Ayuda IA - Tipo Activ Form

Usa este documento para responder preguntas de usuario sobre como trabajar con `Tipo Activ Form`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como crear en Tipo Activ Form?

## Donde Entrar

- Tipo Activ (`actividades.pantalla.tipo_activ`)

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Crear

1. Revisar manualmente los pasos de esta accion.

Referencias tecnicas para verificar la respuesta:
- Ninguna referencia API inferida.

## Pantallas Y Fragmentos Relacionados

- `actividades.pantalla.tipo_activ`

## Objetivo

Gestiona TipoActivForm. Devuelve el HTML del formulario para crear un nuevo tipo de actividad. Portado del case form_nuevo del dispatcher legacy.

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Si el usuario pregunta por errores concretos, responder que estan pendientes salvo que el catalogo los documente.
