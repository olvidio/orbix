---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "shared"
titulo: "Buscar y listar los registros"
flujo: "shared.tablaDB_lista.gestionar.flujo"
preguntas: ["Como buscar y listar los registros en Buscar y listar los registros?", "Como añadir, modificar o eliminar un registro en Buscar y listar los registros?"]
pantallas_principales: ["shared.pantalla.tablaDB_lista_ver"]
fragmentos: ["shared.pantalla.tablaDB_formulario_ver"]
endpoints: ["/src/shared/tablaDB_buscar_datos", "/src/shared/tablaDB_lista_datos", "/src/shared/tablaDB_update"]
source: "docs/catalogo/shared/flujos/tablaDB_lista.md"
estado_revision: "generado"
---

# Ayuda IA - Buscar y listar los registros

Usa este documento para responder preguntas de usuario sobre como trabajar con `Buscar y listar los registros`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como buscar y listar los registros en Buscar y listar los registros?
- Como añadir, modificar o eliminar un registro en Buscar y listar los registros?

## Donde Entrar

- Mantenimiento genérico de tablas (listado) (`shared.pantalla.tablaDB_lista_ver`)

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Buscar y listar los registros

1. Abrir la tabla desde el menú.
2. Si el mantenimiento pide criterios, rellenarlos y pulsar buscar.
3. Aparece la tabla; se puede ordenar por varias columnas.

Referencias tecnicas para verificar la respuesta:
- `/src/shared/tablaDB_buscar_datos`
- `/src/shared/tablaDB_lista_datos`
- `/src/shared/tablaDB_update`

## Añadir, modificar o eliminar un registro

1. Pulsar **nuevo** para añadir, o seleccionar una fila y pulsar **modificar** (ver «Crear o modificar un registro»).
2. Para eliminar, seleccionar la fila, pulsar **eliminar** y confirmar.

Referencias tecnicas para verificar la respuesta:
- `/src/shared/tablaDB_buscar_datos`
- `/src/shared/tablaDB_lista_datos`
- `/src/shared/tablaDB_update`

## Pantallas Y Fragmentos Relacionados

- `shared.pantalla.tablaDB_lista_ver`
- `shared.pantalla.tablaDB_formulario_ver`

## Objetivo

Consultar y mantener los registros de tablas de configuración que se abren desde el menú (asignaturas, ubicaciones, inventario, procesos…) con la misma pantalla genérica.

## Errores Documentados

- `Errores de tablaDB_update (ver ficha API).`
- `Sin errores propios en builders de lista.`

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Usar la seccion "Errores Documentados" cuando el usuario reporte un mensaje conocido.
