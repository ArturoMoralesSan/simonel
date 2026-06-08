<?php

namespace app\Traits;

use Illuminate\Support\Facades\Storage;

trait SaveDocumentClass {

    private function savePreliminaryDocument($folio, $document)
    {
        $filename     = $document->getClientOriginalName();
        $filenameInfo = pathinfo($filename);
        $fullname     = $folio.'.'.$filenameInfo['extension'];
        Storage::disk("documents")->putFileAs($folio, $document, $fullname);
        return $fullname;
    }

    private function saveDocument($folio, $name, $document)
    {
        $filename     = $document->getClientOriginalName();
        $filenameInfo = pathinfo($filename);
        $fullname     = $folio.'_'.$name.'.'.$filenameInfo['extension'];
        Storage::disk("documents")->putFileAs($folio, $document, $fullname);
        return $fullname;
    }

    private function DeleteDocument($folio, $document)
    {
        Storage::disk("documents")->delete($folio.'/'.$document);
    }

    private function saveCall($slug, $document)
    {
        $filename     = $document->getClientOriginalName();
        $filenameInfo = pathinfo($filename);
        $fullname     = $slug.'.'.$filenameInfo['extension'];
        Storage::disk("public")->putFileAs('convocatorias/'.$slug ,$document, $fullname);
        return $fullname;
    }

    private function saveCover($slug, $cover)
    {
        $filename     = $cover->getClientOriginalName();
        $filenameInfo = pathinfo($filename);
        $fullname     = $slug.'.'.$filenameInfo['extension'];
        Storage::disk("public")->putFileAs('convocatorias/'.$slug ,$cover, $fullname);
        return $fullname;
    }

    private function saveFileCall($callslug, $slug, $document)
    {
        $filename     = $document->getClientOriginalName();
        $filenameInfo = pathinfo($filename);
        $fullname     = $slug.'.'.$filenameInfo['extension'];
        Storage::disk("public")->putFileAs('convocatorias/'.$callslug ,$document, $fullname);
        return $fullname;
    }

    private function renameFile($slug, $documentfile, $newTitle) {
        $ext = pathinfo(public_path().'archivos/convocatorias/'.$slug.'/'.$documentfile , PATHINFO_EXTENSION);
        Storage::disk("public")->move('convocatorias/'.$slug.'/'.$documentfile, 'convocatorias/'.$slug.'/'.$newTitle.'.'.$ext);
        $document = $newTitle.'.'.$ext;
        return $document;
    }

    private function DeleteCall($slug, $document)
    {
        Storage::disk("public")->delete('convocatorias/'.$slug.'/'.$document);
    }

    private function moveFiles($slug, $newTitle)
    {
        Storage::disk('public')->move('convocatorias/'.$slug, 'convocatorias/'.$newTitle);
    }


}
