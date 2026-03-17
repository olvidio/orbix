<?php
// Rutas del módulo Usuarios
// Convención: este archivo retorna un callable que recibe (RouteCollector $r)
// para registrar las rutas del módulo. Así evitamos depender del ámbito.

return static function ($r) {
    // Convención: cada archivo en src/usuarios/infrastructure/ui/http/controllers/*.php
    // se mapea a la ruta /usuarios/<nombre_archivo_sin_php>
    // Permitimos GET y POST para máxima compatibilidad durante la migración.

    $r->addRoute(['GET','POST'], '/src/usuarios/borrar_pwd', function () {
        require __DIR__ . '/../infrastructure/ui/http/controllers/borrar_pwd.php';
    });
    $r->addRoute(['GET','POST'], '/src/usuarios/check_first_login_2fa', function () {
        require __DIR__ . '/../infrastructure/ui/http/controllers/check_first_login_2fa.php';
    });
    $r->addRoute(['GET','POST'], '/src/usuarios/grupo_eliminar', function () {
        require __DIR__ . '/../infrastructure/ui/http/controllers/grupo_eliminar.php';
    });
    $r->addRoute(['GET','POST'], '/src/usuarios/grupo_guardar', function () {
        require __DIR__ . '/../infrastructure/ui/http/controllers/grupo_guardar.php';
    });
    $r->addRoute(['GET','POST'], '/src/usuarios/grupo_info', function () {
        require __DIR__ . '/../infrastructure/ui/http/controllers/grupo_info.php';
    });
    $r->addRoute(['GET','POST'], '/src/usuarios/grupo_lista', function () {
        require __DIR__ . '/../infrastructure/ui/http/controllers/grupo_lista.php';
    });
    $r->addRoute(['GET','POST'], '/src/usuarios/mails_contactos_region', function () {
        require __DIR__ . '/../infrastructure/ui/http/controllers/mails_contactos_region.php';
    });
    $r->addRoute(['GET','POST'], '/src/usuarios/perm_activ_eliminar', function () {
        require __DIR__ . '/../infrastructure/ui/http/controllers/perm_activ_eliminar.php';
    });
    $r->addRoute(['GET','POST'], '/src/usuarios/perm_activ_guardar', function () {
        require __DIR__ . '/../infrastructure/ui/http/controllers/perm_activ_guardar.php';
    });
    $r->addRoute(['GET','POST'], '/src/usuarios/perm_activ_lista', function () {
        require __DIR__ . '/../infrastructure/ui/http/controllers/perm_activ_lista.php';
    });
    $r->addRoute(['GET','POST'], '/src/usuarios/perm_menu_eliminar', function () {
        require __DIR__ . '/../infrastructure/ui/http/controllers/perm_menu_eliminar.php';
    });
    $r->addRoute(['GET','POST'], '/src/usuarios/perm_menu_guardar', function () {
        require __DIR__ . '/../infrastructure/ui/http/controllers/perm_menu_guardar.php';
    });
    $r->addRoute(['GET','POST'], '/src/usuarios/perm_menu_info', function () {
        require __DIR__ . '/../infrastructure/ui/http/controllers/perm_menu_info.php';
    });
    $r->addRoute(['GET','POST'], '/src/usuarios/perm_menu_lista', function () {
        require __DIR__ . '/../infrastructure/ui/http/controllers/perm_menu_lista.php';
    });
    $r->addRoute(['GET','POST'], '/src/usuarios/preferencias_guardar', function () {
        require __DIR__ . '/../infrastructure/ui/http/controllers/preferencias_guardar.php';
    });
    $r->addRoute(['GET','POST'], '/src/usuarios/recuperar_2fa_mail', function () {
        require __DIR__ . '/../infrastructure/ui/http/controllers/recuperar_2fa_mail.php';
    });
    $r->addRoute(['GET','POST'], '/src/usuarios/recuperar_password_mail', function () {
        require __DIR__ . '/../infrastructure/ui/http/controllers/recuperar_password_mail.php';
    });
    $r->addRoute(['GET','POST'], '/src/usuarios/role_eliminar', function () {
        require __DIR__ . '/../infrastructure/ui/http/controllers/role_eliminar.php';
    });
    $r->addRoute(['GET','POST'], '/src/usuarios/role_grupmenu_add', function () {
        require __DIR__ . '/../infrastructure/ui/http/controllers/role_grupmenu_add.php';
    });
    $r->addRoute(['GET','POST'], '/src/usuarios/role_grupmenu_del', function () {
        require __DIR__ . '/../infrastructure/ui/http/controllers/role_grupmenu_del.php';
    });
    $r->addRoute(['GET','POST'], '/src/usuarios/role_grupmenu_info', function () {
        require __DIR__ . '/../infrastructure/ui/http/controllers/role_grupmenu_info.php';
    });
    $r->addRoute(['GET','POST'], '/src/usuarios/role_guardar', function () {
        require __DIR__ . '/../infrastructure/ui/http/controllers/role_guardar.php';
    });
    $r->addRoute(['GET','POST'], '/src/usuarios/role_info', function () {
        require __DIR__ . '/../infrastructure/ui/http/controllers/role_info.php';
    });
    $r->addRoute(['GET','POST'], '/src/usuarios/role_lista', function () {
        require __DIR__ . '/../infrastructure/ui/http/controllers/role_lista.php';
    });
    $r->addRoute(['GET','POST'], '/src/usuarios/usuario_2fa_info', function () {
        require __DIR__ . '/../infrastructure/ui/http/controllers/usuario_2fa_info.php';
    });
    $r->addRoute(['GET','POST'], '/src/usuarios/usuario_2fa_update', function () {
        require __DIR__ . '/../infrastructure/ui/http/controllers/usuario_2fa_update.php';
    });
    $r->addRoute(['GET','POST'], '/src/usuarios/usuario_2fa_verify', function () {
        require __DIR__ . '/../infrastructure/ui/http/controllers/usuario_2fa_verify.php';
    });
    $r->addRoute(['GET','POST'], '/src/usuarios/usuario_avisos_form', function () {
        require __DIR__ . '/../infrastructure/ui/http/controllers/usuario_avisos_form.php';
    });
    $r->addRoute(['GET','POST'], '/src/usuarios/usuario_ayuda_info', function () {
        require __DIR__ . '/../infrastructure/ui/http/controllers/usuario_ayuda_info.php';
    });
    $r->addRoute(['GET','POST'], '/src/usuarios/usuario_check_pwd', function () {
        require __DIR__ . '/../infrastructure/ui/http/controllers/usuario_check_pwd.php';
    });
    $r->addRoute(['GET','POST'], '/src/usuarios/usuario_eliminar', function () {
        require __DIR__ . '/../infrastructure/ui/http/controllers/usuario_eliminar.php';
    });
    $r->addRoute(['GET','POST'], '/src/usuarios/usuario_form', function () {
        require __DIR__ . '/../infrastructure/ui/http/controllers/usuario_form.php';
    });
    $r->addRoute(['GET','POST'], '/src/usuarios/usuario_grupo_add', function () {
        require __DIR__ . '/../infrastructure/ui/http/controllers/usuario_grupo_add.php';
    });
    $r->addRoute(['GET','POST'], '/src/usuarios/usuario_grupo_del', function () {
        require __DIR__ . '/../infrastructure/ui/http/controllers/usuario_grupo_del.php';
    });
    $r->addRoute(['GET','POST'], '/src/usuarios/usuario_grupo_del_lst', function () {
        require __DIR__ . '/../infrastructure/ui/http/controllers/usuario_grupo_del_lst.php';
    });
    $r->addRoute(['GET','POST'], '/src/usuarios/usuario_grupo_lst', function () {
        require __DIR__ . '/../infrastructure/ui/http/controllers/usuario_grupo_lst.php';
    });
    $r->addRoute(['GET','POST'], '/src/usuarios/usuario_guardar', function () {
        require __DIR__ . '/../infrastructure/ui/http/controllers/usuario_guardar.php';
    });
    $r->addRoute(['GET','POST'], '/src/usuarios/usuario_guardar_mail', function () {
        require __DIR__ . '/../infrastructure/ui/http/controllers/usuario_guardar_mail.php';
    });
    $r->addRoute(['GET','POST'], '/src/usuarios/usuario_guardar_pwd', function () {
        require __DIR__ . '/../infrastructure/ui/http/controllers/usuario_guardar_pwd.php';
    });
    $r->addRoute(['GET','POST'], '/src/usuarios/usuario_info', function () {
        require __DIR__ . '/../infrastructure/ui/http/controllers/usuario_info.php';
    });
    $r->addRoute(['GET','POST'], '/src/usuarios/usuario_lista', function () {
        require __DIR__ . '/../infrastructure/ui/http/controllers/usuario_lista.php';
    });
    $r->addRoute(['GET','POST'], '/src/usuarios/usuario_preferencias', function () {
        require __DIR__ . '/../infrastructure/ui/http/controllers/usuario_preferencias.php';
    });
};
