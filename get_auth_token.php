<?php
/**
 * 获取API认证令牌的辅助脚本
 * 使用方法: php get_auth_token.php
 */

require_once 'vendor/autoload.php';

// 启动Laravel应用
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\Hash;

echo "=== 珠宝电商API认证令牌获取工具 ===\n\n";

try {
    // 查找第一个用户
    $user = User::first();
    
    if (!$user) {
        echo "❌ 未找到用户，请先运行数据库迁移和种子数据：\n";
        echo "   php artisan migrate --seed\n";
        exit(1);
    }
    
    echo "✅ 找到用户: {$user->name} ({$user->email})\n";
    
    // 检查用户是否已有令牌
    $existingTokens = $user->tokens()->get();
    if ($existingTokens->count() > 0) {
        echo "⚠️  用户已有 {$existingTokens->count()} 个令牌\n";
        echo "是否要创建新令牌？(y/N): ";
        $handle = fopen("php://stdin", "r");
        $line = fgets($handle);
        fclose($handle);
        
        if (trim(strtolower($line)) !== 'y') {
            echo "使用现有令牌:\n";
            foreach ($existingTokens as $token) {
                echo "  - {$token->name}: {$token->plainTextToken ?? '已隐藏'}\n";
            }
            exit(0);
        }
    }
    
    // 创建新令牌
    $tokenName = 'postman-api-token-' . date('Y-m-d-H-i-s');
    $token = $user->createToken($tokenName);
    
    echo "✅ 成功创建认证令牌:\n";
    echo "   令牌名称: {$tokenName}\n";
    echo "   令牌值: {$token->plainTextToken}\n\n";
    
    echo "📋 Postman设置步骤:\n";
    echo "1. 打开Postman\n";
    echo "2. 创建新环境或编辑现有环境\n";
    echo "3. 添加变量:\n";
    echo "   - base_url: http://localhost:8000/api\n";
    echo "   - auth_token: {$token->plainTextToken}\n";
    echo "4. 保存环境并选择使用\n\n";
    
    echo "🔧 或者直接在请求头中使用:\n";
    echo "   Authorization: Bearer {$token->plainTextToken}\n\n";
    
    // 检查用户权限
    if ($user->is_admin) {
        echo "✅ 用户具有管理员权限，可以测试所有端点\n";
    } else {
        echo "⚠️  用户不是管理员，某些管理员端点可能无法访问\n";
        echo "   如需测试管理员功能，请使用管理员账户\n";
    }
    
    echo "\n🎉 设置完成！现在可以开始测试API了\n";
    
} catch (Exception $e) {
    echo "❌ 错误: " . $e->getMessage() . "\n";
    echo "请确保:\n";
    echo "1. Laravel应用已正确配置\n";
    echo "2. 数据库连接正常\n";
    echo "3. 已运行数据库迁移\n";
    exit(1);
}
