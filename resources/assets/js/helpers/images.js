import * as _ from 'lodash'
import $ from 'jquery'

const fadeWhenLoaded = function(selector) {

    $.each($(selector), function(index, el) {

        const $el = $(el);

        if (!el.complete) {

            $el.css('opacity', 0);

            $el.on('load', function() {

                $(this).animate({opacity: 1}, 'normal');
            });

            if (el.complete)
                $el.css('opacity', 1);
        }
    });
};

const getMediaImageOriginalUrl = function(mediaImage) {

    if (!mediaImage || !_.has(mediaImage, 'files'))
        return '';

    const file = _.find(mediaImage.files, (file) => {

        return file.type === 'original';
    });

    return file ? file.url : '';
};

const isEmpty = function(mediaImage) {

    return _.isEmpty(getMediaImageOriginalUrl(mediaImage));
};

export {

    fadeWhenLoaded,
    getMediaImageOriginalUrl,
    isEmpty
}