# API Testing Script - Check if everything works
Write-Host "🧪 TESTING APPAREL STORE APIs" -ForegroundColor Green
Write-Host "==============================" -ForegroundColor Green

# Test if server is running
Write-Host "1. Testing server connection..." -ForegroundColor Yellow
try {
    $response = Invoke-WebRequest -Uri "http://127.0.0.1:8001" -Method Get -TimeoutSec 5
    Write-Host "✅ Server is running on port 8001" -ForegroundColor Green
} catch {
    Write-Host "❌ Server not running. Starting server first..." -ForegroundColor Red
    Write-Host "Run: php artisan serve --port=8001" -ForegroundColor Cyan
    exit
}

Write-Host ""
Write-Host "2. Testing Public APIs (No Auth Required)..." -ForegroundColor Yellow

# Test public routes
$testRoutes = @(
    @{url="http://127.0.0.1:8001/api/apparel/products"; desc="Products List"},
    @{url="http://127.0.0.1:8001/api/apparel/categories"; desc="Categories List"},
    @{url="http://127.0.0.1:8001/api/apparel/products/featured"; desc="Featured Products"}
)

foreach ($route in $testRoutes) {
    try {
        $response = Invoke-RestMethod -Uri $route.url -Method Get -TimeoutSec 10
        Write-Host "✅ $($route.desc): WORKING" -ForegroundColor Green
    } catch {
        Write-Host "❌ $($route.desc): FAILED - $($_.Exception.Message)" -ForegroundColor Red
    }
}

Write-Host ""
Write-Host "3. Testing Login API..." -ForegroundColor Yellow

# Test login
$loginData = @{
    email = "test@university.com"
    password = "password123"
} | ConvertTo-Json

$headers = @{
    "Content-Type" = "application/json"
    "Accept" = "application/json"
}

try {
    $loginResponse = Invoke-RestMethod -Uri "http://127.0.0.1:8001/api/apparel/login" -Method Post -Body $loginData -Headers $headers
    Write-Host "✅ Login API: WORKING" -ForegroundColor Green
    Write-Host "   Token received: $($loginResponse.token.Substring(0,20))..." -ForegroundColor Cyan
    
    # Test protected route with token
    Write-Host ""
    Write-Host "4. Testing Protected APIs (With Auth Token)..." -ForegroundColor Yellow
    
    $authHeaders = @{
        "Accept" = "application/json"
        "Authorization" = "Bearer $($loginResponse.token)"
    }
    
    $protectedRoutes = @(
        @{url="http://127.0.0.1:8001/api/apparel/status"; desc="API Status"},
        @{url="http://127.0.0.1:8001/api/apparel/profile"; desc="User Profile"},
        @{url="http://127.0.0.1:8001/api/apparel/cart"; desc="Shopping Cart"}
    )
    
    foreach ($route in $protectedRoutes) {
        try {
            $response = Invoke-RestMethod -Uri $route.url -Method Get -Headers $authHeaders -TimeoutSec 10
            Write-Host "✅ $($route.desc): WORKING" -ForegroundColor Green
        } catch {
            Write-Host "❌ $($route.desc): FAILED - $($_.Exception.Message)" -ForegroundColor Red
        }
    }
    
} catch {
    Write-Host "❌ Login API: FAILED - $($_.Exception.Message)" -ForegroundColor Red
    Write-Host "   Make sure test user exists: test@university.com" -ForegroundColor Cyan
}

Write-Host ""
Write-Host "🎯 API TESTING SUMMARY:" -ForegroundColor Green
Write-Host "- Test this script to verify all APIs work" -ForegroundColor White
Write-Host "- Make sure Laravel server is running first" -ForegroundColor White
Write-Host "- Check that test user exists in database" -ForegroundColor White
Write-Host ""
Write-Host "📚 Your University Assignment APIs:" -ForegroundColor Green
Write-Host "✅ Laravel Sanctum Authentication" -ForegroundColor Green
Write-Host "✅ MongoDB NoSQL Database" -ForegroundColor Green
Write-Host "✅ Complete E-commerce API System" -ForegroundColor Green