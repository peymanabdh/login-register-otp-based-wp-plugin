<?php
add_action('wp_ajax_nopriv_wp_lr_auth_send_verification_code', 'wp_lr_auth_send_verification_code');
add_action('wp_ajax_nopriv_wp_lr_auth_verify_verification_code', 'wp_lr_auth_verify_verification_code');
add_action('wp_ajax_nopriv_wp_lr_register_user', 'wp_lr_register_user');

function wp_lr_auth_send_verification_code()
{
    if (isset($_POST['_nonce']) && !wp_verify_nonce($_POST['_nonce'])) {
        die('Access Denied!!!');
    }
    //    var_dump($_POST['user_phone']);
    $user_phone = sanitize_text_field($_POST['user_phone']);
    wp_lr_validate_phone($user_phone);
      
       $args=[
        'meta_key' => '_lr_user_phone',
        'meta_value' => $user_phone,
        'compare'=>'='
        ];
    $user_phone2 = new WP_User_Query($args);
    if($user_phone2->get_total() == 1){
        $user = get_user_by('login',$user_phone );
        global $wpdb;
        $table = $wpdb->prefix . 'sms_verify_code';
        $status = $wpdb->get_var($wpdb->prepare("SELECT status FROM {$table} WHERE phone = %s", $user_phone));
        //var_dump( $user);
        if ($user && $status == 1)
        {
                $id = (int) $user->data->ID;
                wp_clear_auth_cookie();
                wp_set_current_user($id);
                wp_set_auth_cookie($id);
                wp_send_json([
                    'success' => true,
                    'message' => 'با موفقیت وارد شدید',
                    'redirect_url' => home_url()
                ], 200);
            } else {
                wp_send_json([
                    'success' => false,
                    'message' => 'حساب کابری شما معلق شده است',
                ], 400);
            }
            
        
    }else{
        $verification_code = generate_varification_code();
    //    var_dump($verification_code);
        $patern =  get_option('sms_service_patern');
        $send_sms = wp_ls_send_sms($verification_code, $user_phone, $patern);
        if($send_sms->StrRetStatus =='Ok'){
            wp_lr_add_verification_code_phone($user_phone, $verification_code);
    //        var_dump($user_phone);
            wp_send_json([
    
                'success'=>true,
                'message'=>'کد تاییدیه به شماره موبایل شما ارسال شد.'
            ],200);
         }
    }
}

function wp_lr_auth_verify_verification_code()
{
    if (isset($_POST['_nonce']) && !wp_verify_nonce($_POST['_nonce'])) {
        die('Access Denied!!!');
    }
    $verification_code = sanitize_text_field($_POST['verification_code']);
    wp_lr_validate_verification_code($verification_code);
    wp_lr_check_user_verfication_code($verification_code);
    $user_login = $_SESSION['current_user_phone'];
    $random_password = wp_generate_password(8, false); // تولید یک رمز عبور تصادفی
    $random_email = $_SESSION['current_user_phone'] . '@example.com'; // تولید یک ایمیل تصادفی

//    $user_login = $user_login[0] . rand(10, 99);
    $user_name_family = explode(' ', $_POST['display_name']);
    $data = [
        'user_login' => apply_filters('pre_user_login',  $user_login),
        'user_pass' => apply_filters('per_user_pass', $random_password),
//        'first_name' => apply_filters('pre_user_first_name', sanitize_text_field($user_name_family[0])),
//        'last_name' => apply_filters('pre_user_last_name', sanitize_text_field($user_name_family[1])),
        'user_email' => apply_filters('pre_user_email', $random_email),
        /*        'display_name' => apply_filter('pre_user_display_name', sanitize_text_field($_POST['display_name']))*/
    ];
    $user_id = wp_insert_user($data);
    add_user_meta($user_id,'_lr_user_phone',$_SESSION['current_user_phone']);
    _verify_user_phone_status($_SESSION['current_user_phone']);
    if (!is_wp_error($user_id)) {
        $user = get_user_by('login',$user_login );
        //var_dump( $user);
        if ($user)
        {
            //var_dump(is_wp_error( $user ) );
            $id=(int) $user->data->ID;
            //var_dump($id);
            wp_clear_auth_cookie();
            wp_set_current_user($id);
            wp_set_auth_cookie($id);
            unset($_SESSION['current_user_phone']);
            wp_send_json([
                'success' => true,
                'message' => 'ثبت نام شما با موفقیت صورت گرفت',
                'redirect_url' => home_url()
            ], 200);
            //global $current_user;
            //var_dump($current_user->user_login );
            //var_dump($user);
            //do_action( 'wp_login', $user->data->user_login, $user );

            //wp_set_current_user( $user1->data-ID, $user1->data->user_login );
            //wp_set_auth_cookie( $user1->data->ID, true);
            //do_action( 'wp_login', $user1->data->user_login, $user1);

        }else{
            wp_send_json([
                'success' => true,
                'message' => 'کاربر یافت نشد!'
            ], 403);
        }
        // wp_ls_send_sms("{$data['first_name']};{$data['user_email']};{$data['user_pass']}",$_SESSION['current_user_phone'],'123305');

    } else {
        wp_send_json([
            'success' => true,
            'message' => 'خطایی در ثبت نام شما صورت گرفته است!'
        ], 403);
    }
    
}

function wp_lr_check_user_verfication_code($verification_code)
{
    global $wpdb;
    $table = $wpdb->prefix . 'sms_verify_code';
    $stmt = $wpdb->get_row($wpdb->prepare("SELECT verification_code,phone FROM {$table} WHERE verification_code = '%d'", $verification_code));
    if ($stmt) {
        $_SESSION['current_user_phone'] = $stmt->phone;
       
    } else {
        wp_send_json([
            'error' => true,
            'message' => 'کد تاییدیه معتبر نمی باشد!'
        ], 403);
    }
}

function wp_lr_validate_verification_code($verification_code)
{
    if ($verification_code == '') {
        wp_send_json([
            'error' => true,
            'message' => 'کد تاییدیه دریافتی را وارد نمایید!'
        ], 403);
    }
    if (strlen($verification_code) != 6) {
        wp_send_json([
            'error' => true,
            'message' => 'کد تاییده باید شامل 6 رقم باشد!'
        ], 403);
    }
}

function wp_lr_validate_phone($phone)
{
    // /^(00|09|\+)[0-9]{8,12}$/
    if (!preg_match('/^(09)[0-9]{8,12}$/', $phone)) {
        wp_send_json([
            'error' => true,
            'message' => 'لطفا شماره موبایل معتبر وارد نمایید!'
        ], 403);
    }
}
