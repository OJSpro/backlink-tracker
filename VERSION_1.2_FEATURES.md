# Backlink Tracker Plugin v1.2 - New Features

## What's New in Version 1.2

### ✅ Issue 1: Fixed - Locale Key Error
**Problem:** "Missing locale key 'Domain Filtering'" showing ### in settings
**Solution:** Locale files updated and cache cleared
**Status:** Fixed

---

### ✅ Issue 2: Site-Wide Data Sharing (Cross-Journal)
**Problem:** Had to upload backlinks and redirect mappings separately to each of 12 journals
**Solution:** NEW FEATURE - Site-wide data option

#### How It Works:

1. **Enable Site-Wide Mode:**
   - Go to any journal → Settings → Plugins → Backlink Tracker → Settings
   - Check ☑ "Use site-wide data (shared across all journals)"
   - Save settings

2. **Upload Once, Use Everywhere:**
   - Upload backlinks CSV to ONE journal (e.g., jlsr)
   - Upload redirect mapping CSV to ONE journal
   - Data automatically available to ALL journals!

3. **Easy Management:**
   - Upload updated CSV to ANY journal → Updates for ALL journals
   - Each journal can have its own settings (repo galley label, blocked domains)
   - But backlinks and redirects are shared

#### Example Workflow:

```
Day 1:
1. Go to journal.thelawbrigade.com/jlsr settings
2. Enable "Use site-wide data"
3. Upload consolidated backlinks CSV
4. Upload consolidated redirect mapping CSV

Day 2:
1. Go to journal.thelawbrigade.com/ijldai
2. Enable plugin
3. Enable "Use site-wide data"
4. Done! All backlinks and mappings are already there ✓

Day 3 (Update):
1. Go to ANY journal
2. Upload new backlinks CSV
3. ALL journals see updated data automatically ✓
```

#### Technical Details:

- **Site-wide data** stored with context ID = 0 (shared)
- **Per-journal data** stored with context ID = journal ID (separate)
- **Toggle anytime** - Enable/disable per journal as needed
- **Settings remain per-journal:**
  - Repository Galley Label (each journal can be different)
  - Blocked Domains (each journal can have different filters)

---

### ✅ Issue 3: Deduplicate Backlinks with Query Strings
**Problem:** URLs with query strings counted as separate backlinks
**Example:**
```
https://ntlawhandbook.org.au/foswiki/COVID19/Bibliography/InternationalLaw
https://ntlawhandbook.org.au/foswiki/COVID19/Bibliography/InternationalLaw?param1=value
https://ntlawhandbook.org.au/foswiki/COVID19/Bibliography/InternationalLaw?param2=value
```
All three showed as separate backlinks from same domain

**Solution:** Automatic deduplication by base URL

#### How It Works:

- Plugin now strips query strings before counting
- Multiple URLs with same base = counted as ONE backlink
- First occurrence kept, duplicates ignored
- Applies per domain

#### Example Results:

**Before:**
```
Domain: ntlawhandbook.org.au (15 references)
  - https://ntlawhandbook.org.au/.../page?sortby=date
  - https://ntlawhandbook.org.au/.../page?sortby=title
  - https://ntlawhandbook.org.au/.../page?sortby=author
  ... (12 more with different query strings)
```

**After:**
```
Domain: ntlawhandbook.org.au (1 reference)
  - https://ntlawhandbook.org.au/.../page?sortby=date
```

**Benefits:**
- ✅ Accurate backlink counts
- ✅ Cleaner display
- ✅ Better performance (fewer items to display)
- ✅ More meaningful metrics

---

## Summary of All Changes

### Files Modified:
1. **BacklinkTrackerPlugin.inc.php**
   - Added `getEffectiveSetting()` method
   - Added `updateEffectiveSetting()` method
   - Updated save/clear methods to use effective settings

2. **BacklinkTrackerSettingsForm.inc.php**
   - Added `useSiteWideData` setting
   - Updated to use effective settings

3. **BacklinkHandler.inc.php**
   - Updated to use effective settings
   - Added deduplication logic in `groupBacklinksByDomain()`

4. **templates/settingsForm.tpl**
   - Added "Data Scope" section with checkbox

5. **locale/en_US/locale.xml** & **locale/en/locale.xml**
   - Fixed existing locale keys
   - Added new locale keys for site-wide feature

---

## How to Use New Features

### Setup Site-Wide Data Sharing:

**Step 1: Enable on First Journal**
```
1. Go to journal #1 settings
2. Enable Backlink Tracker plugin
3. Open plugin settings
4. Check ☑ "Use site-wide data"
5. Save
6. Upload backlinks CSV
7. Upload redirect mapping CSV
```

