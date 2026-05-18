<?php
use frontend\shared\security\HashFront;
?>
const defaultGrupMenu = window.orbixLayout.defaultGrupMenu;
const menuConfig = window.orbixLayout.menuConfig;

function pills2CreateMenuItem(item, level = 0, crumbPrefix = []) {
    const li = document.createElement('li');

    if (typeof item === 'string') {
        const a = document.createElement('a');
        a.href = '#';
        a.textContent = item;
        a.setAttribute('data-pills2-crumb-path', JSON.stringify(crumbPrefix.concat(item)));
        li.appendChild(a);
    } else {
        const a = document.createElement('a');
        a.href = '#';
        a.textContent = item.name;
        a.setAttribute('onclick', item.onClick);
        const crumbPath = crumbPrefix.concat(item.name);
        a.setAttribute('data-pills2-crumb-path', JSON.stringify(crumbPath));

        if (item.submenu && item.submenu.length > 0) {
            a.classList.add('has-submenu');
            const dropdown = document.createElement('div');
            dropdown.classList.add('dropdown');
            const ul = document.createElement('ul');

            item.submenu.forEach(subitem => {
                ul.appendChild(pills2CreateMenuItem(subitem, level + 1, crumbPath));
            });

            dropdown.appendChild(ul);
            li.appendChild(dropdown);
        }
        li.appendChild(a);
    }

    return li;
}

function pills2CleanMenuLabel(anchor) {
    if (!anchor) {
        return '';
    }
    return anchor.textContent.replace(/[\u25BC\u25B6▼▶]/g, '').trim();
}

/**
 * Ruta de menú desde el tab principal hasta el enlace pulsado (incluye submenús anidados).
 *
 * @returns {string[]}
 */
function pills2MenuPathFromAnchor(anchor) {
    if (!anchor) {
        return [];
    }
    const raw = anchor.getAttribute('data-pills2-crumb-path');
    if (raw) {
        try {
            const parsed = JSON.parse(raw);
            if (Array.isArray(parsed) && parsed.length > 0) {
                return parsed.map((part) => String(part).trim()).filter(Boolean);
            }
        } catch (e) {
            /* fallback abajo */
        }
    }

    const topLi = anchor.closest('.pills2-modulebar__menu.horizontal-menu > li');
    if (!topLi) {
        return [];
    }
    const topA = topLi.querySelector(':scope > a');
    if (!topA) {
        return [];
    }

    const path = [pills2CleanMenuLabel(topA)];
    if (anchor === topA) {
        return path.filter(Boolean);
    }

    const innerAnchors = [];
    let node = anchor;
    while (node && node !== topLi) {
        if (node.tagName === 'A') {
            innerAnchors.unshift(node);
        }
        node = node.parentElement;
    }

    innerAnchors.forEach((a) => {
        const label = pills2CleanMenuLabel(a);
        if (label && path[path.length - 1] !== label) {
            path.push(label);
        }
    });

    return path.filter(Boolean);
}

function pills2BreadcrumbSegments(groupName, menuPath) {
    let segments = Array.isArray(menuPath) ? menuPath.slice() : (menuPath ? [menuPath] : []);
    segments = segments.map((part) => String(part).trim()).filter(Boolean);
    if (segments.length > 0 && segments[0] === groupName) {
        segments = segments.slice(1);
    }
    return segments;
}

function pills2UpdateBreadcrumb(groupName, menuPath) {
    const nav = document.getElementById('pills2Breadcrumb');
    if (!nav) {
        return;
    }
    const segments = pills2BreadcrumbSegments(groupName, menuPath);
    nav.innerHTML = '';
    const parts = [groupName, ...segments].filter(Boolean);
    parts.forEach((text, index) => {
        if (index > 0) {
            const sep = document.createElement('span');
            sep.className = 'pills2-breadcrumb__sep';
            sep.setAttribute('aria-hidden', 'true');
            sep.textContent = '/';
            nav.appendChild(sep);
        }
        const span = document.createElement('span');
        span.className = 'pills2-breadcrumb__segment'
            + (index === parts.length - 1 ? ' pills2-breadcrumb__segment--current' : '');
        span.textContent = text;
        nav.appendChild(span);
    });
}

function pills2GetActiveMenuPath() {
    const active = document.querySelector('.pills2-modulebar__menu.horizontal-menu > li.active > a');
    return active ? pills2MenuPathFromAnchor(active) : [];
}

function pills2SetWorkspaceLabel(groupName) {
    const label = document.getElementById('pills2WorkspaceLabel');
    if (label) {
        label.textContent = groupName || '—';
    }
}

function pills2MarkActiveWorkspaceOption(groupName) {
    document.querySelectorAll('.pills2-workspace-option').forEach(btn => {
        const isActive = btn.getAttribute('data-grupo') === groupName;
        btn.classList.toggle('pills2-workspace-option--active', isActive);
        if (isActive) {
            btn.setAttribute('aria-selected', 'true');
        } else {
            btn.removeAttribute('aria-selected');
        }
    });
}

function pills2CloseWorkspacePanel() {
    const panel = document.getElementById('pills2WorkspacePanel');
    const trigger = document.getElementById('pills2WorkspaceTrigger');
    if (panel) {
        panel.setAttribute('hidden', 'hidden');
    }
    if (trigger) {
        trigger.setAttribute('aria-expanded', 'false');
    }
}

