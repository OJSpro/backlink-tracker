# Troubleshooting Guide - Backlink Tracker v1.2

## Issue: Cross-Journal Data Not Showing

### Problem
- Uploaded data to JLSR with site-wide mode enabled
- Enabled plugin on IJLDAI with site-wide mode enabled
- Backlinks not appearing on IJLDAI frontend

### Diagnostic Steps

#### Step 1: Verify Site-Wide Mode is Enabled

**On JLSR:**
1. Go to Settings → Website → Plugins → Backlink Tracker → Settings
2. Verify checkbox "Use site-wide data (shared across all journals)" is **checked**
3. Click Save (even if already checked)
4. Verify you see backlink data status showing count and date

**On IJLDAI:**
1. Go to Settings → Website → Plugins → Backlink Tracker → Settings
2. Verify checkbox "Use site-wide data (shared across all journals)" is **checked**
3. Click Save
4. Check if you see the same backlink count and date as JLSR

#### Step 2: Check Database Directly

You can verify site-wide data exists by checking the database:

```sql
-- Check if site-wide mode is enabled on both journals
SELECT context_id, setting_name, setting_value
FROM plugin_settings
WHERE plugin_name = 'backlinktrackerplugin'
  AND setting_name = 'useSiteWideData';

-- Should show:
-- context_id=1 (JLSR), setting_value='1' (true)
-- context_id=2 (IJLDAI), setting_value='1' (true)

-- Check if site-wide data exists
SELECT context_id, setting_name, LENGTH(setting_value) as data_size
FROM plugin_settings
WHERE plugin_name = 'backlinktrackerplugin'
  AND context_id = 0
  AND setting_name IN ('backlinkData', 'redirectMappingData');

-- Should show:
-- context_id=0, setting_name='backlinkData', data_size=<some large number>
```

#### Step 3: Re-upload Data with Site-Wide Mode

If site-wide data doesn't exist (context_id=0 has no data):

1. **Go to JLSR settings**
2. **Uncheck** "Use site-wide data"
3. Save
4. **Check** "Use site-wide data" again
5. Save
6. **Upload your backlinks CSV** (this will save to context_id=0)
7. **Upload your redirect mapping CSV** (this will save to context_id=0)

**Then on IJLDAI:**
1. Open plugin settings
2. Verify "Use site-wide data" is checked
3. Save
4. Visit an article page
5. Backlinks should now appear

#### Step 4: Clear Browser Cache

Sometimes the issue is browser caching:

1. Hard refresh the article page (Ctrl+Shift+R or Cmd+Shift+R)
2. Or clear browser cache completely
3. Reload article page

#### Step 5: Check JavaScript Console

1. Open article page on IJLDAI
2. Open browser Developer Tools (F12)
3. Go to Console tab
4. Look for errors related to backlink loading
5. Check Network tab for the AJAX request to `/backlink/fetch`
   - Should return JSON with `total`, `domains`, `grouped_links`
   - If `total: 0`, data isn't being retrieved

### Common Issues and Solutions

#### Issue: Locale Keys Showing as ##...##

**Symptoms:**
```
##plugins.generic.backlinkTracker.settings.useSiteWideData##
```

**Solution:**
- Added `getLocaleFilename()` method to plugin (already fixed in latest version)
- Clear OJS cache: Delete contents of `/cache/` directory
- Reload plugin settings page

#### Issue: Settings Page Won't Load

**Symptoms:**
- "Failed Ajax request or invalid JSON returned"
- FBV error in logs

**Solution:**
- Fixed checkbox FBV format (already done in latest version)
- Checkbox now has `list=true` attribute in template

#### Issue: Backlink Count Wrong

**Symptoms:**
- Shows "206 references" but should show "7"
- Query strings and HTTP/HTTPS not deduplicated

**Solution:**
- Fixed deduplication logic (already done)
- Count now calculated AFTER deduplication
- HTTP and HTTPS normalized to same URL

### Testing Site-Wide Mode

**Create Test Scenario:**

1. **Upload test data to Journal A:**
   ```csv
   ascore,source_title,source_url,target_url,anchor
   50,Test Source,https://example.com/test,https://journal.thelawbrigade.com/jlsr/article/view/123,Test Link
   ```

2. **Check Journal A:**
   - Visit article ID 123
   - Should see 1 backlink from example.com

3. **Enable site-wide on Journal B:**
   - Go to IJLDAI settings
   - Check "Use site-wide data"
   - Save

4. **Check Journal B:**
   - Visit ANY article
   - Should see same backlink if target URL matches any IJLDAI article

### Debug Mode (If Still Not Working)

Add this to BacklinkHandler.inc.php temporarily to debug:

```php
// In getBacklinksForUrls() method, add at line 164:
error_log("DEBUG: contextId=$contextId");
error_log("DEBUG: useSiteWide=" . $this->_plugin->getSetting($contextId, 'useSiteWideData'));
error_log("DEBUG: allBacklinks count=" . count($allBacklinks ?: []));
```

Then check your PHP error log when loading an article page.

### Still Not Working?

If after all these steps it's still not working:

**Option 1: Use Per-Journal Mode**
- Uncheck "Use site-wide data" on all journals
- Upload CSV separately to each journal
- Less convenient but guaranteed to work

**Option 2: Manual Database Fix**

If you're comfortable with SQL:

```sql
-- Copy data from JLSR (context_id=1) to site-wide (context_id=0)
INSERT INTO plugin_settings (plugin_name, context_id, setting_name, setting_value, setting_type)
SELECT 'backlinktrackerplugin', 0, setting_name, setting_value, setting_type
FROM plugin_settings
WHERE plugin_name = 'backlinktrackerplugin'
  AND context_id = 1
  AND setting_name IN ('backlinkData', 'backlinkDataStatus', 'backlinkDataCount', 'backlinkDataDate', 'redirectMappingData', 'redirectMappingCount', 'redirectMappingDate')
ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value);
```

### Expected Behavior

**When Site-Wide Mode Works Correctly:**

1. JLSR has site-wide enabled, uploads CSV
2. Data saved to context_id=0 (site-wide)
3. IJLDAI enables site-wide mode
4. IJLDAI reads from context_id=0
5. Both journals show same backlink data
6. Updating CSV on EITHER journal updates for BOTH

**Settings That Remain Per-Journal:**
- Repository Galley Label
- Blocked Domains
- Enable/Disable site-wide mode toggle

**Settings That Are Shared:**
- Backlink data (the actual CSV content)
- Redirect mappings

---

## Contact

If issues persist after trying all troubleshooting steps, you may need to:
1. Check OJS error logs: `/path/to/ojs/error.log`
2. Check Apache/Nginx error logs
3. Verify database permissions
4. Check if plugin is properly registered in OJS
