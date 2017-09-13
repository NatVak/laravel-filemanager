var FileManagerUpload = {
    init: function (maxFileSize, acceptFileTypes, maxNumberOfFiles, popup, inputId, isMulti, cntImgsChecked, translations, fromTinyMce) {
        var selectedFiles = [];
        FileManagerUpload.translations = translations;

        // Initialize the jQuery File Upload widget:
        $('#fileupload').fileupload({
            disableImageResize: true,
            autoUpload: false,
            maxNumberOfFiles: maxNumberOfFiles,
            maxFileSize: maxFileSize,
            acceptFileTypes: acceptFileTypes,
            previewMaxWidth: 121,
            previewMaxHeight: 121,
            previewMinWidth: 120,
            previewMinHeight: 120,
            beforeSend: function(xhr, data) {
                xhr.setRequestHeader('X-CSRF-TOKEN', $('meta[name="csrf-token"]').attr('content'));
            },
            // Uncomment the following to send cross-domain cookies:
            //xhrFields: {withCredentials: true},
        }).bind('fileuploadfinished', function (e, data) {
            if(popup && data.result) {
                var files = data.result.files;

                for (var i=0; i<files.length; i++) {
                    var file = data.result.files[i];
                    var path = file.name;
                    var thumbnail = file.thumbnailUrl;
                    selectedFiles.push( { path: path, thumbnail: thumbnail } );
                }
                var filesSelected = $(".background table tbody.files tr").length;
                if(selectedFiles.length == filesSelected) {
                    parent.FileManagerModal.modalSelectCallback(selectedFiles, inputId, isMulti, fromTinyMce);
                }

            }
        }).bind('fileuploadadded', function (e, data) {
            var filesSelected = $(".background table tbody.files tr").length;
            var filesLimit = maxNumberOfFiles - cntImgsChecked;
            if(maxNumberOfFiles && filesSelected > filesLimit) {
                $(".background table tbody.files tr").slice(filesLimit - filesSelected).remove();
                var limitMessage = FileManagerUpload.translations.upload.limit.message;
                var filesTrans = FileManagerUpload.translations.upload.limit.files;
                alert(limitMessage + maxNumberOfFiles + filesTrans);
            }
        });

        // Load & display existing files:
        $('#fileupload').addClass('fileupload-processing');
        $.ajax({
            // Uncomment the following to send cross-domain cookies:
            //xhrFields: {withCredentials: true},
            url: $('#fileupload').attr("action"),
            dataType: 'json',
            context: $('#fileupload')[0]
        }).always(function () {
            $(this).removeClass('fileupload-processing');
        }).done(function (result) {
            $(this).fileupload('option', 'done')
                .call(this, $.Event('done'), {result: result});
        });
    }
};

