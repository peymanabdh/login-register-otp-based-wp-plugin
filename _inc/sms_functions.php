<?php
function wp_lr_add_verification_code_phone($user_phone, $verification_code)
{
    global $wpdb;
    $table = $wpdb->prefix . 'sms_verify_code';
    $stmt = $wpdb->get_row($wpdb->prepare("SELECT phone FROM {$table} WHERE phone ='%s'", $user_phone));
    if ($stmt) {
        $data = [
            'verification_code' => $verification_code,
        ];
        $where = ['phone' => $user_phone];
        $format = ['%s'];
        $where_format = ['%s'];
        $wpdb->update($table, $data, $where, $format, $where_format);
    } else {
        $data = [
            'verification_code' => $verification_code,
            'phone' => $user_phone
        ];
        $format = ['%s', '%s'];
        $wpdb->insert($table, $data, $format);
    }
    
    
}

function _verify_user_phone_status($user_phone)
{
    global $wpdb;
    $table = $wpdb->prefix . 'sms_verify_code';
    $stmt = $wpdb->get_row($wpdb->prepare("SELECT phone FROM {$table} WHERE phone ='%s'", $user_phone));
    if ($stmt) {
        $data = [
            'status' => 1,
        ];
        $where = ['phone' => $user_phone];
        $format = ['%d'];
        $where_format = ['%s'];
        $wpdb->update($table, $data, $where, $format, $where_format);
    }
       
}
