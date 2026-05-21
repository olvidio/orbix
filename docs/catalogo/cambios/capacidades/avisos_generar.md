---
id: "cambios.avisos_generar.gestionar"
tipo: "capacidad"
modulo: "cambios"
nombre: "Gestionar Avisos Generar"
entidades: ["AvisosGenerar"]
acciones: ["listar"]
endpoints: ["/src/cambios/avisos_generar_lista_data"]
pantallas: ["frontend/cambios/controller/avisos_generar.php"]
casos_uso: ["src\\cambios\\application\\AvisosGenerarListaData"]
tags: ["avisos", "avisos_generar", "cambios", "data", "generar", "lista"]
estado_revision: "generado"
---

# Gestionar Avisos Generar

Propuesta generada automaticamente a partir de endpoints con prefijo comun `avisos_generar`.

## Objetivo Funcional

Gestiona AvisosGenerar. Listado de avisos CambioUsuario (con avisado=false) para el usuario/aviso_tipo dado + opciones de desplegables de la pantalla avisos_generar.

## Acciones Detectadas

- `listar`

## Endpoints

- `/src/cambios/avisos_generar_lista_data`

## Pantallas Relacionadas

- `frontend/cambios/controller/avisos_generar.php`

## Casos De Uso Detectados

- `src\cambios\application\AvisosGenerarListaData`

## Pistas Desde Endpoints

- Endpoint backend: listado de avisos `CambioUsuario` (con `avisado=false`) para el usuario/aviso_tipo dado + opciones de desplegables de la pantalla `avisos_generar`.

## Errores Conocidos

No se han agregado errores desde el catalogo API.

## Revision Manual

- Confirmar si todos los endpoints pertenecen a la misma capacidad.
- Separar esta capacidad si mezcla procesos distintos.
- Marcar capacidades parecidas o duplicadas en otros modulos.
- Completar permisos, efectos sobre datos y ejemplos de uso.
