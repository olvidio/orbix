---
tipo: "ayuda_ia"
subtipo: "pantalla"
modulo: "profesores"
titulo: "Ficha profesor STGR"
pantalla: "profesores.pantalla.ficha_profesor_stgr"
preguntas: ["Que se puede hacer en Ficha profesor STGR?", "Que campos tiene Ficha profesor STGR?", "Que acciones hay en Ficha profesor STGR?"]
capacidades: ["profesores.ficha_profesor_stgr.gestionar"]
endpoints: ["/src/profesores/ficha_profesor_stgr"]
source: "docs/catalogo/profesores/pantallas/ficha_profesor_stgr.md"
estado_revision: "generado"
---

# Ayuda IA Pantalla - Ficha profesor STGR

## Resumen

Dossier académico del profesor: cabecera (nombre, centro, departamento, flags n/agd/sacd), bloques de curriculum, nombramientos, ampliaciones, congresos, docencia, director, juramento y publicaciones. Enlaces **[modificar]** según `aPerm` hacia submantenimientos `tablaDB_lista_ver`. Vista imprimible con `[imprimir]`.

## Uso En Ayuda

Usar esta ficha cuando el usuario pregunte por una pantalla concreta, sus campos o sus acciones.

## Campos Detectados

- `post.depende`
- `post.id_nom`
- `post.id_pau`
- `post.id_tabla`
- `post.obj_pau`
- `post.permiso`
- `post.print`
- `post.sel`
- `post.stack`

## Acciones Detectadas

- `fnjs_update_div`

## Capacidades Relacionadas

- `profesores.ficha_profesor_stgr.gestionar`

## Endpoints Relacionados

- `/src/profesores/ficha_profesor_stgr`

## Precauciones

- Esta ficha puede contener nombres tecnicos. Para respuesta final, convertirlos a lenguaje de usuario cuando sea posible.
