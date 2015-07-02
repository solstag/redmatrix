<script language="javascript" type="text/javascript">

var editor = false;
var textlen = 0;
var plaintext = '{{$editselect}}';

function initEditor(cb){
	if (editor==false){
		$("#profile-jot-text-loading").spin('small').show();
		if(plaintext == 'none') {
			$("#profile-jot-text-loading").spin(false).hide();
			$("#profile-jot-text").css({ 'height': 200, 'color': '#000' });
			if(typeof channelId === 'undefined')
				$("#profile-jot-text").editor_autocomplete(baseurl+"/acl");
			else
				$("#profile-jot-text").editor_autocomplete(baseurl+"/acl",[channelId]); // Also gives suggestions from current channel's connections
			editor = true;
			  $("a#jot-perms-icon").colorbox({ 
				  'inline' : true, 
				  'transition' : 'elastic' 
			});
			$(".jothidden").show();
			if (typeof cb!="undefined") cb();
			return;
		}
		tinyMCE.init({
			theme : "advanced",
			mode : "specific_textareas",
			editor_selector: {{$editselect}},
			auto_focus: "profile-jot-text",
			plugins : "bbcode,paste,autoresize, inlinepopups",
			theme_advanced_buttons1 : "bold,italic,underline,undo,redo,link,unlink,image,forecolor,formatselect,code",
			theme_advanced_buttons2 : "",
			theme_advanced_buttons3 : "",
			theme_advanced_toolbar_location : "top",
			theme_advanced_toolbar_align : "center",
			theme_advanced_blockformats : "blockquote,code",
			gecko_spellcheck : true,
			paste_text_sticky : true,
			entity_encoding : "raw",
			add_unload_trigger : false,
			remove_linebreaks : false,
			force_p_newlines : false,
			force_br_newlines : true,
			forced_root_block : '',
			convert_urls: false,
			content_css: "{{$baseurl}}/view/custom_tinymce.css",
			theme_advanced_path : false,
			file_browser_callback : "fcFileBrowser",
			setup : function(ed) {
				cPopup = null;
				ed.onKeyDown.add(function(ed,e) {
					if(cPopup !== null)
						cPopup.onkey(e);
				});

				ed.onKeyUp.add(function(ed, e) {
					var txt = tinyMCE.activeEditor.getContent();
					match = txt.match(/@([^ \n]+)$/);
					if(match!==null) {
						if(cPopup === null) {
							cPopup = new ACPopup(this,baseurl+"/acl");
						}
						if(cPopup.ready && match[1]!==cPopup.searchText) cPopup.search(match[1]);
						if(! cPopup.ready) cPopup = null;
					}
					else {
						if(cPopup !== null) { cPopup.close(); cPopup = null; }
					}

					textlen = txt.length;
					if(textlen != 0 && $('#jot-perms-icon').is('.unlock')) {
						$('#profile-jot-desc').html(ispublic);
					}
					else {
						$('#profile-jot-desc').html('&nbsp;');
					}
				});

				ed.onInit.add(function(ed) {
					ed.pasteAsPlainText = true;
					$("#profile-jot-text-loading").spin(false).hide();
					$(".jothidden").show();
					if (typeof cb!="undefined") cb();
				});

			}
		});

		editor = true;
	} else {
		if (typeof cb!="undefined") cb();
	}
}

