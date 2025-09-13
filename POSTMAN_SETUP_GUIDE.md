# Postman API 测试指南

## 概述

这个Postman集合包含了珠宝电商Laravel应用的完整API端点。集合已按功能模块组织，便于测试和管理。

## 文件说明

- `Jewelry_E-commerce_API.postman_collection.json` - 完整的Postman集合文件
- `POSTMAN_SETUP_GUIDE.md` - 本使用指南

## 导入步骤

1. 打开Postman应用
2. 点击左上角的"Import"按钮
3. 选择"Upload Files"或"Link"
4. 选择`Jewelry_E-commerce_API.postman_collection.json`文件
5. 点击"Import"完成导入

## 环境配置

### 1. 设置环境变量

在Postman中创建新环境或使用现有环境，设置以下变量：

```
base_url: http://localhost:8000/api
auth_token: (你的认证令牌)
```

### 2. 获取认证令牌

#### 方法1：通过Laravel Tinker
```bash
php artisan tinker
```
```php
$user = App\Models\User::first();
$token = $user->createToken('api-token')->plainTextToken;
echo $token;
```

#### 方法2：通过API登录（如果实现了登录端点）
```bash
curl -X POST http://localhost:8000/api/login \
  -H "Content-Type: application/json" \
  -d '{"email":"your-email@example.com","password":"your-password"}'
```

## 集合结构

### 1. Authentication
- **Get Current User** - 获取当前用户信息

### 2. Products (Public)
- **Get All Products** - 获取所有产品（公开）
- **Search Products** - 搜索产品
- **Get Product Details** - 获取产品详情
- **Get Product Stats** - 获取产品统计
- **Get Products by Inventory** - 按库存获取产品

### 3. Products (Protected)
- **Create Product** - 创建产品（需要认证）
- **Update Product** - 更新产品
- **Delete Product** - 删除产品

### 4. Admin - Dashboard
- **Get Dashboard** - 获取管理员仪表板
- **Get Dashboard Stats** - 获取仪表板统计

### 5. Admin - Customers
- **Get All Customers** - 获取所有客户
- **Get Customer Details** - 获取客户详情
- **Update Customer** - 更新客户信息
- **Delete Customer** - 删除客户
- **Get Customer Orders** - 获取客户订单
- **Block Customer** - 封禁客户
- **Unblock Customer** - 解封客户

### 6. Admin - Reports
- **Customer Segments** - 客户细分报告
- **Sales Report** - 销售报告
- **Product Performance** - 产品性能报告
- **Order Analytics** - 订单分析
- **Revenue Report** - 收入报告
- **Export Customers** - 导出客户数据
- **Export Sales** - 导出销售数据

### 7. Support - Tickets (User)
- **Get User Tickets** - 获取用户工单
- **Create Ticket** - 创建工单
- **Get Ticket Details** - 获取工单详情
- **Update Ticket** - 更新工单
- **Reply to Ticket** - 回复工单
- **Close Ticket** - 关闭工单
- **Get FAQ** - 获取常见问题
- **Get Support Categories** - 获取支持分类

### 8. Support - Chat (User)
- **Start Chat** - 开始聊天
- **Get Queue Status** - 获取队列状态
- **Leave Queue** - 离开队列
- **Terminate Conversation** - 终止对话
- **Get User Conversations** - 获取用户对话
- **Create Conversation** - 创建对话
- **Get Conversation Details** - 获取对话详情
- **Get Conversation Messages** - 获取对话消息
- **Send Message** - 发送消息

### 9. Admin - Support Tickets
- **Get All Tickets (Admin)** - 获取所有工单（管理员）
- **Get Ticket Details (Admin)** - 获取工单详情（管理员）
- **Update Ticket (Admin)** - 更新工单（管理员）
- **Assign Ticket** - 分配工单
- **Reply to Ticket (Admin)** - 回复工单（管理员）
- **Close Ticket (Admin)** - 关闭工单（管理员）
- **Get Ticket History** - 获取工单历史

### 10. Admin - Support Chat
- **Get All Conversations (Admin)** - 获取所有对话（管理员）
- **Get Conversation Details (Admin)** - 获取对话详情（管理员）
- **Get Conversation Messages (Admin)** - 获取对话消息（管理员）
- **Send Message (Admin)** - 发送消息（管理员）
- **Transfer Conversation** - 转移对话

### 11. Admin - Chat Queue
- **Get Chat Queue** - 获取聊天队列
- **Take Next Chat** - 接取下一个聊天
- **Assign Chat** - 分配聊天

### 12. Inventory
- **Get Inventory History** - 获取库存历史
- **Get All Inventory** - 获取所有库存
- **Get Inventory Details** - 获取库存详情

## 使用说明

### 1. 认证设置
- 所有需要认证的请求都会自动使用`{{auth_token}}`变量
- 确保在环境变量中设置了正确的认证令牌

### 2. 参数说明
- 查询参数已预设常用值，可以根据需要修改
- 请求体已包含示例数据，可以根据实际需求调整

### 3. 测试流程建议
1. 首先测试公开端点（无需认证）
2. 获取认证令牌并设置环境变量
3. 测试需要认证的端点
4. 测试管理员端点（需要管理员权限）

### 4. 常见问题

#### 401 Unauthorized
- 检查认证令牌是否正确设置
- 确认令牌是否有效且未过期

#### 403 Forbidden
- 确认用户是否有相应的权限
- 管理员端点需要管理员权限

#### 404 Not Found
- 检查URL路径是否正确
- 确认资源ID是否存在

#### 422 Validation Error
- 检查请求体数据格式
- 确认必填字段是否已填写

## 响应格式

### 成功响应
```json
{
  "status": "success",
  "data": {...},
  "message": "操作成功"
}
```

### 错误响应
```json
{
  "status": "error",
  "message": "错误描述",
  "error": "详细错误信息"
}
```

### 验证错误响应
```json
{
  "status": "error",
  "message": "验证失败",
  "errors": {
    "field_name": ["错误信息"]
  }
}
```

## 注意事项

1. 确保Laravel应用正在运行（`php artisan serve`）
2. 数据库已正确配置并包含测试数据
3. 所有API端点都遵循RESTful规范
4. 建议按模块顺序进行测试
5. 管理员功能需要管理员用户权限

## 扩展功能

如需添加新的API端点：
1. 在相应的模块文件夹中添加新请求
2. 更新环境变量（如需要）
3. 添加相应的测试用例

## 技术支持

如有问题，请参考：
- Laravel Sanctum文档
- Postman官方文档
- 项目API文档（API_DOCUMENTATION.md）
