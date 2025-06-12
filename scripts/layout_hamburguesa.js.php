<?php
/*
   importante para que el navegador entienda que lo que sigue es javascrip, ya que
	la extension del fichero no es ".js", sino ".js.php"
*/
use web\Hash;

//header('Content-Type: text/javascript; charset=UTF-8');
?>


function createMenuItem(item, level = 0) {
    const li = document.createElement('li');

    if (typeof item === 'string') {
        const a = document.createElement('a');
        a.href = '#';
        a.textContent = item;
        li.appendChild(a);
    } else {
        const a = document.createElement('a');
        a.href = '#';
        a.textContent = item.name;
        a.setAttribute("onclick", item.onClick);

        if (item.submenu && item.submenu.length > 0) {
            a.classList.add('has-submenu');
            const dropdown = document.createElement('div');
            dropdown.classList.add('dropdown');
            const ul = document.createElement('ul');

            item.submenu.forEach(subitem => {
                ul.appendChild(createMenuItem(subitem, level + 1));
            });

            dropdown.appendChild(ul);
            li.appendChild(dropdown);
        }
        li.appendChild(a);
    }

    return li;
}

function setActiveGroup(element, groupName) {
    // Remover clase active de todos los grupmenus
    document.querySelectorAll('.main-container a').forEach(a => a.classList.remove('active'));
    if (element) {
        element.classList.add('active');
        // actualizar el contenido de 'main'
        showPortada(groupName);
    }

    // Actualizar menú horizontal
    const horizontalMenu = document.getElementById('horizontalMenu');
    const menuItems = menuConfig[groupName] || [];

    horizontalMenu.innerHTML = '';
    //Array.prototype.forEach.call(
    if (Array.isArray(menuItems)) {
        menuItems.forEach((item, index) => {
            const li = createMenuItem(item);
            if (index === 0) li.classList.add('active');
            horizontalMenu.appendChild(li);
        });
    } else {
        [...menuItems].forEach((item, index) => {
            const li = createMenuItem(item);
            if (index === 0) li.classList.add('active');
            horizontalMenu.appendChild(li);
        });
        console.log('The value is not an array.');
    }

    // Asignar eventos de clic a los nuevos elementos del menú horizontal
    addHorizontalMenuEventListeners();

    const sidebarHeaderH2 = document.getElementById('sidebar-header-h2');
    sidebarHeaderH2.innerHTML=groupName;
    // Cerrar sidebar en móvil
    if (window.innerWidth <= 1024) {
        toggleSidebar();
    }
}


function addHorizontalMenuEventListeners() {
    // Agregar eventos de clic a todos los elementos del menú horizontal
    const horizontalMenuItems = document.querySelectorAll('.horizontal-menu > li > a');

    horizontalMenuItems.forEach(menuItem => {
        menuItem.addEventListener('click', function (e) {
            e.preventDefault();

            // Remover clase active de todos los elementos del menú horizontal
            document.querySelectorAll('.horizontal-menu > li').forEach(li => {
                li.classList.remove('active');
            });

            // Agregar clase active al elemento padre (li)
            this.parentElement.classList.add('active');
        });
    });

    // También agregar eventos a los elementos de los dropdowns
    const dropdownItems = document.querySelectorAll('.dropdown a');

    dropdownItems.forEach(dropdownItem => {
        dropdownItem.addEventListener('click', function (e) {
            e.preventDefault();

            // Opcional: marcar también el elemento principal cuando se selecciona un subelemento
            const mainMenuItem = this.closest('.horizontal-menu > li');
            if (mainMenuItem) {
                document.querySelectorAll('.horizontal-menu > li').forEach(li => {
                    li.classList.remove('active');
                });
                mainMenuItem.classList.add('active');
            }
        });
    });
}

function toggleSidebar() {
    /*
    const sidebar = document.getElementById('sidebar');
    const overlayMenu = document.querySelector('.overlayMenu');

    sidebar.classList.toggle('active');
    overlayMenu.classList.toggle('active');
     */

    const sidebar = document.getElementById('sidebar');
    const mainContent = document.getElementById('mainContent');
    const topMenu = document.getElementById('groupMenu');
    const mobileToggle = document.getElementById('mobileToggle');
    const overlayMenu = document.getElementById('overlayMenu');
    const contentSinMenus = document.getElementById('contenido_sin_menus');

    // Toggle classes
    sidebar.classList.toggle('hide');
    contentSinMenus.classList.toggle('sidebar-open');
    //sidebar.style.display = 'none';

    // En escritorio, empujar el contenido. En móvil, usar overlay
    if (window.innerWidth > 768) {
        mainContent.classList.toggle('sidebar-open');
        topMenu.classList.toggle('sidebar-open');
        mobileToggle.classList.toggle('sidebar-open');
    } else {
        overlayMenu.classList.toggle('active');
    }

}

function showUser() {
    const navUser = document.getElementById('navUser');

    navUser.classList.toggle('user-dropdown');
}

function showPortada(groupName){
    <?php
        $oHash1 = new Hash();
        $oHash1->setUrl("public/portada.php");
        $oHash1->setCamposForm('grupmenu');
        $h = $oHash1->linkSinVal();
    ?>
    url = "public/portada.php";
    datos = 'grupmenu='+groupName+'<?= $h ?>';

    let request = $.ajax({
        data: datos,
        url: url,
        method: 'POST',
        dataType: 'html'
    });

    request.done(function (rta) {
        const sidebar = document.getElementById('main');
        sidebar.innerHTML = rta;
    });
}

// Inicializar con el primer grupo
document.addEventListener('DOMContentLoaded', function () {
    setActiveGroup(document.querySelector('.main-container a.active'), defaultGrupMenu);
    // Agregar eventos a los elementos existentes del menú horizontal
    addHorizontalMenuEventListeners();
});
