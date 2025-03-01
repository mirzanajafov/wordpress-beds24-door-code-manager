jQuery(document).ready(function ($) {

    const elements = $('.modal-overlay, .modal');

    $('.close-modal').click(function () {
        elements.removeClass('active');
    });


    $(document).on('click', '.dcm__table-page', function () {
        $post_id = $(this).data('post_id');
        elements.addClass('active');
        $.ajax({
            url: dcm.ajaxurl,
            type: 'get',
            data: {
                action: 'dcm_getDoorCodeInputs',
                post_id: $post_id
            },
            beforeSend: function () {
                $('.dcm__modal-content').html('<div class="loader"></div>')
            },
            success: function (response) {
                $('.dcm__modal-content').html(response)
            }

        })
    });

    $(document).on('click', '.dcm__save-button', function (event) {
        const button = event.currentTarget;
        const text = button.querySelector(".text");
        const loader = button.querySelector(".loader");


        const data = {
            page_id: $('.dcm__form-wrapper').data('page_id'),
            codes: {}
        }
        $('.dcm_input').each(function () {
            let shortcode = $(this).data('shortcode');
            let code = $(this).val();
            data.codes[shortcode] = code
        });


        const page_id = $(this).parents('.form-wrapper').data('page_id');

        $.ajax({
            url: dcm.ajaxurl,
            type: 'post',
            data: {
                action: 'dcm_saveDoorCodeInputs',
                data: JSON.stringify(data),
            },
            beforeSend: function () {
                button.classList.add("active");
                button.disabled = true;
                text.innerText = "Saving";

            },
            success: function (response) {
                $('.dcm__modal-content').html(response);
            }

        })
    });

    $(document).on('click', '.dcm__view-button', function (event) {
        event.stopPropagation();
        elements.addClass('active');
        $post_id = $(this).data('post_id');
        $.ajax({
            url: dcm.ajaxurl,
            type: 'get',
            data: {
                action: 'dcm_getRoomUrl',
                post_id: $post_id
            },
            beforeSend: function () {
                $('.dcm__modal-content').html('<div class="loader"></div>')
            },
            success: function (response) {
                $('.dcm__modal-content').html(response)
            }
        })
    });

    $(document).on('click', '.dcm__update-url-button', function (event) {
        const button = event.currentTarget;
        const text = button.querySelector(".text");
        const loader = button.querySelector(".loader");
        if (!confirm("Are you sure you want to change this page's URL? (Also it will changes on beds24 room template) ")) {
            return
        }
        $.ajax({
            url: dcm.ajaxurl,
            type: 'post',
            data: {
                action: 'dcm_updatePageUrl',
                page_id: $('.dcm__form-wrapper').data('page_id'),
                old_url: $('.dcm_url-input').val()
            },
            beforeSend: function () {
                button.classList.add("active");
                button.disabled = true;
                text.innerText = "Updating";
            },
            success: function (response) {
                button.classList.remove("active");
                button.disabled = false;
                text.innerText = "Update";
                $('.dcm_url-input').val(response)
            },

        })
    });

    $(document).on('click', '.ClickToCopyUrl, .dcm_url-input', function () {
        $('.dcm_url-input').select();
        document.execCommand('copy');
        $('.ClickToCopyUrl').html('Copied to clipboard!')
        setTimeout(function () {
            $('.ClickToCopyUrl').html('Click to copy url')
        }, 500)
    })


    $(document).on('click', '.dcm__beds24__list_property', function () {
        // $(this).find('.dcm__beds24__list_property-info').toggle();
        elements.addClass('active');
        const propId = $(this).data('prop_id');
        $.ajax({
            url: dcm.ajaxurl,
            type: 'get',
            data: {
                action: 'dcm_getBeds24PropertyInfo',
                propId: propId
            },
            beforeSend: function () {
                $('.dcm__modal-content').html('<div class="loader"></div>')
            },
            success: function (response) {
                $('.dcm__modal-content').html(response)
            }
        })
    });

    $(document).on('click', '.dcm__save-beds24-property-button', function (event) {
        const button = event.currentTarget;
        const text = button.querySelector(".text");
        const loader = button.querySelector(".loader");
        if (!confirm("Are you sure you want to change this property's template?")) {
            return
        }

        const data = {
            propId: $('.dcm__form-wrapper').data('prop_id'),
            template: $('.dcm_beds24_input').val()
        }

        $.ajax({
            url: dcm.ajaxurl,
            type: 'post',
            data: {
                action: 'dcm_updateBeds24PropTemplate',
                data: JSON.stringify(data),
            },
            beforeSend: function () {
                button.classList.add("active");
                button.disabled = true;
                text.innerText = "Saving";

            },
            success: function (response) {
                $('.dcm__modal-content').html(response);
            }

        })
    });
});