/**
 * Modules — Shared JS
 */
(function () {
    'use strict';

    $(function () {
        // ── Delete Confirmation ──
        $(document).on('click', '.btn-delete', function (e) {
            e.preventDefault();
            var form = $(this).closest('form');
            var name = $(this).data('name') || 'هذا العنصر';

            if (confirm('هل أنت متأكد من حذف "' + name + '"؟ لا يمكن التراجع عن هذا الإجراء.')) {
                form.submit();
            }
        });

        // ── Filter Reset ──
        $(document).on('click', '.btn-reset-filter', function () {
            var form = $(this).closest('form');
            form.find('input, select').each(function () {
                if (this.type !== 'submit' && this.type !== 'hidden') {
                    $(this).val('');
                }
            });
            form.submit();
        });

        // ── Print Button ──
        $(document).on('click', '.btn-print', function (e) {
            e.preventDefault();
            window.print();
        });

        // ── Auto-dismiss alerts ──
        setTimeout(function () {
            $('.alert-dismissible.auto-dismiss').fadeOut(400, function () {
                $(this).remove();
            });
        }, 4000);
    });
})();
