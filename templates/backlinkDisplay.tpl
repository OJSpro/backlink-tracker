<div id="backlinkTracker" class="item backlinks">
	<h3 class="label">External References to this Article</h3>
	
	<div id="backlinkData">
		<div class="value">
			<p id="loadingMessage">Loading reference data...</p>
		</div>
	</div>
</div>

<style>
#backlinkTracker {
	margin: 20px 0;
	padding: 20px;
	background: #f8f9fa;
	border-radius: 5px;
}

#backlinkTracker h3.label {
	margin-top: 0;
	color: #333;
	font-size: 1.2em;
}

.backlink-summary {
	margin-bottom: 15px;
	font-size: 1.05em;
}

.backlink-summary p {
	margin: 5px 0;
}

.domain-group {
	margin: 15px 0;
	border: 1px solid #ddd;
	border-radius: 5px;
	overflow: hidden;
}

.domain-header {
	padding: 12px 15px;
	background: #e9ecef;
	cursor: pointer;
	font-weight: normal;
	display: flex;
	justify-content: space-between;
	align-items: center;
}

.domain-header:hover {
	background: #dee2e6;
}

.domain-name {
	color: #007bff;
	font-size: 0.9em;
}

.reference-count {
	color: #666;
	font-size: 0.9em;
}

.domain-links {
	display: none;
	padding: 15px;
	background: white;
}

.domain-links.expanded {
	display: block;
}

.backlink-item {
	padding: 10px 0;
	border-bottom: 1px solid #f0f0f0;
}

.backlink-item:last-child {
	border-bottom: none;
}

.backlink-url {
	color: #007bff;
	text-decoration: none;
	font-size: 0.95em;
	display: block;
	margin-bottom: 5px;
}

.backlink-url:hover {
	text-decoration: underline;
}

.backlink-meta {
	color: #666;
	font-size: 0.9em;
	margin-top: 5px;
}

.backlink-anchor {
	font-style: italic;
}

.backlink-anchor:before {
	content: "Anchor Text: ";
	font-weight: bold;
	font-style: normal;
}

.backlink-target {
	margin-top: 3px;
}

.backlink-target.abstract {
	color: #28a745;
}

.backlink-target.fulltext {
	color: #007bff;
}

.backlink-target:before {
	content: "Reference Target: ";
	font-weight: bold;
}

.expand-icon {
	font-size: 0.8em;
	transition: transform 0.3s;
}

.expand-icon.rotated {
	transform: rotate(180deg);
}

.last-updated {
	color: #888;
	font-style: italic;
	margin-top: 15px;
	font-size: 0.9em;
}

.no-backlinks {
	color: #666;
	font-style: italic;
}

.backlink-branding {
	font-size: 0.75em;
	color: #999;
	text-align: center;
	margin-top: 20px;
	padding-top: 10px;
	border-top: 1px solid #e0e0e0;
}

.backlink-branding a {
	color: #007bff;
	text-decoration: none;
}

.backlink-branding a:hover {
	text-decoration: underline;
}

/* Mobile Optimization */
@media (max-width: 768px) {
	.domain-header {
		padding: 10px 12px;
	}
	
	.domain-name {
		font-weight: normal;  /* Remove bold */
		font-size: 0.95em;    /* Smaller text */
	}
	
	.reference-count {
		font-size: 0.85em;
	}
	
	.backlink-url {
		font-size: 0.85em;
		word-break: break-word;  /* Prevent overflow */
	}
	
	.backlink-meta {
		font-size: 0.8em;
	}
	
	#backlinkTracker {
		padding: 15px;
	}
}

@media (max-width: 480px) {
	.domain-name {
		font-size: 0.9em;
	}
	
	.reference-count {
		font-size: 0.8em;
	}
	
	.backlink-url {
		font-size: 0.8em;
	}
}
</style>

