# HOW TO TEST IF EVERYTHING WORKS
# ================================

## 1. START THE SERVER
php artisan serve --port=8001

## 2. RUN THE TEST SCRIPT
powershell -ExecutionPolicy Bypass -File test_all_apis.ps1

## 3. MANUAL TESTING CHECKLIST

### ✅ Things to Verify:

#### Controllers Created:
- [ ] UniversityController.php (User auth & management)
- [ ] ProductApiController.php (Product CRUD)
- [ ] CartApiController.php (Shopping cart)
- [ ] OrderApiController.php (Order processing)
- [ ] CategoryApiController.php (Category management)
- [ ] MongoAdvancedController.php (MongoDB demos)

#### Database:
- [ ] Test user exists: test@university.com / password123
- [ ] Products table has data
- [ ] Categories table has data
- [ ] MongoDB Atlas connection working

#### API Routes Working:
- [ ] POST /api/apparel/login (Public)
- [ ] GET /api/apparel/products (Public)
- [ ] GET /api/apparel/categories (Public)
- [ ] GET /api/apparel/status (Protected)
- [ ] GET /api/apparel/cart (Protected)
- [ ] POST /api/apparel/cart/add (Protected)

#### University Assignment Proof:
- [ ] Laravel Sanctum authentication working
- [ ] MongoDB Atlas configured
- [ ] API returns tokens
- [ ] Protected routes require authentication

## 4. QUICK VERIFICATION COMMANDS

# Check if controllers exist:
dir app\Http\Controllers\Api\

# Check routes:
php artisan route:list --path=apparel

# Check database connection:
php artisan tinker
# Then run: DB::connection()->getPdo();

# Test MongoDB:
# In tinker: DB::connection('mongodb')->collection('test')->insert(['test' => 'data']);

## 5. COMMON ISSUES & SOLUTIONS

❌ "Class not found" error:
   Solution: Run: composer dump-autoload

❌ "Route not found" error:
   Solution: Check routes/api.php file exists and syntax is correct

❌ "Database connection failed":
   Solution: Check .env file has correct database settings

❌ "MongoDB connection failed":
   Solution: Check MongoDB Atlas credentials in .env

❌ "Token invalid" error:
   Solution: Make sure test user exists, run create_test_user.php

## 6. SUCCESS INDICATORS

✅ Server starts without errors
✅ Login returns valid token
✅ Protected routes work with token
✅ Public routes work without token
✅ No PHP errors in browser/terminal
✅ API responses are properly formatted JSON

## 7. FOR UNIVERSITY SUBMISSION

Your assignment demonstrates:
✅ Laravel Sanctum API authentication
✅ NoSQL database (MongoDB Atlas) integration
✅ Professional API structure
✅ Complete e-commerce functionality
✅ Proper error handling
✅ Clean, documented code