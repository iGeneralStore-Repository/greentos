<?php
	
//Create settings page
add_action('admin_menu', 'forge_settings_pages', 8);
function forge_settings_pages(){
	$icon = 'data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBzdGFuZGFsb25lPSJubyI/Pg0KPCFET0NUWVBFIHN2ZyBQVUJMSUMgIi0vL1czQy8vRFREIFNWRyAyMDAxMDkwNC8vRU4iDQogImh0dHA6Ly93d3cudzMub3JnL1RSLzIwMDEvUkVDLVNWRy0yMDAxMDkwNC9EVEQvc3ZnMTAuZHRkIj4NCjxzdmcgdmVyc2lvbj0iMS4wIiB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciDQogd2lkdGg9IjUwMC4wMDAwMDBwdCIgaGVpZ2h0PSI1MDAuMDAwMDAwcHQiIHZpZXdCb3g9IjAgMCA1MDAuMDAwMDAwIDUwMC4wMDAwMDAiDQogcHJlc2VydmVBc3BlY3RSYXRpbz0ieE1pZFlNaWQgbWVldCI+DQo8ZyB0cmFuc2Zvcm09InRyYW5zbGF0ZSgwLjAwMDAwMCw1MDAuMDAwMDAwKSBzY2FsZSgwLjEwMDAwMCwtMC4xMDAwMDApIg0KZmlsbD0iIzAwMDAwMCIgc3Ryb2tlPSJub25lIj4NCjxwYXRoIGQ9Ik0xMzEgNDU1NCBjLTE5IC0xMyAtMjIgLTIzIC0xOCAtNDkgMyAtMTggMjU4IC00NzEgNTY4IC0xMDA2IDMwOQ0KLTUzNiA3ODkgLTEzNjggMTA2NyAtMTg0OSA2NjkgLTExNTkgNjcwIC0xMTYyIDY5NCAtMTE4NyAyOCAtMzAgNDkgLTI5IDgwIDUNCjMyIDMyIDQzNiA3MzEgNDU1IDc4NCAyNCA3MCA4MiAtNDAgLTY2MiAxMjQ4IC02MTIgMTA1OSAtNTk1IDEwMjkgLTU5NSAxMDY5DQowIDIwIDQgNDEgOCA0NyAyNiAzOSA1IDM5IDEzMzkgNDQgbDEyODMgNSAzNCAzNSBjMjggMjggMzc2IDYxNSA0NDkgNzU3IDIzDQo0NCAyMiA5MCAtMiAxMDMgLTE0IDcgLTc2MiAxMCAtMjM0OCAxMCAtMjEzOCAwIC0yMzMwIC0xIC0yMzUyIC0xNnoiLz4NCjxwYXRoIGQ9Ik0yNzEzIDMwNjUgYy0yOSAtMTIgLTMzIC0xOSAtMzMgLTUzIDAgLTQzIDUgLTUwIDM4OSAtNjYyIDEyMyAtMTk1DQoyMzMgLTM2NSAyNDQgLTM3NyAyNyAtMzAgNjIgLTI5IDkwIDEgMjIgMjUgNTQ4IDk2OSA1NjYgMTAxNyAxNCAzOSAzIDY2IC0zMQ0KNzggLTIxIDcgLTIyNyAxMSAtNjEyIDExIC00NzggLTEgLTU4NyAtMyAtNjEzIC0xNXoiLz4NCjwvZz4NCjwvc3ZnPg==';
	add_menu_page(__('Dashboard', 'forge'), 'Forge', 'edit_posts', 'forge_dashboard', 'forge_dashboard_page', $icon, '600');
	add_submenu_page('forge_dashboard', __('Connections', 'forge'), __('Connections', 'forge'), 'publish_posts', 'forge_connections', 'forge_connections_page');
	add_submenu_page('forge_dashboard', __('Extensions', 'forge'), __('Extensions', 'forge'), 'edit_posts', 'forge_extensions', 'forge_extensions_page');
	add_submenu_page('forge_dashboard', __('Templates', 'forge'), __('Templates', 'forge'), 'publish_posts', 'forge_templates', 'forge_page_templates');
	if(forge_presets()->has_presets()){
		add_submenu_page('forge_dashboard', __('Theme Presets', 'forge'), __('Presets', 'forge'), 'publish_posts', 'forge_presets', 'forge_presets_page');
	}
	add_submenu_page('forge_dashboard', __('Settings', 'forge'), __('Settings', 'forge'), 'manage_options', 'forge_settings', 'forge_settings_page');
}


//Render settings page
function forge_dashboard_page(){
	include(FORGE_DIR.'templates/admin-dashboard.php');
}