<script>
(function() {
	function initBacklinks() {
		if (typeof jQuery === 'undefined') {
			setTimeout(initBacklinks, 100);
			return;
		}
		
		var $ = jQuery;
		var articleId = {$articleId};

		loadBacklinks(articleId);

		function loadBacklinks(articleId) {
			$('#loadingMessage').show();

			$.ajax({
				url: '{url page="backlink" op="fetch"}',
				type: 'POST',
				data: { articleId: articleId },
				success: function(response) {
					if (response.status && response.content) {
						var data = response.content;
						displayBacklinks(data);
					} else {
						var html = '<div class="value">';
						html += '<p class="no-backlinks">No external references found for this article.</p>';
						html += '<p class="backlink-branding">Built by <a href="https://ojspro.com?ref=' + encodeURIComponent(window.location.hostname) + '" target="_blank" rel="noopener">OJSpro.com</a> with ❤️ for Libertatem Media Group</p>';
						html += '</div>';
						$('#backlinkData').html(html);
					}
				},
				error: function() {
					$('#loadingMessage').text('Error loading references.');
				}
			});
		}

		function displayBacklinks(data) {
			var html = '<div class="value"><div class="backlink-summary">';
			
			if (data.total > 0) {
				html += '<p><strong>' + data.total + ' references from ' + data.domains + ' unique source' + (data.domains > 1 ? 's' : '') + '</strong></p>';
			}
			
			html += '</div>';

			if (data.grouped_links && data.grouped_links.length > 0) {
				html += '<div class="backlink-domains">';
				
				for (var i = 0; i < data.grouped_links.length; i++) {
					var domain = data.grouped_links[i];
					html += '<div class="domain-group" data-domain-index="' + i + '">';
					html += '<div class="domain-header">';
					html += '<span class="domain-name">' + escapeHtml(domain.domain) + '</span>';
					html += '<span class="reference-count">(' + domain.count + ' reference' + (domain.count > 1 ? 's' : '') + ') <span class="expand-icon">▼</span></span>';
					html += '</div>';
					html += '<div class="domain-links">';
					
					for (var j = 0; j < domain.links.length; j++) {
						var link = domain.links[j];
						html += '<div class="backlink-item">';
						html += '<a href="' + escapeHtml(link.url) + '" target="_blank" class="backlink-url">' + truncate(escapeHtml(link.url), 80) + '</a>';
						html += '<div class="backlink-meta">';
						if (link.anchor) {
							html += '<div class="backlink-anchor">' + escapeHtml(link.anchor) + '</div>';
						}
						if (link.target_type) {
                            var targetClass = link.target_type === 'Full Text' ? 'fulltext' : 'abstract';
                            html += '<div class="backlink-target ' + targetClass + '">' + escapeHtml(link.target_type) + '</div>';
                        }
						html += '</div>';
						html += '</div>';
					}
					
					html += '</div></div>';
				}
				
				html += '</div>';
			} else {
				html += '<p class="no-backlinks">No external references found for this article.</p>';
			}

			if (data.last_updated) {
				html += '<p class="last-updated">Reference data last updated: ' + data.last_updated + ' IST</p>';
			}

			html += '<p class="backlink-branding">Built by <a href="https://ojspro.com?ref=' + encodeURIComponent(window.location.hostname) + '" target="_blank" rel="noopener">OJSpro.com</a> with ❤️ for Libertatem Media Group</p>';

			html += '</div>';
			
			$('#backlinkData').html(html);
			
			// Add click handlers for expanding
			$('.domain-header').click(function() {
				var $links = $(this).next('.domain-links');
				var $icon = $(this).find('.expand-icon');
				
				$links.toggleClass('expanded');
				$icon.toggleClass('rotated');
			});
		}

		function truncate(str, length) {
			if (str.length <= length) return str;
			return str.substring(0, length) + '...';
		}
		
		function escapeHtml(text) {
			var map = {
				'&': '&amp;',
				'<': '&lt;',
				'>': '&gt;',
				'"': '&quot;',
				"'": '&#039;'
			};
			return String(text).replace(/[&<>"']/g, function(m) { return map[m]; });
		}
	}
	
	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', initBacklinks);
	} else {
		initBacklinks();
	}
})();
</script>