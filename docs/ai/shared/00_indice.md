---
tipo: "ayuda_ia"
subtipo: "indice"
modulo: "shared"
flujos: 6
pantallas: 2
endpoints: 7
estado_revision: "generado"
---

# Ayuda IA - shared

Indice para una IA local. Estos documentos estan pensados para busqueda semantica y respuestas de ayuda funcional.

## Como Debe Responder La IA

- Priorizar pasos de usuario sobre detalles tecnicos.
- Si falta ruta de menu, decir que esta pendiente de documentar.
- No inventar permisos, errores o validaciones que no aparezcan en la documentacion.
- Usar referencias tecnicas solo para verificar, no como respuesta principal al usuario final.

## Flujos Disponibles

- Elegir un idioma en un desplegable -> `flujos/locales_posibles.md`
- Guardar o eliminar un registro de una tabla -> `flujos/tablaDB.md`
- Buscar antes de listar -> `flujos/tablaDB_buscar.md`
- Cambiar un campo que condiciona otro -> `flujos/tablaDB_depende.md`
- Crear o modificar un registro -> `flujos/tablaDB_formulario.md`
- Buscar y listar los registros -> `flujos/tablaDB_lista.md`

## Pantallas Disponibles

- Mantenimiento genérico de tablas (formulario) -> `pantallas/tablaDB_formulario_ver.md`
- Mantenimiento genérico de tablas (listado) -> `pantallas/tablaDB_lista_ver.md`
