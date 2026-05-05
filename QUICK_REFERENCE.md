# Quick Reference Card - Backlink Tracker v1.1

## Access Settings
```
OJS Admin → Settings → Website → Plugins → Backlink Tracker → ▼ → Settings
```

---

## Feature 1: Domain Filtering

### Purpose
Block spam/unwanted domains from backlink display

### Configuration
```
Domain Filtering field (textarea):
spamsite.com
badactor.net
unwanted-domain.org
```

### Rules
- One domain per line
- Blocks subdomains automatically
- Case insensitive
- No protocol needed (just domain)

### Example
```
Block: example.com
Result: Blocks www.example.com, blog.example.com, etc.
```

---

## Feature 2: URL Redirect Mapping

### Purpose
Map old URLs to new URLs after migration

### CSV Format
```csv
old_url,new_url
https://old-domain.com/article/slug/,https://new-domain.com/article/view/123
```

### Rules
- Two columns: old_url, new_url
- Complete URLs (include https://)
- First row can be header or data
- No trailing slashes needed

### Upload Steps
1. Create CSV with mappings
2. Settings → URL Redirect Mapping
3. Choose File → Select CSV
4. Click "Upload Redirect Mapping"
5. Wait for success message

### View Status
- Total Mappings: Shows count
- Last Updated: Shows date

### Clear Mappings
- Click "Clear All Redirect Mappings"
- Confirm dialog
- Page reloads

---

## Your Specific Use Case

### Problem
SEMrush backlinks point to old domain:
```
https://jlsr.thelawbrigade.com/article/...
```

### Solution
Create redirect mapping CSV:
```csv
old_url,new_url
https://jlsr.thelawbrigade.com/article/constitutional-law/,https://journal.thelawbrigade.com/jlsr/article/view/227
https://jlsr.thelawbrigade.com/article/criminal-law/,https://journal.thelawbrigade.com/jlsr/article/view/228
```

Upload once → Works forever ✓

---

## Workflow

### Initial Setup
1. ✅ Upload redirect mapping CSV
2. ✅ Configure domain filtering (optional)
3. ✅ Upload backlink CSV from SEMrush

### Regular Updates
1. ✅ Export new backlinks from SEMrush
2. ✅ Upload via "Upload Backlinks Data"
3. ✅ Mappings apply automatically
4. ✅ Check article pages

### Maintenance
- Update redirect mapping: Re-upload complete CSV
- Update domain filter: Edit and save
- Clear data: Use clear buttons

---

## Common Tasks

### Add Blocked Domain
```
Settings → Domain Filtering → Add domain → Save
```

### Remove Blocked Domain
```
Settings → Domain Filtering → Delete line → Save
```

### Add New URL Mapping
```
Edit CSV → Add row → Re-upload complete CSV
```

### Check Current Settings
```
Settings → View "Total Mappings" and blocked domains
```

---

## Troubleshooting

### Backlinks not showing
- ✓ Check redirect mapping uploaded
- ✓ Verify URLs in mapping match backlink CSV
- ✓ Re-upload backlink CSV

### Domain still appearing
- ✓ Check spelling in domain filter
- ✓ Save settings after editing
- ✓ Clear browser cache

### Upload fails
- ✓ Verify CSV format (not Excel)
- ✓ Check for special characters
- ✓ Test with 2-3 rows first

---

## File Locations

```
Plugin Directory:
/plugins/generic/backlinkTracker/

Documentation:
- NEW_FEATURES_GUIDE.md (comprehensive)
- IMPLEMENTATION_SUMMARY.md (overview)
- TESTING_CHECKLIST.md (testing)
- CHANGELOG_v1.1.md (changes)
- QUICK_REFERENCE.md (this file)

Sample Files:
- REDIRECT_MAPPING_SAMPLE.csv
```

---

## Database Settings

Stored in `plugin_settings` table:
- `blockedDomains` (string)
- `redirectMappingData` (JSON)
- `redirectMappingCount` (int)
- `redirectMappingDate` (timestamp)

---

## URLs for Help

- OJS Forum: https://forum.pkp.sfu.ca/
- OJS Docs: https://docs.pkp.sfu.ca/

---

## Version Info

- **Plugin Version**: 1.1.0
- **Release Date**: December 11, 2024
- **OJS Compatibility**: 3.4+

---

## Key Points to Remember

1. **Redirect mappings are permanent** - Upload once, works forever
2. **Domain filtering is instant** - Changes apply immediately
3. **No performance impact** - Both features are optimized
4. **Fully backward compatible** - Old backlinks still work
5. **Keep CSV backup** - Save your redirect mapping file

---

## Emergency Rollback

If critical issues:
1. Disable plugin
2. Clear settings from database
3. Re-enable plugin
4. Contact support

---

**For detailed information, see NEW_FEATURES_GUIDE.md**
