---
id: "inventario.documentos.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "inventario"
nombre: "Flujo - Modificar varios documentos a la vez"
capacidad: "inventario.documentos.gestionar"
pantallas_principales: []
fragmentos: []
acciones: ["guardar"]
endpoints: ["/src/inventario/documentos_guardar"]
estado_revision: "revisado"
---

# Flujo - Gestionar Documentos

## Objetivo De Usuario

Cambiar de golpe fechas, numeración o estado de varios documentos a la vez.

## Punto De Entrada

Menú **Inventario > documentos > por sigla**: elegir el **tipo de documento**, pulsar **Buscar** y, en el listado resultante, usar la modificación de varios documentos a la vez.

## Fragmentos O Pantallas Auxiliares

No se han detectado fragmentos AJAX relacionados.

## Escenarios Inferidos

### Modificar varios documentos a la vez

Pasos propuestos:
1. Abrir **Inventario > documentos > por sigla**.
2. Elegir el **tipo de documento** y pulsar **Buscar**.
3. En el listado, marcar los documentos que se quieren cambiar y pulsar **Modificar por bloques**. Si falta el tipo o no hay documentos marcados avisa «Debe seleccionar un tipo de documento» o «Debe seleccionar algún documento».
4. En «campos a modificar en los documentos seleccionados», **marcar la casilla «modificar» de cada campo que se quiere cambiar** y escribir su valor: **fecha recibido**, **fecha asignado**, **eliminado** (sí/no), **fecha eliminado**, **número inicial**, **número final**. Los campos sin marcar no se tocan; un campo marcado y vacío borra el valor.
5. Pulsar **guardar** (o **cancel** para salir sin cambios). Si no se marca ningún campo avisa «Debe marcar al menos un campo a modificar». Al guardar bien aparece «guardado» y el listado se actualiza.

Endpoints asociados:
- `/src/inventario/documentos_guardar`

## Campos Y Acciones Detectadas En Pantalla

Campos:
- Ninguno detectado.

Acciones JavaScript:
- Ninguna detectada.

## Endpoints Del Flujo

- `/src/inventario/documentos_guardar`

## Errores Conocidos

No se han documentado errores en la capacidad.

## Ruta de menú

- **Legacy:** scdl > Inventario > documentos > asignar documento
- **Pills2:** —
