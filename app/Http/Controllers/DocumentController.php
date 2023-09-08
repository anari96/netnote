<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Document;
use App\Models\DocumentDetail;
use App\Models\DocumentDetailFile;
use App\Helpers\ConfigHelper;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use PulkitJalan\Google\Facades\Google;
use Google\Service\Drive\DriveFile;

class DocumentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $documents = Document::all();
        $data = [
            "documents" => $documents
        ];
        return response()->view("document.index", $data);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return response()->view("document.create");
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        DB::beginTransaction();
        try {
            $document = Document::create([
                "description" => $request->description,
                "date" => $request->date
            ]);

            $detail_file = [];

            if($request->detailFile){
                foreach($request->detailFile as $df){
                    $detail_file[] = $df; 
                }
            }

            // dd($detail_file);
        
            if(isset($request->detailDescription)){
                $countUploaded = 0;
                for($i = 0; $i < count($request->detailDescription); $i++){
                    $detailDocument = DocumentDetail::create([
                        "document_id" => $document->id,
                        "description" => $request->detailDescription[$i],
                    ]);

                    if($request->detailFileCount[$i]){
                        for($j = 0;$j < $request->detailFileCount[$i];$j++){
                            if(@$detail_file[$j+$countUploaded]){
                                $directory = 'upload';
                                // dd($detail_file[$j + $countUploaded]->getClientOriginalExtension());
                                $filePendukungName = $directory . '/' . date('Y-m-d-H-i-s') . '-' . ($j + $countUploaded) . '.' . $detail_file[$j + $countUploaded]->getClientOriginalExtension();
                            }
                        

                            // dd($j);
                            $detail_file[$j + $countUploaded]->storeAs(
                                'public',
                                $filePendukungName
                            );

                            $dd_file = DocumentDetailFile::create([
                                'document_detail_id' => $detailDocument->id,
                                'filename' => $detail_file[$j + $countUploaded]->getClientOriginalExtension(),
                                'path' => $filePendukungName
                            ]);

                            $drive = Google::make('drive');
                            $config = ConfigHelper::generateRefreshToken();
                            $drive->getClient()->setAccessType('offline');
                            $drive->getClient()->setApprovalPrompt("force");
                            $drive->getClient()->setAccessToken($config->access_token);

                            $folder_id = $config->folder_id;
                            // instansiasi obyek file yg akan diupload ke Google Drive
                            $d_file = new DriveFile();
                            $d_file->setParents([$folder_id]);
                            // set nama file di Google Drive disesuaikan dg nama file aslinya
                            $d_file->setName(basename($filePendukungName));
                            // proses upload file ke Google Drive dg multipart
                            $result = $drive->files->create(
                                $d_file,
                                array(
                                    'data' => Storage::get("public/".$filePendukungName),
                                    'mimeType' => 'application/octet-stream',
                                    'uploadType' => 'multipart'
                                )
                            );

                            $dd_file->update([
                                "cloud_path" => $result->id,
                            ]);
                        }
                    }
                    $countUploaded += $request->detailFileCount[$i];
                }
            }
            

            DB::commit();
            return redirect()->route("document.index");
        } catch (\Throwable $th) {
            throw $th;
            DB::rollback();
            dd($th);
            return redirect()->route("document.index");
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $document = Document::find($id);
        $data = [
            "document" => $document,
        ];

        return response()->view("document.show", $data);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    public function testFile()
    {
        dd(Storage::get("public/upload/2023-09-08-01-11-30-0.png"));
    }
}
