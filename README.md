# Sales ERP System

A comprehensive Sales ERP system built with Laravel 11 and RESTful API, inspired by FrontAccounting.

## Features

### Core Modules
- **Sales Management**: Complete sales order processing with customer management
- **Purchase Management**: Purchase order processing with supplier management  
- **Items & Inventory**: Item catalog with multi-warehouse inventory tracking
- **Manufacturing**: Production planning and bill of materials
- **Fixed Assets**: Asset tracking and depreciation management
- **Banking & General Ledger**: Complete accounting system with chart of accounts
- **Human Resources**: Employee management and payroll
- **Setup**: System configuration and company settings

### Key Features
- Multi-company support
- RESTful API with Laravel Sanctum authentication
- Role-based permissions
- Real-time inventory tracking
- Financial reporting
- Tax management
- Multi-currency support
- Audit trails

## Installation

1. Clone the repository
```bash
git clone <repository-url>
cd sales-erp
```

2. Install dependencies
```bash
composer install
```

3. Copy environment file
```bash
cp .env.example .env
```

4. Generate application key
```bash
php artisan key:generate
```

5. Configure database in `.env` file
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=sales_erp
DB_USERNAME=root
DB_PASSWORD=
```

6. Run migrations and seeders
```bash
php artisan migrate
php artisan db:seed
```

7. Link storage
```bash
php artisan storage:link
```

8. Start the development server
```bash
php artisan serve
```

## API Documentation

### Authentication

#### Register
```http
POST /api/register
Content-Type: application/json

{
    "name": "John Doe",
    "email": "john@example.com",
    "password": "password",
    "password_confirmation": "password",
    "company_name": "ACME Corporation",
    "company_email": "info@acme.com"
}
```

#### Login
```http
POST /api/login
Content-Type: application/json

{
    "email": "john@example.com",
    "password": "password"
}
```

#### Logout
```http
POST /api/logout
Authorization: Bearer {token}
```

### Sales Orders

#### List Sales Orders
```http
GET /api/sales-orders
Authorization: Bearer {token}
```

#### Create Sales Order
```http
POST /api/sales-orders
Authorization: Bearer {token}
Content-Type: application/json

{
    "customer_id": 1,
    "order_date": "2024-01-15",
    "delivery_date": "2024-01-20",
    "payment_terms": "Net 30",
    "items": [
        {
            "item_id": 1,
            "warehouse_id": 1,
            "quantity": 10,
            "unit_price": 100.00,
            "discount_percentage": 10,
            "tax_percentage": 15
        }
    ]
}
```

#### Get Sales Order
```http
GET /api/sales-orders/{id}
Authorization: Bearer {token}
```

#### Update Sales Order
```http
PUT /api/sales-orders/{id}
Authorization: Bearer {token}
Content-Type: application/json
```

#### Confirm Sales Order
```http
POST /api/sales-orders/{id}/confirm
Authorization: Bearer {token}
```

#### Cancel Sales Order
```http
POST /api/sales-orders/{id}/cancel
Authorization: Bearer {token}
```

### Purchase Orders

#### List Purchase Orders
```http
GET /api/purchase-orders
Authorization: Bearer {token}
```

#### Create Purchase Order
```http
POST /api/purchase-orders
Authorization: Bearer {token}
Content-Type: application/json

{
    "supplier_id": 1,
    "order_date": "2024-01-15",
    "expected_date": "2024-01-20",
    "items": [
        {
            "item_id": 1,
            "warehouse_id": 1,
            "quantity": 50,
            "unit_price": 75.00
        }
    ]
}
```

### Items & Inventory

#### List Items
```http
GET /api/items
Authorization: Bearer {token}
```

#### Create Item
```http
POST /api/items
Authorization: Bearer {token}
Content-Type: application/json

{
    "code": "ITEM001",
    "name": "Sample Product",
    "description": "Product description",
    "category": "Electronics",
    "unit_of_measure": "pcs",
    "purchase_price": 75.00,
    "sale_price": 100.00,
    "reorder_level": 10,
    "reorder_quantity": 50
}
```

#### Current Stock
```http
GET /api/inventory/current
Authorization: Bearer {token}
```

#### Inventory Transactions
```http
GET /api/inventory/transactions
Authorization: Bearer {token}
```

### Customers

#### List Customers
```http
GET /api/customers
Authorization: Bearer {token}
```

#### Create Customer
```http
POST /api/customers
Authorization: Bearer {token}
Content-Type: application/json

{
    "name": "Customer Name",
    "email": "customer@example.com",
    "phone": "+1234567890",
    "address": "Customer Address",
    "customer_type": "individual",
    "credit_limit": 10000.00,
    "payment_terms": "Net 30"
}
```

### Suppliers

#### List Suppliers
```http
GET /api/suppliers
Authorization: Bearer {token}
```

#### Create Supplier
```http
POST /api/suppliers
Authorization: Bearer {token}
Content-Type: application/json

{
    "name": "Supplier Name",
    "email": "supplier@example.com",
    "phone": "+1234567890",
    "address": "Supplier Address",
    "supplier_type": "company",
    "credit_limit": 50000.00,
    "payment_terms": "Net 45"
}
```

## Database Schema

The system includes the following main tables:

- **companies**: Multi-company support
- **users**: User management with roles
- **customers**: Customer information
- **suppliers**: Supplier information  
- **items**: Product catalog
- **warehouses**: Warehouse management
- **sales_orders**: Sales order headers
- **sales_order_items**: Sales order line items
- **purchase_orders**: Purchase order headers
- **purchase_order_items**: Purchase order line items
- **inventory_transactions**: Stock movement tracking
- **accounts**: Chart of accounts
- **journal_entries**: Accounting transactions
- **journal_entry_lines**: Transaction line items

## Security

- Laravel Sanctum for API authentication
- Role-based permissions using Spatie Laravel Permission
- Input validation and sanitization
- SQL injection protection
- CSRF protection

## Testing

Run the test suite:
```bash
php artisan test
```

## Contributing

1. Fork the repository
2. Create a feature branch
3. Commit your changes
4. Push to the branch
5. Create a Pull Request

## License

This project is licensed under the MIT License.