var FileManagerBrowse = {
    optionsPlugin: [],
    currentFolder: 0,
    actions: function() {
        // Check all
        $(".checkall").click( function() {
            $('.thumbnail .checkbox').addClass("selectedImg");
            $(".thumbnail").addClass("activeThumb");
            $('.thumbnail .checkedFiles').val('1');

            FileManagerBrowse.refreshActionsActs(FileManagerBrowse.optionsPlugin, true);
        });
        // Uncheck all
        $(".uncheckall").click( function() {
            $('.selectedImg').removeClass('selectedImg').addClass("checkbox");
            $(".activeThumb").removeClass('activeThumb').addClass("thumbnail");
            $('.checkedFiles').val('0');

            FileManagerBrowse.refreshActionsActs(FileManagerBrowse.optionsPlugin, true);
        });
        // Edit name of file/folder
        $(".inputEditName").blur(function() {
            editableText = $(this);
            itemType = editableText.parent().find('.itemType').val();
            itemID = editableText.parent().find('.itemID').val();
            itemName = editableText.val();
            infoText = editableText.parents('.file-manager-item').find('.info_open .text .infoName');
            infoText.text(itemName);
            $.ajax({
                url: route('filemanager.rename'),
                type: "POST",
                data: 'itemType='+itemType+'&itemID='+itemID+'&itemName='+itemName,
                dataType: 'html',
                beforeSend: function() {
                    editableText.prop('disabled', true);
                    editableText.addClass("disableInput");
                },
                complete: function() {
                    editableText.prop('disabled', false);
                    editableText.removeClass("disableInput");
                },
                success: function(response) {
                    if (response == 'error') {
                        editableText.css("border", '1px solid red');
                    }
                    if (itemType == 'folder') {
                        FileManagerBrowse.refreshActionsActs(FileManagerBrowse.optionsPlugin, true);
                    }
                    var successMessage = FileManagerBrowse.translations.messages.success.message;
                    var successTitle = FileManagerBrowse.translations.messages.success.title;
                    showToast(successMessage, successTitle, 'success');
                }
            });
        }).keydown(function(e){
            if (e.keyCode == 13) {
                e.preventDefault();
            }
        });;
        //Search button clicked
        $('#stringSearch').keyup(function(e) {
            e.preventDefault();
            var queryString = $('#stringSearch').val();
            if (!queryString) {
                return $('.file-manager-item').show();
            }
            var inputsThatContainsString = $('.file-manager-item .infoName:contains("'+queryString+'")');
            $('.file-manager-item').hide();
            $(inputsThatContainsString).each(function(input) {
                $(inputsThatContainsString[input]).parents('.file-manager-item').show();
            });
        }).keydown(function(e){
            if (e.keyCode == 13) {
                e.preventDefault();
            }
        });
    },
    listeners: function() {
        // checked or unchecked single (click)
        $(".checkbox").click( function() {
            $(this).toggleClass("selectedImg");
            $(this).parent().parent().toggleClass("activeThumb");

            if ($(this).hasClass("selectedImg")) {
                $(this).parent().find('.checkedFiles').val('1');
            } else {
                $(this).parent().find('.checkedFiles').val('0');
            }
            var isFolder = $(this).parent().find('.itemType').val() == 'folder';
            FileManagerBrowse.refreshActionsActs(FileManagerBrowse.optionsPlugin, isFolder);
        });
        // click open info
        $(".info").click(function(){
            $(this).css('display', 'none');
            $(this).parent().find('.info_btnClose').css('display', 'block');
            $(this).parent().find('.info_open').slideDown();
        });
        // click to close info
        $(".info_btnClose").click(function() {
            var thisPar = this;
            $(this).parent().find('.info_open').slideUp('normal', function(){
                $(thisPar).css('display', 'none');
                $(this).parent().find('.info').css('display', 'block');
            });
        });
        // add to page main button listener
        $('#btnAddToPage').click(function(e) {
            e.preventDefault();
            if ($(this).hasClass('disabled')) {
                return false;
            }
            var selectedItems = $('.thumbnail.file .selectedImg');
            var selectedFiles = [];
            selectedItems.each(function() {
                var options = $(this).parents('.options');
                var path = options.find('.itemPath').val();
                var thumbnail = options.find('.itemThumbnail').val();
                selectedFiles.push( { path: path, thumbnail: thumbnail } );
            });
            parent.FileManagerModal.modalSelectCallback(
                selectedFiles,
                FileManagerBrowse.optionsPlugin.inputId,
                FileManagerBrowse.optionsPlugin.isMulti,
                FileManagerBrowse.optionsPlugin.fromTinyMce
            );
        });
        // Add one file to page
        $('.chooseOneFile button').click(function(e) {
            e.preventDefault();
            var options = $(this).parents('.thumbnail.file').find('.options');
            var path = options.find('.itemPath').val();
            var thumbnail = options.find('.itemThumbnail').val();
            var selectedFiles = [];
            selectedFiles.push( { path: path, thumbnail: thumbnail } );
            parent.FileManagerModal.modalSelectCallback(
                selectedFiles,
                FileManagerBrowse.optionsPlugin.inputId,
                FileManagerBrowse.optionsPlugin.isMulti,
                FileManagerBrowse.optionsPlugin.fromTinyMce
            );
        });
        // Block ui on folder change
        $('.folder.ui-droppable').click(function() { App.blockUI(); });
        // Block ui on form post
        $('.items .background #filesForm').submit(function() { App.blockUI(); });
        // Block ui on adding a new folder
        $('.items .background #filesForm').submit(function() { App.blockUI(); });
        // Bluck ui on clicking top buttons
        $('.modImages .top .buttons a').click(function() { App.blockUI(); });
    },
    init: function(currentFolder, popupOptions, translations) {
        // Modify css contains selector to work case insensitive
        $.expr[":"].contains = $.expr.createPseudo(function(arg) {
            return function( elem ) {
                return $(elem).text().toUpperCase().indexOf(arg.toUpperCase()) >= 0;
            };
        });
        FileManagerBrowse.translations = translations;
        FileManagerBrowse.currentFolder = currentFolder;
        FileManagerBrowse.optionsPlugin = popupOptions;
        FileManagerBrowse.listeners();
        FileManagerBrowse.actions();
    },
    showOptions: function(optionsPlugin) {
        var cntImgsChecked = $('.items').find('.selectedImg').length;
        $('#cntImgsChecked span').html(cntImgsChecked);

        if ($('#transferToFolder option').length == 1) {
            $('.moveOption').hide();
        } else {
            $('.moveOption').show();
        }

        if (cntImgsChecked > 0) {
            $('.acts').slideDown();
            $('#cntImgsChecked').fadeIn();
        } else {
            $('.acts').slideUp();
            $('#cntImgsChecked').fadeOut();
        }

        //is from plugin
        cntImgsChecked = optionsPlugin['cntImgsChecked'];
        $('.items_in .thumbnail.activeThumb .options .typeFileIsOk[value="1"]').each(function(){
            cntImgsChecked = cntImgsChecked + 1;
        });
        // update cnt files checked
        if (optionsPlugin['isFromPlugin']) {
            $('#cntImgsChecked2').html(cntImgsChecked);
        }
        // show bottom add to page (if it's stand in requirements and is from plugin files)
        if (optionsPlugin['isFromPlugin'])  {
            var okFileSelected = $('.items_in .thumbnail.activeThumb .options .typeFileIsOk[value="1"]').length;
            var minFilesOk = cntImgsChecked >= optionsPlugin['newMinFiles'];
            var maxFilesOk = cntImgsChecked <= optionsPlugin['newMaxFiles'];
            var checkedFiles = cntImgsChecked > 0;
            if (minFilesOk && maxFilesOk && checkedFiles && okFileSelected) {
                $("#btnAddToPage").removeClass('disabled');
            } else if (optionsPlugin['isMulti']=='false' && cntImgsChecked==1) {
                $("#btnAddToPage").removeClass('disabled');
            } else {
                $("#btnAddToPage").addClass('disabled');
            }
        }
    },
    refreshActionsActs: function(optionsPlugin, folderChecked) {
        var selectedFolders = $('.activeThumb .itemType[value="folder"]');
        var foldersIds = [];
        $(selectedFolders).each(function() {
            foldersIds.push($(this).siblings('.itemID').val());
        });
        if (folderChecked) {
            $.ajax({
                url: route('filemanager.get_selected_options'),
                data: { foldersIds: foldersIds, currentFolder: FileManagerBrowse.currentFolder },
                method: 'post',
                beforeSend: function() {
                    App.blockUI();
                },
                success: function(data) {
                    $('#transferToFolder').html(data);
                    if (data.length == 1) {
                        $('.moveOption').hide();
                    } else {
                        $('.moveOption').show();
                    }
                    FileManagerBrowse.showOptions(FileManagerBrowse.optionsPlugin);
                    App.unblockUI()
                },
                error: function() {
                    App.unblockUI()
                }
            });
        } else {
            if ($('.moveOption #transferToFolder option').size()) {
                $('.moveOption').show();
            }
            FileManagerBrowse.showOptions(FileManagerBrowse.optionsPlugin);
        }
    }
};

