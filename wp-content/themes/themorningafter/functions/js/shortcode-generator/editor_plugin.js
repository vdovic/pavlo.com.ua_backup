function woo_js_querystring(ji) {

	hu = window.location.search.substring(1);
	gy = hu.split("&");
	for (i=0;i<gy.length;i++) {
	
		ft = gy[i].split("=");
		if (ft[0] == ji) {
		
			return ft[1];
		
		} // End IF Statement
		
	} // End FOR Loop
	
} // End woo_js_querystring()
	
(
	
	function(){
	
		// Get the URL to this script file (as JavaScript is loaded in order)
		// (http://stackoverflow.com/questions/2255689/how-to-get-the-file-path-of-the-currenctly-executing-javascript-code)
		
		var scripts = document.getElementsByTagName("script"),
		src = scripts[scripts.length-1].src;

		var framework_url = src.split( '/js/' );
		
		var icon_url = framework_url[0] + '/images/shortcode-icon.png';
	
		tinymce.create(
			"tinymce.plugins.WooThemesShortcodes",
			{
				init: function(d,e) {
						d.addCommand( "wooVisitWooThemes", function(){ window.open( "http://woothemes.com/" ) } );
						
						d.addCommand( "wooOpenDialog",function(a,c){
							
							// Grab the selected text from the content editor.
							selectedText = '';
						
							if ( d.selection.getContent().length > 0 ) {
						
								selectedText = d.selection.getContent();
								
							} // End IF Statement
							
							wooSelectedShortcodeType = c.identifier;
							wooSelectedShortcodeTitle = c.title;
							
							
							jQuery.get(e+"/dialog.php",function(b){
								
								jQuery('#woo-options').addClass( 'shortcode-' + wooSelectedShortcodeType );
								jQuery('#woo-preview').addClass( 'shortcode-' + wooSelectedShortcodeType );
								
								// Skip the popup on certain shortcodes.
								
								switch ( wooSelectedShortcodeType ) {
							
									// Highlight
									
									case 'highlight':
								
									var a = '[highlight]'+selectedText+'[/highlight]';
									
									tinyMCE.activeEditor.execCommand("mceInsertContent", false, a);
								
									break;
									
									// Dropcap
									
									case 'dropcap':
								
									var a = '[dropcap]'+selectedText+'[/dropcap]';
									
									tinyMCE.activeEditor.execCommand("mceInsertContent", false, a);
								
									break;
							
									default:
									
									jQuery("#woo-dialog").remove();
									jQuery("body").append(b);
									jQuery("#woo-dialog").hide();
									var f=jQuery(window).width();
									b=jQuery(window).height();
									f=720<f?720:f;
									f-=80;
									b-=84;
								
								tb_show("Insert WooThemes "+ wooSelectedShortcodeTitle +" Shortcode", "#TB_inline?width="+f+"&height="+b+"&inlineId=woo-dialog");jQuery("#woo-options h3:first").text("Customize the "+c.title+" Shortcode");
								
									break;
								
								} // End SWITCH Statement
							
							}
													 
						)
						 
						} 
					);
						
						// d.onNodeChange.add(function(a,c){ c.setDisabled( "woothemes_shortcodes_button",a.selection.getContent().length>0 ) } ) // Disables the button if text is highlighted in the editor.
					},
					
				createControl:function(d,e){
				
						if(d=="woothemes_shortcodes_button"){
						
							d=e.createMenuButton("woothemes_shortcodes_button",{
								title:"Insert WooThemes Shortcode",
								image:icon_url,
								icons:false
								});
								
								var a=this;d.onRenderMenu.add(function(c,b){
								
									a.addWithDialog(b,"Button","button");
									a.addWithDialog(b,"Icon Link","ilink");b.addSeparator();
									a.addWithDialog(b,"Info Box","box");
									c=b.addMenu({title:"Typography"});
										a.addWithDialog(c,"Dropcap","dropcap");
										a.addWithDialog(c,"Quote","quote");
										a.addWithDialog(c,"Highlight","highlight");
										a.addWithDialog(c,"Custom Typography","typography");
										a.addWithDialog(c,"Abbreviation","abbr");
									a.addWithDialog(b,"Content Toggle","toggle");
									a.addWithDialog(b,"Related Posts","related");
									a.addWithDialog(b,"Contact Form","contactform");
									b.addSeparator();
									a.addWithDialog(b,"Column Layout","column");
									a.addWithDialog(b,"Tab Layout","tab");
									b.addSeparator();
										c=b.addMenu({title:"List Generator"});
											a.addWithDialog(c,"Unordered List","unordered_list");
											a.addWithDialog(c,"Ordered List","ordered_list");
										c=b.addMenu({title:"Dividers"});
											a.addImmediate(c,"Horizontal Rule","[hr] ");
											a.addImmediate(c,"Divider","[divider] ");
											a.addImmediate(c,"Flat Divider","[divider_flat] ");
										c=b.addMenu({title:"Social Buttons"});
											a.addWithDialog(c,"Social Profile Icon","social_icon");
											c.addSeparator();
											a.addWithDialog(c,"Twitter","twitter");
											a.addWithDialog(c,"Tweetmeme","tweetmeme");
											a.addWithDialog(c,"Digg","digg");
											a.addWithDialog(c,"Like on Facebook","fblike");
											a.addWithDialog(c,"Share on Facebook","fbshare");
		/*b.add({title:"Visit WooThemes.com","class":"woo-woolink",onclick:function(){tinyMCE.activeEditor.execCommand("wooVisitWooThemes",false,"")}})*/ });
							return d
						
						} // End IF Statement
						
						return null
					},
		
				addImmediate:function(d,e,a){d.add({title:e,onclick:function(){tinyMCE.activeEditor.execCommand("mceInsertContent",false,a)}})},
				
				addWithDialog:function(d,e,a){d.add({title:e,onclick:function(){tinyMCE.activeEditor.execCommand("wooOpenDialog",false,{title:e,identifier:a})}})},
		
				getInfo:function(){ return{longname:"WooThemes Shortcode Generator",author:"VisualShortcodes.com",authorurl:"http://visualshortcodes.com",infourl:"http://visualshortcodes.com/shortcode-ninja",version:"1.0"} }
			}
		);
		
		tinymce.PluginManager.add("WooThemesShortcodes",tinymce.plugins.WooThemesShortcodes)
	}
)();
