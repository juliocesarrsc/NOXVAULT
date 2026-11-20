<?php
declare(strict_types=1);

define('APP_ROOT', __DIR__);
date_default_timezone_set('America/Sao_Paulo');
$GLOBALS['config'] = require APP_ROOT . '/admin/config/configuracao.php';
spl_autoload_register(function (string $class): void {
    if (str_starts_with($class, 'App\\')) {
        $map = [
            'Core\\Auth'=>'config/autenticacao', 'Core\\Database'=>'config/conexao',
            'Core\\Controller'=>'nucleo/controlador', 'Core\\HttpException'=>'nucleo/erro_http',
            'Core\\Upload'=>'nucleo/envio_imagem', 'Core\\Resources'=>'nucleo/recursos',
            'Models\\Model'=>'models/modelo', 'Models\\Usuario'=>'models/usuarios',
            'Models\\Game'=>'models/jogos', 'Models\\Categoria'=>'models/categorias',
            'Models\\Fornecedor'=>'models/fornecedores', 'Models\\Pedido'=>'models/pedidos',
            'Models\\ItemPedido'=>'models/itens_pedido', 'Models\\Carrinho'=>'models/carrinho',
            'Models\\Dashboard'=>'models/painel', 'Models\\LoginAttempt'=>'models/tentativas_login',
            'Controllers\\StoreController'=>'controllers/loja', 'Controllers\\AuthController'=>'controllers/autenticacao',
            'Controllers\\CartController'=>'controllers/carrinho', 'Controllers\\OrderController'=>'controllers/pedidos',
            'Controllers\\AdminController'=>'controllers/administracao', 'Controllers\\AdminOrderController'=>'controllers/pedidos_admin',
        ];
        $relative = $map[substr($class,4)] ?? null;
        if ($relative) { require APP_ROOT . '/admin/' . $relative . '.php'; }
    }
});
require APP_ROOT . '/admin/nucleo/funcoes.php';
if (PHP_SAPI !== 'cli') {
    ini_set('display_errors', '0');
    ini_set('session.use_strict_mode', '1');
    ini_set('session.use_only_cookies', '1');
    session_name('noxvault_session');
    session_set_cookie_params([
        'lifetime' => 0, 'path' => base_path() ?: '/',
        'secure' => !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off',
        'httponly' => true, 'samesite' => 'Lax',
    ]);
    session_start();
    if (isset($_SESSION['last_activity']) && time() - $_SESSION['last_activity'] > 7200) {
        $_SESSION = [];
        session_regenerate_id(true);
    }
    $_SESSION['last_activity'] = time();
    header('Content-Type: text/html; charset=UTF-8');
    header('X-Content-Type-Options: nosniff');
    header('X-Frame-Options: DENY');
    header('Referrer-Policy: same-origin');
    header("Content-Security-Policy: default-src 'self'; img-src 'self' blob: data:; style-src 'self'; script-src 'self'; object-src 'none'; base-uri 'self'; form-action 'self'; frame-ancestors 'none'");
    header('Cache-Control: no-store');
}
