---
id: "casas.casa_ec_gastos.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "casas"
nombre: "Flujo - Gestionar Casa Ec Gastos"
capacidad: "casas.casa_ec_gastos.gestionar"
pantallas_principales: []
fragmentos: ["casas.pantalla.casa_ec_gastos_lista"]
acciones: ["guardar", "ver_formulario"]
endpoints: ["/src/casas/casa_ec_gastos_form_data", "/src/casas/casa_ec_gastos_guardar"]
estado_revision: "revisado"
---

# Flujo - Gestionar Casa Ec Gastos

Propuesta generada automaticamente desde la capacidad `casas.casa_ec_gastos.gestionar` y sus pantallas relacionadas.

## Objetivo De Usuario

Gestiona CasaEcGastos. Data builder: formulario anual con gastos y aportaciones (sv/sf) por mes de una casa. Sucesor de la rama que=getGastos de apps/casas/controller/casa_ec_ajax.php. Use case: guardar los gastos y aportaciones (sv/sf) mensuales de una casa para un año completo. Borra los existentes y los reinserta con fecha 5 de cada mes. Sucesor de la rama que=guardarGasto de apps/casas/controller/casa_ec_ajax.php.

## Punto De Entrada

No se ha detectado pantalla principal. Revisar si el flujo solo aparece como fragmento o desde otra pantalla.

## Fragmentos O Pantallas Auxiliares

- `casas.pantalla.casa_ec_gastos_lista`

## Escenarios Inferidos

### Guardar

Pasos propuestos:
1. Revisar manualmente los pasos de esta accion.

Endpoints asociados:
- Ninguno inferido para esta accion.

### Ver Formulario

Pasos propuestos:
1. Desde el listado, elegir crear un nuevo registro o modificar uno existente.
2. Abrir el formulario asociado.
3. Comprobar que los campos cargados corresponden al registro o contexto seleccionado.

Endpoints asociados:
- `/src/casas/casa_ec_gastos_form_data`

## Campos Y Acciones Detectadas En Pantalla

Campos:
- `html.id_ubi`
- `html.year`
- `post.id_cdc`
- `post.year`

Acciones JavaScript:
- `fnjs_comprobar_dinero`
- `fnjs_gastos_guardar`
- `fnjs_ver`

## Endpoints Del Flujo

- `/src/casas/casa_ec_gastos_form_data`
- `/src/casas/casa_ec_gastos_guardar`

## Errores Conocidos

No se han documentado errores en la capacidad.
## Ruta de menú

- **Legacy:** exterior > casas > gastos casa
- **Pills2:** CASAS Y CTR > Gestión casas > gastos casas

