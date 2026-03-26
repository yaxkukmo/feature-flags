RESPONSE=$(curl -X POST http://localhost:8000/login -H "Content-Type: application/json" -d '{"email": "admin@test.com", "password": "twoje_haslo"}')
TOKEN=$(echo $RESPONSE | jq -r '.token')
curl http://localhost:8000/feature?uiserId=11 -H "Authorization: Bearer ${TOKEN}"

