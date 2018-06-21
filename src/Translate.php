<?php

namespace AxelSpringer\WP\Mango;

abstract class Translate
{
	const SETTINGS_PAGE_TITLE    = 'WP Mango';
	const SETTINGS_MENU_TITLE    = 'WP Mango';
	
	const SETTINGS_SECTION_GENERAL      = 'General';
	const SETTINGS_SECTION_ADVANCED     = 'Advanced';
	const SETTINGS_SECTION_CREDENTIALS  = 'Credentials';
	const SETTINGS_SECTION_RESOURCES    = 'Resources';
	const SETTINGS_SECTION_PREVIEW    	= 'Preview';
	const SETTINGS_SECTION_FILTERS		= 'Filters';
	
	const SETTINGS_FIELD_ENABLED        = 'Enabled';
	const SETTINGS_FIELD_TOKEN          = 'Token';
	const SETTINGS_FIELD_SECRET         = 'Secret';
	const SETTINGS_FIELD_NAVIGATION     = 'Navigation';
	const SETTINGS_FIELD_CUSTOMIZER     = 'Customizer';
	const SETTINGS_FIELD_POSTS          = 'Posts';
	const SETTINGS_FIELD_REDIRECT       = 'Redirect';
	const SETTINGS_FIELD_REWRITE_URL    = 'Rewrite URL';
	const SETTINGS_FIELD_PREVIEW_URL    = 'Preview URL';
	const SETTINGS_FIELD_HEALTH			= 'Health Check';
	const SETTINGS_FIELD_PREVIEW 		= 'Preview';
	const SETTINGS_FIELD_MEDIA	 		= 'Media';
	const SETTINGS_FIELD_ITEMS	 		= 'Items';

	const SETTINGS_FIELD_PREVIEW_SAMPLE			= 'Example';
	const SETTINGS_FIELD_PREVIEW_SAMPLE_URL		= 'Example Url';

	// Filters
	const SETTINGS_FIELD_FILTER_PAGE_LINK = 'Flatten Page Links';
	
	const SETTINGS_FIELD_ROLE_CAPABILITIES = 'Capabilities';
	const SETTINGS_DESCRIPTION_ROLE_CAPABILITIES = 'The capabilities the REST API should have';
}
