---
id: "actividades.actividad_que_filtros.gestionar"
tipo: "capacidad"
modulo: "actividades"
nombre: "Gestionar Actividad Que Filtros"
entidades: ["ActividadQueFiltrosBloque"]
acciones: ["ejecutar"]
endpoints: ["/src/actividades/actividad_que_filtros"]
pantallas: ["frontend/actividades/controller/actividad_que.php"]
casos_uso: ["src\\actividades\\application\\ActividadQueFiltrosBloque"]
tags: ["actividad", "actividad_que_filtros", "actividades", "filtros", "que"]
estado_revision: "generado"
---

# Gestionar Actividad Que Filtros

Propuesta generada automaticamente a partir de endpoints con prefijo comun `actividad_que_filtros`.

## Objetivo Funcional

Gestiona ActividadQueFiltrosBloque. Genera el HTML del bloque "filtros extra" (filtro_lugar + lugar + organiza + publicada) en la pantalla actividad_que. El bloque solo se muestra a usuarios con permiso de control (perm_ctr); para el resto devuelve cadena vacia. Encapsula todos los accesos a repositorios y entidades de dominio necesarios (Role, DelegacionDropdown, ActividadLugar) de forma que el frontend controller no tenga que depender directamente de src/.

## Acciones Detectadas

- `ejecutar`

## Endpoints

- `/src/actividades/actividad_que_filtros`

## Pantallas Relacionadas

- `frontend/actividades/controller/actividad_que.php`

## Casos De Uso Detectados

- `src\actividades\application\ActividadQueFiltrosBloque`

## Pistas Desde Endpoints

- Genera el HTML del bloque "filtros extra" (filtro_lugar + lugar + organiza + publicada) en la pantalla `actividad_que`. El bloque solo se muestra a usuarios con permiso de control (`perm_ctr`); para el resto devuelve cadena vacia. Encapsula todos los accesos a repositorios y entidades de dominio necesarios (`Role`, `DelegacionDropdown`, `ActividadLugar`) de forma que el frontend controller no tenga que depender directamente de `src/`.

## Errores Conocidos

No se han agregado errores desde el catalogo API.

## Revision Manual

- Confirmar si todos los endpoints pertenecen a la misma capacidad.
- Separar esta capacidad si mezcla procesos distintos.
- Marcar capacidades parecidas o duplicadas en otros modulos.
- Completar permisos, efectos sobre datos y ejemplos de uso.
