---
tipo: "ayuda_ia"
subtipo: "pantalla"
modulo: "dossiers"
titulo: "Lista Dossiers"
pantalla: "dossiers.pantalla.lista_dossiers"
preguntas: ["Que se puede hacer en Lista Dossiers?", "Que campos tiene Lista Dossiers?", "Que acciones hay en Lista Dossiers?"]
capacidades: ["dossiers.dossiers_lista_fichas.gestionar"]
endpoints: ["/src/dossiers/dossiers_lista_fichas_data"]
source: "docs/catalogo/dossiers/pantallas/lista_dossiers.md"
estado_revision: "generado"
---

# Ayuda IA Pantalla - Lista Dossiers

## Resumen

Include desde 'home_persona.phtml' y 'home_ubis.phtml' (variables $pau, $id_pau, $Qobj_pau).

## Uso En Ayuda

Usar esta ficha cuando el usuario pregunte por una pantalla concreta, sus campos o sus acciones.

## Campos Detectados

- No hay campos detectados.

## Acciones Detectadas

- `fnjs_update_div`

## Capacidades Relacionadas

- `dossiers.dossiers_lista_fichas.gestionar`

## Endpoints Relacionados

- `/src/dossiers/dossiers_lista_fichas_data`

## Precauciones

- Esta ficha puede contener nombres tecnicos. Para respuesta final, convertirlos a lenguaje de usuario cuando sea posible.