function enableOnUser(){
	if (editor) return;
	$(this).val("");
	initEditor();
}
</script>
<script type="text/javascript" src="{{$baseurl}}/view/js/ajaxupload.js" ></script>
<script>
	var ispublic = '{{$ispublic}}';

	$(document).ready(function() {
		/* enable tinymce on focus and click */
		$("#profile-jot-text").focus(enableOnUser);
		$("#profile-jot-text").click(enableOnUser);

		var upload_title = $('#wall-image-upload').attr('title');
		var attach_title = $('#wall-file-upload').attr('title');
		try {
			var uploader = new window.AjaxUpload('wall-image-upload',
			{ action: '{{$baseurl}}/wall_upload/{{$nickname}}',
				name: 'userfile',
				title: upload_title,
				onSubmit: function(file,ext) { $('#profile-rotator').spin('tiny'); },
				onComplete: function(file,response) {
					addeditortext(response);
					$('#jot-media').val($('#jot-media').val() + response);
					$('#profile-rotator').spin(false);
				}
			});
		} catch (e) {
		}
		try {
			var uploader_sub = new window.AjaxUpload('wall-image-upload-sub',
			{ action: '{{$baseurl}}/wall_upload/{{$nickname}}',
				name: 'userfile',
				title: upload_title,
				onSubmit: function(file,ext) { $('#profile-rotator').spin('tiny'); },
				onComplete: function(file,response) {
					addeditortext(response);
					$('#jot-media').val($('#jot-media').val() + response);
					$('#profile-rotator').spin(false);
				}
			});
		} catch(e) {
		}
		try {
			var file_uploader = new window.AjaxUpload('wall-file-upload',
			{ action: '{{$baseurl}}/wall_attach/{{$nickname}}',
				name: 'userfile',
				title: attach_title,
				onSubmit: function(file,ext) { $('#profile-rotator').spin('tiny'); },
				onComplete: function(file,response) {
					addeditortext(response);
					$('#jot-media').val($('#jot-media').val() + response);
					$('#profile-rotator').spin(false);
				}
			});
		} catch(e) {
		}
		try {
			var file_uploader_sub = new window.AjaxUpload('wall-file-upload-sub',
			{ action: '{{$baseurl}}/wall_attach/{{$nickname}}',
				name: 'userfile',
				title: attach_title,
				onSubmit: function(file,ext) { $('#profile-rotator').spin('tiny'); },
				onComplete: function(file,response) {
					addeditortext(response);
					$('#jot-media').val($('#jot-media').val() + response);
					$('#profile-rotator').spin(false);
				}
			});
		} catch(e) {
		}
	});

	function deleteCheckedItems() {
		var checkedstr = '';

		$('.item-select').each( function() {
			if($(this).is(':checked')) {
				if(checkedstr.length != 0) {
					checkedstr = checkedstr + ',' + $(this).val();
				}
				else {
					checkedstr = $(this).val();
				}
			}
		});
		$.post('item', { dropitems: checkedstr }, function(data) {
			window.location.reload();
		});
	}

	function jotGetLink() {
		reply = prompt("{{$linkurl}}");
		if(reply && reply.length) {
			reply = bin2hex(reply);
			$('#profile-rotator').spin('tiny');
			$.get('{{$baseurl}}/parse_url?binurl=' + reply, function(data) {
				addeditortext(data);
				$('#profile-rotator').spin(false);
			});
		}
	}

	function jotVideoURL() {
		reply = prompt("{{$vidurl}}");
		if(reply && reply.length) {
			addeditortext('[video]' + reply + '[/video]');
		}
	}

	function jotAudioURL() {
		reply = prompt("{{$audurl}}");
		if(reply && reply.length) {
			addeditortext('[audio]' + reply + '[/audio]');
		}
	}

	function jotGetLocation() {
		reply = prompt("{{$whereareu}}", $('#jot-location').val());
		if(reply && reply.length) {
			$('#jot-location').val(reply);
		}
	}

	function jotGetExpiry() {
		//reply = prompt("{{$expirewhen}}", $('#jot-expire').val());
		$('#expiryModal').modal();
		$('#expiry-modal-OKButton').on('click', function() {
			reply=$('#expiration-date').val();
			if(reply && reply.length) {
				$('#jot-expire').val(reply);
				$('#expiryModal').modal('hide');
			}
		})
	}

	function jotShare(id) {
		if ($('#jot-popup').length != 0) $('#jot-popup').show();

		$('#like-rotator-' + id).spin('tiny');
		$.get('{{$baseurl}}/share/' + id, function(data) {
			if (!editor) $("#profile-jot-text").val("");
			initEditor(function(){
				addeditortext(data);
				$('#like-rotator-' + id).spin(false);
				$(window).scrollTop(0);
			});
		});
	}

	function linkdropper(event) {
		var linkFound = event.dataTransfer.types.contains("text/uri-list");
		if(linkFound)
			event.preventDefault();
	}

	function linkdrop(event) {
		var reply = event.dataTransfer.getData("text/uri-list");
		event.target.textContent = reply;
		event.preventDefault();
		if(reply && reply.length) {
			reply = bin2hex(reply);
			$('#profile-rotator').spin('tiny');
			$.get('{{$baseurl}}/parse_url?binurl=' + reply, function(data) {
				if (!editor) $("#profile-jot-text").val("");
				initEditor(function(){
					addeditortext(data);
					$('#profile-rotator').spin(false);
				});
			});
		}
	}

	function itemTag(id) {
		reply = prompt("{{$term}}");
		if(reply && reply.length) {
			reply = reply.replace('#','');
			if(reply.length) {

				commentBusy = true;
				$('body').css('cursor', 'wait');

				$.get('{{$baseurl}}/tagger/' + id + '?term=' + reply);
				if(timer) clearTimeout(timer);
				timer = setTimeout(NavUpdate,3000);
				liking = 1;
			}
		}
	}

	function itemFiler(id) {

		var bordercolor = $("input").css("border-color");

		$.get('filer/', function(data){
			$.colorbox({html:data});
			$("#id_term").keypress(function(){
				$(this).css("border-color",bordercolor);
			})
			$("#select_term").change(function(){
				$("#id_term").css("border-color",bordercolor);
			})

			$("#filer_save").click(function(e){
				e.preventDefault();
				reply = $("#id_term").val();
				if(reply && reply.length) {
					commentBusy = true;
					$('body').css('cursor', 'wait');
					$.get('{{$baseurl}}/filer/' + id + '?term=' + reply, NavUpdate);
//					if(timer) clearTimeout(timer);
//					timer = setTimeout(NavUpdate,3000);
					liking = 1;
					$.colorbox.close();
				} else {
					$("#id_term").css("border-color","#FF0000");
				}
				return false;
			});
		});
		
	}

	function itemBookmark(id) {
		$.get('{{$baseurl}}/bookmarks?f=&item=' + id);
		if(timer) clearTimeout(timer);
		timer = setTimeout(NavUpdate,1000);
	}

	function itemAddToCal(id) {
		$.get('{{$baseurl}}/events/add/' + id);
		if(timer) clearTimeout(timer);
		timer = setTimeout(NavUpdate,1000);
	}

	function toggleVoting() {
		if($('#jot-consensus').val() > 0) {
			$('#jot-consensus').val(0);
			$('#profile-voting, #profile-voting-sub').removeClass('icon-check').addClass('icon-check-empty');
		}
		else {
			$('#jot-consensus').val(1);
			$('#profile-voting, #profile-voting-sub').removeClass('icon-check-empty').addClass('icon-check');
		}
	}

	function jotClearLocation() {
		$('#jot-coord').val('');
		$('#profile-nolocation-wrapper').attr('disabled', true);
	}

	{{$geotag}}

