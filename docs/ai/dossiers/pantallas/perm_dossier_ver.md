---
tipo: "ayuda_ia"
subtipo: "pantalla"
modulo: "dossiers"
titulo: "Perm Dossier Ver"
pantalla: "dossiers.pantalla.perm_dossier_ver"
preguntas: ["Que se puede hacer en Perm Dossier Ver?", "Que campos tiene Perm Dossier Ver?", "Que acciones hay en Perm Dossier Ver?"]
capacidades: ["dossiers.perm_dossier_ver.gestionar"]
endpoints: ["/src/dossiers/perm_dossier_ver_data"]
source: "docs/catalogo/dossiers/pantallas/perm_dossier_ver.md"
estado_revision: "generado"
---

# Ayuda IA Pantalla - Perm Dossier Ver

## Resumen

Página de visualización de los permisos de los dossiers.

## Uso En Ayuda

Usar esta ficha cuando el usuario pregunte por una pantalla concreta, sus campos o sus acciones.

## Campos Detectados

- `html.app`
- `html.campo_to`
- `html.class`
- `html.codigo`
- `html.depende_modificar`
- `html.descripcion`
- `html.id_tipo_dossier`
- `html.id_tipo_dossier_rel`
- `html.que`
- `html.tabla_from`
- `html.tabla_to`
- `post.id_tipo_dossier`
- `post.tipo`

## Acciones Detectadas

- `fnjs_eliminar`
- `fnjs_guardar`
- `fnjs_update_div`

## Capacidades Relacionadas

- `dossiers.perm_dossier_ver.gestionar`

## Endpoints Relacionados

- `/src/dossiers/perm_dossier_ver_data`

## Precauciones

- Esta ficha puede contener nombres tecnicos. Para respuesta final, convertirlos a lenguaje de usuario cuando sea posible.
