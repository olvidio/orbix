---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "encargossacd"
titulo: "Crear la tabla de propuestas"
flujo: "encargossacd.propuestas_menu.gestionar.flujo"
preguntas: ["Como crear la tabla de propuestas en Crear la tabla de propuestas?", "Como aprobar las propuestas en Crear la tabla de propuestas?", "Como abrir la edición o los listados en Crear la tabla de propuestas?"]
pantallas_principales: ["encargossacd.pantalla.propuestas_menu"]
fragmentos: ["encargossacd.pantalla.propuestas_aprobar", "encargossacd.pantalla.propuestas_ajax"]
endpoints: ["/src/encargossacd/propuestas_ajax", "/src/encargossacd/propuestas_aprobar"]
source: "docs/catalogo/encargossacd/flujos/propuestas_menu.md"
estado_revision: "generado"
---

# Ayuda IA - Crear la tabla de propuestas

Usa este documento para responder preguntas de usuario sobre como trabajar con `Crear la tabla de propuestas`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como crear la tabla de propuestas en Crear la tabla de propuestas?
- Como aprobar las propuestas en Crear la tabla de propuestas?
- Como abrir la edición o los listados en Crear la tabla de propuestas?

## Donde Entrar

- Propuestas Menu (`encargossacd.pantalla.propuestas_menu`)

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Crear la tabla de propuestas

1. Pulsar la opción para crear la tabla de propuestas.
2. Confirmar el aviso: elimina la tabla anterior y la crea de nuevo.
3. Si no se ha podido crear, sale un aviso.

Referencias tecnicas para verificar la respuesta:
- `/src/encargossacd/propuestas_ajax`
- `/src/encargossacd/propuestas_aprobar`

## Aprobar las propuestas

1. Pulsar **aprobar**.
2. Leer y confirmar el aviso: tarda unos 30 segundos y no se puede deshacer.
3. Al terminar aparece «Hecho!».

Referencias tecnicas para verificar la respuesta:
- `/src/encargossacd/propuestas_ajax`
- `/src/encargossacd/propuestas_aprobar`

## Abrir la edición o los listados

1. Elegir **modificar propuestas**, **listado propuestas por sacd** o **listado propuestas por encargos**; se abre en la misma ventana.

Referencias tecnicas para verificar la respuesta:
- `/src/encargossacd/propuestas_ajax`
- `/src/encargossacd/propuestas_aprobar`

## Pantallas Y Fragmentos Relacionados

- `encargossacd.pantalla.propuestas_menu`
- `encargossacd.pantalla.propuestas_aprobar`
- `encargossacd.pantalla.propuestas_ajax`

## Objetivo

Desde la pantalla de propuestas: volver a crear la tabla de trabajo, aprobar los cambios y pasarlos a los datos reales, o abrir la edición y los listados de propuestas. (Redactado desde el código; no contrastado con la pantalla.)

## Errores Documentados

- `No se puede crear la tabla (crear_tabla)`
- `Fallos de aprobar: sin mensaje _() explícito en el caso de uso (pendiente)`

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Usar la seccion "Errores Documentados" cuando el usuario reporte un mensaje conocido.
