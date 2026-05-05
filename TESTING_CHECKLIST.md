# Testing Checklist - Backlink Tracker v1.1

## Pre-Testing Setup

- [ ] Backup plugin settings from database
- [ ] Note current backlink count
- [ ] Have test CSV files ready

---

## Feature 1: Domain Filtering

### Basic Functionality

- [ ] Open plugin settings
- [ ] Verify "Domain Filtering" section is visible
- [ ] Enter test domain (e.g., `example.com`)
- [ ] Click Save
- [ ] Close settings modal
- [ ] Visit an article page with backlinks from that domain
- [ ] Verify those backlinks are NOT displayed
- [ ] Remove domain from filter
- [ ] Save again
- [ ] Verify backlinks reappear

### Advanced Testing

- [ ] **Multiple Domains**: Add 3-5 domains (one per line), verify all are filtered
- [ ] **Subdomain Matching**: Block `example.com`, verify `www.example.com` is also blocked
- [ ] **Case Insensitivity**: Add `Example.COM`, verify it blocks `example.com` links
- [ ] **Whitespace**: Add domains with trailing spaces, verify they still work
- [ ] **Empty Lines**: Add empty lines between domains, verify no errors
- [ ] **All Blocked**: Block all referring domains, verify "No external references" message

### Expected Results

✅ Blocked domains should not appear in:
- Domain group list
- Expanded backlink details
- Total count

✅ Non-blocked domains should still appear normally

---

## Feature 2: URL Redirect Mapping

### CSV File Preparation

Create test CSV: `test_redirect.csv`

```csv
old_url,new_url
https://jlsr.thelawbrigade.com/test-article-1/,https://journal.thelawbrigade.com/jlsr/article/view/[REAL_ARTICLE_ID_1]
https://jlsr.thelawbrigade.com/test-article-2/,https://journal.thelawbrigade.com/jlsr/article/view/[REAL_ARTICLE_ID_2]
```

**Note:** Replace `[REAL_ARTICLE_ID_1]` with actual article IDs from your OJS installation.

### Upload Testing

- [ ] Open plugin settings
- [ ] Verify "URL Redirect Mapping" section is visible
- [ ] Click "Choose File"
- [ ] Select test CSV file
- [ ] Click "Upload Redirect Mapping"
- [ ] Wait for green success message
- [ ] Verify message shows correct count (e.g., "2 URL redirect mappings")
- [ ] Close and reopen settings
- [ ] Verify "Total Mappings" shows correct count
- [ ] Verify "Last Updated" shows current date/time

### Mapping Application Testing

**Scenario:** Backlink CSV contains old URLs

- [ ] Create backlink CSV with old URLs:
  ```csv
  ascore,source_title,source_url,target_url,anchor
  50,Test Site,https://test.com,https://jlsr.thelawbrigade.com/test-article-1/,Click Here
  ```
- [ ] Upload this backlink CSV via "Upload Backlinks Data"
- [ ] Visit article page with ID matching the NEW URL (from redirect mapping)
- [ ] Verify backlink appears on that article page
- [ ] Expand domain group
- [ ] Verify backlink details are correct

### Edge Cases

- [ ] **URL Variations**: Test with/without trailing slashes
- [ ] **Query Parameters**: Test old URL with `?param=value`
- [ ] **HTTPS vs HTTP**: Test if both protocols work
- [ ] **Case Sensitivity**: Test `Example.com` vs `example.com`
- [ ] **Multiple Mappings to One Article**: Map 2+ old URLs to same new URL

### Clear Functionality

- [ ] Click "Clear All Redirect Mappings"
- [ ] Confirm the dialog
- [ ] Verify page reloads
- [ ] Verify mapping count shows 0
- [ ] Verify last updated date is gone
- [ ] Upload backlink CSV with old URLs
- [ ] Verify backlinks NO LONGER appear (because mappings cleared)

---

## Combined Feature Testing

### Scenario: Migration + Spam Filtering

- [ ] Upload redirect mapping CSV (old → new URLs)
- [ ] Add spam domains to domain filtering
- [ ] Upload backlink CSV containing:
  - Old URLs (should be mapped)
  - New URLs (should match directly)
  - Spam domain URLs (should be filtered)
- [ ] Visit multiple article pages
- [ ] Verify:
  - Old URL backlinks appear (via redirect mapping)
  - New URL backlinks appear (direct match)
  - Spam backlinks do NOT appear (filtered)
  - Counts are accurate
  - Grouping is correct

---

## Regression Testing (Ensure Old Features Still Work)

### Basic Backlink Display

- [ ] Upload standard backlink CSV (without old URLs)
- [ ] Verify backlinks appear on correct articles
- [ ] Verify domain grouping works
- [ ] Verify expand/collapse works
- [ ] Verify anchor text displays
- [ ] Verify reference target shows ("Abstract Page" vs "Full Text")

