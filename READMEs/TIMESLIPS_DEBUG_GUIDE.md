# Timeslips Timer Debug Guide

## Issue Reported
1. **Slip Timer Started** field does not update in browser when timer starts
2. **Slip Timer End** field does not update in browser when timer stops
3. **Total Time** field does not update when timer is stopped

## Debug Steps Added

### Console Logging
Added comprehensive console.log statements throughout the timer functions to track:
- When start time is set
- When start date is set
- Timer start confirmation
- Timer stop events
- End time/date setting
- Hours calculation

### How to Debug

1. **Open Browser DevTools**
   - Press `F12` or right-click → Inspect
   - Go to **Console** tab

2. **Start Timer and Watch Console**
   ```
   Expected console output:
   - "Setting start time..."
   - "Set time: [TIME] to slip_timer_started"
   - "Setting start date: [DATE]"
   - "Timer started at: [TIME] [DATE]"
   - "Updating hours from timer: [SECONDS] seconds = [HOURS] hours" (every second)
   ```

3. **Stop Timer and Watch Console**
   ```
   Expected console output:
   - "Stopping timer..."
   - "Timer seconds: [TOTAL_SECONDS]"
   - "Setting end time..."
   - "Set time: [TIME] to slip_timer_end"
   - "Setting end date: [DATE]"
   - "Updating hours from timer: [SECONDS] seconds = [HOURS] hours"
   - "Timer stopped. End time: [TIME] End date: [DATE]"
   - "Total hours: [DECIMAL_HOURS]"
   ```

## What Was Fixed

### 1. setCurrentTime() Function
**Before:**
```javascript
inputElement.val(formattedTime);
calculateTime();
```

**After:**
```javascript
inputElement.val(formattedTime).trigger('change');
console.log('Set time:', formattedTime, 'to', inputElement.attr('id'));
```

**Why:** Added `.trigger('change')` to ensure timepicker plugins detect the change.

### 2. startTimer() Function
**Before:**
```javascript
if (!$("#slip_start_date").val()) {
    $("#slip_start_date").val(formatDate(new Date()));
}
```

**After:**
```javascript
if (!$("#slip_start_date").val()) {
    const startDate = formatDate(new Date());
    console.log('Setting start date:', startDate);
    $("#slip_start_date").val(startDate).trigger('change');
}
console.log('Timer started at:', $("#slip_timer_started").val(), $("#slip_start_date").val());
```

**Why:**
- Added `.trigger('change')` for datepicker compatibility
- Added logging to verify values are set

### 3. stopTimer() Function
**Before:**
```javascript
setCurrentTime($("#slip_timer_end"));
if (!$("#slip_end_date").val()) {
    $("#slip_end_date").val(formatDate(new Date()));
}
calculateTime(); // Wrong - uses date/time calculation
```

**After:**
```javascript
console.log('Stopping timer...');
console.log('Timer seconds:', timerSeconds);
setCurrentTime($("#slip_timer_end"));
if (!$("#slip_end_date").val()) {
    const endDate = formatDate(new Date());
    console.log('Setting end date:', endDate);
    $("#slip_end_date").val(endDate).trigger('change');
}
updateHoursFromTimer(); // Correct - uses timer elapsed time
console.log('Timer stopped. End time:', $("#slip_timer_end").val(), 'End date:', $("#slip_end_date").val());
console.log('Total hours:', $("#slip_hours").val());
```

**Why:**
- Changed from `calculateTime()` to `updateHoursFromTimer()`
- `calculateTime()` calculates based on date/time difference
- `updateHoursFromTimer()` uses actual timer elapsed seconds
- Added extensive logging

### 4. updateHoursFromTimer() Function
**After:**
```javascript
function updateHoursFromTimer() {
    const totalMinutes = Math.floor(timerSeconds / 60);
    const hours = Math.floor(totalMinutes / 60);
    const minutes = totalMinutes % 60;
    const decimalHours = `${hours}.${minutes.toString().padStart(2, '0')}`;

    console.log('Updating hours from timer:', timerSeconds, 'seconds =', decimalHours, 'hours');
    $("#slip_hours").val(decimalHours);
}
```

**Why:** Added logging to track conversion from seconds to decimal hours.

## Testing Steps

