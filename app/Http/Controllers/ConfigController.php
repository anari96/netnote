<?php

namespace App\Http\Controllers;

use App\Models\Config;
use Illuminate\Http\Request;
use PulkitJalan\Google\Facades\Google;
use Illuminate\Support\Facades\DB;
use Google\Service\Drive\DriveFile;
use App\Helpers\ConfigHelper;

class ConfigController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $config = Config::first();

        $data = [
            "config" => $config
        ];

        return response()->view('config.index', $data);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return response()->view('config.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            "name"=> "required"
        ]);

        DB::beginTransaction();
        try {
            $check = Config::first();

                $config = Config::create([
                    "name" => $request->name
                ]);

            DB::commit();
            return redirect()->route('config.login')->with("config_id", $config->id);
        } catch (\Throwable $th) {

            DB::rollback();
            return redirect()->route("config.index");
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Config $config)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Config $config)
    {
        $config = Config::first();

        $data = [
            "config" => $config
        ];

        return response()->view('config.edit', $data);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Config $config)
    {
        $request->validate([
            "name"=> "required"
        ]);

        DB::beginTransaction();
        try {
            $check = Config::first();

            if($check->count() == 0){
                $config = Config::create([
                    "name" => $request->name
                ]);
            }else if($check->count() > 0){
                $check->update([
                    "name" => $request->name
                ]);
            }

            DB::commit();
            return redirect()->route('config.login')->with("config_id", $config->id);
        } catch (\Throwable $th) {

            DB::rollback();
            return redirect()->route("config.index");
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Config $config)
    {
        //
    }

    public function authGoogle(Request $request)
    {

        $request->session()->forget('token');
        $request->session()->forget('cs_id');
        $oauth2 = Google::make('oauth2');
        $oauth2->getClient()->setAccessType('offline');
        //set redirect uri
        $oauth2->getClient()->setRedirectUri(url('config/login'));
        $oauth2->getClient()->setApprovalPrompt("force");
        $oauth2->getClient()->setScopes(
            array(
                // 'https://www.googleapis.com/auth/plus.me',
                'https://www.googleapis.com/auth/userinfo.email',
                'https://www.googleapis.com/auth/userinfo.profile',
                'https://www.googleapis.com/auth/drive.file',
                // 'https://www.googleapis.com/auth/drive'
            )
        );
        $request->session()->put('config_id', $request->session()->get('config_id'));
        if ($request->get('code')) {
            $oauth2->getClient()->authenticate($request->get('code'));
            $request->session()->put('token', $oauth2->getClient()->getAccessToken());
            $oauth2->getClient()->setState($request->session()->get('config_id')."|".$request->get('code'));
        }
        if ($request->session()->get('token')) {
            $oauth2->getClient()->setAccessToken($request->session()->get('token'));
        }

        if ($oauth2->getClient()->getAccessToken()) {
            $google_user = $oauth2->userinfo->get();

            // dd($google_user);
            if ($request->get('state') && $request->get('code')) {
                $state = explode("|", $request->get('state'));
                $json = $oauth2->getClient()->getAccessToken();
                $json['code'] = $request->get('code');
                $json['refresh_token'] = $oauth2->getClient()->getRefreshToken();
                // dd($json['code']);
                $optParams = array(
                    'fields' => 'nextPageToken, files(id, name, trashed, createdTime, modifiedTime)',
                    'q' => "trashed=false and name='_drivetest'"
                );
                $drive = Google::make('drive');
                $drive->getClient()->setAccessType('offline');
                $drive->getClient()->setApprovalPrompt("force");
                $drive->getClient()->setAccessToken($json['access_token']);
                //cek dir
                $CEK = $drive->files->listFiles($optParams)->files;
                if(count($CEK)>0){
                    $folder = $CEK[0];
                } else {
                    //create new folder
                    $fileMetadata = new DriveFile(
                        array(
                            'name' => '_drivetest',
                            'mimeType' => 'application/vnd.google-apps.folder'
                        )
                    );
                    $folder = $drive->files->create($fileMetadata,[
                        'fields' => 'id'
                    ]);
                }

                //update data
                $id = $state[0];
                $cs = Config::find($id);
                if(!$cs){
                    return "storage not found";
                }
                $json['folder_id'] = $folder->id;
                $cs->update([
                    'auth_name' => $google_user['email'],
                    'status' => 'active',
                    'json_config' => $json
                ]);

                //clear session
                $request->session()->forget('token');
                $request->session()->forget('config_id');
                return redirect()->route('config.index')->with('success', 'Google Drive has been connected successfully');
            } else {
                return "no"; 
            }
        
        } else {
            //For Guest user, get google login url
            $oauth2->getClient()->setState( $request->session()->get('config_id')."|");
            $get_authUrl = $oauth2->getClient()->createAuthUrl();
            // dd($get_authUrl);
            return redirect()->to($get_authUrl);
        }
    }

    public function regenerateToken()
    {
        $test = ConfigHelper::generateRefreshToken();
        return $test;
    }

    public function revokeAccess()
    {
        $config = Config::first();
        if($config->config != null){
            if($config->config->access_token != null){
                $oauth2 = Google::make('oauth2');
                $setting = ConfigHelper::generateRefreshToken($config);
                $oauth2->getClient()->setAccessType('offline');
                $oauth2->getClient()->setApprovalPrompt("force");
                $oauth2->getClient()->setAccessToken($setting->access_token);
                //disconnect
                $oauth2->getClient()->revokeToken();
            }
        }

        $config->delete();
        return redirect()->route("config.index");
    }
}
