---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "actividades"
titulo: "Actividad Tipo"
flujo: "actividades.actividad_tipo.gestionar.flujo"
preguntas: ["Como obtener en Actividad Tipo?"]
pantallas_principales: ["actividades.pantalla.actividad_select_ubi"]
fragmentos: ["actividades.pantalla.actividad_que"]
endpoints: ["/src/actividades/actividad_tipo_get"]
source: "docs/catalogo/actividades/flujos/actividad_tipo.md"
estado_revision: "generado"
---

# Ayuda IA - Actividad Tipo

Usa este documento para responder preguntas de usuario sobre como trabajar con `Actividad Tipo`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como obtener en Actividad Tipo?

## Donde Entrar

- Actividad Select Ubi (`actividades.pantalla.actividad_select_ubi`)

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Obtener

1. Revisar manualmente los pasos de esta accion.

Referencias tecnicas para verificar la respuesta:
- Ninguna referencia API inferida.

## Pantallas Y Fragmentos Relacionados

- `actividades.pantalla.actividad_select_ubi`
- `actividades.pantalla.actividad_que`

## Objetivo

Gestiona ActividadTipoGetActividad, ActividadTipoGetAsistentes, ActividadTipoGetDlOrg, ActividadTipoGetFiltroLugar, ActividadTipoGetIdTarifa, ActividadTipoGetLugar, ActividadTipoGetNivelStgrDefecto, ActividadTipoGetNomTipo, ActividadTipoGetNomTipoTabla. Endpoint backend que devuelve el payload necesario (datos de desplegable, tabla HTML o valor escalar) segun el parametro POST salida.

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Si el usuario pregunta por errores concretos, responder que estan pendientes salvo que el catalogo los documente.
