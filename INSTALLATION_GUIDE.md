# Installation Guide - Backlink Tracker Plugin

## For Beginners: Complete Step-by-Step Installation

This guide assumes no coding experience. Follow each step carefully.

---

## What You'll Need

- ✅ Access to your OJS website (as Administrator)
- ✅ Access to cPanel or FTP
- ✅ SEMrush, Ahrefs, or Google Search Console account
- ✅ 15-20 minutes of your time

**What you DON'T need:**
- ❌ API keys
- ❌ Programming knowledge
- ❌ Server command line access
- ❌ Composer or technical tools

---

## Part 1: Install the Plugin

### Method A: Using cPanel (Easiest)

**Step 1: Log in to cPanel**
1. Open your web browser
2. Go to your cPanel URL (usually: yoursite.com/cpanel)
3. Enter your cPanel username and password
4. Click "Log in"

**Step 2: Open File Manager**
1. Find the "Files" section in cPanel
2. Click on "File Manager"
3. A new window opens showing your files

**Step 3: Navigate to Plugins Folder**
1. In File Manager, look at the left sidebar
2. Click on "public_html" to expand it
3. Click on "plugins" folder
4. Click on "generic" folder
5. You should now see path: `public_html/plugins/generic/`

**Step 4: Create Plugin Folder**
1. At the top menu, click "+ Folder" button
2. In the popup, type: `backlinkTracker`
3. Click "Create New Folder"
4. You should see a new folder called "backlinkTracker"

**Step 5: Upload Plugin Files**
1. Double-click the "backlinkTracker" folder to open it
2. Click "Upload" button at the top
3. A new tab opens
4. Click "Select File" button
5. Select all plugin files from your computer:
   - BacklinkTrackerPlugin.inc.php
   - BacklinkTrackerSettingsForm.inc.php
   - BacklinkHandler.inc.php
   - index.php
   - version.xml
   - And all folders (templates, locale)
6. Wait for upload to complete (green checkmarks appear)
7. Close the upload tab
8. Go back to File Manager tab

**Step 6: Verify File Structure**

Your folder should look like this:
```
backlinkTracker/
├── BacklinkTrackerPlugin.inc.php
├── BacklinkTrackerSettingsForm.inc.php
├── BacklinkHandler.inc.php
├── index.php
├── version.xml
├── templates/
│   ├── settingsForm.tpl
│   └── backlinkDisplay.tpl
└── locale/
    ├── en_US/
    └── en/
```

---

### Method B: Using FTP

**Step 1: Connect to FTP**
1. Open your FTP client (FileZilla, etc.)
2. Enter your FTP credentials:
   - Host: your-site.com
   - Username: your FTP username
   - Password: your FTP password
   - Port: 21
3. Click "Connect"

**Step 2: Navigate and Upload**
1. On the right side (server), navigate to: `/plugins/generic/`
2. Create folder: `backlinkTracker`
3. Enter the folder
4. Drag and drop all plugin files from your computer (left side) to server (right side)
5. Wait for upload to complete

---

## Part 2: Enable the Plugin in OJS

**Step 1: Log in to OJS**
1. Go to your journal's website
2. Click "Login" (usually top right corner)
3. Enter your administrator username and password
4. Click "Sign In"

**Step 2: Navigate to Plugins**
1. At the top menu, click "Settings"
2. From dropdown, click "Website"
3. On the left sidebar, click "Plugins"
4. You'll see a list of plugins

**Step 3: Find and Enable**
1. Scroll down to "Generic Plugins" section
2. Look for "Backlink Tracker"
3. If you see it, great! If not, check Part 5 (Troubleshooting)
4. Check the checkbox next to "Backlink Tracker"
5. The row should turn green/highlighted
6. Plugin is now enabled!

---

## Part 3: Configure the Plugin

**Step 1: Open Settings**
1. Still on the Plugins page
2. Find "Backlink Tracker"
3. Click the blue arrow (▼) next to it
4. Click "Settings"
5. A popup window appears

**Step 2: Configure Repository Galley Label**

In the settings form:
1. Find "Repository Galley Label" field
2. Enter: `REPO`
   - Or whatever label you use for repository/full-text links
   - Common alternatives: `Repository`, `Full Text`, `Full-Text`
3. This tells the plugin which galley contains your WordPress/repository URL

**Step 3: Save Settings**
1. Scroll down in the popup
2. Click "Save" button
3. You should see a green success message
4. Close the popup window

✅ **Plugin is now installed and configured!**