function pills2ToggleWorkspacePanel(evt) {
    if (evt) {
        evt.stopPropagation();
    }
    const panel = document.getElementById('pills2WorkspacePanel');
    const trigger = document.getElementById('pills2WorkspaceTrigger');
    const userPanel = document.getElementById('pills2UserPanel');
    if (!panel || !trigger) {
        return;
    }
    if (userPanel && !userPanel.hasAttribute('hidden')) {
        userPanel.setAttribute('hidden', 'hidden');
        const userTrigger = document.getElementById('pills2UserTrigger');
        if (userTrigger) {
            userTrigger.setAttribute('aria-expanded', 'false');
        }
    }
    const open = panel.hasAttribute('hidden');
    if (open) {
        panel.removeAttribute('hidden');
        trigger.setAttribute('aria-expanded', 'true');
    } else {
        pills2CloseWorkspacePanel();
    }
}

function pills2SetActiveGroup(element, groupName, fromUserClick) {
    document.querySelectorAll('.pills2-workspace-option').forEach(btn => {
        btn.classList.remove('pills2-workspace-option--active');
    });
    if (element) {
        element.classList.add('pills2-workspace-option--active');
        if (fromUserClick) {
            showPortada(groupName);
        }
    }

    pills2SetWorkspaceLabel(groupName);
    pills2MarkActiveWorkspaceOption(groupName);
    pills2CloseWorkspacePanel();

    const horizontalMenu = document.getElementById('horizontalMenu');
    if (!horizontalMenu) {
        return;
    }
    const menuItems = menuConfig[groupName] || [];

    horizontalMenu.innerHTML = '';
    if (Array.isArray(menuItems)) {
        menuItems.forEach((item, index) => {
            const li = pills2CreateMenuItem(item);
            if (index === 0) {
                li.classList.add('active');
            }
            horizontalMenu.appendChild(li);
        });
    } else {
        [...menuItems].forEach((item, index) => {
            const li = pills2CreateMenuItem(item);
            if (index === 0) {
                li.classList.add('active');
            }
            horizontalMenu.appendChild(li);
        });
    }

    pills2AddHorizontalMenuEventListeners();
    pills2UpdateBreadcrumb(groupName, pills2GetActiveMenuPath());
}

function pills2SyncBreadcrumbFromAnchor(anchor) {
    if (!anchor) {
        return;
    }
    const topLi = anchor.closest('.pills2-modulebar__menu.horizontal-menu > li');
    if (topLi) {
        document.querySelectorAll('.pills2-modulebar__menu.horizontal-menu > li').forEach((li) => {
            li.classList.remove('active');
        });
        topLi.classList.add('active');
    }
    const groupName = document.getElementById('pills2WorkspaceLabel')?.textContent?.trim() || '';
    pills2UpdateBreadcrumb(groupName, pills2MenuPathFromAnchor(anchor));
}

function pills2AddHorizontalMenuEventListeners() {
    const horizontalMenu = document.getElementById('horizontalMenu');
    if (!horizontalMenu || horizontalMenu.dataset.pills2MenuBound === '1') {
        return;
    }
    horizontalMenu.dataset.pills2MenuBound = '1';

    horizontalMenu.addEventListener('click', function (e) {
        const anchor = e.target.closest('a[data-pills2-crumb-path]');
        if (!anchor) {
            return;
        }
        const isTopTab = anchor.parentElement?.parentElement?.classList?.contains('horizontal-menu');
        if (isTopTab) {
            const onclick = anchor.getAttribute('onclick') || '';
            if (!onclick.trim() || onclick.trim() === '""') {
                e.preventDefault();
            }
        }
        pills2SyncBreadcrumbFromAnchor(anchor);
    }, true);
}

function pills2ToggleUserPanel(evt) {
    if (evt) {
        evt.stopPropagation();
    }
    const panel = document.getElementById('pills2UserPanel');
    const trigger = document.getElementById('pills2UserTrigger');
    if (!panel || !trigger) {
        return;
    }
    pills2CloseWorkspacePanel();
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
    const userPanel = document.getElementById('pills2UserPanel');
    const userTrigger = document.getElementById('pills2UserTrigger');
    if (userPanel && !userPanel.hasAttribute('hidden')) {
        if (userTrigger && userTrigger.contains(e.target)) {
            return;
        }
        if (!userPanel.contains(e.target)) {
            userPanel.setAttribute('hidden', 'hidden');
            if (userTrigger) {
                userTrigger.setAttribute('aria-expanded', 'false');
            }
        }
    }

    const workspacePanel = document.getElementById('pills2WorkspacePanel');
    const workspaceTrigger = document.getElementById('pills2WorkspaceTrigger');
    const workspaceRoot = document.getElementById('pills2Workspace');
    if (workspacePanel && !workspacePanel.hasAttribute('hidden')) {
        if (workspaceRoot && workspaceRoot.contains(e.target)) {
            return;
        }
        pills2CloseWorkspacePanel();
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
    const workspaceTrigger = document.getElementById('pills2WorkspaceTrigger');
    if (workspaceTrigger) {
        workspaceTrigger.addEventListener('click', pills2ToggleWorkspacePanel);
    }

    const groupNav = document.getElementById('pills2WorkspaceList');
    if (groupNav) {
        groupNav.addEventListener('click', function (e) {
            const btn = e.target.closest('.pills2-group-link');
            if (!btn) {
                return;
            }
            const grupo = btn.getAttribute('data-grupo');
            if (grupo !== null) {
                pills2SetActiveGroup(btn, grupo, true);
            }
        });
    }

    const options = document.querySelectorAll('.pills2-group-link');
    let el = null;
    let name = defaultGrupMenu;
    options.forEach(btn => {
        if (btn.getAttribute('data-grupo') === defaultGrupMenu) {
            el = btn;
        }
    });
    if (!el && options.length) {
        el = options[0];
        name = el.getAttribute('data-grupo') || el.textContent.trim();
    }
    pills2SetActiveGroup(el, name || defaultGrupMenu, false);
});
