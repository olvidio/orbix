# Guía de Desarrollo DDD para Orbix

## Objetivo
Mantener una estructura consistente basada en DDD para todo nuevo código en `src/`, reduciendo acoplamientos con legacy y evitando mezclar capas.

## Estructura mínima por módulo
Todo módulo nuevo debe seguir esta base:

```text
src/<modulo>/
  application/
  domain/
    contracts/
    entity/
    value_objects/
  infrastructure/
    controllers/
    repositories/
  config/
    dependencies.php
    routes.php
```

Reglas:
- No crear nuevas carpetas `db/` o `view/` para funcionalidad nueva.
- No dejar archivos vacíos como placeholder.
- Si un módulo no tiene casos de uso, igualmente debe mantener `config/` y `domain/`.

## Reglas de capas

### Domain
- Debe ser independiente de framework/UI/infra.
- Prohibido en código nuevo:
  - `use core\...` (salvo utilidades puramente de dominio justificadas).
  - `use web\...`, `use frontend\...`.
  - `$GLOBALS`, `$_SESSION`, `$_POST`, `$_GET`.
  - Construcción de HTML, rutas URL o renderizado de vistas.
- `contracts/` define puertos de dominio, no detalles técnicos:
  - No exponer `PDO`, `setoDbl`, `getoDbl`, `getNomTabla`, `getErrorTxt`.

### Application
- Orquesta casos de uso y coordina repositorios/servicios de dominio.
- Inyección de dependencias por constructor.
- No usar `$GLOBALS['container']` dentro de casos de uso nuevos.
- No devolver objetos de presentación (`ContestarJson`, `Desplegable`, `Lista`, etc.).
- No ejecutar SQL directo ni manipular conexiones.

### Infrastructure
- Implementa repositorios concretos y adaptadores de entrada/salida.
- Controladores:
  - Leen request.
  - Invocan un caso de uso.
  - Transforman respuesta a JSON/HTML.
  - Sin lógica de negocio compleja.
- Cualquier uso de `core\...`, `web\...`, PDO o globals debe quedar aquí.

### Config
- `dependencies.php`: mapear interfaces de dominio a implementaciones de `infrastructure`.
- `routes.php`: declarar rutas hacia controladores existentes. No dejar rutas huérfanas.

## Convenciones de código nuevo
- Clases en `PascalCase` (archivo = clase).
- Métodos y propiedades en `camelCase`.
- Interfaces con sufijo `Interface`.
- Casos de uso con nombres explícitos: `CrearXxxUseCase`, `ListarXxxUseCase`, etc.
- Evitar métodos estáticos en aplicación salvo utilidades puras.
- Mantener tipado estricto en firmas y retornos.

## Compatibilidad con legado
- No propagar patrones legacy al código nuevo.
- Si un caso de uso necesita integrarse con legacy:
  - Crear un adaptador en `infrastructure`.
  - Encapsular allí globals, formatos antiguos y APIs heredadas.
  - Mantener `domain` y `application` limpios.

## Checklist obligatorio en cada PR
- [ ] ¿El código nuevo respeta separación Domain/Application/Infrastructure?
- [ ] ¿No se añadieron dependencias de UI/DB en `domain`?
- [ ] ¿No se usó `$GLOBALS` en `domain` o `application` nuevos?
- [ ] ¿Interfaces de dominio sin detalles de PDO/tabla?
- [ ] ¿Rutas apuntan a controladores reales?
- [ ] ¿No hay archivos vacíos?
- [ ] ¿Nombres de clase/método siguen convención?
- [ ] ¿Se añadieron pruebas (unitarias o integración) para comportamiento nuevo?

## Criterio de excepción
Si una regla no puede cumplirse por dependencia legacy:
- Documentar la excepción en el PR.
- Limitarla a `infrastructure`.
- Añadir tarea técnica para eliminarla.
