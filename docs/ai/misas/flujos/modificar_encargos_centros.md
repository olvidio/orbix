---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "misas"
titulo: "Modificar Encargos Centros"
flujo: "misas.modificar_encargos_centros.gestionar.flujo"
preguntas: ["Como obtener datos en Modificar Encargos Centros?"]
pantallas_principales: []
fragmentos: ["misas.pantalla.modificar_encargos_centros"]
endpoints: ["/src/misas/modificar_encargos_centros_data"]
source: "docs/catalogo/misas/flujos/modificar_encargos_centros.md"
estado_revision: "generado"
---

# Ayuda IA - Modificar Encargos Centros

Usa este documento para responder preguntas de usuario sobre como trabajar con `Modificar Encargos Centros`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como obtener datos en Modificar Encargos Centros?

## Donde Entrar

- Pantalla pendiente de revisar.

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Obtener datos

1. Revisar manualmente los pasos de esta accion.

Referencias tecnicas para verificar la respuesta:
- Ninguna referencia API inferida.

## Pantallas Y Fragmentos Relacionados

- `misas.pantalla.modificar_encargos_centros`

## Objetivo

Devuelve el desplegable de zonas permitidas para la pantalla modificar encargos de centros.

## Errores Documentados

- `Usuario no encontrado`
- `No tiene permiso para ver esta página`

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Usar la seccion "Errores Documentados" cuando el usuario reporte un mensaje conocido.
