<div class="notification-bell-wrapper" @click.away="$wire.showPanel = false" x-data="{ showPanel: @entangle('showPanel') }">
    <!-- Bell Icon Button -->
    <button @click="showPanel = !showPanel"
        style="background:none;border:none;color:var(--text-sidebar);font-size:20px;cursor:pointer;position:relative;padding:8px;border-radius:var(--radius-sm);transition:background-color var(--transition);"
        @mouseover="this.style.backgroundColor='var(--bg-sidebar-hover)'"
        @mouseout="this.style.backgroundColor='transparent'" title="Notifications" class="notification-bell">
        <i class="fas fa-bell"></i>
        @if ($unreadCount > 0)
            <span
                style="position:absolute;top:4px;right:4px;min-width:20px;height:20px;background:var(--danger);color:#fff;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:10px;font-weight:700;">
                {{ $unreadCount > 99 ? '99+' : $unreadCount }}
            </span>
        @endif
    </button>

    <!-- Notification Panel -->
    @if ($showPanel)
        <div
            style="position:absolute;top:100%;right:0;width:380px;max-height:600px;background:var(--bg-card);border:1px solid var(--border);border-radius:var(--radius);box-shadow:var(--shadow-lg);z-index:1000;overflow:hidden;display:flex;flex-direction:column;margin-top:8px;">

            <!-- Header -->
            <div
                style="background:var(--bg-input);border-bottom:1px solid var(--border);padding:12px 16px;display:flex;justify-content:space-between;align-items:center;">
                <span style="font-weight:700;color:var(--text-primary);">Notifications</span>
                @if ($unreadCount > 0)
                    <button wire:click="markAllAsRead"
                        style="background:none;border:none;color:var(--accent);font-size:12px;cursor:pointer;text-decoration:underline;font-weight:600;">
                        Mark all as read
                    </button>
                @endif
            </div>

            <!-- Notifications List -->
            <div style="flex:1;overflow-y:auto;">
                @forelse ($allNotifications as $notif)
                    <div style="border-bottom:1px solid var(--border);padding:12px;background:{{ $notif['read_at'] ? 'transparent' : 'var(--accent-light)' }};display:flex;gap:10px;"
                        @click="$wire.markAsRead({{ $notif['id'] }})" class="notification-item"
                        onmouseover="this.style.backgroundColor='var(--bg-input)'"
                        onmouseout="this.style.backgroundColor='{{ $notif['read_at'] ? 'transparent' : 'var(--accent-light)' }}'">

                        <!-- Icon -->
                        <div style="flex-shrink:0;font-size:16px;color:var(--accent);padding-top:2px;">
                            <i class="{{ $notif['icon'] ?? 'fas fa-bell' }}"></i>
                        </div>

                        <!-- Content -->
                        <div style="flex:1;min-width:0;">
                            <div style="font-weight:600;color:var(--text-primary);font-size:13px;margin-bottom:2px;">
                                {{ $notif['title'] }}
                            </div>
                            <div
                                style="color:var(--text-secondary);font-size:12px;line-height:1.4;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;">
                                {{ $notif['message'] }}
                            </div>
                            <div style="font-size:10px;color:var(--text-secondary);margin-top:4px;">
                                {{ $notif['created_at']->diffForHumans() ?? 'Just now' }}
                            </div>
                            @if ($notif['action_url'])
                                <a href="{{ $notif['action_url'] }}"
                                    style="display:inline-block;margin-top:6px;padding:4px 8px;background:var(--accent);color:#fff;border-radius:4px;font-size:11px;font-weight:600;text-decoration:none;transition:background-color var(--transition);"
                                    onmouseover="this.style.backgroundColor='var(--accent-gold)'"
                                    onmouseout="this.style.backgroundColor='var(--accent)'">
                                    {{ $notif['action_label'] ?? 'View' }}
                                </a>
                            @endif
                        </div>

                        <!-- Delete Button -->
                        <button wire:click.stop="deleteNotification({{ $notif['id'] }})"
                            style="background:none;border:none;color:var(--text-secondary);cursor:pointer;padding:0;font-size:14px;flex-shrink:0;transition:color var(--transition);"
                            onmouseover="this.style.color='var(--danger)'"
                            onmouseout="this.style.color='var(--text-secondary)'">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                @empty
                    <div style="padding:40px 16px;text-align:center;color:var(--text-secondary);">
                        <i class="fas fa-inbox" style="font-size:32px;margin-bottom:8px;display:block;opacity:0.5;"></i>
                        <p style="font-size:13px;">No notifications yet</p>
                    </div>
                @endforelse
            </div>

            <!-- Footer -->
            @if (count($allNotifications) > 0)
                <div
                    style="background:var(--bg-input);border-top:1px solid var(--border);padding:10px;text-align:center;display:flex;justify-content:center;gap:12px;align-items:center;">
                    <a href="{{ route('portal.notifications') }}" style="font-size:12px;font-weight:700;color:var(--accent);text-decoration:none;">View All</a>
                    @if (count(array_filter($allNotifications, fn($n) => $n['read_at'])) > 0)
                        <span style="color:var(--border);">|</span>
                        <button wire:click="deleteAllRead"
                            style="background:none;border:none;color:var(--text-secondary);font-size:11px;cursor:pointer;text-decoration:underline;font-weight:600;transition:color var(--transition);"
                            onmouseover="this.style.color='var(--danger)'"
                            onmouseout="this.style.color='var(--text-secondary)'">
                            Clear read notifications
                        </button>
                    @endif
                </div>
            @endif
        </div>
    @endif
</div>