var FileManagerModal = {
    placeholder: '/../assets/admin/images/placeholder.png',
    init: function() {
        var modal = $('#file-manager-modal');
        FileManagerModal.listeners(modal);
        FileManagerModal.actions();
    },
    uuid: function() {
        function s4() {
            return Math.floor((1 + Math.random()) * 0x10000)
                .toString(16)
                .substring(1);
        }
        return s4() + s4() + s4();
    },
    actions: function() {
        $('.tooltips').tooltip();
        $('.files-containers').sortable({
            placeholder: 'ui-state-highlight',
            start: function(e, ui){
                ui.placeholder.height(ui.item.height());
            }
        });
    },
    listeners: function(modal) {
        // File select button
        $(document).on('click', '.fileinput .options .select-file', function() {
            $(this).tooltip('hide');
            var data = $(this).data();
            data = FileManagerModal.cleanData(data);
            var inputId = $(this).parents('.fileinput').data('id');
            data.inputId = inputId;
            var params = $.param(data);
            var url = route('filemanager.main') + '?popup=1&' + params;
            modal.find('iframe').attr("src", url);
            $('.tooltips').tooltip();
            FileManagerModal.openModal(modal);
        });
        // Open file button
        $(document).on('click', '.fileinput .options .open-file', function() {
            var file = $(this).attr('data-file');
            if (file) {
                window.open(file, '', 'width=800,height=600');
            }
        });
        // Remove image button
        $(document).on('click', '.fileinput .options .remove-image', function() {
            var fileInput = $(this).parents('.fileinput-new');
            fileInput.find('.file-name-input').val('');
            fileInput.find('.image-input-placeholder').attr('src', GlobalPath + FileManagerModal.placeholder);
            fileInput.find('.open-file').attr('data-file', '');
            fileInput.find('.crop-image').attr('data-image-path', '');
        });
        // Remove image multi button
        $(document).on('click', '.fileinput .options .remove-image-multi', function() {
            var input = $(this).parents('.fileinput');
            input.remove();
        });
        // File select button
        $(document).on('click', '.fileinput .options .upload-file', function() {
            $(this).tooltip('hide');
            var data = $(this).data();
            data = FileManagerModal.cleanData(data);
            var inputId = $(this).parents('.fileinput').data('id');
            data.inputId = inputId;
            var params = $.param(data);
            var url = route('filemanager.get_selected_options', {folderId: 0}) + '?popup=1&' + params;
            modal.find('iframe').attr("src", url);
            $('.tooltips').tooltip();
            FileManagerModal.openModal(modal);
        });
        // Crop image button
        $(document).on('click', '.fileinput .options .crop-image', function() {
            var imagePath = $(this).attr('data-image-path');
            var cropName = $(this).data('crop-name');
            var url = route('filemanager.crop', {imagePath: imagePath}) + '?cropName=' + cropName;
            modal.find('iframe').attr("src", url);
            FileManagerModal.openModal(modal);
        });
        // File multi select button
        $(document).on('click', '.file-multi-container .file-multi-options .select-files', function(e) {
            e.preventDefault();
            var data = $(this).data();
            data = FileManagerModal.cleanData(data);
            var multiFileContainer = $(this).parents('.file-multi-container');
            var inputId = multiFileContainer.data('id');
            data.inputId = inputId;
            var filesCount = multiFileContainer.find('.fileinput-new').length;
            data.filesCount = filesCount;
            var params = $.param(data);
            var url = route('filemanager.main') + '?popup=1&' + params;
            modal.find('iframe').attr("src", url);
            FileManagerModal.openModal(modal);
        });
        // File multi clean button
        $(document).on('click', '.file-multi-container .file-multi-options .remove-files', function(e) {
            e.preventDefault();
            var multiFileContainer = $(this).parents('.file-multi-container');
            multiFileContainer.find('.files-containers').empty();
        });
        //File multi upload button
        $(document).on('click', '.file-multi-container .file-multi-options .upload-files', function(e) {
            e.preventDefault();
            var multiFileContainer = $(this).parents('.file-multi-container');
            var inputId = multiFileContainer.data('id');
            var data = $(this).data();
            data = FileManagerModal.cleanData(data);
            data.inputId = inputId;
            var filesCount = multiFileContainer.find('.fileinput-new').length;
            data.filesCount = filesCount;
            var params = $.param(data);
            var url = route('filemanager.upload', {folderId : 0 }) + '?popup=1&' + params;
            modal.find('iframe').attr("src", url);
            FileManagerModal.openModal(modal);
        });
    },
    cleanData: function(data) {
        for(var key in data) {
            if(typeof data[key] !== 'string') {
                delete data[key];
            }
            if(typeof data[key] == 'string') {
                data[key] = data[key].replace(/['"]+/g, '');
            }
        }
        return data;
    },
    openModal: function(modal) {
        return modal.modal({show:true});
    },
    modalSelectCallback: function(selectedFiles, element, isMulti, fromTinyMce) {
        $('#file-manager-modal').modal('hide');
        if(isMulti == 'false') {
            if (fromTinyMce) {
                $('#' + element).val( GlobalPublicPath + '/uploads/original/' + selectedFiles[0].path );
                var ed = parent.tinymce.editors[0];
                ed.windowManager.windows[1].close();// CLOSES THE BROWSER WINDOW
            } else {
                var file = selectedFiles[0];
                var inputElement = $('.fileinput[data-id="'+element+'"]');
                inputElement.find('.image-input-placeholder').attr('src', file.thumbnail);
                inputElement.find('.open-file').attr('data-file', GlobalPath + '/../uploads/original/' + file.path);
                inputElement.find('.crop-image').attr('data-image-path', file.path);
                inputElement.find('.file-name-input').val(file.path);
            }
        } else {
            for(var i=0; i<selectedFiles.length; i++) {
                var file = selectedFiles[i];
                var new_file_element = $($('.file-multi-container[data-id="'+element+'"]').siblings('#clone_item').clone().html());
                $(new_file_element).attr('data-id', 'file-manager-' + FileManagerModal.uuid());
                $(new_file_element).find('.open-file').attr('data-file', GlobalPath + '/../uploads/original/' + file.path);
                $(new_file_element).find('.file-name-input').val(file.path);
                $(new_file_element).find('.crop-image').attr('data-image-path', file.path);
                $(new_file_element).find('img.image-input-placeholder').attr('src', file.thumbnail);
                $('.file-multi-container[data-id="'+element+'"]').find('.files-containers').append(new_file_element);
            }
        }
    }
};

var FileManagerHelper = {
    tinyMceInit: function(field_name, url, type, win) {
        var fileManagerUrl = route('filemanager.main') + '?popup=1&allowedExtensions=jpg,jpeg,png,gif&newmaxfiles=1&newminfiles=1&filetype=image&ismulti=false&source=tinymce&inputId=' + field_name;
        tinyMCE.activeEditor.windowManager.open({
            file : fileManagerUrl,
            title : 'ענן הקבצים',
            width : 1024,  // Your dimensions may differ - toy around with them!
            height : 884,
            resizable : "yes",
            inline : "yes",  // This parameter only has an effect if you use the inlinepopups plugin!
            close_previous : "no"
        }, {
            window : win,
            input : field_name
        });
        return false;
    }
};