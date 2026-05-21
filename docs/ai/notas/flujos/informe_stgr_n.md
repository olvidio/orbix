---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "notas"
titulo: "Informe Stgr N"
flujo: "notas.informe_stgr_n.gestionar.flujo"
preguntas: ["Como obtener datos en Informe Stgr N?"]
pantallas_principales: []
fragmentos: ["notas.pantalla.informe_stgr_n"]
endpoints: ["/src/notas/informe_stgr_n_data"]
source: "docs/catalogo/notas/flujos/informe_stgr_n.md"
estado_revision: "generado"
---

# Ayuda IA - Informe Stgr N

Usa este documento para responder preguntas de usuario sobre como trabajar con `Informe Stgr N`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como obtener datos en Informe Stgr N?

## Donde Entrar

- Pantalla pendiente de revisar.

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Obtener datos

1. Revisar manualmente los pasos de esta accion.

Referencias tecnicas para verificar la respuesta:
- Ninguna referencia API inferida.

## Pantallas Y Fragmentos Relacionados

- `notas.pantalla.informe_stgr_n`

## Objetivo

Gestiona InformeStgrNumerarios. Calcula el informe anual STGR de "numerarios" (puntos 1..18 + x). Encapsula el uso de src\notas\application\legacy\Resumen (legacy) para que los controllers del frontend no importen la clase legacy directamente. Devuelve un array neutro {res, textos, curso_txt} listo para renderizado.

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Si el usuario pregunta por errores concretos, responder que estan pendientes salvo que el catalogo los documente.
