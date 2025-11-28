<?php
// Rutas del módulo Usuarios
// Convención: este archivo retorna un callable que recibe (RouteCollector $r)
// para registrar las rutas del módulo. Así evitamos depender del ámbito.

return static function ($r) {
    // Convención: cada archivo en src/usuarios/infrastructure/controllers/*.php
    // se mapea a la ruta /usuarios/<nombre_archivo_sin_php>
    // Permitimos GET y POST para máxima compatibilidad durante la migración.

    $r->addRoute(['GET','POST'], '/usuarios/borrar_pwd', function () {
        require __DIR__ . '/../infrastructure/controllers/borrar_pwd.php';
    });
    $r->addRoute(['GET','POST'], '/usuarios/check_first_login_2fa', function () {
        require __DIR__ . '/../infrastructure/controllers/check_first_login_2fa.php';
    });
    $r->addRoute(['GET','POST'], '/usuarios/grupo_eliminar', function () {
        require __DIR__ . '/../infrastructure/controllers/grupo_eliminar.php';
    });
    $r->addRoute(['GET','POST'], '/usuarios/grupo_guardar', function () {
        require __DIR__ . '/../infrastructure/controllers/grupo_guardar.php';
    });
    $r->addRoute(['GET','POST'], '/usuarios/grupo_info', function () {
        require __DIR__ . '/../infrastructure/controllers/grupo_info.php';
    });
    $r->addRoute(['GET','POST'], '/usuarios/grupo_lista', function () {
        require __DIR__ . '/../infrastructure/controllers/grupo_lista.php';
    });
    $r->addRoute(['GET','POST'], '/usuarios/mails_contactos_region', function () {
        require __DIR__ . '/../infrastructure/controllers/mails_contactos_region.php';
    });
    $r->addRoute(['GET','POST'], '/usuarios/perm_activ_eliminar', function () {
        require __DIR__ . '/../infrastructure/controllers/perm_activ_eliminar.php';
    });
    $r->addRoute(['GET','POST'], '/usuarios/perm_activ_guardar', function () {
        require __DIR__ . '/../infrastructure/controllers/perm_activ_guardar.php';
    });
    $r->addRoute(['GET','POST'], '/usuarios/perm_activ_lista', function () {
        require __DIR__ . '/../infrastructure/controllers/perm_activ_lista.php';
    });
    $r->addRoute(['GET','POST'], '/usuarios/perm_menu_eliminar', function () {
        require __DIR__ . '/../infrastructure/controllers/perm_menu_eliminar.php';
    });
    $r->addRoute(['GET','POST'], '/usuarios/perm_menu_guardar', function () {
        require __DIR__ . '/../infrastructure/controllers/perm_menu_guardar.php';
    });
    $r->addRoute(['GET','POST'], '/usuarios/perm_menu_info', function () {
        require __DIR__ . '/../infrastructure/controllers/perm_menu_info.php';
    });
    $r->addRoute(['GET','POST'], '/usuarios/perm_menu_lista', function () {
        require __DIR__ . '/../infrastructure/controllers/perm_menu_lista.php';
    });
    $r->addRoute(['GET','POST'], '/usuarios/preferencias_guardar', function () {
        require __DIR__ . '/../infrastructure/controllers/preferencias_guardar.php';
    });
    $r->addRoute(['GET','POST'], '/usuarios/recuperar_2fa_mail', function () {
        require __DIR__ . '/../infrastructure/controllers/recuperar_2fa_mail.php';
    });
    $r->addRoute(['GET','POST'], '/usuarios/recuperar_password_mail', function () {
        require __DIR__ . '/../infrastructure/controllers/recuperar_password_mail.php';
    });
    $r->addRoute(['GET','POST'], '/usuarios/role_eliminar', function () {
        require __DIR__ . '/../infrastructure/controllers/role_eliminar.php';
    });
    $r->addRoute(['GET','POST'], '/usuarios/role_grupmenu_add', function () {
        require __DIR__ . '/../infrastructure/controllers/role_grupmenu_add.php';
    });
    $r->addRoute(['GET','POST'], '/usuarios/role_grupmenu_del', function () {
        require __DIR__ . '/../infrastructure/controllers/role_grupmenu_del.php';
    });
    $r->addRoute(['GET','POST'], '/usuarios/role_grupmenu_info', function () {
        require __DIR__ . '/../infrastructure/controllers/role_grupmenu_info.php';
    });
    $r->addRoute(['GET','POST'], '/usuarios/role_guardar', function () {
        require __DIR__ . '/../infrastructure/controllers/role_guardar.php';
    });
    $r->addRoute(['GET','POST'], '/usuarios/role_info', function () {
        require __DIR__ . '/../infrastructure/controllers/role_info.php';
    });
    $r->addRoute(['GET','POST'], '/usuarios/role_lista', function () {
        require __DIR__ . '/../infrastructure/controllers/role_lista.php';
    });
    $r->addRoute(['GET','POST'], '/usuarios/usuario_2fa_info', function () {
        require __DIR__ . '/../infrastructure/controllers/usuario_2fa_info.php';
    });
    $r->addRoute(['GET','POST'], '/usuarios/usuario_2fa_update', function () {
        require __DIR__ . '/../infrastructure/controllers/usuario_2fa_update.php';
    });
    $r->addRoute(['GET','POST'], '/usuarios/usuario_2fa_verify', function () {
        require __DIR__ . '/../infrastructure/controllers/usuario_2fa_verify.php';
    });
    $r->addRoute(['GET','POST'], '/usuarios/usuario_avisos_form', function () {
        require __DIR__ . '/../infrastructure/controllers/usuario_avisos_form.php';
    });
    $r->addRoute(['GET','POST'], '/usuarios/usuario_ayuda_info', function () {
        require __DIR__ . '/../infrastructure/controllers/usuario_ayuda_info.php';
    });
    $r->addRoute(['GET','POST'], '/usuarios/usuario_check_pwd', function () {
        require __DIR__ . '/../infrastructure/controllers/usuario_check_pwd.php';
    });
    $r->addRoute(['GET','POST'], '/usuarios/usuario_eliminar', function () {
        require __DIR__ . '/../infrastructure/controllers/usuario_eliminar.php';
    });
    $r->addRoute(['GET','POST'], '/usuarios/usuario_form', function () {
        require __DIR__ . '/../infrastructure/controllers/usuario_form.php';
    });
    $r->addRoute(['GET','POST'], '/usuarios/usuario_grupo_add', function () {
        require __DIR__ . '/../infrastructure/controllers/usuario_grupo_add.php';
    });
    $r->addRoute(['GET','POST'], '/usuarios/usuario_grupo_del', function () {
        require __DIR__ . '/../infrastructure/controllers/usuario_grupo_del.php';
    });
    $r->addRoute(['GET','POST'], '/usuarios/usuario_grupo_del_lst', function () {
        require __DIR__ . '/../infrastructure/controllers/usuario_grupo_del_lst.php';
    });
    $r->addRoute(['GET','POST'], '/usuarios/usuario_grupo_lst', function () {
        require __DIR__ . '/../infrastructure/controllers/usuario_grupo_lst.php';
    });
    $r->addRoute(['GET','POST'], '/usuarios/usuario_guardar', function () {
        require __DIR__ . '/../infrastructure/controllers/usuario_guardar.php';
    });
    $r->addRoute(['GET','POST'], '/usuarios/usuario_guardar_mail', function () {
        require __DIR__ . '/../infrastructure/controllers/usuario_guardar_mail.php';
    });
    $r->addRoute(['GET','POST'], '/usuarios/usuario_guardar_pwd', function () {
        require __DIR__ . '/../infrastructure/controllers/usuario_guardar_pwd.php';
    });
    $r->addRoute(['GET','POST'], '/usuarios/usuario_info', function () {
        require __DIR__ . '/../infrastructure/controllers/usuario_info.php';
    });
    $r->addRoute(['GET','POST'], '/usuarios/usuario_lista', function () {
        require __DIR__ . '/../infrastructure/controllers/usuario_lista.php';
    });
    $r->addRoute(['GET','POST'], '/usuarios/usuario_preferencias', function () {
        require __DIR__ . '/../infrastructure/controllers/usuario_preferencias.php';
    });
    $r->addRoute(['GET','POST'], '/usuarios/zzusuario_grupo_from', function () {
        require __DIR__ . '/../infrastructure/controllers/zzusuario_grupo_from.php';
    });
    $r->addRoute(['GET','POST'], '/usuarios/zzusuario_perm_form', function () {
        require __DIR__ . '/../infrastructure/controllers/zzusuario_perm_form.php';
    });
};