### Repository URL Matching

- [ ] Verify REPO galley URL setting still works
- [ ] Add REPO galley with WordPress URL to an article
- [ ] Verify backlinks to WordPress URL show as "Full Text"
- [ ] Verify backlinks to OJS URL show as "Abstract Page"

### Settings Form

- [ ] All fields visible and functional
- [ ] Save button works
- [ ] Upload buttons work
- [ ] Clear buttons work
- [ ] Status messages display correctly

---

## Performance Testing

- [ ] **Large Redirect Mapping**: Upload 100+ mappings, verify no errors
- [ ] **Large Blocked Domain List**: Add 20+ domains, verify filtering still fast
- [ ] **Large Backlink CSV**: Upload 1000+ backlinks, verify page loads quickly
- [ ] **Combined Load**: Use all features with large datasets

---

## Browser Compatibility

Test in multiple browsers:

- [ ] Chrome/Edge (latest)
- [ ] Firefox (latest)
- [ ] Safari (latest)
- [ ] Mobile browser (iOS/Android)

---

## Error Handling

### Invalid CSV Files

- [ ] Upload text file as redirect mapping → Expect error
- [ ] Upload CSV with wrong columns → Expect error
- [ ] Upload empty CSV → Expect error
- [ ] Upload CSV with malformed URLs → Should skip invalid rows

### Edge Cases

- [ ] Clear mappings when none exist → Should not error
- [ ] Clear blocked domains when empty → Should not error
- [ ] Save empty domain filter → Should clear filter
- [ ] Upload duplicate mappings → Should use last occurrence

---

## Data Persistence

- [ ] Upload redirect mapping
- [ ] Clear OJS cache
- [ ] Restart web server
- [ ] Verify mappings still exist
- [ ] Verify backlinks still match correctly

---

## Real-World Test Scenario

### Your Specific Use Case

1. **Prepare Redirect Mapping CSV**:
   - List all old `jlsr.thelawbrigade.com` URLs
   - Match to new `journal.thelawbrigade.com/jlsr` article IDs
   - Save as CSV

2. **Upload Redirect Mapping**:
   - [ ] Upload your actual redirect mapping CSV
   - [ ] Verify count matches your URL list

3. **Add Domain Filtering** (optional):
   - [ ] Add any spam/unwanted domains

4. **Upload Real Backlink Data**:
   - [ ] Export from SEMrush/Ahrefs
   - [ ] Upload via plugin
   - [ ] Wait for success message

5. **Verify Results**:
   - [ ] Visit 5-10 different article pages
   - [ ] Verify backlinks appear correctly
   - [ ] Check that old URLs are matched to new articles
   - [ ] Verify counts are reasonable
   - [ ] Check mobile display

---

## Troubleshooting Tests

If issues occur:

- [ ] Check browser console for JavaScript errors
- [ ] Check PHP error logs for backend errors
- [ ] Verify CSV file encoding (UTF-8)
- [ ] Test with minimal data first (2-3 mappings, 2-3 backlinks)
- [ ] Clear browser cache and retry

---

## Success Criteria

✅ **Domain Filtering Works If:**
- Blocked domains don't appear in backlink display
- Non-blocked domains still appear
- No errors in console

✅ **Redirect Mapping Works If:**
- Old URLs in backlink CSV match to correct new articles
- Upload shows success message with count
- Settings show correct mapping count and date

✅ **Combined Features Work If:**
- Both features can be used simultaneously
- No conflicts between features
- Performance is acceptable

---

## Final Verification

- [ ] All tests passed
- [ ] No console errors
- [ ] No PHP errors in logs
- [ ] Performance is acceptable
- [ ] Documentation is clear
- [ ] Ready for production use

---

## Rollback Plan (If Needed)

If critical issues found:

1. Disable plugin temporarily
2. Clear problematic settings from database:
   ```sql
   DELETE FROM plugin_settings
   WHERE plugin_name = 'backlinktrackerplugin'
   AND setting_name IN ('blockedDomains', 'redirectMappingData', 'redirectMappingCount', 'redirectMappingDate');
   ```
3. Re-enable plugin
4. Report issue details

---

## Post-Testing

- [ ] Document any issues found
- [ ] Note any improvements needed
- [ ] Update documentation if needed
- [ ] Train other admins on new features
- [ ] Schedule regular backlink data updates

---

## Notes

Use this space to record test results, issues, or observations:

```
Date: ___________
Tester: ___________

Test Results:
- Domain Filtering: PASS / FAIL / NOTES
- Redirect Mapping: PASS / FAIL / NOTES
- Combined Usage: PASS / FAIL / NOTES

Issues Found:
1.
2.

Performance Notes:
-

Recommendations:
-
```
