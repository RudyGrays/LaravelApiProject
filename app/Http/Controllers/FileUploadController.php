<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\UserFile;
use App\Models\User;
use Faker\Core\File;

class FileUploadController extends Controller
{
    public function uploadFiles(Request $request)
    {   
        $user = auth()->user();

        $files = $request->file('files');
        $uploadedFiles = [];
        foreach ($files as $file) {
            $fileName = $file->getClientOriginalName();


            if ($file->getSize() > 2 * 1024 * 1024) {
                $uploadedFiles[] = [
                    'success'=> false,
                    'message'=> 'File not loaded',
                    'name'=> $fileName,
                ];
                continue; 
            }

            
            $allowedExtensions = ['doc', 'pdf', 'docx', 'jpeg', 'zip', 'jpg', 'png'];
            $extension = $file->getClientOriginalExtension();
            if (!in_array($extension, $allowedExtensions)) {
                $uploadedFiles[] = [
                    'success'=> false,
                    'message'=> 'File not loaded',
                    'name'=> $fileName,
                ];
                continue;
            }

            
            $fileId = bin2hex(random_bytes(5)); 

           
            $fileName = $file->getClientOriginalName();
            
            $existingFilesCount = UserFile::where('file_name', $fileName)->count();


            if ($existingFilesCount > 0) {
                
                $extension = $file->getClientOriginalExtension();
                $newFileName = $this->generateNewFileName($fileName, $existingFilesCount, $extension);
            } else {
                
                $newFileName = $fileName;
            }

            $url = 'http://127.0.0.1:8000/api/files/' . $fileId;
            $uploadedFiles[] = [
                'success'=> true,
                'message'=> 'Success',
                'name'=> $newFileName,
                'url'=> $url,
                'file_id' => $fileId
            ];
            
            $userFile = new UserFile();
            $userFile->user_id =  auth()->user()->id; 
            $userFile->file_name = $newFileName;
            $userFile->file_id = $fileId;
            $userFile->save();

            
            $file->storeAs('uploads', $fileId . '.' . $extension); 
        }

        return response()->json($uploadedFiles);
    }

    private function generateNewFileName($originalName, $count, $extension)
    {
        $baseName = pathinfo($originalName, PATHINFO_FILENAME);
        return $baseName . '(' . $count . ').' . $extension;
    }

    public function updateFile(Request $request, $id)
    {   
        $file = UserFile::where('file_id', $id)->first();
        
        if(!$file ) return response(['success'=> false, 'message'=> 'File doe\'s not exist']);

        if(!isset($request)) return response(['success'=> false, 'message'=> 'Name']);
        $file->file_name = $request['file_name'];
        $file->save();

        return response()->json(['success'=> true, 'message'=> 'renamed']);
    }

    public function deleteFile($id)
    {   
        $file = UserFile::where('file_id', $id)->first();
        
        if(!$file ) return response(['success'=> false, 'message'=> 'File doe\'s not exist']);

        
        
        $file->delete();

        return response()->json(['success'=> true, 'message'=> 'file deleted']);
    }
}
