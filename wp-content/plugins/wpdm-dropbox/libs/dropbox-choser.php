<?php
/**
 * User: shahnuralam
 * Date: 5/11/18
 * Time: 3:18 AM
 */

if (!defined('ABSPATH')) die();

class WPDMDropbox
{

    var $app;
    var $dropbox;

    function __construct()
    {


//        $wpdm_dropbox = maybe_unserialize(get_option('__wpdm_dropbox', array()));
//        $this->app = new DropboxApp($wpdm_dropbox['app_key'], $wpdm_dropbox['app_secret'], $wpdm_dropbox['access_token']);
//        $this->dropbox = new Dropbox($this->app);

        add_action("wpdm_cloud_storage_settings", array($this, "Settings"));
        add_action('wpdm_attach_file_metabox', array($this, 'BrowseButton'));


    }


    function Settings()
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


    function BrowseButton()
    {
        $wpdm_dropbox = maybe_unserialize(get_option('__wpdm_dropbox', array()));

        ?>
        <div class="w3eden">

            <a href="#" id="btn-dropbox" style="margin-top: 10px" title="Drobox" onclick="return false;"
               class="btn wpdm-dropbox btn-block">Select From Dropbox</a>
            <script type="text/javascript" src="https://www.dropbox.com/static/api/2/dropins.js" id="dropboxjs"
                    data-app-key="<?php echo $wpdm_dropbox['app_key']; ?>"></script>

            <script>

                var dropbox;

                function InsertDropBoxLink(file, id, name) {
                    <?php if(version_compare(WPDM_VERSION, '4.0.0', '>')){  ?>
                    var html = jQuery('#wpdm-file-entry').html();
                    file = file.replace('dl=0', 'dl=1');
                    name = file.substring(0, 80) + "...";
                    var filetitle = file;
                    filetitle = filetitle.split('?');
                    console.log(filetitle);
                    filetitle = filetitle[0];
                    filetitle = filetitle.split('/');
                    console.log(filetitle);
                    filetitle = filetitle[filetitle.length - 1];
                    var ext = filetitle.split('.');
                    ext = ext[ext.length - 1];
                    var icon = "<?php echo WPDM_BASE_URL; ?>file-type-icons/48x48/" + ext + ".png";

                    var _file = {};
                    _file.filetitle = filetitle;
                    _file.filepath = file;
                    _file.fileindex = id.replace(":", "__");
                    _file.preview = icon;

                    wpdm_attach_file(_file);

                    <?php } else { ?>
                    jQuery('#wpdmfile').val(file + "#" + name);
                    jQuery('#cfl').html('<div><strong>' + name + '</strong>').slideDown();
                    <?php } ?>
                }

                function popupwindow(url, title, w, h) {
                    var left = (screen.width / 2) - (w / 2);
                    var top = (screen.height / 2) - (h / 2);
                    return window.open(url, title, 'toolbar=0, location=0, directories=no, status=no, menubar=no, scrollbars=no, resizable=no, copyhistory=no, width=' + w + ', height=' + h + ', top=' + top + ', left=' + left);
                }

                jQuery(function () {

                    var options = {

                        // Required. Called when a user selects an item in the Chooser.
                        success: function (files) {
                            console.log(files);
                            var id = files[0].name.replace(/([^a-zA-Z0-9]*)/g, "");
                            InsertDropBoxLink(files[0].link, id, files[0].name)
                        },


                        cancel: function () {

                        },

                        folderselect: false,


                        linkType: "preview", /* List type "direct" generates temporary download link only */

                        multiselect: false
                    };


                    jQuery('#btn-dropbox').click(function () {
                        dropbox = Dropbox.choose(options);
                        return false;
                    });


                });


            </script>
        </div>


        <?php
    }


}

new WPDMDropbox();
