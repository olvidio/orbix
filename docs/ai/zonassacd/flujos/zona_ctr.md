---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "zonassacd"
titulo: "Zona Ctr"
flujo: "zonassacd.zona_ctr.gestionar.flujo"
preguntas: ["Como crear o modificar en Zona Ctr?", "Como ejecutar en Zona Ctr?", "Como consultar el listado en Zona Ctr?"]
pantallas_principales: []
fragmentos: ["zonassacd.pantalla.zona_ctr", "zonassacd.pantalla.zona_ctr_lista_ajax", "zonassacd.pantalla.zona_ctr_update_ajax"]
endpoints: ["/src/zonassacd/zona_ctr", "/src/zonassacd/zona_ctr_lista", "/src/zonassacd/zona_ctr_update"]
source: "docs/catalogo/zonassacd/flujos/zona_ctr.md"
estado_revision: "generado"
---

# Ayuda IA - Zona Ctr

Usa este documento para responder preguntas de usuario sobre como trabajar con `Zona Ctr`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como crear o modificar en Zona Ctr?
- Como ejecutar en Zona Ctr?
- Como consultar el listado en Zona Ctr?

## Donde Entrar

- Pantalla pendiente de revisar.

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Crear o modificar

1. Abrir el formulario de alta o modificacion.
2. Rellenar o corregir los campos requeridos.
3. Guardar los cambios.
4. Comprobar que la pantalla vuelve al listado y refleja el cambio.

Referencias tecnicas para verificar la respuesta:
- `/src/zonassacd/zona_ctr_update`

## Ejecutar

1. Revisar manualmente los pasos de esta accion.

Referencias tecnicas para verificar la respuesta:
- Ninguna referencia API inferida.

## Consultar el listado

1. Abrir la pantalla principal del flujo.
2. Rellenar los filtros visibles si los hay.
3. Ejecutar la accion de busqueda/listado.
4. Revisar el listado mostrado en pantalla.

Referencias tecnicas para verificar la respuesta:
- `/src/zonassacd/zona_ctr_lista`

## Pantallas Y Fragmentos Relacionados

- `zonassacd.pantalla.zona_ctr`
- `zonassacd.pantalla.zona_ctr_lista_ajax`
- `zonassacd.pantalla.zona_ctr_update_ajax`

## Objetivo

Gestiona ZonaCtr, ZonaCtrLista, ZonaCtrPage. Descripcion funcional pendiente de revisar.

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Si el usuario pregunta por errores concretos, responder que estan pendientes salvo que el catalogo los documente.
