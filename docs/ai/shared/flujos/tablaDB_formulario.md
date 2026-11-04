---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "shared"
titulo: "Crear o modificar un registro"
flujo: "shared.tablaDB_formulario.gestionar.flujo"
preguntas: ["Como crear o modificar un registro en Crear o modificar un registro?"]
pantallas_principales: ["shared.pantalla.tablaDB_formulario_ver"]
fragmentos: ["shared.pantalla.tablaDB_formulario_ver"]
endpoints: ["/src/shared/tablaDB_formulario_datos", "/src/shared/tablaDB_update"]
source: "docs/catalogo/shared/flujos/tablaDB_formulario.md"
estado_revision: "generado"
---

# Ayuda IA - Crear o modificar un registro

Usa este documento para responder preguntas de usuario sobre como trabajar con `Crear o modificar un registro`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como crear o modificar un registro en Crear o modificar un registro?

## Donde Entrar

- Mantenimiento genérico de tablas (formulario) (`shared.pantalla.tablaDB_formulario_ver`)

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Crear o modificar un registro

1. En la tabla, pulsar **nuevo**, o seleccionar una fila y pulsar **modificar**.
2. Aparece el formulario con los campos del registro; en una modificación salen con sus valores.
3. Rellenar los campos; las fechas se comprueban antes de enviar.
4. Pulsar guardar: se vuelve a la tabla.

Referencias tecnicas para verificar la respuesta:
- `/src/shared/tablaDB_formulario_datos`
- `/src/shared/tablaDB_update`

## Pantallas Y Fragmentos Relacionados

- `shared.pantalla.tablaDB_formulario_ver`
- `shared.pantalla.tablaDB_formulario_ver`

## Objetivo

Crear o modificar un registro en un mantenimiento genérico de tablas.

## Errores Documentados

- `Mensajes de tablaDB_update en alert vía json.mensaje.`

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Usar la seccion "Errores Documentados" cuando el usuario reporte un mensaje conocido.
