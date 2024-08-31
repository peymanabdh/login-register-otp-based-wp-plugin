<?php
function wp_ls_send_sms($args,$to,$bodyId){
//GitHub نمونه کدهای
    $username =  get_option('sms_service_username');
    $password =  get_option('sms_service_password');
    //$patern =  get_option('sms_service_patern');
//بدون نیاز به پکیج گیت هاب Procedural PHP نمونه کدهای
    $data = array('username' => $username, 'password' => $password,'text' => "$args",'to' =>$to ,"bodyId"=>$bodyId );
    $post_data = http_build_query($data);
    $handle = curl_init('https://rest.payamak-panel.com/api/SendSMS/BaseServiceNumber');
    curl_setopt($handle, CURLOPT_HTTPHEADER, array(
        'content-type' => 'application/x-www-form-urlencoded'
    ));
    curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($handle, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($handle, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($handle, CURLOPT_POST, true);
    curl_setopt($handle, CURLOPT_POSTFIELDS, $post_data);
    $response = curl_exec($handle);
    $response = json_decode($response);
    return $response;
}