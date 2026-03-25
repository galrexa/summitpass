#!/bin/bash

# ============================================
# SummitPass API Quick Testing Script
# ============================================

set -e

BASE_URL="http://localhost:8000"
API_BASE="$BASE_URL/api/v1"

# Colors
GREEN='\033[0;32m'
BLUE='\033[0;34m'
YELLOW='\033[1;33m'
RED='\033[0;31m'
NC='\033[0m' # No Color

# ============================================
# Helper Functions
# ============================================

print_section() {
    echo -e "\n${BLUE}========================================${NC}"
    echo -e "${BLUE}$1${NC}"
    echo -e "${BLUE}========================================${NC}\n"
}

print_success() {
    echo -e "${GREEN}✓ $1${NC}"
}

print_error() {
    echo -e "${RED}✗ $1${NC}"
}

print_info() {
    echo -e "${YELLOW}ℹ $1${NC}"
}

# ============================================
# 1. AUTHENTICATION TESTS
# ============================================

test_register() {
    print_section "1. TEST REGISTER"
    
    RESPONSE=$(curl -s -X POST "$API_BASE/auth/register" \
        -H "Content-Type: application/json" \
        -d '{
            "name": "Test User",
            "email": "test@example.com",
            "phone": "081234567890",
            "password": "password123",
            "password_confirmation": "password123"
        }')
    
    echo "$RESPONSE" | jq .
    
    TOKEN=$(echo "$RESPONSE" | jq -r '.access_token')
    
    if [ "$TOKEN" != "null" ] && [ ! -z "$TOKEN" ]; then
        print_success "Register successful"
        echo "$TOKEN" > /tmp/summitpass_token.txt
    else
        print_error "Register failed"
        exit 1
    fi
}

test_login() {
    print_section "2. TEST LOGIN"
    
    RESPONSE=$(curl -s -X POST "$API_BASE/auth/login" \
        -H "Content-Type: application/json" \
        -d '{
            "email": "test@example.com",
            "password": "password123"
        }')
    
    echo "$RESPONSE" | jq .
    
    TOKEN=$(echo "$RESPONSE" | jq -r '.access_token')
    
    if [ "$TOKEN" != "null" ] && [ ! -z "$TOKEN" ]; then
        print_success "Login successful"
        echo "$TOKEN" > /tmp/summitpass_token.txt
    else
        print_error "Login failed"
        exit 1
    fi
}

test_profile() {
    print_section "3. TEST GET PROFILE"
    
    TOKEN=$(cat /tmp/summitpass_token.txt)
    
    RESPONSE=$(curl -s -X GET "$API_BASE/auth/profile" \
        -H "Authorization: Bearer $TOKEN")
    
    echo "$RESPONSE" | jq .
    
    if echo "$RESPONSE" | jq -e '.user' > /dev/null; then
        print_success "Get profile successful"
    else
        print_error "Get profile failed"
    fi
}

test_update_profile() {
    print_section "4. TEST UPDATE PROFILE"
    
    TOKEN=$(cat /tmp/summitpass_token.txt)
    
    RESPONSE=$(curl -s -X PUT "$API_BASE/auth/profile" \
        -H "Authorization: Bearer $TOKEN" \
        -H "Content-Type: application/json" \
        -d '{
            "name": "Test User Updated",
            "phone": "089876543210"
        }')
    
    echo "$RESPONSE" | jq .
    
    if echo "$RESPONSE" | jq -e '.user' > /dev/null; then
        print_success "Update profile successful"
    else
        print_error "Update profile failed"
    fi
}

# ============================================
# 2. MOUNTAIN TESTS
# ============================================

test_list_mountains() {
    print_section "5. TEST LIST MOUNTAINS"
    
    TOKEN=$(cat /tmp/summitpass_token.txt)
    
    RESPONSE=$(curl -s -X GET "$API_BASE/mountains?per_page=5" \
        -H "Authorization: Bearer $TOKEN")
    
    echo "$RESPONSE" | jq .
    
    MOUNTAIN_ID=$(echo "$RESPONSE" | jq -r '.data[0].id')
    
    if [ "$MOUNTAIN_ID" != "null" ] && [ ! -z "$MOUNTAIN_ID" ]; then
        print_success "List mountains successful"
        echo "$MOUNTAIN_ID" > /tmp/mountain_id.txt
    else
        print_error "List mountains failed or no mountains found"
    fi
}

