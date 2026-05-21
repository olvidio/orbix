---
id: "casas.casa_ec_gastos.gestionar"
tipo: "capacidad"
modulo: "casas"
nombre: "Gestionar Casa Ec Gastos"
entidades: ["CasaEcGastos"]
acciones: ["guardar", "ver_formulario"]
endpoints: ["/src/casas/casa_ec_gastos_form_data", "/src/casas/casa_ec_gastos_guardar"]
pantallas: ["frontend/casas/controller/casa_ec_gastos_lista.php"]
casos_uso: ["src\\casas\\application\\CasaEcGastosFormData", "src\\casas\\application\\CasaEcGastosGuardar"]
tags: ["casa", "casa_ec_gastos", "casas", "data", "ec", "form", "gastos", "guardar"]
estado_revision: "generado"
---

# Gestionar Casa Ec Gastos

Propuesta generada automaticamente a partir de endpoints con prefijo comun `casa_ec_gastos`.

## Objetivo Funcional

Gestiona CasaEcGastos. Data builder: formulario anual con gastos y aportaciones (sv/sf) por mes de una casa. Sucesor de la rama que=getGastos de apps/casas/controller/casa_ec_ajax.php. Use case: guardar los gastos y aportaciones (sv/sf) mensuales de una casa para un año completo. Borra los existentes y los reinserta con fecha 5 de cada mes. Sucesor de la rama que=guardarGasto de apps/casas/controller/casa_ec_ajax.php.

## Acciones Detectadas

- `guardar`
- `ver_formulario`

## Endpoints

- `/src/casas/casa_ec_gastos_form_data`
- `/src/casas/casa_ec_gastos_guardar`

## Pantallas Relacionadas

- `frontend/casas/controller/casa_ec_gastos_lista.php`

## Casos De Uso Detectados

- `src\casas\application\CasaEcGastosFormData`
- `src\casas\application\CasaEcGastosGuardar`

## Pistas Desde Endpoints

- Data builder: formulario anual con gastos y aportaciones (sv/sf) por mes de una casa. Sucesor de la rama `que=getGastos` de `apps/casas/controller/casa_ec_ajax.php`.
- Use case: guardar los gastos y aportaciones (sv/sf) mensuales de una casa para un año completo. Borra los existentes y los reinserta con fecha 5 de cada mes. Sucesor de la rama `que=guardarGasto` de `apps/casas/controller/casa_ec_ajax.php`.

## Errores Conocidos

No se han agregado errores desde el catalogo API.

## Revision Manual

- Confirmar si todos los endpoints pertenecen a la misma capacidad.
- Separar esta capacidad si mezcla procesos distintos.
- Marcar capacidades parecidas o duplicadas en otros modulos.
- Completar permisos, efectos sobre datos y ejemplos de uso.
