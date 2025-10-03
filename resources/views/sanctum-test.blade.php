<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sanctum Authentication Test</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
</head>
<body>
    <div id="app">
        <h1>Laravel Sanctum Authentication Test</h1>
        
        <div id="status" style="padding: 10px; margin: 10px 0; border-radius: 5px;"></div>
        
        <div style="margin: 20px 0;">
            <h2>1. API Login Test</h2>
            <input type="email" id="email" placeholder="Email" value="ansypieris1@gmail.com">
            <input type="password" id="password" placeholder="Password" value="12345678">
            <button onclick="apiLogin()">Login via API</button>
        </div>
        
        <div style="margin: 20px 0;">
            <h2>2. Token Storage</h2>
            <div id="tokenDisplay" style="background: #f0f0f0; padding: 10px; word-break: break-all;"></div>
        </div>
        
        <div style="margin: 20px 0;">
            <h2>3. Protected API Calls</h2>
            <button onclick="testProfile()">Test Profile API (with token)</button>
            <button onclick="testCart()">Test Cart API (with token)</button>
            <button onclick="testStateful()">Test Stateful API (no token)</button>
        </div>
        
        <div style="margin: 20px 0;">
            <h2>4. Results</h2>
            <div id="results" style="background: #f9f9f9; padding: 10px; max-height: 400px; overflow-y: auto;"></div>
        </div>
    </div>

    <script>
        // Configure Axios
        axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
        
        let authToken = localStorage.getItem('sanctum_token');
        if (authToken) {
            axios.defaults.headers.common['Authorization'] = `Bearer ${authToken}`;
            document.getElementById('tokenDisplay').innerHTML = `<strong>Stored Token:</strong> ${authToken}`;
        }
        
        function updateStatus(message, type = 'info') {
            const statusDiv = document.getElementById('status');
            const colors = {
                success: '#d4edda',
                error: '#f8d7da',
                info: '#d1ecf1'
            };
            statusDiv.style.backgroundColor = colors[type];
            statusDiv.innerHTML = message;
        }
        
        function addResult(title, data) {
            const resultsDiv = document.getElementById('results');
            const timestamp = new Date().toLocaleTimeString();
            resultsDiv.innerHTML += `
                <div style="border-bottom: 1px solid #ddd; padding: 10px 0;">
                    <strong>[${timestamp}] ${title}</strong><br>
                    <pre style="background: #fff; padding: 5px; margin: 5px 0;">${JSON.stringify(data, null, 2)}</pre>
                </div>
            `;
            resultsDiv.scrollTop = resultsDiv.scrollHeight;
        }
        
        async function apiLogin() {
            try {
                updateStatus('Attempting API login...', 'info');
                
                const email = document.getElementById('email').value;
                const password = document.getElementById('password').value;
                
                const response = await axios.post('/api/apparel/login', {
                    email: email,
                    password: password
                });
                
                const token = response.data.token;
                authToken = token;
                
                // Store token
                localStorage.setItem('sanctum_token', token);
                axios.defaults.headers.common['Authorization'] = `Bearer ${token}`;
                
                document.getElementById('tokenDisplay').innerHTML = `<strong>New Token:</strong> ${token}`;
                
                updateStatus('✅ API Login successful! Token stored and configured.', 'success');
                addResult('API Login Success', response.data);
                
            } catch (error) {
                updateStatus(`❌ API Login failed: ${error.response?.data?.message || error.message}`, 'error');
                addResult('API Login Failed', error.response?.data || error.message);
            }
        }
        
        async function testProfile() {
            try {
                updateStatus('Testing profile API with token...', 'info');
                
                const response = await axios.get('/api/apparel/profile');
                
                updateStatus('✅ Profile API successful with token!', 'success');
                addResult('Profile API (with token)', response.data);
                
            } catch (error) {
                updateStatus(`❌ Profile API failed: ${error.response?.data?.message || error.message}`, 'error');
                addResult('Profile API Failed', error.response?.data || error.message);
            }
        }
        
        async function testCart() {
            try {
                updateStatus('Testing cart API with token...', 'info');
                
                const response = await axios.get('/api/apparel/cart');
                
                updateStatus('✅ Cart API successful with token!', 'success');
                addResult('Cart API (with token)', response.data);
                
            } catch (error) {
                updateStatus(`❌ Cart API failed: ${error.response?.data?.message || error.message}`, 'error');
                addResult('Cart API Failed', error.response?.data || error.message);
            }
        }
        
        async function testStateful() {
            try {
                updateStatus('Testing stateful API (no token)...', 'info');
                
                // Remove token temporarily
                const originalAuth = axios.defaults.headers.common['Authorization'];
                delete axios.defaults.headers.common['Authorization'];
                
                const response = await axios.get('/api/apparel/profile');
                
                // Restore token
                axios.defaults.headers.common['Authorization'] = originalAuth;
                
                updateStatus('✅ Stateful API successful without token!', 'success');
                addResult('Stateful API (no token)', response.data);
                
            } catch (error) {
                // Restore token
                if (authToken) {
                    axios.defaults.headers.common['Authorization'] = `Bearer ${authToken}`;
                }
                
                updateStatus(`❌ Stateful API failed: ${error.response?.data?.message || error.message}`, 'error');
                addResult('Stateful API Failed', error.response?.data || error.message);
            }
        }
        
        // Initial setup
        updateStatus('Sanctum Authentication Tester Ready', 'info');
        if (authToken) {
            updateStatus('Found existing token in localStorage', 'info');
        }
    </script>
</body>
</html>