//window._ = require('lodash');

/**
 * We'll load jQuery and the Bootstrap jQuery plugin which provides support
 * for JavaScript based Bootstrap features such as modals and tabs. This
 * code may be modified to fit the specific needs of your application.
 */

try {
    window.Popper = require('popper.js').default;
    window.$ = window.jQuery = require('jquery');

    require('bootstrap');
    window.ClipboardJS = require('clipboard/dist/clipboard');
    require('tinymce');
    require('chosen-js');
} catch (e) {
}

/**
 * We'll load the axios HTTP library which allows us to easily issue requests
 * to our Laravel back-end. This library automatically handles sending the
 * CSRF token as a header based on the value of the "XSRF" token cookie.
 */

window.axios = require('axios');

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

/**
 * Next we will register the CSRF Token as a common header with Axios so that
 * all outgoing HTTP requests automatically have it attached. This is just
 * a simple convenience so we don't have to attach every token manually.
 */

let token = document.head.querySelector('meta[name="csrf-token"]');

if (token) {
    window.axios.defaults.headers.common['X-CSRF-TOKEN'] = token.content;
} else {
    console.error('CSRF token not found: https://laravel.com/docs/csrf#csrf-x-csrf-token');
}

/**
 * Echo exposes an expressive API for subscribing to channels and listening
 * for events that are broadcast by Laravel. Echo and event broadcasting
 * allows your team to easily build robust real-time web applications.
 */

// import Echo from 'laravel-echo'

// window.Pusher = require('pusher-js');

// window.Echo = new Echo({
//     broadcaster: 'pusher',
//     key: process.env.MIX_PUSHER_APP_KEY,
//     cluster: process.env.MIX_PUSHER_APP_CLUSTER,
//     encrypted: true
// });


/**
 * Nova codes start here
 */
