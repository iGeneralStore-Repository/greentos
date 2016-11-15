(function(){
	tinymce.PluginManager.add('forge_templates', function(editor, url){
		editor.addButton('forge_templating_button', {
			title: 'Add Forge Template',
			type: 'button',
			icon: ' forge-shortcodes-icon',
			onclick: function(){ 
				editor.windowManager.open({
					title: 'Add Forge Template',
					body: [
						{ type:'listbox', name:'id', label:'Select Template', values:forge_template_list },
					],
					classes:'forge-generator-template',
					onsubmit: function(e){
						editor.selection.setContent('[forge_template id="' + e.data.id + '"]');
					}
				});
			}
		});
	});
})();