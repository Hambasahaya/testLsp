

echo "Testing API Token Generation..."

# Test login
LOGIN_RESPONSE=$(curl -s -X POST http://localhost:8000/api/auth/login \
  -H "Content-Type: application/json" \
  -d '{
    "email": "admin@example.com",
    "password": "password"
  }')

echo "Login Response:"
echo $LOGIN_RESPONSE | jq .

# Extract token
TOKEN=$(echo $LOGIN_RESPONSE | jq -r '.token')

if [ "$TOKEN" != "null" ] && [ ! -z "$TOKEN" ]; then
    echo ""
    echo "✓ Token obtained: ${TOKEN:0:20}..."
    echo ""
    
    # Test product list with token
    echo "Testing product list with token..."
    curl -s -X GET http://localhost:8000/api/products \
      -H "Authorization: Bearer $TOKEN" \
      -H "X-Requested-With: XMLHttpRequest" | jq .
    
    echo ""
    echo "✓ API authentication working!"
else
    echo "✗ Failed to obtain token"
fi
