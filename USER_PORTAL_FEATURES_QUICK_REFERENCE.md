# User Portal Features - Quick Reference

## Files Created

### Models (2 new)

- **AddressIndexingRequest** → `app/Models/AddressIndexingRequest.php`
    - Handles address indexing applications
    - Relationships: User, Payment (polymorphic)
- **StreetRevalidation** → `app/Models/StreetRevalidation.php`
    - Handles street revalidation requests
    - Relationships: User, Street, Payment (polymorphic)

### Livewire Components (2 new)

- **RegisterAddressIndexing** → `app/Livewire/Portal/RegisterAddressIndexing.php`
    - 4-step multi-step form
    - File upload support
    - Validation included
- **StreetRevalidationForm** → `app/Livewire/Portal/StreetRevalidationForm.php`
    - 3-step form with tab selection
    - Support for existing street selection or new street record
    - Document upload support

### Blade Views (2 new)

- `resources/views/livewire/portal/register-address-indexing.blade.php`
- `resources/views/livewire/portal/street-revalidation-form.blade.php`

### Migrations (3 new)

- **2026_04_04_000003_add_polymorphic_payment_support.php**
    - Adds payable_id and payable_type to payments table
    - Enables polymorphic relationships
- **2026_04_04_000004_create_address_indexing_requests_table.php**
    - Creates address_indexing_requests table
    - Columns for address details, owner info, images, status
- **2026_04_04_000005_create_street_revalidations_table.php**
    - Creates street_revalidations table
    - Columns for revalidation details, documents, status

### Documentation

- **USER_PORTAL_FEATURES_GUIDE.md** - Comprehensive implementation guide
- **USER_PORTAL_FEATURES_QUICK_REFERENCE.md** - This file

---

## Feature Overview

### 1. Street Naming Request ✅ (Already Exists)

**Status:** Already implemented via `StreetApplication` model

**Flow:**

```
User submits form → StreetApplication created → Payment → Approval
```

### 2. Address Indexing 🆕

**Status:** Fully created and ready to integrate

**Flow:**

```
4-Step Form → AddressIndexingRequest created → Payment initialization
→ Paystack payment → Payment verify → Status updated
```

**Steps:**

1. Your Information (Name, Phone)
2. Address Details (Address, Coordinates, House No)
3. Owner Information (Owner Name, Phone)
4. Property Images (Upload up to 5 images)

### 3. Street Revalidation 🆕

**Status:** Fully created and ready to integrate

**Flow:**

```
Tab Selection → Details → Reason → Documents
→ StreetRevalidation created → Payment → Approval
```

**Options:**

- Select from existing active streets (with search)
- Record new street for revalidation

**Information Collected:**

- Street name & ward
- Reason for revalidation
- Current status (active/inactive/disputed/under-review)
- Supporting documents (up to 5 files)

---

## Database Schema

### address_indexing_requests

```
- id (Primary Key)
- user_id (FK)
- address_line (string)
- latitude (decimal)
- longitude (decimal)
- house_number (string)
- owner_name (string)
- owner_phone (string)
- applicant_name (string)
- applicant_phone (string)
- property_images (JSON array)
- description (text)
- status (pending|approved|rejected)
- admin_note (text)
- reviewed_at (timestamp)
- timestamps
```

### street_revalidations

```
- id (Primary Key)
- user_id (FK)
- street_id (FK, nullable)
- street_name (string)
- ward (string)
- reason (text)
- supporting_documents (JSON array)
- current_status (string)
- status (pending|approved|rejected)
- admin_note (text)
- reviewed_at (timestamp)
- timestamps
```

### payments (Updated)

```
Added columns:
- payable_id (integer, nullable)
- payable_type (string) → morphs to AddressIndexingRequest or StreetRevalidation
```

---

## API Endpoints (To Be Added)

### Payment Endpoints

```
POST   /portal/payment/initialize
  Payload: {type, request_id, amount, email}
  Returns: {status, authorization_url, reference}

POST   /portal/payment/verify
  Payload: {reference}
  Returns: {status, message, payment}

GET    /portal/payment/status/{reference}
  Returns: {status, amount, paid_at}
```

### Admin Endpoints (To Be Created)

```
GET    /admin/address-indexing-requests
GET    /admin/address-indexing-requests/{id}
POST   /admin/address-indexing-requests/{id}/approve
POST   /admin/address-indexing-requests/{id}/reject

GET    /admin/street-revalidations
GET    /admin/street-revalidations/{id}
POST   /admin/street-revalidations/{id}/approve
POST   /admin/street-revalidations/{id}/reject
```

---

## Integration Steps

1. **Run Migrations**

    ```bash
    php artisan migrate
    ```

2. **Add Routes** to `routes/web.php`
    - See USER_PORTAL_FEATURES_GUIDE.md for details

3. **Add Navigation Items** to portal menu
    - Address Indexing link
    - Street Revalidation link

4. **Extend PaymentController**
    - See USER_PORTAL_FEATURES_GUIDE.md for implementation

5. **Create Admin Components** (optional but recommended)
    - Index & Show pages for both request types
    - Approve/Reject actions

6. **Configure Fees**
    - Add to fee schedule admin panel
    - Set amounts for each service type

7. **Set Up Webhooks**
    - Configure Paystack webhooks in dashboard
    - Point to existing webhook handler

---

## Key Features

✅ **Multi-step Forms** with validation
✅ **File Upload** (images & documents)
✅ **Dynamic Payment** integration with Paystack
✅ **Polymorphic Relationships** for flexible payment handling
✅ **User-friendly UI** with progress indicators
✅ **Status Tracking** (pending → completed/approved)
✅ **Admin Approval Workflow** ready for implementation
✅ **File Storage** with public access

---

## Environment Variables

Already configured in your `.env`:

```
PAYSTACK_PUBLIC_KEY=pk_test_****
PAYSTACK_SECRET_KEY=sk_test_****
```

---

## Form Validations

### AddressIndexing

- All applicant/owner fields required
- Coordinates must be valid (lat: -90 to 90, lng: -180 to 180)
- Images: max 5, each max 5MB
- House number required

### StreetRevalidation

- Street selection OR new street details required
- Reason: required, max 1000 chars
- Current status: required dropdown
- Documents: max 5, each max 10MB

---

## Storage Locations

```
storage/app/public/
├── property-images/          (Address indexing images)
└── revalidation-documents/   (Revalidation supporting docs)
```

Don't forget to run:

```bash
php artisan storage:link
```

---

## Status Flow

### AddressIndexingRequest

```
pending → [Payment] → completed/approved → [Admin Review] → approved/rejected
```

### StreetRevalidation

```
pending → [Payment] → completed → [Admin Review] → approved/rejected
```

---

## Related Existing Features

- **Street Management:** See `Street` model and `RegisterStreet` component
- **Address Management:** See `Address` model and `RegisterAddress` component
- **Payment System:** See `Payment` model and `PaymentProcessor` component
- **User Roles:** Spatie Laravel Permission (Staff can access portal)

---

## Testing Recommendations

1. Test all form validations
2. Test file uploads
3. Test payment flow with Paystack test keys
4. Test admin approval workflow
5. Verify status updates after payment
6. Test error handling and recovery

---

## Support & Maintenance

For updates or modifications:

1. Update migrations for schema changes
2. Update model relationships as needed
3. Update validation rules in components
4. Add admin components for management

---

**Last Updated:** April 4, 2026
**Version:** 1.0.0
**Status:** Ready for Integration
