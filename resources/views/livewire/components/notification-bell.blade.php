<div class="notification-bell-wrapper" wire:poll.15s="loadNotifications" @click.away="$wire.showPanel = false"
    x-data="{ showPanel: @entangle('showPanel') }" style="position: relative;">
    
    <style>
        [x-cloak] { display: none !important; }

        @keyframes notification-pulse {
            0% { transform: scale(1); box-shadow: 0 0 0 0 rgba(192, 57, 43, 0.7); }
            70% { transform: scale(1.1); box-shadow: 0 0 0 10px rgba(192, 57, 43, 0); }
            100% { transform: scale(1); box-shadow: 0 0 0 0 rgba(192, 57, 43, 0); }
        }
        .unread-badge-pulse {
            animation: notification-pulse 2s infinite;
        }
        .notification-item {
            transition: all var(--transition);
            cursor: pointer;
            position: relative;
        }
        .notification-item:hover {
            background-color: var(--bg-input) !important;
        }
        .notification-item.unread::before {
            content: '';
            position: absolute;
            left: 0;
            top: 0;
            bottom: 0;
            width: 3px;
            background: var(--accent);
        }
    </style>

    <!-- Bell Icon Button -->
    <button @click="showPanel = !showPanel"
        style="background:none;border:none;color:var(--text-sidebar);font-size:20px;cursor:pointer;position:relative;padding:8px;border-radius:var(--radius-sm);transition:all var(--transition);"
        @mouseover="this.style.backgroundColor='var(--bg-sidebar-hover)'; this.style.color='var(--text-sidebar-active)'"
        @mouseout="this.style.backgroundColor='transparent'; this.style.color='var(--text-sidebar)'" 
        title="Notifications" 
        class="notification-bell {{ $unreadCount > 0 ? 'has-unread' : '' }}">
        
        <i class="fas fa-bell"></i>
        
        @if ($unreadCount > 0)
            <span class="unread-badge-pulse"
                style="position:absolute;top:4px;right:4px;min-width:18px;height:18px;background:var(--danger);color:#fff;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:10px;font-weight:700;border:2px solid var(--bg-sidebar);">
                {{ $unreadCount > 99 ? '99+' : $unreadCount }}
            </span>
        @endif
    </button>

    <!-- Notification Panel -->
    <div x-show="showPanel" x-cloak x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 scale-95 translate-y-[-10px]"
        x-transition:enter-end="opacity-100 scale-100 translate-y-0"
        style="position:absolute;top:100%;left:0;width:360px;max-height:500px;background:var(--bg-card);border:1px solid var(--border);border-radius:var(--radius);box-shadow:var(--shadow-lg);z-index:1000;overflow:hidden;display:flex;flex-direction:column;margin-top:12px;">

        <!-- Header -->
        <div
            style="background:var(--bg-secondary);border-bottom:1px solid var(--border);padding:14px 16px;display:flex;justify-content:space-between;align-items:center;">
            <span style="font-weight:700;color:var(--text-primary);font-size:15px;">Notifications</span>
            <div style="display:flex; gap:12px; align-items:center;">
                @if ($unreadCount > 0)
                    <button wire:click="markAllAsRead"
                        style="background:none;border:none;color:var(--accent);font-size:11px;cursor:pointer;font-weight:600;display:flex;align-items:center;gap:4px;">
                        <i class="fas fa-check-double"></i> Mark all read
                    </button>
                @endif
            </div>
        </div>

        <!-- Notifications List -->
        <div style="flex:1;overflow-y:auto;background:var(--bg-card);">
            @forelse ($allNotifications as $notif)
                <div class="notification-item {{ ($notif['read_at'] ?? null) ? '' : 'unread' }}"
                    style="border-bottom:1px solid var(--border);padding:14px 16px;background:{{ ($notif['read_at'] ?? null) ? 'transparent' : 'rgba(27, 122, 68, 0.03)' }};display:flex;gap:12px;"
                    @click="$wire.markAsRead({{ $notif['id'] }}, true)">

                    <!-- Icon -->
                    <div style="flex-shrink:0;width:32px;height:32px;border-radius:50%;background:{{ ($notif['read_at'] ?? null) ? 'var(--bg-input)' : 'var(--accent-light)' }};color:{{ ($notif['read_at'] ?? null) ? 'var(--text-secondary)' : 'var(--accent)' }};display:flex;align-items:center;justify-content:center;font-size:14px;">
                        <i class="{{ $notif['icon'] ?? 'fas fa-bell' }}"></i>
                    </div>

                    <!-- Content -->
                    <div style="flex:1;min-width:0;">
                        <div style="display:flex; justify-content:space-between; align-items:flex-start; margin-bottom:2px;">
                            <div style="font-weight:700;color:var(--text-primary);font-size:13px;line-height:1.2;">
                                {{ $notif['title'] }}
                            </div>
                            <div style="font-size:10px;color:var(--text-secondary);white-space:nowrap;margin-left:8px;">
                                {{ \Carbon\Carbon::parse($notif['created_at'])->diffForHumans(null, true) }}
                            </div>
                        </div>
                        <div
                            style="color:var(--text-secondary);font-size:12px;line-height:1.4;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;margin-bottom:4px;">
                            {{ $notif['message'] }}
                        </div>
                        
                        @if ($notif['action_url'])
                            <div style="font-size:11px; font-weight:700; color:var(--accent); display:flex; align-items:center; gap:4px;">
                                {{ $notif['action_label'] ?? 'View Details' }} <i class="fas fa-chevron-right" style="font-size:8px;"></i>
                            </div>
                        @endif
                    </div>

                    <!-- Delete Button (Silent) -->
                    <button wire:click.stop="deleteNotification({{ $notif['id'] }})"
                        style="background:none;border:none;color:var(--border);cursor:pointer;padding:4px;font-size:12px;flex-shrink:0;transition:color var(--transition);"
                        onmouseover="this.style.color='var(--danger)'"
                        onmouseout="this.style.color='var(--border)'">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            @empty
                <div style="padding:60px 24px;text-align:center;color:var(--text-secondary);">
                    <div style="width:64px;height:64px;background:var(--bg-input);border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 16px;">
                        <i class="fas fa-bell-slash" style="font-size:24px;opacity:0.3;"></i>
                    </div>
                    <h4 style="color:var(--text-primary);font-size:15px;margin-bottom:4px;">No notifications</h4>
                    <p style="font-size:12px;">We'll notify you when something important happens.</p>
                </div>
            @endforelse
        </div>

        <!-- Footer -->
        <div
            style="background:var(--bg-secondary);border-top:1px solid var(--border);padding:12px;text-align:center;display:flex;justify-content:center;gap:16px;align-items:center;">
            <a href="{{ route('portal.notifications') }}"
                style="font-size:12px;font-weight:700;color:var(--accent);text-decoration:none;display:flex;align-items:center;gap:6px;">
                View all history <i class="fas fa-external-link-alt" style="font-size:10px;"></i>
            </a>
            @if (count(array_filter($allNotifications, fn($n) => ($n['read_at'] ?? null))) > 0)
                <div style="width:1px; height:12px; background:var(--border);"></div>
                <button wire:click="deleteAllRead"
                    style="background:none;border:none;color:var(--text-secondary);font-size:11px;cursor:pointer;font-weight:600;transition:all var(--transition);"
                    onmouseover="this.style.color='var(--danger)'"
                    onmouseout="this.style.color='var(--text-secondary)'">
                    Clear read
                </button>
            @endif
        </div>
    </div>
</div>