function escapeHtml(unsafe) {
    return unsafe
        .replace(/&/g, "&amp;")
        .replace(/</g, "&lt;")
        .replace(/>/g, "&gt;")
        .replace(/"/g, "&quot;")
        .replace(/'/g, "&#039;");
}

(function ($) {
    $.fn.uploader = function () {
        return this.each(function () {
            const $this = $(this);
            const $file = $this.find('input[type=file]');
            let config = $.extend({
                bounded: 'yes',
                confirmationTemplate: 'Are you sure you want to upload ||FILE||',
                counter: 0,
                name: 'attachments',
                number: 1,
                unknownError: 'Upload failed for an unknown reason'
            }, $this.data());

            config.bounded = config.bounded === 'yes';
            config.number = parseInt(config.number);
            if (isNaN(config.number)) {
                config.number = 1
            }
            config.counter = parseInt(config.counter);
            if (isNaN(config.counter)) {
                config.counter = 0;
            }
            config.action = $this.closest('form').attr('action');
            config.serial = config.counter + 1;
            if ((config.bounded) && (config.counter >= config.number)) {
                $file.prop('disabled', true);
            }
            $this.on('click', '[data-dismiss=alert]', function (event) {
                config.counter--;
                if (config.counter < config.number) {
                    $file.prop('disabled', false);
                }
            });
            $file.on('change', function (event) {
                if (this.files.length === 0) {
                    return;
                }
                if ((config.bounded) && (config.counter >= config.number)) {
                    return;
                }
                $this.find('.errors-section').html('');
                let [file] = this.files;
                needsConfirmation(config.confirmationTemplate.replace('||FILE||', escapeHtml(file.name)), function () {
                    config.counter++;
                    config.serial++;
                    if ((config.bounded) && (config.counter >= config.number)) {
                        $file.prop('disabled', true);
                    }
                    let formData = new FormData();
                    let serial = config.serial;
                    $this.find('.files-section').append('<div class="alert alert-info" data-serial="' + serial + '"><span>' +
                        escapeHtml(file.name) + '</span><input type="hidden" name="' + config.name + '[]' + '" value=""></div>');
                    formData.append('file', file);
                    let onError = function (msg) {
                        config.counter--;
                        if (config.counter < config.number) {
                            $file.prop('disabled', false);
                        }
                        $this.find('.errors-section').html('<span class="text-danger">' + msg + '</span>');
                        $this.find('.files-section').find('[data-serial=' + serial + ']').remove();

                    };
                    axios.post(config.action,
                        formData,
                        {
                            headers: {
                                'Content-Type': 'multipart/form-data'
                            }
                        }
                    )
                        .then(function (response) {
                            if (!response.data.claim_code) {
                                onError(config.unknownError);
                                return;
                            }
                            $this.find('.files-section [data-serial=' + serial + ']').removeClass('alert-info').addClass('alert-success')
                                .append('<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>')
                                .find('input[type=hidden]').val(response.data.claim_code);
                        })
                        .catch(function (error) {
                            let data = error.response.data;
                            onError((data.errors && data.errors.file) ? (data.errors.file[0]) : (data.message || config.unknownError));
                        });
                });
            });
        });
    };
})(jQuery);

const tinymceBaseConfig = {
    plugins: 'print preview searchreplace autolink directionality visualblocks visualchars fullscreen image link media template codesample table charmap hr pagebreak nonbreaking anchor toc insertdatetime advlist lists wordcount imagetools textpattern help',
    toolbar: 'formatselect | bold italic strikethrough forecolor backcolor | link image media | alignleft aligncenter alignright alignjustify  | numlist bullist outdent indent | removeformat | addcomment',
    image_advtab: true,

    importcss_append: true,
    height: 400,
    file_picker_callback: function (callback, value, meta) {
        /* Provide file and text for the link dialog */
        if (meta.filetype === 'file') {
            callback('https://www.google.com/logos/google.jpg', {text: 'My text'});
        }

        /* Provide image and alt text for the image dialog */
        if (meta.filetype === 'image') {
            callback('https://www.google.com/logos/google.jpg', {alt: 'My alt text'});
        }

        /* Provide alternative source and posted for the media dialog */
        if (meta.filetype === 'media') {
            callback('movie.mp4', {source2: 'alt.ogg', poster: 'https://www.google.com/logos/google.jpg'});
        }
    },
    image_caption: true,
    content_style: '.mce-annotation { background: #fff0b7; } .tc-active-annotation {background: #ffe168; color: black; }',
    style_formats: [
        {
            title: 'Image Left',
            selector: 'img',
            styles: {
                'float': 'left',
                'margin': '0 10px 0 10px'
            }
        },
        {
            title: 'Image Right',
            selector: 'img',
            styles: {
                'float': 'right',
                'margin': '0 0 10px 10px'
            }
        }
    ],
    images_upload_url: 'postAcceptor.php'
};

window.createNotification = function(title, options) {
    options = options || {};
    if (!("Notification" in window)) {
        alert("This browser does not support desktop notification");
    }
    else if (Notification.permission === "granted") {
        new Notification(title, options);
    }

    else if (Notification.permission !== "denied") {
        Notification.requestPermission().then(function (permission) {
            if (permission === "granted") {
                new Notification(title, options);
            }
        });
    }
};

window.initWYSIWYG = function (selector) {
    tinymce.init(Object.assign({}, tinymceBaseConfig, {selector: selector}));
};

window.submitDataToUrl = function (url, data, method) {
    method = (method || 'GET').toUpperCase();
    const formMethod = method.toLowerCase();
    const form = document.createElement('form');
    form.setAttribute('id', 'submitToUrlForm')
    let html = '';
    if (formMethod === 'get') {
        form.setAttribute('method', 'get');
    } else {
        form.setAttribute('method', 'post');
        html += '<input type="hidden" name="_token" value="' + token.content + '">'
            + '<input type="hidden" name="_method" value="' + method + '">';
    }
    form.setAttribute('action', url);
    for (let item in data) {
        if (data.hasOwnProperty(item)) {
            html += '<input type="hidden" name="' + item + '" value="' + data[item] + '">';
        }
    }
    form.innerHTML = html;
    document.body.appendChild(form);
    form.submit();
};


window.removeItem = function (url, message) {
    needsConfirmation(message, function () {
        submitDataToUrl(url, {}, 'DELETE');
    });
};

(function (w, $) {
    let $confirmationModal = $('#confirm-modal')
    $confirmationModal.on('shown.bs.modal', function () {
        $confirmationModal.find('#confirm-ok').focus();
    });
    window.needsConfirmation = function (message, cb) {
        if ($confirmationModal.length === 0) {
            if (confirm(message)) {
                cb();
            }
            return
        }

        const func = function (e) {
            e.preventDefault();
            $confirmationModal.modal('hide');
            cb();
        };
        $confirmationModal.off('.confirm');
        $confirmationModal.on('click.confirm', '#confirm-ok', func);
        $confirmationModal.find('.modal-body').html(message);
        $confirmationModal.modal('show');
    };

    $(function () {
        new window.ClipboardJS('.btn-copy');
        $(".chosen-select").chosen({disable_search_threshold: 10});
        $('[data-toggle="tooltip"]').tooltip();
        $('.uploader').uploader();

        $('.listing-sort').on('click', '.dropdown-item', function (event) {
            event.preventDefault();
            var $this = $(this);
            var $wrapper = $(event.delegateTarget);
            $wrapper.find('[data-role=' + $this.data('target') + ']').val($this.data('value'));
            $this.parent().prev().html($this.html());
            $wrapper = null;
            $this = null;
        });
    });
})(window, window.jQuery);

