<script>
	$(function() {ldelim}
		$('#backlinkTrackerSettings').pkpHandler('$.pkp.controllers.form.AjaxFormHandler');
	{rdelim});
</script>

<form class="pkp_form" id="backlinkTrackerSettings" method="post" action="{url router=$smarty.const.ROUTE_COMPONENT op="manage" category="generic" plugin=$pluginName verb="settings" save=true}">
	{csrf}
	{include file="controllers/notification/inPlaceNotification.tpl" notificationId="backlinkTrackerSettingsFormNotification"}

	<div id="description">
		<h3>Backlink Tracker Settings</h3>
		<p>Upload your SEMrush backlinks export (Excel/CSV format) to display backlinks on article pages.</p>
	</div>

	<h3>Data Scope</h3>

	{fbvFormArea id="dataScopeSettings"}
		{fbvFormSection list=true}
			{fbvElement type="checkbox" id="useSiteWideData" checked=$useSiteWideData label="plugins.generic.backlinkTracker.settings.useSiteWideData"}
			<p class="description">{translate key="plugins.generic.backlinkTracker.settings.useSiteWideDataHelp"}</p>
		{/fbvFormSection}
	{/fbvFormArea}

	<h3>Upload Backlinks Data</h3>
	
	{fbvFormArea id="uploadSettings"}
		{fbvFormSection}
			<p class="description">Export backlinks from SEMrush and upload the Excel/CSV file here.</p>
			<input type="file" id="backlinkFile" name="backlinkFile" accept=".xlsx,.xls,.csv" />
			<button type="button" id="uploadBacklinks" class="pkp_button">Upload Backlinks</button>
			<div id="uploadStatus" style="margin-top: 10px;"></div>
		{/fbvFormSection}
		
		{if $backlinkDataStatus}
			{fbvFormSection}
				<div class="backlink-status">
					<p><strong>Current Data Status:</strong></p>
					<p>Total Backlinks: {$backlinkDataCount|default:0}</p>
					<p>Last Updated: {$backlinkDataDate|default:"Never"}</p>
					<button type="button" id="clearBacklinks" class="pkp_button">Clear All Backlink Data</button>
				</div>
			{/fbvFormSection}
		{/if}
	{/fbvFormArea}

	<h3>{translate key="plugins.generic.backlinkTracker.settings.matchingSettings"}</h3>

	{fbvFormArea id="matchingSettings"}
		{fbvFormSection}
			{fbvElement type="text" id="repoGalleyLabel" value=$repoGalleyLabel label="plugins.generic.backlinkTracker.settings.repoGalleyLabel"}
			<p class="description">{translate key="plugins.generic.backlinkTracker.settings.repoGalleyLabelHelp"}</p>
		{/fbvFormSection}

		{fbvFormSection}
			{fbvElement type="textarea" id="blockedDomains" value=$blockedDomains label="plugins.generic.backlinkTracker.settings.blockedDomains" height=$fbvStyles.height.SHORT}
			<p class="description">Enter domains to exclude from backlink display (one per line). Example:<br>spamsite.com<br>example.com</p>
		{/fbvFormSection}
	{/fbvFormArea}

	<h3>URL Redirect Mapping</h3>

	{fbvFormArea id="redirectMapping"}
		{fbvFormSection}
			<p class="description">Upload a CSV file mapping old URLs to new URLs (e.g., after domain migration). Format: old_url,new_url</p>
			<input type="file" id="redirectMappingFile" name="redirectMappingFile" accept=".csv" />
			<button type="button" id="uploadRedirectMapping" class="pkp_button">Upload Redirect Mapping</button>
			<div id="redirectUploadStatus" style="margin-top: 10px;"></div>
		{/fbvFormSection}

		{if $redirectMappingCount > 0}
			{fbvFormSection}
				<div class="mapping-status">
					<p><strong>Current Redirect Mapping Status:</strong></p>
					<p>Total Mappings: {$redirectMappingCount}</p>
					<p>Last Updated: {$redirectMappingDate|default:"Never"}</p>
					<button type="button" id="clearRedirectMapping" class="pkp_button">Clear All Redirect Mappings</button>
				</div>
			{/fbvFormSection}
		{/if}
	{/fbvFormArea}

	{fbvFormButtons}
</form>

