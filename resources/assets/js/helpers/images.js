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

const getMediaImageOriginal = function(mediaImage) {

    return getMediaImageType(mediaImage, 'original');
};

const getMediaImageFocusOrOriginalUrl = function(mediaImage) {

    let file = getMediaImageType(mediaImage, 'region');

    if (!file)
        file = getMediaImageOriginal(mediaImage);

    return file ? file.url : '';
};

const getMediaImageOriginalUrl = function(mediaImage) {

    const file = getMediaImageOriginal(mediaImage);
    return file ? file.url : '';
};

const getMediaImageType = function(mediaImage, type) {

    if (!mediaImage || !_.has(mediaImage, 'files'))
        return '';

    return _.find(mediaImage.files, (file) => {

        return file.type === type;
    });
};

const isEmpty = function(mediaImage) {

    return _.isEmpty(getMediaImageOriginalUrl(mediaImage));
};

export {

    fadeWhenLoaded,
    getMediaImageOriginal,
    getMediaImageFocusOrOriginalUrl,
    getMediaImageOriginalUrl,
    getMediaImageType,
    isEmpty
}