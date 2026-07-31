---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "encargossacd"
titulo: "Propuestas Menu"
flujo: "encargossacd.propuestas_menu.gestionar.flujo"
preguntas: ["Como crear tabla staging en Propuestas Menu?", "Como aprobar propuestas en Propuestas Menu?", "Como navegar a modificar / listados en Propuestas Menu?"]
pantallas_principales: ["encargossacd.pantalla.propuestas_menu"]
fragmentos: ["encargossacd.pantalla.propuestas_aprobar", "encargossacd.pantalla.propuestas_ajax"]
endpoints: ["/src/encargossacd/propuestas_ajax", "/src/encargossacd/propuestas_aprobar"]
source: "docs/catalogo/encargossacd/flujos/propuestas_menu.md"
estado_revision: "generado"
---

# Ayuda IA - Propuestas Menu

Usa este documento para responder preguntas de usuario sobre como trabajar con `Propuestas Menu`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como crear tabla staging en Propuestas Menu?
- Como aprobar propuestas en Propuestas Menu?
- Como navegar a modificar / listados en Propuestas Menu?

## Donde Entrar

- Propuestas Menu (`encargossacd.pantalla.propuestas_menu`)

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Crear tabla staging

1. Confirm JS («Elimina la tabla…»).
2. POST a FE `propuestas_ajax.php?que=crear_tabla` → API `propuestas_ajax`.
3. Alert si `success !== true`.

Referencias tecnicas para verificar la respuesta:
- Ninguna referencia API inferida.

## Aprobar propuestas

1. Confirm JS (aviso de ~30 s e irreversibilidad).
2. Carga FE `propuestas_aprobar.php` → API `propuestas_aprobar`.
3. Muestra texto «Hecho!» en `#main`.

Referencias tecnicas para verificar la respuesta:
- Ninguna referencia API inferida.

## Navegar a modificar / listados

1. `fnjs_update_div` hacia `propuestas_lista`, `propuestas_lista_sacd` o `propuestas_lista_enc`.

Referencias tecnicas para verificar la respuesta:
- Ninguna referencia API inferida.

## Pantallas Y Fragmentos Relacionados

- `encargossacd.pantalla.propuestas_menu`
- `encargossacd.pantalla.propuestas_aprobar`
- `encargossacd.pantalla.propuestas_ajax`

## Objetivo

Desde el hub: regenerar tabla staging, aprobar cambios a producción, o abrir la edición / listados de propuestas.

## Errores Documentados

- `No se puede crear la tabla (crear_tabla)`
- `Fallos de aprobar: sin mensaje _() explícito en el caso de uso (pendiente)`

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Usar la seccion "Errores Documentados" cuando el usuario reporte un mensaje conocido.
