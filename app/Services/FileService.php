<?php
/**
 * Created by PhpStorm.
 * Date: 3/15/2018
 * Time: 19:56
 */

namespace App\Services;

use App\Models\File;

class FileService {
	public static function upload($file) {

		$extension = $file->getClientOriginalExtension();
		$originalFullFIleName = $file->getClientOriginalName();
		$orginalFilename = str_replace(".$extension", "", $originalFullFIleName);
		$newFileName = $orginalFilename . "_" . strtotime("NOW");
		$newFullFileName = $newFileName . ".$extension";

		$destinationPath = config('file.upload_path');
		$file->move($destinationPath, $newFullFileName);

		return [
			'originalFileName' => $originalFullFIleName,
			'uploadedFileName' => $newFullFileName,
			'filePath' => $destinationPath . "/$newFullFileName"
		];
	}

	public static function save($form, $file) {
		$uploadResult = self::upload($file);

		$file = File::create([
			'file_name' => $uploadResult['uploadedFileName'],
			'form_id' => $form->id
		]);

		return $file;
	}
}