---

## Part 4: Export and Upload Backlinks

### From SEMrush (Recommended)

**Step 1: Log in to SEMrush**
1. Go to: https://www.semrush.com/
2. Sign in with your account

**Step 2: Access Backlink Analytics**
1. In left sidebar, click "Link Building"
2. Click "Backlink Analytics"
3. Enter your domain (e.g., `yourjournal.org`)
4. Click "Search"
5. Wait for analysis to complete

**Step 3: Export Backlinks**
1. Click the "Backlinks" tab
2. At the top right, click "Export" button
3. Choose "CSV" format
4. Click "Download"
5. File downloads to your computer
6. Remember where you saved it!

**Step 4: Upload to Plugin**
1. Back in OJS, go to Settings → Website → Plugins
2. Find "Backlink Tracker", click arrow, click "Settings"
3. In the popup, find "Upload Backlinks Data" section
4. Click "Choose File" button
5. Find and select the CSV file you just downloaded
6. Click "Upload Backlinks" button
7. Wait 5-10 seconds (don't close window)
8. You should see: "Successfully imported X backlinks"
9. Close the settings window

**✅ Backlinks are now uploaded!**

---

### From Google Search Console

**Step 1: Access GSC**
1. Go to: https://search.google.com/search-console
2. Sign in with your Google account
3. Select your journal property

**Step 2: Export Links**
1. In left sidebar, click "Links"
2. Under "Top linking sites", click "MORE"
3. Click "Export" → "Download latest links"
4. Choose "CSV" format
5. File downloads to your computer

**Step 3: Upload to Plugin**
Follow Step 4 from SEMrush instructions above.

---

### From Ahrefs

**Step 1: Log in**
1. Go to: https://ahrefs.com/
2. Sign in with your account

**Step 2: Site Explorer**
1. Click "Site Explorer"
2. Enter your domain
3. Click search

**Step 3: Export Backlinks**
1. Click "Backlinks" in left sidebar
2. Click "Export" button
3. Choose "CSV" format
4. Download file

**Step 4: Upload to Plugin**
Follow Step 4 from SEMrush instructions above.

---

## Part 5: Add Repository Links to Articles (Optional)

If you have articles hosted on both OJS and a separate repository (like WordPress), follow these steps:

**Step 1: Edit an Article**
1. In OJS, go to "Submissions"
2. Click "Active" or "Published"
3. Find an article, click on it
4. Click "Publication" tab

**Step 2: Add Galley**
1. Click "Galleys" (in the left menu)
2. Click blue "+ Add Galley" button
3. A form appears

**Step 3: Configure Repository Galley**
1. **Label**: Enter `REPO` (exactly as configured in plugin settings)
2. Select radio button: "This galley will be available at a separate website"
3. **URL**: Enter the full repository URL
   - Example: `https://repository.yoursite.com/article/article-title/`
   - Make sure it's the complete URL
4. Click "Save"

**Step 4: Verify**
1. View the article on your public website
2. Scroll down to see "External References to this Article"
3. Backlinks should now show whether they point to "Abstract Page" or "Full Text"

**Repeat for other articles** that have repository versions.

---

## Part 6: Test the Plugin

**Step 1: View an Article**
1. Go to your journal's public website
2. Navigate to any published article
3. Scroll down the page

**Step 2: Find External References Section**
1. Look for section titled: "External References to this Article"
2. It should appear after the abstract (or wherever you configured it)

**Step 3: Check Display**

You should see:
- Total reference count (e.g., "21 references from 12 unique sources")
- List of domains
- Click on a domain to expand and see individual backlinks
- Each backlink shows:
  - Referring page URL
  - Anchor text
  - Reference target (Abstract Page or Full Text)

**If you see "No external references found":**
- This is normal if:
  - No backlinks exist for this article in your CSV
  - Article URL doesn't match any target URLs in CSV
  - Article is very new

---

## Part 7: Updating Backlinks

**When to update:**
- Monthly for active journals
- Quarterly for established journals
- Whenever you want fresh data

**How to update:**
1. Export fresh CSV from SEMrush/Ahrefs/GSC
2. Go to Plugin Settings
3. Upload new CSV file
4. Done! All old data is replaced with new data

**Important:** Each upload completely replaces old data. There's no merging or adding.

---

## Troubleshooting

### Problem 1: Plugin Doesn't Appear in Plugin List

**Possible causes:**
- Files in wrong location
- File permissions issue
- Plugin files incomplete

**Solution:**
1. Check file location is: `plugins/generic/backlinkTracker/`
2. Not: `plugins/generic/BacklinkTrackerPlugin/` (wrong name)
3. Not: `plugins/backlinkTracker/` (missing "generic" folder)
4. In cPanel, right-click folder → "Change Permissions"
5. Set to 755 (or check: Read, Write, Execute for owner)
6. Clear OJS cache:
   - Go to Administration → Clear Data Caches
   - Or manually delete "cache" folder in OJS root

---

### Problem 2: CSV Upload Fails

**Error: "Invalid file format"**

**Solution:**
- Make sure file is CSV format (.csv extension)
- If you have Excel file (.xlsx), convert it:
  1. Open in Excel/LibreOffice
  2. File → Save As → CSV (Comma delimited)
  3. Upload the new CSV file

**Error: Upload times out or fails**

**Solution:**
- File might be too large
- Try exporting fewer backlinks (filter by date in SEMrush/Ahrefs)
- Or split into multiple uploads (upload will replace, not add)

---

### Problem 3: No Backlinks Show on Articles

**Possible causes:**
- CSV uploaded but URLs don't match
- Article doesn't have backlinks in your export
- URL format mismatch

**Solution:**
1. Check the CSV file - find your article's URL
2. Copy the "Target url" column value
3. Compare with your OJS article URL
4. They must match exactly (except query parameters)
5. Example matches:
   - CSV: `https://yoursite.com/article/view/123`
   - OJS: `https://yoursite.com/article/view/123` ✅
   - OJS: `https://yoursite.com/article/view/123/` ✅ (trailing slash OK)
   - OJS: `https://yoursite.com/article/view/456` ❌ (different article)

---

### Problem 4: "External References" Section Not Visible

**Solution:**
1. Make sure plugin is enabled (green checkbox)
2. Clear browser cache (Ctrl+Shift+Delete)
3. Hard refresh page (Ctrl+F5 or Cmd+Shift+R)
4. Check browser console for errors (F12 → Console tab)
5. Verify JavaScript is enabled in browser

---

### Problem 5: Wrong Backlinks Showing on Article

**Cause:** 
URL matching is too broad or CSV has incorrect data

**Solution:**
1. Plugin uses exact URL matching
2. Check your CSV export - verify target URLs are correct
3. Re-export and upload fresh CSV
4. If problem persists, check REPO galley URLs are accurate

---

## Advanced: Customizing Placement

The "External References" section appears after the Abstract by default.

**To change position:**
1. Read the included `PLACEMENT_GUIDE.md`
2. Edit your theme's `article_details.tpl` file
3. Move the `{call_hook name="Templates::Article::Main"}` line
4. Popular positions:
   - After Usage Statistics
   - After Author Biographies
   - At end of article

**Recommended:** Ask your web developer if you're not comfortable editing template files.

---

## Getting Help

**If you're stuck:**

1. ✅ Re-read this guide carefully
2. ✅ Check the Troubleshooting section
3. ✅ Read README.md for more details
4. ✅ Check PLACEMENT_GUIDE.md for customization
5. ✅ Visit OJS forum: https://forum.pkp.sfu.ca/
6. ✅ Review OJS docs: https://docs.pkp.sfu.ca/
7. ✅ Contact your system administrator

---

## Maintenance

**Regular tasks:**
- Upload fresh CSV monthly/quarterly
- Monitor article pages to verify display
- Update REPO galleys for new articles

**No maintenance needed for:**
- Server configuration
- Database optimization
- Cache management
- API renewals (no APIs used!)

---

## Security Notes

- ✅ No API keys stored
- ✅ No external services called
- ✅ CSV data sanitized on upload
- ✅ All data stored securely in database
- ✅ XSS protection on display

**Your data is safe!**

---

## What Happens Behind the Scenes

For those curious:

1. **Upload**: CSV parsed, converted to JSON, stored in database
2. **Article Page Load**: Plugin checks database for matching URLs
3. **Display**: JavaScript renders backlinks grouped by domain
4. **Performance**: Single database query, no API calls

**Result:** Fast, reliable, zero external dependencies!

---

## Congratulations! 🎉

Your Backlink Tracker plugin is now:
- ✅ Installed
- ✅ Configured
- ✅ Displaying backlinks on articles
- ✅ Ready to showcase your article impact!

Enjoy tracking your journal's external references!

---

**Plugin Version:** 1.0.0  
**Last Updated:** November 2025  
**Compatible:** OJS 3.4+