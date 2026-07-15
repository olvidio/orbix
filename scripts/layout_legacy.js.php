function legacySyncChromeOffset() {
    const shell = document.getElementById('orbixLegacyShell');
    if (!shell) {
        return;
    }
    const height = Math.ceil(shell.getBoundingClientRect().height);
    document.documentElement.style.setProperty('--legacy-chrome-offset', height + 'px');
}

function legacyCaptureUdmZBase() {
    if (typeof window.um !== 'undefined' && window.um.e && window.um._zBase === undefined) {
        window.um._zBase = parseInt(window.um.e[6], 10) || 1000;
    }
}

function legacyInitChromeOffsetObserver() {
    const shell = document.getElementById('orbixLegacyShell');
    if (!shell || shell.dataset.legacyChromeBound === '1') {
        return;
    }
    shell.dataset.legacyChromeBound = '1';

    const sync = () => {
        legacyCaptureUdmZBase();
        window.requestAnimationFrame(legacySyncChromeOffset);
    };

    if (typeof ResizeObserver !== 'undefined') {
        const observer = new ResizeObserver(sync);
        observer.observe(shell);
    }

    window.addEventListener('resize', sync);
    sync();
}

function legacyResetUdmZIndex() {
    if (typeof window.um === 'undefined' || !window.um.e || window.um._zBase === undefined) {
        return;
    }
    window.um.e[6] = window.um._zBase;
    const links = document.querySelectorAll('#udm a[style*="z-index"]');
    links.forEach((link) => {
        link.style.zIndex = '';
    });
}

function legacyResyncUdmLayout() {
    if (typeof window.um === 'undefined' || !window.um.tr || typeof window.um.gc !== 'function') {
        return;
    }
    try {
        if (typeof window.um.closeAllMenus === 'function') {
            window.um.closeAllMenus();
        }
        legacyResetUdmZIndex();
        const anchor = window.um.gc(window.um.tr);
        if (anchor) {
            window.um.lh = anchor.offsetHeight;
        }
    } catch (e) {
    }
}

function legacyPatchUdmSubmenuGap() {
    if (typeof window.um === 'undefined' || !window.um.n || typeof window.um.n.pu !== 'function') {
        return false;
    }
    if (window.um.n.__orbixGapPatched) {
        return true;
    }
    const originalPu = window.um.n.pu;
    window.um.n.pu = function (menuNode) {
        originalPu.call(this, menuNode);
        if (!menuNode || !menuNode.style) {
            return;
        }
        const marginTop = parseInt(menuNode.style.marginTop, 10);
        if (!Number.isNaN(marginTop) && marginTop > 0) {
            menuNode.style.marginTop = Math.max(0, marginTop - 5) + 'px';
        }
    };
    window.um.n.__orbixGapPatched = true;
    return true;
}

function legacyCleanupBodyArtifacts() {
    const selectors = [
        'body > .slick-columnpicker',
        'body > .slick-gridmenu',
        'body > .slick-columnmenu',
        'body > .slick-cell-menu',
        'body > .slick-context-menu',
        'body > .slick-header-menu',
        'body > .slick-custom-tooltip',
        'body > .ui-datepicker',
        'body > .refresh-mv-overlay'
    ];
    selectors.forEach((selector) => {
        document.querySelectorAll(selector).forEach((node) => node.remove());
    });

    if (typeof fnjs_overlay_ocultar === 'function') {
        fnjs_overlay_ocultar();
    }
}

function legacyAfterMainNavigation() {
    legacyCaptureUdmZBase();
    legacyCleanupBodyArtifacts();
    legacySyncChromeOffset();

    window.requestAnimationFrame(function () {
        legacySyncChromeOffset();
        legacyResyncUdmLayout();
    });
}

(function legacyBootstrap() {
    const boot = () => {
        legacyInitChromeOffsetObserver();
        if (!legacyPatchUdmSubmenuGap()) {
            window.setTimeout(legacyPatchUdmSubmenuGap, 0);
        }
    };
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', boot);
    } else {
        boot();
    }
})();
