/**
 * Arrastra modales `.item-details-form` desde `.item-details-form-header`.
 *
 * @param {jQuery} modal Elemento modal
 * @param {string} namespace Namespace de eventos (único por fragmento AJAX)
 */
window.fnjs_initItemDetailsFormDrag = function (modal, namespace) {
    if (!modal || !modal.length) {
        return;
    }

    var header = modal.find('.item-details-form-header');
    if (!header.length) {
        return;
    }

    var ns = namespace || 'itemDetailsForm';
    var drag = { active: false, startX: 0, startY: 0, startLeft: 0, startTop: 0, fixed: false };

    header.off('mousedown.' + ns).on('mousedown.' + ns, function (e) {
        if (e.which !== 1) {
            return;
        }

        drag.fixed = modal.css('position') === 'fixed';
        drag.active = true;

        if (drag.fixed) {
            var rect = modal[0].getBoundingClientRect();
            drag.startX = e.clientX;
            drag.startY = e.clientY;
            drag.startLeft = rect.left;
            drag.startTop = rect.top;
            modal.css({
                transform: 'none',
                left: drag.startLeft,
                top: drag.startTop,
                right: 'auto',
                bottom: 'auto',
                margin: 0
            });
        } else {
            var pos = modal.position();
            drag.startX = e.pageX;
            drag.startY = e.pageY;
            drag.startLeft = pos.left;
            drag.startTop = pos.top;
        }

        e.preventDefault();
    });

    $(document)
        .off('mousemove.' + ns + ' mouseup.' + ns)
        .on('mousemove.' + ns, function (e) {
            if (!drag.active) {
                return;
            }
            if (drag.fixed) {
                modal.css({
                    left: drag.startLeft + (e.clientX - drag.startX),
                    top: drag.startTop + (e.clientY - drag.startY)
                });
            } else {
                modal.css({
                    left: drag.startLeft + (e.pageX - drag.startX),
                    top: drag.startTop + (e.pageY - drag.startY)
                });
            }
        })
        .on('mouseup.' + ns, function () {
            drag.active = false;
        });
};
