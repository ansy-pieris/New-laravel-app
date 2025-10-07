# Cart API Documentation for Mobile App Integration

## Base URL
```
http://13.204.86.61/api/apparel/
```

## Authentication
All cart endpoints require authentication using Laravel Sanctum. Include the bearer token in the Authorization header:
```
Authorization: Bearer YOUR_TOKEN_HERE
```

## Cart API Endpoints

### 1. GET /cart - Get User's Cart
**Description:** Retrieve all items in the user's cart with detailed product information.

**Request:**
```http
GET /api/apparel/cart
Authorization: Bearer {token}
```

**Response:**
```json
{
  "success": true,
  "data": {
    "items": [
      {
        "id": "cart_item_id",
        "quantity": 2,
        "product": {
          "id": 1,
          "name": "Product Name",
          "slug": "product-slug",
          "price": 29.99,
          "formatted_price": "Rs. 29.99",
          "image": "http://13.204.86.61/storage/products/image.jpg",
          "stock": 10,
          "category": {
            "id": 1,
            "name": "Category Name"
          }
        },
        "subtotal": 59.98,
        "formatted_subtotal": "Rs. 59.98"
      }
    ],
    "summary": {
      "total_items": 2,
      "total_price": 59.98,
      "formatted_total": "Rs. 59.98",
      "is_empty": false
    }
  },
  "message": "Cart retrieved successfully"
}
```

### 2. POST /cart/add - Add Item to Cart
**Description:** Add a product to the user's cart or increase quantity if it already exists.

**Request:**
```http
POST /api/apparel/cart/add
Authorization: Bearer {token}
Content-Type: application/json

{
  "product_id": 1,
  "quantity": 2
}
```

**Response:**
```json
{
  "success": true,
  "data": {
    "cart_id": "unique_cart_item_id",
    "user_id": 1,
    "product_id": 1,
    "quantity": 2,
    "product": {
      "id": 1,
      "name": "Product Name",
      "price": 29.99
    }
  },
  "message": "Item added to cart successfully"
}
```

### 3. PUT /cart/update - Update Cart Item Quantity
**Description:** Update the quantity of a specific cart item.

**Request:**
```http
PUT /api/apparel/cart/update
Authorization: Bearer {token}
Content-Type: application/json

{
  "cart_item_id": "cart_item_id",
  "quantity": 3
}
```

**Response:**
```json
{
  "success": true,
  "data": {
    "cart_id": "cart_item_id",
    "quantity": 3,
    "product": {
      "id": 1,
      "name": "Product Name"
    }
  },
  "message": "Cart item updated successfully"
}
```

### 4. DELETE /cart/remove - Remove Item from Cart
**Description:** Remove a specific item from the user's cart.

**Request:**
```http
DELETE /api/apparel/cart/remove
Authorization: Bearer {token}
Content-Type: application/json

{
  "cart_item_id": "cart_item_id"
}
```

**Response:**
```json
{
  "success": true,
  "message": "Item removed from cart successfully"
}
```

### 5. DELETE /cart/clear - Clear Entire Cart
**Description:** Remove all items from the user's cart.

**Request:**
```http
DELETE /api/apparel/cart/clear
Authorization: Bearer {token}
```

**Response:**
```json
{
  "success": true,
  "message": "Cart cleared. 3 items removed."
}
```

### 6. GET /cart/count - Get Cart Item Count
**Description:** Get the total number of items in the user's cart.

**Request:**
```http
GET /api/apparel/cart/count
Authorization: Bearer {token}
```

**Response:**
```json
{
  "success": true,
  "data": {
    "count": 5
  },
  "message": "Cart count retrieved successfully"
}
```

## Flutter Integration Example

### CartProvider Implementation
```dart
class CartProvider extends ChangeNotifier {
  List<CartItem> _items = [];
  double _totalPrice = 0.0;
  
  // Get cart from API
  Future<void> loadCart() async {
    final response = await http.get(
      Uri.parse('${ApiConfig.baseUrl}/cart'),
      headers: {
        'Authorization': 'Bearer ${AuthService.token}',
        'Accept': 'application/json',
      },
    );
    
    if (response.statusCode == 200) {
      final data = json.decode(response.body);
      // Parse and update local cart state
    }
  }
  
  // Add item to cart
  Future<void> addToCart(int productId, int quantity) async {
    final response = await http.post(
      Uri.parse('${ApiConfig.baseUrl}/cart/add'),
      headers: {
        'Authorization': 'Bearer ${AuthService.token}',
        'Content-Type': 'application/json',
      },
      body: json.encode({
        'product_id': productId,
        'quantity': quantity,
      }),
    );
    
    if (response.statusCode == 201) {
      await loadCart(); // Refresh cart
      notifyListeners();
    }
  }
  
  // Update item quantity
  Future<void> updateQuantity(String cartItemId, int quantity) async {
    final response = await http.put(
      Uri.parse('${ApiConfig.baseUrl}/cart/update'),
      headers: {
        'Authorization': 'Bearer ${AuthService.token}',
        'Content-Type': 'application/json',
      },
      body: json.encode({
        'cart_item_id': cartItemId,
        'quantity': quantity,
      }),
    );
    
    if (response.statusCode == 200) {
      await loadCart();
      notifyListeners();
    }
  }
  
  // Remove item from cart
  Future<void> removeFromCart(String cartItemId) async {
    final response = await http.delete(
      Uri.parse('${ApiConfig.baseUrl}/cart/remove'),
      headers: {
        'Authorization': 'Bearer ${AuthService.token}',
        'Content-Type': 'application/json',
      },
      body: json.encode({
        'cart_item_id': cartItemId,
      }),
    );
    
    if (response.statusCode == 200) {
      await loadCart();
      notifyListeners();
    }
  }
}
```

## Error Handling

All endpoints return appropriate HTTP status codes:
- 200: Success
- 201: Created (for add to cart)
- 400: Bad Request (validation errors)
- 401: Unauthorized (invalid/missing token)
- 404: Not Found (cart item not found)
- 422: Unprocessable Entity (validation errors)

## Notes for Mobile Development

1. **Authentication Required:** All cart endpoints require a valid Sanctum token
2. **Cart Persistence:** Cart items are stored server-side and persist across sessions
3. **Offline Support:** Consider implementing local cart storage that syncs when online
4. **Cart Merging:** When user logs in, merge any local cart with server cart
5. **Real-time Updates:** Cart changes are immediate and don't require polling
6. **Image URLs:** Product images use full URLs for easy display in mobile apps