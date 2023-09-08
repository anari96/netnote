<?php
namespace App\Helpers;

use App\Models\Config;
use PulkitJalan\Google\Facades\Google;

class ConfigHelper{
    public static function generateRefreshToken()
    {
        $config_setting = Config::first();
        $config = $config_setting->config;
        $expire_in = $config->expires_in;
        $created = $config->created;

        $oauth = Google::make('oauth2');
        if($expire_in < time() && $config->refresh_token != null){
            $oauth->getClient()->refreshToken($config->refresh_token);
            $_tmp = $oauth->getClient()->getAccessToken();
            $_tmp['code'] = $config->code;
            $_tmp['folder_id'] = $config->folder_id;

            $config = (object)$_tmp;

            $config->expires_in = $oauth->getClient()->getAccessToken()['created'] + $oauth->getClient()->getAccessToken()['expires_in'];
            $config_setting->update([
                'json_config' => json_encode($config)
            ]);
 
        }

        return $config;
    }
}