---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "ubis"
titulo: "Ubis Tabla"
flujo: "ubis.ubis_tabla.gestionar.flujo"
preguntas: ["Como obtener datos en Ubis Tabla?"]
pantallas_principales: []
fragmentos: ["ubis.pantalla.ubis_tabla"]
endpoints: ["/src/ubis/ubis_tabla_data"]
source: "docs/catalogo/ubis/flujos/ubis_tabla.md"
estado_revision: "generado"
---

# Ayuda IA - Ubis Tabla

Usa este documento para responder preguntas de usuario sobre como trabajar con `Ubis Tabla`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como obtener datos en Ubis Tabla?

## Donde Entrar

- Pantalla pendiente de revisar.

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Obtener datos

1. Revisar manualmente los pasos de esta accion.

Referencias tecnicas para verificar la respuesta:
- Ninguna referencia API inferida.

## Pantallas Y Fragmentos Relacionados

- `ubis.pantalla.ubis_tabla`

## Objetivo

Busca ubis por nombre y/o dirección con filtros tipo/loc y construye tabla navegable.

## Errores Documentados

- `debe poner algún criterio de búsqueda`

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Usar la seccion "Errores Documentados" cuando el usuario reporte un mensaje conocido.
