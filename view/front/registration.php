<?php
function wp_ls_register_layout(){
    if(is_user_logged_in()) {
        wp_redirect(site_url());
    }
    ?>
    <div class="col-lg-12 col-md-12 position-static p-4">
                                <div class="log_wraps">
                                    <a href="index.html" class="log-logo_head"><img src="<?php echo LR_PLUGIN_URL.'assets/img/logo.png' ?>" class="img-fluid" width="80" alt="" /></a>
                                    <div id="get_user_phone">
                                        <div class="form-group" id="user_phone_number">
                                            <label for="user_phone">شماره موبایل*</label>
                                            <input type="text" class="form-control user_phone" value="">
                                        </div>
                                        <div class="form-group" id="verification_code">
                                            <label for="verification_code"> کد تاییده*</label>
                                            <input type="text" class="form-control verification_code">
                                        </div>
                                        <div class="form-group">
                                            <a href="" class="btn btn_apply w-100 " id="send_code">ارسال کد تاییده</a>
                                        </div>
                                    </div>

                                </div>
    </div>

    <?php
}
add_shortcode('login-register','wp_ls_register_layout');
