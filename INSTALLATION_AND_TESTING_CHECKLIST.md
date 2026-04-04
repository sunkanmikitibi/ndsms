# Installation & Testing Checklist

## 🚀 Get Started in 5 Commands

### 1. Run Database Migrations

```bash
php artisan migrate
```

**Expected Output:**

```
Migrating: 2026_04_04_000003_add_polymorphic_payment_support
Migrated:  2026_04_04_000003_add_polymorphic_payment_support (123ms)
Migrating: 2026_04_04_000004_create_address_indexing_requests_table
Migrated:  2026_04_04_000004_create_address_indexing_requests_table (145ms)
Migrating: 2026_04_04_000005_create_street_revalidations_table
Migrated:  2026_04_04_000005_create_street_revalidations_table (156ms)
```

### 2. Create Storage Symlink

```bash
php artisan storage:link
```

**Expected Output:**

```
The [public/storage] link has been connected to [storage/app/public].
```

### 3. Verify Routes

```bash
php artisan route:list | grep portal
```

**Look for:**

```
GET|HEAD   portal/register-address-indexing  ... RegisterAddressIndexing
GET|HEAD   portal/street-revalidation       ... StreetRevalidationForm
```

### 4. Clear Cache

```bash
php artisan optimize:clear
```

### 5. Start Server

```bash
php artisan serve
```

---

## ✅ Functionality Testing

### Test 1: Address Indexing Form

```
1. Visit: http://localhost:8000/portal/register-address-indexing
2. Step 1: Enter name "John Doe", phone "08012345678"
3. Step 2: Enter address "123 Main St", coordinates (6.1234, 7.5678)
4. Step 3: Enter owner name & phone
5. Step 4: Upload test image
6. Submit form
7. Check database:
   SELECT * FROM address_indexing_requests;
   ✓ Record should exist with status 'pending'
```

### Test 2: Street Revalidation (Existing Street)

```
1. Visit: http://localhost:8000/portal/street-revalidation
2. Tab: "Existing Street"
3. Search and select a street
4. Step 2: Enter reason, select status
5. Step 3: Upload document (optional)
6. Submit form
7. Check database:
   SELECT * FROM street_revalidations;
   ✓ Record should exist with street_id populated
```

### Test 3: Street Revalidation (New Street)

```
1. Visit: http://localhost:8000/portal/street-revalidation
2. Tab: "Record Street"
3. Enter street name "New Street", ward "Ward A"
4. Step 2: Enter reason, select status
5. Step 3: Upload document
6. Submit form
7. Check database:
   SELECT * FROM street_revalidations WHERE street_id IS NULL;
   ✓ Record should exist with street_id as NULL
```

### Test 4: Database Verification

```bash
# Check new tables exist
php artisan tinker

> Schema::getTables();
> \DB::table('address_indexing_requests')->count();
> \DB::table('street_revalidations')->count();
> \DB::table('payments')->limit(1)->first();
```

---

## 🔧 Troubleshooting Commands

### Check Migration Status

```bash
php artisan migrate:status
```

### Rollback Migrations (if needed)

```bash
# Rollback only new migrations
php artisan migrate:rollback --step=3

# Or specific migration
php artisan migrate:rollback --path=database/migrations/2026_04_04_000005_create_street_revalidations_table.php
```

### Verify Livewire Components

```bash
# Check if components are registered
php artisan livewire:list

# Should show:
# portal.register-address-indexing
# portal.street-revalidation-form
```

### Clear Livewire Cache

```bash
php artisan livewire:clear-cache
```

### Check Route Registration

```bash
php artisan route:list | grep -E "(register-address-indexing|street-revalidation|payment)"
```

---

## 📊 Database Verification

### Using MySQL/SQL

```sql
-- Check new tables
SHOW TABLES LIKE 'address_indexing_requests';
SHOW TABLES LIKE 'street_revalidations';

-- Check payments table structure
DESC payments;
-- Should show: payable_id, payable_type columns

-- Sample data check
SELECT * FROM address_indexing_requests LIMIT 1;
SELECT * FROM street_revalidations LIMIT 1;
SELECT * FROM payments WHERE payable_type IS NOT NULL LIMIT 1;
```

### Using Laravel Tinker

```bash
php artisan tinker

# List tables
> DB::select("SHOW TABLES");

# Check address indexing requests
> App\Models\AddressIndexingRequest::all();

# Check street revalidations
> App\Models\StreetRevalidation::all();

# Check polymorphic payment
> App\Models\Payment::with('payable')->get();
```

---

## 🌐 URL Testing

### Access Points

```
Form Pages:
- http://localhost:8000/portal/register-address-indexing
- http://localhost:8000/portal/street-revalidation
- http://localhost:8000/portal/register-street (existing)

Payment Endpoints:
- POST http://localhost:8000/payment/initialize
- POST http://localhost:8000/payment/verify
- GET  http://localhost:8000/payment/status/{reference}
```

### Test Payment Initialize (using Postman/cURL)

```bash
curl -X POST http://localhost:8000/payment/initialize \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -d '{
    "type": "address_indexing",
    "request_id": 1,
    "amount": 1500
  }'
```

---

## 📝 Manual Testing Scenarios

### Scenario 1: Complete Address Indexing Flow

```
1. Login as staff user
2. Navigate to /portal/register-address-indexing
3. Fill all 4 steps
4. Upload 2-3 images
5. Submit form
6. Verify:
   - Database record created
   - status = 'pending'
   - Images stored in storage/app/public/property-images/
```

