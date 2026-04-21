<div>
    <div class="page-header" style="justify-content: space-between;">
        <div>
            <h2><i class="fas fa-bell" style="color:var(--accent);margin-right:10px;"></i>Notifications</h2>
            <p>Your history of system updates and messages</p>
        </div>
        @if($notifications->total() > 0)
        <button wire:click="markAllAsRead" class="btn btn-outline btn-sm">
            <i class="fas fa-check-double"></i> Mark all as read
        </button>
        @endif
    </div>

    <div class="card" style="padding:0; overflow:hidden;">
        @forelse($notifications as $notification)
            <div style="padding:16px 20px; border-bottom:1px solid var(--border); display:flex; gap:16px; align-items:flex-start; transition:background-color var(--transition); {{ $notification->read_at ? '' : 'background:rgba(var(--accent-rgb), 0.03); border-left:4px solid var(--accent);' }}"
                 onmouseover="this.style.backgroundColor='var(--bg-input)'"
                 onmouseout="this.style.backgroundColor='{{ $notification->read_at ? 'transparent' : 'rgba(var(--accent-rgb), 0.03)' }}'">
                
                <div style="flex-shrink:0; width:40px; height:40px; border-radius:50%; background:{{ $notification->read_at ? 'var(--border)' : 'var(--accent-light)' }}; color:{{ $notification->read_at ? 'var(--text-secondary)' : 'var(--accent)' }}; display:flex; align-items:center; justify-content:center; font-size:18px;">
                    <i class="fas {{ $notification->icon ?: 'fa-info-circle' }}"></i>
                </div>

                <div style="flex-grow:1;">
                    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:4px;">
                        <span style="font-size:11px; text-transform:uppercase; font-weight:700; color:{{ $notification->read_at ? 'var(--text-secondary)' : 'var(--accent)' }}; letter-spacing:0.5px;">{{ str_replace('_', ' ', $notification->type) }}</span>
                        <span style="font-size:11px; color:var(--text-secondary);">{{ $notification->created_at->diffForHumans() }}</span>
                    </div>
                    <h4 style="font-size:15px; font-weight:700; margin-bottom:4px; color:var(--text-primary);">{{ $notification->title }}</h4>
                    <p style="font-size:14px; color:var(--text-secondary); line-height:1.5; margin-bottom:12px;">{{ $notification->message }}</p>
                    
                    <div style="display:flex; justify-content:space-between; align-items:center;">
                        <div>
                            @if($notification->action_url)
                                <a href="{{ $notification->action_url }}" class="btn btn-primary btn-sm" style="padding: 4px 12px; font-size:12px;">
                                    {{ $notification->action_label ?: 'View Details' }}
                                </a>
                            @endif
                        </div>
                        <div style="display:flex; gap:8px;">
                            @if(!$notification->read_at)
                                <button title="Mark as read" wire:click="markAsRead({{ $notification->id }})" style="background:none; border:none; color:var(--accent); cursor:pointer; font-size:14px; padding:4px;">
                                    <i class="fas fa-check"></i>
                                </button>
                            @endif
                            <button title="Delete" wire:click="deleteNotification({{ $notification->id }})" wire:confirm="Are you sure you want to delete this notification?" style="background:none; border:none; color:var(--danger); cursor:pointer; font-size:14px; padding:4px;">
                                <i class="fas fa-trash-alt"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div style="padding:60px 20px; text-align:center;">
                <i class="fas fa-bell-slash" style="font-size:48px; color:var(--border); margin-bottom:16px;"></i>
                <h4 style="color:var(--text-primary);">No notifications yet</h4>
                <p style="color:var(--text-secondary);">Status updates and official messages will appear here.</p>
            </div>
        @endforelse
    </div>

    @if($notifications->total() > 0)
        <div class="pagination-wrapper" style="margin-top:16px;">
            <p>Showing {{ $notifications->firstItem() }}–{{ $notifications->lastItem() }} of {{ $notifications->total() }} notifications</p>
            <div class="pagination-links">
                {{ $notifications->onEachSide(1)->links('pagination.portal') }}
            </div>
        </div>
    @endif

    <style>
        :root {
            --accent-rgb: 0, 81, 145; /* Approximate blue accent */
        }
    </style>
</div>
