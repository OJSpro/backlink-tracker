# Quick Setup Guide - Backlink Tracker v1.2

## New in v1.2: Site-Wide Data Sharing!

Perfect for multi-journal OJS installations like yours (12+ journals).

---

## 🚀 Quick Setup for Multi-Journal Site

### Step 1: Enable on First Journal (5 minutes)

1. Go to **journal.thelawbrigade.com/jlsr** (or any journal)
2. Settings → Website → Plugins
3. Find "Backlink Tracker" → Enable (check the box)
4. Click arrow → **Settings**
5. **Check ☑ "Use site-wide data (shared across all journals)"**
6. Set "Repository Galley Label": **REPO**
7. Set "Domain Filtering": (optional - add spam domains)
8. Click **Save**

### Step 2: Upload Your Data

1. **Upload Redirect Mapping CSV:**
   - Click "Choose File" under "URL Redirect Mapping"
   - Select your consolidated mapping CSV (all journals)
   - Click "Upload Redirect Mapping"
   - Wait for success message

2. **Upload Backlinks CSV:**
   - Click "Choose File" under "Upload Backlinks Data"
   - Select your consolidated backlinks CSV (all journals)
   - Click "Upload Backlinks"
   - Wait for success message

3. **Close Settings**

### Step 3: Enable on Other Journals (2 minutes each)

For **each** of your other journals (ijldai, etc.):

1. Go to that journal (e.g., **journal.thelawbrigade.com/ijldai**)
2. Settings → Website → Plugins
3. Find "Backlink Tracker" → Enable
4. Click arrow → **Settings**
5. **Check ☑ "Use site-wide data (shared across all journals)"**
6. Set any journal-specific settings (repo label, blocked domains)
7. Click **Save**
8. **Done!** Backlinks already there ✓

---

## 📊 What You Get

### With Site-Wide Mode Enabled:

✅ Upload CSV once → Available to ALL journals
✅ Update CSV once → Updates ALL journals
✅ One place to manage mappings
✅ Consistent data across all journals
✅ Save time managing 12+ journals

### Each Journal Can Still Have:

- Own repository galley label
- Own blocked domains list
- Enable/disable site-wide mode independently

---

## 🔄 Updating Data (Monthly/Quarterly)

### Option 1: Update via Any Journal

1. Go to **ANY** journal with site-wide enabled
2. Open plugin settings
3. Upload new CSV (backlinks or mapping)
4. ALL journals updated automatically ✓

### Option 2: Update via Specific Journal

1. Go to your "master" journal (e.g., jlsr)
2. Upload new CSV
3. Done!

---

## 📝 Your CSV Files

### Consolidated Backlinks CSV:
- Contains backlinks for ALL journals
- Format: SEMrush/Ahrefs export
- Columns: ascore, source_title, source_url, target_url, anchor

### Consolidated Redirect Mapping CSV:
- Contains old→new mappings for ALL journals
- Format: old_url, new_url
- Example:
```csv
old_url,new_url
https://jlsr.thelawbrigade.com/article/xyz/,https://journal.thelawbrigade.com/jlsr/article/view/227
https://ijldai.thelawbrigade.com/article/abc/,https://journal.thelawbrigade.com/ijldai/article/view/456
```

---

## ✨ New Feature: Query String Deduplication

**Automatic!** No setup needed.

**Before v1.2:**
```
example.com/page?sort=date
example.com/page?sort=title
example.com/page?sort=author
= 3 separate backlinks
```

**After v1.2:**
```
example.com/page
= 1 backlink (duplicates removed)
```

---

## 🔍 Testing Your Setup

### Test 1: Site-Wide Data Works

1. Enable site-wide on Journal A
2. Upload test CSV to Journal A
3. Visit article on Journal A → See backlinks ✓
4. Enable site-wide on Journal B
5. Visit article on Journal B → See backlinks ✓ (same data!)

### Test 2: Updates Propagate

1. Upload new CSV to Journal B
2. Check Journal A → Should see new data ✓

### Test 3: Per-Journal Settings Work

1. Journal A: Block domain "spam.com"
2. Journal B: Don't block anything
3. Visit articles on both
4. Journal A: spam.com hidden ✓
5. Journal B: spam.com visible ✓

---

## ⚠️ Important Notes

### Site-Wide Mode Requirements:

1. **Must enable on each journal individually**
   - Data is shared
   - But each journal must opt-in

2. **First upload creates shared data**
   - Upload to any journal first
   - Other journals see it after enabling

3. **Last upload wins**
   - If Journal A uploads, then Journal B uploads
   - Journal B's data overwrites Journal A's data
   - Affects all journals

### Best Practices:

1. **Designate one "master" journal**
   - Always upload to this journal
   - Reduces confusion

2. **Document your process**
   - Which journal is master?
   - How often to update?
   - Who has permission?

3. **Test with small dataset first**
   - Upload 10-20 backlinks initially
   - Verify it works across journals
   - Then upload full dataset

---

## 🐛 Troubleshooting

### "Domain Filtering" shows ###

**Solution:** Clear browser cache and reload page

### Site-wide data not showing

**Check:**
- [ ] Site-wide mode enabled on BOTH journals?
- [ ] Plugin enabled on both journals?
- [ ] Data uploaded to a journal with site-wide enabled?
- [ ] Browser cache cleared?

### Query strings still showing

**Note:** Only deduplicates within same domain
- URL base must be identical
- Query strings are stripped for comparison
- First occurrence kept

---

## 📧 Your Workflow Example

### Monthly Update Process:

```
Day 1: Export from SEMrush
- Export backlinks for all domains
- Save as: backlinks_dec2024.csv

Day 2: Upload to OJS
- Go to journal.thelawbrigade.com/jlsr
- Settings → Plugins → Backlink Tracker → Settings
- Upload backlinks_dec2024.csv
- Close settings

Day 3: Verify
- Check 2-3 journals
- Verify backlinks appear
- Verify counts look correct
- Done! ✓
```

---

## 🎯 Quick Reference

### File Formats:

**Backlinks CSV:**
```csv
ascore,source_title,source_url,target_url,anchor
50,Site Name,https://example.com,https://journal.../article/view/1,Link Text
```

**Redirect Mapping CSV:**
```csv
old_url,new_url
https://old-domain.com/article/slug/,https://journal.thelawbrigade.com/journal/article/view/ID
```

### Settings Location:

```
Any Journal → Settings → Website → Plugins → Backlink Tracker → ▼ → Settings
```

### Enable Site-Wide:

```
☑ Use site-wide data (shared across all journals)
```

---

## ✅ Setup Complete!

Once you:
1. ✅ Enable site-wide on all journals
2. ✅ Upload consolidated CSV files
3. ✅ Test on 2-3 journals

You're done! Your multi-journal backlink tracking is now centralized and easy to manage.

---

## Next Steps

1. **Set up monitoring:**
   - Check backlink counts monthly
   - Verify data looks correct
   - Update as needed

2. **Train team:**
   - Document your master journal
   - Share this guide
   - Set update schedule

3. **Optimize:**
   - Review blocked domains periodically
   - Update redirect mappings for new articles
   - Monitor query string deduplication

---

**Version:** 1.2.0
**Date:** December 2024
**For:** Multi-journal OJS installations
