<?php
/**
 * User: shahnuralam
 * Date: 8/21/18
 * Time: 7:15 AM
 */

namespace WPDM\Dropbox\libs;

use WPDM\__\Session;

class RestAPI
{
    var $dropbox;

    function __construct($dropbox)
    {

        $this->dropbox = $dropbox;
        add_action( 'rest_api_init', array($this, 'restAPIInit'));
    }

    function restAPIInit(){

        //wpdmdropbox/v1/explore
        register_rest_route( 'wpdmdropbox/v1', '/explore', array(
            'methods' => 'GET',
            'callback' => array($this, 'explore'),
            'permission_callback' => '__return_true'
        ) );


    }


    function explore(){
        $files = WPDM()->package->getFiles(wpdm_query_var('pid'));
        $allowed = WPDM()->package->userCanAccess(wpdm_query_var('pid'));
        if(!$allowed)
            wp_send_json(array('error' => true, 'message' => 'Not Allowed!'));
        $file = $files[wpdm_query_var('fileid')];
        list($d, $type, $path) = explode("|", $file);
        if($type == 'FOLDER') {
            $listFolderContents = $this->dropbox->listFolder($path);
            $data = $listFolderContents->getData();
            $entries = $data['entries'];
            ob_start();
            ?>
            <div class="list-group" style="margin: 0">
                <?php
                $listFolderContents = $this->dropbox->listFolder($path);
                $data = $listFolderContents->getData();
                $entries = $data['entries'];
                if(count($entries) == 0) echo "<div class='list-group-item text-center'>Directory is empty!</div>";
                foreach ($entries as $entry){
                    if($entry['.tag'] == 'file'){
                        $file = explode(".", $entry['name']);
                        $ext = end($file);
                    }
                    $icon = $entry['.tag'] == 'folder'?"<i class='fa fa-folder'></i>":"<img style='width: 16px' src='".WPDM_BASE_URL."assets/file-type-icons/{$ext}.svg' />";
                    $id = str_replace("id:", "", $entry['id']);
                    if($entry['.tag'] == 'file') {
                        Session::set($id, $entry['path_lower'], 14400);
                        $dlink = home_url("/?dbdl=".$id); //$this->dropbox->getTemporaryLink($entry['path_lower']);
                        echo "<div class='list-group-item'><div class='pull-right float-right'><a href='" . $dlink . "'><i class='fa fa-arrow-alt-circle-down color-green'></i></a></div> {$icon} <span>{$entry['name']}</span></div>";
                    }
                    else
                        echo "<div class='list-group-item'><div class='pull-right float-right'><a class='insert-dp-file' data-path='{$entry['path_lower']}' data-type='{$entry['.tag']}' data-id='{$id}' data-name='{$entry['name']}' href='#'><i class='fa fa-arrow-alt-circle-down color-green'></i></a></div> {$icon} <span>{$entry['name']}</span></div>";
                }
                ?>
            </div>
            <?php
            $html = ob_get_clean();
            wp_send_json(array('html' => $html));
        } else {
            wp_send_json(array('error' => true, 'message' => 'Invalid Type'));
        }
        die();

    }





}
