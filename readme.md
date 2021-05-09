# hector

laravel 的 api 认证中间件，基于jwt。因为 sanctum 太简单，passport 又太复杂。

## 安装

```bash
composer require cola/hector
```

用户模型中 use Cola\Hector\HasApiToken

## 使用

### 路由中间件

```php
Route::middleware('auth:hector')->xxxxx;
```

### 创建token

```php
$account->createToken();
```

向token中添加自定义字段

```php
$account->withPayloads(['role' => $account->roles()->first()->name])->createToken();
```

### 认证通过后可以获取token中的字段

```php
$account->payloads['role'];
```