# New Features Guide - Backlink Tracker Plugin

## Overview

This guide covers two new features added to the Backlink Tracker Plugin:

1. **Domain Filtering** - Block specific domains from appearing in backlink displays
2. **URL Redirect Mapping** - Handle URL migrations and redirects automatically

---

## Feature 1: Domain Filtering

### What It Does

Allows you to exclude backlinks from specific domains (e.g., spam sites, irrelevant referrers) from being displayed on article pages.

### How to Use

1. Go to **Settings → Website → Plugins**
2. Find "Backlink Tracker" and click **Settings**
3. Scroll to **Domain Filtering** section
4. Enter domains to block (one per line)
5. Click **Save**

### Example Configuration

```
spamsite.com
badactor.net
irrelevant-domain.org
```

### Important Notes

- **Subdomain Matching**: Blocking `example.com` will also block `www.example.com`, `blog.example.com`, etc.
- **Case Insensitive**: `SpamSite.com` and `spamsite.com` are treated the same
- **No Protocol Needed**: Enter just the domain name (e.g., `example.com`, not `https://example.com`)
- **Instant Effect**: Changes apply immediately after saving

### Example Use Cases

1. **Remove Spam Backlinks**: Block known spam domains
2. **Hide Internal References**: Block your own domains if needed
3. **Filter Low-Quality Sites**: Block domains with poor reputation

---

## Feature 2: URL Redirect Mapping

### What It Does

Automatically maps old URLs to new URLs after domain migration or site restructuring. This ensures backlinks pointing to old URLs are correctly attributed to the current article pages.

### The Problem It Solves

**Scenario:**
- Your old site: `jlsr.thelawbrigade.com`
- Your new site: `journal.thelawbrigade.com/jlsr`
- You have Cloudflare redirects from old → new
- SEMrush CSV still contains backlinks to **old URLs**
- Without mapping: These backlinks won't appear on article pages
- With mapping: Old URLs automatically matched to new article pages ✓

### How It Works

```
CSV Backlink Points To:
https://jlsr.thelawbrigade.com/article/my-article/
          ↓
Plugin Checks Redirect Mapping
          ↓
Finds Match:
https://journal.thelawbrigade.com/jlsr/article/view/123
          ↓
Displays Backlink on Article #123 ✓
```

### Step-by-Step Setup

#### Step 1: Create Redirect Mapping CSV

Create a CSV file with two columns:

| Column 1 | Column 2 |
|----------|----------|
| old_url  | new_url  |

**Example CSV Content:**

```csv
old_url,new_url
https://jlsr.thelawbrigade.com/article/constitutional-law-review/,https://journal.thelawbrigade.com/jlsr/article/view/227
https://jlsr.thelawbrigade.com/article/criminal-law-analysis/,https://journal.thelawbrigade.com/jlsr/article/view/228
https://jlsr.thelawbrigade.com/article/civil-procedure-study/,https://journal.thelawbrigade.com/jlsr/article/view/229
```

**Important CSV Rules:**

- ✅ First row can be header (`old_url,new_url`) or data
- ✅ URLs must be complete (include `https://`)
- ✅ No trailing slashes needed (plugin normalizes automatically)
- ✅ Query parameters are ignored (`?param=value`)
- ❌ Don't include fragments (`#section`)

#### Step 2: Upload Redirect Mapping

1. Go to **Settings → Website → Plugins**
2. Find "Backlink Tracker" and click **Settings**
3. Scroll to **URL Redirect Mapping** section
4. Click **Choose File** and select your CSV
5. Click **Upload Redirect Mapping**
6. Wait for success message
7. Close settings window

#### Step 3: Upload Backlinks CSV (as usual)

1. Export backlinks from SEMrush/Ahrefs/GSC
2. Upload using **Upload Backlinks Data** section
3. Plugin will automatically apply redirect mappings
4. Visit article pages to see matched backlinks

### Sample Redirect Mapping File

A sample file (`REDIRECT_MAPPING_SAMPLE.csv`) is included in the plugin folder for reference.

### How to Generate Redirect Mapping CSV

**Option 1: Manual Creation (Small Sites)**

Create Excel/CSV with two columns:
1. List all old article URLs
2. Manually match to new OJS article URLs

**Option 2: Database Query (Large Sites)**

If you have access to OJS database, you can generate mappings:

```sql
-- Example query (adjust for your setup)
SELECT
  CONCAT('https://jlsr.thelawbrigade.com/article/', old_slug, '/') as old_url,
  CONCAT('https://journal.thelawbrigade.com/jlsr/article/view/', article_id) as new_url
FROM article_migration_table;
```

**Option 3: Script/Tool**

Write a script to crawl your old site and generate mappings based on redirects.

### Managing Redirect Mappings

**View Current Status:**
- Settings form shows total mappings and last updated date

**Update Mappings:**
- Upload new CSV to completely replace old mappings
- Mappings are permanent (stored in database)

**Clear All Mappings:**
- Click "Clear All Redirect Mappings" button
- Confirms before deleting

### Important Notes

