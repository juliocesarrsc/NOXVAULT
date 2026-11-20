<?php
declare(strict_types=1);
require __DIR__ . '/inicializacao.php';
use App\Controllers\{StoreController,AuthController,CartController,OrderController,AdminController,AdminOrderController};
use App\Core\HttpException;

$requestPath = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
$localPath = substr((string)$requestPath, strlen(base_path()));
if (in_array($localPath, ['/inicio','/inicio/'], true)) {
    $_GET['r'] = 'home';
} elseif ($_SERVER['REQUEST_METHOD'] === 'GET' && !isset($_GET['r']) && in_array($localPath, ['/','/index.php'], true)) {
    header('Location: ' . url('home'), true, 302);
    exit;
}

$routes = [
    'home'=>['GET',StoreController::class,'home'],
    'catalogo'=>['GET',StoreController::class,'catalog'],
    'game'=>['GET',StoreController::class,'game'],
    'login'=>['GET|POST',AuthController::class,'login'],
    'cadastro'=>['GET|POST',AuthController::class,'register'],
    'logout'=>['POST',AuthController::class,'logout'],
    'conta'=>['GET|POST',AuthController::class,'account'],
    'carrinho'=>['GET',CartController::class,'index'],
    'carrinho.alterar'=>['POST',CartController::class,'change'],
    'checkout'=>['POST',CartController::class,'checkout'],
    'pedidos'=>['GET',OrderController::class,'index'],
    'pedido'=>['GET',OrderController::class,'show'],
    'admin'=>['GET',AdminController::class,'dashboard'],
    'admin.listar'=>['GET',AdminController::class,'index'],
    'admin.ver'=>['GET',AdminController::class,'show'],
    'admin.form'=>['GET|POST',AdminController::class,'form'],
    'admin.excluir'=>['POST',AdminController::class,'delete'],
    'admin.pedidos'=>['GET',AdminOrderController::class,'index'],
    'admin.pedido'=>['GET',AdminOrderController::class,'show'],
    'admin.pedido.criar'=>['GET|POST',AdminOrderController::class,'create'],
    'admin.pedido.atualizar'=>['POST',AdminOrderController::class,'update'],
    'admin.pedido.excluir'=>['POST',AdminOrderController::class,'delete'],
];
try {
    $route = input('r','home',$_GET);
    if (!isset($routes[$route])) { throw new HttpException(404,'A página que você procura não foi encontrada.'); }
    [$methods,$class,$action]=$routes[$route];
    if (!in_array($_SERVER['REQUEST_METHOD'],explode('|',$methods),true)) { header('Allow: ' . str_replace('|',', ',$methods)); throw new HttpException(405,'Método não permitido.'); }
    if (str_starts_with($route,'admin')) { \App\Core\Auth::requireAdmin(); }
    if ($_SERVER['REQUEST_METHOD']==='POST') { check_csrf(); }
    (new $class())->$action();
} catch (Throwable $exception) {
    while (ob_get_level()) { ob_end_clean(); }
    $status=$exception instanceof HttpException ? $exception->status : ($exception instanceof DomainException ? 422 : 503);
    http_response_code($status);
    $message=$status===503 ? 'Não foi possível acessar o serviço. Verifique se o MySQL está iniciado e se o banco NOXVAULT foi instalado.' : $exception->getMessage();
    if ($status===503) { error_log('NOXVAULT: ' . $exception->getMessage()); }
    $title='Vamos resolver isso'; $admin=false;
    ob_start(); require APP_ROOT . '/cliente/erro.php'; $content=ob_get_clean();
    require APP_ROOT . '/admin/views/componentes/estrutura.php';
}