</script>

<script language="javascript" type="text/javascript">
  // TODO:
  // Deal with included media:
  // Remove all attachments and warn the member (for now)

  // Returns the reshare ACL to the regular ACL
  function restoreACL (e) {
    $("#profile-jot-text").val("");
    $('#aclModal .modal-title').toggle();
    $('#aclModal .modal-footer > *').toggle();
    $('#aclModal').off('hidden.bs.modal', restoreACL);
    // private post stuff
    $('.bootstrap-switch-id-show_origin').hide();
    $('#acl-showall').show();
    $('#share_private_info').remove();
  }

  // Flashes the background
  function bgWarning(el,n) {
    if(n==0) return;
    el.css('backgroundColor','yellow');
    setTimeout(function () {
      el.css('backgroundColor','white');
      setTimeout(function(){
        bgWarning(el,n);
      }, 1000);
    }, 500);
    n--;
  }
  
  function jotShareAdvanced(lock,id) {
    // spin
    $('#like-rotator-' + id).spin('tiny');

    // includes the reshare ACL items and switches to it
    reshareACL();

    // takes the text and includes it in the editor
    $.get('{{$baseurl}}/share/' + id, function(data) {
      $("#profile-jot-text").val("");
      initEditor(function(){
        $('#aclModal').modal('show');
        addeditortext(data);
        $('#like-rotator-' + id).spin(false);
      });
    });

    // if in normal ACL, turn it to reshare ACL
    if ($('#acl_reshare_btn').css('display') == 'none') {
      $('#aclModal .modal-title').toggle();
      $('#aclModal .modal-footer > *').toggle();
    }

    $("#profile-jot-form").off('submit.private_share');
    $('.bootstrap-switch-id-show_origin').hide();
    $('#acl-showall').show();
    $('#share_private_info').remove();

    if(lock!=''){
      $('.bootstrap-switch-id-show_origin').show();
      $('#acl-showall').hide();
      $('#acl-wrapper').prepend("<div id='share_private_info'>Você está compartilhando um post privado. Selecione ao menos uma pessoa ou grupo com quem compartilhá-lo.</div>");
      // actions to be taken when resharing a private post
      $("#profile-jot-form").on('submit.private_share',function(){
        // check if someone was chosen to reshare only with them
        if($("#acl-fields input[name='contact_allow[]']").length+$("#acl-fields input[name='group_allow[]']").length<=0){
          $('#aclModal').modal('show');
          bgWarning($('#share_private_info'),2)
          return false;
        }
        // if original poster is to be concealed from the header, sanitize the message
        if(!$('#show_origin').bootstrapSwitch('state')){
          var jot_text = $("#profile-jot-text").val();
          // TODO: improve this sanitization (share tag might have been altered before submit)
          var re=/\[share(?:[^\[]*)?posted(?:\s*)=(?:\s*)['"](.{19})/ig
          var a=re.exec(jot_text)
          var posted=a[1];
          re=/\[(share)[^\[]+\]/ig
          jot_text=jot_text.replace(/\[(share)[^\[]+\]/ig,"[$1 author='Someone' posted='"+posted+"']");
          $("#profile-jot-text").val(jot_text);
        }
      });
    }


    // upon exiting the ACL, return to regular ACL
    $('#aclModal').on('hidden.bs.modal', restoreACL );

  }

  // Adds new elements to the ACL selector for resharing
  function reshareACL(){
    if ($('#acl_reshare_btn').length > 0) 
      return;

    // switches the title
    var oldtitle = $('#aclModal .modal-title');
    var newtitle = oldtitle.clone();
    newtitle.text('Recompartilhar com');
    oldtitle.after(newtitle);
    oldtitle.toggle();

    // edit button: fills the edit box with the post and closes the ACL
    var btn_edit  = $('<button id="acl_edit_btn" class="btn btn-default" type="button">Editar</button>');
    btn_edit.click(function(){
      if ($('#jot-popup').length != 0)
        $('#jot-popup').show();
      $(window).scrollTop(0);
      $('#aclModal').off('hidden.bs.modal', restoreACL); // manter reshare ao sair
      $('#aclModal').modal('hide');
    });

    // reshare button: directly reshares the post
    var btn_share = $('<button id="acl_reshare_btn" class="btn btn-default" type="button">Compartilhar</button>');
    btn_share.click(function(){
      $('#profile-jot-form').submit();
    });

    $('#aclModal .modal-footer > .btn').toggle();
    $('#aclModal .modal-footer').prepend(btn_edit);
    $('#aclModal .modal-footer').prepend(btn_share);

    // includes the switch to "show origin"
    var cb_origin = $('<input type="checkbox" id="show_origin" name="show_origin" checked>');
    $('#aclModal .modal-footer').prepend(cb_origin);
    $("#show_origin").bootstrapSwitch({
      onText:'Autor será exibido',
      offText:'Autor será ocultado',
      onColor:'danger',
      offColor:'success'
    });
    $('.bootstrap-switch-id-show_origin').css('width', 335).css('margin-right',5).css('float','left');
  }


</script>


<script>
$( document ).on( "click", ".wall-item-delete-link,.page-delete-link,.layout-delete-link,.block-delete-link", function(e) {
	var link = $(this).attr("href"); // "get" the intended link in a var

	if (typeof(eval($.fn.modal)) === 'function'){
		e.preventDefault();
		bootbox.confirm("<h4>{{$confirmdelete}}</h4>",function(result) {
			if (result) {
				document.location.href = link;
			}
		});
	} else {
		return confirm("{{$confirmdelete}}");
	}
});
</script>
