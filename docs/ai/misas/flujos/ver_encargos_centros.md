---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "misas"
titulo: "Ver Encargos Centros"
flujo: "misas.ver_encargos_centros.gestionar.flujo"
preguntas: ["Como obtener datos en Ver Encargos Centros?"]
pantallas_principales: []
fragmentos: ["misas.pantalla.ver_encargos_centros"]
endpoints: ["/src/misas/ver_encargos_centros_data"]
source: "docs/catalogo/misas/flujos/ver_encargos_centros.md"
estado_revision: "generado"
---

# Ayuda IA - Ver Encargos Centros

Usa este documento para responder preguntas de usuario sobre como trabajar con `Ver Encargos Centros`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como obtener datos en Ver Encargos Centros?

## Donde Entrar

- Pantalla pendiente de revisar.

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Obtener datos

1. Revisar manualmente los pasos de esta accion.

Referencias tecnicas para verificar la respuesta:
- Ninguna referencia API inferida.

## Pantallas Y Fragmentos Relacionados

- `misas.pantalla.ver_encargos_centros`

## Objetivo

Gestiona VerEncargosCentros. Devuelve los datos del SlickGrid de EncargoCtr (encargos visibles para cada centro de una zona) + los desplegables estaticos del modal de edicion (zonas posibles para filtrar encargos, centros de la zona). El desplegable dinamico de encargos (que cambia al seleccionar zona en el modal) no se incluye aqui: el frontend lo pide por separado a DesplegableEncargosData cuando el usuario lo necesita.

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Si el usuario pregunta por errores concretos, responder que estan pendientes salvo que el catalogo los documente.