<script>
$(document).ready(function() {
	$('#uploadBacklinks').click(function() {
		var fileInput = document.getElementById('backlinkFile');
		var file = fileInput.files[0];
		
		if (!file) {
			$('#uploadStatus').html('<span style="color: red;">Please select a file first.</span>');
			return;
		}
		
		var formData = new FormData();
		formData.append('backlinkFile', file);
		formData.append('op', 'uploadBacklinks');
		
		$('#uploadStatus').html('Uploading and processing... Please wait...');
		$('#uploadBacklinks').prop('disabled', true);
		
		// Use component router URL
		var uploadUrl = '{url router=$smarty.const.ROUTE_COMPONENT op="manage" category="generic" plugin=$pluginName verb="uploadBacklinks" escape=false}';
		
		$.ajax({
			url: uploadUrl,
			type: 'POST',
			data: formData,
			processData: false,
			contentType: false,
			success: function(response) {
				if (response.status) {
                    $('#uploadStatus').html('<span style="color: green;">' + (response.content.message || response.content) + '<br><strong>Please close this window to see updated data.</strong></span>');
				} else {
					$('#uploadStatus').html('<span style="color: red;">Error: ' + (response.content || 'Upload failed') + '</span>');
				}
				$('#uploadBacklinks').prop('disabled', false);
			},
			error: function(xhr) {
				$('#uploadStatus').html('<span style="color: red;">Upload failed: ' + xhr.statusText + '</span>');
				$('#uploadBacklinks').prop('disabled', false);
			}
		});
	});
	
	$('#clearBacklinks').click(function() {
		if (!confirm('Are you sure you want to clear all backlink data?')) {
			return;
		}

		var clearUrl = '{url router=$smarty.const.ROUTE_COMPONENT op="manage" category="generic" plugin=$pluginName verb="clearBacklinks" escape=false}';

		$.ajax({
			url: clearUrl,
			type: 'POST',
			success: function(response) {
				if (response.status) {
					alert('Backlink data cleared successfully.');
					window.location.reload();
				} else {
					alert('Error clearing data.');
				}
			}
		});
	});

	// Upload redirect mapping
	$('#uploadRedirectMapping').click(function() {
		var fileInput = document.getElementById('redirectMappingFile');
		var file = fileInput.files[0];

		if (!file) {
			$('#redirectUploadStatus').html('<span style="color: red;">Please select a CSV file first.</span>');
			return;
		}

		var formData = new FormData();
		formData.append('redirectMappingFile', file);
		formData.append('op', 'uploadRedirectMapping');

		$('#redirectUploadStatus').html('Uploading and processing... Please wait...');
		$('#uploadRedirectMapping').prop('disabled', true);

		var uploadUrl = '{url router=$smarty.const.ROUTE_COMPONENT op="manage" category="generic" plugin=$pluginName verb="uploadRedirectMapping" escape=false}';

		$.ajax({
			url: uploadUrl,
			type: 'POST',
			data: formData,
			processData: false,
			contentType: false,
			success: function(response) {
				if (response.status) {
					$('#redirectUploadStatus').html('<span style="color: green;">' + (response.content.message || response.content) + '<br><strong>Please close this window to see updated data.</strong></span>');
				} else {
					$('#redirectUploadStatus').html('<span style="color: red;">Error: ' + (response.content || 'Upload failed') + '</span>');
				}
				$('#uploadRedirectMapping').prop('disabled', false);
			},
			error: function(xhr) {
				$('#redirectUploadStatus').html('<span style="color: red;">Upload failed: ' + xhr.statusText + '</span>');
				$('#uploadRedirectMapping').prop('disabled', false);
			}
		});
	});

	// Clear redirect mapping
	$('#clearRedirectMapping').click(function() {
		if (!confirm('Are you sure you want to clear all redirect mappings?')) {
			return;
		}

		var clearUrl = '{url router=$smarty.const.ROUTE_COMPONENT op="manage" category="generic" plugin=$pluginName verb="clearRedirectMapping" escape=false}';

		$.ajax({
			url: clearUrl,
			type: 'POST',
			success: function(response) {
				if (response.status) {
					alert('Redirect mapping cleared successfully.');
					window.location.reload();
				} else {
					alert('Error clearing mapping.');
				}
			}
		});
	});
});
</script>

<style>
.backlink-status, .mapping-status {
	padding: 15px;
	background: #f0f0f0;
	border-radius: 5px;
	margin: 10px 0;
}
</style>