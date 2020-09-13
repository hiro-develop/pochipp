(function ($){
    $(document).on( 'click', 'ul.yyi-rinker-links a.yyi-rinker-link.yyi-rinker-tracking, div.yyi-rinker-title a.yyi-rinker-tracking, div.yyi-rinker-image a.yyi-rinker-tracking', function ( event ) {
        try {
            var category = 'Rinker';
            var event_label = $(this).data('click-tracking');

            if (typeof gtag !== 'undefined' && $.isFunction(gtag)) {
                gtag('event', 'click', {
                    'event_category': 'Rinker',
                    'event_label': event_label
                });
            } else if (typeof ga !== 'undefined'  && $.isFunction(ga)) {
                ga( 'send', 'event', category, 'click', event_label );
            } else if (typeof _gaq !== 'undefined') {
                _gaq.push(['_trackEvent', category, 'click', event_label]);
            }
        } catch (e) {
            console.log('tracking-error');
            console.log(e.message);
        }
    });
})(jQuery);
