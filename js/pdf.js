( function() {
	// Register plugin
	tinymce.create( 'tinymce.plugins.pdflink', {

		init: function( editor, url )  {
			// Add the Insert Gistpen button
			editor.addButton( 'pdflink', {
				title : 'PDFリンクを入れる',
				image : url+'/pdficon_small.png',
				icon: 'icons dashicons-icon',
				tooltip: 'Insert PDF',
				cmd: 'pdf_dial'
			});

			editor.addCommand( 'pdf_dial', function() {
				editor.windowManager.open({
					title: 'PDF選択',
					width : 420 + parseInt(editor.getLang('button.delta_width', 0)), // size of our window
					height : 35 + parseInt(editor.getLang('button.delta_height', 0)), // size of our window
					inline: 1,
					id: 'pdf-insert-dialog',
					buttons: [{
						text: 'Insert',
						id: 'pdf-button-insert',
						class: 'insert',
						onclick: function( e ) {
						var linkText= editor.selection.getContent();
						var fileName= jQuery( '#uploaded_PDF'). find(":selected").text();;
						 editor.selection.setContent("\r\n"+'<a href="[pdf_url]' +fileName  + '" class="ico_pdf" target="blank">' + linkText + '</a>');
						editor.windowManager.close();
						}
					},
					{
						text: 'Cancel',
						id: 'pdf-button-cancel',
						onclick: 'close'
					}],
				});
				// external function
				appendInsertDialog();
			});

		}

	});

	tinymce.PluginManager.add( 'pdflink', tinymce.plugins.pdflink );

	function appendInsertDialog () {
		var foundPDFCount= foundPDF.length;
		var menuTxt='';
		if(foundPDFCount>0){
			menuTxt='<span id="textPdfLink">ファイル名</span>：<select id="uploaded_PDF">';
		var select= '';
		for(var i in foundPDF) { //var is generated and inserted to page from the WP plugin PDFplugin function
			select= select+'<option>'+foundPDF[i]+'</option>';
		}
		menuTxt= menuTxt+select+'</select>';
		}
		else{
			 menuTxt='<a href="?page=pdf-management.php" style="text-decoration:underline;color:#00a0d2;">管理画面から</a><br>「'+pdfFolder+'」にPDFをアップロードしてください';
		}
		jQuery( '#pdf-insert-dialog-body' ).css( 'text-align', 'center' );
		jQuery( '#pdf-insert-dialog-body' ).css( 'padding', '10px' );
		jQuery( '#pdf-insert-dialog-body' ).append( menuTxt );
		
	}
})();