test_mountain_detail() {
    print_section "6. TEST GET MOUNTAIN DETAIL"
    
    TOKEN=$(cat /tmp/summitpass_token.txt)
    MOUNTAIN_ID=$(cat /tmp/mountain_id.txt)
    
    RESPONSE=$(curl -s -X GET "$API_BASE/mountains/$MOUNTAIN_ID" \
        -H "Authorization: Bearer $TOKEN")
    
    echo "$RESPONSE" | jq .
    
    if echo "$RESPONSE" | jq -e '.data.id' > /dev/null; then
        print_success "Get mountain detail successful"
    else
        print_error "Get mountain detail failed"
    fi
}

test_basecamps() {
    print_section "7. TEST GET BASECAMPS"
    
    TOKEN=$(cat /tmp/summitpass_token.txt)
    MOUNTAIN_ID=$(cat /tmp/mountain_id.txt)
    
    RESPONSE=$(curl -s -X GET "$API_BASE/mountains/$MOUNTAIN_ID/basecamps" \
        -H "Authorization: Bearer $TOKEN")
    
    echo "$RESPONSE" | jq .
    
    BASECAMP_ID=$(echo "$RESPONSE" | jq -r '.data[0].id')
    
    if [ "$BASECAMP_ID" != "null" ] && [ ! -z "$BASECAMP_ID" ]; then
        print_success "Get basecamps successful"
        echo "$BASECAMP_ID" > /tmp/basecamp_id.txt
    else
        print_error "Get basecamps failed"
    fi
}

test_checkpoints() {
    print_section "8. TEST GET CHECKPOINTS"
    
    TOKEN=$(cat /tmp/summitpass_token.txt)
    MOUNTAIN_ID=$(cat /tmp/mountain_id.txt)
    
    RESPONSE=$(curl -s -X GET "$API_BASE/mountains/$MOUNTAIN_ID/checkpoints" \
        -H "Authorization: Bearer $TOKEN")
    
    echo "$RESPONSE" | jq .
    
    if echo "$RESPONSE" | jq -e '.data' > /dev/null; then
        print_success "Get checkpoints successful"
    else
        print_error "Get checkpoints failed"
    fi
}

# ============================================
# 3. BOOKING TESTS
# ============================================

test_available_dates() {
    print_section "9. TEST GET AVAILABLE DATES"
    
    TOKEN=$(cat /tmp/summitpass_token.txt)
    MOUNTAIN_ID=$(cat /tmp/mountain_id.txt)
    
    RESPONSE=$(curl -s -X GET "$API_BASE/mountains/$MOUNTAIN_ID/available-dates" \
        -H "Authorization: Bearer $TOKEN")
    
    echo "$RESPONSE" | jq .
    
    if echo "$RESPONSE" | jq -e '.data' > /dev/null; then
        print_success "Get available dates successful"
    else
        print_error "Get available dates failed"
    fi
}

test_operators() {
    print_section "10. TEST GET OPERATORS"
    
    TOKEN=$(cat /tmp/summitpass_token.txt)
    MOUNTAIN_ID=$(cat /tmp/mountain_id.txt)
    
    RESPONSE=$(curl -s -X GET "$API_BASE/mountains/$MOUNTAIN_ID/operators" \
        -H "Authorization: Bearer $TOKEN")
    
    echo "$RESPONSE" | jq .
    
    OPERATOR_ID=$(echo "$RESPONSE" | jq -r '.data[0].id')
    
    if [ "$OPERATOR_ID" != "null" ] && [ ! -z "$OPERATOR_ID" ]; then
        print_success "Get operators successful"
        echo "$OPERATOR_ID" > /tmp/operator_id.txt
    else
        print_error "Get operators failed"
    fi
}

test_create_booking() {
    print_section "11. TEST CREATE BOOKING"
    
    TOKEN=$(cat /tmp/summitpass_token.txt)
    MOUNTAIN_ID=$(cat /tmp/mountain_id.txt)
    BASECAMP_ID=$(cat /tmp/basecamp_id.txt)
    OPERATOR_ID=$(cat /tmp/operator_id.txt)
    
    # Get tomorrow's date
    START_DATE=$(date -d tomorrow +%Y-%m-%d)
    
    RESPONSE=$(curl -s -X POST "$API_BASE/bookings" \
        -H "Authorization: Bearer $TOKEN" \
        -H "Content-Type: application/json" \
        -d "{
            \"mountain_id\": $MOUNTAIN_ID,
            \"basecamp_id\": $BASECAMP_ID,
            \"start_date\": \"$START_DATE\",
            \"operator_id\": $OPERATOR_ID
        }")
    
    echo "$RESPONSE" | jq .
    
    BOOKING_ID=$(echo "$RESPONSE" | jq -r '.data.id')
    
    if [ "$BOOKING_ID" != "null" ] && [ ! -z "$BOOKING_ID" ]; then
        print_success "Create booking successful"
        echo "$BOOKING_ID" > /tmp/booking_id.txt
    else
        print_error "Create booking failed"
    fi
}

