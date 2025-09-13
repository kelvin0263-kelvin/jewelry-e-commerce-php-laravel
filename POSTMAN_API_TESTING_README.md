# 珠宝电商API Postman测试套件

## 📁 文件说明

本测试套件包含以下文件：

### 核心文件
- **`Jewelry_E-commerce_API.postman_collection.json`** - 完整的Postman集合文件
- **`POSTMAN_SETUP_GUIDE.md`** - 详细的Postman设置和使用指南

### 辅助工具
- **`get_auth_token.php`** - 获取API认证令牌的辅助脚本
- **`test_api_endpoints.php`** - API端点快速测试脚本

## 🚀 快速开始

### 1. 获取认证令牌
```bash
php get_auth_token.php
```

### 2. 测试API端点
```bash
php test_api_endpoints.php
```

### 3. 导入Postman集合
1. 打开Postman
2. 点击"Import"
3. 选择`Jewelry_E-commerce_API.postman_collection.json`
4. 设置环境变量（参考POSTMAN_SETUP_GUIDE.md）

## 📊 API端点概览

### 公开端点（无需认证）
- 产品浏览和搜索
- 产品详情和统计
- FAQ和支持分类

### 认证端点（需要用户令牌）
- 用户信息管理
- 工单系统
- 聊天支持
- 产品管理

### 管理员端点（需要管理员权限）
- 仪表板和数据统计
- 客户管理
- 报告和分析
- 支持系统管理
- 聊天队列管理

## 🔧 环境要求

- PHP 8.0+
- Laravel 10+
- MySQL/PostgreSQL
- Composer
- Postman（用于详细测试）

## 📋 测试流程建议

1. **基础测试**
   - 运行`test_api_endpoints.php`验证基本连通性
   - 检查数据库连接和用户数据

2. **功能测试**
   - 使用Postman测试各个模块
   - 验证认证和权限控制
   - 测试CRUD操作

3. **集成测试**
   - 测试完整的业务流程
   - 验证数据一致性
   - 测试错误处理

## 🛠️ 故障排除

### 常见问题

1. **401 Unauthorized**
   - 检查认证令牌是否正确
   - 确认令牌未过期

2. **403 Forbidden**
   - 确认用户权限
   - 管理员端点需要管理员权限

3. **404 Not Found**
   - 检查URL路径
   - 确认资源存在

4. **422 Validation Error**
   - 检查请求数据格式
   - 确认必填字段

### 调试技巧

1. 使用Laravel日志查看详细错误
2. 检查数据库连接和表结构
3. 验证中间件配置
4. 使用Postman控制台查看请求详情

## 📚 相关文档

- [API_DOCUMENTATION.md](./API_DOCUMENTATION.md) - 完整API文档
- [PRODUCT_API_DOCUMENTATION.md](./PRODUCT_API_DOCUMENTATION.md) - 产品API文档
- [Laravel Sanctum文档](https://laravel.com/docs/sanctum)
- [Postman官方文档](https://learning.postman.com/)

## 🤝 贡献

如需添加新的API端点或改进测试套件：

1. 在Postman集合中添加新请求
2. 更新测试脚本
3. 更新文档
4. 提交Pull Request

## 📞 支持

如有问题或建议，请：
1. 查看故障排除部分
2. 检查相关文档
3. 提交Issue或联系开发团队

---

**注意**: 请确保在生产环境中使用前进行充分测试，并遵循安全最佳实践。
