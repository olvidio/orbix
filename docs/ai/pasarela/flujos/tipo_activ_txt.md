---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "pasarela"
titulo: "Tipo Activ Txt"
flujo: "pasarela.tipo_activ_txt.gestionar.flujo"
preguntas: ["Como obtener datos en Tipo Activ Txt?"]
pantallas_principales: []
fragmentos: ["pasarela.pantalla.activacion_ajax", "pasarela.pantalla.contribucion_no_duerme_ajax", "pasarela.pantalla.contribucion_reserva_ajax", "pasarela.pantalla.nombre_ajax"]
endpoints: ["/src/pasarela/tipo_activ_txt_data"]
source: "docs/catalogo/pasarela/flujos/tipo_activ_txt.md"
estado_revision: "generado"
---

# Ayuda IA - Tipo Activ Txt

Usa este documento para responder preguntas de usuario sobre como trabajar con `Tipo Activ Txt`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como obtener datos en Tipo Activ Txt?

## Donde Entrar

- Pantalla pendiente de revisar.

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Obtener datos

1. Revisar manualmente los pasos de esta accion.

Referencias tecnicas para verificar la respuesta:
- Ninguna referencia API inferida.

## Pantallas Y Fragmentos Relacionados

- `pasarela.pantalla.activacion_ajax`
- `pasarela.pantalla.contribucion_no_duerme_ajax`
- `pasarela.pantalla.contribucion_reserva_ajax`
- `pasarela.pantalla.nombre_ajax`

## Objetivo

Gestiona TipoActivTxt. Devuelve el texto descriptivo (sfsv asistentes actividad) para un id_tipo_activ. Lo consumen los formularios form_modificar desde el frontend para mostrar a qué tipo de actividad corresponde la fila editada.

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Si el usuario pregunta por errores concretos, responder que estan pendientes salvo que el catalogo los documente.
