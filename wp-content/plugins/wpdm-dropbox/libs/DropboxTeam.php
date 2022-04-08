<?php
namespace WPDM\AddOn;

class DropboxTeam
{
    public $endpoint = "https://api.dropboxapi.com/2/team/";

    function execute($action, $params = [])
    {
        $apicall = $this->endpoint.$action;
        $wpdm_dropbox = maybe_unserialize(get_option('__wpdm_dropbox', array()));

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => $apicall,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS =>"{\"limit\": 100}",
            CURLOPT_HTTPHEADER => array(
                "Authorization: Bearer ".wpdm_valueof($wpdm_dropbox, 'access_token'),
                "Content-Type: application/json"
            ),
        ));

        $result = curl_exec($curl);
        curl_close($curl);

        return json_decode($result);
    }

    function listFolders()
    {
        $folders = $this->execute("team_folder/list");
        if(is_object($folders) && isset($folders->team_folders))
            $folders = $folders->team_folders;
        return $folders;
    }
}

$dropbox_business = new DropboxTeam();
$dropbox_business->listFolders();