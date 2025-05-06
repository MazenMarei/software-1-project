<?php
class home {
    public function index() {
        $pass = password_hash("123456", PASSWORD_BCRYPT) ;
        var_dump($pass) ;

        password_verify("123456", $pass) ? var_dump("true") : var_dump("false") ;
    }
}
?>
