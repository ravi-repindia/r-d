<?php
/**
 * User: shahnuralam
 * Date: 5/11/18
 * Time: 3:19 AM
 */

if (!defined('ABSPATH')) die();

include dirname(__FILE__).'/vendor/autoload.php';

use Kunnu\Dropbox\DropboxApp;
use Kunnu\Dropbox\Dropbox;

include dirname(__FILE__)."/class.RestAPI.php";

class WPDMDropboxPro
{

    var $app;
    var $dropbox;


    function __construct()
    {


        add_action("wpdm_cloud_storage_settings", array($this, "settings"));

        $wpdm_dropbox = maybe_unserialize(get_option('__wpdm_dropbox', array()));
        if(!isset($wpdm_dropbox['access_token']) || $wpdm_dropbox['access_token'] === '') return false;


        $this->app = new DropboxApp($wpdm_dropbox['app_key'], $wpdm_dropbox['app_secret'], $wpdm_dropbox['access_token']);

        $this->dropbox = new Dropbox($this->app);

        new \WPDM\Dropbox\libs\RestAPI($this->dropbox);

        add_action('wpdm_attach_file_metabox', array($this, 'browseButton'));
        add_action('wpdm_onstart_download', array($this, 'download'));
        add_filter('wpdm_single_file_download_link', array($this, 'fileDownloadLink'), 10, 3);
        add_action("wp_enqueue_scripts", array($this, 'enqueueScript'));
        add_action("wp_ajax_explore_dropbox", array($this, 'exploreDropbox'));
        add_action("wp", array($this, 'fileDownload'));
        add_action("wpdm_admin_update_package", array($this, 'fetchThumbnails'), 10, 2);
        //add_action("plugin_loaded", array($this, 'getThumbnail'));
        add_filter("wpdm_file_thumbnail", [$this, 'fileThumbnail'], 10,  2);
    }

    function fileThumbnail($thumb, $params){
        $file = wpdm_valueof($params, 'file');
        if(substr_count($file, 'DROPBOXPRO|FILE|')) {
            $thumb  = UPLOAD_BASE . '/wpdm-dropbox-thumbs/' . md5($file) . '.jpg';
            if(file_exists($thumb)) {
                $thumb  = \WPDM\__\FileSystem::imageThumbnail($thumb, $params['size'][0], $params['size'][1], true);
                $thumb  = str_replace(ABSPATH, home_url('/'), $thumb);
                return $thumb;
            }
            else {
                $thumb = $this->fetchThumbnail($file, 'large');
                $thumb  = \WPDM\__\FileSystem::imageThumbnail($thumb, $params['size'][0], $params['size'][1], true);
                $thumb  = str_replace(ABSPATH, home_url('/'), $thumb);
                return $thumb;
            }
        }
        return $thumb;
    }

    function fetchThumbnails($post, $data){
        if(isset($data['files'])   && is_array($data['files'])) {
            foreach ($data['files'] as $id => $file) {
                $this->fetchThumbnail($file, 'large');
            }
        }
    }

