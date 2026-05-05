# Uninstall Guide - Backlink Tracker Plugin

This guide explains how to properly uninstall the Backlink Tracker plugin, what happens to your data, and how to reinstall if needed.

---

## Quick Overview

| Action | Frontend Display | Database Data | Plugin Files |
|--------|-----------------|---------------|--------------|
| **Deactivate** | Hidden | Kept | Kept |
| **Delete Files** | Removed | Kept (orphaned) | Removed |
| **Clean Uninstall** | Removed | Deleted | Removed |

---

## Option 1: Temporary Deactivation (Recommended)

Use this when you want to temporarily hide backlinks but keep your data.

### Steps to Deactivate

**Via OJS Admin:**
1. Log in to OJS as Administrator
2. Go to **Settings** → **Website** → **Plugins**
3. Find "Backlink Tracker" under Generic Plugins
4. **Uncheck** the checkbox next to the plugin
5. Plugin turns gray/inactive

### What Happens When Deactivated

✅ **Kept:**
- All backlink data in database
- Plugin files on server
- Plugin settings/configuration

❌ **Hidden:**
- "External References" section on article pages
- Plugin settings menu

### Reactivating After Deactivation

**To reactivate:**
1. Go to **Settings** → **Website** → **Plugins**
2. Find "Backlink Tracker"
3. **Check** the checkbox
4. Plugin reactivates immediately
5. All data reappears (no data loss!)

**Result:** Everything works exactly as before deactivation.

---

## Option 2: Remove Plugin Files Only

Use this when you want to remove the plugin but might reinstall later.

### Steps to Remove Files

**Method A: Via cPanel**
1. Log in to cPanel
2. Open File Manager
3. Navigate to: `public_html/plugins/generic/`
4. Find the `backlinkTracker` folder
5. Right-click → Delete
6. Confirm deletion

**Method B: Via FTP**
1. Connect to your server via FTP
2. Navigate to: `/plugins/generic/`
3. Delete the `backlinkTracker` folder

### What Happens When Files Deleted

✅ **Kept:**
- All backlink data in database (orphaned)
- Plugin settings in database

❌ **Removed:**
- All plugin files
- "External References" section (code no longer exists)
- Plugin from settings menu

⚠️ **Important:** Data remains in database but is "orphaned" (not used by anything).

### Reinstalling After File Deletion

**To reinstall:**
1. Re-upload plugin files (same location)
2. Go to **Settings** → **Website** → **Plugins**
3. Enable "Backlink Tracker"
4. **All your old data automatically reappears!**

**Result:** No data loss. Plugin reconnects to existing database data.

---

## Option 3: Clean Uninstall (Complete Removal)

Use this when you're permanently removing the plugin and want to clean everything.

### Part A: Remove Plugin Files

Follow steps from "Option 2: Remove Plugin Files Only" above.

### Part B: Clean Database Data

⚠️ **Warning:** This permanently deletes all backlink data. This cannot be undone!

**Method 1: Via OJS Database Admin (If Available)**

Some OJS installations have database admin tools. If yours does:
1. Go to database administration interface
2. Find table: `plugin_settings`
3. Run this query:
   ```sql
   DELETE FROM plugin_settings 
   WHERE plugin_name = 'backlinktrackerplugin';
   ```
4. Data is permanently deleted

**Method 2: Via phpMyAdmin (Most Common)**

1. Log in to cPanel
2. Find and click "phpMyAdmin"
3. On the left, select your OJS database
4. Click "SQL" tab at the top
5. Paste this query:
   ```sql
   DELETE FROM plugin_settings 
   WHERE plugin_name = 'backlinktrackerplugin';
   ```
6. Click "Go" button
7. You should see: "Query OK, X rows affected"

**Method 3: Via Command Line (SSH Access)**

If you have SSH access:
```bash
mysql -u your_db_user -p your_db_name
```

