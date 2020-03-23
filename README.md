# php-user-auth

用户认证核心库，目标是抽象并统一认证API, 各个php框架能在其之上快速实现一个稳定的用户认证库。

## 概念

### 用户身份对象

用于表示用户身份

### 用户身份对象提供器

对于不同系统，用户身份信息来源可能都不一样，可能来源于数据库、配置文件、甚至第三方系统等。

### 认证器

核心; 抽象用户认证API

### 认证管理器

组装内部组件，创建认证器

### 访问控制器

提供一个简单的基于资源的权限访问控制功能

### 权限资源提供器

权限资源可能来源于数据库、配置文件等，所以抽象出来

### 事件管理器

为认证器、访问控制器提供事件处理能力

## 基本使用方式

// 配置
$config = [
    'default' => 'test',
    'authenticators' => [
        'test' => [
            'driver' => 'session',
            'params' => [
                'session_key' => 'UserIdentity',
            ],
            'provider' => [
                'driver' => 'model',
                'params' => [
                    'model' => \app\common\model\User::class,
                ]
            ],
            'events' => [
                'login_before' => []
            ]
        ],
        'test2' => [
            'driver' => 'token',
            'params' => [
                'token_key' => 'user-token',
            ],
            'provider' => [
                'driver' => 'model',
                'params' => [
                    'model' => \app\common\model\User::class,
                ]
            ],
            'events' => [
                'login_before' => []
            ]
        ]
    ]
];

$authManager = new AuthManager($config);

// 注册认证器
$authManager->registerAuthenticatorCreator('session', function($config) {
    return new SessionAuthenticator($config['session_key']);
});
// 注册用户提供器
$authManager->registerUserProviderCreator('model', function($config) {
    return new ModelUserProvider($config['model']);
});

// 创建默认认证器
$authenticator = $authManager->create();
// 创建指定的认证器
// $authenticator = $authManger->create('test2');

// 登录
$authenticator->login([
    'username' => 'xxx',
    'password' => '****',
]);

// 是否已登录
$authenticator->isLogined();

// 获取用户
$user = $authenticator->user();

// 登出
$authenticator->logout();

## 扩展方式

## 扩展认证库

- [php-user-auth-think](https://github.com/liuzhanpeng/php-user-auth-think) ThinkPHP认证库