# Product API Documentation

## 概述

Product API 为珠宝电商系统提供了完整的产品管理功能，包括公开的产品浏览和需要认证的产品管理操作。

## 认证

API 使用 Laravel Sanctum 进行认证：
- 公开端点：无需认证
- 受保护端点：需要 `Authorization: Bearer {token}` 头部
- 管理员端点：需要认证 + 管理员权限

## 端点列表

### 公开端点 (无需认证)

#### 1. 获取产品列表
```
GET /api/products
```

**查询参数：**
- `visible` (boolean): 过滤可见性
- `published` (boolean): 过滤发布状态
- `category` (string): 按类别过滤
- `search` (string): 搜索关键词
- `min_price` (number): 最低价格
- `max_price` (number): 最高价格
- `status` (string): 按状态过滤
- `order_by` (string): 排序字段 (默认: created_at)
- `order_direction` (string): 排序方向 (asc/desc, 默认: desc)
- `per_page` (number): 每页数量 (默认: 15)

**响应示例：**
```json
{
  "data": [...],
  "pagination": {
    "current_page": 1,
    "last_page": 5,
    "per_page": 15,
    "total": 75,
    "from": 1,
    "to": 15
  },
  "filters": {
    "categories": ["ring", "necklace", "earrings"],
    "statuses": ["published", "draft", "pending"]
  }
}
```

#### 2. 搜索产品
```
GET /api/products/search?q={search_term}
```

**查询参数：**
- `q` (string): 搜索关键词

#### 3. 获取产品详情
```
GET /api/products/{id}
```

#### 4. 获取产品统计
```
GET /api/products/stats/overview
```

#### 5. 按库存获取产品
```
GET /api/products/inventory/{inventoryId}
```

### 受保护端点 (需要认证)

#### 1. 创建产品
```
POST /api/products
Authorization: Bearer {token}
```

**请求体：**
```json
{
  "inventory_variation_id": 1,
  "name": "产品名称",
  "description": "产品描述",
  "marketing_description": "营销描述",
  "selling_price": 99.99,
  "discount_price": 79.99,
  "category": "ring",
  "status": "draft",
  "is_visible": false,
  "features": ["特征1", "特征2"],
  "gallery_images": ["image1.jpg", "image2.jpg"],
  "main_image": "main.jpg"
}
```

#### 2. 更新产品
```
PUT /api/products/{id}
Authorization: Bearer {token}
```

#### 3. 删除产品
```
DELETE /api/products/{id}
Authorization: Bearer {token}
```

### 管理员端点 (需要管理员权限)

#### 1. 管理员产品列表
```
GET /api/admin/products
Authorization: Bearer {admin_token}
```

#### 2. 管理员产品搜索
```
GET /api/admin/products/search?q={search_term}
Authorization: Bearer {admin_token}
```

#### 3. 管理员产品详情
```
GET /api/admin/products/{id}
Authorization: Bearer {admin_token}
```

#### 4. 管理员产品统计
```
GET /api/admin/products/stats/overview
Authorization: Bearer {admin_token}
```

#### 5. 管理员创建产品
```
POST /api/admin/products
Authorization: Bearer {admin_token}
```

#### 6. 管理员更新产品
```
PUT /api/admin/products/{id}
Authorization: Bearer {admin_token}
```

#### 7. 管理员删除产品
```
DELETE /api/admin/products/{id}
Authorization: Bearer {admin_token}
```

#### 8. 管理员按库存获取产品
```
GET /api/admin/products/inventory/{inventoryId}
Authorization: Bearer {admin_token}
```

## 响应格式

### 成功响应
```json
{
  "data": {
    "id": 1,
    "name": "产品名称",
    "sku": "PROD-ABC12345",
    "price": 99.99,
    "discount_price": 79.99,
    "category": "ring",
    "status": "published",
    "is_visible": true,
    "features": ["特征1", "特征2"],
    "gallery_images": ["image1.jpg", "image2.jpg"],
    "main_image": "main.jpg",
    "created_at": "2024-01-01T00:00:00.000000Z",
    "updated_at": "2024-01-01T00:00:00.000000Z"
  }
}
```

### 错误响应
```json
{
  "error": "错误信息",
  "messages": {
    "field_name": ["验证错误信息"]
  }
}
```

## 状态码

- `200` - 成功
- `201` - 创建成功
- `400` - 请求错误
- `401` - 未认证
- `403` - 无权限
- `404` - 资源不存在
- `422` - 验证失败
- `500` - 服务器错误

## 使用示例

### JavaScript (Fetch API)
```javascript
// 获取产品列表
const response = await fetch('/api/products?category=ring&per_page=10');
const data = await response.json();

// 创建产品 (需要认证)
const response = await fetch('/api/products', {
  method: 'POST',
  headers: {
    'Content-Type': 'application/json',
    'Authorization': 'Bearer your-token-here'
  },
  body: JSON.stringify({
    inventory_variation_id: 1,
    name: '新产品',
    selling_price: 99.99,
    category: 'ring',
    status: 'draft'
  })
});
```

### PHP (cURL)
```php
// 获取产品列表
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'http://localhost:8000/api/products');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Accept: application/json'
]);
$response = curl_exec($ch);
curl_close($ch);
```

## 测试

运行测试脚本：
```bash
php test_product_api.php
```

## 注意事项

1. 公开端点只返回已发布且可见的产品
2. 受保护端点需要有效的认证令牌
3. 管理员端点需要管理员权限
4. 所有价格字段使用数字格式
5. 图片路径应该是相对路径
6. 产品状态包括：draft, pending, published, issued