Then run:
```sql
DELETE FROM plugin_settings 
WHERE plugin_name = 'backlinktrackerplugin';
exit;
```

### What Gets Deleted from Database

The following settings are removed:
- `backlinkData` - All uploaded backlink records
- `backlinkDataStatus` - Status flag
- `backlinkDataCount` - Backlink count
- `backlinkDataDate` - Last upload date
- `repoGalleyLabel` - Configuration setting

### After Clean Uninstall

✅ **Removed:**
- All plugin files
- All plugin data from database
- All plugin settings
- "External References" section

❌ **Not Affected:**
- Your articles
- Other plugins
- Journal settings
- OJS core functionality

---

## Option 4: Upgrade/Replace Plugin

Use this when installing a newer version of the plugin.

### Method A: Upgrade in Place (Recommended)

**Steps:**
1. **Deactivate** the plugin (don't delete)
2. **Backup** current plugin folder (optional but recommended)
3. **Delete** old plugin files via cPanel/FTP
4. **Upload** new plugin files (same location)
5. **Enable** the plugin
6. Go to plugin settings, verify configuration
7. **Data persists automatically!**

### Method B: Clean Install New Version

If you want to start fresh:
1. **Export/backup** your current backlink CSV (if you still have it)
2. Follow "Option 3: Clean Uninstall" above
3. Install new plugin version
4. Re-upload your CSV file
5. Configure settings

---

## Data Backup Before Uninstall

### Why Backup?

Even though data stays in database after file deletion, it's good practice to backup:
- In case of accidental database cleanup
- To have CSV copy for future use
- For migration to another journal

### How to Backup Backlink Data

**Option 1: Keep Original CSV**
- Simply keep the CSV file you uploaded
- This is your backup!
- Can re-upload anytime

**Option 2: Export from Database (Advanced)**

Via phpMyAdmin:
1. Go to phpMyAdmin
2. Select your OJS database
3. Click "SQL" tab
4. Run this query:
   ```sql
   SELECT setting_value 
   FROM plugin_settings 
   WHERE plugin_name = 'backlinktrackerplugin' 
   AND setting_name = 'backlinkData';
   ```
5. Copy the JSON result and save to a text file
6. This is your data backup (JSON format)

**Option 3: Database Export**

Export entire database:
1. In phpMyAdmin, select database
2. Click "Export" tab
3. Choose "Quick" method
4. Click "Go"
5. SQL file downloads (contains everything)

---

## Reinstalling After Uninstall

### Scenario 1: Files Deleted, Data Intact

**What to do:**
1. Re-upload plugin files
2. Enable plugin
3. Done! Data automatically appears

**Data status:** ✅ All backlinks preserved

### Scenario 2: Complete Clean Uninstall

**What to do:**
1. Re-upload plugin files
2. Enable plugin
3. Configure settings
4. Upload CSV file again
5. Done!

**Data status:** ❌ Must re-upload CSV

### Scenario 3: Migrating to New OJS Installation

**What to do:**
1. Install plugin on new OJS
2. Configure settings
3. Upload your CSV file
4. Done!

**Data status:** ⚠️ Must re-upload CSV (data doesn't migrate)

---

## Troubleshooting Uninstall

### Problem: Can't Delete Plugin Files

**Error:** Permission denied

**Solution:**
1. In cPanel File Manager, right-click folder
2. Choose "Change Permissions"
3. Set to 777 (or enable all checkboxes)
4. Try deleting again
5. If still fails, contact hosting support

### Problem: Plugin Still Appears After Deletion

**Cause:** Cache not cleared

**Solution:**
1. Go to **Administration** → **Clear Data Caches**
2. Refresh your browser (Ctrl+F5)
3. Clear browser cache
4. Check plugins list again

### Problem: Database Query Fails

**Error:** "Access denied" or "Cannot delete"

**Solution:**
1. Check database user has DELETE permissions
2. Try via phpMyAdmin instead of command line
3. Contact your hosting support
4. Or simply leave data orphaned (harmless)

### Problem: Data Reappears After Deletion

**Cause:** Deleted files but not database

**Solution:**
- This is normal if you only deleted files
- Follow "Option 3: Clean Uninstall" to remove database data
- Or leave it (doesn't affect performance)

---

## Impact on Your Journal

### What's NOT Affected by Uninstall

✅ **Safe:**
- Your articles and publications
- Other plugins
- Journal settings
- User accounts
- Subscriptions
- Issues and volumes
- All core OJS functionality

### What IS Affected

❌ **Removed:**
- "External References" section on article pages
- Backlink display
- Plugin settings menu

---

## Alternative: Hide Instead of Uninstall

Don't want to uninstall but want to hide backlinks temporarily?

### Option: CSS Hide

Add this to your theme's CSS:
```css
#backlinkTracker {
    display: none !important;
}
```

**Result:**
- Backlinks hidden on frontend
- Plugin still active
- Data still in database
- Easy to unhide later (remove CSS)

---

## Frequently Asked Questions

### Q: Will uninstalling affect my article URLs?
**A:** No. Plugin only displays data, doesn't change URLs.

### Q: Can I reinstall and keep my data?
**A:** Yes! If you only delete files (not database), data persists.

### Q: How much database space does the plugin use?
**A:** Typically 1-2 MB for 5,000 backlinks. Negligible impact.

### Q: Should I clean database after uninstall?
**A:** Optional. Orphaned data is harmless but cleaning keeps database tidy.

### Q: Can I export my backlink data before uninstall?
**A:** Keep your original CSV file - that's your backup! Or export from database (see "Data Backup" section).

### Q: What if I accidentally deleted everything?
**A:** No problem! Just reinstall plugin and re-upload your CSV.

### Q: Does deactivation affect performance?
**A:** No. Deactivated plugins don't run at all (zero impact).

---

## Best Practices

### Before Uninstalling:

1. ✅ **Backup your CSV** file
2. ✅ **Screenshot** your settings
3. ✅ **Document** your configuration (galley label, etc.)
4. ✅ **Test** deactivation first (instead of deletion)
5. ✅ **Consider** just hiding CSS instead

### During Uninstall:

1. ✅ **Deactivate** first, then delete files
2. ✅ **Double-check** you're deleting correct folder
3. ✅ **Backup** database if doing clean uninstall
4. ✅ **Clear cache** after changes

### After Uninstall:

1. ✅ **Verify** article pages load correctly
2. ✅ **Check** other plugins still work
3. ✅ **Test** journal functionality
4. ✅ **Keep** CSV file safe for future use

---

## Support

If you encounter issues during uninstall:

1. ✅ Re-read this guide
2. ✅ Check Troubleshooting section
3. ✅ Review OJS forum: https://forum.pkp.sfu.ca/
4. ✅ Contact your system administrator
5. ✅ Check OJS docs: https://docs.pkp.sfu.ca/

---

## Summary Table

| Task | Frontend | Database | Files | Reversible? |
|------|----------|----------|-------|-------------|
| Deactivate | Hidden | Kept | Kept | ✅ Yes (reactivate) |
| Delete Files | Removed | Kept | Removed | ✅ Yes (re-upload) |
| Clean Uninstall | Removed | Deleted | Removed | ❌ No (must re-upload CSV) |
| CSS Hide | Hidden | Kept | Kept | ✅ Yes (remove CSS) |

---

## Conclusion

The Backlink Tracker plugin is designed to be:
- ✅ Easy to install
- ✅ Easy to uninstall
- ✅ Safe to remove (no core changes)
- ✅ Reversible (data persists)

**Recommendation:** Deactivate first before deleting. This way you can easily reactivate if needed!

---

**Plugin Version:** 1.0.0  
**Last Updated:** November 2025  
**Compatible:** OJS 3.4+
