# Commands

These commands are available through the Laravel Artisan CLI.

## Assets

Usage: `php artisan bree:assets`
Description: Publish all of the assets used by Bree fieldtypes or fieldsets, including third-party packages

(Optional) You may also want to add the bree:assets command to your post-update and post-install hooks in composer. This would look something like

	"scripts": {
		"pre-update-cmd": [
			"php artisan clear-compiled"
		],
		"post-install-cmd": [
			"php artisan optimize",
			"php artisan bree:assets"
		],
		"post-update-cmd": [
			"php artisan optimize",
			"php artisan bree:assets"
		]
	},
