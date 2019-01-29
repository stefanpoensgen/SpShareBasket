(function ($, window) {
    $.plugin('SpShareBasket', {

        init: function () {
            var me = this;
            me.clipboard = new ClipboardJS('[data-clipboard-target]');
        },

        destroy: function () {
            var me = this;
            me._destroy();
        }
    });

    window.StateManager.addPlugin(
        '.main--actions--sharebasket',
        'SpShareBasket'
    );
})(jQuery, window);
