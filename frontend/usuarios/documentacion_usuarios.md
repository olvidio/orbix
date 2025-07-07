# Documentación del Módulo de Usuarios

## Funciones de la Aplicación

### 1. Gestión de Usuarios
- **Listado de Usuarios**: Permite visualizar todos los usuarios del sistema con opciones para buscar por nombre de usuario.
- **Creación de Usuarios**: Permite crear nuevos usuarios con información como nombre de usuario, contraseña, email, y asignación de roles.
- **Edición de Usuarios**: Permite modificar la información de usuarios existentes, incluyendo cambios de contraseña.
- **Eliminación de Usuarios**: Permite eliminar usuarios del sistema.
- **Gestión de Seguridad**: Incluye opciones para forzar cambio de contraseña al iniciar sesión y activar autenticación de doble factor.

### 2. Gestión de Roles
- **Listado de Roles**: Visualización de todos los roles disponibles en el sistema.
- **Creación de Roles**: Permite crear nuevos roles con configuraciones específicas.
- **Edición de Roles**: Permite modificar roles existentes, incluyendo sus permisos y configuraciones.
- **Configuración de Entornos**: Permite configurar si un rol aplica a entornos SF (San Francisco) y/o SV (San Vicente).
- **Configuración de PAU**: Permite asignar un tipo de usuario/nivel de acceso (PAU) a cada rol.
- **Configuración de DMZ**: Permite configurar si un rol tiene acceso a zonas DMZ (posiblemente relacionado con seguridad de red).

### 3. Gestión de Permisos
- **Asignación de Grupos de Menús**: Permite asignar grupos de menús específicos a cada rol.
- **Eliminación de Grupos de Menús**: Permite quitar grupos de menús asignados a un rol.
- **Permisos en Actividades**: Si la aplicación 'procesos' está instalada, permite gestionar permisos específicos para actividades.
- **Avisos de Cambios**: Si la aplicación 'cambios' está instalada, permite configurar condiciones para los avisos de cambios.

### 4. Gestión de Grupos de Usuarios
- **Visualización de Grupos**: Permite ver los grupos a los que pertenece un usuario.
- **Asignación a Grupos**: Permite añadir usuarios a grupos específicos.
- **Eliminación de Grupos**: Permite quitar usuarios de grupos específicos.

## Procesos para Probar

### 1. Gestión de Usuarios
- **Crear un nuevo usuario**: Verificar que se puede crear un usuario con todos los campos requeridos.
- **Buscar usuarios**: Comprobar que la función de búsqueda filtra correctamente por nombre de usuario.
- **Editar usuario**: Verificar que se pueden modificar todos los campos de un usuario existente.
- **Cambiar contraseña**: Comprobar que el sistema valida correctamente las contraseñas y aplica los cambios.
- **Eliminar usuario**: Verificar que un usuario puede ser eliminado correctamente.
- **Forzar cambio de contraseña**: Comprobar que la opción de forzar cambio de contraseña funciona al iniciar sesión.
- **Activar autenticación de doble factor**: Verificar que se puede activar/desactivar la autenticación de doble factor.

### 2. Gestión de Roles
- **Crear un nuevo rol**: Verificar que se puede crear un rol con todas las configuraciones necesarias.
- **Editar rol**: Comprobar que se pueden modificar las configuraciones de un rol existente.
- **Configurar entornos SF/SV**: Verificar que las opciones SF y SV funcionan correctamente.
- **Asignar PAU**: Comprobar que se puede asignar correctamente un tipo de usuario/nivel de acceso.
- **Configurar DMZ**: Verificar que la opción DMZ funciona correctamente.

### 3. Gestión de Permisos
- **Asignar grupo de menús a rol**: Verificar que se pueden añadir grupos de menús a un rol.
- **Eliminar grupo de menús de rol**: Comprobar que se pueden quitar grupos de menús de un rol.
- **Configurar permisos en actividades**: Si está disponible, verificar que se pueden configurar permisos específicos para actividades.
- **Configurar avisos de cambios**: Si está disponible, comprobar que se pueden configurar las condiciones para los avisos de cambios.

### 4. Gestión de Grupos de Usuarios
- **Ver grupos de usuario**: Verificar que se muestran correctamente los grupos a los que pertenece un usuario.
- **Añadir usuario a grupo**: Comprobar que se puede añadir un usuario a un grupo específico.
- **Quitar usuario de grupo**: Verificar que se puede eliminar un usuario de un grupo específico.

### 5. Pruebas de Integración
- **Iniciar sesión con nuevo usuario**: Verificar que un usuario recién creado puede iniciar sesión correctamente.
- **Verificar permisos de menú**: Comprobar que los grupos de menús asignados a un rol se reflejan correctamente en la interfaz.
- **Verificar permisos de actividades**: Si está disponible, comprobar que los permisos de actividades funcionan correctamente.
- **Verificar avisos de cambios**: Si está disponible, comprobar que los avisos de cambios se generan según las condiciones configuradas.

### 6. Pruebas de Seguridad
- **Intentar acceso sin permisos**: Verificar que un usuario no puede acceder a funcionalidades para las que no tiene permisos.
- **Validación de contraseñas**: Comprobar que el sistema valida correctamente las contraseñas según las políticas establecidas.
- **Funcionamiento de doble factor**: Verificar que la autenticación de doble factor funciona correctamente si está activada.