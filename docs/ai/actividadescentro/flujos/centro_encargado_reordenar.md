---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "actividadescentro"
titulo: "Centro Encargado Reordenar"
flujo: "actividadescentro.centro_encargado_reordenar.gestionar.flujo"
preguntas: ["Como ejecutar en Centro Encargado Reordenar?"]
pantallas_principales: []
fragmentos: ["actividadescentro.pantalla.activ_ctr"]
endpoints: ["/src/actividadescentro/centro_encargado_reordenar"]
source: "docs/catalogo/actividadescentro/flujos/centro_encargado_reordenar.md"
estado_revision: "generado"
---

# Ayuda IA - Centro Encargado Reordenar

Usa este documento para responder preguntas de usuario sobre como trabajar con `Centro Encargado Reordenar`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como ejecutar en Centro Encargado Reordenar?

## Donde Entrar

- Pantalla pendiente de revisar.

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Ejecutar

1. Pulsar un centro encargado ya asignado para abrir el popup de orden.
2. Elegir **+ prioridad** o **- prioridad**.
3. El sistema reordena y refresca la celda de centros de la actividad.

Referencias tecnicas para verificar la respuesta:
- `/src/actividadescentro/centro_encargado_reordenar`

## Pantallas Y Fragmentos Relacionados

- `actividadescentro.pantalla.activ_ctr`

## Objetivo

El usuario sube (**+ prioridad**) o baja (**- prioridad**) un centro encargado en el listado de una actividad. Internamente se intercambia el `num_orden` con el centro vecino.

## Errores Documentados

- `direccion de orden incorrecta (mas / menos)`
- `faltan parametros id_activ / id_ubi`

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Usar la seccion "Errores Documentados" cuando el usuario reporte un mensaje conocido.
