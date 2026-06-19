/**
 * Custom JS — loaded after scripts.js
 * Overrides sidebar toggle behaviour and fixes Bootstrap dropdown interference.
 */

"use strict";

$(document).ready(function () {

    // -----------------------------------------------------------------
    // 1. Prevent Bootstrap / Popper from taking over sidebar dropdowns.
    //    Stisla's scripts.js already handles them via slideToggle.
    //    Removing data-toggle="dropdown" stops Bootstrap from applying
    //    Popper.js inline position/transform styles that cause overlap.
    // -----------------------------------------------------------------
    $('.main-sidebar .sidebar-menu .has-dropdown').each(function () {
        $(this).removeAttr('data-toggle');
    });

    // -----------------------------------------------------------------
    // 2. Re-bind sidebar dropdown click → accordion slideToggle.
    //    (scripts.js already does this but we re-bind after stripping
    //    data-toggle to ensure no Bootstrap interference.)
    // -----------------------------------------------------------------
    $('.main-sidebar .sidebar-menu li a.has-dropdown').off('click').on('click', function () {
        var $menu = $(this).parent().find('> .dropdown-menu');
        $menu.slideToggle(300);
        return false;
    });

    // -----------------------------------------------------------------
    // 3. Sidebar toggle override:
    //    Desktop  (> 1024px) → hamburger toggles MINI mode (icons only)
    //    Mobile   (≤ 1024px) → hamburger slides overlay sidebar in/out
    // -----------------------------------------------------------------
    $('[data-toggle="sidebar"]').off('click').on('click', function () {
        var body    = $('body');
        var isMobile = $(window).outerWidth() <= 1024;

        if (isMobile) {
            // Mobile: slide the overlay sidebar in or out
            if (body.hasClass('sidebar-show')) {
                body.removeClass('sidebar-show').addClass('sidebar-gone');
            } else {
                body.removeClass('sidebar-gone').addClass('sidebar-show');
            }
        } else {
            // Desktop: toggle between full sidebar and icon-only mini mode
            body.removeClass('sidebar-gone');

            if (body.hasClass('sidebar-mini')) {
                // ── Restore full sidebar ──
                body.removeClass('sidebar-mini');
                $('.main-sidebar .sidebar-menu > li > ul .dropdown-title').remove();
                $('.main-sidebar .sidebar-menu > li > a')
                    .removeAttr('data-original-title title');
                try { $('[data-toggle="tooltip"]').tooltip('dispose'); } catch (e) {}
            } else {
                // ── Switch to mini (icon strip + hover flyouts) ──
                body.addClass('sidebar-mini').removeClass('sidebar-show');

                // Close any open dropdowns
                $('.main-sidebar .sidebar-menu .dropdown-menu').hide();

                // Add section title into each flyout dropdown
                $('.main-sidebar .sidebar-menu > li').each(function () {
                    var $li  = $(this);
                    var $a   = $li.find('> a');
                    var $sub = $li.find('> .dropdown-menu');

                    if ($sub.length) {
                        if (!$sub.find('.dropdown-title').length) {
                            $sub.prepend(
                                '<li class="dropdown-title pt-3">' +
                                $a.text().trim() +
                                '</li>'
                            );
                        }
                    } else {
                        // Tooltip for icon-only items
                        var label = $a.text().trim();
                        $a.attr({ title: label, 'data-original-title': label });
                        $a.tooltip({ placement: 'right' });
                    }
                });
            }
        }

        return false;
    });

    // -----------------------------------------------------------------
    // 4. Close mobile sidebar when clicking outside it
    // -----------------------------------------------------------------
    $(document).on('click', function (e) {
        var $t = $(e.target);
        if (
            $(window).outerWidth() <= 1024 &&
            $('body').hasClass('sidebar-show') &&
            !$t.closest('.main-sidebar').length &&
            !$t.closest('[data-toggle="sidebar"]').length
        ) {
            $('body').removeClass('sidebar-show').addClass('sidebar-gone');
        }
    });

    // -----------------------------------------------------------------
    // 5. On mobile, auto-close sidebar when a nav link is clicked
    // -----------------------------------------------------------------
    $(document).on('click', '.main-sidebar .sidebar-menu a:not(.has-dropdown)', function () {
        if ($(window).outerWidth() <= 1024 && $('body').hasClass('sidebar-show')) {
            $('body').removeClass('sidebar-show').addClass('sidebar-gone');
        }
    });

});
