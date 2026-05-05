# Backlink Tracker Plugin - Placement Guide

## Changing the Position of "External References" Section

The Backlink Tracker plugin uses OJS's hook system to inject content into article pages. By default, the section appears after the Abstract. You can change its position by moving the hook in your theme's template file.

---

## Step-by-Step Instructions

### 1. Locate Your Theme's Article Template

**File path:**
```
public_html/templates/frontend/objects/article_details.tpl
```

Or in your theme folder:
```
plugins/themes/YOUR_THEME_NAME/templates/frontend/objects/article_details.tpl
```

---

### 2. Find the Hook Line

Look for this line in the template:
```smarty
{call_hook name="Templates::Article::Main"}
```

**Default position:** After the Abstract section (around line 145)

---

### 3. Choose Your Preferred Position

The article template has several sections in this order:

1. **Authors**
2. **DOI**
3. **Keywords**
4. **Abstract**
5. **External References (Plugin)** ← Current default position
6. **Usage Statistics Chart**
7. **Author Biographies**
8. **References/Citations**

---

## Recommended Placement Options

### Option A: After Usage Statistics (Recommended)

**Location:** After the usage statistics chart section closes

**Why:** Usage statistics are always present, making this a reliable and logical position. Creates a natural flow: Article Views → External References.

**Code placement:**

```smarty
{* Usage statistics chart*}
{if $activeTheme->getOption('displayStats') != 'none'}
	{$activeTheme->displayUsageStatsGraph($article->getId())}
	<section class="item downloads_chart">
		<h2 class="label">
			{translate key="plugins.themes.default.displayStats.downloads"}
		</h2>
		<div class="value">
			<canvas class="usageStatsGraph" data-object-type="Submission" data-object-id="{$article->getId()|escape}"></canvas>
			<div class="usageStatsUnavailable" data-object-type="Submission" data-object-id="{$article->getId()|escape}">
				{translate key="plugins.themes.default.displayStats.noStats"}
			</div>
		</div>
	</section>
{/if}

{call_hook name="Templates::Article::Main"}  ← MOVE HOOK HERE

{* Author biographies *}
{assign var="hasBiographies" value=0}
```

---

### Option B: At the End of Main Content

**Location:** After the References/Citations section, before closing `</div><!-- .main_entry -->`

**Why:** Places external references at the very bottom of the article content.

**Code placement:**

```smarty
{* References *}
{if $parsedCitations || $publication->getData('citationsRaw')}
	<section class="item references">
		<h2 class="label">
			{translate key="submission.citations"}
		</h2>
		<div class="value">
			{if $parsedCitations}
				{foreach from=$parsedCitations item="parsedCitation"}
					<p>{$parsedCitation->getCitationWithLinks()|strip_unsafe_html} {call_hook name="Templates::Article::Details::Reference" citation=$parsedCitation}</p>
				{/foreach}
			{else}
				{$publication->getData('citationsRaw')|escape|nl2br}
			{/if}
		</div>
	</section>
{/if}

{call_hook name="Templates::Article::Main"}  ← MOVE HOOK HERE

</div><!-- .main_entry -->
```

---

### Option C: After Author Biographies

**Location:** After the author biographies section, before References

**Code placement:**

```smarty
{* Author biographies *}
{assign var="hasBiographies" value=0}
{foreach from=$publication->getData('authors') item=author}
	{if $author->getLocalizedData('biography')}
		{assign var="hasBiographies" value=$hasBiographies+1}
	{/if}
{/foreach}
{if $hasBiographies}
	<section class="item author_bios">
		<h2 class="label">
			{if $hasBiographies > 1}
				{translate key="submission.authorBiographies"}
			{else}
				{translate key="submission.authorBiography"}
			{/if}
		</h2>
		<ul class="authors">
		{foreach from=$publication->getData('authors') item=author}
			{if $author->getLocalizedData('biography')}
				<li class="sub_item">
					<div class="label">
						{if $author->getLocalizedData('affiliation')}
							{capture assign="authorName"}{$author->getFullName()|escape}{/capture}
							{capture assign="authorAffiliation"} {$author->getLocalizedData('affiliation')|escape} {/capture}
							{translate key="submission.authorWithAffiliation" name=$authorName affiliation=$authorAffiliation}
						{else}
							{$author->getFullName()|escape}
						{/if}
					</div>
					<div class="value">
						{$author->getLocalizedData('biography')|strip_unsafe_html}
					</div>
				</li>
			{/if}
		{/foreach}
		</ul>
	</section>
{/if}

{call_hook name="Templates::Article::Main"}  ← MOVE HOOK HERE

{* References *}
```

---

## Important Notes

1. **Only one hook per template:** The hook can only appear once in the template file.

2. **Theme updates:** If you update your OJS theme, you may need to reapply this change.

3. **Different themes:** Hook placement may vary slightly depending on your theme's structure.

4. **Clear cache:** After making changes, clear OJS cache:
   - Via Admin Dashboard: Administration → Clear Data Caches
   - Or delete: `cache/` folder in OJS root directory

5. **Backup first:** Always backup your template file before making changes.

---

## Testing

After moving the hook:

1. Clear OJS cache
2. Visit any article page
3. Verify the "External References to this Article" section appears in the desired location
4. Check on both desktop and mobile views

---

## Troubleshooting

**Section not appearing:**
- Verify the hook line is copied correctly
- Check that you cleared the cache
- Ensure the plugin is enabled in Plugin Settings

**Section in wrong position:**
- Look for multiple `{call_hook name="Templates::Article::Main"}` lines (there should be only one)
- Verify you're editing the correct template file for your active theme

**Theme-specific issues:**
- Some custom themes may have different template structures
- Consult your theme's documentation or developer

---

## Support

For issues or questions about placement:
1. Check the OJS forum: https://forum.pkp.sfu.ca/
2. Review OJS theming documentation: https://docs.pkp.sfu.ca/
3. Contact your theme developer for theme-specific guidance

---

**Plugin Version:** 1.0  
**Compatible with:** OJS 3.4+  
**Last Updated:** November 2025
