---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "shared"
titulo: "Guardar o eliminar un registro de una tabla"
flujo: "shared.tablaDB.gestionar.flujo"
preguntas: ["Como guardar o eliminar un registro de una tabla en Guardar o eliminar un registro de una tabla?"]
pantallas_principales: ["shared.pantalla.tablaDB_formulario_ver"]
fragmentos: ["shared.pantalla.tablaDB_formulario_ver", "shared.pantalla.tablaDB_lista_ver"]
endpoints: ["/src/shared/tablaDB_update"]
source: "docs/catalogo/shared/flujos/tablaDB.md"
estado_revision: "generado"
---

# Ayuda IA - Guardar o eliminar un registro de una tabla

Usa este documento para responder preguntas de usuario sobre como trabajar con `Guardar o eliminar un registro de una tabla`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como guardar o eliminar un registro de una tabla en Guardar o eliminar un registro de una tabla?

## Donde Entrar

- Mantenimiento genérico de tablas (formulario) (`shared.pantalla.tablaDB_formulario_ver`)

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Guardar o eliminar un registro de una tabla

1. En el formulario, pulsar guardar: se graba el registro nuevo o los cambios del existente.
2. En la tabla, seleccionar una o varias filas y pulsar **eliminar** para borrarlas.
3. Algunas tablas tienen reglas propias; por ejemplo, al activar un módulo se crean sus tablas.

Referencias tecnicas para verificar la respuesta:
- `/src/shared/tablaDB_update`

## Pantallas Y Fragmentos Relacionados

- `shared.pantalla.tablaDB_formulario_ver`
- `shared.pantalla.tablaDB_formulario_ver`
- `shared.pantalla.tablaDB_lista_ver`

## Objetivo

Dar de alta, modificar o eliminar un registro de cualquier tabla mantenida con la pantalla genérica de mantenimiento.

## Errores Documentados

- `no se ha ejecutado la acción`
- `Errores de repositorio y validación de módulos (ver API tablaDB_update).`

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Usar la seccion "Errores Documentados" cuando el usuario reporte un mensaje conocido.