1. **Permanent Storage**: Mappings are stored permanently in the database
2. **Full Replace**: Each upload replaces all previous mappings
3. **No Merge Mode**: Cannot add to existing mappings (must re-upload complete list)
4. **Case Insensitive**: URLs are normalized to lowercase for matching
5. **Applies to All Uploads**: Once set, mappings apply to all future backlink CSV uploads
6. **No Performance Impact**: Mapping lookup is instant (uses PHP associative array)

### Troubleshooting

**Problem: Backlinks still not showing after mapping**

Solutions:
1. Verify CSV format is correct (two columns, complete URLs)
2. Check that old URLs in mapping exactly match URLs in backlink CSV
3. Re-upload backlink CSV after uploading mappings
4. Check browser console for JavaScript errors

**Problem: Wrong articles showing backlinks**

Solutions:
1. Review redirect mapping CSV for incorrect new URLs
2. Ensure new URLs point to correct OJS article IDs
3. Clear and re-upload corrected mapping

**Problem: Upload fails**

Solutions:
1. Ensure file is CSV format (not Excel)
2. Check for special characters in URLs
3. Verify no empty rows in CSV

---

## Combined Usage Example

### Scenario: Journal Migration + Spam Filtering

**Your Setup:**
- Migrated from `old-domain.com` to `new-domain.com`
- Want to block spam domains from displaying

**Steps:**

1. **Create redirect mapping CSV:**
   ```csv
   old_url,new_url
   https://old-domain.com/article/123,https://new-domain.com/article/view/456
   https://old-domain.com/article/124,https://new-domain.com/article/view/457
   ```

2. **Upload redirect mapping** in plugin settings

3. **Configure domain filtering:**
   ```
   spamsite.com
   badactor.net
   ```

4. **Upload backlinks CSV** from SEMrush

5. **Result:**
   - Backlinks to old URLs → Matched to new articles ✓
   - Spam domains → Filtered out ✓
   - Clean, accurate backlink display ✓

---

## Database Storage

### Domain Filtering
- **Setting Key**: `blockedDomains`
- **Format**: Plain text (newline-separated)
- **Location**: `plugin_settings` table

### URL Redirect Mapping
- **Setting Keys**:
  - `redirectMappingData` (JSON)
  - `redirectMappingCount` (int)
  - `redirectMappingDate` (timestamp)
- **Format**: JSON object `{"old_url": "new_url"}`
- **Location**: `plugin_settings` table

---

## Technical Details

### URL Normalization

Both features use consistent URL normalization:

1. Parse URL into components
2. Remove trailing slashes
3. Remove query parameters
4. Remove fragments
5. Convert to lowercase

**Example:**
```
Input:  https://Example.com/Article/123/?param=value#section
Output: https://example.com/article/123
```

### Matching Logic

**Redirect Mapping (applied first):**
```
For each backlink:
  1. Normalize target_url
  2. Check if exists in redirect mapping
  3. If yes → Replace with new URL
  4. Match against article URLs
```

**Domain Filtering (applied last):**
```
For each matched backlink:
  1. Extract source domain
  2. Check against blocked domains
  3. If blocked → Exclude from display
  4. If not blocked → Include
```

### Performance

- **Redirect Mapping**: O(1) lookup (PHP associative array)
- **Domain Filtering**: O(n×m) where n=backlinks, m=blocked domains
- **No Impact**: Both features add negligible processing time

---

## FAQ

**Q: Can I use wildcards in domain filtering?**
A: Not currently. Use the base domain (e.g., `example.com` blocks all subdomains).

**Q: Can I merge new redirect mappings with existing ones?**
A: No. Each upload replaces all mappings. Keep a master CSV file.

**Q: Do redirect mappings affect backlink CSV uploads?**
A: No. Mappings are applied during display, not storage. Original CSV data is preserved.

**Q: Can I export redirect mappings?**
A: Not currently. Keep your CSV file as a backup.

**Q: What happens if a URL is in the mapping but not in backlinks?**
A: Nothing. Unused mappings are harmless.

**Q: Can I map multiple old URLs to one new URL?**
A: Yes! Multiple old URLs can point to the same new article.

**Q: How many redirect mappings can I have?**
A: Tested with 1000+ mappings. No practical limit.

---

## Best Practices

1. **Keep Master CSV**: Always maintain your redirect mapping CSV file
2. **Document Changes**: Track which old URLs map to which new URLs
3. **Test Small First**: Upload 5-10 mappings first to verify format
4. **Regular Updates**: Update mappings when adding new articles
5. **Backup Database**: Before major changes, backup plugin settings
6. **Monitor Results**: Check article pages after uploading to verify

---

## Version History

**Version 1.1.0** (December 2024)
- Added domain filtering feature
- Added URL redirect mapping feature
- Updated locale files
- Enhanced backlink matching logic

---

## Support

For issues or questions:
1. Check this guide first
2. Review main README.md
3. Check OJS forum: https://forum.pkp.sfu.ca/
4. Review plugin code in `BacklinkHandler.inc.php` (lines 200-204 for redirect mapping)

---

## License

GNU General Public License v3.0
