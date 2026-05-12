<?php
use frontend\shared\security\HashFront;
?>

function modernCreateMenuItem(item, level = 0) {
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
        a.setAttribute('onclick', item.onClick);

        if (item.submenu && item.submenu.length > 0) {
            a.classList.add('has-submenu');
            const dropdown = document.createElement('div');
            dropdown.classList.add('dropdown');
            const ul = document.createElement('ul');

            item.submenu.forEach(subitem => {
                ul.appendChild(modernCreateMenuItem(subitem, level + 1));
            });

            dropdown.appendChild(ul);
            li.appendChild(dropdown);
        }
        li.appendChild(a);
    }

    return li;
}

function modernSetActiveGroup(element, groupName, fromUserClick) {
    document.querySelectorAll('.modern-group-link').forEach(btn => btn.classList.remove('modern-pill--active'));
    if (element) {
        element.classList.add('modern-pill--active');
        if (fromUserClick) {
            showPortada(groupName);
        }
    }

    const horizontalMenu = document.getElementById('horizontalMenu');
    if (!horizontalMenu) {
        return;
    }
    const menuItems = menuConfig[groupName] || [];

    horizontalMenu.innerHTML = '';
    if (Array.isArray(menuItems)) {
        menuItems.forEach((item, index) => {
            const li = modernCreateMenuItem(item);
            if (index === 0) {
                li.classList.add('active');
            }
            horizontalMenu.appendChild(li);
        });
    } else {
        [...menuItems].forEach((item, index) => {
            const li = modernCreateMenuItem(item);
            if (index === 0) {
                li.classList.add('active');
            }
            horizontalMenu.appendChild(li);
        });
    }

    modernAddHorizontalMenuEventListeners();
}

function modernAddHorizontalMenuEventListeners() {
    const horizontalMenuItems = document.querySelectorAll('.modern-modulebar__menu.horizontal-menu > li > a');

    horizontalMenuItems.forEach(menuItem => {
        menuItem.addEventListener('click', function (e) {
            e.preventDefault();
            document.querySelectorAll('.modern-modulebar__menu.horizontal-menu > li').forEach(li => {
                li.classList.remove('active');
            });
            this.parentElement.classList.add('active');
        });
    });

    const dropdownItems = document.querySelectorAll('.modern-modulebar__menu .dropdown a');

    dropdownItems.forEach(dropdownItem => {
        dropdownItem.addEventListener('click', function (e) {
            e.preventDefault();
            const mainMenuItem = this.closest('.modern-modulebar__menu.horizontal-menu > li');
            if (mainMenuItem) {
                document.querySelectorAll('.modern-modulebar__menu.horizontal-menu > li').forEach(li => {
                    li.classList.remove('active');
                });
                mainMenuItem.classList.add('active');
            }
        });
    });
}

function modernToggleUserPanel(evt) {
    if (evt) {
        evt.stopPropagation();
    }
    const panel = document.getElementById('modernUserPanel');
    const trigger = document.getElementById('modernUserTrigger');
    if (!panel || !trigger) {
        return;
    }
    const open = panel.hasAttribute('hidden');
    if (open) {
        panel.removeAttribute('hidden');
        trigger.setAttribute('aria-expanded', 'true');
    } else {
        panel.setAttribute('hidden', 'hidden');
        trigger.setAttribute('aria-expanded', 'false');
    }
}

document.addEventListener('click', function (e) {
    const panel = document.getElementById('modernUserPanel');
    const trigger = document.getElementById('modernUserTrigger');
    if (!panel || panel.hasAttribute('hidden')) {
        return;
    }
    if (trigger && trigger.contains(e.target)) {
        return;
    }
    if (panel.contains(e.target)) {
        return;
    }
    panel.setAttribute('hidden', 'hidden');
    if (trigger) {
        trigger.setAttribute('aria-expanded', 'false');
    }
}, true);

function showPortada(groupName) {
    <?php
    $oHash1 = new HashFront();
    $oHash1->setUrl('public/portada.php');
    $oHash1->setCamposForm('grupmenu');
    $h = $oHash1->linkSinValParams();
    ?>
    const url = 'public/portada.php';
    const datos = 'grupmenu=' + groupName + '<?= $h ?>';

    const request = $.ajax({
        data: datos,
        url: url,
        method: 'POST',
        dataType: 'html'
    });

    request.done(function (rta) {
        const main = document.getElementById('main');
        if (main) {
            main.innerHTML = rta;
        }
    });
}

document.addEventListener('DOMContentLoaded', function () {
    const groupNav = document.getElementById('modernGroupMenu');
    if (groupNav) {
        groupNav.addEventListener('click', function (e) {
            const btn = e.target.closest('.modern-group-link');
            if (!btn) {
                return;
            }
            const grupo = btn.getAttribute('data-grupo');
            if (grupo !== null) {
                modernSetActiveGroup(btn, grupo, true);
            }
        });
    }

    const pills = document.querySelectorAll('.modern-group-link');
    let el = null;
    let name = defaultGrupMenu;
    pills.forEach(btn => {
        if (btn.getAttribute('data-grupo') === defaultGrupMenu) {
            el = btn;
        }
    });
    if (!el && pills.length) {
        el = pills[0];
        name = el.getAttribute('data-grupo') || el.textContent.trim();
    }
    modernSetActiveGroup(el, name || defaultGrupMenu, false);
});