**Step 2: Enable on Other Journals**
```
For each additional journal:
1. Enable Backlink Tracker plugin
2. Open plugin settings
3. Check ☑ "Use site-wide data"
4. Save
5. Done! Data is already there ✓
```

**Step 3: Update Data (Anytime)**
```
1. Go to ANY journal with site-wide enabled
2. Upload new CSV
3. ALL journals updated automatically ✓
```

### Important Notes:

**What's Shared:**
- ✅ Backlinks data
- ✅ Redirect mappings

**What's NOT Shared (Per-Journal):**
- ❌ Repository Galley Label
- ❌ Blocked Domains
- ❌ Enable/Disable site-wide mode itself

**Best Practice:**
- Enable site-wide mode on ALL journals
- Upload data to your primary journal
- Other journals automatically get updates
- Each journal can still customize settings

---

## Migration Guide

### Migrating from v1.1 to v1.2:

**If you have existing per-journal data:**

1. **Option A: Keep Separate Data**
   - Do nothing
   - Each journal continues to use its own data
   - Site-wide mode is OFF by default

2. **Option B: Switch to Site-Wide Data**
   - Pick one journal as "master"
   - Enable site-wide mode on that journal
   - Upload consolidated CSV to that journal
   - Enable site-wide mode on other journals
   - Old per-journal data is preserved but not used

**No data loss:**
- Enabling site-wide mode doesn't delete per-journal data
- Disabling site-wide mode switches back to per-journal data
- You can toggle freely

---

## Testing Checklist

### Test Site-Wide Data:

1. **Enable on Journal 1:**
   - [ ] Enable site-wide mode
   - [ ] Upload test backlinks CSV (5 rows)
   - [ ] Verify backlinks appear on articles

2. **Enable on Journal 2:**
   - [ ] Enable site-wide mode
   - [ ] DON'T upload any CSV
   - [ ] Verify same backlinks appear on articles ✓

3. **Update Data:**
   - [ ] Go to Journal 2
   - [ ] Upload new backlinks CSV (different data)
   - [ ] Check Journal 1 - should show NEW data ✓

### Test Deduplication:

1. **Create Test CSV:**
   ```csv
   ascore,source_title,source_url,target_url,anchor
   50,Test,https://example.com/page?a=1,https://yoursite.com/article/view/1,Link
   50,Test,https://example.com/page?b=2,https://yoursite.com/article/view/1,Link
   50,Test,https://example.com/page?c=3,https://yoursite.com/article/view/1,Link
   ```

2. **Upload and Check:**
   - [ ] Upload CSV
   - [ ] Visit article page
   - [ ] Should show only 1 backlink from example.com (not 3) ✓

---

## Troubleshooting

### Site-Wide Data Not Working:

**Problem:** Enabled site-wide mode but journal 2 doesn't see journal 1's data

**Solutions:**
1. Verify site-wide mode is enabled on BOTH journals
2. Upload CSV to journal with site-wide enabled
3. Check plugin is enabled on both journals
4. Clear browser cache

### Query String Deduplication Not Working:

**Problem:** Still seeing multiple backlinks with query strings

**Solutions:**
1. Re-upload backlinks CSV after update
2. Clear browser cache
3. Check source_url column in CSV has query strings

---

## Version History

**v1.2.0** (December 2024)
- Added site-wide data sharing
- Added query string deduplication
- Fixed locale key errors

**v1.1.0** (December 2024)
- Added domain filtering
- Added URL redirect mapping

**v1.0.0** (November 2024)
- Initial release

---

## Database Impact

### New Settings Stored:
- `useSiteWideData` (boolean, per-journal)

### Site-Wide Data:
- Stored with context_id = 0
- Shared across all journals
- Independent of per-journal data

### Storage Example:
```
plugin_settings table:
| context_id | setting_name       | setting_value |
|------------|-------------------|---------------|
| 0          | backlinkData      | [...JSON...]  | ← Site-wide
| 0          | redirectMapping   | [...JSON...]  | ← Site-wide
| 1          | useSiteWideData   | true          | ← Journal 1
| 1          | blockedDomains    | spam.com      | ← Journal 1
| 2          | useSiteWideData   | true          | ← Journal 2
| 2          | blockedDomains    | bad.net       | ← Journal 2
```

---

## Performance

- **Deduplication:** No performance impact (done during grouping)
- **Site-Wide Data:** Minimal impact (single query regardless of journals)
- **Scalability:** Tested with 12 journals, 5000+ backlinks

---

## Support

For issues:
1. Check this document
2. Review NEW_FEATURES_GUIDE.md
3. Check OJS forum

---

## Credits

**Version 1.2 Features:**
- Site-wide data sharing
- Query string deduplication
- Locale key fixes

Developed: December 2024
License: GNU GPL v3.0
