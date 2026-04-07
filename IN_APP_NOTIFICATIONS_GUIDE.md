# In-App Notifications System

## Overview

The in-app notifications system is a comprehensive notification management solution integrated into the NDSMS platform. It provides real-time, user-specific notifications for various application events like street approvals, payment confirmations, and system updates.

## Architecture

### Components

#### 1. Database Layer

- **Table**: `notifications`
- **Model**: `app/Models/Notification.php`
- **Migration**: `database/migrations/2026_04_07_000100_create_notifications_table.php`

**Schema:**

- `id` (Primary Key)
- `user_id` (Foreign Key to users table)
- `type` (string: info, success, warning, error, approval, rejection, payment, delivery)
- `title` (string)
- `message` (text)
- `icon` (string - Font Awesome class)
- `action_url` (nullable URL)
- `action_label` (nullable string)
- `metadata` (JSON - extensible data)
- `read_at` (nullable timestamp - read status)
- `expires_at` (nullable timestamp - auto-expiration)
- `created_at`, `updated_at` (timestamps)

**Indexes:**

- `(user_id, read_at)` - Query unread notifications quickly
- `(user_id, created_at)` - Recent notifications
- `type` - Filter by notification type

#### 2. Service Layer

**Class**: `app/Services/InAppNotificationService.php`

**Key Methods:**

```php
// Basic notification creation
create(User|int $user, string $type, string $title, string $message, ...)

// Create for multiple users
createForMultiple(array|Collection $users, ...)

// Type-specific helpers
notifyApproval(), notifyRejection(), notifySuccess(), notifyError(), notifyWarning(), notifyInfo(), notifyPayment(), notifyDelivery()

// User-specific queries
getUnreadForUser(User|int $user, $limit = 10)
getUnreadCountForUser(User|int $user)
getRecentForUser(User|int $user, $limit = 30)

// Notification management
markAsRead(Notification $notification)
markAllAsReadForUser(User|int $user)
delete(Notification $notification)
deleteReadNotificationsForUser(User|int $user)
deleteExpiredNotifications()
```

#### 3. Frontend Layer

**Livewire Component**: `app/Livewire/Components/NotificationBell.php`
**View**: `resources/views/livewire/components/notification-bell.blade.php`

**Features:**

- Real-time notification counter
- Dropdown panel with sorting
- Mark individual notifications as read
- Mark all as read functionality
- Delete individual notifications
- Clear read notifications
- Long-format timestamps (e.g., "2 hours ago")
- Full notification details in dropdown

#### 4. Event Observers

**Street Events**: `app/Observers/StreetObserver.php`

- Notifies users on street creation, approval, rejection, hold status
- Uses accurate street names and rejection reasons

**Payment Events**: `app/Observers/PaymentObserver.php`

- Notifies users on payment creation, success, failure, and pending states
- Includes formatted currency amounts

#### 5. Service Provider

**Provider**: `app/Providers/AppServiceProvider.php`

- Registers `InAppNotificationService` as singleton
- Observes `Street` and `Payment` models

## Usage Examples

### Basic Notification Creation

```php
use App\Services\InAppNotificationService;

$notificationService = app(InAppNotificationService::class);

// Create an approval notification
$notificationService->notifyApproval(
    $userId,
    'Street Request Approved',
    'Your street request for Main Street has been approved.',
    route('portal.streets.show', $street),
    ['street_id' => $street->id]
);
```

### In Controller

```php
use App\Services\InAppNotificationService;

class StreetController extends Controller
{
    public function approve(Request $request, Street $street)
    {
        $street->update(['status' => 'approved']);

        // Automatically triggered by StreetObserver

        return redirect()->back()->with('success', 'Street approved');
    }
}
```

### Querying Notifications

```php
// Get 10 unread notifications
$unread = $notificationService->getUnreadForUser(auth()->id());

// Get unread count for bell badge
$count = $notificationService->getUnreadCountForUser(auth()->id());

// Get recent notifications (last 10 days)
$recent = $notificationService->getRecentForUser(auth()->id(), 50);

// Mark all as read
$notificationService->markAllAsReadForUser(auth()->id());
```

## Notification Types

