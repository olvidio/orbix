---
tipo: "ayuda_ia"
subtipo: "pantalla"
modulo: "dossiers"
titulo: "Dossiers Ver"
pantalla: "dossiers.pantalla.dossiers_ver"
preguntas: ["Que se puede hacer en Dossiers Ver?", "Que campos tiene Dossiers Ver?", "Que acciones hay en Dossiers Ver?"]
capacidades: ["dossiers.dossiers_ver_pantalla.gestionar"]
endpoints: ["/src/dossiers/dossiers_ver_pantalla_data"]
source: "docs/catalogo/dossiers/pantallas/dossiers_ver.md"
estado_revision: "generado"
---

# Ayuda IA Pantalla - Dossiers Ver

## Resumen

Visor de dossiers de una entidad (persona/actividad/ubi): cabecera con enlaces «dossiers» y «home», modo lista de carpetas o modo ficha con segmentos `select_*` y tablas `datos_tabla`. Gestiona navegación con `ListNavSupport` y firma `link_spec` en el frontend (`HashFront`).

## Uso En Ayuda

Usar esta ficha cuando el usuario pregunte por una pantalla concreta, sus campos o sus acciones.

## Campos Detectados

- No hay campos detectados.

## Acciones Detectadas

- `fnjs_update_div`

## Capacidades Relacionadas

- `dossiers.dossiers_ver_pantalla.gestionar`

## Endpoints Relacionados

- `/src/dossiers/dossiers_ver_pantalla_data`

## Precauciones

- Esta ficha puede contener nombres tecnicos. Para respuesta final, convertirlos a lenguaje de usuario cuando sea posible.
