---
id: "cambios.avisos_generar.gestionar"
tipo: "capacidad"
modulo: "cambios"
nombre: "Gestionar Avisos Generar"
entidades: ["AvisosGenerar", "CambioUsuario", "Cambio"]
acciones: ["listar", "eliminar", "eliminar_hasta_fecha", "generar_tabla"]
endpoints: ["/src/cambios/avisos_generar_lista_data", "/src/cambios/cambio_usuario_eliminar", "/src/cambios/cambio_usuario_eliminar_hasta_fecha", "/src/cambios/avisos_generar_tabla"]
pantallas: ["frontend/cambios/controller/avisos_generar.php"]
casos_uso: ["src\\cambios\\application\\AvisosGenerarListaData", "src\\cambios\\application\\CambioUsuarioEliminar", "src\\cambios\\application\\CambioUsuarioEliminarHastaFecha", "src\\cambios\\application\\AvisosGenerarTabla"]
tags: ["avisos", "avisos_generar", "cambios", "data", "generar", "lista", "tabla"]
estado_revision: "revisado"
---

# Gestionar Avisos Generar

Consulta/purga de avisos pendientes (`CambioUsuario`) y generación batch de la tabla desde cambios
anotados (`Cambio` → preferencias → `CambioUsuario`).

## Objetivo Funcional

- Listar avisos no avisados del usuario (o de otro si admin).
- Eliminar filas o purgar hasta fecha.
- Regenerar la tabla de avisos (menú «generar tabla avisos» → endpoint batch).

## Acciones Detectadas

- `listar`
- `eliminar`
- `eliminar_hasta_fecha`
- `generar_tabla`

## Endpoints

- `/src/cambios/avisos_generar_lista_data`
- `/src/cambios/cambio_usuario_eliminar`
- `/src/cambios/cambio_usuario_eliminar_hasta_fecha`
- `/src/cambios/avisos_generar_tabla`

## Pantallas Relacionadas

- `frontend/cambios/controller/avisos_generar.php` (listado/purga)
- Generar tabla: sin pantalla FE (menú → URL src)

## Casos De Uso Detectados

- `src\cambios\application\AvisosGenerarListaData`
- `src\cambios\application\CambioUsuarioEliminar`
- `src\cambios\application\CambioUsuarioEliminarHastaFecha`
- `src\cambios\application\AvisosGenerarTabla`

## Errores Conocidos

- `debe indicar la fecha`
- `Hay un error, no se ha eliminado`
- `Hay un error al eliminar los cambios hasta la fecha indicada`
- `Algo falla` (generar tabla / bucle infinito)
