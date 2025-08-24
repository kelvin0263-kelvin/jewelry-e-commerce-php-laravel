# Jewelry E-commerce API Documentation

## Overview

This API provides comprehensive RESTful endpoints for the Admin and Support modules of the jewelry e-commerce application. The API uses Laravel Sanctum for authentication and supports both session-based and token-based authentication.

## Authentication

### Laravel Sanctum

The API uses Laravel Sanctum for authentication. Users can authenticate using:

1. **Session Authentication** (for same-domain requests)
2. **Token Authentication** (for API access)

### Getting an API Token

To get an API token, authenticate a user and create a token:

```php
$user = Auth::user();
$token = $user->createToken('api-token')->plainTextToken;
```

### Using API Tokens

Include the token in the Authorization header:

```
Authorization: Bearer {your-token-here}
```

## API Endpoints

### User Endpoints

#### Get Current User
- **GET** `/api/user`
- **Middleware**: `auth:sanctum`
- **Description**: Get the currently authenticated user's information

### Admin Module Endpoints

All admin endpoints require `auth:sanctum` and `admin` middleware.

#### Dashboard

##### Get Dashboard Data
- **GET** `/api/admin/dashboard`
- **Description**: Get dashboard overview data including metrics and charts

##### Get Dashboard Statistics
- **GET** `/api/admin/dashboard/stats`
- **Description**: Get detailed dashboard statistics
- **Response**:
```json
{
  "status": "success",
  "data": {
    "metrics": {
      "total_revenue": 15000.00,
      "total_sales": 150,
      "total_customers": 75,
      "new_customers_this_month": 12
    },
    "recent_orders": [...],
    "revenue_trend": [...],
    "order_status_stats": {...}
  }
}
```

#### Customer Management

##### List All Customers
- **GET** `/api/admin/customers`
- **Parameters**:
  - `page` (optional): Page number for pagination
  - `per_page` (optional): Number of items per page
- **Description**: Get paginated list of all customers

##### Get Customer Details
- **GET** `/api/admin/customers/{customer}`
- **Description**: Get detailed information about a specific customer

##### Update Customer
- **PUT** `/api/admin/customers/{customer}`
- **Body**:
```json
{
  "name": "John Doe",
  "email": "john@example.com",
  "is_admin": false
}
```

##### Delete Customer
- **DELETE** `/api/admin/customers/{customer}`
- **Description**: Delete a customer account

##### Get Customer Orders
- **GET** `/api/admin/customers/{customer}/orders`
- **Description**: Get all orders for a specific customer

##### Block Customer
- **POST** `/api/admin/customers/{customer}/block`
- **Description**: Block a customer account

##### Unblock Customer
- **POST** `/api/admin/customers/{customer}/unblock`
- **Description**: Unblock a customer account

#### Reports

##### Customer Segmentation
- **GET** `/api/admin/reports/customer-segments`
- **Description**: Get customer segmentation data

##### Sales Report
- **GET** `/api/admin/reports/sales`
- **Parameters**:
  - `start_date` (optional): Start date for the report
  - `end_date` (optional): End date for the report

##### Product Performance
- **GET** `/api/admin/reports/products`
- **Parameters**:
  - `start_date` (optional): Start date for the report
  - `end_date` (optional): End date for the report

##### Order Analytics
- **GET** `/api/admin/reports/orders`
- **Parameters**:
  - `start_date` (optional): Start date for the report
  - `end_date` (optional): End date for the report

##### Revenue Report
- **GET** `/api/admin/reports/revenue`
- **Parameters**:
  - `start_date` (optional): Start date for the report
  - `end_date` (optional): End date for the report

##### Export Customers
- **GET** `/api/admin/reports/export/customers`
- **Description**: Export customer data

##### Export Sales
- **GET** `/api/admin/reports/export/sales`
- **Parameters**:
  - `start_date` (optional): Start date for the export
  - `end_date` (optional): End date for the export

### Support Module Endpoints

#### User Support Endpoints

All user support endpoints require `auth:sanctum` middleware.

##### List User's Tickets
- **GET** `/api/support/tickets`
- **Parameters**:
  - `status` (optional): Filter by ticket status
  - `priority` (optional): Filter by priority
  - `search` (optional): Search in subject and content
  - `per_page` (optional): Number of items per page

##### Create New Ticket
- **POST** `/api/support/tickets`
- **Body**:
```json
{
  "subject": "Issue with my order",
  "content": "Detailed description of the issue",
  "category": "general",
  "priority": "medium"
}
```
- **Categories**: `general`, `technical`, `billing`, `complaint`, `feature_request`
- **Priorities**: `low`, `medium`, `high`, `urgent`

##### Get Ticket Details
- **GET** `/api/support/tickets/{ticket}`
- **Description**: Get detailed information about a specific ticket

##### Update Ticket
- **PUT** `/api/support/tickets/{ticket}`
- **Body**:
```json
{
  "subject": "Updated subject",
  "priority": "high"
}
```

