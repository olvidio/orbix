---
tipo: "ayuda_ia"
subtipo: "pantalla"
modulo: "dossiers"
titulo: "Perm Dossiers"
pantalla: "dossiers.pantalla.perm_dossiers"
preguntas: ["Que se puede hacer en Perm Dossiers?", "Que campos tiene Perm Dossiers?", "Que acciones hay en Perm Dossiers?"]
capacidades: ["dossiers.perm_dossiers.gestionar"]
endpoints: ["/src/dossiers/perm_dossiers_data"]
source: "docs/catalogo/dossiers/pantallas/perm_dossiers.md"
estado_revision: "generado"
---

# Ayuda IA Pantalla - Perm Dossiers

## Resumen

Página de selección de los dossiers cuyos permisos deseo visualizar o modificar.

## Uso En Ayuda

Usar esta ficha cuando el usuario pregunte por una pantalla concreta, sus campos o sus acciones.

## Campos Detectados

- `post.tipo`

## Acciones Detectadas

- `fnjs_update_div`

## Capacidades Relacionadas

- `dossiers.perm_dossiers.gestionar`

## Endpoints Relacionados

- `/src/dossiers/perm_dossiers_data`

## Precauciones

- Esta ficha puede contener nombres tecnicos. Para respuesta final, convertirlos a lenguaje de usuario cuando sea posible.
