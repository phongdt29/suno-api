# Fix "The current credits are insufficient" Error

## Problem
You're seeing "The current credits are insufficient. Please top up." even though dev mode is enabled.

## Why This Happens
- Browser is using cached JavaScript/CSS
- WordPress has cached the old API response
- Session hasn't refreshed

## Solution (Do these in order)

### Step 1: Verify Dev Mode is Enabled ✅
```bash
php test-dev-mode.php
```

Should show:
```
✅ ENABLED
Credits: 999999
Dev mode is working!
```

### Step 2: Clear WordPress Cache
```bash
php clear-cache.php
```

### Step 3: Clear Browser Cache
**Chrome/Edge/Brave:**
- Press: `Ctrl + Shift + R` (Windows)
- Or: `Cmd + Shift + R` (Mac)

**Firefox:**
- Press: `Ctrl + Shift + R` (Windows)
- Or: `Cmd + Shift + R` (Mac)

**Alternative: Open Incognito/Private Window**
- Ctrl + Shift + N (Chrome)
- Ctrl + Shift + P (Firefox)

### Step 4: Test Generate Music
1. Go to music generator page
2. Enter any prompt: "Test song"
3. Click generate
4. Should see:
   - Task ID starting with `dev-task-`
   - 2 songs generated instantly
   - No credits consumed

## Verification Checklist

✅ **Dev mode enabled:**
```php
// In wp-config.php
define('SUNO_DEV_MODE', true);
```

✅ **Mock API working:**
```bash
php test-dev-mode.php
# Should show 999999 credits
```

✅ **Cache cleared:**
```bash
php clear-cache.php
```

✅ **Browser refreshed:**
- Hard refresh with Ctrl+Shift+R
- Or use incognito mode

## What Dev Mode Does

When enabled, the system:
- ❌ Does NOT call real Suno API
- ❌ Does NOT consume credits
- ✅ Returns mock responses instantly
- ✅ Uses free sample audio from SoundHelix
- ✅ Shows 999,999 fake credits

## How to Check It's Working

### In Browser Console (F12):
```javascript
// Should see these logs:
DEV MODE: Mock Suno API Request - /api/v1/generate/credit
DEV MODE: Mock Suno API Request - /api/v1/generate
```

### In Generated Songs:
- Task ID: `dev-task-xxxxx`
- Song ID: `dev-song-xxxxx`
- Audio URL: `soundhelix.com`
- Credits: 999,999

## Still Getting Error?

### Check wp-config.php:
```bash
php -r "require 'wp-config.php'; var_dump(SUNO_DEV_MODE);"
# Should output: bool(true)
```

### Check functions.php has mock function:
```bash
grep -n "miraculous_suno_mock_response" wp-content/themes/miraculous-music/functions.php
# Should show line number ~295
```

### Check API request interceptor:
```bash
grep -n "if (defined('SUNO_DEV_MODE')" wp-content/themes/miraculous-music/functions.php
# Should show line number ~392
```

## Disable Dev Mode (Use Real API)

When you have credits and want to use real Suno API:

```php
// In wp-config.php
define('SUNO_DEV_MODE', false);  // Change to false
define('SUNO_API_KEY', 'your-real-api-key-here');
```

Then clear cache:
```bash
php clear-cache.php
```

## Error Logs

Check WordPress debug log:
```bash
tail -f wp-content/debug.log
```

Should see:
```
DEV MODE: Mock Suno API Request - /api/v1/generate
```

If you see real API errors, dev mode is not working.

## Summary

✅ **Dev mode IS enabled** - Confirmed by test-dev-mode.php
✅ **Mock responses working** - Returns 999,999 credits
✅ **Cache cleared** - Removed old API responses

**Next step:** Hard refresh browser (Ctrl+Shift+R) and try again!

---

**Still stuck?** The error is likely from **cached JavaScript** in your browser. Try:
1. Open incognito window
2. Or clear all browser data for localhost
3. Or use a different browser