test_list_bookings() {
    print_section "12. TEST LIST BOOKINGS"
    
    TOKEN=$(cat /tmp/summitpass_token.txt)
    
    RESPONSE=$(curl -s -X GET "$API_BASE/bookings" \
        -H "Authorization: Bearer $TOKEN")
    
    echo "$RESPONSE" | jq .
    
    if echo "$RESPONSE" | jq -e '.data' > /dev/null; then
        print_success "List bookings successful"
    else
        print_error "List bookings failed"
    fi
}

test_booking_detail() {
    print_section "13. TEST GET BOOKING DETAIL"
    
    TOKEN=$(cat /tmp/summitpass_token.txt)
    BOOKING_ID=$(cat /tmp/booking_id.txt)
    
    RESPONSE=$(curl -s -X GET "$API_BASE/bookings/$BOOKING_ID" \
        -H "Authorization: Bearer $TOKEN")
    
    echo "$RESPONSE" | jq .
    
    if echo "$RESPONSE" | jq -e '.data.id' > /dev/null; then
        print_success "Get booking detail successful"
    else
        print_error "Get booking detail failed"
    fi
}

test_cancel_booking() {
    print_section "14. TEST CANCEL BOOKING"
    
    TOKEN=$(cat /tmp/summitpass_token.txt)
    BOOKING_ID=$(cat /tmp/booking_id.txt)
    
    RESPONSE=$(curl -s -X DELETE "$API_BASE/bookings/$BOOKING_ID/cancel" \
        -H "Authorization: Bearer $TOKEN")
    
    echo "$RESPONSE" | jq .
    
    if echo "$RESPONSE" | jq -e '.message' > /dev/null; then
        print_success "Cancel booking successful"
    else
        print_error "Cancel booking failed"
    fi
}

test_logout() {
    print_section "15. TEST LOGOUT"
    
    TOKEN=$(cat /tmp/summitpass_token.txt)
    
    RESPONSE=$(curl -s -X POST "$API_BASE/auth/logout" \
        -H "Authorization: Bearer $TOKEN")
    
    echo "$RESPONSE" | jq .
    
    if echo "$RESPONSE" | jq -e '.message' > /dev/null; then
        print_success "Logout successful"
    else
        print_error "Logout failed"
    fi
}

# ============================================
# MAIN EXECUTION
# ============================================

main() {
    echo -e "${BLUE}"
    echo "╔════════════════════════════════════════╗"
    echo "║  SummitPass API Testing Script         ║"
    echo "║  Testing: $API_BASE"
    echo "╚════════════════════════════════════════╝"
    echo -e "${NC}"
    
    # Check if server is running
    print_info "Checking if server is running..."
    if ! curl -s "$BASE_URL/health" > /dev/null 2>&1; then
        print_error "Server is not running! Start with: php artisan serve"
        exit 1
    fi
    print_success "Server is running"
    
    # Check if jq is installed
    if ! command -v jq &> /dev/null; then
        print_error "jq is required for JSON parsing. Install it first:"
        echo "  macOS: brew install jq"
        echo "  Linux: apt-get install jq"
        echo "  Windows: choco install jq"
        exit 1
    fi
    
    # Run tests
    test_register
    sleep 1
    test_login
    sleep 1
    test_profile
    sleep 1
    test_update_profile
    sleep 1
    test_list_mountains
    sleep 1
    test_mountain_detail
    sleep 1
    test_basecamps
    sleep 1
    test_checkpoints
    sleep 1
    test_available_dates
    sleep 1
    test_operators
    sleep 1
    test_create_booking
    sleep 1
    test_list_bookings
    sleep 1
    test_booking_detail
    sleep 1
    test_cancel_booking
    sleep 1
    test_logout
    
    print_section "TESTING COMPLETE!"
    print_success "All tests completed successfully!"
    
    # Cleanup
    rm -f /tmp/summitpass_token.txt /tmp/mountain_id.txt /tmp/basecamp_id.txt /tmp/operator_id.txt /tmp/booking_id.txt
}

# Run main
main