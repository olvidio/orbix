---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "actividadescentro"
titulo: "Centro Encargado Asignar"
flujo: "actividadescentro.centro_encargado_asignar.gestionar.flujo"
preguntas: ["Como ejecutar en Centro Encargado Asignar?"]
pantallas_principales: []
fragmentos: ["actividadescentro.pantalla.activ_ctr"]
endpoints: ["/src/actividadescentro/centro_encargado_asignar"]
source: "docs/catalogo/actividadescentro/flujos/centro_encargado_asignar.md"
estado_revision: "generado"
---

# Ayuda IA - Centro Encargado Asignar

Usa este documento para responder preguntas de usuario sobre como trabajar con `Centro Encargado Asignar`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como ejecutar en Centro Encargado Asignar?

## Donde Entrar

- Pantalla pendiente de revisar.

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Ejecutar

1. En una actividad, pulsar **nuevo** para ver los centros candidatos.
2. Pulsar el centro deseado.
3. El sistema lo guarda como encargado y refresca la celda de centros de la actividad.

Referencias tecnicas para verificar la respuesta:
- `/src/actividadescentro/centro_encargado_asignar`

## Pantallas Y Fragmentos Relacionados

- `actividadescentro.pantalla.activ_ctr`

## Objetivo

El usuario asigna un centro (elegido en el desplegable de candidatos) como encargado de una actividad. El centro queda al final del listado (`num_orden = max + 1`) con `encargo = 'organizador'`.

## Errores Documentados

- `faltan parametros id_activ / id_ubi`
- `hay un error, no se ha guardado el centro encargado`

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Usar la seccion "Errores Documentados" cuando el usuario reporte un mensaje conocido.