##### Reply to Ticket
- **POST** `/api/support/tickets/{ticket}/reply`
- **Body**:
```json
{
  "content": "My reply to the ticket"
}
```

##### Close Ticket
- **POST** `/api/support/tickets/{ticket}/close`
- **Description**: Close a ticket

##### Get FAQ
- **GET** `/api/support/faq`
- **Description**: Get frequently asked questions (public endpoint)

##### Get Support Categories
- **GET** `/api/support/categories`
- **Description**: Get available support categories (public endpoint)

#### Chat Support

##### Get User Conversations
- **GET** `/api/support/chat/conversations`
- **Description**: Get user's chat conversations

##### Create New Conversation
- **POST** `/api/support/chat/conversations`
- **Body**:
```json
{
  "subject": "Need help with my order",
  "initial_message": "Hello, I need help with..."
}
```

##### Get Conversation Details
- **GET** `/api/support/chat/conversations/{conversation}`

##### Get Conversation Messages
- **GET** `/api/support/chat/conversations/{conversation}/messages`

##### Send Message
- **POST** `/api/support/chat/conversations/{conversation}/messages`
- **Body**:
```json
{
  "content": "My message content"
}
```

#### Admin Support Endpoints

All admin support endpoints require `auth:sanctum` and `admin` middleware.

##### List All Tickets (Admin)
- **GET** `/api/admin/support/tickets`
- **Parameters**: Same as user tickets endpoint

##### Get Ticket Details (Admin)
- **GET** `/api/admin/support/tickets/{ticket}`

##### Update Ticket (Admin)
- **PUT** `/api/admin/support/tickets/{ticket}`
- **Body**:
```json
{
  "status": "in_progress",
  "priority": "high",
  "assigned_to": 1
}
```

##### Assign Ticket
- **POST** `/api/admin/support/tickets/{ticket}/assign`
- **Body**:
```json
{
  "agent_id": 1
}
```

##### Reply to Ticket (Admin)
- **POST** `/api/admin/support/tickets/{ticket}/reply`
- **Body**:
```json
{
  "content": "Admin response to the ticket"
}
```

##### Close Ticket (Admin)
- **POST** `/api/admin/support/tickets/{ticket}/close`

##### Get Ticket History
- **GET** `/api/admin/support/tickets/{ticket}/history`

#### Admin Chat Management

##### Get All Conversations (Admin)
- **GET** `/api/admin/support/chat/conversations`

##### Get Conversation Details (Admin)
- **GET** `/api/admin/support/chat/conversations/{conversation}`

##### Get Conversation Messages (Admin)
- **GET** `/api/admin/support/chat/conversations/{conversation}/messages`

##### Send Message (Admin)
- **POST** `/api/admin/support/chat/conversations/{conversation}/messages`

##### Transfer Conversation
- **POST** `/api/admin/support/chat/conversations/{conversation}/transfer`
- **Body**:
```json
{
  "agent_id": 2
}
```

#### Chat Queue Management

##### Get Chat Queue
- **GET** `/api/admin/support/queue`

##### Take Next Chat
- **POST** `/api/admin/support/queue/take`

##### Assign Chat
- **POST** `/api/admin/support/queue/{queue}/assign`
- **Body**:
```json
{
  "agent_id": 1
}
```

## Response Format

All API responses follow a consistent format:

### Success Response
```json
{
  "status": "success",
  "data": {...},
  "message": "Operation completed successfully"
}
```

### Error Response
```json
{
  "status": "error",
  "message": "Error description",
  "error": "Detailed error information"
}
```

### Validation Error Response
```json
{
  "status": "error",
  "message": "Validation failed",
  "errors": {
    "field_name": ["Error message"]
  }
}
```

## Status Codes

- `200` - Success
- `201` - Created
- `400` - Bad Request
- `401` - Unauthorized
- `403` - Forbidden
- `404` - Not Found
- `422` - Validation Error
- `500` - Internal Server Error

## Error Handling

All endpoints include comprehensive error handling with appropriate HTTP status codes and descriptive error messages. The API will return detailed error information in development environments and sanitized messages in production.

## Rate Limiting

Consider implementing rate limiting for your API endpoints to prevent abuse. Laravel provides built-in rate limiting middleware that can be applied to routes.

## Testing

Use the provided `test_api.php` script to test basic API functionality. For comprehensive testing, consider using tools like Postman, Insomnia, or Laravel's built-in testing features.

## Security Considerations

1. Always use HTTPS in production
2. Implement proper rate limiting
3. Validate and sanitize all input data
4. Use proper authorization checks
5. Keep API tokens secure
6. Implement logging for security monitoring
7. Regular security audits and updates

## Migration Guide

If you're migrating from an existing API, note the following changes:

1. Authentication now uses Laravel Sanctum
2. All responses follow the new consistent format
3. Proper RESTful conventions are followed
4. Enhanced error handling and validation
5. Comprehensive documentation and examples
