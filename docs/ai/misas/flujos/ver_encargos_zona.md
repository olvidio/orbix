---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "misas"
titulo: "Ver Encargos Zona"
flujo: "misas.ver_encargos_zona.gestionar.flujo"
preguntas: ["Como obtener datos en Ver Encargos Zona?"]
pantallas_principales: []
fragmentos: ["misas.pantalla.ver_encargos_zona"]
endpoints: ["/src/misas/ver_encargos_zona_data"]
source: "docs/catalogo/misas/flujos/ver_encargos_zona.md"
estado_revision: "generado"
---

# Ayuda IA - Ver Encargos Zona

Usa este documento para responder preguntas de usuario sobre como trabajar con `Ver Encargos Zona`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como obtener datos en Ver Encargos Zona?

## Donde Entrar

- Pantalla pendiente de revisar.

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Obtener datos

1. Revisar manualmente los pasos de esta accion.

Referencias tecnicas para verificar la respuesta:
- Ninguna referencia API inferida.

## Pantallas Y Fragmentos Relacionados

- `misas.pantalla.ver_encargos_zona`

## Objetivo

Gestiona VerEncargosZona. Devuelve los datos necesarios para pintar el SlickGrid de encargos de una zona + los desplegables del modal de edicion. Replica la consulta de apps/misas/controller/ver_encargos_zona.php: encargos con id_tipo_enc >= 8100 (grupo 8...) de la zona indicada, ordenados por $orden (orden, prioridad o desc_enc).

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Si el usuario pregunta por errores concretos, responder que estan pendientes salvo que el catalogo los documente.
