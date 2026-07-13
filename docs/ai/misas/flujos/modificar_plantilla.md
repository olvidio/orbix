---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "misas"
titulo: "Modificar Plantilla"
flujo: "misas.modificar_plantilla.gestionar.flujo"
preguntas: ["Como obtener datos en Modificar Plantilla?"]
pantallas_principales: []
fragmentos: ["misas.pantalla.modificar_plantilla"]
endpoints: ["/src/misas/modificar_plantilla_data"]
source: "docs/catalogo/misas/flujos/modificar_plantilla.md"
estado_revision: "generado"
---

# Ayuda IA - Modificar Plantilla

Usa este documento para responder preguntas de usuario sobre como trabajar con `Modificar Plantilla`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como obtener datos en Modificar Plantilla?

## Donde Entrar

- Pantalla pendiente de revisar.

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Obtener datos

1. Revisar manualmente los pasos de esta accion.

Referencias tecnicas para verificar la respuesta:
- Ninguna referencia API inferida.

## Pantallas Y Fragmentos Relacionados

- `misas.pantalla.modificar_plantilla`

## Objetivo

Carga desplegables de zona, orden y tipos de plantilla (con preferencia ultima_plantilla) para modificar plantilla.

## Errores Documentados

- `Usuario no encontrado`
- `No tiene permiso para ver esta página`

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Usar la seccion "Errores Documentados" cuando el usuario reporte un mensaje conocido.
