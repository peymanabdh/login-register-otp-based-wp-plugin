jQuery(document).ready(function ($) {
    $('body').on('click', '#send_code', function (e) {
        e.preventDefault();
        let el = $(this);
        let user_phone = $('.user_phone').val();
        jQuery.ajax({
            url: lr_ajax.ajaxurl,
            type: "POST",
            dataType: "JSON",
            data: {
                action: "wp_lr_auth_send_verification_code",
                user_phone: user_phone,
                _nonce: lr_ajax._nonce
            },
            beforeSend: function () {
                $('#send_code').html('<div class="lds-facebook"><div></div><div></div><div></div></div>');
            },
            success: function (response) {
                if(response.redirect_url){
                    $('#get_user_phone').html('<div class="lds-login-phone-exist">در حال ورورد به سایت</div>');
                    $('#user_phone_number').hide();
                    $('#verification_code').hide();
                   window.location.href = response.redirect_url;
                }else {
                    $('#user_phone_number').hide();
                    $('#verification_code').show();
                    el.attr('id', 'verify_code');
                    el.text('اعتبار سنجی کد تایید');
                         if(response.success){
                            $.toast({
                                text: response.message,
                                icon: 'success',
                                loader: true,        // Change it to false to disable loader
                                loaderBg: '#5a5a5a',  // To change the background
                                textAlign: 'right',
                                bgColor: '#66BB6A',
                                hideAfter: 3000,
                            });
                         }
                } 
            },
            error: function (error) {
                if (error.responseJSON.error) {
                /*    alert(error.responseJSON.message);*/
                    //alert(error.responseJSON.message);
                    $.toast({
                        /*    heading: 'خطا',*/
                        text: error.responseJSON.message,
                        icon: 'error',
                        loader: true,        // Change it to false to disable loader
                        loaderBg: '#5a5a5a',  // To change the background
                        textAlign: 'right',
                        bgColor: '#FF1356',
                        hideAfter: 3000,
                    })
                   
                }
            },
            complete: function () {
                $('#send_code').text('ارسال کد تایید');
            },
        });
    });
    $('body').on('click', '#verify_code', function (e) {
        e.preventDefault();
        let el = $(this);
        let verification_code = $('.verification_code').val();
        jQuery.ajax({
            url: lr_ajax.ajaxurl,
            type: "POST",
            dataType: "JSON",
            data: {
                action: "wp_lr_auth_verify_verification_code",
                verification_code: verification_code,
                _nonce: lr_ajax._nonce
            },
            beforeSend: function () {
                $('#verify_code').html('<div class="lds-facebook"><div></div><div></div><div></div></div>');
            },
            success: function (response) {
                   /* $('#register_form').show();*/
                if(response.success){
                    $.toast({
                        /*    heading: 'خطا',*/
                        text: response.message,
                        icon: 'success',
                        loader: true,        // Change it to false to disable loader
                        loaderBg: '#5a5a5a',  // To change the background
                        textAlign: 'right',
                        bgColor: '#66BB6A',
                        hideAfter: 3000,
                    });
                    window.location.href = response.redirect_url
                }
                    // $('#get_user_phone').html('<div id="register_form"> <div class="form-group"><label>نام و نام خانوادگی*</label> <input type="text" class="form-control display_name" value=""  placeholder="نام و نام خانوادگی..."> </div> <div class="form-group"><label>ایمیل*</label><input type="email..." class="form-control email" value="" placeholder="email..." dir="ltr"></div> <div class="form-group"><label>رمز عبور*</label><input type="text" class="form-control password" value=""></div> <div class="form-group"> <a href="" class="btn btn_apply w-100 " id="register_user">ثبت نام</a> </div></div>');

            },
            error: function (error) {
           /*     if (error.responseJSON.error) {
                        alert(error.responseJSON.message);
                }*/
                if (error.responseJSON.error) {
                    $.toast({
                        /*    heading: 'خطا',*/
                        text: error.responseJSON.message,
                        icon: 'error',
                        loader: true,        // Change it to false to disable loader
                        loaderBg: '#5a5a5a',  // To change the background
                        textAlign: 'right',
                        bgColor: '#FF1356',
                        hideAfter: 3000,
                    })
                }
            },
            complete: function () {
                $('#verify_code').html('ثبت نام');
            },
        });
    });

})