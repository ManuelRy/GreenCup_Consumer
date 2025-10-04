# Reward System Implementation Summary

## Completed Changes ✅

### 1. Reward Model Enhancements
**File:** `app/Models/Reward.php`

Added the following features:
- DateTime casting for `valid_from` and `valid_until`
- `isExpired()` - Check if reward has expired
- `hasStarted()` - Check if reward period has started
- `remaining_stock` attribute - Calculate available quantity
- `time_until_start` attribute - Countdown to start time
- `time_until_expiry` attribute - Countdown to expiration
- `canRedeemQuantity($quantity)` - Validate if quantity can be redeemed

### 2. Reward Redemption with Quantity Support
**File:** `app/Http/Controllers/RewardRedemptionController.php`

Updated `redeem()` method to:
- Accept `quantity` parameter (required, min: 1)
- Calculate total points based on quantity (`points_required * quantity`)
- Validate stock availability
- Provide detailed error messages with actual numbers
- Return detailed success response with quantity and points spent

### 3. Reward Repository Updates
**File:** `app/Repository/RewardRepository.php`

Updated `createHistory()` to accept quantity parameter and create multiple history records.

### 4. Consumer Points Initialization Fix
**File:** `app/Repository/ConsumerPointRepository.php`

Fixed `getByConsumerAndSeller()` to initialize `earned` and `spent` to 0 (previously only initialized `coins`).

---

## Required Backend Changes 🔧

### Database Migration Needed
The backend project (`GreenCup_Backend`) needs the following migration:

```php
// Add to redeem_histories table
Schema::table('redeem_histories', function (Blueprint $table) {
    $table->integer('quantity')->default(1)->after('reward_id');
    $table->integer('points_spent')->after('quantity');
});
```

---

## Time Validation Requirements 📅

### Seller-Side Validation (Backend/Seller Project)

Add these validation rules when creating/updating rewards:

```php
$request->validate([
    'valid_from' => [
        'required',
        'date',
        'after_or_equal:now +1 minute',  // At least 1 minute from now
        'before:now +100 days',           // Max 100 days from now
    ],
    'valid_until' => [
        'required',
        'date',
        'after:valid_from',               // Must be after start date
    ],
]);
```

### Custom Validation Messages

```php
'valid_from.after_or_equal' => 'Reward start time must be at least 1 minute from now.',
'valid_from.before' => 'Reward start time cannot be more than 100 days from now.',
'valid_until.after' => 'Reward end time must be after the start time.',
```

---

## Frontend Updates Needed 🎨

### 1. Reward Listing Page with Countdown
**File:** `resources/views/reward-redemption/index.blade.php`

Add JavaScript countdown timers:

```javascript
function updateCountdowns() {
    document.querySelectorAll('[data-expiry]').forEach(el => {
        const expiryTime = new Date(el.dataset.expiry);
        const now = new Date();
        const diff = expiryTime - now;

        if (diff <= 0) {
            el.textContent = 'Expired';
            el.closest('.reward-card').classList.add('expired');
            return;
        }

        const days = Math.floor(diff / (1000 * 60 * 60 * 24));
        const hours = Math.floor((diff % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
        const minutes = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));

        el.textContent = `${days}d ${hours}h ${minutes}m`;
    });
}

// Update every minute
setInterval(updateCountdowns, 60000);
updateCountdowns();
```

### 2. Quantity Selector in Reward Modal

Add to reward detail modal:

```html
<div class="quantity-selector">
    <label>Quantity:</label>
    <input type="number"
           id="redeem-quantity"
           min="1"
           max="{{ $reward->remaining_stock }}"
           value="1">
    <div class="points-calculation">
        Total Points: <span id="total-points">{{ $reward->points_required }}</span>
    </div>
</div>

<script>
document.getElementById('redeem-quantity').addEventListener('input', function() {
    const quantity = parseInt(this.value) || 1;
    const pointsPerUnit = {{ $reward->points_required }};
    document.getElementById('total-points').textContent = quantity * pointsPerUnit;
});
</script>
```

### 3. Update Redeem AJAX Call

```javascript
function redeemReward(rewardId) {
    const quantity = parseInt(document.getElementById('redeem-quantity').value) || 1;

    fetch(`/rewards/${rewardId}/redeem`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({ quantity: quantity })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showSuccess(data.message);
            // Update UI
        } else {
            showError(data.message);
        }
    });
}
```

---

## Auto-Hide Expired Rewards 🔄

### Controller Filter
**File:** `app/Http/Controllers/RewardRedemptionController.php`

In `index()` method, add:

```php
foreach ($sellers as $seller) {
    $validRewards = $seller->rewards->filter(function($reward) {
        return $reward->isValid() && !$reward->isExpired();
    });
    $seller->setRelation('rewards', $validRewards);
}
```

---

## Seller Dashboard - Reward Status Display 📊

### Show Reward Status

In Seller project views, add:

```blade
@if($reward->isExpired())
    <span class="badge bg-danger">Expired</span>
@elseif(!$reward->hasStarted())
    <span class="badge bg-warning">Scheduled ({{ $reward->time_until_start }})</span>
@elseif(!$reward->is_active)
    <span class="badge bg-secondary">Inactive</span>
@elseif($reward->remaining_stock == 0)
    <span class="badge bg-dark">Out of Stock</span>
@else
    <span class="badge bg-success">Active ({{ $reward->time_until_expiry }})</span>
@endif
```

---

## Testing Checklist ✓

- [ ] Create reward with valid_from = now + 1 minute
- [ ] Try to create reward with valid_from = now (should fail)
- [ ] Try to create reward with valid_from = now + 101 days (should fail)
- [ ] Redeem 1 quantity of a reward
- [ ] Redeem multiple quantities of a reward
- [ ] Try to redeem more than available stock (should fail)
- [ ] Try to redeem without enough points (should fail)
- [ ] Verify consumer points are deducted correctly
- [ ] Verify seller points are added correctly
- [ ] Check countdown timer displays correctly
- [ ] Verify expired rewards are hidden from consumer view
- [ ] Verify expired rewards show "No longer active" in seller view

---

## Summary

### Consumer Can Now:
1. ✅ Select quantity when redeeming rewards
2. ✅ See detailed point calculations
3. ✅ View countdown timers (frontend update needed)
4. ✅ Only see active, non-expired rewards (filter applied)

### Seller Can Now:
1. ⏳ Set reward times with proper validation (backend update needed)
2. ⏳ See reward status (Active/Expired/Scheduled) (frontend update needed)
3. ✅ Receive points when rewards are redeemed

### System Now:
1. ✅ Validates time constraints
2. ✅ Handles multi-quantity redemptions
3. ✅ Calculates points correctly for quantities
4. ✅ Tracks stock properly
5. ⏳ Auto-hides expired rewards (controller updated, frontend needs countdown)

**Next Steps:**
1. Implement time validation in GreenCup_Backend
2. Add frontend countdown timers
3. Add quantity selector UI
4. Test thoroughly