### Test 1: Timer Start Fields Update
1. Go to: http://slworker00:5500/timeslips/edit
2. Open browser console (F12)
3. Click "Start" button
4. **Check in console:**
   - Should see "Setting start time..."
   - Should see "Set time: [TIME] to slip_timer_started"
   - Should see "Timer started at: [TIME] [DATE]"
5. **Check in browser (visual):**
   - "Slip Timer Started" field should show current time (e.g., "10:30:45 AM")
   - "Slip Start Date" field should show today's date (e.g., "09/01/2025")
6. **Result:** ✅ If fields update | ❌ If fields remain empty

### Test 2: Hours Update While Running
1. With timer running from Test 1
2. Wait 65 seconds
3. **Check in console:**
   - Should see "Updating hours from timer: 65 seconds = 1.05 hours"
4. **Check in browser:**
   - "Total Hours" field should show "1.05" or similar
5. **Result:** ✅ If hours increment | ❌ If hours stay at 0.00

### Test 3: Timer Stop Fields Update
1. With timer running, click "Stop"
2. **Check in console:**
   - Should see "Stopping timer..."
   - Should see "Timer seconds: [TOTAL]"
   - Should see "Setting end time..."
   - Should see "Set time: [TIME] to slip_timer_end"
   - Should see "Timer stopped. End time: [TIME] End date: [DATE]"
   - Should see "Total hours: [DECIMAL]"
3. **Check in browser:**
   - "Slip Timer End" field should show current time
   - "Slip End Date" field should show today's date
   - "Total Hours" field should show calculated hours
4. **Result:** ✅ If all fields update | ❌ If any field remains empty

### Test 4: Quick Time Buttons
1. Click "Reset" to clear timer
2. Click "+15 min" button
3. **Check in console:**
   - Should see "Updating hours from timer: 900 seconds = 0.15 hours"
4. **Check in browser:**
   - Timer display: "00:15:00"
   - Hours field: "0.15"
5. Click "+30 min" button
6. **Check:**
   - Timer display: "00:45:00"
   - Hours field: "0.45"
7. **Result:** ✅ If correct | ❌ If incorrect

## Common Issues & Solutions

### Issue 1: Fields Don't Update Visually
**Symptom:** Console shows values being set, but form fields appear empty
**Cause:** Timepicker/datepicker jQuery plugins not detecting programmatic changes
**Solution:** We added `.trigger('change')` to all value updates

### Issue 2: Wrong Hours Calculation
**Symptom:** Hours show incorrect value when timer stops
**Cause:** Using `calculateTime()` instead of `updateHoursFromTimer()`
**Solution:** `stopTimer()` now uses `updateHoursFromTimer()` which uses actual elapsed seconds

### Issue 3: Console Shows Errors
**Symptom:** JavaScript errors in console
**Possible Causes:**
- jQuery not loaded
- Timepicker plugin not loaded
- Field IDs don't match
**Solution:** Check that all required JS libraries are loaded in footer.php

### Issue 4: Time Format Mismatch
**Symptom:** Times show as 24-hour instead of 12-hour with AM/PM
**Cause:** `setCurrentTime()` uses 12-hour format with AM/PM
**Expected Format:** "10:30:45 AM" or "3:45:12 PM"

## File Modified
- **ci4/app/Views/timeslips/edit_improved.php**
  - Lines 535-545: Enhanced `startTimer()` with logging and `.trigger('change')`
  - Lines 574-603: Enhanced `stopTimer()` with logging and proper hours update
  - Lines 633-642: Enhanced `updateHoursFromTimer()` with logging
  - Lines 642-658: Enhanced `setCurrentTime()` with `.trigger('change')` and logging

## Next Steps

1. **Test with browser console open** to see all debug messages
2. **Report exact console output** if issues persist
3. **Check browser network tab** to ensure no JS files failing to load
4. **Verify jQuery version** compatibility with timepicker plugin

## Rollback

If issues persist, you can temporarily revert to the old edit view:

```php
// In ci4/app/Controllers/Timeslips.php line 118:
return view($this->table . "/edit", $data);  // Old view
```

---

**Status:** Debug logging added
**Date:** 2025-01-09
**File:** ci4/app/Views/timeslips/edit_improved.php
**Testing URL:** http://slworker00:5500/timeslips/edit
