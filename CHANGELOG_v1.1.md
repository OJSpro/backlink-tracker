# Changelog - Backlink Tracker Plugin v1.1

## Version 1.1.0 - December 11, 2024

### New Features

#### 1. Domain Filtering
- Added ability to block specific domains from backlink display
- Supports exact domain matching and subdomain matching
- Case-insensitive domain comparison
- Configuration via settings form (textarea, one domain per line)

**Files Modified:**
- `BacklinkTrackerSettingsForm.inc.php` - Added `blockedDomains` setting
- `BacklinkHandler.inc.php` - Added `applyDomainFiltering()` method
- `templates/settingsForm.tpl` - Added domain filtering UI
- `locale/en_US/locale.xml` - Added locale strings
- `locale/en/locale.xml` - Added locale strings

#### 2. URL Redirect Mapping
- Automatically maps old URLs to new URLs after domain migration
- CSV upload for redirect mappings (old_url,new_url format)
- Permanent storage in database as JSON
- Applies during backlink matching phase
- Supports unlimited mappings

**Files Modified:**
- `BacklinkTrackerPlugin.inc.php` - Added:
  - `uploadRedirectMapping()` method
  - `clearRedirectMapping()` method
  - `parseRedirectMappingFile()` method
  - `saveRedirectMappings()` method
  - `normalizeUrl()` helper method
- `BacklinkTrackerSettingsForm.inc.php` - Added redirect mapping settings
- `BacklinkHandler.inc.php` - Modified `getBacklinksForUrls()` to apply redirect mappings
- `templates/settingsForm.tpl` - Added redirect mapping upload UI and JavaScript handlers
- `locale/en_US/locale.xml` - Added locale strings
- `locale/en/locale.xml` - Added locale strings

### Technical Implementation

#### URL Normalization
- Removes trailing slashes
- Strips query parameters
- Case-insensitive comparison
- Consistent across all matching operations

#### Matching Flow
1. Load backlinks from database
2. Load redirect mappings
3. For each backlink:
   - Normalize target URL
   - Apply redirect mapping if exists
   - Match against article URLs
   - Add target type (Abstract/Full Text)
4. Apply domain filtering
5. Group by domain
6. Return to frontend

### Database Changes

New plugin settings added:
- `blockedDomains` (string) - Newline-separated list of domains
- `redirectMappingData` (string/JSON) - JSON object of URL mappings
- `redirectMappingCount` (int) - Count of mappings
- `redirectMappingDate` (string) - Last upload timestamp

### Files Added
- `REDIRECT_MAPPING_SAMPLE.csv` - Sample redirect mapping file
- `NEW_FEATURES_GUIDE.md` - Comprehensive documentation for new features
- `CHANGELOG_v1.1.md` - This file

### Files Modified

1. **BacklinkTrackerPlugin.inc.php**
   - Lines 74-107: Added redirect mapping handlers in `manage()` method
   - Lines 152-264: Added redirect mapping upload/clear/parse methods
   - Added URL normalization helper

2. **BacklinkTrackerSettingsForm.inc.php**
   - Lines 43-52: Added new settings to `initData()`
   - Lines 58-62: Added to `readInputData()`
   - Lines 77-79: Added to `execute()`

3. **BacklinkHandler.inc.php**
   - Lines 51-58: Added redirect mapping and domain filtering to `fetch()`
   - Lines 163-217: Modified `getBacklinksForUrls()` to apply redirect mappings
   - Lines 237-283: Added `applyDomainFiltering()` method

4. **templates/settingsForm.tpl**
   - Lines 38-72: Added domain filtering and redirect mapping UI
   - Lines 140-200: Added JavaScript handlers for upload/clear operations
   - Lines 204-210: Updated CSS for mapping status display

5. **locale/en_US/locale.xml** and **locale/en/locale.xml**
   - Fixed corrupted locale files
   - Added new locale keys for features

### Backward Compatibility

✅ **Fully backward compatible**
- Existing backlinks continue to work
- No database migration required
- New features are optional
- Default behavior unchanged

### Testing Recommendations

1. **Domain Filtering:**
   - Add test domains to blocked list
   - Verify they don't appear on article pages
   - Test subdomain matching
   - Test case-insensitivity

2. **Redirect Mapping:**
   - Create small test CSV (3-5 mappings)
   - Upload and verify success message
   - Upload backlink CSV with old URLs
   - Verify backlinks appear on correct new articles
   - Test clear functionality

3. **Combined Usage:**
   - Use both features together
   - Verify they don't interfere
   - Check performance with large datasets

### Known Limitations

1. **Domain Filtering:**
   - No wildcard support (use base domain for subdomains)
   - No regex patterns

2. **Redirect Mapping:**
   - No merge mode (full replace only)
   - No export functionality (keep CSV backup)
   - CSV only (no Excel support for redirect mapping)

### Performance Impact

- **Domain Filtering**: Negligible (O(n×m) where n=backlinks, m=blocked domains)
- **Redirect Mapping**: Negligible (O(1) lookup using PHP associative array)
- **Memory**: ~1-2 KB per 100 redirect mappings
- **Database**: Minimal additional storage

### Migration Guide

#### Upgrading from v1.0 to v1.1

1. **Backup:** Export any important data
2. **Replace Files:** Copy all updated files to plugin directory
3. **Clear Cache:** Clear OJS cache if needed
4. **Test:** Verify existing backlinks still work
5. **Configure:** Set up new features as needed

No database migration required.

### Support

- Documentation: See `NEW_FEATURES_GUIDE.md`
- Sample Files: See `REDIRECT_MAPPING_SAMPLE.csv`
- Original Docs: See `README.md`, `INSTALLATION_GUIDE.md`

---

## Version 1.0.0 - November 2024

Initial release with:
- CSV upload functionality
- Dual URL matching (OJS + Repository)
- Grouped display by domain
- Reference target detection
- Mobile responsive design
