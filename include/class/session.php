<?php
class session {
    
    public static function startSess() {
        ini_set('session.use_only_cookies', 
                SESSION_USE_ONLY_COOKIES);
        
        $cookieParams = session_get_cookie_params();
        session_set_cookie_params(
                                    $cookieParams["lifetime"], 
                                    $cookieParams["path"], 
                                    $cookieParams["domain"], 
                                    SESSION_SECURE, 
                                    SESSION_HTTP_ONLY
                                 );

        session_start();
        session_regenerate_id(SESSION_REGENERATE_ID);   
    }
    
    public static function destroySess() {

        $_SESSION = array();

        $params = session_get_cookie_params();

        setcookie(  session_name(), 
                    '', 
                    time() - 42000, 
                    $params["path"], 
                    $params["domain"], 
                    $params["secure"], 
                    $params["httponly"]
                );

        session_destroy();
    }
    
    public static function set($key, $value) {
        $_SESSION[$key] = $value;
    }

    public static function destroy($key) {
        if ( isset($_SESSION[$key]) )
            unset($_SESSION[$key]);
    }
    
    public static function get($key, $default = null) {
        if(isset($_SESSION[$key]))
            return $_SESSION[$key];
        else
            return $default;
    }
    
}
