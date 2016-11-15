<div class="forge-dashboard">
	<div class="forge-dashboard-header">
		<h1 class="forge-dashboard-title">
			<?php _e('Forge Page Builder', 'forge'); ?>
		</h1>
		<div class="forge-dashboard-version">
			<span class="forge-dashboard-version-number">
				<?php printf(__('Version %s', 'forge'), FORGE_VERSION); ?>
			</span>
		</div>
		<div class="forge-dashboard-description">
			<?php _e('Thank you for using Forge! This is your page builder dashboard. Here you can find everything you need to configure your pages and start right away.', 'forge'); ?>
		</div>
	</div>
	<div class="forge-dashboard-badge"><?php echo FORGE_VERSION; ?></div>
	
	<div class="forge-dashboard-clear"></div>
	
	<div class="forge-dashboard-section">
		<div class="forge-dashboard-block">
			<?php echo forge_icon('linearicons-&#xe810', 'forge-dashboard-block-icon'); ?>
			<div class="forge-dashboard-block-body">
				<h3 class="forge-dashboard-block-title"><?php _e('Plugin Settings', 'forge'); ?></h3>
				<p>
					<?php _e('Configure the behavior of the page builder and add your license keys.', 'forge'); ?>
				</p>
				<a class="button" href="<?php echo admin_url('admin.php?page=forge_settings'); ?>"><?php _e('Go To Settings', 'forge'); ?></a>
			</div>
		</div>
		<div class="forge-dashboard-block">
			<?php echo forge_icon('linearicons-&#xe80d', 'forge-dashboard-block-icon'); ?>
			<div class="forge-dashboard-block-body">
				<h3 class="forge-dashboard-block-title"><?php _e('Connections', 'forge'); ?></h3>
				<p>
					<?php _e('Connect to third party services and integrate your pages with them.', 'forge'); ?>
				</p>
				<a class="button" href="<?php echo admin_url('admin.php?page=forge_connections'); ?>"><?php _e('Go To Connections', 'forge'); ?></a>
			</div>
		</div>
		<div class="forge-dashboard-block forge-dashboard-block-last">
			<?php echo forge_icon('linearicons-&#xe87d', 'forge-dashboard-block-icon'); ?>
			<div class="forge-dashboard-block-body">
				<h3 class="forge-dashboard-block-title"><?php _e('Get Support & Help', 'forge'); ?></h3>
				<p>
					<?php _e('Browse the knowledge base and find answers to all your questions.', 'forge'); ?>
				</p>
				<a class="button" target="_blank" href="//forgeplugin.com/support?utm_source=plugin&utm_medium=link&utm_campaign=dashboard-support"><?php _e('Read The Documentation', 'forge'); ?></a>
			</div>
		</div>
		<div class="forge-dashboard-clear"></div>
		<div class="forge-dashboard-block forge-dashboard-block-medium">
			<div class="forge-dashboard-block-body">
				<h3 class="forge-dashboard-block-title"><?php _e('Presets', 'forge'); ?></h3>
				<p>
					<?php _e('Presets are complete website layouts that come packaged with your current WordPress theme. You can load them to set up the content of your website in just a few seconds. Click on View Presets to select which ones you want to install into your website.', 'forge'); ?>
				</p>
				<?php if(forge_presets()->has_presets()): ?>
				<a class="button" href="<?php echo admin_url('admin.php?page=forge_presets'); ?>"><?php _e('View Presets', 'forge'); ?></a>
				<?php else: ?>
				<p style="opacity:0.8;">
					<i><?php _e('The current WordPress theme does not have any presets.', 'forge'); ?></i>
				</p>
				<?php endif; ?>
			</div>
		</div>
		<div class="forge-dashboard-block forge-dashboard-block-last forge-dashboard-block-medium">
			<div class="forge-dashboard-block-body">
				<h3 class="forge-dashboard-block-title"><?php _e('Page Building Course', 'forge'); ?></h3>
				<p>
					<?php _e('Do you want to use page builders like a professional designer? Here is a 5-day page building course full of techniques on using Forge. One email each day with clear, actionable tips.', 'forge'); ?>
					<?php $email = get_option('admin_email', ''); ?>
					<br>
					<form target="_blank" action="http://cpomail.com/subscribe" method="POST" accept-charset="utf-8" style="display:block;margin:10px 0 0;">
						<input type="text" name="email" id="email" value="<?php echo $email; ?>" placeholder="'<?php _e('Your email', 'forge'); ?>"/>
						<input type="hidden" name="list" value="nXMTUHtceZwiUJg0keISiA"/>
						<input type="submit" class="button button-primary" name="submit" id="submit" value="<?php _e('Get The Course', 'forge'); ?>"/>
					</form>				
				</p>
			</div>
		</div>
	</div>