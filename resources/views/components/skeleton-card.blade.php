{{-- Skeleton Loading Card Component --}}
<div class="card skeleton-card" aria-busy="true" aria-label="Memuat konten">
    <div class="skeleton-header" style="display:flex;align-items:center;gap:1rem;margin-bottom:1rem;">
        <div class="skeleton skeleton-avatar" style="width:48px;height:48px;border-radius:50%;"></div>
        <div style="flex:1;">
            <div class="skeleton skeleton-text" style="height:16px;width:60%;margin-bottom:0.5rem;"></div>
            <div class="skeleton skeleton-text" style="height:12px;width:40%;"></div>
        </div>
    </div>
    <div class="skeleton-body">
        <div class="skeleton skeleton-text" style="height:14px;width:100%;margin-bottom:0.5rem;"></div>
        <div class="skeleton skeleton-text" style="height:14px;width:80%;margin-bottom:0.5rem;"></div>
        <div class="skeleton skeleton-text" style="height:14px;width:90%;"></div>
    </div>
</div>

<style>
.skeleton-card {
    pointer-events: none;
    user-select: none;
}
</style>

// Made with Bob
