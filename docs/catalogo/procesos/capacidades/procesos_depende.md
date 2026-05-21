---
id: "procesos.procesos_depende.gestionar"
tipo: "capacidad"
modulo: "procesos"
nombre: "Gestionar Procesos Depende"
entidades: ["ProcesosDepende"]
acciones: ["ejecutar"]
endpoints: ["/src/procesos/procesos_depende"]
pantallas: ["frontend/procesos/controller/procesos_ver.php"]
casos_uso: ["src\\procesos\\application\\ProcesosDepende"]
tags: ["depende", "procesos", "procesos_depende"]
estado_revision: "generado"
---

# Gestionar Procesos Depende

Propuesta generada automaticamente a partir de endpoints con prefijo comun `procesos_depende`.

## Objetivo Funcional

Gestiona ProcesosDepende. Caso de uso: devuelve las opciones disponibles para el desplegable de tareas dependientes de la fase indicada (usado al cambiar de fase o fase_previa en el formulario procesos_ver). Respuesta JSON con opciones (value => label). El frontend inyecta los <option> en el <select> destino indicado por acc.

## Acciones Detectadas

- `ejecutar`

## Endpoints

- `/src/procesos/procesos_depende`

## Pantallas Relacionadas

- `frontend/procesos/controller/procesos_ver.php`

## Casos De Uso Detectados

- `src\procesos\application\ProcesosDepende`

## Pistas Desde Endpoints

- Caso de uso: devuelve las opciones disponibles para el desplegable de tareas dependientes de la fase indicada (usado al cambiar de fase o fase_previa en el formulario procesos_ver). Respuesta JSON con `opciones` (value => label). El frontend inyecta los `<option>` en el `<select>` destino indicado por `acc`.

## Errores Conocidos

No se han agregado errores desde el catalogo API.

## Revision Manual

- Confirmar si todos los endpoints pertenecen a la misma capacidad.
- Separar esta capacidad si mezcla procesos distintos.
- Marcar capacidades parecidas o duplicadas en otros modulos.
- Completar permisos, efectos sobre datos y ejemplos de uso.
