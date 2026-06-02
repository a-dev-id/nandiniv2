# Nandini Bali Website

This is a Laravel website for Nandini Jungle by Hanging Gardens. It contains the public marketing pages, content-managed page sections, WebHotelier reservation sync, and a custom membership program called Inner Circle.

Use this file as handoff context for another developer or AI agent before changing the project.

## Tech Stack

- Backend: Laravel 13, PHP 8.3+
- Admin/content: Filament
- Frontend: Blade, Tailwind CSS 4, Alpine.js, Vite
- Tests: PHPUnit via `php artisan test`
- Auth:
  - default `web` guard for normal users/admin
  - custom `member` guard for membership accounts

## Important Commands

```bash
composer install
npm install
php artisan migrate
npm run dev
npm run build
php artisan test
```

The project is intended for shared hosting. Do not assume long-running queue workers or daemon processes are available. Automation should be implemented as Artisan commands that can be called by cron.

## Main Public Routes

Routes are defined in `routes/web.php`.

- `/` home
- `/membership` membership landing page
- `/membership/benefits`
- `/membership/privilege-redemption`
- `/membership/sign-in`
- `/membership/join`
- `/membership/dashboard`
- `/membership/profile/edit`
- `/membership/rewards/{reward}/redeem`
- `/membership/rewards/redemptions/{redemption}/thank-you`

The generic `/login` route redirects to `membership.login`.

## Key Membership Files

- `app/Models/Member.php`
  - member profile, points, tier rules, point transactions, yearly downgrade logic
- `app/Models/Reward.php`
  - redeemable reward records
- `app/Models/MemberPointTransaction.php`
  - earn/redeem/adjustment history
- `app/Models/MemberRewardRedemption.php`
  - redemption code, status, expiry, snapshot of reward name and points used
- `app/Http/Controllers/MembershipAuthController.php`
  - member login, registration, email verification, dashboard, logout
- `app/Http/Controllers/MemberRewardRedemptionController.php`
  - reward redemption POST and thank-you page
- `app/Services/RewardRedemptionService.php`
  - core redemption logic
- `resources/views/pages/membership/dashboard.blade.php`
  - member dashboard, tier card, history, rewards carousel
- `resources/views/pages/membership/privilege-redemption.blade.php`
  - reward redemption listing
- `resources/views/components/sections/membership-use-points-section.blade.php`
  - membership page "Use Your Points" section

## Membership Tier Rules

The tier ranges follow the membership card screenshot:

- Dana / Bronze: `0-400` points
- Upaya / Silver: `401-800` points
- Dhyana / Gold: `801-1200` points
- Jnana / Platinum: `1201+` points

Important behavior:

- Available points are spendable and can decrease when a member redeems rewards.
- The saved member tier should not automatically downgrade just because points are spent.
- Earning points can still upgrade/recalculate tier.
- Redeeming points does not recalculate tier.
- The dashboard should display the saved `members.tier`, not a tier recalculated from current points.

Example:

1. Member has `2000` points and is Jnana / Platinum.
2. Member redeems points and now has `700` points.
3. Member remains Jnana / Platinum during the active membership year.
4. On yearly review, if there is no qualifying stay/renewal, the tier downgrades one step to Dhyana / Gold.
5. Points remain `700` because downgrade only caps points that exceed the new tier maximum; it does not top points up to the new tier minimum.
6. On later yearly downgrades, points are preserved unless they exceed the new tier maximum.
7. If a member downgrades into Dana / Bronze with `700` points, points are capped to `400`; the extra `300` points are removed through an adjustment transaction.

The current downgrade implementation is in `Member::applyYearlyTierDowngrade()`.

## Reward Redemption Flow

Logged-in members can redeem rewards if:

- reward is active
- reward has a positive `points_required`
- member has enough available points

Flow:

1. Member clicks Redeem.
2. Browser posts to `membership.rewards.redeem`.
3. `MemberRewardRedemptionController::store()` validates the request.
4. `RewardRedemptionService::redeem()` runs the transaction.
5. The member row is locked with `lockForUpdate()` to prevent double spending.
6. A negative `member_point_transactions` row is created.
7. `members.points` is reduced.
8. A pending `member_reward_redemptions` row is created.
9. Member is redirected to the thank-you page with a redemption code.

Guests should be sent to `membership.login` before redeeming.

## Yearly Downgrade and Cron

This project is on shared hosting, so scheduled automation should be cron-based.

Future automation should be implemented as an Artisan command, for example:

```bash
php artisan memberships:apply-yearly-downgrades
```

The hosting cron can call it daily. The command should find expired memberships and call the same model/service logic used by the app. Avoid relying on queue workers, Laravel Horizon, or always-running scheduler daemons.

Current code also calls `applyYearlyTierDowngrade()` during membership login/dashboard access, but the intended production automation direction is cron.

## WebHotelier Integration

WebHotelier pull integration lives in:

- `app/Services/WebhotelierPullService.php`
- `app/Services/MemberAutoJoinService.php`
- `app/Http/Controllers/Cron/WebhotelierSyncController.php`

The pull flow is:

1. List pending bookings from WebHotelier.
2. Retrieve each booking.
3. Auto-create a member if the booking has a valid email and is not cancelled.
4. Send the welcome email with temporary password.
5. Mark the booking as synced.

Configuration is read from `config/services.php` and `.env` values for WebHotelier.

Because this is shared hosting, sync should be triggered by a cron-accessible endpoint or Artisan command, depending on hosting support.

## Content and Views

The site uses Blade page templates and reusable components. Many page sections are managed through Filament page/section resources.

Common view areas:

- `resources/views/pages/`
- `resources/views/pages/membership/`
- `resources/views/components/sections/`
- `resources/views/components/heroes/`
- `resources/views/components/layouts/navbar.blade.php`

Images commonly live in:

- `public/images/`
- `storage/app/public/` exposed through Laravel storage link

## Recent Behavior Changes to Preserve

- Membership "Use Your Points" reward card links that are `#` send guests to sign-in.
- Membership "View More" goes to `/membership/privilege-redemption`.
- Member ID is hidden from the dashboard profile/card UI.
- Dashboard history thumbnails should be square.
- Reward redemption should not downgrade tier after spending points.
- Yearly downgrade should step down one tier and cap points only if above the new tier maximum.

## Tests Added for Membership

Focused tests exist for the point redemption and tier downgrade rules:

```bash
php artisan test tests/Feature/RewardRedemptionServiceTest.php tests/Feature/MemberTierDowngradeTest.php
```

These cover:

- redemption deducts points and creates a pending redemption
- insufficient points are rejected
- redemption does not downgrade saved tier
- yearly downgrade steps down one tier
- yearly downgrade caps points to the new tier maximum

## Development Notes for AI Agents

- Read existing Blade and model patterns before changing behavior.
- Use the `member` guard for membership auth checks: `auth('member')`.
- Do not recalculate tier from points in dashboard display unless explicitly asked.
- Use route names where available, especially `membership.login`, `membership.dashboard`, and `membership.privilege-redemption`.
- For membership point changes, prefer model methods such as `earnPoints()` and `redeemPoints()` so transactions are recorded.
- Keep shared hosting constraints in mind: use cron-friendly commands for automation.
- Avoid broad refactors unless the requested change requires them.