    function fetchThumbnail($file, $size){
        $thumb_path =  $file;
        if (substr_count($file, 'DROPBOXPRO|FILE|') > 0) {
            $thumb_dir = UPLOAD_BASE . "/wpdm-dropbox-thumbs/";
            if (!file_exists($thumb_dir)) mkdir($thumb_dir, 0755, true);
            $thumb_path = $thumb_dir . md5($file) . '.jpg';
            $ext = pathinfo($file, PATHINFO_EXTENSION);
            $ext  = strtolower($ext);
            if(!in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'pdf', 'doc', 'docx', 'xls', 'ppt', 'pptx'])) return \WPDM\__\FileSystem::fileTypeIcon($ext);
            file_put_contents($thumb_path, $this->dropbox->getThumbnail(str_replace("DROPBOXPRO|FILE|", "", $file), 'large')->getContents());
        }
        return $thumb_path;
    }

    function enqueueScript(){
        wp_enqueue_script( 'wp-api' );
        wp_enqueue_script("wpdm-dropbox", plugins_url("js/wpdm-dropbox.js", dirname(__FILE__)), array( 'wp-api' ));
    }

    function settings()
    {
        global $current_user;
        if (isset($_POST['__wpdm_dropbox']) && count($_POST['__wpdm_dropbox']) > 0) {
            update_option('__wpdm_dropbox', $_POST['__wpdm_dropbox']);
            die('Settings Saves Successfully!');
        }
        $wpdm_dropbox = maybe_unserialize(get_option('__wpdm_dropbox', array()));

        ?>
        <div class="panel panel-default">
            <div class="panel-heading"><b><?php _e('Dropbox API Credentials', 'wpdmpro'); ?></b></div>

            <table class="table">
                <tr>
                    <td style="width: 120px">Account Type</td>
                    <td>
                        <label><input type="radio" name="__wpdm_dropbox[account]" value="single" <?php checked(wpdm_valueof($wpdm_dropbox, 'account'), 'single'); ?> /> Personal</label>
                        <label><input type="radio" name="__wpdm_dropbox[account]" value="business" <?php checked(wpdm_valueof($wpdm_dropbox, 'account'), 'business'); ?> /> Business</label>
                    </td>
                </tr>

                <tr>
                    <td style="width: 120px">App Key</td>
                    <td><input type="text" name="__wpdm_dropbox[app_key]" class="form-control"
                               value="<?php echo isset($wpdm_dropbox['app_key']) ? $wpdm_dropbox['app_key'] : ''; ?>"/>
                    </td>
                </tr>
                <tr>
                    <td>App Secret</td>
                    <td><input type="text" name="__wpdm_dropbox[app_secret]" class="form-control"
                               value="<?php echo isset($wpdm_dropbox['app_secret']) ? $wpdm_dropbox['app_secret'] : ''; ?>"/>
                    </td>
                </tr>
                <tr>
                    <td>Access Token</td>
                    <td><input type="text" name="__wpdm_dropbox[access_token]" class="form-control"
                               value="<?php echo isset($wpdm_dropbox['access_token']) ? $wpdm_dropbox['access_token'] : ''; ?>"/>
                    </td>
                </tr>

            </table>
            <!--div class="panel-footer">
                    <b>Redirect URI:</b> &nbsp; <input onclick="this.select()" type="text" class="form-control" style="background: #fff;cursor: copy;display: inline;width: 400px" readonly="readonly" value="<?php echo admin_url('?page=wpdm-google-drive'); ?>" />
                </div-->
        </div>


        </div>

        <?php
    }

    function exploreDropbox(){
        if(!current_user_can(WPDM_MENU_ACCESS_CAP) || !wp_verify_nonce(wpdm_query_var('__dbnonce', 'txt'), NONCE_KEY)) die("<div class='list-group-item'>Invalid Request!</div>");

        $listFolderContents = $this->dropbox->listFolder(wpdm_query_var('path', 'txt'));
        $entries = $listFolderContents->getItems();

        //$entries = $data['entries'];
        foreach ($entries as $entry){
            $type = $entry->getDataProperty('.tag');
            $id = $entry->getId();
            $name = $entry->getName();
            $path = $entry->getPathLower();

            if($type == 'file'){
                $file = explode(".", $name);
                $ext = end($file);
            }
            $icon = $type == 'folder'?"<i class='fas fa-folder' style='color: #92CEFF'></i>":"<img class='file-icon' src='".WPDM_BASE_URL."assets/file-type-icons/{$ext}.svg' />";
            $item_link = '';
            echo "<div class='list-group-item'><div class='btns'><a class='insert-dp-file' data-path='{$path}' data-type='{$type}' data-id='{$id}' data-name='{$name}' href='#'><i class='fa fa-plus-circle color-green' style='font-size: 14pt'></i></a></div><a href='#' data-path='{$path}' data-type='{$type}' data-id='{$id}' class='item-action {$type}'>{$icon} <span>{$name}<span></span></a></div>";
        }
        die();
    }


    function browseButton()
    {
        $wpdm_dropbox = maybe_unserialize(get_option('__wpdm_dropbox', array()));
        if(version_compare(WPDM_VERSION, '4.0.0', '<')) {
            wpdmprecho('Need WPDM Pro!');
            return;
        }
        ?>
        <div class="w3eden">

            <button type="button" data-toggle="modal"  data-target="#explore-dropbox"  id="btn-dropbox" style="margin-top: 10px" title="Drobox"
               class="btn wpdm-dropbox btn-block">Select From Dropbox</button>

            <!-- Modal -->
            <div class="modal fade" id="explore-dropbox" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header" style="background: #f5f5f5;border-radius: 4px 4px 0 0">
                            <div class="pull-right">
                                <button  type="button" class="btn btn-sm btn-link color-green fetfont" onclick="jQuery('#dp-cfa').slideToggle();"><i class="fas fa-folder-plus"></i> New Folder</button>
                                <button type="button" class="btn btn-sm btn-link color-purple fetfont" id="upload-dbfile"><i class="fas fa-file-upload"></i> Upload File</button>
                            </div>
                            <h4 class="modal-title" id="myModalLabel" style="font-family: -apple-system,'Segoe UI', sans-serif;"><i class="fab fa-dropbox"></i> DropBox</h4>
                        </div>
                        <div class="modal-header" id="dp-cfa" style="background: #f5f5f5;border-radius: 4px 4px 0 0;display: none;position: absolute;width: 100%;z-index: 99999">
                            <div class="media">
                                <span class="pull-right">
                                    <button type="button" class="btn btn-primary" id="dp-cfb">Create Folder</button>
                                    <button type="button" class="btn btn-secondary" onclick="jQuery('#dp-cfa').slideUp();"><i class="fa fa-times"></i></button>
                                </span>
                                <div class="media-body">
                                    <input type="text" id="dp-folder-name" class="form-control" placeholder="Folder Name" />
                                </div>
                            </div>
                        </div>
                        <div class="modal-header" style="background: #fafafa;border-radius: 4px 4px 0 0">
                            <span class="modal-title" id="myModalLabel" style="font-family: -apple-system,'Segoe UI', sans-serif;letter-spacing: 1px;font-size: 11px">:/ <a href="#" data-type="folder" data-path="/" class="item-action">Home</a>&nbsp;<span id="db-breadcrumb"></span></span>
                        </div>
                        <div id="db-modal-content" class="modal-body1" style="height: 400px;overflow: auto">
                            <div style="display: none">
                                <form>
                                    <input type="file" id="dbfileupload" name="dbfileupload" accept="*" onchange="uploadFile(this)">
                                </form>
                            </div>
                            <div id="wpdm-dropbox-explorer" class="list-group" style="margin: 0">

                            </div>

                        </div>
                        <div class="modal-footer" style="background: #f5f5f5;border-radius: 0 0 4px 4px">
                            <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Close</button>
                            <div class="pull-left color-purple" id="dploading" style="display:none;">
                                <i class="fas fa-sun fa-spin"></i> Uploading.... <span class="label label-danger" id="perc" style="width: 40px;display: inline-block;">0%</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
        <style>
            #db-modal-content{
                position: relative;
            }
            #db-upload-ui .upload-area{
                padding: 30px;
                margin: 50px;
                border: 1px dashed #cccccc;
                text-align: center;
            }
            #db-upload-ui{
                width: 100%;
                height: 400px;
                z-index: 999;
                background: #ffffff;
                left: 0;
                top: 0;
            }
            .list-group,
            .list-group-item{
                border-radius: 0 !important;
            }
            .list-group-item .btns{
                float: right;
                margin-top: -1px;
            }
            .list-group-item{
                font-family:  -apple-system,'Segoe UI', Arial, sans-serif;
                font-weight: 400;
                line-height: 20px;
            }
            .list-group-item img{
                float: left;
            }
            .list-group-item a{
                text-decoration: none !important;
                color: #555555;
            }
            .list-group-item a.folder:hover{
                text-decoration: none !important;
                color: #0070E0;
            }
            .list-group-item span{
                padding-left: 5px;
                display: inline-block;
            }
            .file-icon{
                width: 14px;float: left;margin-top: 4px;
            }
            .modal-header .btn-sm{
                font-family:  -apple-system,'Segoe UI', Arial, sans-serif !important;
                font-weight: normal !important;
                text-transform: unset !important;
            }
            #db-breadcrumb .fa-caret-right{
                vertical-align: middle;
            }
            span.modal-title{
                display: block;
            }
            #db-breadcrumb {
                white-space: nowrap;
                overflow: hidden;
                text-overflow: ellipsis;
                direction: rtl;
                text-align: left;
                display: inline-block;
                width: calc(100% - 90px);
                position: absolute;
            }

            .w3eden .blockui{
                position: relative;
            }
            .w3eden .blockui:before{
                content: "";
                position: absolute;
                width: 100%;
                height: 100%;
                left: 0;
                top: 0;
                z-index: 9999;
                background: rgba(255, 255, 255, 0.4) url("../wp-content/plugins/download-manager/assets/images/loading.svg") center center no-repeat;
                background-size: 48px;
                -webkit-transition: ease-in-out 400ms;
                -moz-transition: ease-in-out 400ms;
                -ms-transition: ease-in-out 400ms;
                -o-transition: ease-in-out 400ms;
                transition: ease-in-out 400ms;
            }
        </style>
        <script>

            var dbinit = 0, activepath = '';

            jQuery(function ($) {

                $('#btn-dropbox').on('click', function () {
                    if(dbinit == 0)
                        $('#wpdm-dropbox-explorer').html("<div class='list-group-item'><i class='fas fa-sun fa-spin'></i> Loading Item...</div>").load(ajaxurl,  {action: 'explore_dropbox', __dbnonce: '<?php echo wp_create_nonce(NONCE_KEY); ?>', path: '/'});
                    dbinit = 1;
                });



                $('body').on('click', '.insert-dp-file', function (e) {
                    e.preventDefault();
                    if($(this).data('added')) return false;
                    $(this).html("<i class=\"fa fa-check-circle color-purple\" style=\"font-size: 14pt\"></i>").data('added', 1);

                    var _file = {};
                    var name = $(this).data('name');
                    var type = $(this).data('type').toUpperCase();
                    var ext = name.split('.');
                    ext = ext[ext.length - 1];
                    _file.filetitle = name;
                    _file.filepath = "DROPBOXPRO|"+type+"|"+$(this).data('path');
                    _file.fileindex = $(this).data('id').replace(":", "__");
                    console.log(_file);
                    _file.preview = "<?php echo WPDM_BASE_URL; ?>file-type-icons/48x48/" + ext + ".png";
                    wpdm_attach_file(_file);

                });

                $('body').on('click', '.item-action', function (e) {
                    e.preventDefault();
                    if($(this).data('type') === 'file') return false;
                    $('#db-modal-content').addClass('blockui');
                    $('#wpdm-dropbox-explorer').load(ajaxurl,  {action: 'explore_dropbox', __dbnonce: '<?php echo wp_create_nonce(NONCE_KEY); ?>', path: $(this).data('path')}, function () {
                        $('#db-modal-content').removeClass('blockui');
                    });
                    activepath  = $(this).data('path');
                    var bc = $(this).data('path').split('/');
                    var dbbreadcrumb = '', xpath = '';
                    $.each(bc, function (index, item) {
                        if(index > 0 && item !== ''){
                            xpath += '/' + item;
                            dbbreadcrumb += ' <a href="#" data-type="folder" data-path="'+xpath+'" class="item-action">'+item+'</a> <i class="fas fa-caret-right text-muted"></i>';
                        }
                    });
                    $('#db-breadcrumb').html(dbbreadcrumb);
                });

                $('#upload-dbfile').on('click', function (e) {
                    e.preventDefault();
                    $('#dbfileupload').trigger('click');
                });

                $('#dp-cfb').on('click', function () {
                    var foldername = $('#dp-folder-name').val();
                    if(foldername === '') return false;
                    var $this = $(this);
                    $this.html("<i class='fas fa-sun fa-spin'></i> Creating...").prop('disabled', true);
                    $.ajax({
                        url: "https://api.dropboxapi.com/2/files/create_folder_v2",
                        data: JSON.stringify({ path: "/"+foldername, autorename: false }),
                        type: "POST",
                        beforeSend: function(xhr){
                            xhr.setRequestHeader('Content-type', 'application/json');
                            xhr.setRequestHeader('Authorization', 'Bearer <?php echo $this->app->getAccessToken(); ?>');
                            },
                        success: function(folder) {
                            folder = folder.metadata;
                            $this.html("Create Folder").prop('disabled', false);
                            $('#dp-cfa').slideUp();
                            jQuery('#wpdm-dropbox-explorer').prepend('<div class="list-group-item"><div class="btns"><a class="insert-dp-file" data-path="'+folder.path_lower+'" data-type="folder" data-id="'+folder.id+'" data-name="'+folder.name+'" href="#"><i class="fa fa-plus-circle color-green" style="font-size: 14pt"></i></a></div> <i class="fas fa-folder"  style=\'color: #92CEFF\'></i> <span>'+folder.name+'</span></div>');
                        }
                    });

                });
            });

            function uploadFile(fileinput) {
                /**
                 * Two variables should already be set.
                 * dropboxToken = OAuth access token, specific to the user.
                 * file = file object selected in the file widget.
                 */

                jQuery(fileinput).prop('disabled', true);
                var xhr = new XMLHttpRequest();
                var file = fileinput.files[0];
                xhr.upload.onprogress = function(evt) {
                    var percentComplete = parseInt(100.0 * evt.loaded / evt.total);
                    jQuery('#perc').html(percentComplete+'%');
                    jQuery('#dploading').show();
                };

                xhr.onload = function() {
                    if (xhr.status === 200) {
                        var fileInfo = JSON.parse(xhr.response);
                        var ext = fileInfo.name.split('.');
                        ext = ext[ext.length-1];
                        jQuery('#wpdm-dropbox-explorer').append('<div class="list-group-item"><div class="btns"><a class="insert-dp-file" data-path="'+fileInfo.path_lower+'" data-type="file" data-id="'+fileInfo.id+'" data-name="'+file.name+'" href="#"><i class="fa fa-plus-circle color-green" style="font-size: 14pt"></i></a></div> <img class="file-icon" src="http://localhost/wpdmpro/wp-content/plugins/download-manager/assets/file-type-icons/'+ext+'.svg"> <span>'+file.name+'</span></div>');
                        jQuery('#dploading').fadeOut();
                        jQuery(fileinput).prop('disabled', false);
                    }
                    else {
                        var errorMessage = xhr.response || 'Unable to upload file';
                        __bootModal('Error!', errorMessage, 300);
                        jQuery('#dploading').fadeOut();
                        jQuery(fileinput).prop('disabled', false);
                    }
                };

                if(activepath === '/') activepath = '';
                xhr.open('POST', 'https://content.dropboxapi.com/2/files/upload');
                xhr.setRequestHeader('Authorization', 'Bearer <?php echo $this->app->getAccessToken(); ?>');
                xhr.setRequestHeader('Content-Type', 'application/octet-stream');
                xhr.setRequestHeader('Dropbox-API-Arg', JSON.stringify({
                    path: activepath + '/' +  file.name,
                    mode: 'add',
                    autorename: true,
                    mute: false
                }));

                xhr.send(file);
            }

        </script>


        <?php
    }

    function download($package){
        $files = $package['files'];
        $file = "";
        if(!is_array($files)) return;
        if(wpdm_query_var('ind') != '')
            $file = $files[wpdm_query_var('ind', 'txt')];
        else if(count($files) == 1 && wpdm_query_var('ind') == '')
            $file = array_shift($files);
        $_file = $file;
        if($file != ''){
            //$file = $files[wpdm_query_var('ind', 'txt')];
            $file = explode("|", $file);
            if($file[0] === 'DROPBOXPRO') {
                if($file[1] == 'FILE') {
                    $path = $file[2];
                    $dropbox_item = $this->dropbox->getTemporaryLink($path);
                    //$item = $this->dropbox->postToAPI("/file_requests/get", array('id' => $dropbo_item_id));
                    WPDM()->downloadHistory->add($package['ID'], $_file, wpdm_query_var('oid'));
                    header("location: " . $dropbox_item->getLink());
                    die();
                } else {

                    try {
                        $dropbox_item = $this->dropbox->postToAPI("/sharing/create_shared_link_with_settings", array("path" => $file[2]));
                        $res = $dropbox_item->getDecodedBody();
                        $url = $res->url;
                    }catch (Exception $e){
                        //$dropbox_item = $this->dropbox->postToContent('/files/download_zip', array('path' => $file[2]));
                        //$dropbox_item = $this->dropbox->getMetadata($file[2]);
                        //$data = $dropbox_item->getData();
                        //$shared_folder_id = $data['shared_folder_id'];
                        $dropbox_item = $this->dropbox->postToAPI("/sharing/list_shared_links", array("path" => $file[2]));
                        $res = $dropbox_item->getDecodedBody();
                        $url = $res['links'][0]['url'];
                        //wpdmdd($res);
                    }
                    WPDM()->downloadHistory->add($package['ID'], $_file, wpdm_query_var('oid'));
                    header("location: " . $url);
                    die();
                }
            }
        }
    }

    function fileDownloadLink($link, $fileID, $package){
        if(!isset($package['files'][$fileID])) return $link;
        $file = $package['files'][$fileID];
        $file = explode("|", $file);
        if($file[0] === 'DROPBOXPRO') {

            if($file[1] == 'FOLDER') {
                $button_label = apply_filters("single_file_download_link_label", __("Download", "download-manager"), $package);
                $link =  "<a href='#' data-pid='{$package['ID']}' data-fileid='{$fileID}' class='btn btn-default btn-primary btn-sm wpdm-dropbox btn-dbexpore'>{$button_label}</a>";
            }
        }
        return $link;
    }


    function fileDownload(){
        if(isset($_GET['dbdl'])){
            $path = \WPDM\__\Session::get(sanitize_text_field($_GET['dbdl']));
            $dropbox_item = $this->dropbox->getTemporaryLink($path);
            header("location: " . $dropbox_item->getLink());
            die();
        }
    }



}

new WPDMDropboxPro();