| Type        | Icon                          | Use Case                               |
| ----------- | ----------------------------- | -------------------------------------- |
| `info`      | `fas fa-info-circle`          | System updates, informational messages |
| `success`   | `fas fa-check`                | Operation success, confirmations       |
| `warning`   | `fas fa-exclamation-triangle` | Warnings, on-hold statuses             |
| `error`     | `fas fa-exclamation-circle`   | Errors, failures, problems             |
| `approval`  | `fas fa-check-circle`         | Approvals granted                      |
| `rejection` | `fas fa-times-circle`         | Rejections, denials                    |
| `payment`   | `fas fa-credit-card`          | Payment-related actions                |
| `delivery`  | `fas fa-truck`                | Delivery/shipping updates              |

## Integration Points

### Portal Layout

Located in `resources/views/components/layouts/portal.blade.php`

```blade
<div style="margin-left:auto;">
    @livewire('components.notification-bell')
</div>
```

### Admin Layout

Located in `resources/views/components/layouts/admin.blade.php`

```blade
<div style="margin-left:auto;">
    @livewire('components.notification-bell')
</div>
```

## Styling

The notification bell uses CSS variables for consistent theming:

- `--accent` - Primary accent color (green)
- `--accent-light` - Light variant for read status
- `--bg-input` - Background for hover states
- `--text-primary` - Primary text color
- `--text-secondary` - Secondary text color
- `--danger` - Red for badge count
- `--border` - Border color
- `--radius` - Border radius
- `--shadow-lg` - Box shadow for dropdown
- `--transition` - Smooth transitions

## Database Cleanup

### Automatic Expiration

Notifications can be set to auto-expire:

```php
$expiresAt = now()->addDays(30);
$notificationService->create(
    $userId,
    'info',
    'Temporary Notice',
    'This notification will expire in 30 days',
    null,
    null,
    null,
    null,
    $expiresAt
);
```

### Manual Cleanup

```php
// Delete expired notifications
$notificationService->deleteExpiredNotifications();

// Delete read notifications for user
$notificationService->deleteReadNotificationsForUser($userId);
```

## Security & Authorization

- Notifications are user-scoped (each user only sees their own)
- NotificationBell component only loads authenticated user's notifications
- Observers only create notifications for specified users
- Action URLs are validated through standard Laravel routes

## Events & Real-time Updates

The notification system supports Livewire events for real-time updates:

```php
// Dispatch event when notification is created
$this->dispatch('notification-created');

// Dispatch event when notification is read
$this->dispatch('notification-read', notificationId: $notificationId);
```

## Future Enhancements

1. **Email Digest** - Send daily/weekly email summaries
2. **Push Notifications** - Browser push notifications via service workers
3. **SMS Notifications** - Send critical notifications via SMS
4. **Notification Preferences** - User-controlled notification settings
5. **Bulk Operations** - Admin bulk notification sending
6. **Analytics** - Track notification read rates and engagement
7. **Grouping** - Group similar notifications by type/source
8. **Rich Media** - Support images and rich content in notifications

## Testing

### Create Test Notifications

```php
use App\Services\InAppNotificationService;

// In test or seeder
$service = app(InAppNotificationService::class);

$service->notifySuccess(
    $user,
    'Test Notification',
    'This is a test notification',
    route('portal.dashboard'),
    ['test' => true]
);
```

### Query Unread Count

```php
$count = \App\Models\Notification::where('user_id', $user->id)
    ->whereNull('read_at')
    ->count();
```

## Files Summary

| File                                                                   | Purpose                           |
| ---------------------------------------------------------------------- | --------------------------------- |
| `app/Models/Notification.php`                                          | Eloquent model with relationships |
| `app/Services/InAppNotificationService.php`                            | Business logic for notifications  |
| `app/Livewire/Components/NotificationBell.php`                         | Real-time bell component          |
| `resources/views/livewire/components/notification-bell.blade.php`      | Bell UI & dropdown                |
| `app/Observers/StreetObserver.php`                                     | Auto-notify on street events      |
| `app/Observers/PaymentObserver.php`                                    | Auto-notify on payment events     |
| `database/migrations/2026_04_07_000100_create_notifications_table.php` | Database schema                   |
| `app/Providers/AppServiceProvider.php`                                 | Service registration & observers  |

## Git Commits

- **3d717b1** - Initial in-app notifications implementation
- Includes complete system from database to UI

---

**Implementation Date**: April 7, 2026
**Version**: 1.0.0
**Status**: Active ✓
