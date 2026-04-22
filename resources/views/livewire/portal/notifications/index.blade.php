<div>
    <div class="page-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
        <div>
            <h2 style="font-size: 24px; font-weight: 800; color: var(--text-primary); margin: 0; display: flex; align-items: center; gap: 12px;">
                <i class="fas fa-bell" style="color: var(--accent);"></i>
                Notifications
            </h2>
            <p style="color: var(--text-secondary); font-size: 14px; margin-top: 4px;">Track your application status and official updates</p>
        </div>
        <div style="display: flex; gap: 12px;">
            @if($notifications->total() > 0)
                <button wire:click="markAllAsRead" class="btn btn-outline btn-sm">
                    <i class="fas fa-check-double"></i> Mark all read
                </button>
                @if($notifications->getCollection()->contains(fn($n) => $n->read_at))
                    <button wire:click="clearAllRead" class="btn btn-outline btn-sm" style="color: var(--danger); border-color: rgba(192, 57, 43, 0.2);">
                        <i class="fas fa-broom"></i> Clear history
                    </button>
                @endif
            @endif
        </div>
    </div>

    <div class="card" style="padding: 0; overflow: hidden; border-radius: var(--radius); border: 1px solid var(--border); box-shadow: var(--shadow);">
        @forelse($notifications as $notification)
            <div style="padding: 20px; border-bottom: 1px solid var(--border); display: flex; gap: 20px; align-items: flex-start; transition: all var(--transition); cursor: pointer; {{ $notification->read_at ? 'opacity: 0.8;' : 'background: rgba(27, 122, 68, 0.02); border-left: 4px solid var(--accent);' }}"
                 @click="$wire.markAsRead({{ $notification->id }}, true)"
                 onmouseover="this.style.backgroundColor='var(--bg-input)'"
                 onmouseout="this.style.backgroundColor='{{ $notification->read_at ? 'transparent' : 'rgba(27, 122, 68, 0.02)' }}'">
                
                <div style="flex-shrink: 0; width: 44px; height: 44px; border-radius: 12px; background: {{ $notification->read_at ? 'var(--bg-input)' : 'var(--accent-light)' }}; color: {{ $notification->read_at ? 'var(--text-secondary)' : 'var(--accent)' }}; display: flex; align-items: center; justify-content: center; font-size: 18px;">
                    <i class="fas {{ $notification->icon ?: 'fa-info-circle' }}"></i>
                </div>

                <div style="flex-grow: 1; min-width: 0;">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 6px;">
                        <span style="font-size: 10px; text-transform: uppercase; font-weight: 800; color: {{ $notification->read_at ? 'var(--text-secondary)' : 'var(--accent)' }}; letter-spacing: 0.8px;">
                            {{ str_replace('_', ' ', $notification->type) }}
                        </span>
                        <span style="font-size: 12px; color: var(--text-secondary); font-weight: 500;">
                            {{ $notification->created_at->diffForHumans() }}
                        </span>
                    </div>
                    
                    <h4 style="font-size: 16px; font-weight: 700; margin-bottom: 6px; color: var(--text-primary); line-height: 1.4;">
                        {{ $notification->title }}
                    </h4>
                    
                    <p style="font-size: 14px; color: var(--text-secondary); line-height: 1.6; margin-bottom: 14px;">
                        {{ $notification->message }}
                    </p>
                    
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <div style="display: flex; gap: 16px; align-items: center;">
                            @if($notification->action_url)
                                <span style="font-size: 12px; font-weight: 700; color: var(--accent); display: flex; align-items: center; gap: 6px;">
                                    {{ $notification->action_label ?: 'View Details' }}
                                    <i class="fas fa-arrow-right" style="font-size: 10px;"></i>
                                </span>
                            @endif
                        </div>
                        
                        <div style="display: flex; gap: 8px;">
                            @if(!$notification->read_at)
                                <button title="Mark as read" wire:click.stop="markAsRead({{ $notification->id }})" 
                                    style="background: none; border: none; color: var(--accent); cursor: pointer; font-size: 14px; padding: 6px; border-radius: 6px; transition: background 0.2s;"
                                    onmouseover="this.style.background='var(--accent-light)'"
                                    onmouseout="this.style.background='none'">
                                    <i class="fas fa-check"></i>
                                </button>
                            @endif
                            <button title="Delete" 
                                wire:click.stop="deleteNotification({{ $notification->id }})" 
                                wire:confirm="Are you sure you want to delete this notification?" 
                                style="background: none; border: none; color: var(--text-secondary); cursor: pointer; font-size: 14px; padding: 6px; border-radius: 6px; transition: all 0.2s;"
                                onmouseover="this.style.background='var(--danger-light)'; this.style.color='var(--danger)'"
                                onmouseout="this.style.background='none'; this.style.color='var(--text-secondary)'">
                                <i class="fas fa-trash-alt"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div style="padding: 80px 20px; text-align: center;">
                <div style="width: 80px; height: 80px; background: var(--bg-input); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 20px;">
                    <i class="fas fa-bell-slash" style="font-size: 32px; color: var(--border);"></i>
                </div>
                <h4 style="color: var(--text-primary); font-size: 18px; font-weight: 700; margin-bottom: 8px;">All caught up!</h4>
                <p style="color: var(--text-secondary); font-size: 14px;">No notifications found in your history.</p>
            </div>
        @endforelse
    </div>

    @if($notifications->total() > 0)
        <div class="pagination-wrapper" style="margin-top: 24px; display: flex; justify-content: space-between; align-items: center;">
            <p style="font-size: 14px; color: var(--text-secondary);">
                Showing <strong>{{ $notifications->firstItem() }}</strong> to <strong>{{ $notifications->lastItem() }}</strong> of <strong>{{ $notifications->total() }}</strong> notifications
            </p>
            <div>
                {{ $notifications->links() }}
            </div>
        </div>
    @endif
</div>
