<?php
/**
 * API端点快速测试脚本
 * 使用方法: php test_api_endpoints.php
 */

require_once 'vendor/autoload.php';

// 启动Laravel应用
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\Http;

echo "=== 珠宝电商API端点测试工具 ===\n\n";

// 配置
$baseUrl = 'http://localhost:8000/api';
$testResults = [];

// 获取认证令牌
try {
    $user = User::first();
    if (!$user) {
        echo "❌ 未找到用户，请先运行数据库种子\n";
        exit(1);
    }
    
    $token = $user->createToken('test-token')->plainTextToken;
    echo "✅ 获取认证令牌成功\n";
} catch (Exception $e) {
    echo "❌ 获取认证令牌失败: " . $e->getMessage() . "\n";
    exit(1);
}

// 测试函数
function testEndpoint($name, $method, $url, $headers = [], $data = null) {
    global $testResults;
    
    echo "测试: {$name}... ";
    
    try {
        $response = Http::withHeaders($headers);
        
        if ($data) {
            $response = $response->withBody(json_encode($data), 'application/json');
        }
        
        $response = $response->$method($url);
        
        $status = $response->status();
        $success = $status >= 200 && $status < 300;
        
        if ($success) {
            echo "✅ ({$status})\n";
        } else {
            echo "❌ ({$status})\n";
            if ($response->body()) {
                echo "   错误: " . substr($response->body(), 0, 100) . "...\n";
            }
        }
        
        $testResults[] = [
            'name' => $name,
            'method' => $method,
            'url' => $url,
            'status' => $status,
            'success' => $success
        ];
        
    } catch (Exception $e) {
        echo "❌ 异常: " . $e->getMessage() . "\n";
        $testResults[] = [
            'name' => $name,
            'method' => $method,
            'url' => $url,
            'status' => 0,
            'success' => false,
            'error' => $e->getMessage()
        ];
    }
}

// 开始测试
echo "\n开始测试API端点...\n\n";

// 公开端点测试
echo "=== 公开端点测试 ===\n";
testEndpoint('获取产品列表', 'GET', "{$baseUrl}/products");
testEndpoint('搜索产品', 'GET', "{$baseUrl}/products/search?q=ring");
testEndpoint('获取产品统计', 'GET', "{$baseUrl}/products/stats/overview");
testEndpoint('获取FAQ', 'GET', "{$baseUrl}/support/faq");
testEndpoint('获取支持分类', 'GET', "{$baseUrl}/support/categories");

// 认证端点测试
echo "\n=== 认证端点测试 ===\n";
$authHeaders = ['Authorization' => "Bearer {$token}"];

testEndpoint('获取当前用户', 'GET', "{$baseUrl}/user", $authHeaders);
testEndpoint('获取用户工单', 'GET', "{$baseUrl}/support/tickets", $authHeaders);
testEndpoint('获取用户对话', 'GET', "{$baseUrl}/support/chat/conversations", $authHeaders);

// 管理员端点测试
echo "\n=== 管理员端点测试 ===\n";
testEndpoint('获取管理员仪表板', 'GET', "{$baseUrl}/admin/dashboard", $authHeaders);
testEndpoint('获取管理员仪表板统计', 'GET', "{$baseUrl}/admin/dashboard/stats", $authHeaders);
testEndpoint('获取所有客户', 'GET', "{$baseUrl}/admin/customers", $authHeaders);
testEndpoint('获取所有工单(管理员)', 'GET', "{$baseUrl}/admin/support/tickets", $authHeaders);
testEndpoint('获取聊天队列', 'GET', "{$baseUrl}/admin/support/queue", $authHeaders);

// 报告端点测试
echo "\n=== 报告端点测试 ===\n";
testEndpoint('客户细分报告', 'GET', "{$baseUrl}/admin/reports/customer-segments", $authHeaders);
testEndpoint('销售报告', 'GET', "{$baseUrl}/admin/reports/sales", $authHeaders);
testEndpoint('产品性能报告', 'GET', "{$baseUrl}/admin/reports/products", $authHeaders);
testEndpoint('订单分析', 'GET', "{$baseUrl}/admin/reports/orders", $authHeaders);
testEndpoint('收入报告', 'GET', "{$baseUrl}/admin/reports/revenue", $authHeaders);

// 库存端点测试
echo "\n=== 库存端点测试 ===\n";
testEndpoint('获取库存历史', 'GET', "{$baseUrl}/inventory/history");
testEndpoint('获取所有库存', 'GET', "{$baseUrl}/inventory");
testEndpoint('获取库存详情', 'GET', "{$baseUrl}/inventory/1");

// 测试结果统计
echo "\n=== 测试结果统计 ===\n";
$totalTests = count($testResults);
$successfulTests = count(array_filter($testResults, function($result) {
    return $result['success'];
}));
$failedTests = $totalTests - $successfulTests;

echo "总测试数: {$totalTests}\n";
echo "成功: {$successfulTests}\n";
echo "失败: {$failedTests}\n";
echo "成功率: " . round(($successfulTests / $totalTests) * 100, 2) . "%\n";

if ($failedTests > 0) {
    echo "\n=== 失败的测试 ===\n";
    foreach ($testResults as $result) {
        if (!$result['success']) {
            echo "❌ {$result['name']} ({$result['method']} {$result['url']}) - 状态: {$result['status']}\n";
            if (isset($result['error'])) {
                echo "   错误: {$result['error']}\n";
            }
        }
    }
}

echo "\n🎉 测试完成！\n";
echo "\n📋 下一步:\n";
echo "1. 导入Postman集合: Jewelry_E-commerce_API.postman_collection.json\n";
echo "2. 设置环境变量:\n";
echo "   - base_url: {$baseUrl}\n";
echo "   - auth_token: {$token}\n";
echo "3. 开始详细测试各个端点\n";