### Scenario 2: Complete Street Revalidation Flow

```
1. Login as staff user
2. Navigate to /portal/street-revalidation
3. Select existing street (or create new)
4. Provide revalidation reason
5. Upload 1-2 documents
6. Submit form
7. Verify:
   - Database record created
   - status = 'pending'
   - Documents stored in storage/app/public/revalidation-documents/
```

### Scenario 3: Payment Flow (when event listener is set up)

```
1. Submit any form (address indexing or street revalidation)
2. Payment initialization should be triggered
3. Verify Payment record created in database
4. (When event listener implemented) Should redirect to Paystack
5. Complete payment with test card
6. Verify Payment status updated to 'completed'
```

---

## 🔍 Verification Checklist

### ✅ Pre-Installation

- [ ] You're in the correct project directory
- [ ] `.env` file is configured
- [ ] Database is accessible
- [ ] Composer packages are installed (if not: `composer install`)

### ✅ Post-Installation

- [ ] `php artisan migrate` completed without errors
- [ ] `php artisan storage:link` created symlink
- [ ] `php artisan optimize:clear` ran successfully

### ✅ Functionality

- [ ] Address Indexing form loads at `/portal/register-address-indexing`
- [ ] Street Revalidation form loads at `/portal/street-revalidation`
- [ ] Both forms validate properly
- [ ] File uploads work
- [ ] Database records are created on form submission

### ✅ Routes

- [ ] New portal routes exist: `php artisan route:list`
- [ ] Payment routes configured correctly
- [ ] Can access forms when authenticated

### ✅ Components

- [ ] Livewire components registered: `php artisan livewire:list`
- [ ] Component classes exist in `app/Livewire/Portal/`
- [ ] Component views exist in `resources/views/livewire/portal/`

### ✅ Models

- [ ] `AddressIndexingRequest` model exists
- [ ] `StreetRevalidation` model exists
- [ ] `Payment` model has polymorphic relationship
- [ ] `User` model has relationships to new models

### ✅ Migrations

- [ ] All migrations ran: `php artisan migrate:status`
- [ ] `address_indexing_requests` table exists
- [ ] `street_revalidations` table exists
- [ ] `payments` table has `payable_id` and `payable_type` columns

---

## 🚨 Troubleshooting Quick Fixes

### Issue: "Class not found"

```bash
php artisan composer:dump-autoload
php artisan optimize:clear
```

### Issue: Forms not showing

```bash
# Check if components load
php artisan livewire:list

# Check routes
php artisan route:list | grep portal
```

### Issue: Migration errors

```bash
# See detailed error
php artisan migrate --vvv

# Check if migrations exist
ls -la database/migrations/ | grep 2026_04_04
```

### Issue: File upload not working

```bash
# Verify symlink
ls -la public/storage

# Create if missing
php artisan storage:link

# Check permissions
chmod -R 755 storage/
```

### Issue: Database not updating

```bash
# Check connection
php artisan tinker
> DB::connection()->getPDO();

# Run migrations explicitly
php artisan migrate:specific --path=database/migrations/2026_04_04_000004_create_address_indexing_requests_table.php
```

---

## 📱 Testing with Different User Roles

### As Staff User

```
✓ Can access: /portal/register-address-indexing
✓ Can access: /portal/street-revalidation
✓ Can access: /portal/register-street
```

### As Admin User

```
✓ Can access: /admin/dashboard
✓ Can view: Submitted applications (when admin components created)
✓ Can approve/reject: Applications (when admin components created)
```

### As Super-Admin

```
✓ Can access: All admin features
✓ Can configure: Fee schedules
✓ Can manage: Approvals workflow
```

---

## 📋 Final Verification

Before considering implementation complete, verify:

```bash
# 1. All required files exist
test -f app/Models/AddressIndexingRequest.php && echo "✓ AddressIndexingRequest model"
test -f app/Models/StreetRevalidation.php && echo "✓ StreetRevalidation model"
test -f app/Livewire/Portal/RegisterAddressIndexing.php && echo "✓ RegisterAddressIndexing component"
test -f app/Livewire/Portal/StreetRevalidationForm.php && echo "✓ StreetRevalidationForm component"

# 2. Verify database
php artisan tinker
> App\Models\AddressIndexingRequest::count();
> App\Models\StreetRevalidation::count();

# 3. Test routes
curl -s http://localhost:8000/portal/register-address-indexing -I | grep 200
curl -s http://localhost:8000/portal/street-revalidation -I | grep 200
```

---

## 🎯 Next Steps After Testing

1. ✅ Complete all checklist items above
2. 💳 Implement payment event listeners (see PAYMENT_INTEGRATION_GUIDE.md)
3. 🔌 Configure Paystack webhooks
4. 👥 Train your team
5. 🚀 Deploy to production

---

## 📞 Support Resources

- **USER_PORTAL_FEATURES_GUIDE.md** - Full implementation reference
- **PAYMENT_INTEGRATION_GUIDE.md** - Payment setup instructions
- **IMPLEMENTATION_COMPLETE.md** - Troubleshooting guide
- **FULL_IMPLEMENTATION_SUMMARY.md** - Executive overview

---

**Last Updated:** April 4, 2026
**Checklist Version:** 1.0
**Status:** Ready for Testing
